<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    </section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('service'); ?>">Services</a></li>
            <li class="active">Add Service</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Service</h3>
                        <a class="btn btn-default pull-right" id="cancel" onclick="cancel('service')">Back</a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="well">
                            <form role="form" id="form" method="post" action="<?php echo base_url('service/add_Service'); ?>" encType="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="service_code">Service Code <span class="validation-color">*</span></label>
                                            <input type="text" class="form-control" id="service_code" name="service_code" value="<?php echo $invoice_number; ?>" tabindex="1" <?php
                                            if ($access_settings[0]->invoice_readonly == 'yes')
                                            {
                                            echo "readonly";
                                            }
                                            ?>>
                                            <span class="validation-color" id="err_service_code"><?php echo form_error('service_code'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" class="form-control" id="c_type" name="c_type" value="service">
                                            <label for="service_category">Select Category <span class="validation-color">*</span></label>
                                            <div class="input-group">
                                                                                                              <?php

if (in_array($category_module_id, $active_add))
{

    ?>
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#category_modal" data-name="service" class="pull-right new_category">+</a>
                                                </div>
<?php } ?>
                                                <select class="form-control select2" id="service_category" name="service_category" style="width: 100%;" tabindex="3">
                                                    <option value="">Select</option>
                                                    <?php
                                                    foreach ($service_category as $row)
                                                    {
                                                    echo "<option value='$row->category_id'" . set_select('service_category', $row->category_id) . ">$row->category_name</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <span class="validation-color" id="err_service_category"><?php echo form_error('service_category'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="service_price">Unit Price <span class="validation-color">*</span></label>
                                            <input type="text" class="form-control" id="service_price" name="service_price" value="<?php echo set_value('service_price'); ?>" tabindex="5">
                                            <span class="validation-color" id="err_service_price"><?php echo form_error('service_price'); ?></span>
                                        </div>
                                        <?php
                                            if($access_settings[0]->tax_type=="gst")
                                            {
                                                ?>
                                        <div class="form-group">
                                            <label for="service_hsn_sac_code">Service SAC Code </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#sac_modal" class="pull-right"><span class="fa fa-eye"></span></a>
                                                </div>
                                                <input type="text" class="form-control" id="service_hsn_sac_code" name="service_hsn_sac_code" value="<?php echo set_value('service_hsn_sac_code'); ?>" tabindex="7">
                                                </div>
                                                <span class="validation-color" id="err_service_hsn_sac_code"><?php echo form_error('service_hsn_sac_code'); ?></span>
                                            
                                        </div>
                                            <?php } ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="service_name">Service Name<span class="validation-color">*</span></label>
                                            <input type="hidden" name="service_id" id="service_id" value="0">
                                            <input type="text" class="form-control" id="service_name" name="service_name" value="<?php echo set_value('service_name'); ?>" tabindex="2">
                                            <span class="validation-color" id="err_service_name"><?php echo form_error('service_name'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="service_subcategory">Select Subcategory</label>
                                            <div class="input-group">
                                                                                                                  <?php

if (in_array($subcategory_module_id, $active_add))
{

    ?>
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#subcategory_modal" data-name="service" class="pull-right open_subcategory_modal new_subcategory">+</a>
                                                </div>
<?php } ?>
                                                <select class="form-control select2" id="service_subcategory" name="service_subcategory" style="width: 100%;" tabindex="4">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                            <span class="validation-color" id="err_service_subcategory"><?php echo form_error('service_subcategory'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="service_tax">Select Service Tax</label>
                                            <div class="input-group">
                                                                                                                  <?php

if (in_array($tax_module_id, $active_add))
{

    ?>
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#tax_modal" data-name="service" class="pull-right new_tax">+</a>
                                                </div>
<?php } ?>
                                                <select class="form-control select2" id="service_tax" name="service_tax" style="width: 100%;" tabindex="6">
                                                    <option value="0-0">No Tax</option>
                                                    <?php
                                                    foreach ($tax as $row)
                                                    {
                                                    echo "<option value='$row->tax_id-$row->tax_value'" . set_select('service_tax', $row->tax_id . "-" . $row->tax_value) . ">$row->tax_name</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <span class="validation-color" id="err_service_tax"><?php echo form_error('service_tax'); ?></span>
                                           
                                        </div>

                                            <?php 
    if($access_settings[0]->tds_visible =="yes")
    {

    ?>
                                                        <div class="form-group">
                                                <label for="service_hsn_sac_code">Select service TDS/TCS </label>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <a href="" data-toggle="modal" data-target="#tds_modal" class="pull-right"><span class="fa fa-eye"></span></a>
                                                    </div>
                                                    <input type="text" class="form-control" id="service_tds_code" name="service_tds_code" value="" readonly>
                                                     <input type="hidden" class="form-control" id="tds_id" name="tds_id" value="" >
                                                </div>
                                                <span class="validation-color" id="err_service_tds_code"></span>
                                            </div>
    <?php } ?>

                                    </div>
                                </div>
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
if (in_array($tax_module_id, $active_add))
{
$this->load->view('tax/tax_modal');
}
    $this->load->view('service/sac_modal');
if (in_array($category_module_id, $active_add))
{
$this->load->view('category/category_modal');
}
if (in_array($subcategory_module_id, $active_add))
{
$this->load->view('subcategory/subcategory_modal');
}
$this->load->view('service/tds_modal');
    ?>
    <script type="text/javascript">
    $(document).ready(function () {
    $('#category_type').html("<option value='service'>Service</option>");
    });
    </script>
    <script src="<?php echo base_url('assets/js/service/') ?>service.js"></script>