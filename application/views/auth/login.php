<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>AODRY | Log in</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="shortcut icon" type="image/png" href="<?php echo base_url('assets/images/favicon.png'); ?>" />        
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lib/font-awesome/4.5.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lib/ionicons/css/ionicons.min.css">
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

            .login-box .checkbox label{
                font-weight: 500;
                color: #333;
            }
            
            .login-box .box-title, .left-side-bar .box-title  {             
                border-bottom: 1px solid #ececec;
                padding-bottom: 8px;
                line-height: 1.5;
                color: #000;
            }       

            .link_style{
                color: #333;
                border-bottom: 1px solid #fcd23e;
            }            
            hr{
                border: none;
            }
            .login-box a{
                font-weight: 500;
                color: #333;
            }

            .left-side-bar .box-title{
                border: 0;
            }
            #rotate{
                 min-height: 50px;
            }
            #rotate div {
                font-size: 22px;   
                color: #333;                
                font-weight: 600;
            }      

            .footer-copyright {
                background: #fff;
                box-shadow: 0 2px 6px 0 rgba(0,0,0,.12), inset 0 -1px 0 0 #dadce0;
                transition: transform .4s,background .4s;
                z-index: 100;
                float: left;
                width: 100%;
                height: auto;            
                padding: 15px;
                text-align: center;
            }

            .footer-copyright a{
                color: #333;
            }
            .footer_pd{
                padding-right:0px!important;
                padding-left:0px!important;
                position: fixed;
                bottom: 0;
                width: 100%;
            }
            .footer_content a{
                color: #012b72;
                font-size: 15px;
                font-weight: 600;
            }
        </style>
    </head>
     <?php
            $this->load->view('layout/login_header');
        ?>
    <body class="hold-transition login-page">
        <div class="container" style="margin-bottom: 84px;">
            <div class="row">
                <div class="col-sm-7">
                    <div class="left-side-bar">
                       <!--  <img src = "<?= base_url('assets/images/aodry-black-logo.png') ?>"> -->
<!--                        <h4><small>Billing | Accounting | Taxation</small></h4>-->
<!--                        <h1 class="mb-4"><small> Welcome to</small> <span>Aodry</span></h1>-->
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
                            <h2> +91-9900328729</h2>
                            <h2> +91-9900539903</h2>
                            <h2> +91-80-40909797</h2>
                        </div>   
                        <hr>
                        <div class="call_us">
                            <h4 class="box-title">
                                <a class="link_style" href="https://calendly.com/harsha-rajaiah/" target="_blank">Click here</a> to request a personalized demo
                            </h4>
                        </div>                    
                    </div>
                </div>
                <div class="col-sm-5 login_back">
                    <div class="login-box">
                        <div class="login-logo">
                            <span><img src = "<?= base_url('assets/images/Aodry- white-09.svg') ?>"></span>
                            <!-- <img src = "<?= base_url('assets/images/taxbar1.png') ?>" width='200'> -->
                            <h4><small>Billing | Accounting | Taxation</small></h4>
                        </div>
                        <div class="login-box-body">
                            <h4 class="box-title">Sign In</h4>
                            <span style="color: #000000;font-weight: 500;">Don't have an account? <a href="signup" style="color: #012b72;font-weight: 500">Sign up</a></span>
                            <!-- <p class="login-box-msg text-left">This is a secure system and you will need to provide your login details to access the site.</p> -->
                            <?php
                                if(@$message){
                                    preg_match('/(success)/', $message, $matches);
                                    if(!empty($matches)){ ?>
                                        <div id="infoMessage" style="color: green;margin-top: 10px;"><?php echo $message; ?></div>
                                    <?php }else{ ?>
                                        <div id="infoMessage" style="color: red;margin-top: 10px;"><?php echo $message; ?></div>
                                    <?php }
                                }
                            ?>
                            <!-- <form action="../../index2.html" method="post"> -->
                            <?php echo form_open("auth/login"); ?>
                            <!-- input type="text" class="form-control" placeholder="company_code" -->
                            <div class="form-group has-feedback" style="margin-top: 13px;">
                                <!-- <input type="email" class="form-control" placeholder="Email"> -->
                                <!--                                <label>User Code</label>-->
                                <?php echo form_input($branch_code, '', 'class="form-control" placeholder="User Code"'); ?>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <!-- <input type="email" class="form-control" placeholder="Email"> -->
                                <!--                                <label>Email ID</label>-->
                                <?php echo form_input($identity, '', 'class="form-control" placeholder="Email"'); ?>
                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <!-- <input type="password" class="form-control" placeholder="Password"> -->
                                <!--                                <label>Password</label>-->
                                <?php echo form_input($password, '', 'class="form-control" placeholder="Password"'); ?>
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            </div>
                            <div class="row">
                                <div class="col-xs-8" style="margin-bottom: 15px;">
                                    <div class="checkbox icheck">
                                        <label>
                                            Remember Me <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"'); ?>
                                        </label>
                                    </div>
                                </div>                           
                                <div class="col-xs-4">
                                    <!-- <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button> -->
                                   <!--  <?php echo form_submit('submit', lang('login_submit_btn'), 'class="btn btn-primary btn-block btn-flat"'); ?> -->
                                   <input type="submit" name="submit" value="Next" class="btn btn-primary btn-block btn-flat">
                                </div>
                            </div>
                            <?php echo form_close(); ?>
                            <a href="forgot_password" style="line-height:2.5">Forgot your password?</a><br>
                            <!-- <span>Don't have account? </span><a href="signup" style="color: #012b72">Sign up</a> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="container-fluid footer_pd" style="">
                <div class="footer-copyright">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="footer_content">© 2019 Copyright. Aodry is a product by <a href="https://www.aavana.in" target="_blank" style=""> Aavana Corporate Solutions PVT LTD, </a> 
                                <a href="http://aodry.com/privacy-policy.php" target="_blank" style="padding-right: 4px;padding-left:5px;">Privacy Policy,</a> 
                                <a href="http://aodry.com/terms.php" target="_blank">Terms & Conditions</a>
                            </div>
                        </div>
                    </div>
                </div>    
             </div>

    
        <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/iCheck/icheck.min.js"></script>
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
</html>
