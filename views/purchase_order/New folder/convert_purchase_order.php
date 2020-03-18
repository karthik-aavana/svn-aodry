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
                <li><a href="<?php echo base_url('purchase_order'); ?>">Purchase Order</a></li>
                <li class="active">Move to Purchase</li>
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
                        <h3 class="box-title">Add Purchase</h3>
                        <a class="btn btn-sm btn-default pull-right" href="<?php echo base_url('purchase_order'); ?>">Back </a>
                        <!-- <span class="btn btn-sm btn-default pull-right" id="cancel" onclick="cancel('purchase_order')">Back</span> -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post" action="<?php echo base_url('purchase/add_purchase'); ?>">

                                <div class="col-sm-12">
                                    <div class="well">
                                        <div class="row">

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="date">Date<span class="validation-color">*</span></label>
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


                                                    <input type="hidden" id="date_old" name="date_old" value="<?php
                                                    echo $data[0]->purchase_order_date;
                                                    ?>">
                                                    <input type="hidden" id="added_date" name="added_date" value="<?php
                                                    echo $data[0]->added_date;
                                                    ?>">
                                                    <input type="hidden" id="added_user_id" name="added_user_id" value="<?php
                                                    echo $data[0]->added_user_id;
                                                    ?>">
                                                    <input type="hidden" name="purchase_order_id" value="<?php
                                                    echo $data[0]->purchase_order_id;
                                                    ?>">

                                                    <span class="validation-color" id="err_date"><?php echo form_error('date'); ?></span>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Purchase Reference Number<span class="validation-color">*</span></label>
                                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo $invoice_number; ?>" <?php
                                                    if ($access_settings[0]->invoice_readonly == 'yes')
                                                    {
                                                        echo "readonly";
                                                    }
                                                    ?>>

                                                    <span class="validation-color" id="err_reference_no"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>


                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="supplier">Supplier <span class="validation-color">*</span></label><!-- <a href="" data-toggle="modal" data-target="#customer_modal" class="pull-right">+ Add Customer</a> -->
                                                    <select class="form-control select2" id="supplier" name="supplier" style="width: 100%;">

                                                        <?php
                                                        foreach ($supplier as $key)
                                                        {
                                                            if ($key->supplier_id == $data[0]->purchase_order_party_id)
                                                            {
                                                                ?>
                                                                <option value='<?php echo $key->supplier_id ?>' <?php
                                                                if ($key->supplier_id == $data[0]->purchase_order_party_id)
                                                                {
                                                                    echo "selected";
                                                                }
                                                                ?>><?php echo $key->supplier_name ?></option>
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
                                                    <label for="nature_of_supply">Nature of Supply <span class="validation-color">*</span></label>

<?php if ($data[0]->purchase_order_nature_of_supply == "product")
{
    ?>
                                                        <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="product" readonly>
                                                        <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Goods" readonly>

<?php
}
elseif ($data[0]->purchase_order_nature_of_supply == "service")
{
    ?>
                                                        <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="service" readonly>
                                                        <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Service" readonly>

                                                    <?php
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="both" readonly>
                                                        <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Product/Service" readonly>

<?php } ?>

                                                      <!-- <select class="form-control select2" id="nature_of_supply" name="nature_of_supply">
                                                        <option value="">Select</option>
                                                        <option value="product"<?php if ('product' == $data[0]->purchase_order_nature_of_supply) echo "selected='selected'" ?>>Goods</option>
                                                        <option value="service" <?php if ('service' == $data[0]->purchase_order_nature_of_supply) echo "selected='selected'" ?>>Service</option>
                                                        <option value="both" <?php if ('both' == $data[0]->purchase_order_nature_of_supply) echo "selected='selected'" ?>>Both</option>
                                                    </select> -->
                                                    <span class="validation-color" id="err_nature_supply"><?php echo form_error('nature_of_supply'); ?></span>
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
                                                            <option value="<?= $value->country_id ?>" <?php if ($value->country_id == $data[0]->purchase_order_billing_country_id) echo "selected='selected'"; ?> ><?= $value->country_name ?></option>

    <?php
}
?>

                                                    </select>
                                                    <span class="validation-color" id="err_billing_country"><?php echo form_error('type_of_supply'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="type_of_supply">Place of Supply <span class="validation-color">*</span></label>

                                                    <select class="form-control select2" id="billing_state" name="billing_state" style="width: 100%;">
                                                        <?php
                                                        if ($data[0]->purchase_order_billing_country_id == $branch[0]->branch_country_id)
                                                        {
                                                            ?>
                                                            <?php
                                                            foreach ($state as $key)
                                                            {
                                                                ?>
                                                                <option value='<?php echo $key->state_id ?>' <?php if ($key->state_id == $data[0]->purchase_order_billing_state_id) echo "selected='selected'"; ?>>
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
                                                            <option value="0">Other Country</option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="validation-color" id="err_billing_state"><?php echo form_error('type_of_supply'); ?></span>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>
                                                    <select class="form-control select2" id="type_of_supply" name="type_of_supply">
<?php if ($data[0]->purchase_order_type_of_supply == 'regular')
{
    ?>
                                                            <option value="regular" <?php
                                                                    if ($data[0]->purchase_order_type_of_supply == 'regular')
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
    if ($data[0]->purchase_order_type_of_supply == 'import')
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
                                                <div class="form-group">
                                                    <label for="gst_payable">GST Payable on Reverse Charge <span class="validation-color">*</span></label>
                                                    <br/>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="gstPayable" value="yes" <?php if ($data[0]->purchase_order_gst_payable == 'yes') echo "checked='checked'" ?> /> Yes
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="gstPayable" value="no" <?php if ($data[0]->purchase_order_gst_payable == 'no') echo "checked='checked'" ?>/> No
                                                    </label>
                                                    <br/>
                                                    <span class="validation-color" id="err_gst_payable"><?php echo form_error('gst_payable'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="supplier_ref">Supplier Invoice Number</label>
                                                    <input type="text" class="form-control" id="supplier_ref" name="supplier_ref" value="">
                                                    <span class="validation-color" id="err_supplier_ref"><?php echo form_error('supplier_ref'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="supplier_ref">Supplier Invoice Date</label>
                                                    <input type="text" class="form-control datepicker" id="supplier_date" name="supplier_date" value="">
                                                    <span class="validation-color" id="err_supplier_supplier_date"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="supplier_ref">E-way Billing</label>
                                                    <input type="text" class="form-control" id="e_way_bill" name="e_way_bill" value="">
                                                    <span class="validation-color" id="err_e_way_bill"><?php echo form_error('e_way_bill'); ?></span>
                                                </div>
                                            </div>


                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="nature_of_supply">Purchase Order </label>
                                                    <input type="text" class="form-control" id="purchase_order" name="purchase_order" value="">
                                                    <span class="validation-color" id="err_purchase_order"><?php echo form_error('purchase_order'); ?></span>
                                                </div>
                                            </div>

                                            <!-- <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="delivery_challan">Delivery Challan</label>
                                                    <input type="text" class="form-control" id="delivery_challan" name="delivery_challan" value="">
                                                    <span class="validation-color" id="err_delivery_challan"><?php echo form_error('delivery_challan'); ?></span>
                                                </div>
                                            </div> -->

                                        </div>
                                        <div class="row">

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="delivery_challan_number">Delivery Challan Number</label>
                                                    <input type="text" class="form-control" id="delivery_challan_number" name="delivery_challan_number" value="" >
                                                    <span class="validation-color" id="err_delivery_challan_number"><?php echo form_error('delivery_challan_number'); ?></span>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="delivery_note_date">Delivery Date</label>
                                                    <input type="text" class="form-control datepicker" id="delivery_date" name="delivery_date" value="">
                                                    <span class="validation-color" id="err_delivery_date"><?php echo form_error('delivery_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="dispatch_through">Received Via</label>
                                                    <input type="text" class="form-control" id="dispatch_through" name="dispatch_through" value="">
                                                    <span class="validation-color" id="err_dispatch_through"><?php echo form_error('dispatch_through'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="currency_id">Billing Currency <span class="validation-color">*</span></label>
                                                    <select class="form-control select2" id="currency_id" name="currency_id">
<?php
foreach ($currency as $key => $value)
{
    if ($value->currency_id == $data[0]->currency_id)
    {
        echo "<option value='" . $value->currency_id . "' selected>" . $value->currency_name . "</option>";
    }
    else
    {
        echo "<option value='" . $value->currency_id . "'>" . $value->currency_name . "</option>";
    }
}
?>
                                                    </select>
                                                    <span class="validation-color" id="err_currency_id"></span>
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                <?php
                    if (in_array($transporter_sub_module_id, $access_sub_modules)) {
                        $this->load->view('sub_modules/transporter_sub_module');

                    }

                    if (in_array($shipping_sub_module_id, $access_sub_modules)) {
                        $this->load->view('sub_modules/shipping_sub_module');

                    }
                ?>

                                <div class="well">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <span class="validation-color" id="err_product"></span>
                                    </div>
                                    <?php
                                    $product_modal=0;
                                    $service_modal=0;
                                    $product_inventory_modal=0;
                                    $item_modal=0;
                                    if ($data[0]->purchase_order_nature_of_supply == 'product')
                                    {
                                    ?>
                                    <div class="col-sm-12 search_purchase_code">
                                        <?php
                                        if (in_array($product_module_id, $active_add))
                                        {
                                        if ($inventory_access == "yes")
                                        {
                                        $product_inventory_modal=1;
                                        ?>
                                        <a href="" data-toggle="modal" data-target="#product_inventory_modal" class="open_product_modal pull-left">+ Add Product</a>
                                        <?php
                                        }
                                        else
                                        {
                                        $product_modal=1;
                                        ?>
                                        <a href="" data-toggle="modal" data-target="#product_modal" class="open_product_modal pull-left">+ Add Product</a>
                                        <?php
                                        }
                                        }
                                        ?>
                                        <input id="input_purchase_code" class="form-control" type="text" name="input_purchase_code" placeholder="Enter Product Code/Name" >
                                    </div>
                                    <?php
                                    }
                                    elseif ($data[0]->purchase_order_nature_of_supply == 'service')
                                    {
                                    
                                    ?>
                                    <div class="col-sm-12 search_purchase_code">
                                        <?php
                                        if (in_array($service_module_id, $active_add))
                                        {
                                        $service_modal=1;
                                        ?>
                                        <a href="" data-toggle="modal" data-target="#service_modal" class="open_service_modal pull-left">+ Add Service</a>
                                        <?php
                                        }
                                        ?>
                                        <input id="input_purchase_code" class="form-control" type="text" name="input_purchase_code" placeholder="Enter Product Code/Name" >
                                    </div>
                                    <?php
                                    }
                                    else
                                    {
                                    
                                    ?>
                                    <div class="col-sm-12 search_purchase_code">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <?php
                                                if (in_array($service_module_id, $active_add) && in_array($product_module_id, $active_add))
                                                {
                                                $item_modal=1;
                                                ?>
                                                <a href="" data-toggle="modal" data-target="#item_modal" class="pull-left">+</a>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                            <input id="input_purchase_code" class="form-control" type="text" name="input_purchase_code" placeholder="Enter Product/Service Code/Name" >
                                        </div>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                    <div class="col-sm-12">
                                        <span class="validation-color" id="err_purchase_code"></span>
                                    </div>
                                </div>
                            </div>
                                        <div class="row">
                                <div class="col-sm-12 mt-30">
                                    <div class="form-group">
                                        <label>Inventory Items</label>
                                        <table class="table table-striped table-bordered table-condensed table-hover purchase_table table-responsive" name="purchase_data" id="purchase_data">
                                            <thead>
                                                <tr>
                                                    <th width="2%"><img src="<?php
                                                        echo base_url();
                                                    ?>assets/images/bin1.png" /></th>
                                                    <th class="span2" width="10%">Items</th>
                                                    <?php
                                                    if ($description_exist == 1)
                                                    {
                                                    ?>
                                                    <th class="span2" width="17%">Description</th>
                                                    <?php
                                                    }
                                                    ?>
                                                    <th class="span2" width="6%">Quantity</th>
                                                    <th class="span2" >Rate</th>
                                                    <?php
                                                    if ($discount_exist == 1)
                                                    {
                                                    ?>
                                                    <th class="span2" >Discount
                                                        <?php
                                                        if (in_array($discount_module_id, $active_add))
                                                        {
                                                        ?>
                                                        <a href="" data-toggle="modal" data-target="#discount_modal"><strong>+</strong></a>
                                                        <?php
                                                        }
                                                        ?>
                                                    </th>
                                                    <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($tax_exist == 1 || ($igst_exist == 1 || $cgst_exist == 1 || $sgst_exist == 1))
                                                    {
                                                    if ($discount_exist == 1)
                                                    {
                                                    ?>
                                                    <th class="span2" >Taxable Value</th>
                                                    <?php
                                                    }
                                                    
                                                    }
                                                    ?>
                                                    <?php
if ($tds_exist == 1)
 {
?>
                                       <th class="span2" width="6%">TDS (%)</th>
                                       <?php } ?>
                                                    <?php
                                                    if ($tax_exist == 1 || ($igst_exist == 1 || $cgst_exist == 1 || $sgst_exist == 1))
                                                    {
                                                    
                                                    ?>
                                                    <th class="span2" >TAX (%) </th>
                                                    <?php
                                                    }
                                                    ?>
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
                                                    <td><a class='deleteRow'> <img src='<?php
                                                        echo base_url();
                                                        ?>assets/images/bin_close.png' /></a><input type='hidden' name='item_key_value'  value="<?= $i ?>"><input type='hidden' name='item_id' value="<?php
                                                        echo $key->item_id;
                                                        ?>"><input type='hidden' name='item_type' value="<?php
                                                        echo $key->item_type;
                                                    ?>"></td>
                                                    <?php
                                                    if ($key->item_type == 'product' || $key->item_type == 'product_inventory')
                                                    {
                                                    ?>
                                                    <td>
                                                        <input type='hidden' name='item_code' value='<?php
                                                        echo $key->product_code;
                                                        ?>'>
                                                        <?php
                                                        echo $key->product_name;
                                                        ?><br>HSN: <?php
                                                        echo $key->product_hsn_sac_code;
                                                        ?>
                                                    </td>
                                                    <?php
                                                    }
                                                    else
                                                    {
                                                    ?>
                                                    <td>
                                                        <input type='hidden' name='item_code' value='<?php
                                                        echo $key->service_code;
                                                        ?>'>
                                                        <?php
                                                        echo $key->service_name;
                                                        ?><br>SAC: <?php
                                                        echo $key->service_hsn_sac_code;
                                                        ?>
                                                    </td>
                                                    <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($description_exist == 1)
                                                    {
                                                    ?>
                                                    <td><input type='text' class='form-control form-fixer' name='item_description' value='<?php
                                                        echo $key->purchase_order_item_description;
                                                    ?>'></td>
                                                    <?php
                                                    }
                                                    ?>
                                                    <td><input type='text' class='form-control form-fixer text-center float_number' value='<?php
                                                        echo $key->purchase_order_item_quantity ? $key->purchase_order_item_quantity : 0;
                                                    ?>' data-rule='quantity' name='item_quantity'></td>
                                                    <td><input type='text' class='form-control form-fixer text-right float_number' name='item_price' value='<?php
                                                        echo $key->purchase_order_item_unit_price ? $key->purchase_order_item_unit_price : 0;
                                                        ?>'><span id='item_sub_total_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?php
                                                            echo $key->purchase_order_item_sub_total ? $key->purchase_order_item_sub_total : 0;
                                                        ?></span><input type='hidden' class='form-control form-fixer text-right' style='' value='<?php
                                                        echo $key->purchase_order_item_sub_total ? $key->purchase_order_item_sub_total : 0;
                                                    ?>' name='item_sub_total'></td>
                                                    <?php
                                                    if ($discount_exist == 1)
                                                    {
                                                    ?>
                                                    <td><input type='hidden' name='item_discount_id' value='<?php
                                                        echo $key->purchase_order_item_discount_id ? $key->purchase_order_item_discount_id : 0;
                                                        ?>'>
                                                        <input type='hidden' name='item_discount_value' value='<?php
                                                        echo $key->item_discount_value ? $key->item_discount_value : 0;
                                                        ?>'>
                                                        <input type='hidden' name='item_discount_amount' value='<?php
                                                        echo $key->purchase_order_item_discount_amount ? $key->purchase_order_item_discount_amount : 0;
                                                        ?>'>
                                                        <div class="form-group" style="margin-bottom:0px !important;">
                                                            <select class="form-control open_discount form-fixer select2" name="item_discount" style="width: 100%;">
                                                                <option value="">Select</option>
                                                                <?php
                                                                foreach ($discount as $key3 => $value3)
                                                                {
                                                                if ($value3->discount_id == $key->purchase_order_item_discount_id)
                                                                {
                                                                echo "<option value='" . $value3->discount_id . "-" . $value3->discount_value . "' selected>" . $value3->discount_value . "</option>";
                                                                }
                                                                else
                                                                {
                                                                echo "<option value='" . $value3->discount_id . "-" . $value3->discount_value . "'>" . $value3->discount_value . "</option>";
                                                                }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <span id='item_discount_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?php
                                                            echo $key->purchase_order_item_discount_amount ? $key->purchase_order_item_discount_amount : 0;
                                                        ?></span>
                                                    </td>
                                                    <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($tax_exist == 1 || ($igst_exist == 1 || $cgst_exist == 1 || $sgst_exist == 1))
                                                    {
                                                    if ($discount_exist == 1)
                                                    {
                                                    ?>
                                                    <!-- tax area -->
                                                    <td align='right'><input type='hidden' name='item_taxable_value' value='<?= $key->purchase_order_item_taxable_value ? $key->purchase_order_item_taxable_value : 0 ?>' >
                                                        <span id='item_taxable_value_lbl_<?= $i ?>'><?= $key->purchase_order_item_taxable_value ? $key->purchase_order_item_taxable_value : 0 ?></span>
                                                    </td>
                                                    <?php
                                                    }
                                                    
                                                    }
                                                    ?>
                                                    <?php
                                                   
                                                    if ($tds_exist == 1)
                                                    {
                                                       ?>
                                                    <td>
                                                       <?php

                                                      if($key->tds_module_type=="" || $key->tds_module_type==NULL)
                                                      {
                                                        $input_type="hidden";
                                                        echo "<br/>";
                                                        echo "<br/>";

                                                      } 
                                                      else
                                                      {
                                                        $input_type="text";
                                                      } 
                                                    ?>
         

            <input type='hidden' name='item_tds_id' value='<?= $key->purchase_order_item_tds_id ? $key->purchase_order_item_tds_id : 0 ?>'><input type='hidden' name='item_tds_type' value='<?= $key->tds_module_type ? $key->tds_module_type : '' ?>'><input type='<?= $input_type ?>' class='form-control form-fixer text-center float_number' name='item_tds_percentage' value='<?= $key->purchase_order_item_tds_percentage ? $key->purchase_order_item_tds_percentage : 0 ?>'><input type='hidden' name='item_tds_amount' value='<?= $key->purchase_order_item_tds_amount ? $key->purchase_order_item_tds_amount : 0 ?>'><span id='item_tds_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?= $key->purchase_order_item_tds_amount ? $key->purchase_order_item_tds_amount : 0 ?></span></td>
                                                  
                                                    <!-- tds area -->
                                                  
                                                    <?php
                                                    }
                                                    
                                                    
                                                    ?>
                                                    <?php
                                                    if ($tax_exist == 1 || ($igst_exist == 1 || $cgst_exist == 1 || $sgst_exist == 1))
                                                    {
                                                   ?>
                                                    <td><input type='hidden' name='item_tax_id' value='<?= $key->purchase_order_item_tax_id ? $key->purchase_order_item_tax_id : 0 ?>'>
                                                        <input type='hidden' name='item_tax_percentage' value='<?= $key->purchase_order_item_tax_percentage ? $key->purchase_order_item_tax_percentage : 0 ?>'>
                                                        <input type='hidden' name='item_tax_amount' value='<?= $key->purchase_order_item_tax_amount ? $key->purchase_order_item_tax_amount : 0 ?>'>
                                                        <div class="form-group" style="margin-bottom:0px !important;">
                                                            <select class="form-control open_tax form-fixer select2" name="item_tax" style="width: 100%;">
                                                                <option value="">Select</option>
                                                                <?php
                                                                foreach ($tax as $key3 => $value3)
                                                                {
                                                                if ($value3->tax_id == $key->purchase_order_item_tax_id)
                                                                {
                                                                echo "<option value='" . $value3->tax_id . "-" . $value3->tax_value . "' selected>" . $value3->tax_value . "</option>";
                                                                }
                                                                else
                                                                {
                                                                echo "<option value='" . $value3->tax_id . "-" . $value3->tax_value . "'>" . $value3->tax_value . "</option>";
                                                                }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <span id='item_tax_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?= $key->purchase_order_item_tax_amount ? $key->purchase_order_item_tax_amount : 0 ?></span>
                                                    </td>
                                                    <?php
                                                    }
                                                    ?>
                                                    <!-- tax area  -->
                                                    <td><input type='text' class='float_number form-control form-fixer text-right' name='item_grand_total' value='<?php
                                                        echo $key->purchase_order_item_grand_total ? $key->purchase_order_item_grand_total : 0;
                                                    ?>'></td>
                                                    <?php
                                                    $purchase_order_temp = array(
                                                    "item_key_value" => $i,
                                                    "item_id" => $key->item_id,
                                                    "item_type" => $key->item_type,
                                                    "item_quantity" => $key->purchase_order_item_quantity,
                                                    "item_price" => $key->purchase_order_item_unit_price ? $key->purchase_order_item_unit_price : 0,
                                                    "item_description" => $key->purchase_order_item_description,
                                                    "item_sub_total" => $key->purchase_order_item_sub_total ? $key->purchase_order_item_sub_total : 0,
                                                    "item_discount_amount" => $key->purchase_order_item_discount_amount ? $key->purchase_order_item_discount_amount : 0,
                                                    "item_discount_id" => $key->purchase_order_item_discount_id ? $key->purchase_order_item_discount_id : 0,
                                                    "item_discount_value" => $key->item_discount_value ? $key->item_discount_value : 0,
                                                    "item_tax_amount" => $key->purchase_order_item_tax_amount ? $key->purchase_order_item_tax_amount : 0,
                                                    "item_tax_id" => $key->purchase_order_item_tax_id ? $key->purchase_order_item_tax_id : 0,
                                                    "item_tax_percentage" => $key->purchase_order_item_tax_percentage ? $key->purchase_order_item_tax_percentage : 0,
                                                    "item_tds_amount" => $key->purchase_order_item_tds_amount ? $key->purchase_order_item_tds_amount : 0,
                                                    "item_tds_id" => $key->purchase_order_item_tds_id ? $key->purchase_order_item_tds_id : 0,
                                                    "item_tds_percentage" => $key->purchase_order_item_tds_percentage ? $key->purchase_order_item_tds_percentage : 0,
                                                    "item_taxable_value" => $key->purchase_order_item_taxable_value ? $key->purchase_order_item_taxable_value : 0,
                                                    "item_grand_total" => $key->purchase_order_item_grand_total ? $key->purchase_order_item_grand_total : 0
                                                    );
                                                    if ($key->item_type == 'product' || $key->item_type == 'product_inventory')
                                                    {
                                                    $purchase_order_temp['item_code'] = $key->product_code;
                                                    }
                                                    else
                                                    {
                                                    $purchase_order_temp['item_code'] = $key->service_code;
                                                    }
                                                    ?>
                                                </tr>
                                                <?php
                                                $purchase_data[$i] = $purchase_order_temp;
                                                $i++;
                                                }
                                                $purchase     = htmlspecialchars(json_encode($purchase_data));
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
                                        <input type="hidden" class="form-control" id="product_module_id" name="product_module_id" value="<?= $product_module_id ?>" readonly>
                                        <input type="hidden" class="form-control" id="service_module_id" name="service_module_id" value="<?= $service_module_id ?>" readonly>
                                        <input type="hidden" class="form-control" id="supplier_module_id" name="supplier_module_id" value="<?= $supplier_module_id ?>" readonly>
                                        <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php
                                        echo $data[0]->purchase_order_date;
                                        ?>" readonly>
                                        <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $purchase_module_id ?>" readonly>
                                        <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>
                                        <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $data[0]->purchase_order_invoice_number ?>" readonly>
                                        <input type="hidden" class="form-control" id="section_area" name="section_area" value="edit_purchase" readonly>
                                        <input type="hidden" name="total_sub_total" id="total_sub_total" value="<?= $data[0]->purchase_order_sub_total; ?>">
                                        <input type="hidden" name="total_taxable_amount" id="total_taxable_amount" value="<?= $data[0]->purchase_order_taxable_value; ?>">
                                        <input type="hidden" name="total_discount_amount" id="total_discount_amount" value="<?= $data[0]->purchase_order_discount_amount; ?>">
                                        <input type="hidden" name="total_tax_amount" id="total_tax_amount" value="<?= $data[0]->purchase_order_tax_amount; ?>">
                                        <input type="hidden" name="total_tds_amount" id="total_tds_amount" value="<?= $data[0]->purchase_order_tds_amount; ?>">

                                        <input type="hidden" name="total_igst_amount" id="total_igst_amount" value="">
                                        <input type="hidden" name="total_cgst_amount" id="total_cgst_amount" value="">
                                        <input type="hidden" name="total_sgst_amount" id="total_sgst_amount" value="">
                                        <input type="hidden" name="total_grand_total" id="total_grand_total" value="<?php
                                        echo ($data[0]->purchase_order_grand_total + $data[0]->round_off_amount);
                                        ?>">
                                        <input type="hidden" name="table_data" id="table_data" value="<?php
                                        echo $purchase;
                                        ?>">
                                        <input type="hidden" name="total_other_amount" id="total_other_amount" value="<?= $data[0]->total_other_amount; ?>">
                                        <?php
                                        $charges_sub_module = 0;
                                        if (in_array($charges_sub_module_id, $access_sub_modules) || ($data[0]->total_freight_charge>0 || $data[0]->total_insurance_charge >0 || $data[0]->total_packing_charge >0 || $data[0]->total_incidental_charge >0 || $data[0]->total_inclusion_other_charge >0 || $data[0]->total_exclusion_other_charge >0))
                                        {
                                        $this->load->view('sub_modules/charges_sub_module');
                                        $charges_sub_module = 1;
                                        }
                                        if ($charges_sub_module == 1)
                                        {
                                        ?>
                                        <input type="hidden" name="total_freight_charge" id="total_freight_charge" value="<?php
                                        echo $data[0]->total_freight_charge;
                                        ?>">
                                        <input type="hidden" name="total_insurance_charge" id="total_insurance_charge" value="<?php
                                        echo $data[0]->total_insurance_charge;
                                        ?>">
                                        <input type="hidden" name="total_packing_charge" id="total_packing_charge" value="<?php
                                        echo $data[0]->total_packing_charge;
                                        ?>">
                                        <input type="hidden" name="total_incidental_charge" id="total_incidental_charge" value="<?php
                                        echo $data[0]->total_incidental_charge;
                                        ?>">
                                        <input type="hidden" name="total_other_inclusive_charge" id="total_other_inclusive_charge" value="<?php
                                        echo $data[0]->total_inclusion_other_charge;
                                        ?>">
                                        <input type="hidden" name="total_other_exclusive_charge" id="total_other_exclusive_charge" value="<?php
                                        echo $data[0]->total_exclusion_other_charge;
                                        ?>">
                                        <?php
                                        }
                                        ?>
                                        <!-- Hidden Field -->
                                        <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive">
                                            <tr>
                                                <td align="right">Total Value (+)</td>
                                                <td align='right'><span id="totalSubTotal"><?php
                                                    echo $data[0]->purchase_order_sub_total;
                                                ?></span></td>
                                            </tr>
                                         <tr <?php
if ($discount_exist != 1)
 {
?> style="display: none;" <?php } ?>>
                                                <td align="right">Total Discount (-)</td>
                                                <td align='right'><span id="totalDiscountAmount"><?php
                                                    echo $data[0]->purchase_order_discount_amount;
                                                ?></span>
                                            </td>
                                        </tr>
                                         <tr <?php
if ($tax_exist != 1 && $igst_exist != 1 && $cgst_exist != 1 && $sgst_exist != 1)
 {
?> style="display: none;" <?php } ?>>
                                          <td align="right">Total Tax (+)</td>
                                            <td align='right'><span id="totalTaxAmount"><?php
                                                echo $data[0]->purchase_order_tax_amount;
                                            ?></span>
                                        </td>
                                    </tr>
                                                                      <tr <?php
if ($tds_exist != 1)
 {
?>
                                    style="display: none;" <?php } ?>
                                    >
                                    <td align="right"><?php echo 'TDS Amount'; ?> (+-)</td>
                                    <td align='right'>
                                       <span id="totalTdsAmount"><?php
                                                echo $data[0]->purchase_order_tds_amount;
                                            ?></span>
                                    </td>
                                 </tr>
                                    <?php
                                    if ($charges_sub_module == 1)
                                    {
                                    ?>
                                    <tr id="freight_charge_tr">
                                        <td align="right">Freight Charge (+)</td>
                                        <td align='right'>
                                            <span id="freight_charge"><?php
                                                echo $data[0]->total_freight_charge;
                                            ?></span>
                                        </td>
                                    </tr>
                                    <tr id="insurance_charge_tr">
                                        <td align="right">Insurance Charge (+)</td>
                                        <td align='right'>
                                            <span id="insurance_charge"><?php
                                                echo $data[0]->total_insurance_charge;
                                            ?></span>
                                        </td>
                                    </tr>
                                    <tr id="packing_charge_tr">
                                        <td align="right">Packing & Forwarding Charge (+)</td>
                                        <td align='right'>
                                            <span id="packing_charge"><?php
                                                echo $data[0]->total_packing_charge;
                                            ?></span>
                                        </td>
                                    </tr>
                                    <tr id="incidental_charge_tr">
                                        <td align="right">Incidental Charge (+)</td>
                                        <td align='right'>
                                            <span id="incidental_charge"><?php
                                                echo $data[0]->total_incidental_charge;
                                            ?></span>
                                        </td>
                                    </tr>
                                    <tr id="other_inclusive_charge_tr">
                                        <td align="right">Other Inclusive Charge (+)</td>
                                        <td align='right'>
                                            <span id="other_inclusive_charge"><?php
                                                echo $data[0]->total_inclusion_other_charge;
                                            ?></span>
                                        </td>
                                    </tr>
                                    <tr id="other_exclusive_charge_tr">
                                        <td align="right">Other Exclusive Charge (-)</td>
                                        <td align='right'>
                                            <span id="other_exclusive_charge"><?php
                                                echo $data[0]->total_exclusion_other_charge;
                                            ?></span>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr>
                                        <td align="right">Grand Total (=)</td>
                                        <td align='right'><span id="totalGrandTotal"><?php
                                            echo ($data[0]->purchase_order_grand_total + $data[0]->round_off_amount);
                                        ?></span></td>
                                    </tr>
                                </table>
                                <?php
                                $checked        = "";
                                $value_selected = "";
                                if ($data[0]->round_off_amount > 0 || $data[0]->round_off_amount < 0)
                                {
                                $checked     = "checked";
                                $grand_total = ($data[0]->purchase_order_grand_total + $data[0]->round_off_amount);
                                $next_val    = ceil($grand_total);
                                $prev_val    = floor($grand_total);
                                if ($data[0]->round_off_amount < 0)
                                {
                                $value_selected = "selected";
                                }
                                
                                }
                                ?>
                                <!-- Round Off -->
                                <table id="table-round-off" class="table table-striped table-bordered table-condensed table-hover table-responsive">
                                    <tr>
                                        <td align="right" width="50%">Round Off</td>
                                        <td align="right" width="50%">
                                            <label>
                                                <input type="radio" name="round_off_key" value="no" class="minimal" <?php
                                                if ($checked == "")
                                                {
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
                                    if ($checked == "checked")
                                    {
                                    ?>
                                    <tr id="round_off_select">
                                        <td align="right" width="50%">Select the nearest value</td>
                                        <td align="right" width="50%">
                                            <select id="round_off_value" class="form-control" name="round_off_value">
                                                <?php
                                                if ($next_val == $prev_val)
                                                {
                                                ?>
                                                <option value="<?= $next_val ?>"><?= $next_val ?></option>
                                                <?php
                                                }
                                                else
                                                {
                                                ?>
                                                <option value="<?= $prev_val ?>" <?php
                                                    if ($value_selected == "")
                                                    {
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
                                    }
                                    else
                                    {
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
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($tax_exist == 1)
                    {
                    $settings_tax_type = "single_tax";
                    }
                    else if ($igst_exist == 1 || $cgst_exist == 1 || $sgst_exist == 1)
                    {
                    $settings_tax_type = "gst";
                    }
                    else
                    {
                    $settings_tax_type = "no_tax";
                    }
                    if ($discount_exist == 1)
                    {
                    $discount_visible = "yes";
                    }
                    else
                    {
                    $discount_visible = "no";
                    }
                    if ($description_exist == 1)
                    {
                    $description_visible = "yes";
                    }
                    else
                    {
                    $description_visible = "no";
                    }
                    if ($tds_exist == 1)
                    {
                    $tds_visible = "yes";
                    }
                    else
                    {
                    $tds_visible = "no";
                    }
                    ?>
                    <input type="hidden" name="tax_type" id="tax_type" value="<?= $settings_tax_type ?>">


                                <div class="col-sm-12">
                                    <div class="box-footer">
                                        <button type="submit" id="purchase_submit" name="submit" value="add" class="btn btn-info">Add</button>
                                        <button type="submit" id="purchase_pay_now" name="submit" value="pay_now" class="btn btn-success">Pay Now</button>
                                        <span class="btn btn-default" id="cancel" onclick="cancel('purchase_order')">Cancel</span>
                                    </div>
                                </div>

<?php
$notes_sub_module = 0;
if (in_array($notes_sub_module_id, $access_sub_modules))
{
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
$this->load->view('supplier/supplier_modal');
$this->load->view('product/product_modal');
$this->load->view('discount/discount_modal');
$this->load->view('tax/tax_modal');
$this->load->view('general/modal/hsn_modal');
$this->load->view('category/category_modal');
$this->load->view('subcategory/subcategory_modal');
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
    var purchase_data = <?php echo json_encode($purchase_data); ?>;
    var branch_state_list = <?php echo json_encode($state); ?>;
    var count_data= <?= $countData; ?>;
    var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
    var common_settings_amount_precision = "<?= $access_common_settings[0]->amount_precision ?>";
    var settings_tax_percentage = "<?= $access_common_settings[0]->tax_split_percentage ?>";
    var common_settings_inventory_advanced= "<?= $inventory_access ?>";
    var settings_tax_type = "<?= $settings_tax_type ?>";
    var settings_discount_visible = "<?= $discount_visible ?>";
    var settings_description_visible = "<?= $description_visible ?>";
    var settings_tds_visible = "<?= $tds_visible ?>";
    var settings_item_editable = "yes";
</script>
<script src="<?php echo base_url('assets/js/purchase/') ?>purchase_basic_common.js"></script>
