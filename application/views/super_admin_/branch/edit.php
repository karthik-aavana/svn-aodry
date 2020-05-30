<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('super_admin/layouts/header');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('superadmin/auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?php echo base_url('superadmin/branch'); ?>">Branch</a></li>
                <li class="active">Edit Branch</li>
            </ol>
        </h5>    
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Branch</h3>
                    </div>
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('superadmin/branch/edit_branch'); ?>" encType="multipart/form-data">
                            <div class="well">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="hidden" class="form-control" id="firm_id" name="firm_id" value="<?php
                                            if (isset($data[0]->firm_id)) {
                                                echo $data[0]->firm_id;
                                            }
                                            ?>">
                                            <input type="hidden" class="form-control" id="branch_id" name="branch_id" value="<?php
                                            if (isset($data[0]->branch_id)) {
                                                echo $data[0]->branch_id;
                                            }
                                            ?>">
                                            <label for="name">Branch Name<span class="validation-color"> *</span></label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php
                                            if (isset($data[0]->branch_name)) {
                                                echo $data[0]->branch_name;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="short_name">Short Name<span class="validation-color"> *</span></label>
                                            <input type="text" class="form-control" id="short_name" name="short_name" value="<?php
                                            if (isset($data[0]->firm_short_name)) {
                                                echo $data[0]->firm_short_name;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_short_name"><?php echo form_error('short_name'); ?></span>
                                        </div>
                                    </div> -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="registered_type">Registered Type<span class="validation-color"> *</span></label>                                            
                                            <select class="form-control" id="registered_type" name="registered_type">
                                                <option value="">Select</option>
                                                <option value="Private Limited Company" <?php if(isset($data[0]->firm_registered_type)){ if($data[0]->firm_registered_type=='Private Limited Company'){ echo "selected";}}?>>Private Limited Company</option>
                                                <option value="Proprietorship" <?php if(isset($data[0]->firm_registered_type)){ if($data[0]->firm_registered_type=='Proprietorship'){ echo "selected";}}?>>Proprietorship</option>
                                                <option value="Partnership" <?php if(isset($data[0]->firm_registered_type)){ if($data[0]->firm_registered_type=='Partnership'){ echo "selected";}}?>>Partnership</option>
                                                <option value="One Person Company" <?php if(isset($data[0]->firm_registered_type)){ if($data[0]->firm_registered_type=='One Person Company'){ echo "selected";}}?>>One Person Company</option>
                                                <option value="Limited Liability Partnership" <?php if(isset($data[0]->firm_registered_type)){ if($data[0]->firm_registered_type=='Limited Liability Partnership'){ echo "selected";}}?>>Limited Liability Partnership</option>
                                                <!-- <option value="Foreign Company in India" <?php if($data[0]->firm_registered_type=='Foreign Company in India'){ echo "selected";}?>>Foreign Company in India</option> -->
                                            </select>
                                            <span class="validation-color" id="err_registration_type"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="Company Code">Company Code<span class="validation-color"> *</span></label>
                                            <input type="text" class="form-control" id="company_code" name="company_code" value="<?php
                                            if (isset($data[0]->firm_company_code)) {
                                                echo $data[0]->firm_company_code;
                                            }
                                            ?>" autocomplite="off" readonly>
                                            <span class="validation-color" id="err_company_code"><?php echo form_error('company_code'); ?></span>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="iec">Invoice Reference
                                                <span class="validation-color"> *</span>
                                            </label>
                                            <input type="text" class="form-control" id="invoice_reference" name="invoice_reference" value="<?php
                                            if (isset($data[0]->invoice_reference)) {
                                                echo $data[0]->invoice_reference;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_inv_ref"></span>
                                        </div>
                                    </div> -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="financial_year_id">Financial Year<span class="validation-color"> *</span></label>
                                            <select class="form-control select2" id="financial_year_id" name="financial_year_id" style="width: 100%;">
                                                <?php
                                                    foreach ($financial_year as $key => $value) 
                                                    {
                                                        if($value->financial_year_id==$data[0]->financial_year_id)
                                                        {
                                                            echo "<option value='".$value->financial_year_id."'' selected>".$value->financial_year_title."</option>";
                                                        }
                                                        else
                                                        {
                                                            echo "<option value='".$value->financial_year_id."''>".$value->financial_year_title."</option>";
                                                        }
                                                    }
                                                ?>
                                            </select>
                                            <span class="validation-color" id="err_financial_year_id"><?php echo form_error('financial_year_id'); ?></span>
                                        </div>
                                    </div>
                                          
                                </div>
                                <div class="row">  
                                    <div class="col-md-3" hidden="true">
                                        <div class="form-group">
                                            <label for="file"><span>Logo</span></label>
                                            <?php if (!isset($data[0]->firm_logo) || $data[0]->firm_logo=="") { ?>
                                            <input type="file" class="form-control" id="logo" name="logo" >
                                            <?php }?>
                                            <div id="logo_pic">
                                            <?php if (isset($data[0]->firm_logo) && $data[0]->firm_logo) { ?>
                                            <img src="<?php echo base_url('assets/branch_files/').$data[0]->branch_id.'/'. $data[0]->firm_logo; ?>" width="18%" id="logo_img">
                                            
                                            <a href="" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#remove_logo" title="Remove Logo" ><i class="fa fa-trash-o text-purple"></i></a>
                                            <?php } ?>
                                            </div>
                                            <input type="hidden" id="hidden_logo_name" name="hidden_logo_name" value="<?php
                                            if (isset($data[0]->firm_logo)) 
                                            {
                                                echo $data[0]->firm_logo;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_logo"><?php echo form_error('logo'); ?></span>
                                        </div>
                                    </div>  
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="branch_gst_registration_type">GST Registration Type
                                                <!-- <?php //echo $this->lang->line('biller_lable_country');            ?> --> <span class="validation-color"> *</span>
                                            </label>
                                            <select class="form-control select2" id="branch_gst_registration_type" name="branch_gst_registration_type" style="width: 100%;">
                                                <option value="">Select</option>
                                                <option <?php
                                                if (isset($data[0]->branch_gst_registration_type)) {
                                                    if ($data[0]->branch_gst_registration_type == "Registered") {
                                                        echo "selected";
                                                    }
                                                }
                                                ?>>Registered
                                                </option>
                                                <option <?php
                                                if (isset($data[0]->branch_gst_registration_type)) {
                                                    if ($data[0]->branch_gst_registration_type == "Unregistered") {
                                                        echo "selected";
                                                    }
                                                }
                                                ?>>Unregistered
                                                </option>                                              
                                            </select>
                                        </div>
                                        <div>
                                             <span class="validation-color" id="err_branch_gst_registration_type"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" id="gstn_selected">
                                            <label for="gstin">GSTIN<span class="validation-color"> *</span></label>
                                            <input type="text" class="form-control" id="branch_gstin_number" name="branch_gstin_number" value="<?php
                                            if (isset($data[0]->branch_gstin_number)) {
                                                echo $data[0]->branch_gstin_number;
                                            }
                                            ?>" autocomplite="off">
                                            <span class="validation-color" id="err_branch_gstin_number"><?php echo form_error('branch_gstin_number'); ?></span>
                                        </div>
                                    </div> 
                                            
               
                                </div>
                                <div class="row"> 
                                </div>
                            </div>                            
                            <div class="well">
                                <div class="row">
                                    <div class="col-md-3" hidden="true">
                                        <div class="form-group">
                                            <label for="iec">Branch Name
                                                <span class="validation-color"> *</span>
                                            </label>
                                            <input type="text" class="form-control" id="branch_name" name="branch_name" value="<?php
                                            if (isset($data[0]->branch_name)) {
                                                echo $data[0]->branch_name;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_branch_name"></span>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="country">Country <span class="validation-color"> *</span></label>
                                            <input type="hidden" id="country_edit" name="country_edit" value="1">
                                            <select class="form-control select2" id="country" name="country" style="width: 100%;">
                                                <option value="">Select</option>
                                                <?php
                                                foreach ($country as $key) {
                                                    ?>
                                                    <option
                                                        value='<?php echo $key->country_id ?>'
                                                        <?php
                                                        if (isset($data[0]->branch_country_id)) {
                                                            if ($key->country_id == $data[0]->branch_country_id) {
                                                                echo "selected";
                                                            }
                                                        }
                                                        ?>
                                                        >
                                                        <?php echo $key->country_name; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <span class="validation-color" id="err_country"><?php echo form_error('country'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="state">State <span class="validation-color"> *</span></label>
                                            <select class="form-control select2" id="state" name="state" style="width: 100%;">
                                                <option value="">Select</option>
                                                <?php
                                                foreach ($state as $key) {
                                                    ?>
                                                    <option
                                                        value='<?php echo $key->state_id ?>'
                                                        <?php
                                                        if (isset($data[0]->branch_state_id)) {
                                                            if ($key->state_id == $data[0]->branch_state_id) {
                                                                echo "selected";
                                                            }
                                                        }
                                                        ?>
                                                        >
                                                        <?php echo $key->state_name; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <span class="validation-color" id="err_state"><?php echo form_error('state'); ?></span>
                                            <input type="hidden" class="form-control" id="state_code" name="state_code" value="<?php
                                            if (isset($data[0]->state_code)) {
                                                echo $data[0]->state_code;
                                            }
                                            ?>">
                                        </div>
                                    </div>
                                        <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="city">City <span class="validation-color"> *</span></label>
                                            <select class="form-control select2" id="city" name="city" style="width: 100%;">
                                                <option value="">Select</option>
                                                <?php
                                                foreach ($city as $key) {
                                                    ?>
                                                    <option
                                                        value='<?php echo $key->city_id ?>'
                                                        <?php
                                                        if (isset($data[0]->branch_city_id)) {
                                                            if ($key->city_id == $data[0]->branch_city_id) {
                                                                echo "selected";
                                                            }
                                                        }
                                                        ?>
                                                        >
                                                        <?php echo $key->city_name; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <span class="validation-color" id="err_city"><?php echo form_error('city'); ?></span>
                                        </div>
                                    </div>                                   
                                </div>
                                <div class="row">                                    
                                    <!-- <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="street">Street <span class="validation-color"> *</span></label>
                                            <input type="text" class="form-control" id="street" name="street" value="<?php
                                            if (isset($data[0]->street)) {
                                                echo $data[0]->street;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_street"><?php echo form_error('street'); ?></span>
                                        </div>
                                    </div> -->
                                     <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="country">Address <span class="validation-color"> *</span></label>
                                            <input type="text" class="form-control" id="branch_address" name="branch_address" value="<?php  if (isset($data[0]->branch_address)) {
                                                echo $data[0]->branch_address;
                                            }  ?>">
                                           <span class="validation-color" id="err_branch_address"><?php echo form_error('branch_address'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="branch_postal_code">Zip Code </label>
                                            <input type="text" class="form-control" id="branch_postal_code" name="branch_postal_code" value="<?php
                                            if (isset($data[0]->branch_postal_code)) {
                                                echo $data[0]->branch_postal_code;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_branch_postal_code"><?php echo form_error('branch_postal_code'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="email">Email <span class="validation-color"> *</span></label>
                                            <input type="text" class="form-control" id="email" name="email" value="<?php
                                            if (isset($data[0]->branch_email_address)) {
                                                echo $data[0]->branch_email_address;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_email"><?php echo form_error('email'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="phone">Mobile </label>
                                            <input type="text" class="form-control" id="mobile" name="mobile" value="<?php
                                            if (isset($data[0]->branch_mobile)) {
                                                echo $data[0]->branch_mobile;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_mobile"><?php echo form_error('mobile'); ?></span>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="phone">Landline Number</label>
                                            <input type="text" class="form-control" id="land_number" name="land_number" value="<?php if(isset($data[0]->branch_land_number)) { echo $data[0]->branch_land_number;} ?>">
                                            <span class="validation-color" id="err_land_number"></span>
                                           
                                        </div>
                                    </div>
                                 
                                </div>
                                    
                            </div>
                            <div class="well">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="servicetaxno">PAN Number
                                                <span class="validation-color"></span>
                                            </label>
                                            <input type="text" class="form-control" id="pan_number" name="pan_number" value="<?php
                                            if (isset($data[0]->branch_pan_number)) {
                                                echo $data[0]->branch_pan_number;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_pan_number"><?php echo form_error('pan_number'); ?></span>
                                        </div>
                                    </div>
                                    <div id="hide_cin" <?php if(isset($data[0]->firm_registered_type)) { if($data[0]->firm_registered_type=="Proprietorship" || $data[0]->firm_registered_type=="Partnership" || $data[0]->firm_registered_type=="Foreign Company in India") {?> style="display:none" <?php }} ?>>
                                    <div class="col-md-3" >
                                        <div class="form-group">
                                            <label for="cin_number">CIN Number
                                                <!-- <?php //echo $this->lang->line('add_customer_compname');            ?> -->
                                                <span class="validation-color"></span>
                                            </label>
                                            <input type="text" class="form-control" id="cin_number" name="cin_number" value="<?php
                                            if (isset($data[0]->branch_cin_number)) {
                                                echo $data[0]->branch_cin_number;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_cin_number"><?php echo form_error('cin_number'); ?></span>
                                        </div>
                                    </div>                                  
                                    <div class="col-md-3" hidden="true">
                                        <div class="form-group">
                                            <label for="branch_roc">ROC
                                                <span class="validation-color"></span>
                                            </label>
                                            <input type="text" class="form-control" id="branch_roc" name="branch_roc" value="<?php
                                            if (isset($data[0]->branch_roc)) {
                                                echo $data[0]->branch_roc;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_branch_roc"><?php echo form_error('branch_roc'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="branch_esi">ESI
                                                <span class="validation-color"></span>
                                            </label>
                                            <input type="text" class="form-control" id="branch_esi" name="branch_esi" value="<?php
                                            if (isset($data[0]->branch_csi)) {
                                                echo $data[0]->branch_csi;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_branch_esi"><?php echo form_error('branch_esi'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="branch_pf">PF
                                                <span class="validation-color"></span>
                                            </label>
                                            <input type="text" class="form-control" id="branch_pf" name="branch_pf" value="<?php
                                            if (isset($data[0]->branch_pf)) {
                                                echo $data[0]->branch_pf;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_branch_pf"><?php echo form_error('branch_pf'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tan_number">TAN Number
                                                <!-- <?php // echo $this->lang->line('add_customer_compname');           ?> -->
                                                <span class="validation-color"></span>
                                            </label>
                                            <input type="text" class="form-control" id="tan_number" name="tan_number" value="<?php
                                            if (isset($data[0]->branch_tan_number)) {
                                                echo $data[0]->branch_tan_number;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_tan_number"><?php echo form_error('tan_number'); ?></span>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="iec">Import & Export Code
                                                <span class="validation-color"></span>
                                            </label>
                                            <input type="text" class="form-control" id="import_export_code" name="import_export_code" value="<?php
                                            if (isset($data[0]->branch_import_export_code)) {
                                                echo $data[0]->branch_import_export_code;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_import_export_code"><?php echo form_error('import_export_code'); ?></span>
                                        </div>
                                    </div> -->
                                    <div class="col-md-3" hidden="true">
                                        <div class="form-group">
                                            <label for="iec"><span>Import & Export Code</span></label>
                                            <?php if (!isset($data[0]->branch_import_export_code) || $data[0]->branch_import_export_code=="") { ?>
                                            <input type="file" class="form-control" id="import_export_code" name="import_export_code" >
                                            <?php }?>
                                            <div id="iec_file">
                                            <?php if (isset($data[0]->branch_import_export_code) && $data[0]->branch_import_export_code) { ?>
                                            <a href="<?php echo base_url('assets/branch_files/').$data[0]->branch_id.'/'. $data[0]->branch_import_export_code; ?>" title="Document" target="_blank"><i class="fa fa-file text-green"></i></a>
                                            
                                            <a href="" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#remove_iec" title="Remove Document" ><i class="fa fa-trash-o text-purple"></i></a>
                                            <?php } ?>
                                            </div>
                                            <input type="hidden" id="hidden_iec_name" name="hidden_iec_name" value="<?php
                                            if (isset($data[0]->branch_import_export_code)) 
                                            {
                                                echo $data[0]->branch_import_export_code;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_import_export_code"><?php echo form_error('import_export_code'); ?></span>
                                        </div>
                                    </div>  
                                    <div class="col-md-3" hidden="true">
                                        <div class="form-group">
                                            <label for="shop"><span>Shop & Establishment</span></label>
                                            <?php if (!isset($data[0]->branch_shop_establishment) || $data[0]->branch_shop_establishment=="") { ?>
                                            <input type="file" class="form-control" id="shop_establishment" name="shop_establishment" >
                                            <?php }?>
                                            <div id="shop_file">
                                            <?php if (isset($data[0]->branch_shop_establishment) && $data[0]->branch_shop_establishment) { ?>
                                            <a href="<?php echo base_url('assets/branch_files/').$data[0]->branch_id.'/'. $data[0]->branch_shop_establishment; ?>" title="Document" target="_blank"><i class="fa fa-file text-green"></i></a>
                                            
                                            <a href="" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#remove_shop" title="Remove Document" ><i class="fa fa-trash-o text-purple"></i></a>
                                            <?php } ?>
                                            </div>
                                            <input type="hidden" id="hidden_shop_name" name="hidden_shop_name" value="<?php
                                            if (isset($data[0]->branch_shop_establishment)) 
                                            {
                                                echo $data[0]->branch_shop_establishment;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_shop_establishment"><?php echo form_error('shop_establishment'); ?></span>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="shop">Shop & Establishment
                                                <span class="validation-color"></span>
                                            </label>
                                            <input type="text" class="form-control" id="shop_establishment" name="shop_establishment" value="<?php
                                            if (isset($data[0]->branch_shop_establishment)) {
                                                echo $data[0]->branch_shop_establishment;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_shop_establishment"><?php echo form_error('shop_establishment'); ?></span>
                                        </div>
                                    </div> -->
                                    <!-- <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="others">Others
                                                <span class="validation-color"></span>
                                            </label>
                                            <input type="text" class="form-control" id="others" name="others" value="<?php
                                            if (isset($data[0]->branch_others)) {
                                                echo $data[0]->branch_others;
                                            }
                                            ?>">
                                            <span class="validation-color" id="err_others"><?php echo form_error('others'); ?></span>
                                        </div>
                                    </div> -->
                                </div>
                            </div> 
                             <div class="well">
                                <div class="row">
                                       <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="branch_code">Login Code <span class="validation-color"> *</span></label>
                                            <input type="text" class="form-control" id="branch_code" name="branch_code" value="<?php if(isset($data[0]->branch_code)) { echo $data[0]->branch_code;} ?>">
                                            <span class="validation-color" id="err_branch_code"></span>
                                        </div>
                                    </div>
                                                                        <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="round_off_access">Round Off Access<span class="validation-color"> *</span></label>
                                            <select class="form-control select2" id="round_off_access" name="round_off_access" style="width: 100%;">
                                                <option value="yes" <?php if(isset($data[0]->round_off_access)) { if($data[0]->round_off_access=='yes'){echo "selected";}}?>>Yes</option>
                                                <option value="no" <?php if(isset($data[0]->round_off_access)) { if($data[0]->round_off_access=='no'){echo "selected";}}?>>No</option>
                                            </select>
                                            <span class="validation-color" id="err_round_off_access"><?php echo form_error('round_off_access'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tax_split_equaly">Tax Split Equally<span class="validation-color"> *</span></label>
                                            <select class="form-control select2" id="tax_split_equaly" name="tax_split_equaly" style="width: 100%;">
                                                <option value="yes" <?php if(isset($data[0]->tax_split_equaly)) { if($data[0]->tax_split_equaly=='yes'){echo "selected";}}?>>Yes</option>
                                                <option value="no" <?php if(isset($data[0]->tax_split_equaly)) { if($data[0]->tax_split_equaly=='no'){echo "selected";}}?>>No</option>
                                            </select>
                                            <span class="validation-color" id="err_tax_split_equaly"><?php echo form_error('tax_split_equaly'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tax_split_equaly">Default Notification Date<span class="validation-color"> *</span></label>
                                            <select class="form-control select2" id="default_notification_date" name="default_notification_date" style="width: 100%;">
                                               <option <?php if(isset($data[0]->invoice_footer)) { echo ($data[0]->default_notification_date == 5 ? "selected":"");}?>>5</option>
                                               <option <?php if(isset($data[0]->invoice_footer)) { echo ($data[0]->default_notification_date == 10 ? "selected":"");}?>>10</option>
                                               <option <?php if(isset($data[0]->invoice_footer)) { echo ($data[0]->default_notification_date == 20? "selected":"");}?>>20</option>
                                               <option <?php if(isset($data[0]->invoice_footer)) { echo ($data[0]->default_notification_date == 30 ? "selected":"");}?>>30</option>
                                            </select>
                                            <span class="validation-color" id="err_tax_split_equaly"><?php echo form_error('tax_split_equaly'); ?></span>
                                        </div>
                                    </div>
                                
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="currency_id">Currency<span class="validation-color"> *</span></label>
                                            <select class="form-control select2" id="currency_id" name="currency_id" style="width: 100%;">
                                                <?php
                                                    foreach ($currency as $key => $value){
                                                        if ($value->currency_id == $data[0]->branch_default_currency) {
                                                            echo "
                                                                <option value='" . $value->currency_id . "' selected>" . $value->currency_name.' - ' . $value->country_shortname. "</option>";
                                                        } else {
                                                            echo "
                                                                <option value='" . $value->currency_id . "'>" . $value->currency_name . ' - ' . $value->country_shortname."</option>";
                                                        }
                                                    }
                                                ?>
                                            </select>
                                            <span class="validation-color" id="err_currency_id"><?php echo form_error('currency_id'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" id="gstn_selected">
                                            <label for="gstin">Set Financial Year Password<span class="validation-color"> *</span></label>
                                            <input type="text" class="form-control" id="financial_year_password" name="financial_year_password" value="<?php
                                            if (isset($data[0]->financial_year_password)) {
                                                echo $this->encryption->decrypt($data[0]->financial_year_password);
                                            }
                                            ?>" autocomplite="off">
                                            <span class="validation-color" id="err_financial_year_password"><?php echo form_error('err_financial_year_password'); ?></span>
                                        </div>
                                    </div>
                                        <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="invoice_footer">Invoice Footer</label>
                                            <textarea type="text" class="form-control" id="invoice_footer" name="invoice_footer"><?php if(isset($data[0]->invoice_footer)) { echo $data[0]->invoice_footer;}?></textarea>
                                            <span class="validation-color" id="err_invoice_footer"><?php echo form_error('invoice_footer'); ?></span>
                                        </div>
                                    </div> 
                                </div>
                                </div> 
                            <div class="col-sm-12">
                                <div class="box-footer">
                                    <button type="submit" id="company_submit" name="company_submit" class="btn btn-info btn-flat">Update</button>
                                    <span class="btn btn-default btn-flat" id="cancel" onclick="cancel('superadmin/branch')">Cancel</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- <div id="remove_logo" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
   <div class="modal-dialog modal-md">
      <form id="logoForm">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title">Remove Logo</h4>
            </div>
            <div class="modal-body">
                <label for="category_name">Are your sure! You want to remove logo ?</label>
            </div>
            <div class="modal-footer">
               <a href="<?php echo base_url('company_setting/remove_logo');?>" class="btn btn-sm btn-info pull-right" style="margin-left: 10px;">Yes</a>
               <button class="btn btn-sm btn-submit pull-right" data-dismiss="modal">No</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div id="remove_iec" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
   <div class="modal-dialog modal-md">
      <form id="iecForm">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title">Remove Import & Export Code</h4>
            </div>
            <div class="modal-body">
                <label for="category_name">Are your sure! You want to remove this file ?</label>
            </div>
            <div class="modal-footer">
               <a href="<?php echo base_url('company_setting/remove_iec');?>" class="btn btn-sm btn-info pull-right" style="margin-left: 10px;">Yes</a>
               <button class="btn btn-sm btn-submit pull-right" data-dismiss="modal">No</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div id="remove_shop" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
   <div class="modal-dialog modal-md">
      <form id="shopForm">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title">Remove Shop Establishment</h4>
            </div>
            <div class="modal-body">
                <label for="category_name">Are your sure! You want to remove this file ?</label>
            </div>
            <div class="modal-footer">
               <a href="<?php echo base_url('company_setting/remove_shop');?>" class="btn btn-sm btn-info pull-right" style="margin-left: 10px;">Yes</a>
               <button class="btn btn-sm btn-submit pull-right" data-dismiss="modal">No</button>
            </div>
         </div>
      </form>
   </div>
</div> -->
<?php $this->load->view('layout/footer'); ?>
<script>
    var mobile_regex= /^[\+\d\-\s]+$/;
    $(document).ready(function () {
        $("#gstn_selected").hide();
        $("#branch_gst_registration_type").change(function () {
            $("#gstn_selected").hide();
            var gstn_type = $(this).val();
            if (gstn_type == 'Registered') {
                $("#gstn_selected").show();
            } else {
                $("#gstn_selected").hide();
            }
        }).change();
        var name_empty = "Please Enter Name.";
        var name_invalid = "Please Enter Valid Name";
        var company_registered_type = "Please Select Company Registration Type";
        var name_length = "Please Enter Name Minimun 3 Character";
        var short_name_empty = "Please Enter Site Short Name.";
        var short_name_invalid = "Please Enter Valid Site Short Name";
        var short_name_length = "Please Enter Site Short Name Minimun 2 Character";
        var email_empty = "Please Enter Email.";
        var email_invalid = "Please Enter Valid Email";
        var mobile_empty = "Please Enter Mobile No.";
        var mobile_invalid = "Please Enter Valid Mobile No.";
        var mobile_length = "Please Enter 10 digit Mobile No.";
        var street_empty = "Please Enter Street.";
        var street_invalid = "Please Enter Valid Street";
        var street_length = "Please Enter Street Minimun 3 Character";
        var city_select = "Please Select City.";
        var state_select = "Please Select State.";
        var country_select = "Please Select Country.";
        var gstin_empty = "Please Enter GSTIN";
        var gstin_not_match = "Please Enter Valid GSTIN.Your GSTIN and State does not match.";
        var branch_name_empty="Please Enter Branch Name";
        
        $("#company_submit").click(function () {
            var name = $('#name').val();
            var short_name = $('#short_name').val();
            var branch_gst_registration_type = $('#branch_gst_registration_type').val();          
            var address = $('#branch_address').val();
             var registered_type = $('#registered_type').val();
            var email = $('#email').val();
            var mobile = $('#mobile').val();
            var city = $('#city').val();
            var state = $('#state').val();
            var country = $('#country').val();           
            var gstin = $('#branch_gstin_number').val();
            var state_code = $('#state_code').val();
            var gstin_state_code = gstin.slice(0, 2);
            var company_code = $('#company_code').val();
            var branch_code = $('#branch_code').val();
            var branch_name = $('#branch_name').val();
            var financial_year_password = $('#financial_year_password').val();
            if (name == null || name == "") {
                $("#err_name").text(name_empty);
                return false;
            } else {
                $("#err_name").text("");
            }
            if (!name.match(name_regex)) {
                $('#err_name').text(name_invalid);
                return false;
            } else {
                $("#err_name").text("");
            }
            if (name.length < 3) {
                $('#err_name').text(name_length);
                return false;
            } else {
                $("#err_name").text("");
            }
          //validation for company code
            if (registered_type == null || registered_type == "") {
                $("#err_registration_type").text('Please Select Company Registration Type');
                return false;
            } else {
                $("#err_registration_type").text("");
            }
            if (company_code == null || company_code == "") {
                $("#err_company_code").text('Please enter company code');
                return false;
            } else {
                $("#err_company_code").text("");
            }
            // if (branch_name == null || branch_name == "") {
            //     $("#err_branch_name").text('Please enter branch name');
            //     return false;
            // } else {
            //     $("#err_branch_name").text("");
            // }
            //name validation complite.
            // if (short_name == null || short_name == "") {
            //     $("#err_short_name").text(short_name_empty);
            //     return false;
            // } else {
            //     $("#err_short_name").text("");
            // }
            // if (!short_name.match(name_regex)) {
            //     $('#err_short_name').text(short_name_invalid);
            //     return false;
            // } else {
            //     $("#err_short_name").text("");
            // }
            // if (short_name.length < 2) {
            //     $('#err_short_name').text(short_name_length);
            //     return false;
            // } else {
            //     $("#err_short_name").text("");
            // }
            if (branch_gst_registration_type == null || branch_gst_registration_type == "") {
                $("#err_branch_gst_registration_type").text("Please Choose Registered Type");
                return false;
            } else {
                $("#err_branch_gst_registration_type").text("");
            }
            if (branch_gst_registration_type != "Unregistered") {
                if (gstin == null || gstin == "") 
                {
                    $("#err_branch_gstin_number").text("Please Enter GSTIN Number");
                    return false;
                } else 
                {
                    $("#err_branch_gstin_number").text("");
                }
                 if (gstin.length <15 || gstin.length>15) 
                 {
                    $("#err_branch_gstin_number").text("GSTIN Number should have length 15");
                    return false;
                } else 
                {
                    $("#err_branch_gstin_number").text("");
                }
                /*var res=gstin.slice(0, 2);
                var res1 = gstin.slice(13, 14);
                if (res!=state_code || res1!='Z') 
                {
                    $("#err_branch_gstin_number").text("Please enter valid GST number(Ex:29AAAAA9999AXZX).");
                    return false;
                } 
                else 
                {
                    $("#err_branch_gstin_number").text("");
                }*/
            }
          
            var companylogo = $("#logo").val();
            var hidden_logo_name = $("#hidden_logo_name").val();
            // if (companylogo == "" && hidden_logo_name=="" ) {
            //     $('#err_logo').text("Please Select Company Logo");
            //     return false;
            // } else {
            //     $('#err_logo').text("");
            // }
            if(companylogo)
            {
                var extension = companylogo.split('.').pop().toUpperCase();
                if (companylogo.length < 1) {
                    companylogook = 0;
                } else if (extension != "PNG" && extension != "JPG" && extension != "JPEG") {
                    companylogook = 0;
                    $('#err_logo').text("Invalid extension " + extension);
                    return false;
                } else {
                    companylogook = 1;
                }
            }
            if (country == "" || country == null) {
                $('#err_country').text(country_select);
                return false;
            } else {
                $('#err_country').text("");
            }
            if (state == "" || state == null) {
                $('#err_state').text(state_select);
                return false;
            } else {
                $('#err_state').text("");
            }
            //state validation copmlite.
            if (city == "" || city == null) {
                $('#err_city').text(city_select);
                return false;
            } else {
                $('#err_city').text("");
            }
          
            if (address == null || address == "") {
                $("#err_branch_address").text(" Please Enter Address");
                return false;
            } else {
                $("#err_branch_address").text("");
            }
            
          
            //stret validation complite.
            if (email == null || email == "") {
                $("#err_email").text(email_empty);
                return false;
            } else {
                $("#err_email").text("");
            }
            if (!email.match(email_regex)) {
                $('#err_email').text(email_invalid);
                return false;
            } else {
                $("#err_email").text("");
            }
            if (email.length < 2) {
                $('#err_email').text(email_length);
                return false;
            } else {
                $("#err_email").text("");
            }
            //email validation complite.
            // if (mobile == null || mobile == "") {
            //     $("#err_mobile").text(mobile_empty);
            //     return false;
            // } else {
            //     $("#err_mobile").text("");
            // }
            if(mobile.length>1)
            {
            if (!mobile.match(mobile_regex)) {
                $('#err_mobile').text(mobile_invalid);
                return false;
            } else {
                $("#err_mobile").text("");
            }
        }
            var import_export_code = $("#import_export_code").val();
            if(import_export_code)
            {
                var extension1 = import_export_code.split('.').pop().toUpperCase();
                if (import_export_code.length < 1) {
                    import_export_code_ok = 0;
                } else if (extension1 != "PNG" && extension1 != "JPG" && extension1 != "JPEG" && extension1 != "PDF" && extension1 != "doc" && extension1 != "docx" && extension1 != "xlx" && extension1 != "xlxs") {
                    import_export_code_ok = 0;
                    $('#err_import_export_code').text("Invalid extension " + extension1);
                    return false;
                } else {
                    import_export_code_ok = 1;
                }
            }
            
            var shop_establishment = $("#shop_establishment").val();
            if(shop_establishment)
            {
                var extension2 = shop_establishment.split('.').pop().toUpperCase();            
                if (shop_establishment.length < 1) {
                    shop_establishment_ok = 0;
                } else if (extension2 != "PNG" && extension2 != "JPG" && extension2 != "JPEG" && extension2 != "PDF" && extension2 != "doc" && extension2 != "docx" && extension2 != "xlx" && extension2 != "xlxs") {
                    shop_establishment_ok = 0;
                    $('#err_shop_establishment').text("Invalid extension " + extension2);
                    return false;
                } else {
                    shop_establishment_ok = 1;
                }
            }
            if (branch_code == null || branch_code == "") {
                $("#err_branch_code").text('Please enter login code');
                return false;
            } else {
                $("#err_branch_code").text("");
            }
            if (financial_year_password == null || financial_year_password == "") {
                $("#err_financial_year_password").text("Financial Year Password Required");
                return false;
            } else {
                $("#err_financial_year_password").text("");
            }
            if (financial_year_password.length < 3) {
                $('#err_financial_year_password').text("password should be greater then 4 charecter");
                return false;
            } else {
                $("#err_financial_year_password").text("");
            }
            //mobile validation complite.
        });
        $("#name").on("blur keyup", function (event) {
            var name = $('#name').val();
            if (name == null || name == "") {
                $("#err_name").text(name_empty);
                return false;
            } else {
                $("#err_name").text("");
            }
            if (!name.match(name_regex)) {
                $('#err_name').text(name_invalid);
                return false;
            } else {
                $("#err_name").text("");
            }
            if (name.length < 3) {
                $('#err_name').text(name_length);
                return false;
            } else {
                $("#err_name").text("");
            }
        });
         $("#registered_type").on("change", function (event) {
            var registered_type = $('#registered_type').val();
            if (registered_type == null || registered_type == "") {
                $("#err_registration_type").text(company_registered_type);
                return false;
            } else {
                $("#err_registration_type").text("");
            }
            if (registered_type == "Private Limited Company" || registered_type == "One Person Company" || registered_type == "Limited Liability Partnership") 
            {
                $("#hide_cin").show();
            }
            else
            {
                $("#hide_cin").hide();
                $('#cin_number').val('');
                $('#branch_roc').val('');
            }
           
        
        });
        $("#branch_name").on("blur keyup", function (event) 
        {
        
            var branch_name = $('#branch_name').val();
            if (branch_name == null || branch_name == "") {
                $("#err_branch_name").text(branch_name_empty);
                return false;
            } else {
                $("#err_branch_name").text("");
            }
        
        });
        // $("#short_name").on("blur keyup", function (event) {
       
        //     var short_name = $('#short_name').val();
        //     if (short_name == null || short_name == "") {
        //         $("#err_short_name").text(short_name_empty);
        //         return false;
        //     } else {
        //         $("#err_short_name").text("");
        //     }
        //     if (!short_name.match(name_regex)) {
        //         $('#err_short_name').text(short_name_invalid);
        //         return false;
        //     } else {
        //         $("#err_short_name").text("");
        //     }
        //     if (short_name.length < 2) {
        //         $('#err_short_name').text(short_name_length);
        //         return false;
        //     } else {
        //         $("#err_short_name").text("");
        //     }
        // });
        
        $("#address").on("blur keyup", function (event) {
            var address = $('#branch_address').val();
            if (address == null || address == "") {
                $("#err_branch_address").text(" Please Enter Address");
                return false;
            } else {
                $("#err_branch_address").text("");
            }
        });
        
        $("#email").on("blur keyup", function (event) {
  
            var email = $('#email').val();
            if (email == null || email == "") {
                $("#err_email").text(email_empty);
                return false;
            } else {
                $("#err_email").text("");
            }
            if (!email.match(email_regex)) {
                $('#err_email').text(email_invalid);
                return false;
            } else {
                $("#err_email").text("");
            }
        });
        $("#mobile").on("blur keyup", function (event) {
          
            var mobile = $('#mobile').val();
            // if (mobile == null || mobile == "") {
            //     $("#err_mobile").text(mobile_empty);
            //     return false;
            // } else {
            //     $("#err_mobile").text("");
            // }
            if(mobile.length>1)
            {
            if (!mobile.match(mobile_regex)) {
                $('#err_mobile').text(mobile_invalid);
                return false;
            } else {
                $("#err_mobile").text("");
            }
        }
          
        });
        $("#street").on("blur keyup", function (event) {
            var name_regex = /^[-a-zA-Z\s]+$/;
            var street = $('#street').val();
            if (street == null || street == "") {
                $("#err_street").text(street_empty);
                return false;
            } else {
                $("#err_street").text("");
            }
            if (!street.match(name_regex)) {
                $('#err_street').text(street_invalid);
                return false;
            } else {
                $("#err_street").text("");
            }
            if (street.length < 3) {
                $('#err_street').text(street_length);
                return false;
            } else {
                $("#err_street").text("");
            }
        });
        $("#city").change(function () {
            var city = $('#city').val();
            if (city == "" || city == null) {
                $('#err_city').text(city_select);
                return false;
            } else {
                $('#err_city').text("");
            }
        });
        $("#state").change(function () {
            var state = $('#state').val();
            if (state == "" || state == null) {
                $('#err_state').text(state_select);
                return false;
            } else {
                $('#err_state').text("");
            }
        });
        $('#country').change(function () {
            var country = $('#country').val();
            if (country == "" || country == null) {
                $('#err_country').text(country_select);
                return false;
            } else {
                $('#err_country').text("");
            }
        });
        //for company code form validation
         $("#company_code").on("blur keyup", function (event) {
          
            var company_code = $('#company_code').val();
            if (company_code == null || company_code == "") {
                $("#err_company_code").text('Please enter company code');
                return false;
            } else {
                $("#err_company_code").text("");
            }
            if (company_code.length < 3) {
                $('#err_company_code').text(name_length);
                return false;
            } else {
                $("#err_company_code").text("");
            }
        });
        //end company code form validation
        // $('#branch_gstin_number').on('keyup blur', function () 
        // {
        //     var gstin = $('#branch_gstin_number').val();
        //     var state_code = $('#state_code').val();
        //     $("#branch_gstin_number").val(gstin.toUpperCase());
        //     var gstin_state_code = gstin.slice(0, 2);
        //     if (gstin == "" || gstin == null) {
        //         $('#err_branch_gstin_number').text(gstin_empty);
        //         return false;
        //     } else 
        //     {
        //         $('#err_branch_gstin_number').text("");
        //     }
        // });
        $("#branch_gstin_number").on("blur keyup", function (event) {
            var gstin = $('#branch_gstin_number').val();
            $("#branch_gstin_number").val(gstin.toUpperCase());
            var gstin_length=gstin.length;
            if(gstin_length > 0)
            {
                if (gstin == null || gstin == "") {
                    $("#err_branch_gstin_number").text("Please Enter GSTIN Number.");
                    return false;
                } else {
                    $("#err_branch_gstin_number").text("");
                }
                if (!gstin.match(sname_regex)) {
                    $('#err_branch_gstin_number').text("Please Enter Valid GSTIN Number.");
                    return false;
                } else {
                    $("#err_branch_gstin_number").text("");
                }
                if (gstin_length < 15 || gstin_length > 15) 
                {
                    $("#err_branch_gstin_number").text("GSTIN Number should have length 15");
                    return false;
                } else {
                    $("#err_branch_gstin_number").text("");
                }
                var res = gstin.slice(2, 13);
                $('#pan_number').val(res);
            }
            else
            {
                $('#pan_number').val('');
            }
        });
    $('#logo').change(function () {
        var filePath=$('#logo').val();
        if(filePath!="" || filePath!=null)
        {
            document.getElementById("logo_pic").innerHTML = "";
        }
    });
    $('#import_export_code').change(function () {
        var filePath1=$('#import_export_code').val();
        if(filePath1!="" || filePath1!=null)
        {
            document.getElementById("iec_file").innerHTML = "";
        }
    });
    $('#shop_establishment').change(function () {
        var filePath2=$('#shop_establishment').val();
        if(filePath2!="" || filePath2!=null)
        {
            document.getElementById("shop_file").innerHTML = "";
        }
    });
 
});
</script>