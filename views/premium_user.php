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
        <link rel="stylesheet" href="<?php echo base_url(); ?>libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>/libs/ionicons/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
        <!-- iCheck -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/iCheck/square/blue.css">
        <!-- google fonts -->
        <link href="https://fonts.googleapis.com/css?family=Abril+Fatface" rel="stylesheet">

    </head>
    <body class="hold-transition ">
        <div class="login-box">
            <div class="login-logo" style="color: #3c8dbc;">
                <span style="font-size: 28px;">AO</span>DRY
            <!-- <img src = "<?= base_url('assets/images/taxbar1.png') ?>" width='200'> -->

                <h4><small>Billing | Accounting | Taxation</small></h4>

            </div>
            <div class="login-box-body">
                <?php
                if (isset($sucess))
                {

                    echo "<div class='alert alert-success'>
  <strong>Success!</strong> $sucess
</div>";
                }
                ?>
                <p class="login-box-msg">Enter Your Email to Become Premium user</p>

                <!-- <form action="../../index2.html" method="post"> -->
                <form method="post" action="<?= base_url("my_404/send_mail") ?>">



                    <div class="form-group has-feedback">
                        <input type="email" class="form-control" name="email" placeholder="Email">

                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>

                    <!-- /.col -->
                    <div class="col-xs-4">
                        <!-- <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button> -->
                        <input type="submit" name="submit" class="btn btn-success btn-block btn-flat">
                    </div>
                    <!-- /.col -->
            </div>
        </form>
        <!-- </form> -->

    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<!-- jQuery 2.2.3 -->
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
