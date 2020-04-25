<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
$user_id = $this->uri->segment(3);
?>
<div class="content-wrapper">
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
                    Edit Users
                </li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <!-- Edit customer -->
                            Edit Users
                        </h3>
                    </div>
                    <form name="form" id="form" method="post" action="<?php echo base_url('auth/edit'); ?>">
                        <div class="box-body">                        
                            <div class="row">                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">
                                            First Name<span class="validation-color">*</span>
                                        </label>
                                        <input type="hidden" id="id" name="id" value="<?= $user->id; ?>">
                                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $user->first_name; ?>">
                                        <span class="validation-color" id="err_first_name"><?php echo form_error('first_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">
                                            Last Name<span class="validation-color">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $user->last_name; ?>">
                                        <span class="validation-color" id="err_last_name"><?php echo form_error('last_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 hide">
                                    <div class="form-group">
                                        <label for="customer_name">
                                            Company Name<span class="validation-color">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="company" name="company" value="<?php echo $user->company ?>">
                                        <span class="validation-color" id="err_customer_name"><?php echo form_error('customer_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">
                                            Email<span class="validation-color">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="email" name="email" value="<?php echo $user->email ?>">
                                        <span class="validation-color" id="err_email"><?php echo form_error('email'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">
                                            Phone<span class="validation-color">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $user->phone; ?>" maxlength="15">
                                        <span class="validation-color" id="err_phone"><?php echo form_error('phone'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">
                                            Password<span class="validation-color">*</span>
                                        </label>
                                        <div class="input-group">
                                          <input type="password" class="form-control" id="password" name="password" value="">
                                            <span class="input-group-addon"><span toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password1"></span></span>
                                          </div>
                                        <span class="validation-color" id="err_password"><?php echo form_error('password'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirm">
                                            Confirm Password<span class="validation-color">*</span>
                                        </label>

                                        <div class="input-group">
                                           <input type="password" class="form-control" id="password_confirm" name="password_confirm" value="">
                                            <span class="input-group-addon"><span toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password2"></span></span>
                                          </div>
                                        <span class="validation-color" id="err_password_confirm"><?php echo form_error('password_confirm'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="user_group">
                                           User Group<span class="validation-color">*</span>
                                        </label>
                                         <select class="form-control select2" id="cmb_group" class="group" name="cmb_group" style="width: 100%;">
                                                <option value="">Select User Group</option>
                                                <?php
                                                $user_group_id = (@$user_group[0]->group_id ? $user_group[0]->group_id : '0');
                                                foreach ($groups as $key) {
                                                    if($key->id == $user_group_id){
                                                        ?>
                                                        <option value='<?php echo $key->id ?>' selected><?php echo $key->name; ?> </option>
                                                        <?php
                                                    }else{?>
                                                    <option value='<?php echo $key->id ?>' ><?php echo $key->name; ?> </option>
                                                <?php }} ?>
                                            </select>
                                        <span class="validation-color" id="err_user_group"></span>
                                    </div>
                                </div>
                            </div>                          
                            <div class="row" style="display: none">
                                <?php
                                $i = 1;
                                $user_group_id = (@$user_group[0]->group_id ? $user_group[0]->group_id : '0');
                                foreach ($groups as $group) {
                                    $checked = "";
                                    if ($group->id == $user_group_id) $checked = "checked"; ?>
                                    <div class="col-md-2">
                                        <label>
                                            <input type="checkbox" class="" <?= $checked ?> value="<?= $group->id ?>" id="group<?= $i ?>" name="group" readonly> <?= $group->name ?></label>
                                    </div>
                                    <?php
                                    $i++;
                                }
                                ?>
                            </div>
                            <!--Hidden fiels -->
                            <input type="hidden" name="section_area" id="section_area" value="edit_user">
                            <input type="hidden" name="old_group" id="old_group" value="<?=$user_group_id; ?>">
                            <!--Hidden fiels -->
                            <div class="box-footer">
                                <button type="submit" id="user_submit" name="user_submit" class="btn btn-info">
                                    <!-- Add -->
                                    Update</button>
                                <span class="btn btn-default" id="cancel" onclick="cancel('auth')"><!-- Cancel -->
                                    Cancel</span>
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
