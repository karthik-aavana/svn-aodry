<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('super_admin/layouts/header');
// $user_id = $this->uri->segment(3);
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('superadmin/auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> 
                        <!-- Dashboard -->
                        Dashboard</a>
                </li>
                <li><a href="<?php echo base_url('superadmin/users'); ?>"> Users</a></li>
                <li class="active">
                    <!-- Edit Customer -->
                    Add User
                </li>
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
                        <h3 class="box-title">
                            <!--  Add User -->
                           Add User
                        </h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                       
                            <form role="form" id="form" method="post" action="<?php echo base_url('superadmin/users/add_user'); ?>">
                                 <div class="row">
                                        <div class="col-md-6">
                                                <div class="form-group">
                                       <label for="type_of_supply">Firm Name <span class="validation-color">*</span></label> 
                                       <select class="form-control select2" id="firm_id" name="firm_id" required>
                                       <option value="">Select</option>
                                       <?php
                                      foreach ($firm as $key) { ?>
                                        <option value='<?php echo $key->firm_id ?>'><?php echo $key->firm_name ; ?> </option>
                                       <?php } ?>
                                    </select>
                                       <span class="validation-color" id="err_firm"></span>
                                    </div>
                                            </div>
                                
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customer_name">
                                                    Branch<span class="validation-color">*</span>
                                                </label>
                                                <select class="form-control select2" id="branch_id" name="branch_id" required="true">
                                                   <option value="">select</option>
                                                </select>
                                              
                                                <span class="validation-color" id="err_branch"></span>
                                            </div>
                                        </div>
                                        
                                          
                                      </div>
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
                                               <input type="text" class="form-control" id="company" name="company" value="">
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
                                                    Mobile No
                                                </label>
                                               
                                                 <input type="text" class="form-control" id="phone" name="phone" >
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
                                               
                                                 <input type="password" class="form-control" id="password" name="password">
                                                <span class="validation-color" id="err_password"></span>
                                            </div> 
                                            </div>
                                             <div class="col-md-6">
                                          <div class="form-group">
                                                <label for="customer_name">
                                                    Password Confirm <span class="validation-color">*</span>
                                                </label>
                                               
                                                 <input type="password" class="form-control" id="password_confirm" name="password_confirm" value="">
                                                <span class="validation-color" id="err_password_confirm"></span>
                                            </div> 
                                            </div> 
                                          </div>
                                          <div class="row">
                                             
                                        </div>
                                        <div class="row" style="display: none;">
                                           <?php
                                           $i=1;
                                           // print_r($groups);
                                           foreach($groups as $group)
                                           {
                                           ?>
                                         <div class="col-md-2"><label>
                                        <input type="checkbox" <?php if ($group->name=="admin") {echo "checked";} ?> value="<?= $group->id?>" id="group<?= $i ?>" name="group" readonly> <?= $group->name?></label> 
                                        </div>
                                        <?php
                                        $i++; }
                                        ?>
                                        </div>
              <!--Hidden fiels --> 
                  <input type="hidden" name="section_area" id="section_area" value="add_user">
              <!--Hidden fiels --> 
                                       
                            <div class="col-sm-12">
                            <div class="box-footer">
                                <button type="submit" id="user_submit" name="user_submit" class="btn btn-info">
                                    <!-- Add -->
                                    Add</button>
                                <span class="btn btn-default" id="cancel" onclick="cancel('superadmin/users')"><!-- Cancel -->
                                    Cancel</span>
                            </div>
                        </div>
                                    
                        
                        </form>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<?php
$this->load->view('layout/footer');
?>
<script>
  $(document).ready(function () {
    $('input[name="group"]').on('change', function() {
    $('input[name="group"]').not(this).prop('checked', false);
  });
});
     $('#firm_id').change(function () {
        var fid = $(this).val();
        
        $('#branch_id').html('');
        $('#branch_id').append('<option value="">Select</option>');
        $.ajax({
            url: base_url +'superadmin/branch/get_branch/'+fid,
            type: "GET",
            dataType: "JSON",           
            success: function (data) {
            
                for (i = 0; i < data.length; i++) {
                    $('#branch_id').append('<option value="' + data[i].branch_id + '">' + data[i].branch_name + '</option>');
                }
               
            }
        });
    });       
</script>
<script src="<?php echo base_url('assets/js/user/') ?>user_supperadmin.js"></script>