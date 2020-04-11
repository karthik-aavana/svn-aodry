<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>AODRY | Registration</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="shortcut icon" type="image/png" href="<?php echo base_url('assets/images/favicon.png'); ?>" />        
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lib/font-awesome/4.5.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lib/ionicons/css/ionicons.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/select2/select2.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/iCheck/square/blue.css">    
        <style>
            .left-side-bar{
                margin-top: 11%;
                text-align: left;
                width: auto;
            }
            .left-side-bar h1 span {
                color: #4459d0;
                font-weight: 600;
            }
            .left-side-bar h2{
                color: #333;
                font-weight: 600;
                margin: 0;  
                font-size: 22px;
                padding-bottom: 8px;
            }          
            .left-side-bar img{
                width: 140px;
                margin-bottom: 6%;
            }            
            .left-side-bar h4 small{
                font-size: 18px;
                color: #4459d0;
                font-weight: 500;
            }    
            .has-feedback label~.form-control-feedback{
                right: 15px;
                width: 30px;
                height: 34px !important;
                border: 1px solid #9b9b9b;
                background: #d0e0fc;
                color: #012b72;
            }
            .left-side-bar .box-title{
                border: 0;
                line-height: 30px;
            }
            .register-box .box-title{
                border-bottom: 1px solid #ececec;
                padding-bottom: 8px;
                line-height: 1.5;
                color: #000;
                font-weight: 600;
            }
            #rotate{
                 min-height: 70px;
            }
            #rotate div {
                font-size: 22px;   
                color: #333;                
                font-weight: 600;
            }      
            .footer-copyright {
                font-weight: 600;
                position: fixed;
                bottom: 25px;
                text-align: center;
                width: 80%;
            }

            .footer-copyright a{
                color: #333;
            }
            .register-box{
                max-width: 100% !important;
                margin: 25% auto;
            }
            .register-box .btn-primary{
                background: #FFEB3B !important;
                color: #000000;
                font-weight: 500;
            }
            .p_l_0{padding-left: 0px;}
            #infoMessage{color: #006500;
                font-size: 15px;
                font-weight: bold;
                padding: 5px;
                background: #a1fba1;
                margin-bottom: 8px;
                border: 1px solid transparent;
                border-radius: 5px;
            }
            #infoMessage.error{color: red; background: #ffc7c7;}
            .downBtn{opacity: 0.7; pointer-events: none;}
        </style>
    </head>
    <body class="hold-transition login-page">
        <div class="container">
            <div class="row">
                <div class="col-sm-7">
                    <div class="left-side-bar">
                        <img src = "<?= base_url('assets/images/aodry-black-logo.png') ?>">
                        <div id="rotate"> 
                            <div>Complicated accounting made simple</div>
                            <div>Your trusted source for automated accounting</div>
                            <div>Safe, secure and time saving accounting</div>
                            <div>Experience hassle-free accounting</div>
                            <div>An accounting tool that saves your time</div>
                            <div>Grow your business with a time saving accounting tool</div>
                            <div>Switch to aodry for a smooth and easy accounting experience</div>
                        </div>
                        <hr>
                        <div class="call_us">
                            <h4 class="box-title">To request a demo of the<br> <strong>Accounting Software</strong>, Call us at</h4>
                            <h2> +91-9900492704</h2>
                            <h2> +91-9900539903</h2>
                            <h2> +91-80-40909797</h2>
                        </div>   
                        <hr>
                        <div class="call_us">
                            <h4 class="box-title">
                                <a class="link_style" href="https://calendly.com/pavan-hv/" target="_blank">Click here</a> to request a personalized demo
                            </h4>
                        </div>                    
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="register-box">                       
                        <div class="register-box-body">
                            <h4 class="box-title">Registration</h4>
                            <?php if (@$error_message) { ?>
                                <div id="infoMessage" class="error" style="color: red;"><?=($error_message);?></div>
                            <?php } ?>
                            <?php if (@$message) { ?>
                                <div id="infoMessage" style="color: green;"><?=($message);?></div>
                            <?php } ?>
                            <p class="register-box-msg text-left">This is a secure system and you will need to provide your company details to try the Aodry trial.</p>
                            
                            <!-- <form action="../../index2.html" method="post"> -->
                            <?php echo form_open("superadmin/firm/autoSignup"); ?>
                            <div class="row">
                                <!-- <div class="form-group has-feedback col-sm-6">
                                    <label for="registered_type">Registered Type <span class="validation-color"> *</span></label>
                                    <select class="form-control select2" id="registered_type" name="registered_type">
                                        <option value="">Select Registered Type</option>
                                        <option value="Private Limited Company" <?=(@$registered_type && $registered_type == 'Private Limited Company' ? 'selected': '')?>>Private Limited Company</option>
                                        <option value="Proprietorship" <?=(@$registered_type && $registered_type == 'Proprietorship' ? 'selected': '')?>>Proprietorship</option>
                                        <option value="Partnership" <?=(@$registered_type && $registered_type == 'Partnership' ? 'selected': '')?>>Partnership</option>
                                        <option value="One Person Company" <?=(@$registered_type && $registered_type == 'One Person Company' ? 'selected': '')?>>One Person Company</option>
                                        <option value="Limited Liability Partnership" <?=(@$registered_type && $registered_type == 'Limited Liability Partnership' ? 'selected': '')?>>Limited Liability Partnership</option>
                                    </select>
                                    <span class="validation-color" id="err_registration_type"></span>
                                </div> -->

                                <div class="form-group has-feedback col-sm-12">
                                    <label for="name">Firm Name <span class="validation-color"> *</span></label>
                                    <?php echo form_input('name', (@$name ? $name : ''), 'class="form-control" placeholder="Firm Name"'); ?>
                                    <input type="hidden" name="payment" value="trial">
                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                    <span class="validation-color" id="err_name"></span>
                                </div>
                                <div class="form-group has-feedback col-sm-12">
                                    <label for="email">Email <span class="validation-color"> *</span></label>
                                    <?php echo form_input('email', (@$email ? $email : ''), 'class="form-control" placeholder="Email"'); ?>
                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                    <span class="validation-color" id="err_email"></span>
                                </div>
                            </div>
                            <!-- <div class="row">
                                
                                <div class="form-group has-feedback col-sm-6">
                                    <label for="mobile">Mobile</label>
                                    <?php echo form_input('mobile', (@$mobile ? $mobile : ''), 'class="form-control" placeholder="Mobile Number"'); ?>
                                    <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                                    <span class="validation-color" id="err_mobile"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group has-feedback col-sm-6">
                                    <label for="branch_gstin_number">GSTIN</label>
                                    <?php echo form_input('branch_gstin_number', (@$branch_gstin_number ? $branch_gstin_number : ''), 'class="form-control" placeholder="GSTIN Number"'); ?>
                                    <span class="validation-color" id="err_branch_gstin_number"></span>
                                </div>
                                <div class="form-group has-feedback col-sm-6">
                                    <label for="country">Country <span class="validation-color"> *</span></label>
                                    <select class="form-control select2" id="sa_country" name="sa_country">
                                        <option value="">Select Country</option>
                                        <?php
                                        $selected_id = 101;
                                        if(@$sa_country){
                                        	$selected_id = $sa_country;
                                        } 
                                        foreach ($country as $key) { ?>
                                        <option value="<?=$key->country_id;?>" <?=($key->country_id == $selected_id ? 'selected' : '')?>>
                                            <?php echo $key->country_name; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                    <span class="validation-color" id="err_country"><?php echo form_error('sa_country'); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group has-feedback col-sm-6">
                                    <label for="sa_state">State <span class="validation-color"> *</span></label>
                                    <select class="form-control select2" id="sa_state" name="sa_state">
                                        <option value="">Select State</option>
                                        <?php 
                                        $state_id = 17;
                                        if(@$sa_state){
                                        	$state_id = $sa_state;
                                        } 
                                        foreach ($state as $key) { ?>
                                            <option value='<?php echo $key->state_id ?>' <?=($key->state_id == $state_id ? 'selected' : '')?> code='<?=$key->state_code;?>'>
                                                <?=$key->state_name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <input type="hidden" class="form-control" id="state_code" name="state_code" value="">
                                    <span class="validation-color" id="err_state"><?php echo form_error('sa_state'); ?></span>
                                </div>
                                <div class="form-group has-feedback col-sm-6">
                                    <label for="sa_city">City <span class="validation-color"> *</span></label>
                                    <select class="form-control select2" id="sa_city" name="sa_city">
                                        <option value="">Select City</option>
                                        <?php
                                        $city_id = 43889;
                                        if(@$sa_city){
                                        	$city_id = $sa_city;
                                        }
                                        foreach ($city as $key) { ?>
                                            <option value='<?php echo $key->city_id ?>' <?=($key->city_id == $city_id ? 'selected' : '')?> >
                                                <?php echo $key->city_name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <span class="validation-color" id="err_city"><?php echo form_error('sa_city'); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group has-feedback col-sm-12">
                                    <label for="branch_address">Address <span class="validation-color"> *</span></label>
                                    <input type="text" class="form-control" id="branch_address" name="branch_address" value="<?=(@$branch_address ? $branch_address : '');?>">
                                    <span class="validation-color" id="err_branch_address"></span>
                                </div>
                            </div> -->
                            <div class="row" >
                                 <div class="col-xs-9">
                                    <div class="checkbox icheck">
                                        <?php echo form_close(); ?>Already have an account? <a href="login">Login</a>
                                    </div>
                                </div>                      
                                <div class="col-xs-3">
                                    <!-- <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button> -->
                                    <?php echo form_submit('register', 'Register', 'class="btn btn-primary btn-block btn-flat"'); ?>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="footer-copyright text-center">© 2019 Copyright. Aodry is a product by <a href="https://www.aavana.in" target="_blank"> Aavana Corporate Solutions PVT LTD </a>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/iCheck/icheck.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/select2/select2.full.min.js"></script>
        <script>
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%' // optional
                });
            });
            (function ($) {
                $.fn.extend({
                    rotaterator: function (options) {
                        var defaults = {
                            fadeSpeed: 400,
                            pauseSpeed: 10000,
                            child: null
                        };
                        var options = $.extend(defaults, options);
                        return this.each(function () {
                            var o = options;
                            var obj = $(this);
                            var items = $(obj.children(), obj);
                            items.each(function () {
                                $(this).hide();
                            })
                            if (!o.child) {
                                var next = $(obj).children(':first');
                            } else {
                                var next = o.child;
                            }
                            $(next).fadeIn(o.fadeSpeed, function () {
                                $(next).delay(o.pauseSpeed).fadeOut(o.fadeSpeed, function () {
                                    var next = $(this).next();
                                    if (next.length == 0) {
                                        next = $(obj).children(':first');
                                    }
                                    $(obj).rotaterator({child: next, fadeSpeed: o.fadeSpeed, pauseSpeed: o.pauseSpeed});
                                });
                            });
                        });
                    }
                });
            })(jQuery);
            $(document).ready(function () {
                $('#rotate').rotaterator({fadeSpeed: 400, pauseSpeed: 10000});
                
                
            });
        </script>
    </body>
    <script type="text/javascript">
        var base_url = '<?=base_url();?>';
        var mobile_regex= /^[\+\d\-\s]+$/;
        var name_regex_spl = /^[a-zA-Z\[\]/@()&\-.,\d\-_\s\']+$/;
        var email_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        var gst_regex = "^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$";
        $(document).ready(function(){
            /*$('#sa_country').change(function () {
                getCountryById();
            });

            $('.select2').select2();

            $('#sa_state').change(function() {

				var id = $(this).val();
				$('#state_code').val($(this).find('option:selected').attr('code'));
				$('#sa_city').html('<option value="">Select</option>');
				$.ajax({
					url : base_url + 'superadmin/general/get_city/' + id,
					type : "GET",
					dataType : "JSON",
					success : function(data) {
						for ( i = 0; i < data.length; i++) {
							$('#sa_city').append('<option value="' + data[i].city_id + '">' + data[i].city_name + '</option>');
						}
					}
				});
			});*/

            $('[name=name]').on('keyup',function(){
                var name = $('[name=name]').val();
                if (name == null || name == "") {
                    $("#err_name").text('Please Enter Name.');
                    
                } else if (!name.match(name_regex_spl)) {
                    $('#err_name').text("Please Enter Valid Name");
                    
                } else if (name.length < 3) {
                    $('#err_name').text("Please Enter Name Minimun 3 Character");
                    
                } else {
                    $("#err_name").text("");
                }
            })

            $('[name=email]').on('keyup',function(){
                var email = $('[name=email]').val();
                if (email == null || email == "") {
                    $("#err_email").text("Please Enter Email.");
                    return false;
                } else if (!email.match(email_regex)) {
                    $('#err_email').text("Please Enter Valid Email");
                    return false;
                } else if (email.length < 2) {
                    $('#err_email').text("Please Enter Valid Email");
                    return false;
                } else {
                    $("#err_email").text("");
                }
            })

            $("[name=register]").click(function () {
                /*var registered_type = $('#registered_type').val();*/
                var name = $('[name=name]').val();
                /*var address = $('[name=branch_address]').val();*/
                var email = $('[name=email]').val();
                /*var mobile = $('[name=mobile]').val();
                var city = $('[name=sa_city]').val();
                var state = $('[name=sa_state]').val();
                var country = $('[name=sa_country]').val();           
                var gstin = $('[name=branch_gstin_number]').val();
                var state_code = $('[name=state_code]').val();*/
                /*if(gstin) var gstin_state_code = gstin.slice(0, 2);
                
                if (registered_type == null || registered_type == "") {
                    $("#err_registration_type").text('Please Select Registration Type');
                    return false;
                } else {
                    $("#err_registration_type").text("");
                }*/
                if (name == null || name == "") {
                    $("#err_name").text('Please Enter Name.');
                    return false;
                } else {
                    $("#err_name").text("");
                }
                
                if (!name.match(name_regex_spl)) {
                    $('#err_name').text("Please Enter Valid Name");
                    return false;
                } else {
                    $("#err_name").text("");
                }
                if (name.length < 3) {
                    $('#err_name').text("Please Enter Name Minimun 3 Character");
                    return false;
                } else {
                    $("#err_name").text("");
                }
                if (email == null || email == "") {
                    $("#err_email").text("Please Enter Email.");
                    return false;
                } else {
                    $("#err_email").text("");
                }
                if (!email.match(email_regex)) {
                    $('#err_email').text("Please Enter Valid Email");
                    return false;
                } else {
                    $("#err_email").text("");
                }
                if (email.length < 2) {
                    $('#err_email').text("Please Enter Valid Email");
                    return false;
                } else {
                    $("#err_email").text("");
                }
                
                /*if(gstin != '' && gstin != null){
                    if (gstin.length > 0) {
                        if (!gstin.match(gst_regex)) {
                            $('#err_branch_gstin_number').text("Please Enter Valid GSTIN Number.");
                            return false;
                        } else {
                            $("#err_branch_gstin_number").text("");
                        }
                        if ((gstin.length) < 15 || (gstin.length) > 15)
                        {
                            $("#err_branch_gstin_number").text("GSTIN Number should have length 15");
                            return false;
                        } else {
                            $("#err_branch_gstin_number").text("");
                        }
                    } else {
                        $("#err_branch_gstin_number").text("");
                    }
                }

                if (country == "" || country == null) {
                
                    $('#err_country').text("Please Select Country.");
                    return false;
                } else {
                    $('#err_country').text("");
                }
                if (state == "" || state == null) {
                    $('#err_state').text("Please Select State.");
                    return false;
                } else {
                    $('#err_state').text("");
                }
                //state validation copmlite.
                if (city == "" || city == null) {
                    $('#err_city').text("Please Select City.");
                    return false;
                } else {
                    $('#err_city').text("");
                }
              
                if (address == null || address == "") {
                    $("#err_branch_address").text(" Please Enter Address");
                    return false;
                } else {
                    $("#err_branch_address").text("");
                }*/
              
                
                //email validation complite.
                // if (mobile == null || mobile == "") {
                //     $("#err_mobile").text(mobile_empty);
                //     return false;
                // } else {
                //     $("#err_mobile").text("");
                // }
                /*if(mobile.length>1){
                    if (!mobile.match(mobile_regex)) {
                        $('#err_mobile').text("Please Enter Valid Mobile No.");
                        return false;
                    } else {
                        $("#err_mobile").text("");
                    }
                }*/
                $("[name=register]").addClass('downBtn');
                
                //mobile validation complite.
            });
        });

        function getCountryById(){
            var id = $('#sa_country').val();
            $('#sa_state').empty();
            $('#sa_city').empty();
            $.ajax({
                url: base_url + 'superadmin/general/get_state/'+id,
                method: "POST",
                data: {id: id},
                dataType: "JSON",
                success: function (data) {
                    var state = $('#sa_state');
                    var opt = document.createElement('option');
                    opt.text = 'Select State';
                    opt.value = '';
                    state.append(opt);
                    var city = $('#sa_city');
                    var opt1 = document.createElement('option');
                    opt1.text = 'Select City';
                    opt1.value = '';
                    city.append(opt1);
                    for (i = 0; i < data.length; i++) {
                        $('#sa_state').append('<option value="' + data[i].state_id + '" code="'+data[i].state_code+'">' + data[i].state_name + '</option>');
                    }
                }
            });
        }
    </script>
</html>