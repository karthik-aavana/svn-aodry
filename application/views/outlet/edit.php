<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php
                echo base_url('auth/dashboard');
                ?>"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li><a href="<?php
                echo base_url('outlet');
                ?>">Outlet</a>
            </li>
            <li class="active">Edit Outlet</li>
        </ol>
    </div>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Outlet</h3>
                        <a class="btn btn-sm btn-default pull-right back_button" href1="<?php echo base_url('outlet'); ?>">Back</a>
                    </div>
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('outlet/edit_outlet'); ?>">
                            <div class="row">
                                <input type="hidden" name="outlet_id" value="<?php echo $data[0]->outlet_id;?>">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="date">Invoice Date<span class="validation-color">*</span>
                                        </label>
                                        <!-- <input type="text" class="form-control" id="invoice_date" name="invoice_date" value="<?php
                                        echo $data[0]->outlet_date;
                                        ?>"> -->
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?php echo date('d-m-Y', strtotime($data[0]->outlet_date)); ?>">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                        <input type="hidden" id="date_old" name="date_old" value="<?php
                                        echo $data[0]->outlet_date;
                                        ?>">
                                        <input type="hidden" id="added_date" name="added_date" value="<?php
                                        echo $data[0]->added_date;
                                        ?>">
                                        <input type="hidden" id="added_user_id" name="added_user_id" value="<?php
                                        echo $data[0]->added_user_id;
                                        ?>">
                                         <span class="validation-color" id="err_date"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="invoice_number">Invoice Number<span class="validation-color">*</span>
                                        </label>
                                        <?php ?>
                                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php
                                        echo $data[0]->outlet_invoice_number;
                                        ?>" <?php
                                                if ($access_settings[0]->invoice_readonly == 'yes') {
                                                   echo "readonly";
                                                }
                                               ?>> <span class="validation-color" id="err_invoice_number"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="to_branch_id">Branch <span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="to_branch_id" name="to_branch_id" readonly>
                                            <?php
                                            foreach ($branches as $row) {
                                                if ($row->branch_id == $data[0]->to_branch_id) {
                                                    echo "<option value='$row->branch_id'>$row->branch_name</option>";
                                                }
                                            } ?>
                                        </select> <span class="validation-color" id="err_branch"><?php
                                            echo form_error('branch');
                                            ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3" style='display: none;'>
                                    <div class="form-group">
                                        <label for="type_of_supply">Billing Country <span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="billing_country" name="billing_country">
                                            <?php foreach ($country as $key => $value) { ?>
                                                <option value="<?= $value->country_id ?>" <?= ($value->country_id == $data[0]->outlet_billing_country_id ? "selected='selected'" : ''); ?> >
                                                    <?= $value->country_name ?></option>
                                            <?php } ?>
                                        </select> <span class="validation-color" id="err_billing_country"><?= form_error('billing_country'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="type_of_supply">Place of Supply<span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="billing_state" name="billing_state" readonly>
                                            <?php
                                            /* if ($data[0]->outlet_billing_country_id == $branch[0]->branch_country_id) { */
                                            foreach ($state as $key => $value) {
                                                ?>
                                                <option value="<?= $value->state_id ?>" <?= ( $value->state_id == $data[0]->outlet_billing_state_id ? "selected='selected'" : ''); ?> utgst='<?= $value->is_utgst; ?>'>
                                                    <?= $value->state_name ?></option>
                                                <?php
                                            }
                                            /* } */
                                            ?>
                                            <option value="0" <?= ( $data[0]->outlet_billing_state_id == 0 ? 'selected' : '') ?>>Out of Country</option>
                                        </select> <span class="validation-color" id="err_billing_state"><?php
                                            echo form_error('billing_state');
                                            ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="type_of_supply">Type of Supply <span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="type_of_supply" name="type_of_supply" readonly>
                                            <?php if ($data[0]->outlet_type_of_supply == 'regular') { ?>
                                                <option value="regular" selected="selected">Regular</option>
                                            <?php } else { ?>
                                                <option value="export_with_payment" <?php
                                                if ($data[0]->outlet_type_of_supply == 'export_with_payment') {
                                                    echo "selected";
                                                }
                                                ?>>Export (With Payment)</option>
                                                <option value="export_without_payment" <?php
                                                if ($data[0]->outlet_type_of_supply == 'export_without_payment') {
                                                    echo "selected";
                                                }
                                                ?>>Export (Without Payment)</option>
                                            <?php } ?>
                                        </select> <span class="validation-color" id="err_type_supply"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3 gst_payable_div" style="<?=($data[0]->outlet_type_of_supply != 'regular' ? 'display:none;' : '');?>">
                                    <div class="form-group">
                                        <label for="gst_payable">GST Payable(Reverse Charge) <span class="validation-color">*</span>
                                        </label>
                                        <br/>
                                        <label class="radio-inline">
                                            <input type="radio" name="gst_payable" value="yes" class="minimal" <?php
                                            if ($data[0]->outlet_gst_payable == "yes") {
                                                echo 'checked="checked"';
                                            }
                                            ?> /> Yes</label>
                                        <label class="radio-inline">
                                            <input type="radio" name="gst_payable" value="no" class="minimal" <?php
                                            if ($data[0]->outlet_gst_payable == "no") {
                                                echo 'checked="checked"';
                                            }
                                            ?> /> No</label>
                                        <br/> <span class="validation-color" id="err_gst_payable"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3" style="display: none">
                                    <div class="form-group">
                                        <label for="currency_id">Billing Currency <span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="currency_id" name="currency_id">
                                            <?php
                                            foreach ($currency as $key => $value) {
                                                if ($value->currency_id == $data[0]->currency_id) {
                                                    echo "<option value='" . $value->currency_id . "' selected>" . $value->currency_name.' - ' . $value->country_shortname. "</option>";
                                                } else {
                                                    echo "<option value='" . $value->currency_id . "'>" . $value->currency_name . ' - ' . $value->country_shortname."</option>";
                                                }
                                            }
                                            ?></select> <span class="validation-color" id="err_currency_id"></span>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if (in_array($transporter_sub_module_id, $access_sub_modules)) {
                                $this->load->view('sub_modules/transporter_sub_module');
                            } if (in_array($shipping_sub_module_id, $access_sub_modules)) {
                                $this->load->view('sub_modules/shipping_sub_module');
                            }
                            ?>
                            <div class="row mt-15">
                                <div class="col-sm-12"> <span class="validation-color" id="err_product"></span>
                                </div>
                                <?php
                                $product_modal = 0;
                                $item_modal = 0; ?>
                                <div class="col-sm-12 search_outlet_code">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <?php
                                            if (in_array($product_module_id, $active_add)) {
                                                if ($inventory_access == "yes") {
                                                    $product_inventory_modal = 1;
                                                    ?> <a href="" data-toggle="modal" data-target="#product_inventory_modal" class="open_product_modal pull-left">+</a>
                                                    <?php
                                                } else {
                                                    $product_modal = 1;
                                                    ?> <a href="" data-toggle="modal" data-target="#product_modal" class="open_product_modal pull-left">+</a>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <input id="input_outlet_code" class="form-control" type="text" name="input_outlet_code" placeholder="Enter Product Code/Name">
                                    </div>
                                </div>
                                <div class="col-sm-12"> <span class="validation-color" id="err_outlet_code"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="box-header">
                                        <h3 class="box-title ml-0">Inventory Items</h3>
                                        <table class="table table-striped table-bordered table-condensed table-hover outlet_table table-responsive" id="outlet_data">
                                            <thead>
                                                <tr>
                                                    <th width="2%">
                                                        <img src="<?= base_url(); ?>assets/images/bin1.png" />
                                                    </th>
                                                    <th class="span2">Items</th>
                                                    <?php if ($access_settings[0]->description_visible == 'yes') { ?>
                                                        <th class="span2">Description</th>
                                                    <?php } ?>
                                                    <th class="span2" width="6%">Quantity</th>
                                                    <th class="span2" width="8%">Rate</th>
                                                    <?php if ($access_settings[0]->discount_visible == 'yes') { ?>
                                                        <th class="span2" width="8%">Discount</th>
                                                    <?php } ?>
                                                    <?php
                                                    if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') {
                                                        if ($access_settings[0]->discount_visible == 'yes') {
                                                            ?>
                                                            <th class="span2" width="11%">Taxable Value</th>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    <?php if ($access_settings[0]->tds_visible == 'yes') { ?>
                                                        <th class="span2" width="9%">TDS/TCS(%)</th>
                                                    <?php } ?>
                                                    <?php if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') { ?>
                                                        <th class="span2" width="9%">GST(%)</th>
                                                        <th class="span2" width="9%">Cess(%)</th>
                                                    <?php } ?>
                                                    <th class="span2" width="12%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="outlet_table_body">
                                                <?php
                                                $i = 0;
                                                $tot = 0;
                                                $txtAry = array();
                                                foreach ($items as $key) {
                                                    ?>
                                                    <tr id="<?= $i ?>">
                                                        <td><a class='deleteRow'> <img src='<?= base_url(); ?>assets/images/bin_close.png' /></a>
                                                            <input type='hidden' name='item_key_value' value="<?= $i ?>">
                                                            <input type='hidden' name='item_id' value="<?= $key->item_id; ?>">
                                                            <input type='hidden' name='item_type' value="product">
                                                        </td>
                                                        <td>
                                                            <input type='hidden' name='item_code' value='<?php
                                                            echo $key->product_code;
                                                            ?>'>
                                                            <?php echo $key->product_name; ?>
                                                            <br>(P) (HSN/SAC: 
                                                            <?php echo $key->product_hsn_sac_code; ?>)
                                                            <br>
                                                            <?php echo $key->product_batch; ?>
                                                        </td>
                                                        <?php if ($access_settings[0]->description_visible == 'yes') { ?>
                                                        <td>
                                                            <input type='text' class='form-control form-fixer' name='item_description' value='<?= $key->outlet_item_description; ?>'>
                                                        </td>
                                                        <?php } ?>
                                                        <td>
                                                            <input type='text' class='form-control form-fixer text-center float_number' value='<?php
                                                            echo $key->outlet_item_quantity ? $key->outlet_item_quantity : 0;
                                                            ?>' data-rule='quantity' name='item_quantity'>
                                                        </td>
                                                        <td>
                                                            <input type='text' class='form-control form-fixer text-right float_number' name='item_price' value='<?php
                                                            echo $key->outlet_item_unit_price ? precise_amount($key->outlet_item_unit_price) : 0;
                                                            ?>'><span id='item_sub_total_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?php
                                                                   echo $key->outlet_item_sub_total ? precise_amount($key->outlet_item_sub_total) : 0;
                                                                   ?></span>
                                                            <input type='hidden' class='form-control form-fixer text-right' style='' value='<?php
                                                            echo $key->outlet_item_sub_total ? precise_amount($key->outlet_item_sub_total) : 0;
                                                            ?>' name='item_sub_total'>
                                                        </td>
                                                        <?php if ($access_settings[0]->discount_visible == 'yes') { ?>
                                                        <td>
                                                            <input type='hidden' name='item_discount_id' value='<?php
                                                            echo $key->outlet_item_discount_id ? $key->outlet_item_discount_id : 0;
                                                            ?>'>
                                                            <input type='hidden' name='item_discount_percentage' value='<?php
                                                            echo $key->item_discount_percentage ? precise_amount($key->item_discount_percentage) : 0;
                                                            ?>'>
                                                            <input type='hidden' name='item_discount_amount' value='<?= ($key->outlet_item_discount_amount ? precise_amount($key->outlet_item_discount_amount) : 0); ?>'>
                                                            <div class="form-group" style="margin-bottom:0px !important;">
                                                                <select class="form-control open_discount form-fixer select2" name="item_discount">
                                                                    <option value="">Select</option>
                                                                    <?php
                                                                    foreach ($discount as $key3 => $value3) {
                                                                        if ($value3->discount_id == $key->outlet_item_discount_id) {
                                                                            echo "
                                                                <option value='" . $value3->discount_id . "-" . (float) ($value3->discount_value) . "' selected>" . (float) ($value3->discount_value) . "%</option>";
                                                                        } else {
                                                                            echo "
                                                                <option value='" . $value3->discount_id . "-" . (float) ($value3->discount_value) . "'>" . (float) ($value3->discount_value) . "%</option>";
                                                                        }
                                                                    }
                                                                    ?></select>
                                                            </div> <span id='item_discount_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?php
                                                                echo $key->outlet_item_discount_amount ? precise_amount($key->outlet_item_discount_amount) : 0;
                                                                ?></span>
                                                        </td>
                                                        <?php } ?>
                                                        <!-- tax area -->
                                                        <?php
                                                        if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') {
                                                        if ($access_settings[0]->discount_visible == 'yes') {
                                                            ?>
                                                        <td align='right'>
                                                            <input type='hidden' name='item_taxable_value' value='<?= ($key->outlet_item_taxable_value ? $key->outlet_item_taxable_value : 0); ?>'> <span id='item_taxable_value_lbl_<?= $i ?>'><?= $key->outlet_item_taxable_value ? precise_amount($key->outlet_item_taxable_value) : 0; ?></span>
                                                        </td>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                        <?php if ($access_settings[0]->tds_visible == 'yes') { ?>
                                                        <td style='text-align:center'>
                                                            <?php
                                                            if ($key->tds_module_type == "" || $key->tds_module_type == null) {
                                                                $input_type = "hidden";
                                                                ?>
                                                                <?php
                                                                /* echo "<br/>"; */
                                                            } else {
                                                                $input_type = "text";
                                                            }
                                                            ?>
                                                            <div class='tds_modal_body' style='display:none;'>
                                                                <table id="tds_table" index="<?= $i ?>" class="table table-bordered table-striped sac_table ">
                                                                    <thead>
                                                                    <th>TAX Name</th>
                                                                    <th>Action</th>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        $tds_per = 0;
                                                                        foreach ($tax as $key3 => $value3) {
                                                                            if (($key->item_type == 'service' && $value3->tax_name == 'TDS') || ($value3->tax_name == 'TCS' && $key->item_type == 'product')) {
                                                                                if ($value3->tax_id == $key->outlet_item_tds_id) {
                                                                                    $tds_per = (float) ($value3->tax_value);
                                                                                }
                                                                                ?>
                                                                                <tr>
                                                                                    <td>
                                                                                        <?= $value3->tax_name . '(Sec ' . $value3->section_name . ') @ ' . (float) ($value3->tax_value); ?>%</td>
                                                                                    <td>
                                                                                        <div class="radio">
                                                                                            <label>
                                                                                                <input type="radio" name="tds_tax" value="<?= (float) ($value3->tax_value); ?> " <?= ( $value3->tax_id == $key->outlet_item_tds_id ? 'selected' : '' ) ?> tds_id='<?= $value3->tax_id ?>' typ='<?= $value3->tax_name ?>'></label>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <input type="text" class="form-control open_tds_modal pointer" name="item_tds_percentage" value="<?= $tds_per ?>%" readonly>
                                                            <input type='hidden' name='item_tds_id' value='<?= $key->outlet_item_tds_id ? $key->outlet_item_tds_id : 0 ?>'>
                                                            <input type='hidden' name='item_tds_type' value='<?= $key->tds_module_type ? $key->tds_module_type : ' ' ?>'>
                                                            <input type='hidden' name='item_tds_amount' value='<?= $key->outlet_item_tds_amount ? precise_amount($key->outlet_item_tds_amount) : 0 ?>'><span id='item_tds_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?= $key->outlet_item_tds_amount ? precise_amount($key->outlet_item_tds_amount) : 0 ?></span>
                                                        </td>
                                                        <?php } ?>
                                                        <!-- tds area -->
                                                        <?php if ($access_settings[0]->gst_visible == 'yes') { ?>
                                                        <td>
                                                            <?php 
                                                            if($key->outlet_item_tax_amount > 0){
                                                                if(isset($txtAry[$key->outlet_item_tax_percentage])){
                                                                    $txtAry[$key->outlet_item_tax_percentage] += precise_amount($key->outlet_item_tax_amount);
                                                                }else{
                                                                    $txtAry[$key->outlet_item_tax_percentage] = precise_amount($key->outlet_item_tax_amount);
                                                                }
                                                            }
                                                            ?>
                                                            <input type='hidden' name='item_tax_id' value='<?= $key->outlet_item_tax_id ? $key->outlet_item_tax_id : 0 ?>'>
                                                            <input type='hidden' name='item_tax_percentage' value='<?= $key->outlet_item_tax_percentage ? precise_amount($key->outlet_item_tax_percentage) : 0 ?>'>
                                                            <input type='hidden' name='item_tax_amount_cgst' value='0'>
                                                            <input type='hidden' name='item_tax_amount_sgst' value='0'>
                                                            <input type='hidden' name='item_tax_amount_igst' value='0'>
                                                            <input type='hidden' name='item_tax_amount' value='<?= $key->outlet_item_tax_amount ? precise_amount($key->outlet_item_tax_amount) : 0 ?>'>
                                                            <div class="form-group" style="margin-bottom:0px !important;">
                                                                <select class="form-control open_tax form-fixer select2" name="item_tax">
                                                                    <option value="">Select</option>
                                                                    <?php
                                                                    foreach ($tax as $key3 => $value3) {
                                                                        if ($value3->tax_name == 'GST') {
                                                                            echo "
                                                                <option value='" . $value3->tax_id . "-" . ($value3->tax_value) . "' " . ($value3->tax_id == $key->outlet_item_tax_id ? 'selected' : '' ) . " per=".(float) ($value3->tax_value).">" . (float) ($value3->tax_value) . "%</option>";
                                                                        }
                                                                    }
                                                                    ?></select>
                                                            </div> <span id='item_tax_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?= $key->outlet_item_tax_amount ? precise_amount($key->outlet_item_tax_amount) : 0 ?></span>
                                                        </td>

                                                        <td>
                                                            <input type='hidden' name='item_tax_cess_id' value='<?= ($key->outlet_item_tax_cess_id ? $key->outlet_item_tax_cess_id : 0); ?>'>
                                                            <input type='hidden' name='item_tax_cess_percentage' value='<?= $key->outlet_item_tax_cess_percentage ? (float) ($key->outlet_item_tax_cess_percentage) : 0; ?>'>
                                                            <input type='hidden' name='item_tax_cess_amount' value='<?= $key->outlet_item_tax_cess_amount ? precise_amount($key->outlet_item_tax_cess_amount) : 0 ?>'>
                                                            <div class="form-group" style="margin-bottom:0px !important;">
                                                                <select class="form-control open_tax form-fixer select2" name="item_tax_cess" <?= $key->outlet_item_tax_cess_id; ?>>
                                                                    <option value="">Select</option>
                                                                    <?php
                                                                    foreach ($tax as $key3 => $value3) {
                                                                        if ($value3->tax_name == 'CESS') {
                                                                            echo "
                                                                <option value='" . $value3->tax_id . "-" . ($value3->tax_value) . "' " . ($value3->tax_id == $key->outlet_item_tax_cess_id ? 'selected' : '' ) . ">" . (float) ($value3->tax_value) . "%</option>";
                                                                        }
                                                                    }
                                                                    ?></select>
                                                            </div> <span id='item_tax_cess_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?= $key->outlet_item_tax_cess_amount ? precise_amount($key->outlet_item_tax_cess_amount) : 0 ?></span>
                                                        </td>
                                                        <!-- tax area  -->
                                                        <?php } ?>
                                                        <td>
                                                            <input type='text' class='float_number form-control form-fixer text-right' name='item_grand_total' value='<?php
                                                            echo $key->outlet_item_grand_total ? precise_amount($key->outlet_item_grand_total) : 0;
                                                            ?>'>
                                                        
                                                        <?php
                                                        $outlet_temp = array("item_key_value" => $i, "item_id" => $key->item_id, "item_quantity" => $key->outlet_item_quantity, "item_price" => $key->outlet_item_unit_price ? precise_amount($key->outlet_item_unit_price) : 0, "item_description" => $key->outlet_item_description, "item_sub_total" => $key->outlet_item_sub_total ? precise_amount($key->outlet_item_sub_total) : 0, "item_discount_amount" => $key->outlet_item_discount_amount ? precise_amount($key->outlet_item_discount_amount) : 0, "item_discount_id" => $key->outlet_item_discount_id ? $key->outlet_item_discount_id : 0, "item_discount_percentage" => $key->item_discount_percentage ? precise_amount($key->item_discount_percentage) : 0, "item_tax_amount" => $key->outlet_item_tax_amount ? precise_amount($key->outlet_item_tax_amount) : 0, "item_tax_id" => $key->outlet_item_tax_id ? $key->outlet_item_tax_id : 0, "item_tax_cess_amount" => $key->outlet_item_tax_cess_amount ? precise_amount($key->outlet_item_tax_cess_amount) : 0, "item_tax_cess_id" => $key->outlet_item_tax_cess_id ? $key->outlet_item_tax_cess_id : 0, "item_tax_cess_percentage" => $key->outlet_item_tax_cess_percentage ? precise_amount($key->outlet_item_tax_cess_percentage) : 0, "item_tax_percentage" => $key->outlet_item_tax_percentage ? precise_amount($key->outlet_item_tax_percentage) : 0, "item_tds_amount" => $key->outlet_item_tds_amount ? precise_amount($key->outlet_item_tds_amount) : 0, "item_tds_id" => $key->outlet_item_tds_id ? $key->outlet_item_tds_id : 0, "item_tds_percentage" => $key->outlet_item_tds_percentage ? precise_amount($key->outlet_item_tds_percentage) : 0, "item_taxable_value" => $key->outlet_item_taxable_value ? precise_amount($key->outlet_item_taxable_value) : 0, "item_grand_total" => $key->outlet_item_grand_total ? precise_amount($key->outlet_item_grand_total) : 0);
                                                            $outlet_temp['item_code'] = $key->product_code;
                                                        ?><input type="hidden" class="table_json" name="table_data[]" value="<?=json_encode($outlet_temp);?>"></td></tr>
                                                    <?php
                                                    $outlet_data[$i] = $outlet_temp;
                                                    $i++;
                                                } $outlet = htmlspecialchars(json_encode($outlet_data));
                                                $countData = $i;
                                                ?>
                                            </tbody>
                                        </table>
                                        <!-- Hidden Field -->
                                        <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php
                                        echo $branch[0]->branch_country_id;
                                        ?>">
                                        <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php
                                        echo $branch[0]->branch_state_id;
                                        ?>">
                                        <input type="hidden" class="form-control" id="product_module_id" name="product_module_id" value="<?= $product_module_id ?>">
                                        
                                        <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo $data[0]->outlet_date; ?>">
                                        <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $outlet_module_id ?>">
                                        <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>">
                                        <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $data[0]->outlet_invoice_number ?>">
                                        <input type="hidden" class="form-control" id="section_area" name="section_area" value="edit_outlet">
                                        <input type="hidden" name="total_sub_total" id="total_sub_total" value="<?= precise_amount($data[0]->outlet_sub_total); ?>">
                                        <input type="hidden" name="total_taxable_amount" id="total_taxable_amount" value="<?= precise_amount($data[0]->outlet_taxable_value); ?>">
                                        <input type="hidden" name="total_other_taxable_amount" id="total_other_taxable_amount" value="<?= $data[0]->total_other_taxable_amount; ?>">
                                        <input type="hidden" name="without_reound_off_grand_total" id="without_reound_off_grand_total">
                                        <input type="hidden" name="total_discount_amount" id="total_discount_amount" value="<?= precise_amount($data[0]->outlet_discount_amount); ?>">
                                        <input type="hidden" name="total_tax_amount" id="total_tax_amount" value="<?= precise_amount($data[0]->outlet_tax_amount); ?>">
                                        <input type="hidden" name="total_tax_cess_amount" id="total_tax_cess_amount" value="<?= precise_amount($data[0]->outlet_tax_cess_amount); ?>">
                                        <input type="hidden" name="total_igst_amount" id="total_igst_amount" value="<?= precise_amount($data[0]->outlet_igst_amount); ?>">
                                        <input type="hidden" name="total_cgst_amount" id="total_cgst_amount" value="<?= precise_amount($data[0]->outlet_cgst_amount); ?>">
                                        <input type="hidden" name="total_sgst_amount" id="total_sgst_amount" value="<?= precise_amount($data[0]->outlet_cgst_amount); ?>">
                                        <input type="hidden" name="total_tds_amount" id="total_tds_amount" value="<?= precise_amount($data[0]->outlet_tds_amount); ?>">
                                        <input type="hidden" name="total_tcs_amount" id="total_tcs_amount" value="<?= precise_amount($data[0]->outlet_tcs_amount); ?>">
                                        <input type="hidden" name="total_grand_total" id="total_grand_total" value="<?= precise_amount($data[0]->outlet_grand_total + $data[0]->round_off_amount); ?>">
                                        
                                        <input type="hidden" name="total_other_amount" id="total_other_amount" value="<?= precise_amount($data[0]->total_other_amount); ?>">
                                        <?php
                                        $charges_sub_module = 0;
                                        if (in_array($charges_sub_module_id, $access_sub_modules) || ($data[0]->total_freight_charge > 0 || $data[0]->total_insurance_charge > 0 || $data[0]->total_packing_charge > 0 || $data[0]->total_incidental_charge > 0 || $data[0]->total_inclusion_other_charge > 0 || $data[0]->total_exclusion_other_charge > 0)) {
                                            $this->load->view('sub_modules/charges_sub_module');
                                            $charges_sub_module = 1;
                                        } if ($charges_sub_module == 1) {
                                            ?>
                                            <input type="hidden" name="total_freight_charge" id="total_freight_charge" value="<?php
                                            echo precise_amount($data[0]->total_freight_charge);
                                            ?>">
                                            <input type="hidden" name="total_insurance_charge" id="total_insurance_charge" value="<?php
                                            echo precise_amount($data[0]->total_insurance_charge);
                                            ?>">
                                            <input type="hidden" name="total_packing_charge" id="total_packing_charge" value="<?php
                                            echo precise_amount($data[0]->total_packing_charge);
                                            ?>">
                                            <input type="hidden" name="total_incidental_charge" id="total_incidental_charge" value="<?php
                                            echo precise_amount($data[0]->total_incidental_charge);
                                            ?>">
                                            <input type="hidden" name="total_other_inclusive_charge" id="total_other_inclusive_charge" value="<?php
                                            echo precise_amount($data[0]->total_inclusion_other_charge);
                                            ?>">
                                            <input type="hidden" name="total_other_exclusive_charge" id="total_other_exclusive_charge" value="<?php
                                            echo precise_amount($data[0]->total_exclusion_other_charge);
                                            ?>">
                                               <?php } ?>
                                        <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive mt-15">
                                            <tr>
                                                <td align="right">Total Value (+)</td>
                                                <td align='right'><span id="totalSubTotal"><?php
                                                        echo precise_amount($data[0]->outlet_sub_total);
                                                        ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ( $data[0]->outlet_discount_amount <= 0 ? 'style="display: none;"' : '' );
                                                        ?>class='totalDiscountAmount_tr'>
                                                <td align="right">Total Discount (-)</td>
                                                <td align='right'><span id="totalDiscountAmount"><?php
                                                        echo precise_amount($data[0]->outlet_discount_amount);
                                                        ?></span>
                                                </td>
                                            </tr>                                           
                                            <tr <?= ( $cgst_exist != 1 || $data[0]->outlet_cgst_amount <= 0 ? 'style="display: none;"' : '');
                                                        ?>class='totalCGSTAmount_tr'>
                                                <?php
                                                $txt_lbl = $txt_amt = '' ;
                                                foreach ($txtAry as $k => $sgst) {
                                                    $per = (float)($k/2);
                                                    $sgst = (float)($sgst/2);
                                                    $txt_lbl .= 'CGST('.$per.'%)<br>';
                                                    $txt_amt .= precise_amount($sgst).'<br>';
                                                }
                                                ?>
                                                <td align="right"><?=$txt_lbl;?></td>
                                                <td align='right'><span id="totalCGSTAmount"><?=$txt_amt; ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ( $sgst_exist != 1 || $data[0]->outlet_sgst_amount <= 0 ? 'style="display: none;"' : '');
                                                        ?>class='totalSGSTAmount_tr'>
                                                <?php
                                                $lbl = 'SGST';
                                                if ($is_utgst == '1')
                                                    $lbl = 'UTGST';

                                                $txt_lbl = $txt_amt = '' ;
                                                foreach ($txtAry as $k => $sgst) {
                                                    $per = (float)($k/2);
                                                    $sgst = (float)($sgst/2);
                                                    $txt_lbl .= $lbl.'('.$per.'%)<br>';
                                                    $txt_amt .= precise_amount($sgst).'<br>';
                                                }
                                                ?>

                                                <td align="right <?= $data[0]->outlet_billing_state_id; ?>">
                                                    <?= $txt_lbl; ?></td>
                                                <td align='right'><span id="totalSGSTAmount"><?= $txt_amt; ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ( $igst_exist != 1 || $data[0]->outlet_igst_amount <= 0 ? 'style="display: none;"' : '');
                                                    ?>class='totalIGSTAmount_tr'>
                                                <?php
                                                $txt_lbl = $txt_amt = '' ;
                                                foreach ($txtAry as $k => $sgst) {
                                                    $per = (float)($k/2);
                                                    $sgst = (float)($sgst/2);
                                                    $txt_lbl .= 'IGST('.$per.'%)<br>';
                                                    $txt_amt .= precise_amount($sgst).'<br>';
                                                }
                                                ?>
                                                <td align="right"><?=$txt_lbl?></td>
                                                <td align='right'><span id="totalIGSTAmount"><?=$txt_amt; ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ( $cess_exist != 1 || $data[0]->outlet_tax_cess_amount <= 0 ? 'style="display: none;"' : '');
                                                    ?>class='totalCessAmount_tr'>
                                                <td align="right">Cess (+)</td>
                                                <td align='right'><span id="totalTaxCessAmount"><?= precise_amount($data[0]->outlet_tax_cess_amount); ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ( $tds_exist != 1 || $data[0]->outlet_tds_amount <= 0 ? 'style="display: none;"' : '');
                                                    ?>class='tds_amount_tr'>
                                                <td align="right">
                                                    <?php echo 'TDS Amount'; ?>
                                                </td>
                                                <td align='right'> <span id="totalTdsAmount"><?= precise_amount($data[0]->outlet_tds_amount); ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ( $tds_exist != 1 || $data[0]->outlet_tcs_amount <= 0 ? 'style="display: none;"' : '');
                                                    ?>class='tcs_amount_tr'>
                                                <td align="right">
                                                    <?php echo 'TCS Amount'; ?>(+)</td>
                                                <td align='right'> <span id="totalTcsAmount"><?= precise_amount($data[0]->outlet_tcs_amount); ?></span>
                                                </td>
                                            </tr>
                                            <?php if ($charges_sub_module == 1) { ?>
                                                <tr id="freight_charge_tr">
                                                    <td align="right">Freight Charge (+)</td>
                                                    <td align='right'> <span id="freight_charge"><?= precise_amount($data[0]->total_freight_charge); ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="insurance_charge_tr">
                                                    <td align="right">Insurance Charge (+)</td>
                                                    <td align='right'> <span id="insurance_charge"><?php
                                                            echo precise_amount($data[0]->total_insurance_charge);
                                                            ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="packing_charge_tr">
                                                    <td align="right">Packing & Forwarding Charge (+)</td>
                                                    <td align='right'> <span id="packing_charge"><?php
                                                            echo precise_amount($data[0]->total_packing_charge);
                                                            ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="incidental_charge_tr">
                                                    <td align="right">Incidental Charge (+)</td>
                                                    <td align='right'> <span id="incidental_charge"><?php
                                                            echo precise_amount($data[0]->total_incidental_charge);
                                                            ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="other_inclusive_charge_tr">
                                                    <td align="right">Other Inclusive Charge (+)</td>
                                                    <td align='right'> <span id="other_inclusive_charge"><?php
                                                            echo precise_amount($data[0]->total_inclusion_other_charge);
                                                            ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="other_exclusive_charge_tr">
                                                    <td align="right">Other Exclusive Charge (-)</td>
                                                    <td align='right'> <span id="other_exclusive_charge"><?php
                                                            echo precise_amount($data[0]->total_exclusion_other_charge);
                                                            ?></span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($access_common_settings[0]->round_off_access == 'yes') { ?>
                                                <tr class="round_off_minus_tr" <?= ( $data[0]->round_off_amount <= 0 ? 'style="display: none;"' : '');
                                                ?>>
                                                    <td align="right">Round Off (-)</td>
                                                    <td align='right'> <span id="round_off_minus_charge"><?= precise_amount(abs($data[0]->round_off_amount)); ?></span>
                                                    </td>
                                                </tr>
                                                <tr class="round_off_plus_tr" <?= ( $data[0]->round_off_amount >= 0 ? 'style="display: none;"' : ''); ?>>
                                                    <td align="right">Round Off (+)</td>
                                                    <td align='right'> <span id="round_off_plus_charge"><?= precise_amount(abs($data[0]->round_off_amount)); ?></span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php
                                            $checked = "";
                                            $value_selected = "";
                                            if ($data[0]->round_off_amount > 0 || $data[0]->round_off_amount < 0) {
                                                $checked = "checked";
                                                $grand_total = precise_amount($data[0]->outlet_grand_total + $data[0]->round_off_amount);
                                                $next_val = ceil($grand_total);
                                                $prev_val = floor($grand_total);
                                                if ($data[0]->round_off_amount < 0) {
                                                    $value_selected = "selected";
                                                }
                                            }
                                            ?>                                           
                                            <tr id="round_off_option">
                                                <td align="right" width="50%">Round Off</td>
                                                <td align="right" width="50%">
                                                    <label>
                                                        <input type="radio" name="round_off_key" value="yes" class="minimal" <?= $checked ?>>yes
                                                    </label>                                                    
                                                    <label>
                                                        <input type="radio" name="round_off_key" value="no" class="minimal" <?php
                                                        if ($checked == "") {
                                                            echo "checked";
                                                        }
                                                        ?>>No</label>                                                   
                                                </td>
                                            </tr>
                                            <?php if ($checked == "checked") { ?>
                                                <tr id="round_off_select">
                                                    <td align="right" width="50%">Select the nearest value</td>
                                                    <td align="right" width="50%">
                                                        <select id="round_off_value" class="form-control" name="round_off_value">
                                                            <?php if ($next_val == $prev_val) { ?>
                                                                <option value="<?= $next_val ?>">
                                                                    <?= $next_val ?>
                                                                </option>
                                                            <?php } else { ?>
                                                                <option value="<?= $prev_val ?>" <?php
                                                                if ($value_selected == "") {
                                                                    echo "selected";
                                                                }
                                                                ?>>
                                                                            <?= $prev_val ?>
                                                                </option>
                                                                <option value="<?= $next_val ?>" <?= $value_selected ?>>
                                                                    <?= $next_val ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                            <?php } else { ?>
                                                <tr style="display: none;" id="round_off_select">
                                                    <td align="right" width="50%">Select the nearest value</td>
                                                    <td align="right" width="50%">
                                                        <select id="round_off_value" name="round_off_value"></select>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <tr>
                                                <td align="right">Grand Total (=)</td>
                                                <td align='right'><span id="totalGrandTotal"><?= precise_amount($data[0]->outlet_grand_total); ?></span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if ($tax_exist == 1) {
                                $settings_tax_type = "single_tax";
                            } else if ($igst_exist == 1 || $cgst_exist == 1 || $sgst_exist == 1 || $cess_exist == 1) {
                                $settings_tax_type = "gst";
                            } else {
                                $settings_tax_type = "no_tax";
                            } if ($discount_exist == 1) {
                                $discount_visible = "yes";
                            } else {
                                $discount_visible = "no";
                            } if ($description_exist == 1) {
                                $description_visible = "yes";
                            } else {
                                $description_visible = "no";
                            } if ($tds_exist == 1) {
                                $tds_visible = "yes";
                            } else {
                                $tds_visible = "no";
                            }
                            ?>
                            <input type="hidden" name="tax_type" id="tax_type" value="<?= $settings_tax_type ?>">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="box-footer">
                                        <button type="submit" id="outlet_submit" name="outlet_submit" class="btn btn-info">Update</button> <span class="btn btn-default" id="sale_cancel" onclick="cancel('outlet')">Cancel</span>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $notes_sub_module = 0;
                            if (in_array($notes_sub_module_id, $access_sub_modules)) {
                                $notes_sub_module = 1;
                            } if ($notes_sub_module == 1) {
                                $this->load->view('sub_modules/notes_sub_module');
                            }
                            ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('discount/discount_modal_product');
$this->load->view('tax/tax_modal_tds');
$this->load->view('tax/tax_modal_gst');
$this->load->view('uqc/uom_modal');
$this->load->view('service/hsn_modal');
if ($item_modal == 1) {
    $this->load->view('layout/item_modal');
}
?>
<?php if ($charges_sub_module == 1) { ?>
    <script src="<?php echo base_url('assets/js/sub_modules/'); ?>charges_sub_module.js"></script>
<?php } ?>
<script type="text/javascript">
    var sales_data = <?php echo json_encode($outlet_data); ?>;
    var branch_state_list = <?php echo json_encode($state); ?>;
    var discount_ary= <?=json_encode($discount);?>;
    var tax_ary = <?php echo json_encode($tax); ?>;
    var uqc = <?=json_encode($uqc)?>;
    var count_data = <?= $countData; ?>;
    var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
    var common_settings_amount_precision = "<?= $access_common_settings[0]->amount_precision ?>";
    var settings_tax_percentage = "<?= $access_common_settings[0]->tax_split_percentage ?>";
    var common_settings_inventory_advanced = "<?= $inventory_access ?>";
    var settings_tax_type = "<?= $access_settings[0]->tax_type ?>";
    var settings_discount_visible = "<?= $access_settings[0]->discount_visible ?>";
    var settings_description_visible = "<?= $access_settings[0]->description_visible ?>";
    var settings_tds_visible = "<?= $access_settings[0]->tds_visible ?>";
    var settings_item_editable = "<?= $access_settings[0]->item_editable ?>";
</script>
<script src="<?php echo base_url('assets/js/outlet/'); ?>outlet.js"></script>
<script src="<?php echo base_url('assets/js/outlet/'); ?>outlet_basic_common.js"></script>