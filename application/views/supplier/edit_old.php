<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>
                        <!-- Dashboard -->
                        Dashboard</a>
                </li>
                <li><a href="<?php echo base_url('supplier'); ?>">
                        <!-- Supplier -->
                        Supplier</a>
                </li>
                <li class="active">
                    <!-- Edit Supplier -->
                    Edit Supplier
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
                            <!-- Edit Supplier -->
                            Edit Supplier
                        </h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post" action="<?php echo base_url('supplier/edit_supplier'); ?>">
                                <div class="col-md-12">
                                    <div class="form-group col-md-4">
                                        <label for="supplier_code">
                                            Supplier Code
                                        </label>
                                        <input type="text" class="form-control" id="supplier_code" name="supplier_code" value="<?php echo $data[0]->supplier_code; ?>" <?php
                                        if ($access_settings[0]->invoice_readonly == 'yes')
                                        {
                                            echo "readonly";
                                        }
                                        ?>>
                                        <span class="validation-color" id="err_supplier_code"><?php echo form_error('supplier_code'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="supplier_type">
                                            Supplier Type<span class="validation-color"></span>
                                        </label>
                                        <select class="form-control select2" id="supplier_type" name="supplier_type" style="width: 100%;">
                                            <!-- <option value="">Select</option> -->
                                            <option value="individual" <?php
                                                    if ($data[0]->supplier_type == 'individual')
                                                    {
                                                        echo "selected";
                                                    }
                                                    ?>>Individual</option>
                                            <option value="company" <?php
                                            if ($data[0]->supplier_type == 'company')
                                            {
                                                echo "selected";
                                            }
                                                    ?>>Company</option>
                                        </select>
                                        <span class="validation-color" id="err_supplier_type"><?php echo form_error('supplier_type'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <input type="hidden" id="country_edit" name="country_edit" value="1">
                                        <input type="hidden" name="supplier_id" id="supplier_id" value="<?php echo $data[0]->supplier_id; ?>">
                                        <input type="hidden" name="contact_person_id" id="contact_person_id" value="<?php echo $contact_person[0]->contact_person_id; ?>">
                                        <input type="hidden" name="added_user_id" id="added_user_id" value="<?php echo $data[0]->added_user_id; ?>">
                                        <input type="hidden" name="added_date" id="added_date" value="<?php echo $data[0]->added_date; ?>">
                                        <input type="hidden" name="ledger_id" id="supplier_ledger_id" value="<?php echo $data[0]->ledger_id; ?>">

                                        <label for="supplier_name">
                                            Supplier Name<span class="validation-color">*</span>
                                        </label>

                                        <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="<?php echo $data[0]->supplier_name; ?>">
                                        <span class="validation-color" id="err_supplier_name"><?php echo form_error('supplier_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group col-md-4">
                                        <label for="gstregtype">Gst Registration Type
                                            <span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="gstregtype" name="gstregtype" style="width: 100%;">
                                            <option value="">
                                                Select
                                            </option>
                                            <option value="Registered" <?php
                                            if ($data[0]->supplier_gst_registration_type == "Registered")
                                            {
                                                echo "selected";
                                            }
                                            ?> >Registered</option>
                                            <option value="Unregistered" <?php
                                            if ($data[0]->supplier_gst_registration_type == "Unregistered")
                                            {
                                                echo "selected";
                                            }
                                            ?> >Unregistered</option>
                                        </select>
                                        <span class="validation-color" id="err_gstin_type"><?php echo form_error('gstin_type'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4" id="gstn_selected" <?php
                                    if ($data[0]->supplier_gst_registration_type != "Registered")
                                    {
                                        echo "hidden='true'";
                                    }
                                            ?>>
                                        <label for="gstin">GSTIN<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="gstin" name="gstin" value="<?php echo $data[0]->supplier_gstin_number; ?>">
                                        <span class="validation-color" id="err_gstin"><?php echo form_error('gstin'); ?></span>
                                    </div>

                                </div>
                                <div class="col-md-12">
                                    <div class="form-group col-md-4">
                                        <label for="country">
                                            <!-- Country -->
                                            Country<span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="country" name="country" style="width: 100%;">
                                            <option value="">Select</option>
                                                    <?php
                                                    foreach ($country as $key)
                                                    {
                                                        ?>


                                                <option value='<?php echo $key->country_id ?>' <?php
                                            if ($key->country_id == $data[0]->supplier_country_id)
                                            {
                                                echo "selected";
                                            }
                                            ?> ><?php echo $key->country_name; ?>
                                                </option>


    <?php
}
?>
                                        </select>
                                        <span class="validation-color" id="err_country"></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="state">
                                            <!-- State -->
                                            State <span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="state" name="state" style="width: 100%;">
                                            <option value=""><!-- Select -->
                                                Select</option>
                                            <?php
                                            foreach ($state as $key)
                                            {
                                                ?>
                                                <option value='<?php echo $key->state_id ?>'  <?php
                                                if ($key->state_id == $data[0]->supplier_state_id)
                                                {
                                                    echo "selected";
                                                }
                                                ?> ><?php echo $key->state_name; ?>
                                                </option>
    <?php
}
?>
                                        </select>
                                        <span class="validation-color" id="err_state"></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="city">
                                            <!-- City -->
                                            City<span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="city" name="city" style="width: 100%;">
                                            <option value=""><!-- Select -->
                                                Select
                                            </option>

                                            <?php
                                            foreach ($city as $key)
                                            {
                                                ?>
                                                <option value='<?php echo $key->city_id ?>' <?php
                                                if ($key->city_id == $data[0]->supplier_city_id)
                                                {
                                                    echo "selected";
                                                }
                                                ?> > <?php echo $key->city_name; ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="validation-color" id="err_city"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group col-md-4">
                                        <label for="address1">
                                            Address<span class="validation-color"></span>
                                        </label>
                                        <textarea class="form-control" id="address" rows="4" name="address"><?php
                                            echo str_replace(array(
                                                    "\r\n",
                                                    "\\r\\n",
                                                    "\n",
                                                    "\\n" ), "&#10;", $data[0]->supplier_address);
                                            ?></textarea>
                                        <span class="validation-color" id="err_address"><?php echo form_error('address'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="postal_code"><!-- Postal Code -->
                                            Postal Code</label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo $data[0]->supplier_postal_code; ?>">
                                        <span class="validation-color" id="err_postal_code"><?php echo form_error('postal_code'); ?></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="website">Website<span class="validation-color"></span>
                                        </label>
                                        <input type="text" class="form-control" id="website" name="website" value="<?php echo $data[0]->supplier_website; ?>">
                                        <span class="validation-color" id="err_website"><?php echo form_error('website'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group col-md-4">
                                        <label for="email"><!-- email -->
                                            Email
                                        </label>
                                        <input type="text" class="form-control" id="email" name="email" value="<?php echo $data[0]->supplier_email; ?>">
                                        <span class="validation-color" id="err_email"><?php echo form_error('email'); ?></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="mobile"><!-- Mobile -->
                                            Mobile
                                        </label>
                                        <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo $data[0]->supplier_mobile; ?>">
                                        <span class="validation-color" id="err_mobile"><?php echo form_error('mobile'); ?></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="telephone"><!-- telephone -->
                                            Telephone<!-- <span class="validation-color">*</span> -->
                                        </label>

                                        <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo $data[0]->supplier_telephone; ?>">
                                        <span class="validation-color" id="err_telephone"><?php echo form_error('telephone'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group col-md-4">
                                        <label for="pan_no">PAN No<span class="validation-color"></span></label>
                                        <input type="text" class="form-control" id="panno" name="panno" value="<?php echo $data[0]->supplier_pan_number; ?>">
                                        <span class="validation-color" id="err_panno"><?php echo form_error('panno'); ?></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="tanno">TAN<span class="validation-color"></span>
                                        </label>
                                        <input type="text" class="form-control" id="tanno" name="tanno" value="<?php echo $data[0]->supplier_tan_number; ?>">
                                        <span class="validation-color" id="err_tan"><?php echo form_error('tanno'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12" id="contact_person" style="display: none;">
                                    <div class="form-group col-md-4">
                                        <input type="checkbox" id="add_contact_person" name="add_contact_person" value="yes" checked="checked"> I want to Add contact person.
                                    </div>
                                </div>

                                <div id="contact_person_div">
                                    <div class="col-md-12">
                                        <div class="form-group col-md-4">
                                            <label for="contact_person_code">
                                                Contact Person Code<span class="validation-color">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="contact_person_code" name="contact_person_code" value="<?php echo $contact_person[0]->contact_person_code; ?>">
                                            <span class="validation-color" id="err_contact_person_code"><?php echo form_error('contact_person_code'); ?></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="contact_person_name">
                                                Contact Person Name<span class="validation-color">*</span>
                                            </label>
                                            <input type="text"  class="form-control" id="contact_person_name" name="contact_person_name" value="<?php echo $contact_person[0]->contact_person_name; ?>">
                                            <span class="validation-color" id="err_contact_person_name"><?php echo form_error('contact_person_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group col-md-4">
                                            <label for="contact_person_country">
                                                <!-- Country -->
                                                Country<span class="validation-color">*</span>
                                            </label>
                                            <select class="form-control select2" id="contact_person_country" name="contact_person_country" style="width: 100%;">
                                                <option value="">Select</option>
<?php
foreach ($country as $key)
{
    ?>
                                                    <option value='<?php echo $key->country_id ?>' <?php
    if ($key->country_id == $contact_person[0]->contact_person_country_id)
    {
        echo "selected";
    }
    ?>>
                                                    <?php echo $key->country_name; ?>
                                                    </option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                            <span class="validation-color" id="err_contact_person_country"></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="contact_person_state">
                                                <!-- State -->
                                                State <span class="validation-color">*</span>
                                            </label>
                                            <select class="form-control select2" id="contact_person_state" name="contact_person_state" style="width: 100%;">
                                                <option value="">Select</option>
<?php
foreach ($contact_person_state as $key)
{
    ?>
                                                    <option value='<?php echo $key->state_id ?>' <?php
    if ($key->state_id == $contact_person[0]->contact_person_state_id)
    {
        echo "selected";
    }
    ?>>
                                                    <?php echo $key->state_name; ?>
                                                    </option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                            <span class="validation-color" id="err_contact_person_state"></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="contact_person_city">
                                                <!-- City -->
                                                City<span class="validation-color">*</span>
                                            </label>
                                            <select class="form-control select2" id="contact_person_city" name="contact_person_city" style="width: 100%;">
                                                <option value="">Select</option>
<?php
foreach ($contact_person_city as $key)
{
    ?>
                                                    <option value='<?php echo $key->city_id ?>' <?php
    if ($key->city_id == $contact_person[0]->contact_person_city_id)
    {
        echo "selected";
    }
    ?>>
                                                    <?php echo $key->city_name; ?>
                                                    </option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                            <span class="validation-color" id="err_contact_person_city"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group col-md-4">
                                            <label for="contact_person_address1">
                                                Address<span class="validation-color"></span>
                                            </label>
                                            <textarea class="form-control" id="contact_person_address" rows="2" name="contact_person_address"><?php echo set_value('contact_person_address'); ?><?php
                                                echo str_replace(array(
                                                        "\r\n",
                                                        "\\r\\n",
                                                        "\n",
                                                        "\\n" ), "&#10;", $contact_person[0]->contact_person_address);
                                                ?></textarea>
                                            <span class="validation-color" id="err_contact_person_address"><?php echo form_error('contact_person_address'); ?></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="contact_person_postal_code"><!-- Postal Code -->
                                                Postal Code</label>
                                            <input type="text" class="form-control" id="contact_person_postal_code" name="contact_person_postal_code" value="<?php echo $contact_person[0]->contact_person_postal_code; ?>">
                                            <span class="validation-color" id="err_contact_person_postal_code"><?php echo form_error('contact_person_postal_code'); ?></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="website">Website<span class="validation-color"></span>
                                            </label>
                                            <input type="text" class="form-control" id="contact_person_website" name="contact_person_website" value="<?php echo $contact_person[0]->contact_person_website; ?>">
                                            <span class="validation-color" id="err_contact_person_website"><?php echo form_error('contact_person_website'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group col-md-4">
                                            <label for="contact_person_email"><!-- email -->
                                                Email
                                                <!--  <span class="validation-color">*</span> -->
                                            </label>
                                            <input type="text" class="form-control" id="contact_person_email" name="contact_person_email" value="<?php echo $contact_person[0]->contact_person_email; ?>">
                                            <span class="validation-color" id="err_contact_person_email"><?php echo form_error('contact_person_email'); ?></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="contact_person_mobile"><!-- Mobile -->
                                                Mobile<!-- <span class="validation-color">*</span> -->
                                            </label>

                                            <input type="text" class="form-control" id="contact_person_mobile" name="contact_person_mobile" value="<?php echo $contact_person[0]->contact_person_mobile; ?>">
                                            <span class="validation-color" id="err_contact_person_mobile"><?php echo form_error('contact_person_mobile'); ?></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="contact_person_telephone"><!-- telephone -->
                                                Telephone<!-- <span class="validation-color">*</span> -->
                                            </label>

                                            <input type="text" class="form-control" id="contact_person_telephone" name="contact_person_telephone" value="<?php echo $contact_person[0]->contact_person_telephone; ?>">
                                            <span class="validation-color" id="err_contact_person_telephone"><?php echo form_error('contact_person_telephone'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group col-md-4">
                                            <label for="contact_person_designation">Designation<span class="validation-color"></span>
                                            </label>
                                            <input type="text" class="form-control" id="contact_person_designation" name="contact_person_designation" value="<?php echo $contact_person[0]->contact_person_designation; ?>">
                                            <span class="validation-color" id="err_contact_person_designation"><?php echo form_error('contact_person_designation'); ?></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="contact_person_department">Department<span class="validation-color"></span></label>
                                            <input type="text" class="form-control" id="contact_person_department" name="contact_person_department" value="<?php echo $contact_person[0]->contact_person_department; ?>">
                                            <span class="validation-color" id="err_contact_person_department"><?php echo form_error('contact_person_department'); ?></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="contact_person_industry">
                                                Industry<span class="validation-color"></span>
                                            </label>
                                            <input type="text" class="form-control" id="contact_person_industry" name="contact_person_industry" value="<?php echo $contact_person[0]->contact_person_industry; ?>">
                                            <span class="validation-color" id="err_contact_person_industry"><?php echo form_error('contact_person_industry'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="well">
                                    <div class="col-sm-12">
                                        <div class="box-footer">
                                            <button type="submit" id="supplier_modal_submit" class="btn btn-info">
                                                <!-- Add -->
                                                Update</button>
                                            <span class="btn btn-default" id="cancel" onclick="cancel('supplier')"><!-- Cancel -->
                                                Cancel</span>
                                        </div>
                                    </div>
                                </div>

                            </form>
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
<script>
    var state_url = '<?php echo base_url('common/getState/') ?>';
    var city_url = '<?php echo base_url('common/getCity/') ?>';
    var state_code_url = '<?php echo base_url('common/getStateCode/') ?>';
    var supplier_add_url = '<?php echo base_url('supplier/add_supplier_ajax') ?>';
    var ledger_check_url = '<?php echo base_url('common/get_check_ledger/') ?>';

</script>

<script src="<?php echo base_url('assets/js/supplier/') ?>supplier.js"></script>
