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
                <li><a href="<?php echo base_url('service'); ?>">Modules</a></li>
                <li class="active">Assign Modules </li>
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
                        <h3 class="box-title">User Name : <span><?= $this->session->userdata("user_name");?></span></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                        <form role="form" id="form" method="post" action="<?php echo base_url('superadmin/modules/add_modules'); ?>" encType="multipart/form-data">
                        
                        <div class="row">
                        <input type="text" name="branch_id" value="<?= $this->session->userdata("branch_id");?>">
                        <input type="text" name="user_id" value="<?= $this->session->userdata("user_id");?>"> 
       
                            <?php 
                        count($modules);
                            foreach ($modules as $module){ ?>
                            <div class="col-md-2">
                            <div class="checkbox">
                            <label><input type="checkbox" id="groups<?= $module->module_id?>" name="<?=$module->module_name?>[]" value="<?= $module->module_id?>"><b><?=$module->module_name?></b></label><br>
                            <div style="margin-left: 30px" id="prevelages<?= $module->module_id?>">
                            <input type="checkbox" value ="view" name="<?=$module->module_name?>[]">view<br>
                            <input type="checkbox" value ="edit" name="<?=$module->module_name?>[]">edit <br>
                            <input type="checkbox" value ="delete" name="<?=$module->module_name?>[]">Delete 
                            </div>


                            </div>
                            </div>    
                            <?php
                                }
                            ?>
                            
                        </div>
                        <div class="col-sm-12">
                           <div class="box-footer">
                                    <button type="submit" id="service_modal_submit" class="btn btn-info">Add</button>
                                    <span class="btn btn-default" id="cancel" onclick="cancel('service')">Cancel</span>
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