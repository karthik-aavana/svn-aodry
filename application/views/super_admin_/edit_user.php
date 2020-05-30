<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// $p = array('admin', 'manager');
// if (!(in_array($this->session->userdata('type'), $p))) {
//     redirect('auth');
// }
$this->load->view('layout/header');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?php echo base_url('service'); ?>">Users</a></li>
                <li class="active">Edit Users</li>
            </ol>
        </h5>    
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Users</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <?php
                      foreach ($data as $key) {
                        # code...
                      
                      ?>
                        <form role="form" id="form" method="post" action="<?php echo base_url('superadmin/admin/edit_users'); ?>" encType="multipart/form-data">
                        <div class="row">                            
                            <div class="col-md-6">
                                <input type="hidden" name="id" value="<?= $key->id?>" id="branch_id">
                                 <input type="hidden" name="branch_code" value="<?= $this->session->userdata('branch_code')?>" id="branch_code">
                                <div class="form-group">
                                    <label for="service_code">First Name / User Name<span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="user_name" name="user_name" value="<?= $key->username?>" >
                                    <span class="validation-color" id="err_user_name"><?php echo form_error('service_code'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="service_code">Mobile Number <span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="user_no" name="user_no" value="<?= $key->phone?>" >
                                    <span class="validation-color" id="err_user_no"><?php echo form_error('service_code'); ?></span>
                                </div>
                            </div>

                            
                        </div>
                        <div class="row">
                             <div class="col-md-6">

                                <div class="form-group">
                                    <label for="service_code">Last Name <span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?= $key->last_name?>" >
                                    <span class="validation-color" id="err_last_name"><?php echo form_error('service_code'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="service_code">Password<span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="password" name="password" value="" >
                                    <span class="validation-color" id="err_password"><?php echo form_error('service_code'); ?></span>
                                </div>
                            </div>
                            
                        </div>
                         <div class="row">
                             <div class="col-md-6">

                                <div class="form-group">
                                    <label for="service_code">Email <span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="email_id" name="email_id" value="<?= $key->email?>" >
                                    <span class="validation-color" id="err_email_id"><?php echo form_error('service_code'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="service_code">Confirm Password <span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="confirm_password" name="confirm_password" value="" >
                                    <span class="validation-color" id="err_confirm_password"><?php echo form_error('service_code'); ?></span>
                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="col-sm-12">
                           <div class="box-footer">
                                    <button type="submit" id="service_modal_submit" class="btn btn-info">Add</button>
                                    <span class="btn btn-default" id="cancel" onclick="cancel('service')">Cancel</span>
                                </div>
                        </div>
                    </form>
                    <?php
                  }
                    ?>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!--/.col (right) -->
            </div>
            <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php


$this->load->view('layout/footer');
$this->load->view('tax/tax_modal');
$this->load->view('general/modal/hsn_modal');
$this->load->view('category/category_modal');
$this->load->view('subcategory/subcategory_modal');

?>

<script type="text/javascript">
$(document).ready(function () {  
    $('#category_type').html("<option value='service'>Service</option>");
});
</script>

