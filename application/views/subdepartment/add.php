<?php

defined('BASEPATH') OR exit('No direct script access allowed');



$this->load->view('layout/header');

?>

<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <h5>

            <ol class="breadcrumb">

                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> <!-- Dashboard --> Dashboard</a></li>

                <li><a href="<?php echo base_url('subcategory'); ?>">Subcategory </a></li>

                <li class="active"><!-- Add Subcategory -->Add Subcategory</li>

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

                        <h3 class="box-title"><!-- Add Subcategory -->Add Subcategory</h3>

                    </div>

                    <!-- /.box-header -->

                    <div class="box-body">

                        <div class="row">

                            <div class="col-md-6">

                                <form role="form" id="form" method="post" action="<?php echo base_url('subcategory/add_subcategory'); ?>">

                                    <div class="form-group">

                                        <label for="category"><!-- Select Category  -->Select Category<span class="validation-color">*</span></label>

                                        <select class="form-control select2" id="category_id_model" name="category_id_model" style="width: 100%;">

                                            <option value="">Select</option>

                                            <?php

                                            foreach ($data as $row)

                                            {

                                                echo "<option value='$row->category_id'" . set_select('category', $row->category_id) . ">$row->category_name</option>";

                                            }

                                            ?>

                                        </select>

                                        <span class="validation-color" id="err_category_id_model"><?php echo form_error('category'); ?></span>

                                    </div>

                                    <!-- /.form-group -->

                                    <div class="form-group">

                                        <label for="subcategory_name"><!-- Subcategory Name  -->Subcategory Name<span class="validation-color">*</span></label>

                                        <input type="text" class="form-control" id="subcategory_name" name="subcategory_name" value="<?php echo set_value('subcategory_name'); ?>">

                                        <span class="validation-color" id="err_subcategory_name"><?php echo form_error('subcategory_name'); ?></span>

                                    </div>

                            </div>

                            <div class="col-sm-12">

                                <div class="box-footer">

                                    <button type="submit" id="add_subcategory" class="btn btn-info"><!-- Add -->Add</button>

                                    <span class="btn btn-default" id="cancel" onclick="cancel('subcategory')"><!-- Cancel -->Cancel</span>

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

$this->load->view('layout/footer');

?>

<script src="<?php echo base_url('assets/js/subcategory/') ?>subcategory.js"></script>

