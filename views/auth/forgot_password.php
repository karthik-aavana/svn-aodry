<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Forgot password</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
        <!-- iCheck -->
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
                margin-bottom: 11%;
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
            .login-box, .register-box {
                width: 304px;                
                margin: 25% auto;
            }
            .login-box .box-title, .left-side-bar .box-title  {             
                border-bottom: 1px solid #ececec;
                padding-bottom: 8px;
                line-height: 1.5;
                color: #000;
                font-weight: 600;
            }       

            .login-box a{
                font-weight: 500;
                color: #333;
            }

            .left-side-bar .box-title{
                border: 0;
            }

            #rotate{
                margin-bottom: 10%;
            }
            #rotate div {
                font-size: 22px;   
                color: #333;
                font-family: 'Nunito Sans', sans-serif;
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
        </style>
    </head>
    <body class="hold-transition login-page">
        <div class="container">
            <div class="row">
                <div class="col-sm-7">
                    <div class="left-side-bar">
                        <img src = "<?= base_url('assets/images/aodry-black-logo.png') ?>">
<!--                        <h4><small>Billing | Accounting | Taxation</small></h4>-->
<!--                        <h1 class="mb-4"><small> Welcome to</small> <span>Aodry</span></h1>-->
                        <div id="rotate" class="mt-25"> 
                            <div>Complicated accounting made simple</div>
                            <div>Your trusted source for automated accounting</div>
                            <div>Safe, secure and time saving accounting</div>
                            <div>Experience hassle-free accounting</div>
                            <div>An accounting tool that saves your time</div>
                            <div>Grow your business with a time saving accounting tool</div>
                            <div>Switch to aodry for a smooth and easy accounting experience</div>
                        </div>
                        <div class="call_us">
                            <h4 class="box-title mb-25">To request a demo of the<br> <strong>Accounting Software</strong>, Call us at</h4>
                            <h2> +91 9900539903</h2>
                            <h2> +91 9900328729</h2>
                        </div>                       
                    </div>
                </div>
                <div class="col-sm-5 login_back">
                    <div class="login-box">
                        <div class="login-logo">
                            <?php
                            $data = $this->db->get('super_admin')->result();
                            if ($data[0] != "") {
                                if ($data[0]->logo) {
                                    ?>
                                    <span><img src = "<?= base_url('assets/images/Aodry- white-09.svg') ?>"></span>
                                    <!-- <img src="<?php echo base_url($data[0]->logo); ?>" width="50%"/></b><?php
                                } else {
                                    ?><img src="<?php echo base_url(); ?>/assets/images/logo.png" width="50%"/><?php } ?> -->
                            <!--  <h4><small>Billing | Accounting | Taxation</small></h4> -->
                                <h4><small><?php
                                        echo $data[0]->name;
                                        echo "<br/>Accounting | Billing | Inventory Management System";
                                        ?>
                                    <?php } ?></small></h4>
                        </div>
                        <!-- /.login-logo -->
                        <div class="login-box-body">
                            <h4 class="box-title">Forgot Password</h4>
                            <div id="infoMessage"><?php echo $message; ?></div>
                            <!-- <form action="../../index2.html" method="post"> -->
                            <?php echo form_open("auth/forgot_password"); ?>
                            <div class="form-group has-feedback">
                                <label for="login_code">User Code</label>
                                <?php echo form_input($login_code, '', 'class="form-control"'); ?>
                                <span class="glyphicon glyphicon-subtitles form-control-feedback"></span> 
                            </div>
                            <div class="form-group has-feedback">
                                <label for="identity"><?php echo (($type == 'email') ? sprintf(lang('forgot_password_email_label'), $identity_label) : sprintf(lang('forgot_password_identity_label'), $identity_label)); ?></label>
                                <?php echo form_input($identity, '', 'class="form-control"'); ?>
                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                            </div>
                            <div class="row">                                
                                <div class="col-xs-12">
                                    <div class="pull-right">
                                         <?php echo form_submit('submit', lang('forgot_password_submit_btn'), 'class="btn btn-primary btn-flat"'); ?>
                                          <a href="<?php echo base_url('auth/login');?>" class="btn btn-primary btn-flat">Cancel</a>      
                                    </div>
                                </div>
                            </div>
                            <?php echo form_close(); ?>
                            <!--                             <a href="#">I forgot my password</a>
                                                          <a href="register.html" class="text-center">Register a new membership</a>                             -->
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
        <!-- Bootstrap 3.3.6 -->
        <script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
        <!-- iCheck -->
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
