<?php defined('BASEPATH') OR exit('No direct script access allowed');
$this -> load -> view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>  Dashboard </a></li>
                <li><a href="<?php echo base_url('discount'); ?>"><!-- Discount --> Discount</a></li>
                <li class="active"><!-- Add Discount --> Add Discount</li>
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
                        <h3 class="box-title"><!-- Add Discount --> Add Discount</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('discount/add_discount'); ?>">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="discount_name"><!-- Discount Name --> Discount Name <span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="discount_name" name="discount_name" value="<?php echo set_value('discount_name'); ?>">
                                        <input type="hidden" name="id" id="discount_id" value="0">
                                        <span class="validation-color" id="err_discount_name"><?php echo form_error('discount_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="discount_percentage"><!-- Discount Value --> Discount Value <span class="validation-color">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-right" id="discount_percentage" name="discount_percentage" value="<?php echo set_value('discount_percentage'); ?>">
                                            <span class="input-group-addon percentage_icon">%</span>
                                        </div>
                                        <span class="validation-color" id="err_discount_percentage"><?php echo form_error('discount_percentage'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4 mt-15">
                                    <div class="box-footer">
                                        <button type="submit" id="discount_submit" class="btn btn-info">Add</button>
                                        <button type="button" class="btn btn-info" id="cancel" onclick="cancel('discount')"><!-- Cancel --> Cancel</button>
                                    </div>
                                </div>
                        </form>
                    </div>
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
$this -> load -> view('layout/footer');
?>
<script src="<?php echo base_url('assets/js/discount/') ?>discount.js"></script>
