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
                <li><a href="<?php echo base_url('subcategory'); ?>"> Subcategory </a></li>
                <li class="active"><!-- Edit Subcategory --> Edit Subcategory</li>
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
                        <h3 class="box-title"><!-- Edit Subcategory --> Edit Subcategory</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-sm-6">
                            <form role="form" id="form" method="post" action="<?php echo base_url('subcategory/edit_subcategory'); ?>">
                                <?php foreach ($data as $row)
                                {
                                    ?>
                                    <div class="form-group">
                                        <label for="category"><!-- Select Category --> Select Category*</span></label>
                                        <select class="form-control select2" id="category_id_model" name="category_id_model" style="width: 100%;">
                                            <option value="">Select</option>
                                            <?php
                                            foreach ($category as $key)
                                            {
                                                ?>
                                                <option value='<?php echo $key->category_id ?>' <?php
                                                if ($key->category_id == $row->category_id)
                                                {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $key->category_name ?></option>
        <?php
    }
    ?>
                                        </select>
                                        <span class="validation-color" id="err_category_id_model"><?php echo form_error('category'); ?></span>
                                    </div>
                                    <!-- /.form-group -->
                                    <div class="form-group">
                                        <label for="subcategory_name"><!-- Subcategory Name --> Subcategory Name <span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="subcategory_name" name="subcategory_name" value="<?php echo $row->sub_category_name; ?>">
                                        <input type="hidden" id="id" name="id" value="<?php echo $row->sub_category_id; ?>">

                                        <span class="validation-color" id="err_subcategory_name"><?php echo form_error('subcategory_name'); ?></span>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="box-footer">
                                            <button type="submit" id="add_subcategory" class="btn btn-info"><!-- Update --> Update</button>
                                            <span class="btn btn-default" id="cancel" onclick="cancel('subcategory')"><!-- Cancel --> Cancel</span>
                                        </div>
                                    </div>
<?php } ?>
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
<script src="<?php echo base_url('assets/js/subcategory/') ?>subcategory.js"></script>
