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
            <li><a href="<?php echo base_url('contact_person'); ?>">
                    <!-- Contact Person -->
                    Contact Person
                </a></li>
            <li class="active"><!-- Add Contact Person -->
                Edit Contact Person
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
                        <h3 class="box-title"><!-- Add Contact Person -->
                            Edit Contact Person
                        </h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post" action="<?php echo base_url('contact_person/edit_contact_person'); ?>">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contact_person_code">
                                            Contact Person Code<span class="validation-color">*</span>
                                        </label>
                                        <input type="hidden" name="contact_person_id" id="contact_person_id" value="<?php echo $data[0]->contact_person_id; ?>">
                                        <input type="text" class="form-control" id="contact_person_code" name="contact_person_code" value="<?php echo $data[0]->contact_person_code; ?>">
                                        <span class="validation-color" id="err_contact_person_code"><?php echo form_error('contact_person_code'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_person_name">
                                            Contact Person Name<span class="validation-color">*</span>
                                        </label>
                                        <input type="text"  class="form-control" id="contact_person_name" name="contact_person_name" value="<?php echo $data[0]->contact_person_name; ?>">
                                        <span class="validation-color" id="err_contact_person_name"><?php echo form_error('contact_person_name'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="party_type">Party Type
                                            <span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="party_type" name="party_type" style="width: 100%;">
                                            <option value="">Select</option>
                                            <option value="customer" <?php
                                            if ($data[0]->party_type == 'customer')
                                            {
                                                echo "selected";
                                            }
                                            ?>>Customer</option>
                                            <option value="supplier" <?php
                                                    if ($data[0]->party_type == 'supplier')
                                                    {
                                                        echo "selected";
                                                    }
                                                    ?>>Supplier</option>
                                        </select>
                                        <span class="validation-color" id="err_party_type"><?php echo form_error('party_type'); ?></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="party_id">Party Name<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="party_id" name="party_id" style="width: 100%;">
                                            <option value="">Select</option>
                                            <?php
                                            if ($data[0]->party_type == 'customer')
                                            {
                                                foreach ($customer as $row)
                                                {
                                                    if ($data[0]->party_id == $row->customer_id)
                                                    {
                                                        echo "<option value='" . $row->customer_id . "' selected>" . $row->customer_name . "</option>";
                                                    }
                                                    else
                                                    {
                                                        echo "<option value='" . $row->customer_id . "'>" . $row->customer_name . "</option>";
                                                    }
                                                }
                                            }
                                            elseif ($data[0]->party_type == 'supplier')
                                            {
                                                foreach ($supplier as $row)
                                                {
                                                    if ($data[0]->party_id == $row->supplier_id)
                                                    {
                                                        echo "<option value='" . $row->supplier_id . "' selected>" . $row->supplier_name . "</option>";
                                                    }
                                                    else
                                                    {
                                                        echo "<option value='" . $row->supplier_id . "'>" . $row->supplier_name . "</option>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="validation-color" id="err_party_id"><?php echo form_error('party_id'); ?></span>
                                    </div>

                                    <div class="form-group">
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
                                                if ($key->country_id == $data[0]->contact_person_country_id)
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
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
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
                                                if ($key->state_id == $data[0]->contact_person_state_id)
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

                                    <div class="form-group">
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
                                                if ($key->city_id == $data[0]->contact_person_city_id)
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

                                    <div class="form-group">
                                        <label for="postal_code"><!-- Postal Code -->
                                            Postal Code</label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo $data[0]->contact_person_postal_code; ?>">
                                        <span class="validation-color" id="err_postal_code"><?php echo form_error('postal_code'); ?></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="email"><!-- email -->
                                            Email
                                            <!--  <span class="validation-color">*</span> -->
                                        </label>
                                        <input type="text" class="form-control" id="email" name="email" value="<?php echo $data[0]->contact_person_email; ?>">
                                        <span class="validation-color" id="err_email"><?php echo form_error('email'); ?></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="mobile"><!-- Mobile -->
                                        Mobile<!-- <span class="validation-color">*</span> -->
                                        </label>

                                        <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo $data[0]->contact_person_mobile; ?>">
                                        <span class="validation-color" id="err_mobile"><?php echo form_error('mobile'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="telephone"><!-- telephone -->
                                        Telephone<!-- <span class="validation-color">*</span> -->
                                        </label>
                                        <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo $data[0]->contact_person_telephone; ?>">
                                        <span class="validation-color" id="err_telephone"><?php echo form_error('telephone'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="website">Website<span class="validation-color"></span>
                                        </label>
                                        <input type="text" class="form-control" id="website" name="website" value="<?php echo $data[0]->contact_person_website; ?>">
                                        <span class="validation-color" id="err_website"><?php echo form_error('website'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="designation">Designation<span class="validation-color"></span>
                                        </label>
                                        <input type="text" class="form-control" id="designation" name="designation" value="<?php echo $data[0]->contact_person_designation; ?>">
                                        <span class="validation-color" id="err_designation"><?php echo form_error('designation'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="department">Department<span class="validation-color"></span></label>
                                        <input type="text" class="form-control" id="department" name="department" value="<?php echo $data[0]->contact_person_department; ?>">
                                        <span class="validation-color" id="err_tan"><?php echo form_error('department'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="industry">
                                            Industry<span class="validation-color"></span>
                                        </label>
                                        <input type="text" class="form-control" id="industry" name="industry" value="<?php echo $data[0]->contact_person_industry; ?>">
                                        <span class="validation-color" id="err_industry"><?php echo form_error('industry'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address1">
                                            Address<span class="validation-color"></span>
                                        </label>
                                        <textarea class="form-control" id="address" rows="2" name="address"><?php
echo str_replace(array(
        "\r\n",
        "\\r\\n",
        "\n",
        "\\n" ), "&#10;", $data[0]->contact_person_address);
?></textarea>
                                        <span class="validation-color" id="err_address"><?php echo form_error('address'); ?></span>
                                    </div>
                                </div>
                                <div class="well">
                                    <div class="col-sm-12">
                                        <div class="box-footer">
                                            <button type="submit" id="contact_person_submit" class="btn btn-info">
                                                <!-- Add -->
                                                Update</button>
                                            <span class="btn btn-default" id="cancel" onclick="cancel('contact_person')"><!-- Cancel -->
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
                var state_url = '<?php echo base_url('common/getState/') ?>';
                var city_url = '<?php echo base_url('common/getCity/') ?>';
                var state_code_url = '<?php echo base_url('common/getStateCode/') ?>';
            </script>
            <script src="<?php echo base_url('assets/js/contact_person/') ?>contact_person.js"></script>
