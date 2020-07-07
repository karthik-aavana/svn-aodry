<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<style type="text/css">
    .disabled_input { pointer-events: none;cursor: not-allowed;opacity: 0.8;}
</style>
<div class="content-wrapper">   
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php
                echo base_url('auth/dashboard');
                ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php
                echo base_url('expense_bill');
                ?>">expense_bill</a></li>
            <li class="active">Edit expense_bill</li>
        </ol>
    </div>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Expense Bill</h3>
                        <a class="btn btn-sm btn-default pull-right back_button" href1="<?php echo base_url('expense_bill'); ?>">Back</a>
                    </div>
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php
                        echo base_url('expense_bill/edit_expense_bill');
                        ?>" encType="multipart/form-data">                            
                            <div class="row">
                                <input type="hidden" name="expense_bill_id" value="<?= $data[0]->expense_bill_id; ?>">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="date">Invoice Date<span class="validation-color">*</span></label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?= date('d-m-Y',strtotime($data[0]->expense_bill_date)); ?>">
                                            <input type="hidden" id="date_old" name="date_old" value="<?= $data[0]->expense_bill_date; ?>">
                                            <input type="hidden" id="added_date" name="added_date" value="<?= $data[0]->added_date; ?>">
                                            <input type="hidden" id="added_user_id" name="added_user_id" value="<?= $data[0]->added_user_id; ?>">                                   
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                        <span class="validation-color" id="err_date"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="invoice_number">Invoice Number<span class="validation-color">*</span></label>

                                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?= $data[0]->expense_bill_invoice_number; ?>"  <?php
                                        if ($access_settings[0]->invoice_readonly == 'yes') {
                                            echo "readonly";
                                        }
                                        ?>>
                                        <span class="validation-color" id="err_invoice_number"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="supplier">Supplier <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="supplier" name="supplier" style="width: 100%;">
                                            <?php
                                            $is_others = '';
                                             if($data[0]->expense_bill_payee_id == 0){ 
                                                    $is_others = 'disabled="disabled"'; ?>
                                                    <option value="0">Others</option>
                                            <?php
                                            }else{
                                                foreach ($supplier as $key) {

                                                    if ($key->supplier_id == $data[0]->expense_bill_payee_id) {
                                                        if($key->supplier_mobile != ''){
                                                        ?>
                                                        <option value='<?= $key->supplier_id; ?>' selected><?= $key->supplier_name.'('.$key->supplier_mobile.')'; ?></option>
                                                        <?php
                                                        }else{
                                                        ?>
                                                            <option value='<?= $key->supplier_id; ?>' selected><?= $key->supplier_name; ?></option>
                                                        <?php
                                                        }
                                                        break;
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="validation-color" id="err_supplier"><?php echo form_error('supplier'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="type_of_supply">Place of Supply <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="billing_state" name="billing_state" <?=$data[0]->expense_bill_billing_country_id;?>>
                                            <?php
                                            /*if ($data[0]->expense_bill_billing_country_id == $branch[0]->branch_country_id) {*/
                                                foreach ($state as $key => $value) {
                                                    ?>
                                                    <option value="<?= $value->state_id ?>" <?= ($value->state_id == $data[0]->expense_bill_billing_state_id ? "selected='selected'" : ''); ?> utgst='<?= $value->is_utgst; ?>'><?= $value->state_name ?></option>
                                                    <?php
                                                }
                                           /* }*/
                                            ?>
                                            <option value="0" <?= ($data[0]->expense_bill_billing_state_id == '0' ? 'selected' : '') ?>>Out of Country</option>
                                        </select>
                                        <span class="validation-color" id="err_billing_state"><?= form_error('billing_state'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3" style="display: none;">
                                    <div class="form-group">
                                        <label for="type_of_supply">Billing Country <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="billing_country" name="billing_country">
                                            <?php
                                            foreach ($country as $key => $value) {
                                                ?>
                                                <option value="<?= $value->country_id ?>" <?php
                                                if ($value->country_id == $data[0]->expense_bill_billing_country_id) {
                                                    echo "selected='selected'";
                                                }
                                                ?> ><?= $value->country_name ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                        </select>
                                        <span class="validation-color" id="err_billing_country"><?php
                                            echo form_error('billing_country');
                                            ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group disabled_input">
                                        <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="type_of_supply" name="type_of_supply">
                                            <?php if ($data[0]->expense_bill_type_of_supply == 'inter_state' || $data[0]->expense_bill_type_of_supply == 'intra_state') { ?>
                                                <option value="inter_state" <?= ($data[0]->expense_bill_type_of_supply == 'inter_state' ? "selected" : ''); ?>>Regular(Inter State)</option>
                                                <option value="intra_state" <?= ($data[0]->expense_bill_type_of_supply == 'intra_state' ? "selected" : ''); ?>>Regular(Intra State)</option>
                                            <?php } else {
                                                ?>
                                                <option value="import" selected>Import</option>
                                            <?php } ?>
                                        </select>
                                        <span class="validation-color" id="err_type_supply"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3 gst_payable_div">
                                    <div class="form-group" <?= ($data[0]->expense_bill_type_of_supply == 'import' ? 'style="display:none;"' : '') ?>>
                                        <label for="gst_payable">GST Payable(Reverse Charge) <span class="validation-color">*</span></label>
                                        <br/>
                                        <label class="radio-inline">
                                            <input type="radio" name="gst_payable" value="yes" class="minimal"  <?php
                                            if ($data[0]->expense_bill_gst_payable == "yes") {
                                                echo 'checked="checked"';
                                            }
                                            ?> /> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="gst_payable" value="no" class="minimal" <?php
                                            if ($data[0]->expense_bill_gst_payable == "no") {
                                                echo 'checked="checked"';
                                            }
                                            ?> /> No
                                        </label>
                                        <br/>
                                        <span class="validation-color" id="err_gst_payable"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="supplier_ref">Supplier Invoice Number</label>
                                        <input type="text" class="form-control" id="supplier_ref" name="supplier_ref" value="<?= $data[0]->expense_bill_supplier_invoice_number ?>">
                                        <span class="validation-color" id="err_supplier_ref"><?php echo form_error('supplier_ref'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="supplier_ref">Supplier Invoice Date</label>
                                        <?php
                                        $expense_bill_supplier_date = explode('-', $data[0]->expense_bill_supplier_date);

                                        if ($expense_bill_supplier_date[0] > 0) {
                                            $expense_bill_supplier_date = $data[0]->expense_bill_supplier_date;
                                        } else {
                                            $expense_bill_supplier_date = "";
                                        }
                                        if ($expense_bill_supplier_date == '1970-01-01')
                                            $expense_bill_supplier_date = "";
                                        ?>
                                        <div class="input-group date">
                                        <input type="text" class="form-control datepicker" id="supplier_date" name="supplier_date" value="<?=($expense_bill_supplier_date != '' ? date('d-m-Y',strtotime($expense_bill_supplier_date)) : ''); ?>">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                        <span class="validation-color" id="err_supplier_supplier_date"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-8">
                                    <span class="validation-color" id="err_product"></span>
                                </div>
                               <!--  <div class="col-sm-12 search_expense_code">
                                    <input id="input_expense_code" class="form-control" type="text" name="input_expense_code" placeholder="Enter Product/Service Code/Name" >
                                </div> -->
                                <div class="col-sm-12">
                                    <span class="validation-color" id="err_expense_bill_code"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="box-header with-border">
                                        <h3 class="box-title ml-0">Inventory Items</h3>                                        
                                        <table class="table table-striped table-bordered table-condensed table-hover expense_bill_table table-responsive" id="expense_data">
                                            <thead>
                                                <tr>
                                                    <th width="2%"><img src="<?= base_url(); ?>assets/images/bin1.png" /></th>
                                                    <th class="span2">Items</th>
                                                    <?php
                                                    if ($access_settings[0]->description_visible == 'yes') {
                                                        ?>
                                                        <th class="span2">Description</th>
                                                    <?php } ?>

                                                    <th class="span2" width="6%">Quantity</th>
                                                    <th class="span2" width="11%">Rate</th>
                                                    <?php
                                                    if ($access_settings[0]->discount_visible == 'yes') {
                                                        ?>
                                                        <th class="span2" width="9%">Discount
                                                            <?php if (in_array($discount_module_id, $active_add)) { ?>
        <!--                                                                <a href="" data-toggle="modal" data-target="#discount_modal"><strong>+</strong></a>-->
                                                                <?php
                                                            }
                                                            ?>
                                                        </th>
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
                                                        <th class="span2" width="9%">GST(%) </th>
                                                        <th class="span2" width="9%">Cess(%) </th>
                                                    <?php } ?>
                                                    <th class="span2" width="11%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="expense_table_body">
                                                <?php
                                                $i = 0;
                                                $tot = 0;
                                                foreach ($items as $key) {
                                                    ?>
                                                    <tr id="<?= $i ?>">
                                                        <td><a class='deleteRow'> <img src='<?= base_url(); ?>assets/images/bin_close.png' /></a><input type='hidden' name='item_key_value'  value="<?= $i ?>"><input type='hidden' name='item_id' value="<?= $key->item_id; ?>"><input type='hidden' name='item_type' value="product"></td>
                                                        <td><input type='hidden' name='item_code' value='<?= $key->product_code; ?>'>
                                                            <?= $key->product_name; ?>
                                                            <?php if($key->expense_hsn_code != ''){ ?>
                                                            <br>(P) (HSN/SAC: 
                                                                <?php echo $key->expense_hsn_code; ?>)
                                                            <?php } ?>
                                                        </td>
                                                        <?php
                                                        if ($access_settings[0]->description_visible == 'yes') {
                                                            ?>
                                                            <td>
                                                                <input type='text' class='form-control form-fixer' name='item_description' value='<?= $key->expense_bill_item_description; ?>'>
                                                            </td>
                                                        <?php } ?>
                                                        <td>
                                                            <input type='text' class='form-control form-fixer text-center float_number' value='<?php
                                                            echo $key->expense_bill_item_quantity ? $key->expense_bill_item_quantity : 0;
                                                            ?>' data-rule='quantity' name='item_quantity'>
                                                        </td>
                                                        <td>
                                                            <input type='text' class='form-control form-fixer text-right float_number' name='item_price' value='<?php
                                                            echo $key->expense_bill_item_unit_price ? precise_amount($key->expense_bill_item_unit_price) : 0;
                                                            ?>'><span id='item_sub_total_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?php
                                                                   echo $key->expense_bill_item_sub_total ? precise_amount($key->expense_bill_item_sub_total) : 0;
                                                                   ?></span><input type='hidden' class='form-control form-fixer text-right' style='' value='<?php
                                                                echo $key->expense_bill_item_sub_total ? precise_amount($key->expense_bill_item_sub_total) : 0;
                                                                ?>' name='item_sub_total'>
                                                        </td>
                                                        <?php if ($access_settings[0]->discount_visible == 'yes') { ?>
                                                            <td>
                                                                <input type='hidden' name='item_discount_id' value='<?php
                                                                echo $key->expense_bill_item_discount_id ? $key->expense_bill_item_discount_id : 0;
                                                                ?>'>
                                                                <input type='hidden' name='item_discount_percentage' value='<?= ($key->item_discount_percentage ? (float)($key->item_discount_percentage) : 0); ?>'>
                                                                <input type='hidden' name='item_discount_amount' value='<?= ($key->expense_bill_item_discount_amount ? precise_amount($key->expense_bill_item_discount_amount) : 0); ?>'>
        
                                                                <div class="form-group" style="margin-bottom:0px !important;">
                                                                    <select class="form-control open_discount form-fixer select2" name="item_discount" <?php echo $is_others ?> style="width: 100%;">
                                                                        <option value="">Select</option>
                                                                        <?php
                                                                        foreach ($discount as $key3 => $value3) {
                                                                            if ($value3->discount_id == $key->expense_bill_item_discount_id) {
                                                                                echo "<option value='" . $value3->discount_id . "-" .  (float)($value3->discount_value) . "' selected>" . (float)($value3->discount_value) . "%</option>";
                                                                            } else {
                                                                                echo "<option value='" . $value3->discount_id . "-" . (float)($value3->discount_value) . "'>" . (float)($value3->discount_value) . "%</option>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <span id='item_discount_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?php
                                                                    echo $key->expense_bill_item_discount_amount ? precise_amount($key->expense_bill_item_discount_amount) : 0;
                                                                    ?></span>
                                                            </td>
                                                        <?php } ?>
                                                        <!-- tax area -->
                                                        <td align='right'>
                                                            <input type='hidden' name='item_taxable_value' value='<?= ($key->expense_bill_item_taxable_value ? $key->expense_bill_item_taxable_value : 0); ?>' >
                                                            <span id='item_taxable_value_lbl_<?= $i ?>'><?= $key->expense_bill_item_taxable_value ? precise_amount($key->expense_bill_item_taxable_value) : 0; ?></span>
                                                        </td>
                                                        <?php if ($access_settings[0]->tds_visible == 'yes') { ?>
                                                            <td style='text-align:center'>
                                                                <?php
                                                                if ($key->tds_module_type == "" || $key->tds_module_type == null) {
                                                                    $input_type = "hidden";
                                                                    ?>
                                                                    <!-- <span id='item_tds_percentage_hide_lbl_<?= $i ?>' class='text-center' ><?= $key->expense_bill_item_tds_percentage ? precise_amount($key->expense_bill_item_tds_percentage) : 0 ?></span> -->
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
                                                                                if (($value3->tax_name == 'TDS')) {

                                                                                    if ($value3->tax_id == $key->expense_bill_item_tds_id) {
                                                                                        $tds_per = (float)($value3->tax_value);
                                                                                    }
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td><?= $value3->tax_name . '(Sec ' . $value3->section_name . ') @ ' . (float)($value3->tax_value); ?>%</td>
                                                                                        <td><div class="radio">
                                                                                                <label><input type="radio" name="tds_tax" value="<?= (float)($value3->tax_value); ?> "<?= ($value3->tax_id == $key->expense_bill_item_tds_id ? 'selected' : '' ) ?> tds_id='<?= $value3->tax_id ?>' typ='<?= $value3->tax_name ?>'></label>
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
                                                                <input type="text" class="form-control open_tds_modal pointer" name="item_tds_percentage" <?php echo $is_others;?> value="<?= $tds_per ?>%" readonly>

                                                                <input type='hidden' name='item_tds_id' value='<?= $key->expense_bill_item_tds_id ? $key->expense_bill_item_tds_id : 0 ?>'><input type='hidden' name='item_tds_type' value='<?= $key->tds_module_type ? $key->tds_module_type : '' ?>'>
                                                                <input type='hidden' name='item_tds_amount' value='<?= $key->expense_bill_item_tds_amount ? precise_amount($key->expense_bill_item_tds_amount) : 0 ?>'><span id='item_tds_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?= $key->expense_bill_item_tds_amount ? precise_amount($key->expense_bill_item_tds_amount) : 0 ?></span>
                                                            </td>                                                          
    <?php } ?>
    <?php if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') { ?>
                                                            <td><input type='hidden' name='item_tax_id' value='<?= $key->expense_bill_item_tax_id ? $key->expense_bill_item_tax_id : 0 ?>'>
                                                                <input type='hidden' name='item_tax_percentage' value='<?= $key->expense_bill_item_tax_percentage ? (float)($key->expense_bill_item_tax_percentage) : 0 ?>'>
                                                                <input type='hidden' name='item_tax_amount_cgst' value='0'>
                                                                <input type='hidden' name='item_tax_amount_sgst' value='0'>
                                                                <input type='hidden' name='item_tax_amount_igst' value='0'>
                                                                <input type='hidden' name='item_tax_amount' value='<?= $key->expense_bill_item_tax_amount ? precise_amount($key->expense_bill_item_tax_amount) : 0 ?>'>
                                                                <div class="form-group" style="margin-bottom:0px !important;">
                                                                    <select class="form-control open_tax form-fixer select2" name="item_tax" <?php echo $is_others; ?> style="width: 100%;">
                                                                        <option value="">Select</option>
                                                                        <?php
                                                                        foreach ($tax as $key3 => $value3) {
                                                                            if ($value3->tax_name == 'GST') {

                                                                                echo "<option value='" . $value3->tax_id . "-" . (float)($value3->tax_value) . "' " . ($value3->tax_id == $key->expense_bill_item_tax_id ? 'selected' : '' ) . ">" . (float)($value3->tax_value) . "%</option>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <span id='item_tax_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?= $key->expense_bill_item_tax_amount ? precise_amount($key->expense_bill_item_tax_amount) : 0 ?></span>
                                                            </td>
                                                            <td><input type='hidden' name='item_tax_cess_id' value='<?= ($key->expense_bill_item_tax_cess_id ? $key->expense_bill_item_tax_cess_id : 0); ?>'>
                                                                <input type='hidden' name='item_tax_cess_percentage' value='<?= $key->expense_bill_item_tax_cess_percentage ? (float)($key->expense_bill_item_tax_cess_percentage) : 0; ?>'>
                                                                <input type='hidden' name='item_tax_cess_amount' value='<?= $key->expense_bill_item_tax_cess_amount ? precise_amount($key->expense_bill_item_tax_cess_amount) : 0 ?>'>
                                                                <div class="form-group" style="margin-bottom:0px !important;">
                                                                    <select class="form-control open_tax form-fixer select2" name="item_tax_cess" <?php echo $is_others; ?> style="width: 100%;" <?= $key->expense_bill_item_tax_cess_id; ?>>
                                                                        <option value="">Select</option>
                                                                        <?php
                                                                        foreach ($tax as $key3 => $value3) {
                                                                            if ($value3->tax_name == 'CESS') {
                                                                                echo "<option value='" . $value3->tax_id . "-" . (float)($value3->tax_value) . "' " . ($value3->tax_id == $key->expense_bill_item_tax_cess_id ? 'selected' : '' ) . ">" . (float)($value3->tax_value) . "%</option>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <span id='item_tax_cess_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?= $key->expense_bill_item_tax_cess_amount ? precise_amount($key->expense_bill_item_tax_cess_amount) : 0 ?></span>
                                                            </td>
                                                            <?php } ?>
                                                        <td>
                                                            <input type='text' class='float_number form-control form-fixer text-right' name='item_grand_total' value='<?php
                                                            echo $key->expense_bill_item_grand_total ? precise_amount($key->expense_bill_item_grand_total) : 0;
                                                            ?>'>
                                                        </td>
                                                        <?php
                                                        $expense_bill_temp = array(
                                                            "item_key_value" => $i,
                                                            "item_id" => $key->item_id,
                                                            "item_quantity" => $key->expense_bill_item_quantity,
                                                            "item_price" => $key->expense_bill_item_unit_price ? precise_amount($key->expense_bill_item_unit_price) : 0,
                                                            "item_description" => $key->expense_bill_item_description,
                                                            "item_sub_total" => $key->expense_bill_item_sub_total ? precise_amount($key->expense_bill_item_sub_total) : 0,
                                                            "item_discount_amount" => $key->expense_bill_item_discount_amount ? precise_amount($key->expense_bill_item_discount_amount) : 0,
                                                            "item_discount_id" => $key->expense_bill_item_discount_id ? $key->expense_bill_item_discount_id : 0,
                                                            /* "item_discount_percentage" => $key->item_discount_percentage ? precise_amount($key->item_discount_percentage) : 0, */
                                                            "item_tax_amount" => $key->expense_bill_item_tax_amount ? precise_amount($key->expense_bill_item_tax_amount) : 0,
                                                            "item_tax_id" => $key->expense_bill_item_tax_id ? $key->expense_bill_item_tax_id : 0,
                                                            "item_tax_cess_amount" => $key->expense_bill_item_tax_cess_amount ? precise_amount($key->expense_bill_item_tax_cess_amount) : 0,
                                                            "item_tax_cess_id" => $key->expense_bill_item_tax_cess_id ? $key->expense_bill_item_tax_cess_id : 0,
                                                            "item_tax_cess_percentage" => $key->expense_bill_item_tax_cess_percentage ? precise_amount($key->expense_bill_item_tax_cess_percentage) : 0,
                                                            "item_tax_percentage" => $key->expense_bill_item_tax_percentage ? precise_amount($key->expense_bill_item_tax_percentage) : 0,
                                                            "item_tds_amount" => $key->expense_bill_item_tds_amount ? precise_amount($key->expense_bill_item_tds_amount) : 0,
                                                            "item_tds_id" => $key->expense_bill_item_tds_id ? $key->expense_bill_item_tds_id : 0,
                                                            "item_tds_percentage" => $key->expense_bill_item_tds_percentage ? precise_amount($key->expense_bill_item_tds_percentage) : 0,
                                                            "item_taxable_value" => $key->expense_bill_item_taxable_value ? precise_amount($key->expense_bill_item_taxable_value) : 0,
                                                            "item_grand_total" => $key->expense_bill_item_grand_total ? precise_amount($key->expense_bill_item_grand_total) : 0
                                                        );
                                                        $expense_bill_temp['item_code'] = $key->product_code;
                                                        ?>
                                                    </tr>
                                                    <?php
                                                    $expense_bill_data[$i] = $expense_bill_temp;
                                                    $i++;
                                                }
                                                $expense_bill = htmlspecialchars(json_encode($expense_bill_data));
                                                $countData = $i;
                                                ?>
                                                 <tr id="0">
                                                    <td colspan="2">
                                                        <input id="input_expense_code" class="form-control" type="text" name="input_expense_code" placeholder="Enter Product/Service Code/Name">
                                                    </td>
                                                    <?php 
                                                   if($access_settings[0]->description_visible == 'yes'){
                                                   ?>
                                                    <td>
                                                        <input type="text" class="form-control form-fixer" name="item_description" autocomplete="off">
                                                    </td>
                                                    <td style="text-align:center">
                                                        <input type="text" class="form-control form-fixer text-center float_number" value="1" data-rule="quantity" name="item_quantity">
                                                    </td>
                                                    <?php } ?>
                                                    <td>
                                                        <input type="text" class="form-control form-fixer text-right float_number" name="item_price" value="0.00"><span id="item_sub_total_lbl_0" class="pull-right">0.00</span>
                                                    </td>
                                                    <?php
                                                    if ($access_settings[0]->discount_visible == 'yes') {
                                                        ?>
                                                    <td>
                                                        <div class="form-group" style="margin-bottom:0px !important;">
                                                            <select class="form-control open_discount form-fixer select2 select2-hidden-accessible" name="item_discount" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                                <option value="">Select</option>
                                                            </select>
                                                        </div><span id="item_discount_lbl_0" class="pull-right" style="color:red;">0.00</span>
                                                    </td>
                                                    <?php } ?>
                                                    <?php
                                                    if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') {
                                                        if ($access_settings[0]->discount_visible == 'yes'){ ?>
                                                    <td style="text-align:center">
                                                        <span id="item_taxable_value_lbl_0">0.00</span></td>
                                                        <?php } ?>
                                                    <?php } ?>
                                                     <?php if ($access_settings[0]->tds_visible == 'yes') { ?>
                                                    <td>
                                                        <input type="text" class="form-control pointer" name="item_tds_percentage" value="0%" readonly=""><span id="item_tds_lbl_0" class="pull-right" style="color:red;">0.00</span></td>
                                                    <?php }?>
                                                    <?php if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') { ?>
                                                        <?php if ($access_settings[0]->gst_visible == 'yes') { ?>
                                                            <td>
                                                                <div class="form-group" style="margin-bottom:0px !important;">
                                                                    <select class="form-control open_tax form-fixer select2 select2-hidden-accessible" name="item_tax" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                                        <option value="">Select</option>
                                                                    </select>
                                                                </div><span id="item_tax_lbl_0" class="pull-right" style="color:red;">0.00</span>
                                                            </td>
                                                        <td>
                                                            <div class="form-group" style="margin-bottom:0px !important;">
                                                                <select class="form-control open_tax form-fixer select2 select2-hidden-accessible" name="item_tax_cess" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                                    <option value="">Select</option>
                                                                </select>
                                                            </div><span id="item_tax_cess_lbl_0" class="pull-right" style="color:red;">0.00</span>
                                                        </td>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <td>
                                                        <input type="text" class="float_number form-control form-fixer text-right" name="item_grand_total">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php
                                                echo $branch[0]->branch_country_id;
                                                ?>">
                                        <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php
                                        echo $branch[0]->branch_state_id;
                                                ?>">
                                        <input type="hidden" class="form-control" id="product_module_id" name="product_module_id" value="<?= $product_module_id ?>" >
                                        <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo $data[0]->expense_bill_date; ?>" >
                                        <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $expense_bill_module_id ?>" >
                                        <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" >
                                        <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $data[0]->expense_bill_invoice_number ?>" >
                                        <input type="hidden" class="form-control" id="section_area" name="section_area" value="edit_expense_bill" >
                                        <input type="hidden" name="total_sub_total" id="total_sub_total" value="<?= precise_amount($data[0]->expense_bill_sub_total); ?>">
                                        <input type="hidden" name="total_taxable_amount" id="total_taxable_amount" value="<?= precise_amount($data[0]->expense_bill_taxable_value); ?>">
                                        <input type="hidden" name="total_other_taxable_amount" id="total_other_taxable_amount" value="<?= $data[0]->total_other_taxable_amount; ?>">
                                        <input type="hidden" name="without_reound_off_grand_total" id="without_reound_off_grand_total">
                                        <input type="hidden" name="total_discount_amount" id="total_discount_amount" value="<?= precise_amount($data[0]->expense_bill_discount_amount); ?>">
                                        <input type="hidden" name="total_tax_amount" id="total_tax_amount" value="<?= precise_amount($data[0]->expense_bill_tax_amount); ?>">
                                        <input type="hidden" name="total_tax_cess_amount" id="total_tax_cess_amount" value="<?= precise_amount($data[0]->expense_bill_tax_cess_amount); ?>">
                                        <input type="hidden" name="total_igst_amount" id="total_igst_amount" value="<?= precise_amount($data[0]->expense_bill_igst_amount); ?>">
                                        <input type="hidden" name="total_cgst_amount" id="total_cgst_amount" value="<?= precise_amount($data[0]->expense_bill_cgst_amount); ?>">
                                        <input type="hidden" name="total_sgst_amount" id="total_sgst_amount" value="<?= precise_amount($data[0]->expense_bill_cgst_amount); ?>">
                                        <input type="hidden" name="total_tds_amount" id="total_tds_amount" value="<?= precise_amount($data[0]->expense_bill_tds_amount); ?>">
                                        <input type="hidden" name="total_grand_total" id="total_grand_total" value="<?= precise_amount($data[0]->expense_bill_grand_total + $data[0]->round_off_amount); ?>">
                                        <input type="hidden" name="table_data" id="table_data" value="<?= $expense_bill; ?>">
                                        <input type="hidden" name="total_other_amount" id="total_other_amount" value="<?= precise_amount($data[0]->total_other_amount); ?>">
                                        <?php
                                        $charges_sub_module = 0;
                                        if (in_array($charges_sub_module_id, $access_sub_modules) || ($data[0]->total_freight_charge > 0 || $data[0]->total_insurance_charge > 0 || $data[0]->total_packing_charge > 0 || $data[0]->total_incidental_charge > 0 || $data[0]->total_inclusion_other_charge > 0 || $data[0]->total_exclusion_other_charge > 0)) {
                                            $this->load->view('sub_modules/charges_sub_module');
                                            $charges_sub_module = 1;
                                        }
                                        if ($charges_sub_module == 1) {
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
                                                   <?php
                                               }
                                               ?>
                                               <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="file_upload"><h4>File upload</h4></label>
                                                     <?php
                                                    if (!isset($data[0]->expense_file) || $data[0]->expense_file == "") {
                                                        ?>
                                                        <input type="file" class="form-control" id="expense_file" name="expense_file" >
                                                        <?php } ?>
                                                        <div id="logo_pic">
                                            <?php
                                            if (isset($data[0]->expense_file) && $data[0]->expense_file) {
                                                    $file_name = $data[0]->expense_file;
                                                    $img = substr(strrchr($file_name,'.'),1);
                                                    if($img == 'pdf' || $img == 'PDF'){
                                                        ?>
                                                        <a href="<?php echo base_url('assets/images/').'BRANCH-'.$data[0]->branch_id.'/Expense/'.$data[0]->expense_file; ?>" target="_blank">
                                                <img src="<?=base_url();?>assets/images/pdf_thumb.jpg" width="18%" id="logo_img"></a>
                                                <a href="" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#remove_logo" title="Remove Logo" ><i class="fa fa-trash-o text-purple"></i></a>
                                                        <?php
                                                    }else{
                                                ?>
                                                <a href="<?php echo base_url('assets/images/').'BRANCH-'.$data[0]->branch_id.'/Expense/'.$data[0]->expense_file; ?>" target="_blank"><img src="<?php echo base_url('assets/images/').'BRANCH-'.$data[0]->branch_id.'/Expense/'.$data[0]->expense_file; ?>" width="18%" id="logo_img"></a>
                                                <a href="" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#remove_logo" title="Remove Logo" ><i class="fa fa-trash-o text-purple"></i></a>
                                            <?php } } ?>
                                        </div>
                                        <input type="hidden" id="hidden_expense_file" name="hidden_expense_file" value="<?php
                                        if (isset($data[0]->expense_file)) {
                                            echo $data[0]->expense_file;
                                        }
                                        ?>">
                                        <span class="validation-color" id="error_expence_file"></span>
                                                </div>
                                            </div>    
                                        </div>
                                        <!-- Hidden Field -->
                                        <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive mt-15">
                                            <tr>
                                                <td align="right">Total Value (+)</td>
                                                <td align='right'><span id="totalSubTotal"><?php
                                               echo precise_amount($data[0]->expense_bill_sub_total);
                                               ?></span></td>
                                            </tr>
                                            <tr <?= ($data[0]->expense_bill_discount_amount <= 0 ? 'style="display: none;"' : '' ); ?> class='totalDiscountAmount_tr'>
                                                <td align="right">Total Discount (-)</td>
                                                <td align='right'><span id="totalDiscountAmount"><?php
                                               echo precise_amount($data[0]->expense_bill_discount_amount);
                                               ?></span>
                                                </td>
                                            </tr>
                                            <!-- <tr <?= ($tax_exist != 1 && $igst_exist != 1 && $cgst_exist != 1 && $sgst_exist != 1 ? 'style="display: none;"' : ''); ?>>
                                              <td align="right">Total Tax (+)</td>
                                              <td align='right'><span id="totalTaxAmount"><?= precise_amount($data[0]->expense_bill_tax_amount); ?></span>
                                              </td>
                                            </tr> -->
                                            <tr <?= ($cgst_exist != 1 || $data[0]->expense_bill_cgst_amount <= 0 ? 'style="display: none;"' : ''); ?> class='totalCGSTAmount_tr'>
                                                <td align="right">CGST (+)</td>
                                                <td align='right'><span id="totalCGSTAmount"><?= precise_amount($data[0]->expense_bill_cgst_amount); ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ($sgst_exist != 1 || $data[0]->expense_bill_sgst_amount <= 0 ? 'style="display: none;"' : ''); ?> class='totalSGSTAmount_tr'>
                                                <?php
                                                $lbl = 'SGST';
                                                if ($is_utgst == '1')
                                                    $lbl = 'UTGST';
                                                ?>
                                                <td align="right <?= $data[0]->expense_bill_billing_state_id; ?>"><?= $lbl; ?> (+)</td>
                                                <td align='right'><span id="totalSGSTAmount"><?= precise_amount($data[0]->expense_bill_sgst_amount); ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ($igst_exist != 1 || $data[0]->expense_bill_igst_amount <= 0 ? 'style="display: none;"' : ''); ?> class='totalIGSTAmount_tr'>
                                                <td align="right">IGST (+)</td>
                                                <td align='right'><span id="totalIGSTAmount"><?= precise_amount($data[0]->expense_bill_igst_amount); ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ($cess_exist != 1 || $data[0]->expense_bill_tax_cess_amount <= 0 ? 'style="display: none;"' : ''); ?> class='totalCessAmount_tr'>
                                                <td align="right">Cess (+)</td>
                                                <td align='right'><span id="totalTaxCessAmount"><?= precise_amount($data[0]->expense_bill_tax_cess_amount); ?></span>
                                                </td>
                                            </tr>
                                            <tr <?= ($tds_exist != 1 || $data[0]->expense_bill_tds_amount <= 0 ? 'style="display: none;"' : ''); ?> class='tds_amount_tr'>
                                                <td align="right"><?php echo 'TDS Amount'; ?> </td>
                                                <td align='right'>
                                                    <span id="totalTdsAmount"><?= precise_amount($data[0]->expense_bill_tds_amount); ?></span>
                                                </td>
                                            </tr>

<?php if ($charges_sub_module == 1) { ?>
                                                <tr id="freight_charge_tr">
                                                    <td align="right">Freight Charge (+)</td>
                                                    <td align='right'>
                                                        <span id="freight_charge"><?= precise_amount($data[0]->total_freight_charge); ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="insurance_charge_tr">
                                                    <td align="right">Insurance Charge (+)</td>
                                                    <td align='right'>
                                                        <span id="insurance_charge"><?php
    echo precise_amount($data[0]->total_insurance_charge);
    ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="packing_charge_tr">
                                                    <td align="right">Packing & Forwarding Charge (+)</td>
                                                    <td align='right'>
                                                        <span id="packing_charge"><?php
    echo precise_amount($data[0]->total_packing_charge);
    ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="incidental_charge_tr">
                                                    <td align="right">Incidental Charge (+)</td>
                                                    <td align='right'>
                                                        <span id="incidental_charge"><?php
    echo precise_amount($data[0]->total_incidental_charge);
    ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="other_inclusive_charge_tr">
                                                    <td align="right">Other Inclusive Charge (+)</td>
                                                    <td align='right'>
                                                        <span id="other_inclusive_charge"><?php
    echo precise_amount($data[0]->total_inclusion_other_charge);
    ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="other_exclusive_charge_tr">
                                                    <td align="right">Other Exclusive Charge (-)</td>
                                                    <td align='right'>
                                                        <span id="other_exclusive_charge"><?php
    echo precise_amount($data[0]->total_exclusion_other_charge);
    ?></span>
                                                    </td>
                                                </tr>
<?php } ?>
<?php if ($access_common_settings[0]->round_off_access == 'yes') { ?>
                                                <tr class="round_off_minus_tr" <?= ($data[0]->round_off_amount >= 0 ? 'style="display: none;"' : ''); ?>>
                                                    <td align="right">Round Off (-)</td>
                                                    <td align='right'>
                                                        <span id="round_off_minus_charge"><?= precise_amount($data[0]->round_off_amount); ?></span>
                                                    </td>
                                                </tr>
                                                <tr class="round_off_plus_tr" <?= ($data[0]->round_off_amount <= 0 ? 'style="display: none;"' : ''); ?>>
                                                    <td align="right">Round Off (+)</td>
                                                    <td align='right'>
                                                        <span id="round_off_plus_charge"><?= precise_amount($data[0]->round_off_amount); ?></span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php
                                            $checked = "";
                                            $value_selected = "";
                                            if ($data[0]->round_off_amount > 0 || $data[0]->round_off_amount < 0) {
                                                $checked = "checked";
                                                $grand_total = precise_amount($data[0]->expense_bill_grand_total + $data[0]->round_off_amount);
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
                                                        <input type="radio" name="round_off_key" value="no" class="minimal" <?php
                                                        if ($checked == "") {
                                                            echo "checked";
                                                        }
                                                        ?>>No
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="round_off_key" value="yes" class="minimal" <?= $checked ?>>yes
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
<?php } else {
    ?>
                                                <tr style="display: none;" id="round_off_select">
                                                    <td align="right" width="50%">Select the nearest value</td>
                                                    <td align="right" width="50%">
                                                        <select id="round_off_value" name="round_off_value">
                                                        </select>
                                                    </td>
                                                </tr>
<?php } ?>
                                            <tr>
                                                <td align="right">Grand Total (=)</td>
                                                <td align='right'><span id="totalGrandTotal"><?= precise_amount($data[0]->expense_bill_grand_total + $data[0]->round_off_amount); ?></span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if ($tax_exist == 1) {
                                $settings_tax_type = "single_tax";
                            } else
                            if ($igst_exist == 1 || $cgst_exist == 1 || $sgst_exist == 1) {
                                $settings_tax_type = "gst";
                            } else {
                                $settings_tax_type = "no_tax";
                            }
                            if ($discount_exist == 1) {
                                $discount_visible = "yes";
                            } else {
                                $discount_visible = "no";
                            }
                            if ($description_exist == 1) {
                                $description_visible = "yes";
                            } else {
                                $description_visible = "no";
                            }
                            if ($tds_exist == 1) {
                                $tds_visible = "yes";
                            } else {
                                $tds_visible = "no";
                            }
                            ?>
                            <input type="hidden" name="tax_type" id="tax_type" value="<?= $settings_tax_type ?>">
                            <div class="box-footer">
                                <button type="submit" id="expense_bill_submit" name="expense_bill_submit" class="btn btn-info">Update</button>
                                <span class="btn btn-default" id="sale_cancel" onclick="cancel('expense_bill')">Cancel</span>
                            </div>
                            <?php
                            $notes_sub_module = 0;
                            if (in_array($notes_sub_module_id, $access_sub_modules)) {
                                $notes_sub_module = 1;
                            }
                            if ($notes_sub_module == 1) {
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
<div id="remove_logo" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <form id="logoForm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Expense Bill File</h4>
                </div>
                <div class="modal-body">
                    <label for="category_name">Are your sure! You want to Delete Expense Bill Image ?</label>
                </div>
                <div class="modal-footer">
                    <a href="<?php echo base_url('expense_bill/remove_image/' . $data[0]->expense_bill_id); ?>" class="btn btn-sm btn-info pull-right" style="margin-left: 10px;">Yes</a>
                    <button class="btn btn-sm btn-submit pull-right" data-dismiss="modal">No</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('supplier/supplier_modal');
$this->load->view('tax/tax_modal');
$this->load->view('discount/discount_modal');
if ($charges_sub_module == 1) {
    ?>
    <script src="<?php echo base_url('assets/js/sub_modules/'); ?>charges_sub_module.js"></script>
<?php } ?>
<script type="text/javascript">
// var expense_bill_data = new Array();
    var expense_data = <?php echo json_encode($expense_bill_data); ?>;
    var branch_state_list = <?php echo json_encode($state); ?>;
    var count_data = <?= $countData; ?>;
    var discount_ary = <?php echo json_encode($discount); ?>;
    var tax_ary = <?php echo json_encode($tax); ?>;
    var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
    var common_settings_amount_precision = "<?= $access_common_settings[0]->amount_precision ?>";
    var settings_tax_percentage = "<?= $access_common_settings[0]->tax_split_percentage ?>";
    var settings_tax_type = "<?= $access_settings[0]->tax_type ?>";
    var common_settings_inventory_advanced = "";
    var settings_discount_visible = "<?= $access_settings[0]->discount_visible ?>";
    var settings_description_visible = "<?= $access_settings[0]->description_visible ?>";
    var settings_tds_visible = "<?= $access_settings[0]->tds_visible ?>";
    var settings_item_editable = "<?= $access_settings[0]->item_editable ?>";
</script>
<script src="<?php echo base_url('assets/js/expense/'); ?>expense.js"></script>
<script src="<?php echo base_url('assets/js/expense/'); ?>expense_basic_common.js"></script>
<style type="text/css">
    .autocomplete-suggestions {width: 800px !important;text-overflow: initial !important; overflow-x: visible;}
    .autocomplete-suggestions .autocomplete-suggestion{width: 750px !important;text-overflow: initial !important;overflow-x: visible; }
    .autocomplete-suggestions span.stock_span{color : red;float: right;}
    #discount_modal{
        color: red;
        float: right;
        margin: 0 !important;
        position: relative;
        top: -25px;
        right: 0;
        font-size: 20px;
    }

    a.gst_plus,a.discount_plus{    
        color: red;
        float: right;
        margin: 0 !important;
        position: relative;
        top: -25px;
        right: 0;
        font-size: 20px;
    }
    table tr th a.gst_plus:hover, table tr th a.gst_plus:focus,table tr th a.discount_plus:hover, table tr th a.discount_plus:focus{
        color: red;
    }

</style>