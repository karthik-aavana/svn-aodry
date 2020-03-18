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

                Add Lead

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

                        <h3 class="box-title"><!-- Add Leads -->

                            Add Leads

                        </h3>

                    </div>

                    <!-- /.box-header -->

                    <div class="box-body">

                        <form role="form" id="form" method="post" action="<?php echo base_url('leads/add_lead'); ?>" encType="multipart/form-data">

                            <div class="row">

                                <div class="form-group col-md-4">

                                    <label for="lead_date">

                                        Lead Date<span class="validation-color">*</span>

                                    </label>

                                    <input type="text" class="form-control" id="lead_date" name="lead_date" value="<?php echo date('Y-m-d'); ?>">

                                    <span class="validation-color" id="err_lead_date"><?php echo form_error('lead_date'); ?></span>

                                </div>

                                <div class="form-group col-md-4">

                                    <label for="asr_no">

                                        Lead No<span class="validation-color">*</span>

                                    </label>

                                    <input type="text"  class="form-control" id="asr_no" name="asr_no" value="<?= $invoice_number ?>" <?php

                                    if ($access_settings[0]->invoice_readonly == 'yes')

                                    {

                                        echo "readonly";

                                    }

                                    ?>>

                                    <span class="validation-color" id="err_asr_no"><?php echo form_error('asr_no'); ?></span>

                                </div>

                                <div class="col-sm-4">

                                    <div class="form-group">

                                        <label for="customer">Customer <span class="validation-color">*</span></label>

                                        

                                        <div class="input-group">

                                            

                                            <div class="input-group-addon">

                                                <?php

                                                if (in_array($customer_module_id, $active_add))

                                                {

                                                ?>

                                                <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-reference_type="leads" data-target="#customer_modal" class="open_customer_modal pull-right">+</a>

                                                <?php }

                                                ?>

                                            </div>                                            



                                            <select class="form-control select2" autofocus="on" id="customer" name="customer" style="width: 100%;">

                                                <option value="">Select</option>

                                                <?php

                                                foreach ($customer as $row)

                                                {
                                                    if($row->customer_mobile != ''){
                                                        echo "<option value='$row->customer_id'>$row->customer_code - $row->customer_name($row->customer_mobile)</option>";

                                                    }else{
                                                        echo "<option value='$row->customer_id'>$row->customer_code - $row->customer_name</option>";

                                                    }
                                                }

                                                ?>

                                            </select>

                                        </div>

                                        <span class="validation-color" id="err_customer"><?php echo form_error('customer'); ?></span>

                                    </div>

                                </div>

                            </div>

                            <div class="row">

                                <div class="form-group col-md-4">

                                    <label for="group">Group<span class="validation-color">*</span>

                                    </label>

                                    <div class="input-group">

                                        <div class="input-group-addon">

                                            <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#group_modal" class="group_modal pull-right">+</a>

                                        </div>

                                        <select class="form-control select2" id="group" name="group" style="width: 100%;">

                                            <option value="">Select</option>

                                            <?php foreach ($group as $key => $value)

                                            {

                                                ?>

                                                <option value="<?= $value->lead_group_id ?>"><?= $value->lead_group_name ?></option>

<?php } ?>

                                        </select>

                                    </div>

                                    <span class="validation-color" id="err_group"><?php echo form_error('group'); ?></span>

                                </div>

                                <div class="form-group col-md-4">

                                    <label for="stages">Stages<span class="validation-color">*</span></label>

                                    <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#stages_modal" class="stages_modal pull-right" style="display: none;">+ Add Stages</a>

                                    <select class="form-control select2" id="stages" name="stages" style="width: 100%;">

                                        <option value="">Select</option>

                                    </select>

                                    <span class="validation-color" id="err_stages"><?php echo form_error('stages'); ?></span>

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

                                                foreach ($product_inventory_variants as $key => $value)

                                                {

                                                    echo '<option value="' . $value->product_inventory_varients_id . '">' . $value->varient_name . '</option>';

                                                }

                                            }

                                            else

                                            {

                                                foreach ($products as $key => $value)

                                                {

                                                    echo '<option value="' . $value->product_id . '">' . $value->product_name . '</option>';

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

                                                echo '<option value="' . $value->service_id . '">' . $value->service_name . '</option>';

                                            }

                                            ?>

                                        </select>

                                        <span class="validation-color" id="err_services"></span>

                                    </div>

<?php } ?>

                                <div class="form-group col-md-4">

                                    <label for="source">

                                        Source<span class="validation-color">*</span>

                                    </label>

                                    <div class="input-group">

                                        <div class="input-group-addon">

                                            <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#source_modal" class="source_modal pull-right">+</a>

                                        </div>

                                        <select class="form-control select2" id="source" name="source" style="width: 100%;">

                                            <option value="">Select</option>

                                            <?php foreach ($source as $key => $value)

                                            {

                                                ?>

                                                <option value="<?= $value->lead_source_id ?>"><?= $value->lead_source_name ?></option>

<?php } ?>

                                            <!-- <option value="Customer Referral">Customer Referral</option>

                                            <option value="Direct Traffic">Direct Traffic</option>

                                            <option value="Employee Referral">Employee Referral</option>

                                            <option value="In bound email">In bound email</option>

                                            <option value="In bound phone call">In bound phone call</option>

                                            <option value="Organic Search">Organic Search</option>

                                            <option value="Out bound phone call">Out bound phone call</option>

                                            <option value="Partner Referral">Partner Referral</option>

                                            <option value="PPC (Paper Click)">PPC (Paper Click)</option>

                                            <option value="Referral Sites">Referral Sites</option>

                                            <option value="Trade Show">Trade Show</option>

                                            <option value="Social Media">Social Media</option>

                                            <option value="Unknown">Unknown</option> -->

                                        </select>

                                    </div>

                                    <span class="validation-color" id="err_source"></span>

                                </div>

                                <div class="form-group col-md-4">

                                    <label for="business_type">

                                        Business Type<span class="validation-color">*</span>

                                    </label>

                                    <div class="input-group">

                                        <div class="input-group-addon">

                                            <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#business_type_modal" class="business_type_modal pull-right">+</a>

                                        </div>

                                        <select class="form-control select2" id="business_type" name="business_type" style="width: 100%;">

                                            <option value="">Select</option>

