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
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('service'); ?>">Services</a></li>
            <li class="active">Edit Service</li>
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
                        <h3 class="box-title">Edit New Service</h3>
                        <a class="btn btn-default pull-right" id="cancel" onclick="cancel('service')">Back</a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                       
                        <div class="well">
                            <form role="form" id="form" method="post" action="<?php echo base_url('service/edit_service'); ?>" encType="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="hidden" name="service_id" id="service_id" value="<?= $data[0]->service_id ?>">
                                        <div class="form-group">
                                            <label for="service_code">Service Code<span class="validation-color">*</span></label>
                                            <input type="text" class="form-control" id="service_code" name="service_code" value="<?= $data[0]->service_code ?>" tabindex="1" <?php
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
                                                        <?php foreach ($service_category as $cat)
                                                        {
                                                        ?>
                                                        <option value='<?php echo $cat->category_id ?>' <?php
                                                            if ($cat->category_id == $data[0]->service_category_id)
                                                            {
                                                            echo "selected";
                                                            }
                                                        ?>><?php echo $cat->category_name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <span class="validation-color" id="err_service_category"><?php echo form_error('service_category'); ?></span>
                                            </div>
                                                        
                                            <div class="form-group">
                                                <label for="service_price">Unit Price <span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" id="service_price" name="service_price" value="<?= precise_amount($data[0]->service_price) ?>" tabindex="5">
                                                <span class="validation-color" id="err_service_price"><?php echo form_error('service_price'); ?></span>
                                            </div>
 <?php
                                            if($access_settings[0]->tax_type=="gst" || $data[0]->service_hsn_sac_code !="" )
                                            {
                                                ?>
                                            <div class="form-group">
                                                <label for="service_hsn_sac_code">Service SAC Code <span class="validation-color">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <a href="" data-toggle="modal" data-target="#sac_modal" class="pull-right"><span class="fa fa-eye"></span></a>
                                                    </div>
                                                    <input type="text" class="form-control" id="service_hsn_sac_code" name="service_hsn_sac_code" value="<?= $data[0]->service_hsn_sac_code ?>" tabindex="7"></div>
                                                    <span class="validation-color" id="err_service_hsn_sac_code"><?php echo form_error('service_hsn_sac_code'); ?></span>
                                                
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="service_name">Service Name<span class="validation-color">*</span></label>
                                               <input type="text" class="form-control" id="service_name" name="service_name" value="<?= $data[0]->service_name; ?>" tabindex="2">
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
                                                <select class="form-control select2" id="service_subcategory" name="service_subcategory" style="width: 100%;" tabindex="6">
                                                    <option value="">Select</option>
                                                    <?php foreach ($service_subcategory as $sub)
                                                    {
                                                        ?>
                                                        <option value='<?php echo $sub->sub_category_id ?>' <?php
                                                                if ($sub->sub_category_id == $data[0]->service_subcategory_id)
                                                                {
                                                                    echo "selected";
                                                                }
                                                                ?>><?php echo $sub->sub_category_name ?></option>
<?php } ?>
                                                </select>
                                                    </div>
                                                    <span class="validation-color" id="err_service_subcategory"><?php echo form_error('service_subcategory'); ?></span>
                                                </div>
                                                               <?php
                        if($access_settings[0]->tax_type=="gst" || $access_settings[0]->tax_type =="single_tax" || $data[0]->service_tax_id !=0 || $data[0]->service_tax_id !="")
                        {
                            ?>
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
                                                        <option value="0">Select</option>
                                                        <?php foreach ($tax as $t)
                                                        {
                                                        ?>
                                                        <option value='<?php echo $t->tax_id . "-" . $t->tax_value; ?>'
                                                            <?php
                                                            if ($t->tax_id == $data[0]->service_tax_id)
                                                            {
                                                            echo "selected";
                                                            }
                                                            ?>>
                                                            <?php echo $t->tax_name ?>
                                                        </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                  
                                                    <span class="validation-color" id="err_service_tax"><?php echo form_error('service_tax'); ?></span>
                                                   
                                                </div>
                                                        <?php } ?>
                                 <?php 
    if($access_settings[0]->tds_visible =="yes" || $data[0]->service_tds_id !=0 || $data[0]->service_tds_id != "")
    {

    ?>
                                                  <div class="form-group">
                                                <label for="service_hsn_sac_code">Select service TDS </label>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <a href="" data-toggle="modal" data-target="#tds_modal" class="pull-right"><span class="fa fa-eye"></span></a>
                                                    </div>
                                                    <input type="text" class="form-control" id="service_tds_code1" name="service_tds_code1" value="<?php echo precise_amount($data[0]->service_tds_value); ?>" readonly>
                                                     <input type="hidden" class="form-control" id="service_tds_code" name="service_tds_code" value="<?php echo precise_amount($data[0]->service_tds_value); ?>" >
                                                     <input type="hidden" class="form-control" id="tds_id" name="tds_id" value="<?php echo $data[0]->service_tds_id; ?>" >
                                                </div>
                                                <span class="validation-color" id="err_service_tds_code"></span>
                                            </div>
    <?php } ?>

                                            </div>
                                        </div>
                                        <div class="box-footer">
                                            <button type="submit" id="service_modal_submit" class="btn btn-info">Update</button>
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