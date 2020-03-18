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

                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>

                        <!-- Dashboard -->

                        Dashboard</a>

                </li>

                <li><a href="<?php echo base_url('module_settings'); ?>">

                        <!-- customer -->

                        Module Settings

                    </a></li>

                <li class="active"><!-- Add customer -->

                    Edit Module Settings

                </li>

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

                        <h3 class="box-title">

                            Edit Module Settings

                        </h3>

                    </div>

                    <!-- /.box-header -->

                    <div class="box-body">

                        <div class="row">

                            <div class="well">

                                <form role="form" id="form" method="post" action="<?php echo base_url('module_settings/edit_module_settings'); ?>">

                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <input type="hidden" name="settings_id" value="<?php echo $data[0]->settings_id; ?>">

                                            <label for="module_name">Module Name<span class="validation-color">*</span></label>
                                            <select class="form-control" id="module_name" name="module_name" readonly>

                                                <option value="<?php echo $data[0]->module_id; ?>"><?php echo $data[0]->module_name; ?></option>

                                            </select>

                                            <span class="validation-color" id="err_module_name"><?php echo form_error('module_name'); ?></span>

                                        </div>

                                        <div class="form-group">

                                            <label for="invoice_first_prefix">Invoice First Prefix<span class="validation-color">*</span></label>

                                            <input type="text" class="form-control" id="invoice_first_prefix" name="invoice_first_prefix" value="<?php echo $data[0]->settings_invoice_first_prefix; ?>">

                                            <span class="validation-color" id="err_invoice_first_prefix"><?php echo form_error('invoice_first_prefix'); ?></span>

                                        </div>

                                        <div class="form-group">

                                            <label for="invoice_last_prefix">Invoice Last Prefix<span class="validation-color">*</span></label>

                                            <select class="form-control select2" id="invoice_last_prefix" name="invoice_last_prefix" style="width: 100%;">

                                                <option value="">Select</option>

                                                <option value="number" <?php

                                                if ($data[0]->settings_invoice_last_prefix == 'number')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>Number</option>

                                                <option value="month_with_number" <?php

                                                if ($data[0]->settings_invoice_last_prefix == 'month_with_number')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>Month With Number</option>

                                            </select>

                                            <span class="validation-color" id="err_invoice_last_prefix"><?php echo form_error('invoice_last_prefix'); ?></span>

                                        </div>

                                        <div class="form-group">

                                            <label for="invoice_seperation">Invoice Separation<span class="validation-color">*</span>

                                            </label>

                                            <select class="form-control select2" id="invoice_seperation" name="invoice_seperation" style="width: 100%;">

                                                <option value="">Select</option>

                                                <option value="-" <?php

                                                if ($data[0]->invoice_seperation == '-')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>-</option>

                                                <option value="/" <?php

                                                if ($data[0]->invoice_seperation == '/')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>/</option>

                                                <option value="no" <?php

                                                if ($data[0]->invoice_seperation == 'no')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>No Separation</option>

                                            </select>

                                            <span class="validation-color" id="err_invoice_seperation"><?php echo form_error('invoice_seperation'); ?></span>

                                        </div>

                                        <div class="form-group">

                                            <label for="invoice_type">Invoice Type<span class="validation-color">*</span></label>

                                            <select class="form-control select2" id="invoice_type" name="invoice_type" style="width: 100%;">

                                                <option value="">Select</option>

                                                <option value="regular" <?php

                                                if ($data[0]->invoice_type == 'regular')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>Regular</option>

                                                <option value="monthly" <?php

                                                if ($data[0]->invoice_type == 'monthly')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>Monthly</option>

                                                <option value="yearly" <?php

                                                if ($data[0]->invoice_type == 'yearly')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>Yearly</option>

                                            </select>

                                            <span class="validation-color" id="err_invoice_type"><?php echo form_error('invoice_type'); ?></span>

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label for="invoice_creation">Invoice Creation<span class="validation-color">*</span></label>

                                            <select class="form-control select2" id="invoice_creation" name="invoice_creation" style="width: 100%;">

                                                <option value="">Select</option>

                                                <option value="automatic" <?php

                                                if ($data[0]->invoice_creation == 'automatic')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>Automatic</option>

                                                <option value="manual" <?php

                                                if ($data[0]->invoice_creation == 'manual')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>Manual</option>

                                            </select>

                                            <span class="validation-color" id="err_invoice_creation"><?php echo form_error('invoice_creation'); ?></span>

                                        </div>

                                        <div class="form-group">

                                            <label for="invoice_readonly">Invoice Readonly<span class="validation-color">*</span></label>

                                            <select class="form-control select2" id="invoice_readonly" name="invoice_readonly" style="width: 100%;">

                                                <option value="">Select</option>

                                                <?php

                                                if ($data[0]->invoice_creation == 'automatic')

                                                {

                                                    ?>

                                                    <option value="yes" <?php

                                                    if ($data[0]->invoice_readonly == 'yes')

                                                    {

                                                        echo "selected";

                                                    }

                                                    ?>>Yes</option>

                                                            <?php

                                                        }

                                                        else

                                                        {

                                                            ?>

                                                    <option value="yes" <?php

                                                    if ($data[0]->invoice_readonly == 'yes')

                                                    {

                                                        echo "selected";

                                                    }

                                                    ?>>Yes</option>

                                                    <option value="no" <?php

                                                    if ($data[0]->invoice_readonly == 'no')

                                                    {

                                                        echo "selected";

                                                    }

                                                    ?>>No</option>

                                                        <?php } ?>

                                            </select>

                                            <span class="validation-color" id="err_invoice_readonly"><?php echo form_error('invoice_readonly'); ?></span>

                                        </div>

                                        <div class="form-group">

                                            <label for="item_access">Item Access<span class="validation-color">*</span></label>

                                            <select class="form-control select2" id="item_access" name="item_access" style="width: 100%;">

                                                <option value="">Select</option>

                                                <option value="product" <?php

                                                if ($data[0]->item_access == 'product')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>Goods</option>

                                                <option value="service" <?php

                                                if ($data[0]->item_access == 'service')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>Service</option>

                                                <option value="both" <?php

                                                if ($data[0]->item_access == 'both')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>Both</option>

                                            </select>

                                            <span class="validation-color" id="err_item_access"><?php echo form_error('item_access'); ?></span>

                                        </div>

                                        <div class="form-group">

                                            <label for="note_split">Note Split<span class="validation-color">*</span></label>

                                            <select class="form-control select2" id="note_split" name="note_split" style="width: 100%;">

                                                <option value="">Select</option>

                                                <option value="yes" <?php

                                                if ($data[0]->note_split == 'yes')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>Yes</option>

                                                <option value="no" <?php

                                                if ($data[0]->note_split == 'no')

                                                {

                                                    echo "selected";

                                                }

                                                ?>>No</option>

                                            </select>

                                            <span class="validation-color" id="err_note_split"><?php echo form_error('note_split'); ?></span>

                                        </div>

                                        <div class="form-group">

                                            <?php

                                            $checked_gst = '';

                                            if (isset($data[0]->gst_visible)) {

                                                if($data[0]->gst_visible == 'no'){

                                                    $checked_gst = '';

                                                }else{

                                                    $checked_gst = 'checked=checked';

                                                }

                                            }

                                            $checked_tds = '';

                                            if (isset($data[0]->tds_visible)) {

                                                if($data[0]->tds_visible == 'no'){

                                                    $checked_tds = '';

                                                }else{

                                                    $checked_tds = 'checked=checked';

                                                }

                                            }

                                            

                                            $checked_tcs = '';

                                            if (isset($data[0]->tcs_visible)) {

                                                if($data[0]->tcs_visible == 'no'){

                                                    $checked_tcs = '';

                                                }else{

                                                    $checked_tcs = 'checked=checked';

                                                }

                                            }

                                            

                                            ?>

                                            <label for="note_split">Display Tax</label><br>

                                            <label>

                                                <input type="checkbox" id="gst" name="gst" value="yes" <?php echo $checked_gst;?> >

                                                GST </label>&nbsp;&nbsp;&nbsp; <label>

                                                <input type="checkbox" id="tcs" name="tcs" value="yes" <?php echo $checked_tcs;?>>

                                                TCS </label>&nbsp;&nbsp;&nbsp;  <label>

                                                <input type="checkbox" id="tds" name="tds" value="yes" <?php echo $checked_tds;?>>

                                                TDS </label>

                                            <span class="validation-color" id="err_note_split"><?php echo form_error('note_split'); ?></span>

                                        </div>

                                        <div class="form-group" id="reference_first_prefix_div" <?php

                                        if ($data[0]->module_id != "10")

                                        {

                                            ?> style="display: none;" <?php } ?>>

                                            <label for="reference_first_prefix">Reference First Prefix<span class="validation-color">*</span></label>

                                            <input type="text" class="form-control" id="reference_first_prefix" name="reference_first_prefix" value="<?= $data[0]->settings_reference_first_prefix; ?>">

                                            <span class="validation-color" id="err_reference_first_prefix"><?php echo form_error('reference_first_prefix'); ?></span>

                                        </div>

                                    </div>

                                    <div class="col-sm-12">

                                        <div class="box-footer">

                                            <button type="submit" id="module_settings_submit" class="btn btn-info"><!-- Add -->Update</button>

                                            <span class="btn btn-default" id="cancel" onclick="cancel('module_settings')"><!-- Cancel -->

                                                Cancel</span>

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

            <script src="<?php echo base_url('assets/js/module_settings/') ?>module_settings.js"></script>