<?php foreach ($business_type as $key => $value)

{

    ?>

                                                <option value="<?= $value->lead_business_id ?>"><?= $value->lead_business_type ?></option>

<?php } ?>

                                        </select>

                                    </div>

                                    <!-- <input type="text" class="form-control" id="business_type" name="business_type" value="<?php echo set_value('business_type'); ?>"> -->

                                    <span class="validation-color" id="err_business_type"><?php echo form_error('business_type'); ?></span>

                                </div>

                                <div class="form-group col-md-4">

                                    <label for="next_action_date">

                                        Next Action Date<span class="validation-color">*</span>

                                    </label>

                                    <input type="text" class="form-control datepicker" id="next_action_date" name="next_action_date" value="<?php echo date('Y-m-d'); ?>">

                                    <span class="validation-color" id="err_next_action_date"><?php echo form_error('next_action_date'); ?></span>

                                </div>



                                <div class="form-group col-md-4">

                                    <label for="expected_closing">

                                        Expected Closing Date<span class="validation-color">*</span>

                                    </label>

                                    <input type="text" class="form-control datepicker" id="expected_closing" name="expected_closing" value="<?php echo date('Y-m-d'); ?>">

                                    <span class="validation-color" id="err_expected_closing"><?php echo form_error('expected_closing'); ?></span>

                                </div>

                                <div class="form-group col-md-4">

                                    <label for="priority">

                                        Priority<span class="validation-color">*</span>

                                    </label>

                                    <select class="form-control select2" id="priority" name="priority" style="width: 100%;">

                                        <option value="">Select</option>

                                        <option value="Hot">Hot</option>

                                        <option value="Warm">Warm</option>

                                        <option value="Cold">Cold</option>

                                    </select>

                                    <span class="validation-color" id="err_priority"><?php echo form_error('priority'); ?></span>

                                </div>

                                <div class="form-group col-md-4">

                                    <label for="evange_list">

                                        Evange List<span class="validation-color">*</span>

                                    </label>

                                    <br/>

                                    <!-- <select class="form-control select2" id="evange_list" name="evange_list" style="width: 100%;">

                                        <option value="">Select</option>

                                        <option value="Yes">Yes</option>

                                        <option value="No">No</option>

                                    </select> -->

                                    <label class="radio-inline">

                                        <input type="radio" name="evange_list" value="yes" class="minimal" /> Yes

                                    </label>

                                    <label class="radio-inline">

                                        <input type="radio" name="evange_list" value="no" class="minimal" checked="checked"/> No

                                    </label>

                                    <span class="validation-color" id="err_evange_list"><?php echo form_error('evange_list'); ?></span>

                                </div>

                                <div class="form-group col-md-4">

                                    <label for="assign_to">Assign To<span class="validation-color">*</span>

                                    </label>

                                    <!-- <input type="text" class="form-control" id="assign_to" name="assign_to" value="<?php echo set_value('assign_to'); ?>"> -->

                                    <select class="form-control select2" id="assign_to" name="assign_to" style="width: 100%;">

                                        <option value="">Select</option>

