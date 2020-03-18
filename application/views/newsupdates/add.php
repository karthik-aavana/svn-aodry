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
                <li><a href="<?php echo base_url('newsupdates'); ?>"><!-- Category -->News and Updates</a></li>
                <li class="active"><!-- Add Category --> Adds and Updates</li>
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
                        <h3 class="box-title"><!-- Add Category -->Adds and Updates</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('newsupdates/add_newsupdates'); ?>">
                            <div class="well">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="category_name"><!-- Category Name -->Title <span class="validation-color">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title">
                                            <span class="validation-color" id="err_category_name"><?php echo form_error('category_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="category_name">Type<span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="category_type" name="type" style="width: 100%;">
                                                <option value="">Select</option>
                                                <option value='news'>News</option>
                                                <option value='update'>Update</option>
                                            </select>
                                            <span class="validation-color" id="err_category_type"><?php echo form_error('category_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="category_name">Users<span class="validation-color">*</span></label>
                                            <select multiple="multiple" class="form-control select2" id="usersSelect" name="usersSelect[]" style="width: 100%;">
                                                <?php
                                                foreach ($users as $key)
                                                {
                                                    ?>
                                                    <option value="<?= $key->id ?>"><?= $key->username ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                            <span class="validation-color" id="err_category_type"><?php echo form_error('category_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="box-footer mt-15">
                                            <button class="btn btn-default" id="selectAll" type="button">Select All</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="category_name">Description<span class="validation-color">*</span></label>
                                            <textarea class="ckeditor" name="description"></textarea>
                                            <span class="validation-color" id="err_category_type"><?php echo form_error('category_name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="box-footer">
                                            <button type="submit" id="news_updates_submit" class="btn btn-info">Add</button>
                                            <span class="btn btn-default" id="cancel" onclick="cancel('newsupdates')"><!-- Cancel -->Cancel</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php
$this->load->view('layout/footer');
?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#selectAll").click(function () {
            $("#usersSelect option").prop('selected', true);
        })
    })
</script>
<script src="<?php echo base_url('assets/js/category/') ?>category.js"></script>
