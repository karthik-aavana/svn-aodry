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
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>
                    <!-- Dashboard -->
                    Dashboard</a>
            </li>
            <li><a href="<?php echo base_url('leads'); ?>">
                    <!-- Leads -->
                    Leads
                </a></li>
            <li class="active"><!-- Add Leads -->
                Edit Lead
            </li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><!-- Edit Leads -->
                            Edit Leads
                        </h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post" action="<?php echo base_url('leads/edit_lead'); ?>" encType="multipart/form-data">
                                <div class="form-group col-md-4">
                                    <label for="lead_date">
                                        Lead Date <span class="validation-color">*</span>
                                    </label>
                                    <input type="hidden" name="lead_id" id="lead_id" value="<?= $data[0]->lead_id ?>">
                                    <input type="text" class="form-control datepicker" id="lead_date" name="lead_date" value="<?= $data[0]->lead_date ?>">
                                    <span class="validation-color" id="err_lead_date"><?php echo form_error('lead_date'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="asr_no">
                                        Lead No <span class="validation-color">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="asr_no" name="asr_no" value="<?= $data[0]->asr_no ?>">
                                    <span class="validation-color" id="err_asr_no"><?php echo form_error('asr_no'); ?></span>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="customer">Customer <span class="validation-color">*</span></label>
                                        <?php
                                        if (isset($other_modules_present['customer_module_id']) && $other_modules_present['customer_module_id'] != "")
                                        {
                                            ?>
                                            <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#customer_modal" class="open_customer_modal pull-right">+ Add Customer</a>
                                        <?php }
                                        ?>

                                        <select class="form-control select2" autofocus="on" id="customer" name="customer" style="width: 100%;">
                                            <!-- <option value="">Select</option> -->
                                            <?php
                                            foreach ($customer as $row)
                                            {
                                                if ($lead_stages[0]->closed_stages_status == 1)
                                                {
                                                    if ($data[0]->party_id == $row->customer_id)
                                                    {
                                                        echo "<option value='$row->customer_id' selected>$row->customer_code - $row->customer_name</option>";
                                                    }
                                                }
                                                else
                                                {
                                                    if ($data[0]->party_id == $row->customer_id)
                                                    {
                                                        echo "<option value='$row->customer_id' selected>$row->customer_code - $row->customer_name</option>";
                                                    }
                                                    else
                                                    {
                                                        echo "<option value='$row->customer_id'>$row->customer_code - $row->customer_name</option>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="validation-color" id="err_customer"><?php echo form_error('customer'); ?></span>
                                    </div>
                                </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="source">
                                    <!-- Source -->
                                    Source <span class="validation-color">*</span>
                                </label>
                                <select class="form-control select2" id="source" name="source" style="width: 100%;">
                                    <option value="">Select</option>
                                    <?php
                                    foreach ($source as $key => $value)
                                    {
                                        if ($value->lead_source_id == $data[0]->source)
                                        {
                                            echo "<option value='" . $value->lead_source_id . "' selected>" . $value->lead_source_name . "</option>";
                                        }
                                        else
                                        {
                                            echo "<option value='" . $value->lead_source_id . "'>" . $value->lead_source_name . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <span class="validation-color" id="err_source"></span>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="business_type">
                                    Business Type<span class="validation-color">*</span>
                                </label>
                                <!-- <input type="text" class="form-control" id="business_type" name="business_type" value="<?= $data[0]->business_type ?>"> -->
                                <select class="form-control select2" id="business_type" name="business_type" style="width: 100%;">
                                    <option value="">Select</option>
                                    <?php
                                    foreach ($business_type as $key => $value)
                                    {
                                        if ($value->lead_business_id == $data[0]->business_type)
                                        {
                                            echo "<option value='" . $value->lead_business_id . "' selected>" . $value->lead_business_type . "</option>";
                                        }
                                        else
                                        {
                                            echo "<option value='" . $value->lead_business_id . "'>" . $value->lead_business_type . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <span class="validation-color" id="err_business_type"><?php echo form_error('business_type'); ?></span>
                                <input type="hidden" name="item_access" id="item_access" value="<?= $access_settings[0]->item_access ?>">
                            </div>

                            <?php
                            if ($access_settings[0]->item_access == 'product' || $access_settings[0]->item_access == 'both')
                            {
                                ?>
                                <div class="form-group col-md-4">
                                    <label for="products">
                                        Product<span class="validation-color">*</span>
                                    </label>
                                    <select multiple="" class="form-control select2" id="products" name="products[]" style="width: 100%;">
                                        <option value="">Select</option>
                                        <?php
                                        if ($inventory_access == "yes")
                                        {
                                            if (isset($data[0]->product_inventory_variants) && $data[0]->product_inventory_variants)
                                            {
                                                $selected_products = explode(',', $data[0]->product_inventory_variants);
                                            }
                                            else
                                            {
                                                $selected_products = array();
                                            }
                                            foreach ($product_inventory_variants as $key => $value)
                                            {
                                                ?>
                                                <option value="<?= $value->product_inventory_varients_id ?>" <?php if (in_array($value->product_inventory_varients_id, $selected_products)) echo "selected = 'selected'"; ?>><?= $value->varient_name ?></option>
                                                <?php
                                            }
                                        }
                                        else
                                        {
                                            if (isset($data[0]->products) && $data[0]->products)
                                            {
                                                $selected_products = explode(',', $data[0]->products);
                                            }
                                            else
                                            {
                                                $selected_products = array();
                                            }
                                            foreach ($products as $key => $value)
                                            {
                                                ?>
                                                <option value="<?= $value->product_id ?>" <?php if (in_array($value->product_id, $selected_products)) echo "selected = 'selected'"; ?>><?= $value->product_name ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <span class="validation-color" id="err_products"></span>
                                </div>
                                <?php
                            }
                            if ($access_settings[0]->item_access == 'service' || $access_settings[0]->item_access == 'both')
                            {
                                if (isset($data[0]->services) && $data[0]->services)
                                {
                                    $selected_services = explode(',', $data[0]->services);
                                }
                                else
                                {
                                    $selected_services = array();
                                }
                                ?>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="services">
                                        Service<span class="validation-color">*</span>
                                    </label>
                                    <select multiple="" class="form-control select2" id="services" name="services[]" style="width: 100%;">
                                        <option value="">Select</option>
                                        <?php
                                        foreach ($services as $key => $value)
                                        {
                                            ?>
                                            <option value="<?= $value->service_id ?>" <?php if (in_array($value->service_id, $selected_services)) echo "selected = 'selected'"; ?>><?= $value->service_name ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="validation-color" id="err_services"></span>
                                </div>
                            <?php } ?>
                            <div class="form-group col-md-4">
                                <label for="expected_closing">
                                    Expected Closing Date
                                    <span class="validation-color">*</span>
                                </label>
                                <input type="text" class="form-control datepicker" id="expected_closing" name="expected_closing" value="<?= $data[0]->expected_closing ?>">
                                <span class="validation-color" id="err_expected_closing"><?php echo form_error('expected_closing'); ?></span>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="priority">
                                    Priority<span class="validation-color">*</span>
                                </label>
                                <select class="form-control select2" id="priority" name="priority" style="width: 100%;">
                                    <option value="">Select</option>
                                    <option value="Hot" <?php
                                    if ($data[0]->priority == 'Hot')
                                    {
                                        echo "selected";
                                    }
                                    ?>>Hot</option>
                                    <option value="Warm" <?php
                                            if ($data[0]->priority == 'Warm')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Warm</option>
                                    <option value="Cold" <?php
                                    if ($data[0]->priority == 'Cold')
                                    {
                                        echo "selected";
                                    }
                                            ?>>Cold</option>
                                </select>
                                <span class="validation-color" id="err_priority"><?php echo form_error('priority'); ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="evange_list">
                                    Evange List<span class="validation-color">*</span>
                                </label>
                                <br/>
                                <!-- <select class="form-control select2" id="evange_list" name="evange_list" style="width: 100%;">
                                    <option value="">Select</option>
                                    <option value="Yes" <?php
                                if ($data[0]->evange_list == 'Yes')
                                {
                                    echo "selected";
                                }
                                ?>>Yes</option>
                                    <option value="No" <?php
                                           if ($data[0]->evange_list == 'No')
                                           {
                                               echo "selected";
                                           }
                                           ?>>No</option>
                                </select> -->
                                <label class="radio-inline">
                                    <input type="radio" name="evange_list" value="yes" class="minimal" <?php
                                           if ($data[0]->evange_list == "yes")
                                           {
                                               echo 'checked="checked"';
                                           }
                                           ?> /> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="evange_list" value="no" class="minimal" <?php
                                if ($data[0]->evange_list == "no")
                                {
                                    echo 'checked="checked"';
                                }
                                ?> /> No
                                </label>
                                <span class="validation-color" id="err_evange_list"><?php echo form_error('evange_list'); ?></span>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="attachment">Add Attachment
                                </label>
                                <input type="file" class="form-control" id="attachment" name="attachment">
                                <input type="hidden" class="form-control" id="attachment1" name="attachment1" value="<?= $data[0]->attachment ?>">
                                <span class="validation-color" id="err_attachment"><?php echo form_error('attachment'); ?></span>
<?php if (isset($data[0]->attachment) && $data[0]->attachment)
{
    ?>
                                    <a href="<?php echo base_url('assets/branch_files/') . $data[0]->branch_id . '/leads/' . $data[0]->attachment; ?>" title="Document" target="_blank"><i class="fa fa-file text-blue"></i></a>
<?php } ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box-footer">
                                    <button type="submit" id="leads_submit" class="btn btn-info">
                                        <!-- Add -->
                                        Update</button>
                                    <span class="btn btn-default" id="cancel" onclick="cancel('leads')"><!-- Cancel -->
                                        Cancel</span>
                                </div>
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
?>
<script>
    var lead_edit = 'yes';
</script>
<script src="<?php echo base_url('assets/js/leads/') ?>leads.js"></script>
