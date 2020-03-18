<?php
defined('BASEPATH') OR exit('No direct script access allowed');
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
// $p = array('admin', 'manager');
// if (!(in_array($this->session->userdata('type'), $p))) {
//     redirect('auth');
// }
$this->load->view('layout/header');
?>
<style type="text/css">
    .tabactive .active>a{background-color: #0177a9!important;color: #fff!important }
    .tabactive a{color: #000}
    .nav-tabs>li>a{color: #000!important}
    .mt-30{margin:30px 0px;}
    .table-scroll{width: 100%;overflow: scroll;display: none;}
    .add-more>.fa,.removeDiv>.fa{font-size: 20px;  }
    .add-more,.removeDiv{color: #0177a9 !important;cursor: pointer;}
    .add-more{margin-top: 25px;}
    .mt-25{margin-top: 25px}
    #getRowsValue{color:#0177a9;display: none;}
    .alert-custom{color:red;display: none;}
    input[type=radio] {
        height: 20px;
        width: 20px;
    }
    .require_field{display: none;color:red}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

    </section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('labour'); ?>">Labour</a></li>
            <li class="active">Edit Labour</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Labour</h3>
                        <a class="btn btn-default pull-right" id="cancel" onclick="cancel('labour')">Back</a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('labour/edit_labour'); ?>" encType="multipart/form-data">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="raw_material_code">Activity Name <span class="validation-color">*</span></label>
                                        <input type="hidden" name="labour_id" id="labour_id" value="<?= $labour_data[0]->labour_id ?>">
                                        <input type="text" class="form-control" tabindex="1"  id="activity_name" name="activity_name" value="<?= $labour_data[0]->activity_name ?>">
                                        <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="raw_material_code">Classification <span class="validation-color">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <span  data-toggle="modal" data-target="#add_classification" style="float: right;color:blue">+</span>
                                            </div>
                                            <select class="form-control select2" id="classify" name="classify" style="width: 100%;" tabindex="6">
                                                <option value="">Select</option>
                                                <?php
                                                foreach ($labour_classification as $key => $value)
                                                {
                                                    if ($value->labour_classification_id == $labour_data[0]->labour_classification_id)
                                                    {
                                                        echo "<option value='" . $value->labour_classification_id . "' selected>" . $value->labour_classification_name . "</option>";
                                                    }
                                                    else
                                                    {

                                                        echo "<option value='" . $value->labour_classification_id . "'>" . $value->labour_classification_name . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="raw_material_code">No Of Labour <span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" tabindex="1"  id="no_of_labour" name="no_of_labour" value="<?= $labour_data[0]->no_of_labour ?>">
                                        <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="raw_material_code">Cost per Hour <span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" tabindex="1"  id="cost_per_person" name="cost_per_person" value="<?= $labour_data[0]->cost_per_hour ?>">
                                        <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!--   <div class="col-md-3">
                                  <div class="form-group">
                                    <label for="raw_material_code"> No of Days<span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" tabindex="1"  id="no_of_days" name="no_of_days" value="">
                                    <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                  </div>
                                </div> -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="raw_material_code"> Type<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="labour_type" name="labour_type" style="width: 100%;" tabindex="6">
                                            <option value="hourly" <?php
                                            if ($labour_data[0]->type == "hourly")
                                            {
                                                echo "selected";
                                            }
                                            ?>>Hourly</option>
                                            <option value="daily_basis" <?php
                                                    if ($labour_data[0]->type == "daily_basis")
                                                    {
                                                        echo "selected";
                                                    }
                                                    ?>>Days Basis</option>
                                        </select>
                                    </div>
                                </div>

<?php if ($labour_data[0]->type == "daily_basis")
{
    ?>
                                    <div id="day_basis">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="raw_material_code"> No of Hours/days<span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" tabindex="1"  id="no_of_hours" name="no_of_hours" value="<?= $labour_data[0]->no_of_hours ?>">
                                                <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="raw_material_code"> No of Days<span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" tabindex="1"  id="no_of_days_per" name="no_of_days_per" value="<?= $labour_data[0]->no_of_days ?>">
                                                <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                            </div>
                                        </div>
                                    </div>
<?php } ?>
                                <div class="col-md-3" id="total_hours">
                                    <div class="form-group" >
                                        <label for="raw_material_code">Total Hours<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control"  tabindex="1"  id="total_hours" name="total_hours" value="<?= $labour_data[0]->total_no_hours ?>">
                                        <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="box-footer">
                                        <button type="submit" id="varient_submit" name="submit" value="add" class="btn btn-info">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <p id="result"></p>
                    <!-- <button id="finalresults">finalresults</button>    -->
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
<!-- Modal -->
<div id="add_classification" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Classification</h4>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="raw_material_code">Classification Name<span class="validation-color">*</span></label>
                                <input type="text" class="form-control" tabindex="1"  id="labour_classification_name" name="labour_classification_name" value="">
                                <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-sm-12">
                    <div class="box-footer">
                        <button type="submit" id="add_classification_submit" name="submit" value="add" class="btn btn-info"  data-dismiss="modal">Add</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('tax/tax_modal');
$this->load->view('product/hsn_modal');
$this->load->view('category/category_modal');
$this->load->view('subcategory/subcategory_modal');
?>
<script type="text/javascript">
    $("#labour_type").change(function ()
    {
        if ($("#labour_type").val() == "hourly")
        {
            $("#day_basis").hide()
        }
        if ($("#labour_type").val() == "daily_basis") {
            $("#day_basis").show()
        }
    })
    $("#add_classification_submit").click(function () {
        $.ajax({
            url: base_url + 'manufacturing/add_classification',
            dataType: 'JSON',
            method: 'POST',
            data: {'classification_name': $('#labour_classification_name').val()},
            success: function (result)
            {
                console.log(result);
                var data = result.data;
                $('#labour_classification_name').val('');
                $('#classify').append('<option value="">Select</option>');
                for (i = 0; i < data.length; i++)
                {
                    $('#classify').append('<option value="' + data[i].labour_classification_id + '">' + data[i].labour_classification_name + '</option>')
                }
                $('#classify').val(result.labour_classification_id).attr("selected", "selected")
            }
        })
    });
</script>
