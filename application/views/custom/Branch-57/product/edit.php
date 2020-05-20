<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li>
                <a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li>
                <a href="<?php echo base_url('product'); ?>">Product</a>
            </li>
            <li class="active">
                Add Product
            </li>
        </ol>
    </div>
    <form name="form" id="form" method="post" action="<?php echo base_url('product/edit_product'); ?>" encType="multipart/form-data">
        <input type="hidden" name="section_area" value="product">
        <section class="content mt-50">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Edit Product</h3>
                            <a class="btn btn-default pull-right back_button" id="cancel" onclick1="cancel('product')">Back</a>
                        </div>
                        <div class="box-body">
                            <div class="well">                          
                                <div class="row mb-0">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_code">Article<span class="validation-color">*</span></label>
                                            <input type="hidden" name="section_area" value="edit_product">
                                            <input type="hidden" name="product_id" id="product_id" value="<?php echo $data[0]->product_id; ?>">
                                            <input type="text" class="form-control product_code_edit" tabindex="1" id="product_code" name="product_code" value="<?php echo $data[0]->product_code; ?>" <?php
                                            if ($access_settings[0]->invoice_readonly == 'yes') {
                                                /*echo "readonly";*/
                                            }
                                            ?>>
                                            <span class="validation-color" id="err_product_code"><?php echo form_error('product_code'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_name_edit">Product Name <span class="validation-color">*</span></label>
                                             <div class="input-group">
                                        <div class="input-group-addon">
                                           <a href="" data-toggle="modal" data-target="#master_product_modal" data-name="product" class="pull-right">+</a>
                                       </div>
                                        <select name="product_name_edit" id="product_name_edit" class="form-control select2" >
                                            <option value="">Select Product</option>
                                            <?php                                            
                                            foreach ($product_master as $value) {
                                                if($data[0]->product_name == $value->master_product_name){
                                                    echo '<option value="'.$value->master_product_name.'" selected>'.$value->master_product_name.'</option>';
                                                }else{
                                                echo '<option value="'.$value->master_product_name.'">'.$value->master_product_name.'</option>';
                                                }
                                            }
                                            ?>
                                        </select></div>
                                            <span class="validation-color" id="err_product_name"><?php echo form_error('product_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 disable_div" >
                                        <div class="form-group">
                                            <label for="product_code">Do we have Varient<span class="validation-color">*</span></label>
                                            <div class="form-group">
                                                <?php
                                                $yes_varient = '';
                                                $no_varient = '';
                                                if ($data[0]->is_varients == 'Y') {
                                                    $yes_varient = 'checked="checked"';
                                                } else {
                                                    $no_varient = 'checked="checked"';
                                                }
                                                ?>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="varient" id="varient1" value="Y" <?php echo $yes_varient; ?>>
                                                        Yes </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="varient" id="varient2" value="N" <?php echo $no_varient; ?>>
                                                        No </label>
                                                </div>
                                            </div>
                                            <span class="validation-color" id="err_product_code"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_code">Is it an asset..?<span class="validation-color">*</span></label>
                                            <div class="form-group">
                                                <?php
                                                $yes_asset = '';
                                                $no_asset = '';
                                                if ($data[0]->is_assets == 'Y') {
                                                    $yes_asset = 'checked="checked"';
                                                } else {
                                                    $no_asset = 'checked="checked"';
                                                }
                                                ?>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="asset" id="asset1" value="Y" <?php echo $yes_asset; ?>>
                                                        Yes </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="asset" id="asset2" value="N" <?php echo $no_asset; ?>>
                                                        No </label>
                                                </div>
                                            </div>
                                            <span class="validation-color" id="err_product_code"></span>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin-top:0" />
                                <div class="row">
                                    <div class="col-md-3 produc_type">
                                        <div class="form-group">
                                            <?php
                                            $product_type_list = array('rawmaterial' => "Raw Material", 'semifinishedgoods' => "Semi Finished Goods", 'finishedgoods' => "Finished Goods");
                                            $product_type = $data[0]->product_type;
                                            ?>
                                            <label for="product_type">Product Type<span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="product_type" name="product_type">
                                                <option value="">Select Product Type</option>
                                                <?php
                                                foreach ($product_type_list as $key => $value) {
                                                    if ($product_type == $key) {
                                                        echo '<option value="' . $key . '" selected>' . $value . '</option>';
                                                    } else {
                                                        echo '<option value="' . $key . '">' . $value . '</option>';
                                                    }
                                                }
                                                ?>                                              
                                            </select>
                                            <span class="validation-color" id="err_product_type"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 produc_hsn">
                                        <div class="form-group">
                                            <label for="product_hsn_sac_code">Product HSN Code </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#hsn_modal" class="pull-right"><span class="fa fa-eye"></span></a>
                                                </div>
                                                <input type="text" class="form-control" id="product_hsn_sac_code" name="product_hsn_sac_code" value="<?php echo $data[0]->product_hsn_sac_code; ?>" tabindex="11">
                                            </div>
                                            <span class="validation-color" id="err_product_hsn_sac_code"><?php echo form_error('product_hsn_sac_code'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 product_category">
                                        <div class="form-group">
                                            <input type="hidden" class="form-control" id="c_type" name="c_type" value="product">
                                            <label for="product_category">Category <span class="validation-color">*</span></label>
                                            <div class="input-group">
                                                <?php
                                                if (in_array($category_module_id, $active_add)) {
                                                    ?>
                                                    <div class="input-group-addon">
                                                        <a href="" data-toggle="modal" data-target="#category_modal" data-name="product" class="pull-right new_category">+</a>
                                                    </div>
                                                <?php } ?>
                                                <select class="form-control select2" id="product_category" name="product_category" style="width: 100%;" tabindex="5">
                                                    <option value="">Select</option>
                                                    <?php
                                                    foreach ($product_category as $cat) {
                                                        ?>
                                                        <option value='<?php echo $cat->category_id ?>' <?php
                                                        if ($cat->category_id == $data[0]->product_category_id) {
                                                            echo "selected";
                                                        }
                                                        ?>><?php echo $cat->category_name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <span class="validation-color" id="err_product_category"><?php echo form_error('product_category'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 product_subcategory">
                                        <div class="form-group">
                                            <label for="product_subcategory">Subcategory</label>
                                            <div class="input-group">       
                                                <?php
                                                if (in_array($subcategory_module_id, $active_add)) {
                                                    ?>
                                                    <div class="input-group-addon">
                                                        <a href="" data-toggle="modal" data-target="#subcategory_modal" data-name="product" class="pull-right new_subcategory">+</a>
                                                    </div>
                                                <?php } ?>
                                                <select class="form-control select2" id="product_subcategory" name="product_subcategory" style="width: 100%;" tabindex="6">
                                                    <option value="">Select</option>
                                                    <?php
                                                    foreach ($product_subcategory as $sub) {
                                                        ?>
                                                        <option value='<?php echo $sub->sub_category_id ?>' <?php
                                                        if ($sub->sub_category_id == $data[0]->product_subcategory_id) {
                                                            echo "selected";
                                                        }
                                                        ?>><?php echo $sub->sub_category_name ?></option>
                                                    <?php } ?>
                                                </select></div>
                                            <span class="validation-color" id="err_product_subcategory"><?php echo form_error('product_subcategory'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_batch">Batch<span class="validation-color">*</span></label>
                                            <input type="text" class="form-control" name="product_batch" id="product_batch" value="<?php echo $data[0]->product_batch;?>" >
                                            <span class="validation-color" id="err_product_batch"><?php echo form_error('product_batch'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 product_uom">
                                        <div class="form-group">
                                            <label for="product_unit">Unit of Measurement<span class="validation-color">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <a href="#" data-toggle="modal" data-name="product" data-target="#uom_modal" class="pull-right new_tax" title="Add UOM">+</a>
                                                </div>
                                                <select class="form-control select2" id="product_unit" name="product_unit"  tabindex="4">
                                                    <option value="">Select UOM</option>
                                                    <?php
                                                    foreach ($uqc as $value) {
                                                        if ($value->id == $data[0]->product_unit_id) {
                                                            echo "<option value='$value->id' selected>$value->uom - $value->description</option>";
                                                        } else {
                                                            echo "<option value='$value->id'>$value->uom - $value->description</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <span class="validation-color" id="err_product_unit"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_sku">SKU </label>
                                            <input type="text" class="form-control" id="product_sku" name="product_sku" value="<?php echo $data[0]->product_sku; ?>" >
                                            <span class="validation-color" id="product_sku_code"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_mrp">MRP<span class="validation-color">*</span></label>
                                            <input type="number" class="form-control" id="product_mrp" name="product_mrp" value="<?php echo precise_amount($data[0]->product_mrp_price,2); ?>" >
                                            <span class="validation-color" id="err_product_mrp"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 product_discount">
                                        <div class="form-group">
                                            <label for="markdown_discount">Markdown Discount<!-- <span class="validation-color">*</span> --></label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-name="product" data-target="#add_discount_product" class="pull-right addDiscount" data-option="markdown_discount" title="Add discount">+</a>
                                                </div>
                                                <select class="form-control select2" id="markdown_discount" name="product_discount"  tabindex="4">
                                                    <option value="">Select Discount</option>
                                                    <?php
                                                    foreach ($discount as $value) {
                                                        if ($value->discount_value == $data[0]->product_discount_value) {
                                                            echo "<option value='$value->discount_id' selected dic='".(float)$value->discount_value."'>".(float)$value->discount_value."%</option>";
                                                        } else {
                                                            echo "<option value='$value->discount_id' dic='".(float)$value->discount_value."'>".(float)$value->discount_value."%</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <input type="hidden" name="product_discount_value">
                                            </div>
                                            <span class="validation-color" id="err_product_discount"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 product_discount">
                                        <div class="form-group">
                                            <label for="margin_discount">Marginal Discount<!-- <span class="validation-color">*</span> --></label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-name="product" data-target="#add_discount_product" class="pull-right addDiscount" data-option="margin_discount" title="Add discount">+</a>
                                                </div>
                                                <select class="form-control select2" id="margin_discount" name="margin_discount"  tabindex="4">
                                                    <option value="">Select Discount</option>
                                                    <?php
                                                    foreach ($discount as $value) {
                                                        if ($value->discount_value == $data[0]->margin_discount_value) {
                                                            echo "<option value='$value->discount_id' selected dic='".(float)$value->discount_value."'>".(float)$value->discount_value."%</option>";
                                                        } else {
                                                            echo "<option value='$value->discount_id' dic='".(float)$value->discount_value."'>".(float)$value->discount_value."%</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <input type="hidden" name="margin_discount_value">
                                            </div>
                                            <span class="validation-color" id="err_margin_discount"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 product_gst" >
                                        <div class="form-group">
                                            <label for="gst_tax_product">Tax(GST Output)</label>
                                            <div class="input-group"><?php
                                                if (in_array($tax_module_id, $active_add)) { ?>
                                                    <div class="input-group-addon">
                                                        <a href="" data-toggle="modal" data-target="#tax_gst_modal" data-name="product" class="pull-right new_tax">+</a>
                                                    </div>
                                                <?php } ?>
                                                <select class="form-control select2" id="gst_tax_product" name="gst_tax_product" style="width: 100%;" tabindex="6">
                                                    <option value="">Select Tax</option>
                                                    <?php
                                                    foreach ($tax_gst as $row) {
                                                        if ($row->tax_id == $data[0]->product_gst_id) {
                                                            echo "<option value='$row->tax_id' selected  per='".(float)($row->tax_value)."'>$row->tax_name@" . round($row->tax_value, 2) . "%</option>";
                                                        } else {
                                                            echo "<option value='$row->tax_id' per='".(float)($row->tax_value)."'>$row->tax_name@" . round($row->tax_value, 2) . "%</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <span class="validation-color" id="err_product_code"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_gst_code">GST Tax Percentage </label>
                                            <input type="text" class="form-control" id="product_gst_code" name="product_gst_code" value="<?php echo round($data[0]->product_gst_value, 2); ?>" readonly>
                                            <span class="validation-color" id="product_gst_code"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_selling_price">Selling Price</label>
                                            <input type="number" class="form-control" id="product_selling_price" name="product_selling_price" value="<?php echo precise_amount($data[0]->product_selling_price); ?>" readonly>
                                            <span class="validation-color" id="product_selling"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_basic_price">Basic Price</label>
                                            <input type="number" class="form-control" id="product_basic_price" name="product_basic_price" value="<?php echo precise_amount($data[0]->product_basic_price); ?>" readonly>
                                            <span class="validation-color" id="product_selling"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 product_tcs">
                                        <div class="form-group">
                                            <label for="tds_tax_product">Tax(TCS)</label>
                                            <div class="input-group"><?php
                                                if (in_array($tax_module_id, $active_add)) {
                                                    ?>
                                                    <div class="input-group-addon">
                                                        <a href="" data-toggle="modal" data-target="#tax_tds_modal" data-name="product" class="pull-right new_tax">+</a>
                                                    </div>
                                                <?php } ?>
                                                <select class="form-control select2" id="tds_tax_product" name="tds_tax_product" style="width: 100%;" tabindex="6">
                                                    <option value="">Select Tax</option>
                                                    <?php
                                                    foreach ($tax_tds as $row) {
                                                        if ($row->tax_id == $data[0]->product_tds_id) {
                                                            echo "<option value='$row->tax_id' selected>$row->tax_name(Sec " . $row->section_name . ") @ " . round($row->tax_value, 2) . "%</option>";
                                                        } else {
                                                            echo "<option value='$row->tax_id'>$row->tax_name(Sec " . $row->section_name . ") @ " . round($row->tax_value, 2) . "%</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <span class="validation-color" id="err_product_code"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_tds_code">TCS Tax Percentage </label>
                                            <input type="text" class="form-control" id="product_tds_code" name="product_tds_code" value="<?php echo round($data[0]->product_tds_value, 2); ?>%" readonly>
                                            <span class="validation-color" id="product_tds_code"></span>
                                            <input id="subcategory_hidden" name="subcategory_hidden" type="hidden">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_mrp">Serial Number</label>
                                            <input type="text" class="form-control" id="product_serial" name="product_serial" value="<?php echo $data[0]->product_serail_no; ?>" >
                                            <span class="validation-color" id="product_mrp_code"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_mrp">Product Image </label>
                                            <?php
                                            if (!isset($data[0]->product_image) || $data[0]->product_image == "") {
                                                ?>
                                                <input type="file" class="form-control" id="product_image" name="product_image" >
                                            <?php } ?>
                                            <div id="logo_pic">
                                                <?php
                                                if (isset($data[0]->product_image) && $data[0]->product_image) {
                                                    ?>
                                                    <img src="<?php echo base_url('assets/product_image/') . $data[0]->branch_id . '/' . $data[0]->product_image; ?>" width="18%" id="product_img">
                                                    <a href="" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#remove_logo" title="Remove Product Image" ><i class="fa fa-trash-o text-purple"></i></a>
                                                <?php } ?>
                                            </div>
                                            <input type="hidden" id="hidden_product_image" name="hidden_product_image" value="<?php
                                            if (isset($data[0]->product_image)) {
                                                echo $data[0]->product_image;
                                            }
                                            ?>">
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_brand">Product Brand<span class="validation-color">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#brand_popup" data-name="product" class="pull-right">+</a>
                                                </div>
                                                <select class="form-control select2" id="product_brand" name="product_brand">
                                                    <option value="">Select Brand</option>
                                                    <?php if(!empty($brand)){
                                                        foreach ($brand as $key => $value) { ?>
                                                            <option value="<?=$value->brand_id;?>" <?=($data[0]->brand_id == $value->brand_id ? 'selected' : '');?>><?=$value->brand_name;?></option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                            <span class="validation-color" id="err_product_brand"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="exp_date">Expiry date</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control datepicker" id="exp_date" name="exp_date" value="<?=($data[0]->exp_date != '0000-00-00' ? date('d-m-Y', strtotime($data[0]->exp_date)) : '');?>">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div> 
                                            <span class="validation-color" id="err_exp_date"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="mfg_date">Manufacture date</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control datepicker" id="mfg_date" name="mfg_date" value="<?=($data[0]->mfg_date != '0000-00-00' ? date('d-m-Y', strtotime($data[0]->mfg_date)) : '');?>">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div> 
                                            <span class="validation-color" id="err_mfg_date"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="product_price">Purchase Price</label>
                                            <input type="number" class="form-control" id="product_price" name="product_price" value="<?php echo precise_amount($data[0]->product_price); ?>" >
                                            <span class="validation-color" id="product_price"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="product_description">Description</label>
                                            <textarea type="text" name="product_description" id="product_description" class="form-control" rows="3"><?php echo $data[0]->product_details; ?></textarea>
                                            <span class="validation-color" id="err_product_description"></span>
                                        </div>
                                    </div>
                                </div>
                                <div id="varients-yes">
                                    <div class="box-header with-border">
                                        <h3 class="box-title ml-0">Add Varient</h3>
                                    </div>                                    
                                    <div class="row mt-15">
                                        <div class="col-sm-6">
                                            <?php
                                            $var_i = 1;
                                            foreach ($varient_key_value as $key => $value) {
                                                $value_dropdown = $varient_value_dropdown[$key];
                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Varient Key</label>
                                                            <select class="form-control select2 key_varient" id="varient_key_<?php echo $var_i; ?>" name="varient_key[]" style="width: 100%;">
                                                                <option value="">Select</option>
                                                                <?php
                                                                foreach ($varients_key as $row) {
                                                                    if ($key == $row->varients_id) {
                                                                        echo "<option value='$row->varients_id' selected>$row->varient_key</option>";
                                                                    } else {
                                                                        echo "<option value='$row->varients_id'>$row->varient_key</option>";
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Varient Value</label>
                                                            <select multiple="" class="form-control select2 value_varient" id="varient_value_<?php echo $var_i; ?>" name="varient_value_<?php echo $var_i; ?>[]" style="width: 100%;">
                                                                <?php
                                                                foreach ($value_dropdown as $valRow) {
                                                                    if (in_array($valRow->varients_value_id, $value)) {
                                                                        echo "<option value='$valRow->varients_value_id' selected>$valRow->varients_value</option>";
                                                                    } else {
                                                                        echo "<option value='$valRow->varients_value_id'>$valRow->varients_value</option>";
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>                                           
                                                </div>
                                                <?php
                                                $var_i++;
                                            }
                                            ?>
                                        </div>
                                        <div class="col-sm-6 mt-25">
                                            <button type="button"  class="btn btn-info pull-right"  name="create_combination" id="create_combination">Create Combinations</button>
                                        </div>
                                    </div>                                       
                                    <div id="app_row"></div>
                                    <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                                        <thead>
                                        <th>Article</th>
                                        <th>Product Name with Var</th>
                                        <th>Action</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="box-footer">
                                    <button type="submit" id="product_modal_edit" class="btn btn-info">
                                        Update
                                    </button>
                                    <span class="btn btn-default" id="cancel" onclick="cancel('product')">Cancel</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>
</div>
<div id="remove_logo" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <form id="logoForm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Remove Product Image</h4>
                </div>
                <div class="modal-body">
                    <label for="category_name">Are your sure! You want to remove Product Image ?</label>
                </div>
                <div class="modal-footer">
                    <a href="<?php echo base_url('product/remove_image/' . $data[0]->product_id); ?>" class="btn btn-sm btn-info pull-right" style="margin-left: 10px;">Yes</a>
                    <button class="btn btn-sm btn-submit pull-right" data-dismiss="modal">No</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('product/hsn_modal');
$this->load->view('category/category_modal');
$this->load->view('subcategory/subcategory_modal');
$this->load->view('uqc/uom_modal');
$this->load->view('discount/discount_modal_product');
$this->load->view('brand/brand_popup');
//$this->load->view('service/tds_modal');
if (in_array($tax_module_id, $active_add)) {
    $this->load->view('tax/tax_modal_tds');
    $this->load->view('tax/tax_modal_gst');
}
//$this->load->view('service/hsn_modal');
if (in_array($category_module_id, $active_add)) {
    $this->load->view('category/category_modal');
}
if (in_array($subcategory_module_id, $active_add)) {
    $this->load->view('subcategory/subcategory_modal');
}
//$this -> load -> view('product/tds_modal');
$this->load->view('product/edit_master_product_modal');
?>
<script type="text/javascript">
    $(document).ready(function () {
<?php
if ($data[0]->is_varients == 'Y') {
    ?>
            var id = <?php echo $data[0]->product_id; ?>;
            var comp_table = $('#list_datatable').DataTable({
                'ajax': {
                    url: base_url + 'product/fetch_combination',
                    type: 'post',
                    data: {'id': id},
                },
                'columns': [
                    {'data': 'product_code'},
                    {'data': 'name'},
                    {'data': 'action'},
                ],
                "columnDefs": [
                    {"visible": true, "targets": 0}
                ]
            });
            $('.product_tcs').addClass('disable_div');
            $('.product_gst').addClass('disable_div');
            $('.product_uom').addClass('disable_div');
            $('.product_category').addClass('disable_div');
            $('.product_subcategory').addClass('disable_div');
            $('.produc_hsn').addClass('disable_div');
            $('.produc_type').addClass('disable_div');
            $('.product_name').addClass('disable_div');
    <?php
}
?>
        i = 1;
        $(document).on("click", "#add_new_row", function () {
            $("#app_row").append('<div class="row mt-15" data-id="row_' + i + '"><div class="col-md-12 filter"><ul class="pull-left"><li> <label>Varient Key' + i + '</label> <select class="form-control select2 key_varient" id="varient_key_1" name="varient_key[]" style="width: 100%;"><option value="">Select</option></select></li><li> <label>Varient Key' + (i + 1) + '</label> <select multiple="" class="form-control select2 value_varient" id="varient_value_2" name="varient_value[]" style="width: 100%;"></select></li><li class="auto-wid"> <button type="submit" id="" class="btn btn-info"> Save </button></li><li class="auto-wid"> <a href="javascript:void(0);" id="remove_row" class="btn btn-info" title="Remove Row"> - </a></li></ul></div></div>');
            i = i + 2;
        });
        $(document).on('click', '#remove_row', function () {
            $(this).closest('.row').fadeOut(200);
        });
        var radioValue = $("input[name='varient']:checked").val();
        if (radioValue == 'N') {
            $("#varients-yes").slideUp(400);
            $("#varients-no").slideDown(400);
        }
        if (radioValue == 'Y') {
            $("#varients-no").slideUp(400);
            $("#varients-yes").slideDown(400);
        }
        $("input[name$='varient']").click(function () {
            var radioValue = $("input[name='varient']:checked").val();
            if (radioValue == 'N') {
                $("#varients-yes").slideUp(400);
                $("#varients-no").slideDown(400);
            }
            if (radioValue == 'Y') {
                $("#varients-no").slideUp(400);
                $("#varients-yes").slideDown(400);
            }
        });
        $('#category_type').html("<option value='product'>Product</option>");
        $.ajax({
            url: base_url + 'purchase/get_product_code',
            type: 'POST',
            success: function (data) {
                var parsedJson = $.parseJSON(data);
                var product_code = parsedJson.product_code;
                $("#code").val(product_code);
                $(".modal-body #category_type").val('product');
                // $(".modal-body .type_div").hide();
            }
        });
        $(document).on('change', '.key_varient', function (event)
        {
            // alert(($('.get_num select').length)/2);
            var id = $(this).attr('id');
            var id_index = id.split("_");//get the x value
            var value = $(this).val();
            $.ajax({
                url: base_url + 'varients/get_varient_value',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'varient_id': value
                },
                success: function (result)
                {
                    var data = result;
                    $('#varient_value_' + id_index[2]).html('');
                    for (i = 0; i < data.length; i++)
                    {
                        $('#varient_value_' + id_index[2]).append('<option value="' + data[i].varients_value_id + '">' + data[i].varients_value + '</option>');
                    }
                }
            });
        });
        $("#create_combination").click(function (event) {
            var arr = [];
            var arr1 = [];
            //var i = 0;
            $.each($(".value_varient"), function () {
                var id = $(this).attr('id');
                var val = $('#' + id).val();
                arr1.push(val);
            });
            var key2 = document.getElementsByName('varient_key[]');
            for (var i = 0; i < key2.length; i++) {
                var inp1 = key2[i];
                arr.push(inp1.value);
            }
            var jsonvalue = JSON.stringify(arr);
            var jsonkey = JSON.stringify(arr1);
            var product_code = $('#product_code').val();
            var product_id = $('#product_id').val();
            //comp_table.empty();
            comp_table.destroy();
            comp_table = $('#list_datatable').DataTable({
                'ajax': {
                    url: base_url + 'product/edit_combinations',
                    type: 'post',
                    data: {'value': jsonvalue, 'key': jsonkey, 'product_code': product_code, 'product_id': product_id},
                },
                'paging': true,
                'searching': true,
                "bStateSave": true,
                'ordering': false,
                'columns': [
                    {'data': 'product_code'},
                    {'data': 'name'},
                    {'data': 'action'}
                ]
            });
        });
        var combination_id = [];
        $.each($("input[name='combination']:checked"), function () {
            combination_id.push($(this).val());
        });
    });
</script>
<script src="<?php echo base_url('assets/custom/branch-'.$this->session->userdata('SESS_BRANCH_ID').'/js/product/') ?>product.js"></script>
<script src="<?php echo base_url('assets/js/product/') ?>product_mrp.js"></script>