<?php foreach ($users as $key => $value)

{

    ?>

                                            <option value="<?= $value->id ?>"><?php echo $value->first_name . ' ' . $value->last_name; ?></option>

<?php } ?>

                                    </select>

                                    <span class="validation-color" id="err_assign_to"><?php echo form_error('assign_to'); ?></span>

                                </div>

                                <div class="form-group col-md-4">

                                    <label for="attachment">Add Attachment</label>

                                    <input type="file" class="form-control" id="attachment" name="attachment">

                                    <span class="validation-color" id="err_attachment"><?php echo form_error('attachment'); ?></span>

                                </div>

                            </div>

                            <div class="row">

                                <div class="form-group col-md-12">

                                    <label for="comments">

                                        Comments<span class="validation-color">*</span>

                                    </label>

                                    <textarea class="form-control" id="comments" rows="2" name="comments"><?php echo set_value('comments'); ?></textarea>

                                    <span class="validation-color" id="err_comments"><?php echo form_error('comments'); ?></span>

                                </div>

                            </div>

                            <input type="hidden" class="form-control" id="customer_module_id" name="customer_module_id" value="<?php

                                   if (in_array($customer_module_id, $active_add))

                                   {

                                       echo $customer_module_id;

                                   }

                            ?>" readonly>

                            <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>

                            <div class="row">

                                <div class="col-sm-12">

                                    <div class="box-footer">

                                        <button type="submit" id="leads_submit" class="btn btn-info">Add</button>

                                        <span class="btn btn-default" id="cancel" onclick="cancel('leads')">Cancel</span>

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

<script type="text/javascript">

    var group_add = "yes";

    var stages_add = "yes";

    var source_add = "yes";

    var business_type_add = "yes";

</script>

<?php

$this->load->view('layout/footer');

$this->load->view('customer/customer_modal');

$this->load->view('leads/group_modal');

$this->load->view('leads/stages_modal');

$this->load->view('leads/source_modal');

$this->load->view('leads/business_type_modal');

?>

<script src="<?php echo base_url('assets/js/leads/') ?>leads.js"></script>

<script type="text/javascript">

        // $("#expected_closing").datepicker({ minDate: 0 });

        // $("#next_action_date").datepicker({ minDate: new Date() });

        // $("#lead_date").datepicker({ minDate: dateToday });

        $("#lead_date, #next_action_date, #expected_closing").datepicker({

            autoclose: true,

            format: "yyyy-mm-dd",

            todayHighlight: true,

            orientation: "auto",

            todayBtn: false,

            startDate: new Date(),

        });

</script>