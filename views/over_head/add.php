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
            <li><a href="<?php echo base_url('over_head'); ?>">Over Head</a></li>
            <li class="active">Add Over Head</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Over Head</h3>
                        <a class="btn btn-default pull-right" id="cancel" onclick="cancel('product')">Back</a>
                    </div>
                    <div class="well">
                        <div class="box-body">
                            <form role="form" id="form" method="post" action="<?php echo base_url('over_head/add_over_head'); ?>" encType="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="raw_material_code">Name<span class="validation-color">*</span></label>
                                            <input type="text" class="form-control" tabindex="1"  id="over_head_name" name="over_head_name" value="">
                                            <span class="validation-color" id="err_over_head_name"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="raw_material_code">Unit<span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="over_head_unit" name="over_head_unit" style="width: 100%;" tabindex="4">
                                                <option value="">Select</option>
                                                <?php
                                                foreach ($uqc as $value)
                                                {
                                                    echo "<option value='$value->uom - $value->description'" . set_select('brand', $value->id) . ">$value->uom - $value->description</option>";
                                                }
                                                ?>
                                            </select>
                                            <span class="validation-color" id="err_over_head_unit"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="raw_material_code">Cost per Unit <span class="validation-color">*</span></label>
                                            <input type="text" class="form-control" tabindex="1"  id="cost_per_unit" name="cost_per_unit" value="">
                                            <span class="validation-color" id="err_cost_per_unit"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="raw_material_code">Quantity <span class="validation-color">*</span></label>
                                            <input type="text" class="form-control" tabindex="1"  id="over_head_quantity" name="over_head_quantity" value="">
                                            <span class="validation-color" id="err_over_head_quantity"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="box-footer">
                                            <button type="submit" id="submit" name="submit" value="add" class="btn btn-info">Add</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
?>
