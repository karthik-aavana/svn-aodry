<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> <!-- Dashboard --> Dashboard</a></li>
                <li><a href="<?php echo base_url('category'); ?>"><!-- Category -->Category</a></li>
                <li class="active"><!-- Add Category --> Add Category</li>
            </ol>
        </h5>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><!-- Add Category -->Add Category</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('category/add_category'); ?>">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="category_name"><!-- Category Name -->Category Name <span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo set_value('category_name'); ?>">
                                    <span class="validation-color" id="err_category_name"><?php echo form_error('category_name'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="category_name">Category Type <span class="validation-color">*</span></label>
                                    <select class="form-control select2" id="category_type" name="category_type" style="width: 100%;">
                                        <option value="">Select</option>
                                        <option value='service'>Service</option>
                                        <option value='product'>Goods</option>
                                    </select>
                                    <span class="validation-color" id="err_category_type"><?php echo form_error('category_name'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="box-footer">
                                    <button type="submit" id="add_category" class="btn btn-info">Add</button>
                                    <span class="btn btn-default" id="cancel" onclick="cancel('category')"><!-- Cancel -->Cancel</span>
                                </div>
                            </div>
                        </form>
                    </div>
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
<!-- /.content-wrapper -->
<?php
$this->load->view('layout/footer');
?>
<script src="<?php echo base_url('assets/js/category/') ?>category.js"></script>
