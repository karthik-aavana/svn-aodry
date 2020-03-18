<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('super_admin/layouts/header');

?>

<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <h5>

            <ol class="breadcrumb">

                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>

                <li><a href="<?php echo base_url('superadmin/users'); ?>">Users</a></li>

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



                        <form role="form" id="form" method="post" action="<?php echo base_url('superadmin/users/edit_user'); ?>" encType="multipart/form-data">

                        <div class="row">



                            <div class="col-md-6">

                                <div class="form-group">

                                    <label for="type_of_supply">Firm Name<span class="validation-color">*</span></label> 

                                    <select class="form-control select2" id="firm_id" name="firm_id" required>

                                        <option value="<?= $firm[0]->firm_id ?>"><?= $firm[0]->firm_name ?></option>

                                        <!-- <option value="">Select</option>

                                        <?php

                                        foreach ($firm as $key) {

                                            ?>

                                            <option value='<?php echo $key->firm_id ?>' <?php



                                            if($key->firm_id == $users[0]->firm_id){

                                                echo "selected";

                                            }



                                            ?>>

                                            <?php echo $key->firm_name ; ?>

                                        </option>

                                        <?php

                                        }

                                        ?> -->

                                    </select>

                                    <span class="validation-color" id="err_firm"></span>

                                </div>

                            </div>



                            <div class="col-md-6">

                                <div class="form-group">

                                    <label for="type_of_supply">Branch<span class="validation-color">*</span></label> 

                                    <input type="hidden" name="id" id="id" value="<?= $users[0]->id?>">

                                    <select class="form-control select2" id="branch_id" name="branch_id">

                                        <option value="<?= $branch[0]->branch_id ?>"><?= $branch[0]->branch_name ?></option>

                                        <!-- <option value="">Select</option>

                                        <?php

                                        foreach ($branch as $key) {

                                            ?>

                                            <option value='<?php echo $key->branch_id ?>' <?php



                                            if($key->branch_id == $users[0]->branch_id){

                                                echo "selected";

                                            }



                                            ?>>

                                            <?php echo $key->branch_name; ?>

                                        </option>

                                        <?php

                                        }

                                        ?> -->

                                    </select>

                                    <span class="validation-color" id="err_billing_country"><?php echo form_error('billing_country'); ?></span>

                                </div>

                            </div>

                        </div>

                        <div class="row">                            

                            <div class="col-md-6">

                                <div class="form-group">

                                    <label for="service_code">First Name<span class="validation-color">*</span></label>

                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?= $users[0]->first_name?>">

                                    <span class="validation-color" id="err_first_name"><?php echo form_error('service_code'); ?></span>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">

                                    <label for="customer_name">

                                        Last Name<span class="validation-color">*</span>

                                    </label>

                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?= $users[0]->last_name?>">

                                    <span class="validation-color" id="err_last_name"><?php echo form_error('last_name'); ?></span>

                                </div>

                            </div>                            

                        </div>

                        <div class="row">

                             <div class="col-md-6">

                                <div class="form-group">

                                    <label for="service_code">Email<span class="validation-color">*</span></label>

                                    <input type="text" class="form-control" id="email" name="email" value="<?= $users[0]->email?>" >

                                    <span class="validation-color" id="err_email"><?php echo form_error('email'); ?></span>

                                </div>

                            </div>



                            <div class="col-md-6">

                                <div class="form-group">

                                    <label for="service_code">Mobile Number</label>

                                    <input type="text" class="form-control" id="phone" name="phone" value="<?= $users[0]->phone?>" >

                                    <span class="validation-color" id="err_phone"><?php echo form_error('service_code'); ?></span>

                                </div>

                            </div>

                        </div>

                         <div class="row">

                            <div class="col-md-6">

                                <div class="form-group">

                                    <label for="service_code">Password</label>

                                    <input type="password" class="form-control" id="password" name="password" value="">

                                    <span class="validation-color" id="err_password"><?php echo form_error('password'); ?></span>

                                </div>

                            </div>

                            

                            <div class="col-md-6">



                                <div class="form-group">

                                    <label for="service_code">Confirm Password</label>

                                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" value="">

                                    <span class="validation-color" id="err_password_confirm"><?php echo form_error('password_confirm'); ?></span>

                                </div>

                            </div>

                            

                        </div>

                       <!--  <div class="row">



                            <?php 

                        count($modules);

                            foreach ($modules as $module){ ?>

                            <div class="col-md-2">

                            <div class="checkbox">

                            <label><input type="checkbox" id="groups<?= $module->module_id?>" name="groups<?= $module->module_id?>" value="<?= $module->module_id?>"><b><?=$module->module_name?></b></label><br>

                            <div style="margin-left: 30px" id="prevelages<?= $module->module_id?>">

                            <input type="checkbox" name="view">view&nbsp&nbsp&nbsp&nbsp<br>

                            <input type="checkbox" name="view">edit <br>

                            <input type="checkbox" name="view">Delete 

                            </div>





                            </div>

                            </div>    

                            <?php

                                }

                            ?>

                            

                        </div> -->

                <!--Hidden fiels --> 

                    <input type="hidden" name="section_area" id="section_area" value="edit_user">

                <!--Hidden fiels -->

                        <div class="col-sm-12">

                           <div class="box-footer">

                                    <button type="submit" id="user_submit" class="btn btn-info">Update</button>

                                    <span class="btn btn-default" id="cancel" onclick="cancel('superadmin/users')">Cancel</span>

                                </div>

                        </div>

                    </form>

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

// $this->load->view('tax/tax_modal');

// $this->load->view('general/modal/hsn_modal');

// $this->load->view('category/category_modal');

// $this->load->view('subcategory/subcategory_modal');

?>



<script src="<?php echo base_url('assets/js/user/') ?>user_supperadmin.js"></script>