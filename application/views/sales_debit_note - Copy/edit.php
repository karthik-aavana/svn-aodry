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
            <li><a href="<?php echo base_url('sales_debit_note'); ?>">Sales Debit Note</a></li>
            <li class="active">Edit Sales Debit Note</li>
        </ol>
        </h5>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Sales Debit Note</h3>
                        <a class="btn btn-sm btn-default pull-right" href="<?php echo base_url('sales_debit_note'); ?>">Back </a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post" action="<?php echo base_url('sales_debit_note/edit_sales_debit_note'); ?>">
                                <div class="col-sm-12">
                                    <div class="well">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="date">Date<span class="validation-color">*</span></label>
                                                    <div class="input-group date">
                                                        <input type="hidden" name="sales_debit_note_id" value="<?= $data[0]->sales_debit_note_id ?>">
                                                        <input type="text" style="background: #fff;" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?php echo $data[0]->sales_debit_note_date; ?>" readonly>
                                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                    </div>
                                                    <span class="validation-color" id="err_date"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="invoice_number">Debit Note Number<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?= $data[0]->sales_debit_note_invoice_number ?>" <?php
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
                                                    <label for="customer">Customer <span class="validation-color">*</span></label>
                                                    <!-- <?php
                                                    if (isset($other_modules_present['customer_module_id']) && $other_modules_present['customer_module_id'] != "")
                                                    {
                                                    ?>
                                                    <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#customer_modal" class="pull-right">+ Add Customer</a>
                                                    <?php }
                                                    ?> -->
                                                    <select class="form-control select2" autofocus="on" id="customer" name="customer" style="width: 100%;">
                                                        <!-- <option value="">Select</option> -->
                                                        <?php
                                                        foreach ($customer as $row)
                                                        {
                                                        if ($data[0]->sales_debit_note_party_type == 'customer' && $data[0]->sales_debit_note_party_id == $row->customer_id)
                                                        {
                                                        echo "<option value='$row->customer_id-$row->customer_country_id-$row->customer_state_id' selected>$row->customer_name</option>";
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
                                                    <select class="form-control select2" id="sales_invoice_number" name="sales_invoice_number" style="width: 100%;">
                                                        <option value='<?= $data[0]->sales_id ?>'><?= $data[0]->sales_invoice_number ?></option>
                                                    </select>
                                                    <span class="validation-color" id="err_sales_invoice_number"><?php echo form_error('sales_invoice_number'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="nature_of_supply">Nature of Supply <span class="validation-color">*</span></label>
                                                    <?php if ($data[0]->sales_debit_note_nature_of_supply == "product")
                                                    {
                                                    ?>
                                                    <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="product" readonly>
                                                    <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Goods" readonly>
                                                    <?php
                                                    }
                                                    elseif ($data[0]->sales_debit_note_nature_of_supply == "service")
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
                                                        <option value="product" <?php
                                                            if ($data[0]->sales_debit_note_nature_of_supply == 'product')
                                                            {
                                                            echo "selected";
                                                            }
                                                        ?>>Goods</option>
                                                        <option value="service"<?php
                                                            if ($data[0]->sales_debit_note_nature_of_supply == 'service')
                                                            {
                                                            echo "selected";
                                                            }
                                                        ?>>Service</option>
                                                        <option value="both" <?php
                                                            if ($data[0]->sales_debit_note_nature_of_supply == 'both')
                                                            {
                                                            echo "selected";
                                                            }
                                                        ?>>Both</option>
                                                    </select> -->
                                                    <span class="validation-color" id="err_nature_supply"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="type_of_supply">Billing Country <span class="validation-color">*</span></label>
                                                    <select class="form-control select2" id="billing_country" name="billing_country" >
                                                        <option value="">Select</option>
                                                        <?php
                                                        foreach ($country as $key)
                                                        {
                                                        ?>
                                                        <option value='<?php echo $key->country_id ?>' <?php
                                                            if ($key->country_id == $data[0]->sales_debit_note_billing_country_id)
                                                            {
                                                            echo "selected";
                                                            }
                                                            ?>>
                                                            <?php echo $key->country_name; ?>
                                                        </option>
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
                                                        if ($data[0]->sales_debit_note_billing_country_id == $branch[0]->branch_country_id)
                                                        {
                                                        ?>
                                                        <?php
                                                        foreach ($state as $key)
                                                        {
                                                        ?>
                                                        <option value='<?php echo $key->state_id ?>' <?php
                                                            if ($key->state_id == $data[0]->sales_debit_note_billing_state_id)
                                                            {
                                                            echo "selected";
                                                            }
                                                            ?>>
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
                                                    <span class="validation-color" id="err_billing_state"><?php echo form_error('billing_state'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>
                                                    <select class="form-control select2" id="type_of_supply" name="type_of_supply">
                                                        <?php if ($data[0]->sales_debit_note_type_of_supply == 'regular')
                                                        {
                                                        ?>
                                                        <option value="regular" <?php
                                                            if ($data[0]->sales_debit_note_type_of_supply == 'regular')
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
                                                            if ($data[0]->sales_debit_note_type_of_supply == 'import')
                                                            {
                                                            echo "selected";
                                                            }
                                                        ?>>Import</option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="validation-color" id="err_type_supply"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
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
                                            
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="gst_payable">GST Payable on Reverse Charge <span class="validation-color">*</span></label>
                                                    <br/>
                                                    <label class="radio-inline">
                                                        <input class="minimal" type="radio" name="gstPayable" value="yes" <?php
                                                        if ($data[0]->sales_debit_note_gst_payable == "yes")
                                                        {
                                                        echo 'checked="checked"';
                                                        }
                                                        ?> /> Yes
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input class="minimal" type="radio" name="gstPayable" value="no" <?php
                                                        if ($data[0]->sales_debit_note_gst_payable == "no")
                                                        {
                                                        echo 'checked="checked"';
                                                        }
                                                        ?> /> No
                                                    </label>
                                                    <br/>
                                                    <span class="validation-color" id="err_gst_payable"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php
                                    foreach ($access_sub_modules as $key => $value)
                                    {
                                    if (isset($transporter_sub_module_id))
                                    {
                                    if ($transporter_sub_module_id == $value->sub_module_id)
                                    {
                                    $this->load->view('sub_modules/transporter_sub_module');
                                    }
                                    }
                                    }
                                    foreach ($access_sub_modules as $key => $value)
                                    {
                                    if (isset($shipping_sub_module_id))
                                    {
                                    if ($shipping_sub_module_id == $value->sub_module_id)
                                    {
                                    $this->load->view('sub_modules/shipping_sub_module');
                                    }
                                    }
                                    }
                                    if (isset($other_modules_present['accounts_module_id']) && $other_modules_present['accounts_module_id'] != "")
                                    {
                                    foreach ($access_sub_modules as $key => $value)
                                    {
                                    if (isset($accounts_sub_module_id))
                                    {
                                    if ($accounts_sub_module_id == $value->sub_module_id)
                                    {
                                    $this->load->view('sub_modules/accounts_sub_module');
                                    break;
                                    }
                                    }
                                    }
                                    }
                                    ?>
                                    
                                    
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <span class="validation-color" id="err_product"></span>
                                        </div>
                                        <?php
                                        if ($access_settings[0]->item_access == 'product')
                                        {
                                        ?>
                                        <div class="col-sm-12 search_sales_code">
                                            <?php
                                            if (isset($other_modules_present['product_module_id']) && $other_modules_present['product_module_id'] != "")
                                            {
                                            ?>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#product_modal" class="open_product_modal pull-left">+</a></div>
                                                    <?php } ?>
                                                    <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product Code/Name" >
                                                </div>
                                            </div>
                                            <?php
                                            }
                                            elseif ($access_settings[0]->item_access == 'service')
                                            {
                                            ?>
                                            <div class="col-sm-12 search_sales_code">
                                                <?php
                                                if (isset($other_modules_present['service_module_id']) && $other_modules_present['service_module_id'] != "")
                                                {
                                                ?>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <a href="" data-toggle="modal" data-target="#service_modal" class="open_service_modal pull-left">+</a></div>
                                                        <?php } ?>
                                                    <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product Code/Name" ></div>
                                                </div>
                                                <?php
                                                }
                                                else
                                                {
                                                ?>
                                                <div class="col-sm-12 search_sales_code">
                                                    <?php
                                                    if (isset($other_modules_present['product_module_id']) && isset($other_modules_present['service_module_id']) && $other_modules_present['product_module_id'] != "" && $other_modules_present['service_module_id'] != "")
                                                    {
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
                                                <div class="col-sm-8">
                                                    <span class="validation-color" id="err_sales_code"></span>
                                                </div>
                                                
                                                
                                                <div class="col-sm-12 mt-15">
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
                                                                    <th class="span2" >Discount
                                                                        <?php
                                                                        if (isset($other_modules_present['discount_module_id']) && $other_modules_present['discount_module_id'] != "")
                                                                        {
                                                                        ?>
                                                                        <a href="" data-toggle="modal" data-target="#discount_modal"><strong>+</strong></a>
                                                                        <?php } ?>
                                                                    </th>
                                                                    <th class="span2" >Taxable Value</th>
                                                                    <th class="span2" >IGST(%)</th>
                                                                    <th class="span2" >CGST(%)</th>
                                                                    <th class="span2" >SGST(%)</th>
                                                                    <th class="span2" >Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="sales_table_body">
                                                                <?php
                                                                $i   = 0;
                                                                $tot = 0;
                                                                foreach ($items as $key)
                                                                {
                                                                ?>
                                                                <tr id="<?= $i ?>">
                                                                    <td>
                                                                        <a class='deleteRow'> <img src='<?php echo base_url(); ?>assets/images/bin_close.png' /></a>
                                                                        <input type='hidden' name='item_key_value' value="<?php echo $i ?>">
                                                                        <input type='hidden' name='item_id' value="<?php echo $key->item_id ?>">
                                                                        <input type='hidden' name='item_type' value="<?php echo $key->item_type ?>">
                                                                        <?php if ($key->item_type == 'product' || $key->item_type == 'product_inventory')
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
                                                                        <?php if ($key->item_type == 'product' || $key->item_type == 'product_inventory')
                                                                        {
                                                                        ?>
                                                                        <td><?php echo $key->product_name; ?><br>HSN: <?php echo $key->product_hsn_sac_code; ?></td>
                                                                        <?php
                                                                        }
                                                                        else
                                                                        {
                                                                        ?>
                                                                        <td><?php echo $key->service_name; ?><br>SAC: <?php echo $key->service_hsn_sac_code; ?></td>
                                                                        <?php } ?>
                                                                        <td><input type='text' name='item_description' class='form-control form-fixer' value="<?php echo $key->sales_debit_note_item_description ?>"></td>
                                                                        <td><input type="text" class="float_number form-control form-fixer text-center" value="<?php echo $key->sales_debit_note_item_quantity ?>" data-rule="quantity" name='item_quantity'>
                                                                        </td>
                                                                        <td>
                                                                            <input type='text' class='float_number form-control form-fixer text-right' name='item_price' value='<?php echo $key->sales_debit_note_item_unit_price ?>'>
                                                                            <span id='item_sub_total_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?php echo $key->sales_debit_note_item_sub_total; ?></span>
                                                                            <input type='hidden' class='form-control' style='' value='<?php echo $key->sales_debit_note_item_sub_total ?>' name='item_sub_total' readonly>
                                                                        </td>
                                                                        <td align="right">
                                                                            <div class="form-group">
                                                                                <select class="form-control open_discount" name="item_discount" style="width: 100%;">
                                                                                    <option value="">Select</option>
                                                                                    <?php
                                                                                    $item_discount_value = 0;
                                                                                    foreach ($discount as $dis)
                                                                                    {
                                                                                    if ($key->sales_debit_note_item_discount_id == $dis->discount_id)
                                                                                    {
                                                                                    $item_discount_value = $dis->discount_value;
                                                                                    ?>
                                                                                    <option value='<?php echo $dis->discount_id . '-' . $dis->discount_value ?>' selected="selected"><?php echo $dis->discount_value . '%'; ?></option>
                                                                                    <?php
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                    ?>
                                                                                    <option value='<?php echo $dis->discount_id . '-' . $dis->discount_value ?>'>
                                                                                    <?php echo $dis->discount_value . '%'; ?></option>
                                                                                    <?php
                                                                                    }
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                            <input type="hidden" name="item_discount_amount" value="<?php echo $key->sales_debit_note_item_discount_amount; ?>">
                                                                            <input type="hidden" name="item_discount_value" value="<?php echo $item_discount_value; ?>">
                                                                        </td>
                                                                        <td align="right">
                                                                            <input type='text' class='float_number form-control form-fixer text-right' name='item_taxable_value' value='<?= $key->sales_debit_note_item_taxable_value ?>'>
                                                                        </td>
                                                                        <td>
                                                                            <input type='hidden' name='item_tax_percentage' value="<?= $key->sales_debit_note_item_tax_percentage ?>">
                                                                            <input type='hidden' name='item_tax_amount' value="<?= $key->sales_debit_note_item_tax_amount ?>">
                                                                            <input type="text" name="item_igst" class="float_number form-control form-fixer text-center" value="<?= $key->sales_debit_note_item_igst_percentage ?>" <?php if ($data[0]->sales_debit_note_billing_state_id == $branch[0]->branch_state_id) echo "readonly"; ?>>
                                                                            <input type="hidden" name="item_igst_amount" class="form-control form-fixer" value="<?php echo $key->sales_debit_note_item_igst_amount; ?>" >
                                                                            <span id="item_igst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red;"><?php echo $key->sales_debit_note_item_igst_amount; ?></span>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="item_cgst" class="float_number form-control form-fixer text-center" value="<?= $key->sales_debit_note_item_cgst_percentage ?>" <?php if ($data[0]->sales_debit_note_billing_state_id != $branch[0]->branch_state_id) echo "readonly"; ?>>
                                                                            <input type="hidden" name="item_cgst_amount" class="form-control form-fixer" value="<?php echo $key->sales_debit_note_item_cgst_amount; ?>">
                                                                            <span id="item_cgst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red;">
                                                                            <?php echo $key->sales_debit_note_item_cgst_amount; ?></span>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="item_sgst" class="float_number form-control form-fixer text-center" value="<?= $key->sales_debit_note_item_sgst_percentage ?>" <?php if ($data[0]->sales_debit_note_billing_state_id != $branch[0]->branch_state_id) echo "readonly"; ?>>
                                                                            <input type="hidden" name="item_sgst_amount" class="form-control form-fixer text-center" value="<?php echo $key->sales_debit_note_item_sgst_amount; ?>">
                                                                            <span id="item_sgst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red;"><?php echo $key->sales_debit_note_item_sgst_amount; ?></span>
                                                                        </td>
                                                                        <td align="right">
                                                                            <input type="text" class="float_number form-control form-fixer text-right" name="item_grand_total" value="<?php echo $key->sales_debit_note_item_grand_total; ?>">
                                                                        </td>
                                                                        <?php
                                                                        $temp                                            = array(
                                                                        "item_id"   => $key->item_id,
                                                                        "item_type" => $key->item_type,
                                                                        "item_igst" => $key->sales_debit_note_item_igst_percentage,
                                                                        "item_cgst" => $key->sales_debit_note_item_cgst_percentage,
                                                                        "item_sgst" => $key->sales_debit_note_item_sgst_percentage
                                                                        );
                                                                        $item_gst[$key->item_id . '-' . $key->item_type] = $temp;
                                                                        ?>
                                                                    </tr>
                                                                    <?php
                                                                    $sales_data[$i]['item_id']                    = $key->item_id;
                                                                    $sales_data[$i]['item_type']                  = $key->item_type;
                                                                    if ($key->item_type == 'product' || $key->item_type == 'product_inventory')
                                                                    {
                                                                    $sales_data[$i]['item_code'] = $key->product_code;
                                                                    }
                                                                    else
                                                                    {
                                                                    $sales_data[$i]['item_code'] = $key->service_code;
                                                                    }
                                                                    $sales_data[$i]['item_price']       = $key->sales_debit_note_item_unit_price;
                                                                    $sales_data[$i]['item_key_value']   = $i;
                                                                    $sales_data[$i]['item_sub_total']   = $key->sales_debit_note_item_sub_total;
                                                                    $sales_data[$i]['item_description'] = $key->sales_debit_note_item_description;
                                                                    $sales_data[$i]['item_quantity']    = $key->sales_debit_note_item_quantity;
                                                                    $sales_data[$i]['item_discount_amount'] = $key->sales_debit_note_item_discount_amount;
                                                                    $sales_data[$i]['item_discount_value']  = $item_discount_value;
                                                                    $sales_data[$i]['item_discount']        = $key->sales_debit_note_item_discount_id;
                                                                    $sales_data[$i]['item_igst']            = $key->sales_debit_note_item_igst_percentage;
                                                                    $sales_data[$i]['item_igst_amount']     = $key->sales_debit_note_item_igst_amount;
                                                                    $sales_data[$i]['item_cgst']            = $key->sales_debit_note_item_cgst_percentage;
                                                                    $sales_data[$i]['item_cgst_amount']     = $key->sales_debit_note_item_cgst_amount;
                                                                    $sales_data[$i]['item_sgst']            = $key->sales_debit_note_item_sgst_percentage;
                                                                    $sales_data[$i]['item_sgst_amount']     = $key->sales_debit_note_item_sgst_amount;
                                                                    $sales_data[$i]['item_tax_percentage']  = $key->sales_debit_note_item_tax_percentage;
                                                                    $sales_data[$i]['item_tax_amount']      = $key->sales_debit_note_item_tax_amount;
                                                                    $sales_data[$i]['item_taxable_value']   = $key->sales_debit_note_item_taxable_value;
                                                                    $sales_data[$i]['item_grand_total']     = $key->sales_debit_note_item_grand_total;
                                                                    //array_push($product_data,$product);
                                                                    $i++;
                                                                    }
                                                                    $sales = htmlspecialchars(json_encode($sales_data));
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                            <!-- Hidden Field -->
                                                            <input type="hidden" class="form-control" id="section_area" name="section_area" value="edit_sales_debit_note" readonly>
                                                            <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php echo $branch[0]->branch_country_id; ?>">
                                                            <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php echo $branch[0]->branch_state_id; ?>">
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
                                                            <input type="hidden" class="form-control" id="customer_module_id" name="customer_module_id" value="<?php
                                                            if (isset($other_modules_present['customer_module_id']))
                                                            {
                                                            echo $other_modules_present['customer_module_id'];
                                                            }
                                                            ?>" readonly>
                                                            <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo date("Y-m-d"); ?>" readonly>
                                                            <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" readonly>
                                                            <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>
                                                            <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $data[0]->sales_debit_note_invoice_number ?>" readonly>
                                                            <input type="hidden" name="total_sub_total" id="total_sub_total" value="<?= $data[0]->sales_debit_note_sub_total; ?>">
                                                            <input type="hidden" name="total_taxable_amount" id="total_taxable_amount" value="<?= $data[0]->sales_debit_note_taxable_value; ?>">
                                                            <input type="hidden" name="total_discount_amount" id="total_discount_amount" value="<?= $data[0]->sales_debit_note_discount_value; ?>">
                                                            <input type="hidden" name="total_tax_amount" id="total_tax_amount" value="<?= $data[0]->sales_debit_note_tax_amount; ?>">
                                                            <input type="hidden" name="total_igst_amount" id="total_igst_amount" value="<?= $data[0]->sales_debit_note_igst_amount; ?>">
                                                            <input type="hidden" name="total_cgst_amount" id="total_cgst_amount" value="<?= $data[0]->sales_debit_note_cgst_amount; ?>">
                                                            <input type="hidden" name="total_sgst_amount" id="total_sgst_amount" value="<?= $data[0]->sales_debit_note_sgst_amount; ?>">
                                                            <input type="hidden" name="total_grand_total" id="total_grand_total" value="<?= $data[0]->sales_debit_note_grand_total; ?>">
                                                            <input type="hidden" name="table_data" id="table_data" value="<?php echo $sales; ?>">
                                                            <input type="hidden" name="total_other_amount" id="total_other_amount" value="<?= $data[0]->total_other_amount; ?>">
                                                            <?php
                                                            $charges_sub_module = 0;
                                                            foreach ($access_sub_modules as $key => $value)
                                                            {
                                                            if (isset($charges_sub_module_id))
                                                            {
                                                            if ($charges_sub_module_id == $value->sub_module_id)
                                                            {
                                                            $this->load->view('sub_modules/charges_sub_module');
                                                            $charges_sub_module = 1;
                                                            }
                                                            }
                                                            }
                                                            if ($charges_sub_module == 1)
                                                            {
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
                                                                    <td align="right">Total Value (+)</td>
                                                                    <td align='right'><span id="totalSubTotal"><?php echo $data[0]->sales_debit_note_sub_total; ?></span></td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="right">Total Discount (-)</td>
                                                                    <td align='right'><span id="totalDiscountAmount"><?php echo $data[0]->sales_debit_note_discount_value; ?></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="right">Total Tax (+)</td>
                                                                <td align='right'><span id="totalTaxAmount"><?php echo $data[0]->sales_debit_note_tax_amount; ?></span>
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
                                                            <td align="right">Grand Total (=)</td>
                                                            <td align='right'><span id="totalGrandTotal"><?php echo $data[0]->sales_debit_note_grand_total; ?></span></td>
                                                        </tr>
                                                    </table>
                                                </div> </div>
                                                <div class="col-sm-12">
                                                    <div class="box-footer">
                                                        <button type="submit" id="sales_submit" name="sales_submit" class="btn btn-info">Update</button>
                                                        <span class="btn btn-default" id="sale_cancel" onclick="cancel('sales_debit_note')">Cancel</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <?php
                                            $notes_sub_module = 0;
                                            foreach ($access_sub_modules as $key => $value)
                                            {
                                            if (isset($notes_sub_module_id))
                                            {
                                            if ($notes_sub_module_id == $value->sub_module_id)
                                            {
                                            $notes_sub_module = 1;
                                            }
                                            }
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
                            <!-- /.box -->
                        </div>
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
        <?php if ($charges_sub_module == 1)
        {
        ?>
        <script src="<?php echo base_url('assets/js/sub_modules/') ?>charges_sub_module.js"></script>
        <?php } ?>
        <script type="text/javascript">
        var sales_data = new Array();
        var sales_data = <?php echo json_encode($sales_data); ?>;
        var branch_state_list = <?php echo json_encode($state); ?>;
        var item_gst =<?php echo json_encode($item_gst); ?>;
        var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
        var common_settings_tax_split = "<?= $access_common_settings[0]->tax_split_equaly ?>";
        </script>
        <script src="<?php echo base_url('assets/js/sales_debit_note/') ?>sales_debit_note.js"></script>