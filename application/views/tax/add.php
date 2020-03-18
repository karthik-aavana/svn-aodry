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

                <li><a href="<?php echo base_url('tax'); ?>">Tax</a></li>

                <li class="active">Add Tax</li>

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

                        <h3 class="box-title"><!-- Add Tax --> Add Tax</h3>

                    </div>

                    <!-- /.box-header -->

                    <div class="box-body">

                        <form role="form" id="form" method="post" action="<?php echo base_url('tax/add_tax'); ?>">

                            <div class="col-sm-3">

                                <div class="form-group">

                                    <label for="tax_name"><!-- Tax Name --> Tax Name <span class="validation-color">*</span></label>

                                    <input type="text" class="form-control" id="tax_name" name="tax_name" value="<?php echo set_value('tax_name'); ?>" tabindex="1">

                                    <input type="hidden" class="form-control" id="tax_id" name="id" value="0" >

                                    <span class="validation-color" id="err_tax_name"><?php echo form_error('tax_name'); ?></span>

                                </div>

                            </div>

                            <div class="col-sm-3">

                                <div class="form-group sales">

                                    <label for="tax_value">Tax Rate <span class="validation-color">*</span></label>

                                    <div class="input-group">

                                        <input type="text" class="form-control text-right" id="tax_value" name="tax_value" value="<?php echo set_value('tax_value'); ?>" tabindex="2">

                                        <span class="input-group-addon">%</span>

                                    </div>

                                    <span class="validation-color" id="err_tax_value"><?php echo form_error('tax_value'); ?></span>

                                </div>

                            </div>

                            <div class="col-sm-3">

                                <div class="form-group">

                                    <label for="description"><!-- Description --> Description</label>

                                    <textarea class="form-control" id="description" name="description" tabindex="3"><?php echo set_value('description'); ?></textarea>

                                    <span class="validation-color" id="err_description"></span>

                                </div>

                            </div>

                            <div class="col-sm-3 mt-15">

                                <div class="box-footer">

                                    <button type="submit" id="add_tax" class="btn btn-info"><!-- Add --> Add</button>

                                    <span class="btn btn-default" id="cancel" onclick="cancel('tax')"><!-- Cancel --> Cancel</span>

                                </div>

                            </div>

                        </form>

                    </div>

                </div> <!-- /.box-body -->

            </div><!-- /.box -->

        </div><!--/.col (right) -->

    </section> <!-- /.content -->

</div><!-- /.content-wrapper -->

<?php

$this->load->view('layout/footer');

?>

<script src="<?php echo base_url('assets/js/tax/') ?>tax.js"></script>

