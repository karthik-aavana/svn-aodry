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
                                <div class="col-md-12">
                                    <div class="form-group col-md-4">
                                        <label for="lead_date">
                                            Date<span class="validation-color">*</span>
                                        </label>

                                        <input type="hidden" name="lead_id" id="lead_id" value="<?= $data[0]->lead_id ?>">

                                        <input type="text" class="form-control datepicker" id="lead_date" name="lead_date" value="<?= $data[0]->lead_date ?>">
                                        <span class="validation-color" id="err_lead_date"><?php echo form_error('lead_date'); ?></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="asr_no">
                                            ASR No<span class="validation-color">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="asr_no" name="asr_no" value="<?= $data[0]->asr_no ?>">
                                        <span class="validation-color" id="err_asr_no"><?php echo form_error('asr_no'); ?></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="group">Group
                                            <span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="group" name="group" style="width: 100%;">
                                            <option value="">Select</option>
                                            <option value="Subscriber" <?php
                                            if ($data[0]->group == 'Subscriber')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Subscriber</option>
                                            <option value="Lead" <?php
                                                    if ($data[0]->group == 'Lead')
                                                    {
                                                        echo "selected";
                                                    }
                                                    ?>>Lead</option>
                                            <option value="Opportunities" <?php
                                            if ($data[0]->group == 'Opportunities')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Opportunities</option>
                                            <option value="Not Interested" <?php
                                            if ($data[0]->group == 'Not Interested')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Not Interested</option>
                                        </select>
                                        <span class="validation-color" id="err_group"><?php echo form_error('group'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group col-md-4">
                                        <label for="stages">Stages<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="stages" name="stages" style="width: 100%;">
                                            <option value="">Select</option>
                                            <?php if ($data[0]->group == 'Subscriber')
                                            {
                                                ?>
                                                <option value="Subscriber" selected>Subscriber</option>

                                            <?php
                                                    }
                                                    else if ($data[0]->group == 'Lead')
                                                    {
                                                        ?>
                                                <option value="Open / Not Attempted" <?php
                                            if ($data[0]->stages == 'Open / Not Attempted')
                                            {
                                                echo "selected";
                                            }
                                                        ?>>Open / Not Attempted</option>
                                                <option value="Attempting to Contact" <?php
                                                if ($data[0]->stages == 'Attempting to Contact')
                                                {
                                                    echo "selected";
                                                }
                                                ?>>Attempting to Contact</option>
                                                <option value="Interested" <?php
                                            if ($data[0]->stages == 'Interested')
                                            {
                                                echo "selected";
                                            }
                                                ?>>Interested</option>
                                                <option value="Nurture" <?php
                                                if ($data[0]->stages == 'Nurture')
                                                {
                                                    echo "selected";
                                                }
                                                ?>>Nurture</option>
                                                <option value="Unresponsive" <?php
                                            if ($data[0]->stages == 'Unresponsive')
                                            {
                                                echo "selected";
                                            }
                                                ?>>Unresponsive</option>
                                                <option value="No Further Action" <?php
                                                if ($data[0]->stages == 'No Further Action')
                                                {
                                                    echo "selected";
                                                }
                                                ?>>No Further Action</option>

                                            <?php
                                            }
                                            else if ($data[0]->group == 'Opportunities')
                                            {
                                                ?>
                                                <option value="Qualified" <?php
                                                if ($data[0]->stages == 'Qualified')
                                                {
                                                    echo "selected";
                                                }
                                                ?>>Qualified</option>
                                                <option value="Presentation and Demo" <?php
                                                if ($data[0]->stages == 'Presentation and Demo')
                                                {
                                                    echo "selected";
                                                }
                                                ?>>Presentation and Demo</option>
                                                <option value="Proposal" <?php
                                                if ($data[0]->stages == 'Proposal')
                                                {
                                                    echo "selected";
                                                }
                                                ?>>Proposal</option>
                                                <option value="Negotiation" <?php
                                            if ($data[0]->stages == 'Negotiation')
                                            {
                                                echo "selected";
                                            }
                                                ?>>Negotiation</option>
                                                <option value="Close / Customer" <?php
                                                    if ($data[0]->stages == 'Close / Customer')
                                                    {
                                                        echo "selected";
                                                    }
                                                    ?>>Close / Customer</option>
                                                <option value="Closed Lost" <?php
                                                    if ($data[0]->stages == 'Closed Lost')
                                                    {
                                                        echo "selected";
                                                    }
                                                ?>>Closed Lost</option>

                                            <?php
                                            }
                                            else if ($data[0]->group == 'Not Interested')
                                            {
                                                ?>
                                                <option value="Not Interested" selected>Not Interested</option>

                                                    <?php } ?>
                                        </select>
                                        <span class="validation-color" id="err_stages"><?php echo form_error('stages'); ?></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="product_service">
                                            Product / Service<span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="product_service" name="product_service" style="width: 100%;">
                                            <option value="">Select</option>
                                            <option value="Product" <?php
                                            if ($data[0]->product_service == 'Product')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Product</option>
                                            <option value="Service" <?php
                                            if ($data[0]->product_service == 'Service')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Service</option>
                                        </select>
                                        <span class="validation-color" id="err_product_service"></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="source">
                                            <!-- Source -->
                                            Source <span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="source" name="source" style="width: 100%;">
                                            <option value="">Select</option>
                                            <option value="Customer Referral" <?php
                                                    if ($data[0]->source == 'Customer Referral')
                                                    {
                                                        echo "selected";
                                                    }
                                            ?>>Customer Referral</option>
                                            <option value="Direct Traffic" <?php
                                            if ($data[0]->source == 'Direct Traffic')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Direct Traffic</option>
                                            <option value="Employee Referral" <?php
                                            if ($data[0]->source == 'Employee Referral')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Employee Referral</option>
                                            <option value="In bound email" <?php
                                            if ($data[0]->source == 'In bound email')
                                            {
                                                echo "selected";
                                            }
                                            ?>>In bound email</option>
                                            <option value="In bound phone call" <?php
                                            if ($data[0]->source == 'In bound phone call')
                                            {
                                                echo "selected";
                                            }
                                            ?>>In bound phone call</option>
                                            <option value="Organic Search" <?php
                                            if ($data[0]->source == 'Organic Search')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Organic Search</option>
                                            <option value="Out bound phone call" <?php
                                            if ($data[0]->source == 'Out bound phone call')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Out bound phone call</option>
                                            <option value="Partner Referral" <?php
                                            if ($data[0]->source == 'Partner Referral')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Partner Referral</option>
                                            <option value="PPC (Paper Click)" <?php
                                            if ($data[0]->source == 'PPC (Paper Click)')
                                            {
                                                echo "selected";
                                            }
                                            ?>>PPC (Paper Click)</option>
                                            <option value="Referral Sites" <?php
                                            if ($data[0]->source == 'Referral Sites')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Referral Sites</option>
                                            <option value="Trade Show" <?php
                                            if ($data[0]->source == 'Trade Show')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Trade Show</option>
                                            <option value="social_media" <?php
                                            if ($data[0]->source == 'Social Media')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Social Media</option>
                                            <option value="Unknown" <?php
                                            if ($data[0]->source == 'Unknown')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Unknown</option>
                                        </select>
                                        <span class="validation-color" id="err_source"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group col-md-4">
                                        <label for="business_type">
                                            Business Type<span class="validation-color">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="business_type" name="business_type" value="<?= $data[0]->business_type ?>">
                                        <span class="validation-color" id="err_business_type"><?php echo form_error('business_type'); ?></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="next_action_date">
                                            Next Action Date<span class="validation-color">*</span>
                                        </label>
                                        <input type="text" class="form-control datepicker" id="next_action_date" name="next_action_date" value="<?= $data[0]->next_action_date ?>">
                                        <span class="validation-color" id="err_next_action_date"><?php echo form_error('next_action_date'); ?></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="comments">
                                            Comments<span class="validation-color">*</span>
                                        </label>
                                        <textarea class="form-control" id="comments" rows="2" name="comments"><?= $data[0]->comments ?></textarea>
                                        <span class="validation-color" id="err_comments"><?php echo form_error('comments'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
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
                                    <div class="form-group col-md-4">
                                        <label for="evange_list">
                                            Evange List<span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="evange_list" name="evange_list" style="width: 100%;">
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
                                        </select>
                                        <span class="validation-color" id="err_evange_list"><?php echo form_error('evange_list'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
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
                                    <div class="form-group col-md-4">
                                        <label for="assign_to">Assign To<span class="validation-color">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="assign_to" name="assign_to" value="<?= $data[0]->assign_to ?>">
                                        <span class="validation-color" id="err_assign_to"><?php echo form_error('assign_to'); ?></span>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="box-footer">
                                        <button type="submit" id="leads_submit" class="btn btn-info">
                                            <!-- Add -->
                                            Update</button>
                                        <span class="btn btn-default" id="cancel" onclick="cancel('leads')"><!-- Cancel -->
                                            Cancel</span>
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
