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
                        <h3 class="box-title">Edit Service</h3>
                        <a class="btn btn-default pull-right back_button" id="cancel" onclick1="cancel('service')">Back</a>
                    </div>
                    <div class="box-body">
                        <form name="form" id="form" method="post" action="<?php echo base_url('service/edit_service'); ?>" encType="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="service_code">Service Code <span class="validation-color">*</span></label>
                                        <input type="hidden" name="service_id" id="service_id" value="<?= $data[0]->service_id ?>">
                                        <input type="text" class="form-control" id="service_code" name="service_code" value="<?= $data[0]->service_code ?>" tabindex="1" <?php
                                        if ($access_settings[0]->invoice_readonly == 'yes') {
                                            echo "readonly";
                                        }
                                        ?>>
                                        <span class="validation-color" id="err_service_code"><?php echo form_error('service_code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="service_name">Service Name<span class="validation-color">*</span></label>
                                       <!--  <input type="hidden" name="service_id" id="service_id" value="0"> -->
                                        <input type="text" class="form-control" id="service_name" name="service_name" maxlength="70" value="<?= $data[0]->service_name; ?>" tabindex="2">
                                        <span class="validation-color" id="err_service_name"><?php echo form_error('service_name'); ?></span>
                                    </div>
                                    </div>                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="service_price">Unit Price <span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="service_price" name="service_price" value="<?= precise_amount($data[0]->service_price) ?>" tabindex="5">
                                        <span class="validation-color" id="err_service_price"><?php echo form_error('service_price'); ?></span>
                                    </div>
                                    </div>
                                
                                 </div>
                                        <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="hidden" class="form-control" id="c_type" name="c_type" value="service">
                                        <label for="service_category">Category <span class="validation-color">*</span></label>
                                        <div class="input-group">
                                                <div class="input-group-addon">
                                                    <?php
                                                    if (in_array($category_module_id, $active_add)) {
                                                    ?>
                                                    <a href="" data-toggle="modal" data-target="#category_modal" data-name="service" class="pull-right new_category">+</a>
                                                    <?php } ?>
                                                </div>
                                            <select class="form-control select2" id="service_category" name="service_category" style="width: 100%;" tabindex="3">
                                                <option value="">Select</option>
                                                <?php
                                                foreach ($service_category as $cat) {
                                                    ?>
                                                    <option value='<?php echo $cat->category_id ?>' <?php
                                                    if ($cat->category_id == $data[0]->service_category_id) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $cat->category_name ?></option>
                                                        <?php } ?>
                                            </select>
                                        </div>
                                        <span class="validation-color" id="err_service_category"><?php echo form_error('service_category'); ?></span>
                                    </div>
                                    </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="service_subcategory">Subcategory</label>
                                        <div class="input-group">
                                                <div class="input-group-addon">
                                                    <?php
                                                    if (in_array($subcategory_module_id, $active_add)) {
                                                    ?>
                                                        <a href="" data-toggle="modal" data-target="#subcategory_modal" data-name="service" class="pull-right open_subcategory_modal new_subcategory">+</a>
                                                    <?php } ?>
                                                </div>
                                            <select class="form-control select2" id="service_subcategory" name="service_subcategory" style="width: 100%;" tabindex="4">
                                                <option value="">Select</option>
                                                <?php
                                                foreach ($service_subcategory as $sub) {
                                                    ?>
                                                    <option value='<?php echo $sub->sub_category_id ?>' <?php
                                                    if ($sub->sub_category_id == $data[0]->service_subcategory_id) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $sub->sub_category_name ?></option>
                                                        <?php } ?>
                                            </select>
                                        </div>
                                        <span class="validation-color" id="err_service_subcategory"><?php echo form_error('service_subcategory'); ?></span>
                                    </div>
                                    </div>
                                <div class="col-md-4">
                                    <?php
                                    if ($access_settings[0]->tax_type == "gst") {
                                        ?>
                                        <div class="form-group">
                                            <label for="service_hsn_sac_code">HSN/SAC Code<span class="validation-color">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#hsn_modal" class="pull-right"><span class="fa fa-eye"></span></a>
                                                </div>
                                                <input type="text" class="form-control" id="service_hsn_sac_code" name="service_hsn_sac_code" value="<?php echo $data[0]->service_hsn_sac_code; ?>" tabindex="7">
                                            </div>
                                            <span class="validation-color" id="err_service_hsn_sac_code"><?php echo form_error('service_hsn_sac_code'); ?></span>

                                        </div>
                                    <?php } ?>
                                    </div>
                                     </div>
                                        <div class="row">
                                    <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gst_tax">Tax(GST)</label>
                                        <div class="input-group">
                                                <div class="input-group-addon">
                                                    <?php
                                                    if (in_array($tax_module_id, $active_add)) {
                                                    ?>
                                                    <a href="" data-toggle="modal" data-target="#tax_gst_modal" data-name="service" class="pull-right new_tax">+</a>
                                                    <?php } ?>
                                                </div>
                                            <select class="form-control select2" id="gst_tax" name="gst_tax" style="width: 100%;" tabindex="6">
                                                <option value="">Select Tax</option>
                                                <?php
                                                foreach ($tax_gst as $row) {
                                                    if ($data[0]->service_gst_id == $row->tax_id) {
                                                        echo "<option value='" . $row->tax_id . "' selected >$row->tax_name @ " . round($row->tax_value, 2) . "%</option>";
                                                    } else {
                                                        echo "<option value='" . $row->tax_id . "' >$row->tax_name @ " . round($row->tax_value, 2) . "%</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <span class="validation-color" id="err_service_tax"><?php echo form_error('gst_tax'); ?></span>
                                    </div>
                                    </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="service_gst_code">GST Tax Percentage</label>

                                        <input type="text" class="form-control" id="service_gst_code" name="service_gst_code" value="<?php echo round($data[0]->service_gst_value, 2); ?>%" readonly>
                                        <span class="validation-color" id="service_gst_code"></span>
                                    </div>
                                    </div>
                                    <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tds_tax">Tax(TDS)</label>
                                        <div class="input-group">
                                                <div class="input-group-addon">
                                                    <?php
                                                    if (in_array($tax_module_id, $active_add)) {
                                                    ?>
                                                    <a href="" data-toggle="modal" data-target="#tax_tds_modal" data-name="service" class="pull-right new_tax">+</a>
                                                    <?php } ?>
                                                </div>
                                            <select class="form-control select2" id="tds_tax" name="tds_tax" style="width: 100%;" tabindex="6">
                                                <option value="">Select Tax</option>
                                                <?php
                                                foreach ($tax_tds as $row) {
                                                    if ($data[0]->service_tds_id == $row->tax_id) {
                                                        echo "<option value='" . $row->tax_id . "' selected >$row->tax_name(Sec " . $row->section_name . ") @ " . round($row->tax_value, 2) . "%</option>";
                                                    } else {
                                                        echo "<option value='" . $row->tax_id . "' >$row->tax_name@(Sec " . $row->section_name . ") @ " . round($row->tax_value, 2) . "%</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <span class="validation-color" id="err_service_tax"><?php echo form_error('tds_tax'); ?></span>
                                    </div>

                                </div>
                                 </div>
                                        <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="service_tds_code">TDS Tax Percentage </label>
                                        <input type="text" class="form-control" id="service_tds_code" name="service_tds_code" value="<?php echo round($data[0]->service_tds_value, 2); ?>%" readonly>
                                        <span class="validation-color" id="service_tds_code"></span>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                            <label for="service_unit_uom">Unit of Measurement<span class="validation-color">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <?php
                                                    if (in_array($uqc_module_id, $active_add)) {
                                                    ?>
                                                        <a href="#" data-toggle="modal" data-name="service" data-target="#uom_modal" class="pull-right new_tax">+</a>
                                                    <?php } ?>
                                                </div>
                                                <select class="form-control select2" id="service_unit" name="service_unit"  tabindex="4">
                                                    <option value="">Select UOM</option>
                                                    <?php
                                                    foreach ($uqc as $value) {
                                                        if ($value->id == $data[0]->service_unit) {
                                                            echo "<option value='$value->id' selected>$value->uom - $value->description</option>";
                                                        } else {
                                                            echo "<option value='$value->id'>$value->uom - $value->description</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <span class="validation-color" id="err_product_unit"></span>
                                        </div>
                                    </div>
                                </div>                           
                            <div class="box-footer">
                                <button type="submit" id="service_modal_submit" class="btn btn-info">Update</button>
                                <span class="btn btn-default" id="cancel" onclick="cancel('service')">Cancel</span>
                            </div>                           
                        </form>
                    </div>
                </div>
            </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
if (in_array($tax_module_id, $active_add)) {
    $this->load->view('tax/tax_modal_tds');
    $this->load->view('tax/tax_modal_gst');
    $this->load->view('uqc/uom_modal');
}
$this->load->view('service/hsn_modal');
if (in_array($category_module_id, $active_add)) {
    $this->load->view('category/category_modal');
}
if (in_array($subcategory_module_id, $active_add)) {
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