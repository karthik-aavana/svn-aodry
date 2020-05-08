<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$type = user_limit();
$scount = supplier_count();
//  if($type['coun']=50) || {
// header('location:test.php');
// }
$this->load->view('layout/header');
// $user_id = $this->uri->segment(3);
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>
                        <!-- Dashboard -->
                        Dashboard</a>
                </li>
                <li><a href="<?php echo base_url('auth'); ?>">
                        <!-- Customer -->
                        Users</a>
                </li>
                <li class="active">
                    <!-- Edit Customer -->
                    Create User
                </li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">                         
                            Add User
                        </h3>
                        <a class="btn btn-sm btn-default pull-right back_button" onclick1="cancel('auth')">Back</a>
                    </div>
                    <form name="form" id="form" method="post" action="<?php echo base_url('auth/create_new_user'); ?>">
                        <div class="box-body">                      
                            <div class="row">                            
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_name">
                                            First Name<span class="validation-color">*</span>
                                        </label>
                                        <input type="hidden" id="id" name="id" value="0">
                                        <input type="text" class="form-control" id="first_name" name="first_name" value="">
                                        <span class="validation-color" id="err_first_name"><?php echo form_error('customer_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_name">
                                            Last Name<span class="validation-color">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="">
                                        <span class="validation-color" id="err_last_name"><?php echo form_error('customer_name'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 hide">
                                    <div class="form-group">
                                        <label for="customer_name">
                                            Company Name<span class="validation-color">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="company" name="company" value="<?= $this->session->userdata('SESS_BRANCH_ID') ?>">
                                        <span class="validation-color" id="err_company"><?php echo form_error('customer_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_name">
                                            Email<span class="validation-color">*</span>
                                        </label>

                                        <input type="text" class="form-control" id="email" name="email" value="">
                                        <span class="validation-color" id="err_email"><?php echo form_error('customer_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_name">
                                            Mobile No<span class="validation-color">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="phone" name="phone" maxlength="15">
                                        <span class="validation-color" id="err_phone"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_name">
                                            Password<span class="validation-color">*</span>
                                        </label>
                                        <div class="input-group">
                                           <input type="password" class="form-control" id="password" name="password">
                                            <span class="input-group-addon"><span toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password1"></span></span>
                                          </div>
                                        <span class="validation-color" id="err_password"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_name">
                                           Confirm Password<span class="validation-color">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" value="">
                                            <span class="input-group-addon"><span toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password2"></span></span>
                                          </div>
                                        <span class="validation-color" id="err_password_confirm"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: none">
                                <?php
                                $i = 1;                                
                                foreach ($groups as $group) {
                                    ?>
                                    <div class="col-md-2"><label>
                                            <input type="checkbox" class="groups" <?php
                                            if ($group->name == "members") {
                                                echo "checked";
                                            }
                                            ?> value="<?= $group->id ?>" id="group<?= $i ?>" name="group" readonly> <?= $group->name ?></label>
                                    </div>
                                    <?php
                                    $i++;
                                }
                                ?>
                            </div>
                            <input type="hidden" name="section_area" id="section_area" value="add_user">
                            <div class="box-footer">
                                <button type="submit" id="user_submit" name="user_submit" class="btn btn-info">Add</button>

                                <span class="btn btn-default" id="cancel" onclick="cancel('auth')">Cancel</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
?>
<script>
$(document).on('click', '.toggle-password1', function() {
    $(this).toggleClass("fa-eye fa-eye-slash");    
    var input = $("#password");
    input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password');
});

$(document).on('click', '.toggle-password2', function() {
    $(this).toggleClass("fa-eye fa-eye-slash");    
    var input = $("#password_confirm");
    input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password');
});

    $(document).ready(function () {
        $('input[name="group"]').on('change', function () {
            $('input[name="group"]').not(this).prop('checked', false);
        });
    });
</script>
<script src="<?php echo base_url('assets/js/user/') ?>user.js"></script>
