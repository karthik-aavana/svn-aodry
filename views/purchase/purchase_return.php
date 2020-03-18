<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// $p = array('admin', 'purchaser', 'manager');
// if (!(in_array($this->session->userdata('type'), $p))) {
//     redirect('auth');
// }
$this->load->view('layout/header');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('purchase'); ?>">Purchase</a></li>
            <li class="active">Add Purchase Return</li>
        </ol>
        </h5>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Purchase Return</h3>
                        <a class="btn btn-default pull-right" id="cancel" onclick="cancel('purchase')">Back</a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post" action="<?php echo base_url('purchase_return/add_purchase_return'); ?>">
                                <?php foreach ($data as $row)
                                {
                                ?>
                                <div class="col-sm-12">
                                    <div class="well">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="date">Purchase Return Date<span class="validation-color">*</span></label>
                                                    <!-- <div class="input-group"> -->
                                                    <?php
                                                    $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));
                                                    if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1]))
                                                    {
                                                    $date = $financial_year[0] . '-04-01';
                                                    }
                                                    else
                                                    {
                                                    $date = date('Y-m-d');
                                                    }
                                                    ?>
                                                    <input type="text" style="background: #fff;" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?php echo $date; ?>" readonly>
                                                    <!-- <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                </div> -->
                                                <span class="validation-color" id="err_date"><?php echo form_error('date'); ?></span>
                                            </div>
                                        </div>
                                        <!-- <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="reference_no">Reference Number<span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo $invoice_number; ?>" readonly>
                                                <span class="validation-color" id="err_reference_no"><?php echo form_error('reference_no'); ?></span>
                                            </div>
                                        </div> -->
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="reference_no">Reference Number<span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo $invoice_number; ?>" <?php
                                                if ($access_settings[0]->invoice_readonly == 'yes')
                                                {
                                                echo "readonly";
                                                }
                                                ?>>
                                                <span class="validation-color" id="err_invoice_number"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="supplier">Supplier <span class="validation-color">*</span></label><!-- <a href="" data-toggle="modal" data-target="#customer_modal" class="pull-right">+ Add Customer</a> -->
                                                <select class="form-control select2" id="supplier" name="supplier" style="width: 100%;">
                                                    <?php
                                                    foreach ($supplier as $key)
                                                    {
                                                    if ($key->supplier_id == $row->purchase_party_id)
                                                    {
                                                    ?>
                                                    <option value='<?php echo $key->supplier_id ?>' selected><?php echo $key->supplier_name ?></option>
                                                    <?php
                                                    }
                                                    }
                                                    ?>
                                                </select>
                                                <span class="validation-color" id="err_supplier"><?php echo form_error('supplier'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="purchase_invoice_number">Purchase Reference Number <span class="validation-color">*</span></label>
                                                <select class="form-control select2" id="purchase_invoice_number" name="purchase_invoice_number" style="width: 100%;">
                                                    <option value='<?php echo $row->purchase_id; ?>'><?php echo $row->purchase_invoice_number; ?></option>
                                                </select>
                                                <span class="validation-color" id="err_purchase_invoice_number"><?php echo form_error('purchase_invoice_number'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="type_of_supply">Billing Country <span class="validation-color">*</span></label>
                                                <select class="form-control select2" id="billing_country" name="billing_country">
                                                    <option value="">Select</option>
                                                    <?php
                                                    foreach ($country as $key => $value)
                                                    {
                                                    ?>
                                                    <option value="<?= $value->country_id ?>" <?php if ($value->country_id == $row->purchase_billing_country_id) echo "selected='selected'"; ?> ><?= $value->country_name ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                <span class="validation-color" id="err_billing_country"><?php echo form_error('type_of_supply'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3" style="display: none">
                                            <div class="form-group">
                                                <label for="type_of_supply">Place of Supply <span class="validation-color">*</span></label>
                                                <select class="form-control select2" id="billing_state" name="billing_state" style="width: 100%;">
                                                    <?php
                                                    if ($row->purchase_billing_country_id == $branch[0]->branch_country_id)
                                                    {
                                                    ?>
                                                    <?php
                                                    foreach ($state as $key)
                                                    {
                                                    ?>
                                                    <option value='<?php echo $key->state_id ?>' <?php if ($key->state_id == $row->purchase_billing_state_id) echo "selected='selected'"; ?>>
                                                        <?php echo $key->state_name; ?>
                                                    </option>
                                                    <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    }
                                                    else
                                                    {
                                                    ?>
                                                    <option value="0">Out of Country</option>
                                                    <?php } ?>
                                                </select>
                                                <span class="validation-color" id="err_billing_state"><?php echo form_error('type_of_supply'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3" style="display: none;">
                                            <div class="form-group">
                                                <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>
                                                <select class="form-control select2" id="type_of_supply" name="type_of_supply">
                                                    <?php if ($data[0]->purchase_type_of_supply == 'regular')
                                                    {
                                                    ?>
                                                    <option value="regular" <?php
                                                        if ($data[0]->purchase_type_of_supply == 'regular')
                                                        {
                                                        echo "selected";
                                                        }
                                                    ?>>Regular</option>
                                                    <?php
                                                    }
                                                    else
                                                    {
                                                    ?>
                                                    <option value="import" <?php
                                                        if ($data[0]->purchase_type_of_supply == 'import')
                                                        {
                                                        echo "selected";
                                                        }
                                                    ?>>Import</option>
                                                    <?php } ?>
                                                </select>
                                                <span class="validation-color" id="err_type_supply"><?php echo form_error('type_of_supply'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group" style="display: none;">
                                                <label for="gst_payable">GST Payable on Reverse Charge <span class="validation-color">*</span></label>
                                                <br/>
                                                <label class="radio-inline">
                                                    <input class="minimal" type="radio" name="gstPayable" value="yes" <?php if ($data[0]->purchase_gst_payable == 'yes') echo "checked='checked'" ?> /> Yes
                                                </label>
                                                <label class="radio-inline">
                                                    <input class="minimal" type="radio" name="gstPayable" value="no" <?php if ($data[0]->purchase_gst_payable == 'no') echo "checked='checked'" ?>/> No
                                                </label>
                                                <br/>
                                                <span class="validation-color" id="err_gst_payable"><?php echo form_error('gst_payable'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3" style="display: none">
                                            <div class="form-group">
                                                <label for="currency_id">Billing Currency <span class="validation-color">*</span></label>
                                                <select class="form-control select2" id="currency_id" name="currency_id">
                                                    <?php
                                                    foreach ($currency as $key => $value){
                                                        if ($value->currency_id == $data[0]->currency_id) {
                                                            echo "<option value='" . $value->currency_id . "' selected>" . $value->currency_name.' - ' . $value->country_shortname."</option>";
                                                        }
                                                    // else
                                                    // {
                                                    //     echo "<option value='".$value->currency_id."'>".$value->currency_name."</option>";
                                                    // }
                                                    }
                                                    ?>
                                                </select>
                                                <span class="validation-color" id="err_currency_id"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           <div class="col-sm-12" style="display: none;"> 
                                             <?php
                                             $this->load->view('sub_modules/transporter_sub_module');
                                        $this->load->view('sub_modules/shipping_sub_module');
                                        ?>
                                    </div>
                            <!-- <div class="col-sm-12">
                                <div class="well">
                                    <div class="row">
                                        <?php
                                        if ($access_settings[0]->item_access == 'product')
                                        {
                                        ?>
                                        <div class="col-sm-12 search_purchase_code">
                                            <?php
                                            if (isset($other_modules_present['product_module_id']) && $other_modules_present['product_module_id'] != "")
                                            {
                                            ?>
                                            <a href="" data-toggle="modal" data-target="#product_modal" class="open_product_modal pull-left">+ Add Product</a>
                                            <?php } ?>
                                            <input id="input_purchase_code" class="form-control" type="text" name="input_purchase_code" placeholder="Enter Product Code/Name" >
                                        </div>
                                        <?php
                                        }
                                        elseif ($access_settings[0]->item_access == 'service')
                                        {
                                        ?>
                                        <div class="col-sm-12 search_purchase_code">
                                            <?php
                                            if (isset($other_modules_present['service_module_id']) && $other_modules_present['service_module_id'] != "")
                                            {
                                            ?>
                                            <a href="" data-toggle="modal" data-target="#service_modal" class="open_service_modal pull-left">+ Add Service</a>
                                            <?php } ?>
                                            <input id="input_purchase_code" class="form-control" type="text" name="input_purchase_code" placeholder="Enter Product Code/Name" >
                                        </div>
                                        <?php
                                        }
                                        else
                                        {
                                        ?>
                                        <div class="col-sm-12 search_purchase_code">
                                            <?php
                                            if (isset($other_modules_present['product_module_id']) && isset($other_modules_present['service_module_id']) && $other_modules_present['product_module_id'] != "" && $other_modules_present['service_module_id'] != "")
                                            {
                                            ?>
                                            <a href="" data-toggle="modal" data-target="#item_modal" class="pull-left">+ Add Item</a>
                                            <?php } ?>
                                            <input id="input_purchase_code" class="form-control" type="text" name="input_purchase_code" placeholder="Enter Product/Service Code/Name" >
                                        </div>
                                        <?php
                                        }
                                        ?>
                                        <div class="col-sm-8">
                                            <span class="validation-color" id="err_purchase_code"></span>
                                        </div>
                                    </div>
                                </div>
                                </div> --> <!--/col-md-12 -->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('purchase_inventory_items'); ?></label>
                                        <table class="table items table-striped table-bordered table-condensed table-hover product_table table-responsive" name="product_data" id="product_data">
                                            <thead>
                                                <tr>
                                                    <th width="2%"><img src="<?php echo base_url(); ?>assets/images/bin1.png" /></th>
                                                    <th class="span2" width="10%">Product Name</th>
                                                    <th class="span2" width="17%">Product Description</th>
                                                    <th class="span2" width="6%">Qty </th>
                                                    <th class="span2" >Rate</th>
                                                    <th class="span2" >Taxable Value</th>
                                                    <th class="span2" >GST(%)</th>
                                                    <th class="span2" >Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="purchase_table_body">
                                                <?php
                                                $i   = 0;
                                                $tot = 0;
                                                foreach ($items as $key)
                                                {
                                                ?>
                                                <tr id="<?= $i ?>">
                                                    <td>
                                                        <!-- <a class='deleteRow'> <img src='<?php echo base_url(); ?>assets/images/bin_close.png' /> </a> -->
                                                        <input type='checkbox' name='checkbox_valid'>
                                                    </td>
                                                    <?php if ($key->item_type == 'product')
                                                    {
                                                    ?>
                                                    <td><?php echo $key->product_name; ?><br>HSN/SAC: <?php echo $key->product_hsn_sac_code; ?></td>
                                                    <?php
                                                    }
                                                    else
                                                    {
                                                    ?>
                                                    <td><?php echo $key->service_name; ?><br>HSN/SAC: <?php echo $key->service_hsn_sac_code; ?></td>
                                                    <?php } ?>
                                                    <td><input type='text' name='item_description' class='form-control form-fixer' value="<?php echo $key->purchase_item_description ?>" readonly></td>
                                                    <td>
                                                        <!-- <?php $quantity            = round(bcsub($key->purchase_item_quantity, $key->purchase_return_quantity, 2)); ?>
                                                        <input type="number" step='0.01' class="form-control form-fixer text-center" value="<?= $quantity ?>" data-rule="quantity" name='item_quantity' id='qty' min="1" max="<?= $quantity ?>" readonly> -->
                                                        <input type="text" class="float_number form-control form-fixer text-center" value="<?= $quantity ?>" data-rule="quantity" name='item_quantity' readonly>
                                                    </td>
                                                    <td>
                                                        <input type='text' class='float_number form-control form-fixer text-right' name='item_price' value='<?php echo $key->purchase_item_unit_price ?>' readonly>
                                                        <span id='item_sub_total_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?php $sub_total           = bcmul($key->purchase_item_unit_price, $quantity, 2);
                                                            echo $sub_total;
                                                        ?></span>
                                                    </td>
                                                     <?php
                                                                $item_discount_value = 0;
                                                                ?>
                                                   
                                                    <td align="right">
                                                        <?php
                                                        if ($item_discount_value == 0)
                                                        {
                                                        $taxable_value = $sub_total;
                                                        }
                                                        else
                                                        {
                                                        $taxable_value = bcdiv(($sub_total * $item_discount_value), 100, 2);
                                                        $taxable_value = bcsub($sub_total, $taxable_value, 2);
                                                        }
                                                        ?>
                                                        <span id="item_taxable_value_<?= $i ?>"><?= $taxable_value ?></span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($key->purchase_item_igst_percentage > 0)
                                                        {
                                                        $purchase_item_igst_amount = bcdiv(($taxable_value * $key->purchase_item_igst_percentage), 100, 2);
                                                        }
                                                        else
                                                        {
                                                        $purchase_item_igst_amount = "0.00";
                                                        }
                                                        if ($key->purchase_item_cgst_percentage > 0)
                                                        {
                                                        $purchase_item_cgst_amount = bcdiv(($taxable_value * $key->purchase_item_cgst_percentage), 100, 2);
                                                        }
                                                        else
                                                        {
                                                        $purchase_item_cgst_amount = "0.00";
                                                        }
                                                        if ($key->purchase_item_sgst_percentage > 0)
                                                        {
                                                        $purchase_item_sgst_amount = bcdiv(($taxable_value * $key->purchase_item_sgst_percentage), 100, 2);
                                                        }
                                                        else
                                                        {
                                                        $purchase_item_sgst_amount = "0.00";
                                                        }
                                                        $item_tax_percentage                             = $key->purchase_item_igst_percentage + $key->purchase_item_cgst_percentage + $key->purchase_item_sgst_percentage;
                                                        $item_tax_amount                                 = $purchase_item_igst_amount + $purchase_item_cgst_amount + $purchase_item_sgst_amount;
                                                        ?>
                                                        <input type="hidden" name="item_igst" class="float_number form-control form-fixer text-center" value="<?= $key->purchase_item_igst_percentage ?>" readonly>
                                                        <span id="item_igst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red; display: none;"><?php echo $purchase_item_igst_amount; ?></span>
                                                         <select class="form-control open_tax select2" name="item_tax" style="width: 100%;" readonly ><option value="">Select</option>
                                                                    <?php
                                                                    foreach ($tax as $key3 => $value3) {
                                                                        if ($value3->tax_name == 'GST') {

                                                                            echo "<option value='" . $value3->tax_id . "-" . precise_amount($value3->tax_value) . "' " . ($value3->tax_id == $key->purchase_item_tax_id ? 'selected' : '' ) . ">" . precise_amount($value3->tax_value) . "</option>";
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select><span id='item_tax_lbl_<?php echo $i; ?>' class='pull-right' style='color:red;'><?php echo precise_amount($key->purchase_item_tax_amount); ?></span>
                                                                <?php
                                                                $item_tax_percentage = $key->purchase_item_igst_percentage + $key->purchase_item_cgst_percentage + $key->purchase_item_sgst_percentage;
                                                                $item_tax_amount = $key->purchase_item_igst_amount + $key->purchase_item_cgst_amount + $key->purchase_item_sgst_amount;
                                                                ?>
                                                        <input type="hidden" name="item_cgst" class="float_number form-control form-fixer text-center" value="<?= $key->purchase_item_cgst_percentage ?>" readonly>
                                                        <span id="item_cgst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red; display:none">
                                                        <?php echo $purchase_item_cgst_amount; ?></span>
                                                   
                                                        <input type="hidden" name="item_sgst" class="float_number form-control form-fixer text-center" value="<?= $key->purchase_item_sgst_percentage ?>" readonly>
                                                        <span id="item_sgst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red; display:none"><?php echo $purchase_item_sgst_amount; ?></span>
                                                    </td>
                                                    <td align="right">
                                                        <input type="text" class="float_number form-control form-fixer text-right" name="item_grand_total" value=" <?php printf("%.2f", ($taxable_value + $purchase_item_igst_amount + $purchase_item_cgst_amount + $purchase_item_sgst_amount)); ?>" readonly>
                                                    </td>
                                                    <?php
                                                    $temp                                            = array(
                                                    "item_id"   => $key->item_id,
                                                    "item_type" => $key->item_type,
                                                    "item_igst" => $key->purchase_item_igst_percentage,
                                                    "item_cgst" => $key->purchase_item_cgst_percentage,
                                                    "item_sgst" => $key->purchase_item_sgst_percentage
                                                    );
                                                    $item_gst[$key->item_id . '-' . $key->item_type] = $temp;
                                                    ?>
                                                    <!-- Items Hidden Field -->
                                                    <input type='hidden' name='checkbox_valid1' value='off'>
                                                    <input type='hidden' name='purchase_item_id' value='<?php echo $key->purchase_item_id; ?>'>
                                                    <input type='hidden' name='item_key_value' value="<?php echo $i ?>">
                                                    <input type='hidden' name='item_id' value="<?php echo $key->item_id ?>">
                                                    <input type='hidden' name='item_type' value="<?php echo $key->item_type ?>">
                                                    <?php if ($key->item_type == 'product')
                                                    {
                                                    ?>
                                                    <input type='hidden' name='item_code' value='<?php echo $key->product_code ?>'>
                                                    <?php
                                                    }
                                                    else
                                                    {
                                                    ?>
                                                    <input type='hidden' name='item_code' value='<?php echo $key->service_code ?>'>
                                                    <?php } ?>
                                                    <input type='hidden' class='form-control' style='' value='<?php echo $key->purchase_item_sub_total ?>' name='item_sub_total'>
                                                    <input type="hidden" name="item_discount_amount" value="<?php echo $key->purchase_item_discount_amount; ?>">
                                                    <input type="hidden" name="item_discount_value" value="<?php echo $item_discount_value; ?>">
                                                    <input type='hidden' name='item_taxable_value' value="<?= $key->purchase_item_taxable_value ?>">
                                                    <input type='hidden' name='item_tax_percentage' value="<?= $item_tax_percentage ?>">
                                                    <input type='hidden' name='item_tax_amount' value="<?= $item_tax_amount ?>">
                                                    <input type="hidden" name="item_igst_amount" class="form-control form-fixer" value="<?php echo $key->purchase_item_igst_amount; ?>" >
                                                    <input type="hidden" name="item_cgst_amount" class="form-control form-fixer" value="<?php echo $key->purchase_item_cgst_amount; ?>">
                                                    <input type="hidden" name="item_sgst_amount" class="form-control form-fixer text-center" value="<?php echo $key->purchase_item_sgst_amount; ?>">
                                                    <!-- Items Hidden Field -->
                                                </tr>
                                                <?php
                                                //                             $product_data[$i]['item_id'] = $key->item_id;
                                                //                             $product_data[$i]['item_type'] = $key->item_type;
                                                //                             if($key->item_type=='product')
                                                //                             {
                                                //                                 $product_data[$i]['item_code'] = $key->product_code;
                                                //                             }
                                                //                             else
                                                //                             {
                                                //                                 $product_data[$i]['item_code'] = $key->service_code;
                                                //                             }
                                                // $product_data[$i]['item_price'] = $key->purchase_item_unit_price;
                                                // $product_data[$i]['item_key_value'] = $i;
                                                // $product_data[$i]['item_sub_total'] = $key->purchase_item_sub_total;
                                                // $product_data[$i]['item_description'] = $key->purchase_item_description;
                                                // $product_data[$i]['item_quantity'] = $key->purchase_item_quantity;
                                                // $product_data[$i]['item_discount_amount'] = $key->purchase_item_discount_amount;
                                                // $product_data[$i]['item_discount_value'] = $item_discount_value;
                                                // $product_data[$i]['item_discount'] = $key->purchase_item_discount_id;
                                                // $product_data[$i]['item_igst'] = $key->purchase_item_igst_percentage;
                                                // $product_data[$i]['item_igst_amount'] = $key->purchase_item_igst_amount;
                                                // $product_data[$i]['item_cgst'] = $key->purchase_item_cgst_percentage;
                                                // $product_data[$i]['item_cgst_amount'] = $key->purchase_item_cgst_amount;
                                                // $product_data[$i]['item_sgst'] = $key->purchase_item_sgst_percentage;
                                                // $product_data[$i]['item_sgst_amount'] = $key->purchase_item_sgst_amount;
                                                // $product_data[$i]['item_tax_percentage'] = $item_tax_percentage;
                                                // $product_data[$i]['item_tax_amount'] = $item_tax_amount;
                                                // $product_data[$i]['item_taxable_value'] = $key->purchase_item_taxable_value;
                                                // $product_data[$i]['item_grand_total'] = $key->purchase_item_grand_total;
                                                // $product_data[$i]['checkbox_value'] = "off";
                                                //array_push($product_data,$product);
                                                $i++;
                                                }
                                                // $product = htmlspecialchars(json_encode($product_data));
                                                ?>
                                            </tbody>
                                        </table>
                                        <!-- Hidden Field -->
                                        <input type="hidden" name="purchase_id" value="<?php echo $row->purchase_id; ?>">
                                        <input type="hidden" class="form-control" id="product_module_id" name="product_module_id" value="<?php
                                        if (isset($other_modules_present['product_module_id']))
                                        {
                                        echo $other_modules_present['product_module_id'];
                                        }
                                        ?>" readonly>
                                        <input type="hidden" class="form-control" id="service_module_id" name="service_module_id" value="<?php
                                        if (isset($other_modules_present['service_module_id']))
                                        {
                                        echo $other_modules_present['service_module_id'];
                                        }
                                        ?>" readonly>
                                        <input type="hidden" class="form-control" id="supplier_module_id" name="supplier_module_id" value="<?php
                                        if (isset($other_modules_present['supplier_module_id']))
                                        {
                                        echo $other_modules_present['supplier_module_id'];
                                        }
                                        ?>" readonly>
                                        <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo $date; ?>" readonly>
                                        <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" readonly>
                                        <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>
                                        <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $invoice_number ?>" readonly>
                                        <input type="hidden" class="form-control" id="section_area" name="section_area" value="add_purchase" readonly>
                                        <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php echo $branch[0]->branch_country_id; ?>">
                                        <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php echo $branch[0]->branch_state_id; ?>">
                                        <input type="hidden" name="total_sub_total" id="total_sub_total">
                                        <input type="hidden" name="total_taxable_amount" id="total_taxable_amount">
                                        <input type="hidden" name="total_discount_amount" id="total_discount_amount">
                                        <input type="hidden" name="total_tax_amount" id="total_tax_amount">
                                        <input type="hidden" name="total_igst_amount" id="total_igst_amount">
                                        <input type="hidden" name="total_cgst_amount" id="total_cgst_amount">
                                        <input type="hidden" name="total_sgst_amount" id="total_sgst_amount">
                                        <input type="hidden" name="total_grand_total" id="total_grand_total">
                                        <input type="hidden" name="table_data" id="table_data">
                                        <input type="hidden" name="total_other_amount" id="total_other_amount" value="<?= $data[0]->total_other_amount; ?>">
                                        <input type="hidden" name="grand_total_check" id="grand_total_check" value="1">
                                        <?php
                                        $charges_sub_module = 0;
                                        if (in_array($charges_sub_module_id, $access_sub_modules)) {
                                       
                                        ?>
                                        <input type="hidden" name="total_freight_charge" id="total_freight_charge" value="<?php echo $data[0]->total_freight_charge; ?>">
                                        <input type="hidden" name="total_insurance_charge" id="total_insurance_charge" value="<?php echo $data[0]->total_insurance_charge; ?>">
                                        <input type="hidden" name="total_packing_charge" id="total_packing_charge" value="<?php echo $data[0]->total_packing_charge; ?>">
                                        <input type="hidden" name="total_incidental_charge" id="total_incidental_charge" value="<?php echo $data[0]->total_incidental_charge; ?>">
                                        <input type="hidden" name="total_other_inclusive_charge" id="total_other_inclusive_charge" value="<?php echo $data[0]->total_inclusion_other_charge; ?>">
                                        <input type="hidden" name="total_other_exclusive_charge" id="total_other_exclusive_charge" value="<?php echo $data[0]->total_exclusion_other_charge; ?>">
                                        <?php } ?>
                                        <!-- Hidden Field -->
                                        <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive">
                                             <tr>
                                                        <td align="right" width="80%"><?php echo 'Subtotal'; ?> (+)</td>
                                                        <td align='right'><span id="totalSubTotal">0.00</span></td>
                                                    </tr>
                                                    <tr style="display: none;">
                                                        <td align="right"><?php echo 'Discount'; ?> (-)</td>
                                                        <td align='right'><span id="totalDiscountAmount">0.00</span>
                                                        </td>
                                                    </tr>
                                                    <tr style="display: none">
                                                        <td align="right"><?php echo 'Tax Amount'; ?> (+)</td>
                                                        <td align='right'><span id="totalTaxAmount">0.00</span>
                                                        </td>
                                                    </tr>
                                                     <tr class='totalCGSTAmount_tr'>
                                                        <td align="right"><?php echo 'CGST'; ?> (+)</td>
                                                        <td align='right'>
                                                            <span id="totalCGSTAmount">0.00</span>
                                                        </td>
                                                    </tr>
                                            <tr class='totalSGSTAmount_tr'>
                                                <td align="right"><?php echo 'SGST'; ?> (+)</td>
                                                <td align='right'>
                                                    <span id="totalSGSTAmount">0.00</span>
                                                </td>
                                            </tr>
                                            <tr class='totalIGSTAmount_tr'>
                                                <td align="right"><?php echo 'IGST'; ?> (+)</td>
                                                <td align='right'>
                                                    <span id="totalIGSTAmount">0.00</span>
                                                </td>
                                            </tr>
                                    <?php if ($charges_sub_module == 1)
                                    {
                                    ?>
                                    <tr id="freight_charge_tr">
                                        <td align="right">Freight Charge (+)</td>
                                        <td align='right'>
                                            <span id="freight_charge"><?php echo $data[0]->total_freight_charge; ?></span>
                                        </td>
                                    </tr>
                                    <tr id="insurance_charge_tr">
                                        <td align="right">Insurance Charge (+)</td>
                                        <td align='right'>
                                            <span id="insurance_charge"><?php echo $data[0]->total_insurance_charge; ?></span>
                                        </td>
                                    </tr>
                                    <tr id="packing_charge_tr">
                                        <td align="right">Packing & Forwarding Charge (+)</td>
                                        <td align='right'>
                                            <span id="packing_charge"><?php echo $data[0]->total_packing_charge; ?></span>
                                        </td>
                                    </tr>
                                    <tr id="incidental_charge_tr">
                                        <td align="right">Incidental Charge (+)</td>
                                        <td align='right'>
                                            <span id="incidental_charge"><?php echo $data[0]->total_incidental_charge; ?></span>
                                        </td>
                                    </tr>
                                    <tr id="other_inclusive_charge_tr">
                                        <td align="right">Other Inclusive Charge (+)</td>
                                        <td align='right'>
                                            <span id="other_inclusive_charge"><?php echo $data[0]->total_inclusion_other_charge; ?></span>
                                        </td>
                                    </tr>
                                    <tr id="other_exclusive_charge_tr">
                                        <td align="right">Other Exclusive Charge (-)</td>
                                        <td align='right'>
                                            <span id="other_exclusive_charge"><?php echo $data[0]->total_exclusion_other_charge; ?></span>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td align="right"><?php echo 'Grand Total'; ?> (=)</td>
                                        <td align='right'><span id="totalGrandTotal">0.00</span></td>
                                    </tr>
                                </table>
                                <span class="validation-color pull-right" id="err_product"></span>
                            </div>
                        </div>
                       
                        <div class="col-sm-12">
                            <div class="box-footer">
                                <button type="submit" id="purchase_submit" class="btn btn-info">Add</button>
                                <span class="btn btn-default" id="cancel" onclick="cancel('purchase')">Cancel</span>
                            </div>
                        
                        <?php } ?>
                        <?php
                        $notes_sub_module = 0;
                         if (in_array($notes_sub_module_id, $access_sub_modules)) {
                                        $notes_sub_module = 1;
                                    }
                        if ($notes_sub_module == 1)
                        {
                        $this->load->view('sub_modules/notes_sub_module');
                        }
                        ?>
                    </form>
                    </div>
                </div>
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
//$this->load->view('supplier/supplier_modal');
//$this->load->view('product/product_modal');
//$this->load->view('discount/discount_modal');
//$this->load->view('tax/tax_modal');
//$this->load->view('general/modal/hsn_modal');
//$this->load->view('category/category_modal');
//$this->load->view('subcategory/subcategory_modal');
?>
<script type="text/javascript">
var purchase_reload = "yes";
</script>
<?php if ($charges_sub_module == 1)
{
?>
<script src="<?php echo base_url('assets/js/sub_modules/') ?>charges_sub_module.js"></script>
<?php } ?>
<?php
if (isset($items))
{
if (count($items) > 0)
{
$count_data = count($items);
}
else
{
$count_data = 0;
}
}
else
{
$count_data = 0;
}
?>
<script type="text/javascript">var count_data = "<?php echo $count_data; ?>";</script>
<script type="text/javascript">
var purchase_data = new Array();
var branch_state_list = <?php echo json_encode($state); ?>;
var item_gst =<?php echo json_encode($item_gst); ?>;
    var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
    var common_settings_tax_split = "<?= $access_common_settings[0]->tax_split_equaly ?>";
    var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
    var common_settings_amount_precision = "<?= $access_common_settings[0]->amount_precision ?>";
    var settings_tax_percentage = "<?= $access_common_settings[0]->tax_split_percentage ?>";
    var settings_tax_type = "<?= $access_settings[0]->tax_type ?>";
    var settings_discount_visible = "<?= $access_settings[0]->discount_visible ?>";
    var settings_description_visible = "<?= $access_settings[0]->description_visible ?>";
    var settings_tds_visible = "<?= $access_settings[0]->tds_visible ?>";
    var settings_item_editable = "<?= $access_settings[0]->item_editable ?>";
</script>
<script src="<?php echo base_url('assets/js/purchase/') ?>purchase_return.js"></script>