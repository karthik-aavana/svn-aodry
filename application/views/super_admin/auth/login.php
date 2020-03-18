<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Taxbar | Log in</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>/libs/ionicons/css/ionicons.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/iCheck/square/blue.css">
    <style type="text/css">
        @media (min-width: 768px){
        .login-box{
            max-width: 304px;
            margin: 15% auto;
        }
    }
    </style>
    </head>

    <body class="hold-transition login-page">
<div class="container">
            <div class="row">
                <div class="col-sm-12">
        <div class="login-box">

            <div class="login-logo">

                <?php

                $data = $this->db->get('super_admin')->result();

                if ($data[0] != "") {

                    if ($data[0]->logo) {

                        ?>

                        <b> <img src="<?php echo base_url($data[0]->logo); ?>" width="50%"/></b><?php } else { ?><img src="<?php echo base_url(); ?>/assets/images/logo.png" width="50%"/><?php } ?><br><h5><?php

                        echo "<h4><small>" . $data[0]->name . "</h4></small>";

                        echo "<h4><small>Accounting | Billing | Inventory Management System </h4></small> ";

                        ?></h5>

                <?php } ?>

            </div>

            <div class="login-box-body">

                <p class="login-box-msg">Sign in to start your session</p>

                <div id="infoMessage" style="color: red;"><?php echo $message; ?></div>                

                <?php echo form_open("superadmin/auth/login"); ?>

                <div class="form-group has-feedback">                   

                    <?php echo form_input($branch_code, '', 'class="form-control" placeholder="User Code"'); ?>

                    <span class="glyphicon glyphicon-flag form-control-feedback"></span>

                </div>

                <div class="form-group has-feedback">                  

                    <?php echo form_input($identity, '', 'class="form-control" placeholder="Email"'); ?>

                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>

                </div>

                <div class="form-group has-feedback">                  

                    <?php echo form_input($password, '', 'class="form-control" placeholder="Password"'); ?>

                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                </div>

                <div class="row">

                    <div class="col-xs-8">

                        <div class="checkbox icheck">

                            <label>

                             Remember Me <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"'); ?>

                            </label>

                        </div>

                    </div>                    

                    <div class="col-xs-4">

                        <!-- <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button> -->

                        <?php echo form_submit('submit', 'Login', 'class="btn btn-success btn-block btn-flat"'); ?>

                    </div>                  

                </div>

                <?php echo form_close(); ?>

            </div>
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

        </script>

    </body>

</html>