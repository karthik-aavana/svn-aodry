<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?php echo base_url('delivery_challan'); ?>">Delivery Challan</a></li>
                <li class="active">Edit Delivery Challan</li>
            </ol>
        </h5>
    </section>   
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Delivery Challan</h3>
                        <a class="btn btn-sm btn-default pull-right back_button" href1="<?php echo base_url('delivery_challan'); ?>">Back </a>
                    </div>
                    <div class="box-body">

                        <form role="form" id="form" method="post" action="<?php echo base_url('delivery_challan/edit_delivery_challan'); ?>">
                            <?php
                            foreach ($data as $row) {
                                ?>

                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="date">Date<span class="validation-color">*</span></label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?php echo date('d-m-Y', strtotime($row->delivery_challan_date)); ?>">
                                                <input type="hidden" id="date_old" name="date_old" value="<?php echo $row->delivery_challan_date; ?>">
                                                <input type="hidden" id="added_date" name="added_date" value="<?php echo $row->added_date; ?>">
                                                <input type="hidden" id="added_user_id" name="added_user_id" value="<?php echo $row->added_user_id; ?>">
                                                <input type="hidden" name="delivery_challan_id" value="<?php echo $row->delivery_challan_id; ?>">
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                            </div>
                                            <span class="validation-color" id="err_date"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="invoice_number">Invoice Number<span class="validation-color">*</span></label>
                                            <?php ?>
                                            <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo $row->delivery_challan_invoice_number ?>" <?php
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
                                            <!--   <?php
                                            if (isset($other_modules_present['customer_module_id']) && $other_modules_present['customer_module_id'] != "") {
                                                ?>
                                                                                                        <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#customer_modal" class="pull-right">+ Add Customer</a>
                                            <?php }
                                            ?> -->

                                            <select class="form-control select2" autofocus="on" id="customer" name="customer" style="width: 100%;">
                                                <!-- <option value="">Select</option> -->
                                                <?php
                                                foreach ($customer as $key) {
                                                    if ($key->customer_id == $row->delivery_challan_party_id) {
                                                        ?>
                                                        <option value='<?php echo $key->customer_id ?>' <?php
                                                        if ($key->customer_id == $row->delivery_challan_party_id) {
                                                            echo "selected";
                                                        }
                                                        ?>><?php echo $key->customer_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                            </select>
                                            <span class="validation-color" id="err_customer"><?php echo form_error('customer'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="nature_of_supply">Nature of Supply <span class="validation-color">*</span></label>

                                            <?php
                                            if ($row->delivery_challan_nature_of_supply == "product") {
                                                ?>
                                                <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="product" readonly>
                                                <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Goods" readonly>

                                                <?php
                                            } elseif ($row->delivery_challan_nature_of_supply == "service") {
                                                ?>
                                                <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="service" readonly>
                                                <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Service" readonly>
                                                <?php
                                            } else {
                                                ?>
                                                <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="both" readonly>
                                                <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Product/Service" readonly>
                                            <?php } ?>
    <!-- <select class="form-control select2" id="nature_of_supply" name="nature_of_supply">
    <option value="">Select</option>
    <option value="product" <?php
                                            if ($row->delivery_challan_nature_of_supply == "product") {
                                                echo 'selected="selected"';
                                            }
                                            ?>>Goods</option>
    <option value="service" <?php
                                            if ($row->delivery_challan_nature_of_supply == "service") {
                                                echo 'selected="selected"';
                                            }
                                            ?>>Service</option>
    <option value="both" <?php
                                            if ($row->delivery_challan_nature_of_supply == "both") {
                                                echo 'selected="selected"';
                                            }
                                            ?>>Both</option>
    </select> -->
                                            <span class="validation-color" id="err_nature_supply"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="type_of_supply">Billing Country <span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="billing_country" name="billing_country">
                                                <?php
                                                foreach ($country as $key => $value) {
                                                    ?>
                                                    <option value="<?= $value->country_id ?>" <?php if ($value->country_id == $row->delivery_challan_billing_country_id) echo "selected='selected'"; ?> ><?= $value->country_name ?></option>
                                                    <?php
                                                }
                                                ?>

                                            </select>
                                            <span class="validation-color" id="err_billing_country"><?php echo form_error('billing_country'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="type_of_supply">Place of Supply <span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="billing_state" name="billing_state">
                                                <?php
                                                if ($row->delivery_challan_billing_country_id == $branch[0]->branch_country_id) {
                                                    ?>
                                                    <?php
                                                    foreach ($state as $key => $value) {
                                                        ?>
                                                        <option value="<?= $value->state_id ?>" <?php if ($value->state_id == $row->delivery_challan_billing_state_id) echo "selected='selected'"; ?> ><?= $value->state_name ?></option>

                                                    <?php } ?>
                                                    <?php
                                                }
                                                else {
                                                    ?>
                                                    <option value="0">Out of Country</option>
                                                <?php } ?>
                                            </select>
                                            <span class="validation-color" id="err_billing_state"><?php echo form_error('billing_state'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="type_of_supply" name="type_of_supply">
                                                <?php
                                                if ($row->delivery_challan_type_of_supply == 'regular') {
                                                    ?>
                                                    <option value="regular" selected="selected">Regular</option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value="export_with_payment" <?php
                                                    if ($row->delivery_challan_type_of_supply == 'export_with_payment') {
                                                        echo "selected";
                                                    }
                                                    ?>>Export (With Payment)</option>
                                                    <option value="export_without_payment"  <?php
                                                    if ($row->delivery_challan_type_of_supply == 'export_without_payment') {
                                                        echo "selected";
                                                    }
                                                    ?>>Export (Without Payment)</option>
                                                        <?php } ?>
                                            </select>
                                            <span class="validation-color" id="err_type_supply"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="gst_payable">GST Payable on Reverse Charge <span class="validation-color">*</span></label>
                                            <br/>
                                            <label class="radio-inline">
                                                <input type="radio" name="gstPayable" value="yes" class="minimal" <?php
                                                if ($row->delivery_challan_gst_payable == "yes") {
                                                    echo 'checked="checked"';
                                                }
                                                ?> /> Yes
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="gstPayable" value="no" class="minimal" <?php
                                                if ($row->delivery_challan_gst_payable == "no") {
                                                    echo 'checked="checked"';
                                                }
                                                ?> /> No
                                            </label>
                                            <br/>
                                            <span class="validation-color" id="err_gst_payable"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="currency_id">Billing Currency <span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="currency_id" name="currency_id">
                                                <?php
                                                foreach ($currency as $key => $value) {
                                                    if ($value->currency_id == $row->currency_id) {
                                                        echo "<option value='" . $value->currency_id . "' selected>" . $value->currency_name . "</option>";
                                                    } else {
                                                        echo "<option value='" . $value->currency_id . "'>" . $value->currency_name . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <span class="validation-color" id="err_currency_id"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-8">
                                        <span class="validation-color" id="err_product"></span>
                                    </div>
                                    <?php
                                    if ($access_settings[0]->item_access == 'product') {
                                        ?>
                                        <div class="col-sm-12 search_sales_code">
                                            <?php
                                            if (isset($other_modules_present['product_module_id']) && $other_modules_present['product_module_id'] != "") {
                                                ?>
                                                <a href="" data-toggle="modal" data-target="#product_modal" class="open_product_modal pull-left">+ Add Product</a>
                                            <?php } ?>
                                            <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product Code/Name" >
                                        </div>
                                        <?php
                                    } elseif ($access_settings[0]->item_access == 'service') {
                                        ?>
                                        <div class="col-sm-12 search_sales_code">
                                            <?php
                                            if (isset($other_modules_present['service_module_id']) && $other_modules_present['service_module_id'] != "") {
                                                ?>
                                                <a href="" data-toggle="modal" data-target="#service_modal" class="open_service_modal pull-left">+ Add Service</a>
                                            <?php } ?>
                                            <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product Code/Name" >
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="col-sm-12 search_sales_code">
                                            <?php
                                            if (isset($other_modules_present['product_module_id']) && isset($other_modules_present['service_module_id']) && $other_modules_present['product_module_id'] != "" && $other_modules_present['service_module_id'] != "") {
                                                ?>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <a href="" data-toggle="modal" data-target="#item_modal" class="pull-left">+</a>
                                                    </div>
                                                <?php } ?>
                                                <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product/Service Code/Name" >
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <div class="col-sm-12">
                                        <span class="validation-color" id="err_sales_code"></span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Inventory Items</label>
                                            <table class="table table-striped table-bordered table-condensed table-hover sales_table table-responsive" name="sales_data" id="sales_data">
                                                <thead>
                                                    <tr>
                                                        <th width="2%"><img src="<?php echo base_url(); ?>assets/images/bin1.png" /></th>
                                                        <th class="span2" width="10%">Product/Service Name</th>
                                                        <th class="span2" width="17%">Product/Service Description</th>
                                                        <th class="span2" width="6%">Quantity</th>
                                                        <th class="span2" >Rate</th>
                                                        <th class="span2" >Discount</th>
                                                        <th class="span2" >Taxable Value</th>
                                                        <th class="span2" >IGST(%)</th>
                                                        <th class="span2" >CGST(%)</th>
                                                        <th class="span2" >SGST(%)</th>
                                                        <th class="span2" >Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="sales_table_body">
                                                    <?php
                                                    $i = 0;
                                                    $tot = 0;
                                                    foreach ($items as $key) {
                                                        ?>
                                                        <tr id="<?= $i ?>">
                                                            <td>
                                                                <a class='deleteRow'> <img src='<?php echo base_url(); ?>assets/images/bin_close.png' /> </a>
                                                                <input type='hidden' name='item_key_value' value="<?php echo $i ?>">
                                                                <input type='hidden' name='item_id' value="<?php echo $key->item_id ?>">
                                                                <input type='hidden' name='item_type' value="<?php echo $key->item_type ?>">
                                                                <?php
                                                                if ($key->item_type == 'product' || $key->item_type == 'product_inventory') {
                                                                    ?>
                                                                    <input type='hidden' name='item_code' value='<?php echo $key->product_code ?>'>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <input type='hidden' name='item_code' value='<?php echo $key->service_code ?>'>
                                                                <?php } ?>
                                                                <?php
                                                                if ($key->item_type == 'product' || $key->item_type == 'product_inventory') {
                                                                    ?>
                                                                <td><?php echo $key->product_name; ?><br>HSN/SAC: <?php echo $key->product_hsn_sac_code; ?></td>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <td><?php echo $key->service_name; ?><br>HSN/SAC: <?php echo $key->service_hsn_sac_code; ?></td>
                                                            <?php } ?>
                                                            <td><input type='text' name='item_description' class='form-control form-fixer' value="<?php echo $key->delivery_challan_item_description ?>"></td>

                                                            <td><input type="number" step='0.01' class="form-control form-fixer text-center" value="<?php echo $key->delivery_challan_item_quantity ?>" data-rule="quantity" name='item_quantity' min="1" >
                                                            </td>
                                                            <td>
                                                                <input type='number' step='0.01' class='form-control form-fixer text-right' name='item_price' value='<?php echo $key->delivery_challan_item_unit_price ?>'>
                                                                <span id='item_sub_total_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?php echo $key->delivery_challan_item_unit_price; ?></span>
                                                                <input type='hidden' class='form-control' style='' value='<?php echo $key->delivery_challan_item_sub_total ?>' name='item_sub_total' readonly>
                                                            </td>
                                                            <td align="right">
                                                                <div class="form-group">
                                                                    <select class="form-control" name="item_discount" style="width: 100%;">
                                                                        <option value="">Select</option>
                                                                        <?php
                                                                        $item_discount_value = 0;
                                                                        foreach ($discount as $dis) {
                                                                            if ($key->delivery_challan_item_discount_id == $dis->discount_id) {
                                                                                $item_discount_value = $dis->discount_value;
                                                                                ?>
                                                                                <option value='<?php echo $dis->discount_id . '-' . $dis->discount_value ?>' selected="selected"><?php echo $dis->discount_value . '%'; ?></option>
                                                                                <?php
                                                                            } else {
                                                                                ?>
                                                                                <option value='<?php echo $dis->discount_id . '-' . $dis->discount_value ?>'>
                                                                                    <?php echo $dis->discount_value . '%'; ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <input type="hidden" name="item_discount_amount" value="<?php echo $key->delivery_challan_item_discount_amount; ?>">
                                                                <input type="hidden" name="item_discount_value" value="<?php echo $item_discount_value; ?>">
                                                            </td>
                                                            <td align="right">
                                                                <input type='hidden' name='item_taxable_value' value="<?= $key->delivery_challan_item_taxable_value ?>">
                                                                <span id="item_taxable_value_<?= $i ?>"><?= $key->delivery_challan_item_taxable_value ?></span>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $item_tax_percentage = $key->delivery_challan_item_igst_percentage + $key->delivery_challan_item_cgst_percentage + $key->delivery_challan_item_sgst_percentage;
                                                                $item_tax_amount = $key->delivery_challan_item_igst_amount + $key->delivery_challan_item_cgst_amount + $key->delivery_challan_item_sgst_amount;
                                                                ?>
                                                                <input type='hidden' name='item_tax_percentage' value="<?= $item_tax_percentage ?>">
                                                                <input type='hidden' name='item_tax_amount' value="<?= $item_tax_amount ?>">
                                                                <input type="number" step="0.01" name="item_igst" class="form-control form-fixer text-center" max="100" min="0" value="<?= $key->delivery_challan_item_igst_percentage ?>" <?php if ($row->delivery_challan_billing_state_id == $branch[0]->branch_state_id || $row->delivery_challan_billing_country_id != $branch[0]->branch_country_id) echo "readonly"; ?>>
                                                                <input type="hidden" name="item_igst_amount" class="form-control form-fixer" value="<?php echo $key->delivery_challan_item_igst_amount; ?>" >

                                                                <span id="item_igst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red;"><?php echo $key->delivery_challan_item_igst_amount; ?></span>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" name="item_cgst" class="form-control form-fixer text-center" max="100" min="0" value="<?= $key->delivery_challan_item_cgst_percentage ?>" <?php if ($row->delivery_challan_billing_state_id != $branch[0]->branch_state_id) echo "readonly"; ?>>
                                                                <input type="hidden" name="item_cgst_amount" class="form-control form-fixer" value="<?php echo $key->delivery_challan_item_cgst_amount; ?>">
                                                                <span id="item_cgst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red;">
                                                                    <?php echo $key->delivery_challan_item_cgst_amount; ?></span>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" name="item_sgst" class="form-control form-fixer text-center" max="100" min="0" value="<?= $key->delivery_challan_item_sgst_percentage ?>" <?php if ($row->delivery_challan_billing_state_id != $branch[0]->branch_state_id) echo "readonly"; ?>>
                                                                <input type="hidden" name="item_sgst_amount" class="form-control form-fixer text-center" value="<?php echo $key->delivery_challan_item_sgst_amount; ?>">
                                                                <span id="item_sgst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red;"><?php echo $key->delivery_challan_item_sgst_amount; ?></span>
                                                            </td>
                                                            <td align="right">
                                                                <input type="text" class="form-control form-fixer text-right" name="item_grand_total" value=" <?php echo $key->delivery_challan_item_grand_total ?>">
                                                            </td>
                                                            <?php
                                                            $temp = array(
                                                                "item_id" => $key->item_id,
                                                                "item_type" => $key->item_type,
                                                                "item_igst" => $key->delivery_challan_item_igst_percentage,
                                                                "item_cgst" => $key->delivery_challan_item_cgst_percentage,
                                                                "item_sgst" => $key->delivery_challan_item_sgst_percentage
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
                                                        $sales_data[$i]['item_price'] = $key->delivery_challan_item_unit_price;
                                                        $sales_data[$i]['item_key_value'] = $i;
                                                        $sales_data[$i]['item_sub_total'] = $key->delivery_challan_item_sub_total;
                                                        $sales_data[$i]['item_description'] = $key->delivery_challan_item_description;
                                                        $sales_data[$i]['item_quantity'] = $key->delivery_challan_item_quantity;

                                                        $sales_data[$i]['item_discount_amount'] = $key->delivery_challan_item_discount_amount;
                                                        $sales_data[$i]['item_discount_value'] = $item_discount_value;
                                                        $sales_data[$i]['item_discount'] = $key->delivery_challan_item_discount_id;
                                                        $sales_data[$i]['item_igst'] = $key->delivery_challan_item_igst_percentage;
                                                        $sales_data[$i]['item_igst_amount'] = $key->delivery_challan_item_igst_amount;
                                                        $sales_data[$i]['item_cgst'] = $key->delivery_challan_item_cgst_percentage;
                                                        $sales_data[$i]['item_cgst_amount'] = $key->delivery_challan_item_cgst_amount;
                                                        $sales_data[$i]['item_sgst'] = $key->delivery_challan_item_sgst_percentage;
                                                        $sales_data[$i]['item_sgst_amount'] = $key->delivery_challan_item_sgst_amount;
                                                        $sales_data[$i]['item_tax_percentage'] = $item_tax_percentage;
                                                        $sales_data[$i]['item_tax_amount'] = $item_tax_amount;
                                                        $sales_data[$i]['item_taxable_value'] = $key->delivery_challan_item_taxable_value;
                                                        $sales_data[$i]['item_grand_total'] = $key->delivery_challan_item_grand_total;

                                                        //array_push($product_data,$product);
                                                        $i++;
                                                    }
                                                    $sales = htmlspecialchars(json_encode($sales_data));
                                                    ?>
                                                </tbody>
                                            </table>
                                            <!-- Hidden Field -->
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
                                            <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo date("Y-m-d"); ?>" readonly>
                                            <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" readonly>
                                            <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>
                                            <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $row->delivery_challan_invoice_number ?>" readonly>
                                            <input type="hidden" class="form-control" id="section_area" name="section_area" value="edit_delivery_challan" readonly>
                                            <input type="hidden" name="total_sub_total" id="total_sub_total" value="<?= $row->delivery_challan_sub_total; ?>">
                                            <input type="hidden" name="total_taxable_amount" id="total_taxable_amount" value="<?= $row->delivery_challan_taxable_value; ?>">
                                            <input type="hidden" name="total_discount_amount" id="total_discount_amount" value="<?= $row->delivery_challan_discount_value; ?>">
                                            <input type="hidden" name="total_tax_amount" id="total_tax_amount" value="<?= $row->delivery_challan_tax_amount; ?>">
                                            <input type="hidden" name="total_igst_amount" id="total_igst_amount" value="<?= $row->delivery_challan_igst_amount; ?>">
                                            <input type="hidden" name="total_cgst_amount" id="total_cgst_amount" value="<?= $row->delivery_challan_cgst_amount; ?>">
                                            <input type="hidden" name="total_sgst_amount" id="total_sgst_amount" value="<?= $row->delivery_challan_sgst_amount; ?>">
                                            <input type="hidden" name="total_grand_total" id="total_grand_total" value="<?= $row->delivery_challan_grand_total; ?>">
                                            <input type="hidden" name="table_data" id="table_data" value="<?php echo $sales; ?>">
                                            <input type="hidden" name="total_other_amount" id="total_other_amount" value="0">
                                            <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive">
                                                <tr>
                                                    <td align="right">Total Value (+)</td>
                                                    <td align='right'><span id="totalSubTotal"><?php echo $row->delivery_challan_sub_total; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <td align="right">Total Discount (-)</td>
                                                    <td align='right'><span id="totalDiscountAmount"><?php echo $row->delivery_challan_discount_value; ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="right">Total Tax (+)</td>
                                                    <td align='right'><span id="totalTaxAmount"><?php echo $row->delivery_challan_tax_amount; ?></span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td align="right">Grand Total (=)</td>
                                                    <td align='right'><span id="totalGrandTotal"><?php echo $row->delivery_challan_grand_total; ?></span></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="box-footer">
                                            <button type="submit" id="sales_submit" name="sales_submit" class="btn btn-info">Update</button>
                                            <span class="btn btn-default" id="sale_cancel" onclick="cancel('delivery_challan')">Cancel</span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php
                            $notes_sub_module = 0;
                            foreach ($access_sub_modules as $key => $value) {
                                if (isset($notes_sub_module_id)) {
                                    if ($notes_sub_module_id == $value->sub_module_id) {
                                        $notes_sub_module = 1;
                                    }
                                }
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
$this->load->view('service/sac_modal');
$this->load->view('product/hsn_modal');
?>
<script type="text/javascript">
    var sales_data = new Array();
    var sales_data = <?php echo json_encode($sales_data); ?>;
    var branch_state_list = <?php echo json_encode($state); ?>;
    var item_gst =<?php echo json_encode($item_gst); ?>;
    var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
    var common_settings_tax_split = "<?= $access_common_settings[0]->tax_split_equaly ?>";
</script>
<script src="<?php echo base_url('assets/js/sales/') ?>sales_basic_common.js"></script>
<script src="<?php echo base_url('assets/js/sales/') ?>sales_basic.js"></script>
