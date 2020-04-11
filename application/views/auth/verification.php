
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>AODRY | Log in</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lib/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lib/ionicons/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
        <!-- iCheck -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/iCheck/square/blue.css">
        <!-- google fonts -->
        <link href="https://fonts.googleapis.com/css?family=Abril+Fatface" rel="stylesheet">

        <style>
            .login-box .box-title, .left-side-bar .box-title {
                border-bottom: 1px solid #ececec;
                padding-bottom: 8px;
                line-height: 1.5;
                color: #000;
                font-weight: 600;
            }

            .login-box a {
                font-weight: 500;
                color: #333;
            }

            .left-side-bar .box-title {
                border: 0;
            }

            #rotate {
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

            .footer-copyright a {
                color: #333;
            }
        </style>
    </head>
    <body class="hold-transition login-page">    	
        <div class="col-sm-12">    	
            <div class="login-box">
                <div class="login-logo">
                    <span style="font-size: 28px;">AO</span>DRY
                <!-- <img src = "<?= base_url('assets/images/taxbar1.png') ?>" width='200'> -->
                    <h4><small>Billing | Accounting | Taxation</small></h4>
                </div>      
                <div class="login-box-body">
                    <h4 class="box-title">Change Password</h4>
                    <!-- <p class="login-box-msg"><?= lang('reset_password_heading') ?></p> -->
                    <div id="infoMessage" style="color: red;"><?php echo $message; ?></div>
                    <!--
                    <h1><?php echo lang('reset_password_heading'); ?></h1>
    
                    <div id="infoMessage"><?php echo $message; ?></div> -->

                    <?php echo form_open('auth/reset_password/' . $code); ?>	
                    <div class="form-group has-feedback">
                        <label>Password</label>
                        <?php echo form_input($new_password, '', 'class="form-control"'); ?>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>	                
                    <div class="form-group has-feedback">
                        <?php echo lang('reset_password_new_password_confirm_label', 'new_password_confirm'); ?> <br />
                        <?php echo form_input($new_password_confirm, '', 'class="form-control"'); ?>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <?php echo form_input($user_id); ?>
                    <?php echo form_input($branch_id); ?>
                    <?php echo form_hidden($csrf); ?>
                    <div class="row">                                
                        <div class="col-xs-12">
                            <div class="pull-right">                                  
                                <?php echo form_submit('submit', lang('reset_password_submit_btn'), 'class="btn btn-primary btn-flat"'); ?>
                                 <a href="<?php echo base_url('auth/login'); ?>" class="btn btn-primary btn-flat">Cancel</a>   
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
        <?php echo form_close(); ?>
