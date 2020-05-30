<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('super_admin/layouts/header');
function in_object($ob,$key){
    foreach ($ob as $k => $value) {
        if($key == $value->module_id){
            return true;
            break;
        }
    }
    return false;
}
?>
<style type="text/css">
    .module_list li{list-style: none;}
    .module_list ul{padding-left: 5px;}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper add_margin">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('superadmin/auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?php echo base_url('superadmin/firm'); ?>">Package</a></li>
                <li class="active">Edit Package</li>
            </ol>
        </h5>    
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Package</h3>
                    </div>
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('superadmin/payment_methods/package_update'); ?>" encType="multipart/form-data">
                            <div class="well">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="hidden" name="package_id" value="<?=$package_detail->Id;?>">
                                            <label for="name">Package</label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?=$package_detail->payment_method;?>" readonly="readonly">
                                            <span class="validation-color" id="err_name"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Valid Days</label>
                                            <input type="number" class="form-control" id="valid_days" name="valid_days" value="<?=$package_detail->valid_days;?>">
                                            <span class="validation-color" id="err_valid_days"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Amounts</label>
                                            <input type="number" class="form-control" id="amounts" name="amounts" value="<?=$package_detail->amounts;?>">
                                            <span class="validation-color" id="err_amounts"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row module_list">
                                    <?php
                                    $modules = array_chunk($modules, ceil(count($modules)/3), true);
                                    ?>
                                    <div class="col-sm-4 ">
                                        <ul>
                                        <?php
                                        foreach ($modules[0] as $key => $value) { 
                                            $checked = '';
                                            if(in_object($selected_modules,$value->module_id))
                                                $checked = 'checked'; ?>
                                            <li><label><input type="checkbox" name="module_id[]" value="<?=$value->module_id;?>" <?=$checked;?>> <?=$value->module_name;?></label></li>
                                        <?php } ?>
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        <ul>
                                        <?php
                                        foreach ($modules[1] as $key => $value) { 
                                           $checked = '';
                                            if(in_object($selected_modules,$value->module_id))
                                                $checked = 'checked'; ?>
                                            <li><label><input type="checkbox" name="module_id[]" value="<?=$value->module_id;?>" <?=$checked;?>> <?=$value->module_name;?></label></li>
                                        <?php } ?>
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        <ul>
                                        <?php
                                        foreach ($modules[2] as $key => $value) { 
                                            $checked = '';
                                            if(in_object($selected_modules,$value->module_id))
                                                $checked = 'checked'; ?>
                                            <li><label><input type="checkbox" name="module_id[]" value="<?=$value->module_id;?>" <?=$checked;?>> <?=$value->module_name;?></label></li>
                                        <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-sm-12">
                                <div class="box-footer">
                                    <button type="submit" id="update_package" name="update_package" class="btn btn-info btn-flat">Update</button>
                                    <span class="btn btn-default btn-flat" id="cancel" onclick="cancel('superadmin/payment_methods')">Cancel</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php $this->load->view('layout/footer'); ?>