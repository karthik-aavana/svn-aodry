<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?php echo base_url('sales_credit_note'); ?>">Sales Credit Note</a></li>
                <li class="active">Edit Sales Credit Note</li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Sales Credit Note</h3>
                        <a class="btn btn-sm btn-default pull-right back_button" href1="<?php echo base_url('sales_credit_note'); ?>">Back </a>
                    </div>
                    <form role="form" id="form" method="post" action="<?php echo base_url('sales_credit_note/edit_sales_credit_note'); ?>">
                        <div class="box-body">
                            <div class="row">
                                <input type="hidden" name="sales_credit_note_id" value="<?= $data[0]->sales_credit_note_id ?>">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="date">Credit Note Date<span class="validation-color">*</span></label>
                                        <div class="input-group date">              
                                            <input type="text" style="background: #fff;" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?php echo date('d-m-Y',strtotime($data[0]->sales_credit_note_date)); ?>">
                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                        </div>
                                        <span class="validation-color" id="err_date"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="invoice_number">Credit Note Number<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?= $data[0]->sales_credit_note_invoice_number ?>" <?php
                                        if ($access_settings[0]->invoice_readonly == 'yes') {
                                            echo "readonly";
                                        }
                                        ?>>
                                        <span class="validation-color" id="err_invoice_number"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="customer">Customer <span class="validation-color">*</span></label>
                                        <!-- <?php
                                        if (isset($other_modules_present['customer_module_id']) && $other_modules_present['customer_module_id'] != "") {
                                            ?>
                                                                                                            <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#customer_modal" class="pull-right">+ Add Customer</a>
                                        <?php }
                                        ?> -->
                                        <select class="form-control select2" autofocus="on" id="customer" name="customer" readonly>
                                            <!-- <option value="">Select</option> -->
                                            <?php
                                            foreach ($customer as $row) {

                                                if ($data[0]->sales_credit_note_party_type == 'customer' && $data[0]->sales_credit_note_party_id == $row->customer_id) {
                                                    if($row->customer_mobile != ''){
                                                        echo "<option value='$row->customer_id-$row->customer_country_id-$row->customer_state_id' selected>$row->customer_name($row->customer_mobile)</option>";
                                                    }else{
                                                        echo "<option value='$row->customer_id-$row->customer_country_id-$row->customer_state_id' selected>$row->customer_name</option>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="validation-color" id="err_customer"><?php echo form_error('customer'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="sales_invoice_number">Sales Reference Number <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="sales_invoice_number" name="sales_invoice_number" readonly>
                                            <option value='<?= $data[0]->sales_id ?>'><?= $sales_invoice_number ?></option>
                                        </select>
                                        <span class="validation-color" id="err_sales_invoice_number"><?php echo form_error('sales_invoice_number'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 disable_div">
                                    <div class="form-group">
                                        <label for="nature_of_supply">Nature of Supply <span class="validation-color">*</span></label>
                                        <select class="form-control select2"  id="nature_of_supply" name="nature_of_supply">
                                            <option value="product" <?= ($data[0]->sales_credit_note_nature_of_supply == "product" ? 'selected' : '') ?>>Product</option>
                                            <option value="service" <?= ($data[0]->sales_credit_note_nature_of_supply == "service" ? 'selected' : '') ?>>Service</option>
                                            <option value="both" <?= ($data[0]->sales_credit_note_nature_of_supply != "product" && $data[0]->sales_credit_note_nature_of_supply != "service" ? 'selected' : '') ?>>Both</option>
                                        </select>
                                        <!-- <?php if ($data[0]->sales_credit_note_nature_of_supply == "product") { ?>                                                  <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="product" readonly>                 <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Goods" readonly>
                                        <?php } elseif ($data[0]->sales_credit_note_nature_of_supply == "service") {  ?> <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="service" readonly>  <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Service" readonly>
                                        <?php } else { ?>         <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="both" readonly>   <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Product/Service" readonly>
                                        <?php } ?> -->
                                        <span class="validation-color" id="err_nature_supply"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3" style="display: none;">
                                    <div class="form-group disable_div">
                                        <label for="type_of_supply">Billing Country <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="billing_country" name="billing_country" >
                                            <option value="">Select</option>
                                            <?php foreach ($country as $key) { ?>
                                                <option value='<?php echo $key->country_id ?>' <?php
                                                if ($key->country_id == $data[0]->sales_credit_note_billing_country_id) {
                                                    echo "selected";
                                                } ?>><?php echo $key->country_name; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <span class="validation-color" id="err_billing_country"><?php echo form_error('billing_country'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group disable_div">
                                        <label for="type_of_supply">Place of Supply <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="billing_state" name="billing_state">
                                            <?php
                                            if ($data[0]->sales_credit_note_billing_country_id == $branch[0]->branch_country_id) {

                                                foreach ($state as $key) {
                                                    ?>
                                                    <option value='<?php echo $key->state_id ?>' <?php
                                                    if ($key->state_id == $data[0]->sales_credit_note_billing_state_id) {
                                                        echo "selected";
                                                    }
                                                    ?>>
                                                                <?php echo $key->state_name; ?>
                                                    </option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <option value="0">Out of Country</option>
                                        </select>
                                        <span class="validation-color" id="err_billing_state"><?php echo form_error('billing_state'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group disable_div">
                                        <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="type_of_supply" name="type_of_supply">
                                            <?php if ($data[0]->sales_credit_note_type_of_supply == 'regular') { ?>
                                                <option value="regular" <?php
                                                if ($data[0]->sales_credit_note_type_of_supply == 'regular') {
                                                    echo "selected";
                                                }
                                                ?>>Regular</option>
                                                    <?php } else { ?>
                                                <option value="export_with_payment" <?= ($data[0]->sales_credit_note_type_of_supply == 'export_with_payment' ? "selected" : ''); ?>>Export (With Tax Payment)</option>
                                                <option value="export_without_payment" <?= ($data[0]->sales_credit_note_type_of_supply == 'export_without_payment' ? "selected" : ''); ?>>Export (Without Tax Payment)</option>
                                            <?php } ?>
                                        </select>
                                        <span class="validation-color" id="err_type_supply"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3" style="display: none">
                                    <div class="form-group disable_div">
                                        <label for="currency_id">Billing Currency <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="currency_id" name="currency_id">
                                            <?php
                                            foreach ($currency as $key => $value) {
                                                if ($value->currency_id == $data[0]->currency_id) {
                                                    echo "<option value='" . $value->currency_id . "' selected>" . $value->currency_name .' - ' . $value->country_shortname. "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="validation-color" id="err_currency_id"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 gst_payable_div disable_div" style="<?= ($data[0]->sales_credit_note_type_of_supply != 'regular' ? "display:none;" : ''); ?>">
                                    <div class="form-group">
                                        <label for="gst_payable">GST Payable on Reverse Charge <span class="validation-color">*</span></label>
                                        <br/>
                                        <label class="radio-inline p_l_20">
                                            <input class="" type="radio" name="gst_payable" value="yes" <?= ($data[0]->sales_credit_note_gst_payable == "yes" ? 'checked="checked"' : ''); ?> /> Yes
                                        </label>
                                        <label class="radio-inline p_l_20">
                                            <input class="" type="radio" name="gst_payable" value="no" <?= ($data[0]->sales_credit_note_gst_payable == "no" ? 'checked="checked"' : ''); ?> /> No
                                        </label>
                                        <br/>
                                        <span class="validation-color" id="err_gst_payable"></span>
                                    </div>
                                </div>
                            </div>


                            <?php
                            foreach ($access_sub_modules as $key => $value) {

                                if (isset($transporter_sub_module_id)) {

                                    if ($transporter_sub_module_id == $value) {
                                        $this->load->view('sub_modules/transporter_sub_module');
                                    }
                                }
                            }

                            foreach ($access_sub_modules as $key => $value) {
                                if (isset($shipping_sub_module_id)) {
                                    if ($shipping_sub_module_id == $value) {
                                        $this->load->view('sub_modules/shipping_sub_module');
                                    }
                                }
                            }

                            if (isset($other_modules_present['accounts_module_id']) && $other_modules_present['accounts_module_id'] != "") {
                                foreach ($access_sub_modules as $key => $value) {
                                    if (isset($accounts_sub_module_id)) {
                                        if ($accounts_sub_module_id == $value) {
                                            $this->load->view('sub_modules/accounts_sub_module');
                                            break;
                                        }
                                    }
                                }
                            }
                            ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <span class="validation-color" id="err_product"></span>
                                </div>
                                <?php if ($access_settings[0]->item_access == 'product') { ?>
                                    <div class="col-sm-12 search_sales_code">
                                        <?php if (isset($other_modules_present['product_module_id']) && $other_modules_present['product_module_id'] != "") { ?>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#product_modal" class="open_product_modal pull-left">+</a></div>
                                            <?php } ?>
                                            <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product Code/Name" >
                                        </div>
                                    </div>
                                <?php } elseif ($access_settings[0]->item_access == 'service') { ?>
                                    <div class="col-sm-12 search_sales_code">
                                        <?php
                                        if (isset($other_modules_present['service_module_id']) && $other_modules_present['service_module_id'] != "") {
                                            ?>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#service_modal" class="open_service_modal pull-left">+</a></div>
                                            <?php }
                                            ?>
                                            <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product Code/Name" ></div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <!-- <div class="col-sm-12 search_sales_code">
                                    <?php
                                    if (isset($other_modules_present['product_module_id']) && isset($other_modules_present['service_module_id']) && $other_modules_present['product_module_id'] != "" && $other_modules_present['service_module_id'] != "") {
                                        ?>  <div class="input-group"> <div class="input-group-addon"><a href="" data-toggle="modal" data-target="#item_modal" class="pull-left">+</a></div>
                                    <?php }
                                    ?>
                                            <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product/Service Code/Name" >
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?> -->
                                <div class="col-sm-12">
                                    <span class="validation-color" id="err_sales_code"></span>
                                </div>
                                <div class="col-sm-12">                                                    	
                                    <div class="box-header">
                                        <h3 class="box-title ml-0">Inventory Items</h3>                                                      
                                        <table class="table table-striped table-bordered table-condensed table-hover sales_table table-responsive" name="sales_data" id="sales_credit_note_data">
                                            <thead>
                                                <tr>
                                                    <th width="2%"><img src="<?php echo base_url(); ?>assets/images/bin1.png" /></th>
                                                    <th class="span2">Items</th>
                                                    <?php
                                                    if ($access_settings[0]->description_visible == 'yes') {
                                                        ?>
                                                        <th class="span2">Description</th>
                                                    <?php } ?>
                                                    <th class="span2" width="6%">Quantity</th>
                                                    <!-- <th class="span2" >Rate</th> -->
                                                    <!-- <?php
                                                    if ($access_settings[0]->discount_visible == 'yes') {
                                                        ?>  <th class="span2" >Discount
                                                        <?php if (isset($other_modules_present['discount_module_id']) && $other_modules_present['discount_module_id'] != "") { ?>  <a href="" data-toggle="modal" data-target="#discount_modal"><strong>+</strong></a>
                                                        <?php } ?> <?php } ?> -->
                                                    <?php
                                                    if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') {
                                                        if ($access_settings[0]->discount_visible == 'yes') {
                                                            ?>
                                                            <th class="span2"  width="14%">Taxable Value</th>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    <?php if ($access_settings[0]->tds_visible == 'yes') { ?>
                                                        <th class="span2" width="10%">TDS/TCS(%)</th>
                                                    <?php } ?>
                                                    <?php if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') { ?>
                                                        <th class="span2"  width="10%">GST(%)</th>
                                                        <th class="span2"  width="10%">Cess(%) </th>
                                                    <?php } ?>
                                                    <th class="span2" width="14%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sales_credit_note_table_body">
                                                <?php
                                                $i = 0;
                                                $tot = 0;
                                                foreach ($items as $key) {
                                                    ?>
                                                    <tr id="<?= $i ?>">
                                                        <td>
                                                            <a class='deleteRow'> <img src='<?php echo base_url(); ?>assets/images/bin_close.png' /></a>
                                                            <input type='hidden' name='item_key_value' value="<?php echo $i ?>">
                                                            <input type='hidden' name='item_id' value="<?php echo $key->item_id ?>">
                                                            <input type='hidden' name='item_type' value="<?php echo $key->item_type ?>">
                                                            <?php if ($key->item_type == 'product' || $key->item_type == 'product_inventory') { ?>
                                                                <input type='hidden' name='item_code' value='<?php echo $key->product_code ?>'>
                                                            <?php } else {
                                                                ?>
                                                                <input type='hidden' name='item_code' value='<?php echo $key->service_code ?>'>
                                                                <?php
                                                            }
                                                            if ($key->item_type == 'product' || $key->item_type == 'product_inventory') {
                                                                ?>
                                                            <td><?php echo $key->product_name; ?><br>(P) (HSN/SAC: <?php echo $key->product_hsn_sac_code; ?>)<br><?php echo $key->product_batch; ?></td>
                                                        <?php } else {
                                                            ?>
                                                            <td><?php echo $key->service_name; ?><br>(S) (HSN/SAC: <?php echo $key->service_hsn_sac_code; ?>)</td>
                                                        <?php } ?>
                                                        <?php
                                                        if ($access_settings[0]->description_visible == 'yes') {
                                                            ?>
                                                            <td><input type='text' name='item_description' class='form-control form-fixer' value="<?php echo $key->sales_credit_note_item_description ?>"></td>
                                                        <?php } ?>
                                                        <td><input type="text" class="float_number form-control form-fixer text-center" value="<?php echo $key->sales_credit_note_item_quantity ?>" data-rule="quantity" name='item_quantity' limit='<?= $key->sales_credit_note_item_quantity; ?>'>
                                                        </td>
                                                        <td align='right'>
                                                            <input type='hidden' name='item_discount_id' value='<?php
                                                            echo $key->sales_credit_note_item_discount_id ? $key->sales_credit_note_item_discount_id : 0;
                                                            ?>'>
                                                            <input type='hidden' name='item_discount_percentage' value='0'>
                                                            <input type='hidden' name='item_discount_amount' value='<?php
                                                            echo $key->sales_credit_note_item_discount_amount ? $key->sales_credit_note_item_discount_amount : 0;
                                                            ?>'>
                                                            <input type='hidden' class='float_number form-control form-fixer text-right' name='item_price' value='<?php echo precise_amount($key->sales_credit_note_item_unit_price); ?>' limit='<?php echo precise_amount($key->sales_credit_note_item_unit_price); ?>'>
                                                            <input type='hidden' class='form-control' style='' value='<?php echo $key->sales_credit_note_item_sub_total ?>' name='item_sub_total' readonly>
                                                            <input type='text' name='item_taxable_value' class="float_number form-control form-fixer text-right" value='<?= precise_amount($key->sales_credit_note_item_taxable_value ? $key->sales_credit_note_item_taxable_value : 0); ?>' limit='<?= precise_amount($key->sales_credit_note_item_taxable_value ? $key->sales_credit_note_item_taxable_value : 0); ?>' >
                                                        </td>
                                                        <?php if ($access_settings[0]->tds_visible == 'yes') { ?>
                                                            <td style='text-align:center'>
                                                                <?php
                                                                if ($key->tds_module_type == "" || $key->tds_module_type == null) {
                                                                    $input_type = "hidden";
                                                                    ?>                                                                                                                                    <!-- <span id='item_tds_percentage_hide_lbl_<?= $i ?>' class='text-center' ><?= $key->sales_credit_note_item_tds_percentage ? precise_amount($key->sales_credit_note_item_tds_percentage) : 0 ?></span> -->
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
                                                                                if ($value3->tax_value <= $key->sales_credit_note_item_tds_percentage) {

                                                                                    if ($value3->tax_id == $key->sales_credit_note_item_tds_id) {
                                                                                        $tds_per = (float)($value3->tax_value);
                                                                                    }
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td><?= $value3->tax_name . '(Sec ' . $value3->section_name . ') @ ' . (float)($value3->tax_value); ?>%</td>
                                                                                        <td><div class="radio">
                                                                                                <label><input type="radio" name="tds_tax" value="<?= (float)($value3->tax_value); ?> "<?= ($value3->tax_id == $key->sales_credit_note_item_tds_id ? 'selected' : '' ) ?> tds_id='<?= $value3->tax_id ?>' typ='<?= $value3->tax_name ?>'></label>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                        }
                                                                        ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <input type="text" class="form-control open_tds_modal pointer" name="item_tds_percentage" value="<?= $tds_per ?>%" readonly>

                                                                <input type='hidden' name='item_tds_id' value='<?= $key->sales_credit_note_item_tds_id ? $key->sales_credit_note_item_tds_id : 0 ?>'><input type='hidden' name='item_tds_type' value='<?= $key->tds_module_type ? $key->tds_module_type : '' ?>'>
                                                                <input type='hidden' name='item_tds_amount' value='<?= $key->sales_credit_note_item_tds_amount ? precise_amount($key->sales_credit_note_item_tds_amount) : 0 ?>'><span id='item_tds_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?= $key->sales_credit_note_item_tds_amount ? precise_amount($key->sales_credit_note_item_tds_amount) : 0 ?></span>
                                                            </td>
                                                        <?php } ?>
                                                        <?php if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') { ?>
                                                            <td><input type='hidden' name='item_tax_id' value='<?= $key->sales_credit_note_item_tax_id ? $key->sales_credit_note_item_tax_id : 0 ?>'>
                                                                <input type='hidden' name='item_tax_percentage' value='<?= $key->sales_credit_note_item_tax_percentage ? (float)($key->sales_credit_note_item_tax_percentage) : 0 ?>'>
                                                                <input type='hidden' name='item_tax_amount_cgst' value='0'>
                                                                <input type='hidden' name='item_tax_amount_sgst' value='0'>
                                                                <input type='hidden' name='item_tax_amount_igst' value='0'>
                                                                <input type='hidden' name='item_tax_amount' value='<?= $key->sales_credit_note_item_tax_amount ? precise_amount($key->sales_credit_note_item_tax_amount) : 0 ?>'>
                                                                <div class="form-group" style="margin-bottom:0px !important;">
                                                                    <select class="form-control open_tax form-fixer select2" name="item_tax">
                                                                        <option value="">Select</option>
                                                                        <?php
                                                                        foreach ($tax as $key3 => $value3) {
                                                                            if ($value3->tax_name == 'GST') {
                                                                                if ($value3->tax_value <= $key->sales_credit_note_item_tax_percentage) {
                                                                                    echo "<option value='" . $value3->tax_id . "-" . ($value3->tax_value) . "' " . ($value3->tax_id == $key->sales_credit_note_item_tax_id ? 'selected' : '' ) . " " . $key->sales_credit_note_item_tax_id . ">" . (float) ($value3->tax_value) . "%</option>";
                                                                                }
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <span id='item_tax_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?= $key->sales_credit_note_item_tax_amount ? precise_amount($key->sales_credit_note_item_tax_amount) : 0 ?></span>
                                                            </td>
                                                            <td><input type='hidden' name='item_tax_cess_id' value='<?= ($key->sales_credit_note_item_tax_cess_id ? $key->sales_credit_note_item_tax_cess_id : 0); ?>'>
                                                                <input type='hidden' name='item_tax_cess_percentage' value='<?= $key->sales_credit_note_item_tax_cess_percentage ? (float)($key->sales_credit_note_item_tax_cess_percentage) : 0; ?>'>
                                                                <input type='hidden' name='item_tax_cess_amount' value='<?= $key->sales_credit_note_item_tax_cess_amount ? precise_amount($key->sales_credit_note_item_tax_cess_amount) : 0 ?>'>
                                                                <div class="form-group" style="margin-bottom:0px !important;">
                                                                    <select class="form-control open_tax form-fixer select2" name="item_tax_cess" <?= $key->sales_credit_note_item_tax_cess_id; ?>>
                                                                        <option value="">Select</option>
                                                                        <?php
                                                                        foreach ($tax as $key3 => $value3) {
                                                                            if ($value3->tax_name == 'CESS') {
                                                                                if ($value3->tax_value <= $key->sales_credit_note_item_tax_cess_percentage) {
                                                                                    echo "<option value='" . $value3->tax_id . "-" . ($value3->tax_value) . "' " . ($value3->tax_id == $key->sales_credit_note_item_tax_cess_id ? 'selected' : '' ) . ">" . (float) ($value3->tax_value) . "%</option>";
                                                                                }
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <span id='item_tax_cess_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?= $key->sales_credit_note_item_tax_cess_amount ? precise_amount($key->sales_credit_note_item_tax_cess_amount) : 0 ?></span>
                                                            </td>
                                                        <?php } ?>
                                                        <td align="right">
                                                            <input type="text" class="float_number form-control form-fixer text-right" name="item_grand_total" value="<?php echo precise_amount($key->sales_credit_note_item_grand_total); ?>" limit="<?php echo precise_amount($key->sales_credit_note_item_grand_total); ?>">
                                                        </td>
                                                        <?php
                                                        $temp = array(
                                                            "item_id" => $key->item_id,
                                                            "item_type" => $key->item_type,
                                                            "item_igst" => $key->sales_credit_note_item_igst_percentage,
                                                            "item_cgst" => $key->sales_credit_note_item_cgst_percentage,
                                                            "item_sgst" => $key->sales_credit_note_item_sgst_percentage
                                                        );
                                                        $item_gst[$key->item_id . '-' . $key->item_type] = $temp;
                                                        ?>
                                                    </tr>
                                                    <?php
                                                    $sales_data[$i]['item_id'] = $key->item_id;
                                                    $sales_data[$i]['item_type'] = $key->item_type;

                                                    if ($key->item_type == 'product' || $key->item_type == 'product_inventory') {
                                                        $sales_data[$i]['item_code'] = $key->product_code;
                                                    } else {
                                                        $sales_data[$i]['item_code'] = $key->service_code;
                                                    }

                                                    $sales_data[$i]['item_price'] = $key->sales_credit_note_item_unit_price;
                                                    $sales_data[$i]['item_key_value'] = $i;
                                                    $sales_data[$i]['item_sub_total'] = $key->sales_credit_note_item_sub_total;
                                                    $sales_data[$i]['item_description'] = $key->sales_credit_note_item_description;
                                                    $sales_data[$i]['item_quantity'] = $key->sales_credit_note_item_quantity;
                                                    $sales_data[$i]['item_discount_amount'] = (@$key->sales_credit_note_item_discount_amount ? $key->sales_credit_note_item_discount_amount : 0);
                                                    $sales_data[$i]['item_discount_value'] = (@$key->sales_credit_note_item_discount_value ? $key->sales_credit_note_item_discount_value : 0);
                                                    $sales_data[$i]['item_discount'] = $key->sales_credit_note_item_discount_id;
                                                    $sales_data[$i]['item_discount_id'] = $key->sales_credit_note_item_discount_id;
                                                    $sales_data[$i]['item_igst'] = $key->sales_credit_note_item_igst_percentage;
                                                    $sales_data[$i]['item_igst_amount'] = $key->sales_credit_note_item_igst_amount;
                                                    $sales_data[$i]['item_cgst'] = $key->sales_credit_note_item_cgst_percentage;
                                                    $sales_data[$i]['item_cgst_amount'] = $key->sales_credit_note_item_cgst_amount;
                                                    $sales_data[$i]['item_sgst'] = $key->sales_credit_note_item_sgst_percentage;
                                                    $sales_data[$i]['item_sgst_amount'] = $key->sales_credit_note_item_sgst_amount;
                                                    $sales_data[$i]['item_tds_percentage'] = $key->sales_credit_note_item_tds_percentage;
                                                    $sales_data[$i]['item_tds_id'] = $key->sales_credit_note_item_tds_id;
                                                    $sales_data[$i]['item_tds_amount'] = $key->sales_credit_note_item_tds_amount;
                                                    $sales_data[$i]['item_tax_cess_percentage'] = $key->sales_credit_note_item_tax_cess_percentage;
                                                    $sales_data[$i]['item_tax_cess_amount'] = $key->sales_credit_note_item_tax_cess_amount;
                                                    $sales_data[$i]['item_tax_cess_id'] = $key->sales_credit_note_item_tax_cess_id;
                                                    $sales_data[$i]['item_tax_percentage'] = $key->sales_credit_note_item_tax_percentage;
                                                    $sales_data[$i]['item_tax_amount'] = $key->sales_credit_note_item_tax_amount;
                                                    $sales_data[$i]['item_tax_id'] = $key->sales_credit_note_item_tax_id;
                                                    $sales_data[$i]['item_taxable_value'] = $key->sales_credit_note_item_taxable_value;
                                                    $sales_data[$i]['item_grand_total'] = $key->sales_credit_note_item_grand_total;

                                                    //array_push($product_data,$product);
                                                    $i++;
                                                }

                                                $sales = htmlspecialchars(json_encode($sales_data));
                                                ?>
                                            </tbody>
                                        </table>
                                        <!-- Hidden Field -->
                                        <input type="hidden" class="form-control" id="section_area" name="section_area" value="edit_sales_credit_note" readonly>
                                        <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php echo $branch[0]->branch_country_id; ?>">
                                        <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php echo $branch[0]->branch_state_id; ?>">
                                        <input type="hidden" class="form-control" id="product_module_id" name="product_module_id" value="<?php
                                        if (isset($other_modules_present['product_module_id'])) {
                                            echo $other_modules_present['product_module_id'];
                                        }
                                        ?>" readonly>
                                        <input type="hidden" class="form-control" id="service_module_id" name="service_module_id" value="<?php
                                        if (isset($other_modules_present['service_module_id'])) {
                                            echo $other_modules_present['service_module_id'];
                                        }
                                        ?>" readonly>
                                        <input type="hidden" class="form-control" id="customer_module_id" name="customer_module_id" value="<?php
                                        if (isset($other_modules_present['customer_module_id'])) {
                                            echo $other_modules_present['customer_module_id'];
                                        }
                                        ?>" readonly>
                                        <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo date('d-m-Y',strtotime($data[0]->sales_credit_note_date)); ?>" readonly>
                                        <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" readonly>
                                        <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>
                                        <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $data[0]->sales_credit_note_invoice_number ?>" readonly>
                                        <input type="hidden" name="total_sub_total" id="total_sub_total" value="<?= $data[0]->sales_credit_note_sub_total; ?>">
                                        <input type="hidden" name="total_taxable_amount" id="total_taxable_amount" value="<?= $data[0]->sales_credit_note_taxable_value; ?>">
                                        <input type="hidden" name="total_discount_amount" id="total_discount_amount" value="<?= $data[0]->sales_credit_note_discount_amount; ?>">
                                        <input type="hidden" name="total_tax_amount" id="total_tax_amount" value="<?= $data[0]->sales_credit_note_tax_amount; ?>">
                                        <input type="hidden" name="total_igst_amount" id="total_igst_amount" value="<?= $data[0]->sales_credit_note_igst_amount; ?>">
                                        <input type="hidden" name="total_cgst_amount" id="total_cgst_amount" value="<?= $data[0]->sales_credit_note_cgst_amount; ?>">
                                        <input type="hidden" name="total_sgst_amount" id="total_sgst_amount" value="<?= $data[0]->sales_credit_note_sgst_amount; ?>">
                                        <input type="hidden" name="total_tax_cess_amount" id="total_tax_cess_amount" value="<?= $data[0]->sales_credit_note_tax_cess_amount; ?>">
                                        <input type="hidden" name="total_tds_amount" id="total_tds_amount" value="<?= $data[0]->sales_credit_note_tds_amount; ?>">
                                        <input type="hidden" name="total_tcs_amount" id="total_tcs_amount" value="<?= $data[0]->sales_credit_note_tcs_amount; ?>">
                                        <input type="hidden" name="total_grand_total" id="total_grand_total" value="<?= $data[0]->sales_credit_note_grand_total; ?>">
                                        <input type="hidden" name="table_data" id="table_data" value="<?php echo $sales; ?>">
                                        <input type="hidden" name="without_reound_off_grand_total" id="without_reound_off_grand_total" value="<?= $data[0]->sales_credit_note_grand_total + $data[0]->round_off_amount; ?>">
                                        <input type="hidden" name="total_other_amount" id="total_other_amount" value="<?= $data[0]->total_other_amount; ?>">
                                        <input type="hidden" name="total_other_taxable_amount" id="total_other_taxable_amount" value="<?= $data[0]->total_other_taxable_amount; ?>">
                                        <?php
                                        $charges_sub_module = 0;

                                        foreach ($access_sub_modules as $key => $value) {

                                            if (isset($charges_sub_module_id)) {

                                                if ($charges_sub_module_id == $value) {
                                                    $this->load->view('sub_modules/charges_sub_module');
                                                    $charges_sub_module = 1;
                                                }
                                            }
                                        }
                                        if ($charges_sub_module == 1) {
                                            ?>
                                            <input type="hidden" name="total_freight_charge" id="total_freight_charge" value="<?php echo $data[0]->total_freight_charge; ?>">
                                            <input type="hidden" name="total_insurance_charge" id="total_insurance_charge" value="<?php echo $data[0]->total_insurance_charge; ?>">
                                            <input type="hidden" name="total_packing_charge" id="total_packing_charge" value="<?php echo $data[0]->total_packing_charge; ?>">
                                            <input type="hidden" name="total_incidental_charge" id="total_incidental_charge" value="<?php echo $data[0]->total_incidental_charge; ?>">
                                            <input type="hidden" name="total_other_inclusive_charge" id="total_other_inclusive_charge" value="<?php echo $data[0]->total_inclusion_other_charge; ?>">
                                            <input type="hidden" name="total_other_exclusive_charge" id="total_other_exclusive_charge" value="<?php echo $data[0]->total_exclusion_other_charge; ?>">
                                        <?php } ?>


                                        <!-- Hidden Field -->
                                        <table id="table-total" class="table-striped table-bordered table-condensed table-hover table-responsive">
                                            <tr>
                                                <td align="right">Subtotal (+)</td>
                                                <td align='right'><span id="totalSubTotal"><?php echo precise_amount($data[0]->sales_credit_note_sub_total); ?></span></td>
                                            </tr>
                                            <tr <?= ($data[0]->sales_credit_note_discount_amount <= 0 ? 'style="display: none;"' : '' ); ?> class='totalDiscountAmount_tr'>
                                                <td align="right">Total Discount (-)</td>
                                                <td align='right'><span id="totalDiscountAmount"><?php echo precise_amount($data[0]->sales_credit_note_discount_amount); ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ($igst_exist <= 0 ? 'style="display: none;"' : ''); ?> class='totalIGSTAmount_tr'>
                                                <td align="right">IGST (+)</td>
                                                <td align='right'><span id="totalIGSTAmount"><?= ($igst_exist > 0 ? precise_amount($data[0]->sales_credit_note_igst_amount) : ''); ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ($cgst_exist <= 0 ? 'style="display: none;"' : ''); ?> class='totalCGSTAmount_tr'>
                                                <td align="right">CGST (+)</td>
                                                <td align='right'><span id="totalCGSTAmount"><?= ($cgst_exist > 0 ? precise_amount($data[0]->sales_credit_note_cgst_amount) : ''); ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ($sgst_exist <= 0 ? 'style="display: none;"' : ''); ?> class='totalSGSTAmount_tr'>
                                                <td align="right"><?= ($is_utgst == '1' ? 'UTGST' : 'SGST') ?> (+)</td>
                                                <td align='right'><span id="totalSGSTAmount"><?= ($sgst_exist > 0 ? precise_amount($data[0]->sales_credit_note_sgst_amount) : ''); ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ($cess_exist <= 0 ? 'style="display: none;"' : ''); ?> class='totalCessAmount_tr'>
                                                <td align="right">Cess (+)</td>
                                                <td align='right'><span id="totalTaxCessAmount"><?= ($cess_exist > 0 ? precise_amount($data[0]->sales_credit_note_tax_cess_amount) : ''); ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ($tds_exist != 1 || $data[0]->sales_credit_note_tds_amount <= 0 ? 'style="display: none;"' : ''); ?> class='tds_amount_tr'>
                                                <td align="right"><?php echo 'TDS Amount'; ?></td>
                                                <td align='right'>
                                                    <span id="totalTdsAmount"><?= precise_amount($data[0]->sales_credit_note_tds_amount); ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ($tds_exist != 1 || $data[0]->sales_credit_note_tcs_amount <= 0 ? 'style="display: none;"' : ''); ?> class='tcs_amount_tr'>
                                                <td align="right"><?php echo 'TCS Amount'; ?> (+)</td>
                                                <td align='right'>
                                                    <span id="totalTcsAmount"><?= precise_amount($data[0]->sales_credit_note_tcs_amount); ?></span>
                                                </td>
                                            </tr>                                        
                                            <?php if ($charges_sub_module == 1) { ?>
                                                <tr id="freight_charge_tr">
                                                    <td align="right">Freight Charge (+)</td>
                                                    <td align='right'>
                                                        <span id="freight_charge"><?php echo precise_amount($data[0]->total_freight_charge); ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="insurance_charge_tr">
                                                    <td align="right">Insurance Charge (+)</td>
                                                    <td align='right'>
                                                        <span id="insurance_charge"><?php echo precise_amount($data[0]->total_insurance_charge); ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="packing_charge_tr">
                                                    <td align="right">Packing & Forwarding Charge (+)</td>
                                                    <td align='right'>
                                                        <span id="packing_charge"><?php echo precise_amount($data[0]->total_packing_charge); ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="incidental_charge_tr">
                                                    <td align="right">Incidental Charge (+)</td>
                                                    <td align='right'>
                                                        <span id="incidental_charge"><?php echo precise_amount($data[0]->total_incidental_charge); ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="other_inclusive_charge_tr">
                                                    <td align="right">Other Inclusive Charge (+)</td>
                                                    <td align='right'>
                                                        <span id="other_inclusive_charge"><?php echo precise_amount($data[0]->total_inclusion_other_charge); ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="other_exclusive_charge_tr">
                                                    <td align="right">Other Exclusive Charge (-)</td>
                                                    <td align='right'>
                                                        <span id="other_exclusive_charge"><?php echo precise_amount($data[0]->total_exclusion_other_charge); ?></span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($access_common_settings[0]->round_off_access == 'yes') { ?>
                                                <tr class="round_off_minus_tr" <?= ($data[0]->round_off_amount <= 0 ? 'style="display: none;"' : ''); ?>>
                                                    <td align="right">Round Off (-)</td>
                                                    <td align='right'>
                                                        <span id="round_off_minus_charge"><?= precise_amount(abs($data[0]->round_off_amount)); ?></span>
                                                    </td>
                                                </tr>
                                                <tr class="round_off_plus_tr" <?= ($data[0]->round_off_amount >= 0 ? 'style="display: none;"' : ''); ?>>
                                                    <td align="right">Round Off (+)</td>
                                                    <td align='right'>
                                                        <span id="round_off_plus_charge"><?= precise_amount(abs($data[0]->round_off_amount)); ?></span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php
                                            $checked = "";
                                            $value_selected = "";
                                            if ($data[0]->round_off_amount > 0 || $data[0]->round_off_amount < 0) {
                                                $checked = "checked";
                                                $grand_total = precise_amount($data[0]->sales_credit_note_grand_total + $data[0]->round_off_amount);
                                                $next_val = ceil($grand_total);
                                                $prev_val = floor($grand_total);
                                                if ($data[0]->round_off_amount < 0) {
                                                    $value_selected = "selected";
                                                }
                                            }
                                            ?>
                                            <!-- Round off -->                                       
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
                                                        ?>>No
                                                    </label>                                                    
                                                </td>
                                            </tr>
                                            <?php
                                            if ($checked == "checked") {
                                                ?>
                                                <tr id="round_off_select">
                                                    <td align="right" width="50%">Select the nearest value</td>
                                                    <td align="right" width="50%">
                                                        <select id="round_off_value" class="form-control" name="round_off_value">
                                                            <?php
                                                            if ($next_val == $prev_val) {
                                                                ?>
                                                                <option value="<?= $next_val ?>"><?= $next_val ?></option>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <option value="<?= $prev_val ?>" <?php
                                                                if ($value_selected == "") {
                                                                    echo "selected";
                                                                }
                                                                ?> ><?= $prev_val ?></option>
                                                                <option value="<?= $next_val ?>" <?= $value_selected ?>><?= $next_val ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <?php
                                            } else {
                                                ?>
                                                <tr style="display: none;" id="round_off_select">
                                                    <td align="right" width="50%">Select the nearest value</td>
                                                    <td align="right" width="50%">
                                                        <select id="round_off_value" name="round_off_value">
                                                        </select>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>
                                                <td align="right">Grand Total (=)</td>
                                                <td align='right'><span id="totalGrandTotal"><?php echo precise_amount($data[0]->sales_credit_note_grand_total); ?></span></td>
                                            </tr>
                                        </table>
                                    </div> </div>
                                <div class="col-sm-12">
                                    <div class="box-footer">
                                        <button type="submit" id="sales_credit_note_submit" name="sales_credit_note_submit" class="btn btn-info">Update</button>
                                        <span class="btn btn-default" id="sale_cancel" onclick="cancel('sales_credit_note')">Cancel</span>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $notes_sub_module = 0;

                            foreach ($access_sub_modules as $key => $value) {

                                if (isset($notes_sub_module_id)) {

                                    if ($notes_sub_module_id == $value) {
                                        $notes_sub_module = 1;
                                    }
                                }
                            }

                            if ($notes_sub_module == 1) {
                                $this->load->view('sub_modules/notes_sub_module');
                            }
                            ?>
                        </div>
                    </form>
                </div>
            </div>                             
        </div>
    </section>
</div>
            

        <?php
$this->load->view('layout/footer');
$this->load->view('customer/customer_modal');
$this->load->view('product/product_modal');
$this->load->view('service/service_modal');
$this->load->view('layout/item_modal');
$this->load->view('category/category_modal');
$this->load->view('subcategory/subcategory_modal');
$this->load->view('tax/tax_modal');
$this->load->view('discount/discount_modal');
/*$this->load->view('service/sac_modal');*/
$this->load->view('product/hsn_modal');
?><?php
if ($charges_sub_module == 1)
{?>
<script src="<?php echo base_url('assets/js/sub_modules/') ?>charges_sub_module.js"></script>
<?php } ?>
<script type="text/javascript">
  var sales_credit_note_data = new Array();
  var branch_state_list = <?php echo json_encode($state); ?>;
  var item_gst = new Array();
  var common_settings_round_off = "<?=$access_common_settings[0]->round_off_access?>";
  var common_settings_amount_precision = "<?=$access_common_settings[0]->amount_precision?>";
  var settings_tax_percentage = "<?=$access_common_settings[0]->tax_split_percentage?>";
  var common_settings_inventory_advanced = "<?=$inventory_access?>";

  var settings_tax_type = "<?=$access_settings[0]->tax_type?>";
  var settings_discount_visible = "<?=$access_settings[0]->discount_visible?>";
  var settings_description_visible = "<?=$access_settings[0]->description_visible?>";
  var settings_tds_visible = "<?=$access_settings[0]->tds_visible?>";
  var settings_item_editable = "<?=$access_settings[0]->item_editable?>";
</script>
<script type="text/javascript">

var sales_data = new Array();
var sales_data = <?php echo json_encode($sales_data); ?>;
console.log(sales_data,1212);
var branch_state_list = <?php echo json_encode($state); ?>;
var item_gst =<?php echo json_encode($item_gst); ?>;
var common_settings_round_off = "<?=$access_common_settings[0]->round_off_access?>";
var common_settings_tax_split = "<?=$access_common_settings[0]->tax_split_equaly?>";
</script>
<script src="<?php echo base_url('assets/js/sales_credit_note/') ?>sales_credit_note.js"></script>
<script src="<?php echo base_url('assets/js/sales_credit_note/'); ?>sales_credit_note_basic_common.js"></script>