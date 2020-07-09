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
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('purchase'); ?>">Purchase</a></li>
            <li class="active">Add Purchase</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Purchase</h3>
                        <a class="btn btn-sm btn-default pull-right back_button" href1="<?php echo base_url('purchase'); ?>">Back </a>
                    </div>
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('purchase/add_purchase'); ?>"  encType="multipart/form-data">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="date">Invoice Date<span class="validation-color">*</span></label>
                                            <!-- <div class="input-group"> -->
                                            <?php
                                            $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));
                                            if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1])) {
                                                $date = '01-04-'.$financial_year[0];
                                            } else {
                                                $date = date('d-m-Y');
                                            }
                                            ?>
                                            <div class="input-group date">
                                                <input type="hidden" name="shipping_address_id" id="shipping_address_id" value="0">
                                                <input type="text" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?php echo $date; ?>">
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
                                            <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?= $invoice_number ?>" <?php
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
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <?php
                                                    if (in_array($supplier_module_id, $active_add)) {
                                                        ?>
                                                        <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#supplier_modal" data-reference_type="purchase" class="open_supplier_modal pull-right">+</a>
                                                    <?php }
                                                    ?>
                                                </div>
                                                <select class="form-control select2" id="supplier" name="supplier">
                                                    <option value="">Select</option>
                                                    <?php
                                                    foreach ($supplier as $row) {
                                                        if($row->supplier_mobile != ''){
                                                            echo "<option value='$row->supplier_id'>$row->supplier_name($row->supplier_mobile)</option>";
                                                        }else{
                                                            echo "<option value='$row->supplier_id'>$row->supplier_name</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <div class="input-group-addon">
                                                <span id="supplier_pop"><i class="fa fa-map-marker icon-blue"></i></span>
                                            </div>
                                            </div>
                                            <span class="validation-color" id="err_supplier"><?php echo form_error('supplier'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="nature_of_supply">Nature of Supply <span class="validation-color">*</span></label>
                                            <select class="form-control select2"  id="nature_of_supply" name="nature_of_supply" style="width: 100%;">
                                                <option value="">Select</option>
                                                <option value="product">Product</option>
                                                <option value="service">Service</option>
                                                <option value="both" selected="selected">Both</option>
                                            </select>                      
                                            <span class="validation-color" id="err_nature_supply"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3" style="display: none;">
                                        <div class="form-group">
                                            <label for="type_of_supply">Billing Country <span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="billing_country" name="billing_country">
                                                <option value="">Select</option>
                                                <?php foreach ($country as $key) { ?>
                                                    <option value='<?= $key->country_id; ?>' <?= ($key->country_id == $branch[0]->branch_country_id ? "selected" : ''); ?>>
                                                        <?php echo $key->country_name; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <span class="validation-color" id="err_billing_country"><?php echo form_error('billing_country'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="type_of_supply">Place of Supply <span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="billing_state" name="billing_state">
                                                <option value="">Select</option>
                                                <?php foreach ($state as $key) { ?>
                                                    <option value='<?php echo $key->state_id; ?>' <?= ($key->state_id == $branch[0]->branch_state_id ? "selected" : ''); ?> utgst='<?= $key->is_utgst; ?>'><?= $key->state_name; ?></option>
                                                <?php } ?>
                                                <option value="0">Out of Country</option>
                                            </select>
                                            <span class="validation-color" id="err_billing_state"><?php echo form_error('billing_state'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group disabled_input">
                                            <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="type_of_supply" name="type_of_supply" readonly>
                                                <option value="inter_state" <?php echo "selected"; ?>>Regular(Inter State)</option>
                                                <option value="instra_state">Regular(Intra State)</option>
                                            </select>
                                            <span class="validation-color" id="err_type_supply"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 gst_payable_div">
                                        <div class="form-group ">
                                            <label for="gst_payable">GST Payable(Reverse Charge) <span class="validation-color">*</span></label>
                                            <br/>
                                            <label class="radio-inline">
                                                <input type="radio" name="gst_payable" value="yes" class="minimal" /> Yes
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="gst_payable" value="no" class="minimal" checked="checked"/> No
                                            </label>
                                            <br/>
                                            <span class="validation-color" id="err_gst_payable"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label for="due_days">Credit Period</label>
                                        <input class="form-control" type="number" name="due_days" id="due_days" min = '0' max = '365' />
                                        <span class="validation-color" id="err_due_days"><?php echo form_error('due_days'); ?></span>
                                    </div>                                  
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="Grn_no">GR Number</label>
                                            <input type="number" class="form-control" id="grn_number" name="grn_number" />
                                            <span class = "validation-color" id = "err_invoice_number"></span>
                                        </div>
                                    </div>                                    
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="date">GRN Date</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control datepicker" id="grn_date" name="grn_date" autocomplete="off">
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                            </div>
                                            <span class="validation-color" id="err_date"></span>
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
                                            <div class="input-group date">
                                                <input type="text" class="form-control datepicker" id="supplier_date" name="supplier_date" value="">
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                            </div>
                                            <span class="validation-color" id="err_supplier_supplier_date"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="supplier_ref">E-way Bill Number</label>
                                            <input type="text" class="form-control" id="e_way_bill" name="e_way_bill" value="">
                                            <span class="validation-color" id="err_e_way_bill"><?php echo form_error('e_way_bill'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="nature_of_supply">Purchase Order Number </label>
                                            <input type="text" class="form-control" id="order_number" name="order_number" value="">
                                            <span class="validation-color" id="err_order_number"><?php echo form_error('order_number'); ?></span>
                                        </div>
                                    </div>
                                    <!-- <div class="col-sm-3">
                                      <div class="form-group">
                                        <label for="currency_id">Billing Currency <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="currency_id" name="currency_id">
                                    <?php
                                    foreach ($currency as $key => $value) {
                                        if ($value->currency_id == $this->session->userdata('SESS_DEFAULT_CURRENCY')) {
                                            echo "<option value='" . $value->currency_id . "' selected>" . $value->currency_name . "</option>";
                                        } else {
                                            echo "<option value='" . $value->currency_id . "'>" . $value->currency_name . "</option>";
                                        }
                                    }
                                    ?>
                                        </select>
                                        <span class="validation-color" id="err_currency_id"></span>
                                      </div>
                                    </div> -->
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="nature_of_supply">Purchase Order Date</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control datepicker" id="purchase_order_date" name="purchase_order_date" value="">
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                            </div>
                                            <span class="validation-color" id="err_purchase_order"><?php echo form_error('purchase_order_date'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="delivery_challan_number">Delivery Challan Number</label>
                                            <input type="text" class="form-control" id="delivery_challan_number" name="delivery_challan_number" value="" >
                                            <span class="validation-color" id="err_delivery_challan_number"><?php echo form_error('delivery_challan_number'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="delivery_note_date">Delivery Challan Date</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control datepicker" id="delivery_date" name="delivery_date" value="">
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                            </div>
                                            <span class="validation-color" id="err_delivery_date"><?php echo form_error('delivery_date'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="">Received Via</label>
                                            <input type="text" class="form-control" id="received_via" name="received_via" value="" >
                                            <span class="validation-color" id=""></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- <?php
                                if (in_array($transporter_sub_module_id, $access_sub_modules)) {
                                    $this->load->view('sub_modules/transporter_sub_module');
                                }
                                if (in_array($shipping_sub_module_id, $access_sub_modules)) {
                                    $this->load->view('sub_modules/shipping_sub_module');
                                }
                                ?> -->                             
                                    
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <span class="validation-color" id="err_product"></span>
                                        </div>
                                        <?php
                                        $product_modal = 0;
                                        $service_modal = 0;
                                        $product_inventory_modal = 0;
                                        $item_modal = 0;
                                        if ($access_settings[0]->item_access == 'product') {
                                            ?>
                                            <!-- <div class="col-sm-12 search_purchase_code">
                                                <?php
                                                if (in_array($product_module_id, $active_add)) {
                                                    if ($inventory_access == "yes") {
                                                        $product_inventory_modal = 1;
                                                        ?>
                                                        <a href="" data-toggle="modal" data-target="#product_inventory_modal" class="open_product_modal pull-left">+ Add Product</a>
                                                        <?php
                                                    } else {
                                                        $product_modal = 1;
                                                        ?>
                                                        <a href="" data-toggle="modal" data-target="#product_modal" class="open_product_modal pull-left">+ Add Product</a>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                <input id="input_purchase_code" class="form-control" type="text" name="input_purchase_code" placeholder="Enter Product Code/Name" >
                                            </div> -->
                                        <?php } elseif ($access_settings[0]->item_access == 'service') { ?>
                                            <!-- <div class="col-sm-12 search_purchase_code">
                                                <?php
                                                if (in_array($service_module_id, $active_add)) {
                                                    $service_modal = 1;
                                                    ?>
                                                    <a href="" data-toggle="modal" data-target="#service_modal" class="open_service_modal pull-left">+ Add Service</a>
                                                <?php } ?>
                                                <input id="input_purchase_code" class="form-control" type="text" name="input_purchase_code" placeholder="Enter Product Code/Name" >
                                            </div> -->
                                        <?php } else { ?>
                                            <!-- <div class="col-sm-12 search_purchase_code">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <?php
                                                        if (in_array($service_module_id, $active_add) || in_array($product_module_id, $active_add)) {
                                                            $item_modal = 1;
                                                            ?>
                                                            <a href="" data-toggle="modal" data-target="#item_modal" class="pull-left">+</a>
                                                        <?php } ?>
                                                    </div>
                                                    <input id="input_purchase_code" class="form-control" type="text" name="input_purchase_code" placeholder="Enter Product/Service Code/Name" >
                                                </div>
                                            </div> -->
                                        <?php } ?>
                                        <div class="col-sm-12">
                                            <span class="validation-color" id="err_purchase_code"></span>
                                        </div>
                                    </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="box-header shipping">
                                            <h3 class="box-title ml-0">Inventory Items</h3>
                                            <table class="table table-striped table-bordered table-condensed table-hover purchase_table table-responsive" id="purchase_data">
                                                <thead>
                                                    <tr>
                                                        <th width="2%"><img src="<?php echo base_url(); ?>assets/images/bin1.png" /></th>
                                                        <th class="span2">Items</th>
                                                        <?php if ($access_settings[0]->description_visible == 'yes') { ?>
                                                            <th class="span2">Description</th>
                                                        <?php } ?>
                                                        <th class="span2" width="6%">Quantity</th>
                                                        <th class="span2"  width="10%">Rate</th>
                                                        <?php if ($access_settings[0]->discount_visible == 'yes') { ?>
                                                            <th class="span2" width="9%" >Discount
                                                                <?php if (in_array($discount_module_id, $active_add)) { ?>
                                                                <?php } ?>
                                                            </th>
                                                            <?php
                                                        }
                                                        if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') {
                                                            if ($access_settings[0]->discount_visible == 'yes') {
                                                                ?>
                                                                <th class="span2" width="11%">Taxable Value</th>
                                                                <?php
                                                            }
                                                        }
                                                        if ($access_settings[0]->tds_visible == 'yes') {
                                                            ?>
                                                            <th class="span2" width="9%">TCS(%)</th>
                                                        <?php } ?>
                                                        <?php if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') { ?>
                                                            <?php if ($access_settings[0]->gst_visible == 'yes') { ?>
                                                            <th class="span2" width="9%">GST(%) </th>
                                                            <th class="span2" width="9%">Cess(%)</th>
                                                        <?php } ?>
                                                        <?php } ?>
                                                        <th class="span2" width="11%">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="purchase_table_body">
                                                    <tr id="0">
                                                        <?php 
                                                        $colspan = 2;
                                                        if ($access_settings[0]->description_visible == 'yes') { $colspan++; } ?>
                                                        <td colspan="<?=$colspan;?>">
                                                            <div class="input-group" style="width: 100%">
                                                            <?php if (in_array($product_module_id, $active_add)) { $item_modal = 1;  ?>
                                                            <div class="input-group-addon">
                                                                <a href="#" data-toggle="modal"  data-target="#item_modal" class="open_product_modal pull-left">+</a></div>
                                                            <?php } ?>
                                                            <input id="input_purchase_code" class="form-control" type="text" name="input_purchase_code" placeholder="Enter Product/Service Code/Name" >
                                                                
                                                            </div>
                                                        </td>
                                                        <td style="text-align:center">
                                                            <input type="text" class="form-control form-fixer text-center float_number" value="1" data-rule="quantity" name="item_quantity">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-fixer text-right float_number" name="item_price" value="0.00"><span id="item_sub_total_lbl_0" class="pull-right">0.00</span>
                                                        </td>
                                                        <?php if ($access_settings[0]->discount_visible == 'yes') { ?>
                                                        <td>
                                                            <div class="form-group" style="margin-bottom:0px !important;">
                                                                <select class="form-control open_discount form-fixer select2 select2-hidden-accessible" name="item_discount" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                                    <option value="">Select</option>
                                                                </select>
                                                            </div>
                                                            <span id="item_discount_lbl_0" class="pull-right" style="color:red;">0.00</span>
                                                        </td>
                                                        <?php
                                                        }
                                                        if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') {
                                                            if ($access_settings[0]->discount_visible == 'yes') {
                                                                ?>
                                                        <td style="text-align:center">
                                                            <span id="item_taxable_value_lbl_0">0.00</span>
                                                        </td>
                                                        <?php
                                                            }
                                                        }
                                                        if ($access_settings[0]->tds_visible == 'yes') {
                                                            ?>
                                                        <td style="text-align:center">
                                                            
                                                            <input type="text" class="form-control open_tds_modal pointer" name="item_tds_percentage" value="0%" readonly="">
                                                            <span id="item_tds_lbl_0" class="pull-right" style="color:red;">0.00</span></td>
                                                        <?php } ?>
                                                        <?php if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') { ?>
                                                            <?php if ($access_settings[0]->gst_visible == 'yes') { ?>
                                                        <td>
                                                            <div class="form-group" style="margin-bottom:0px !important;">
                                                                <select class="form-control open_tax form-fixer select2 select2-hidden-accessible" name="item_tax" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                                    <option value="">Select</option>
                                                                </select>
                                                            </div><span id="item_tax_lbl_0" class="pull-right" style="color:red;">0.00</span>
                                                        </td>
                                                        <?php } ?>
                                                        <?php if ($access_settings[0]->gst_visible == 'yes') { ?>
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
                                            <!-- Hidden Field -->
                                            <input type="hidden" name="is_utgst" id="is_utgst" value="0">
                                            <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php echo $branch[0]->branch_country_id; ?>">
                                            <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php echo $branch[0]->branch_state_id; ?>">
                                            <input type="hidden" class="form-control" id="product_module_id" name="product_module_id" value="<?= $product_module_id ?>" >
                                            <input type="hidden" class="form-control" id="service_module_id" name="service_module_id" value="<?= $service_module_id ?>" >
                                            <input type="hidden" class="form-control" id="supplier_module_id" name="supplier_module_id" value="<?= $supplier_module_id ?>" >
                                            <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo $date; ?>" >
                                            <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $purchase_module_id ?>" >
                                            <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" >
                                            <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $invoice_number ?>" >
                                            <input type="hidden" class="form-control" id="section_area" name="section_area" value="add_purchase" >
                                            <input type="hidden" name="total_sub_total" id="total_sub_total">
                                            <input type="hidden" name="total_taxable_amount" id="total_taxable_amount">
                                            <input type="hidden" name="total_other_taxable_amount" id="total_other_taxable_amount">
                                            <input type="hidden" name="total_tax_amount" id="total_tax_amount">
                                            <input type="hidden" name="total_tax_cess_amount" id="total_tax_cess_amount">
                                            <input type="hidden" name="total_igst_amount" id="total_igst_amount">
                                            <input type="hidden" name="total_cgst_amount" id="total_cgst_amount">
                                            <input type="hidden" name="total_sgst_amount" id="total_sgst_amount">
                                            <input type="hidden" name="total_discount_amount" id="total_discount_amount">
                                            <input type="hidden" name="total_tds_amount" id="total_tds_amount">
                                            <input type="hidden" name="total_tcs_amount" id="total_tcs_amount">
                                            <input type="hidden" name="total_grand_total" id="total_grand_total">
                                            <input type="hidden" name="table_data" id="table_data">
                                            <input type="hidden" name="total_other_amount" id="total_other_amount">
                                            <input type="hidden" name="without_reound_off_grand_total" id="without_reound_off_grand_total">
                                            <?php
                                            $charges_sub_module = 0;
                                            if (in_array($charges_sub_module_id, $access_sub_modules)) {
                                                $this->load->view('sub_modules/charges_sub_module');
                                                $charges_sub_module = 1;
                                            }
                                            if ($charges_sub_module == 1) {
                                                ?>
                                                <input type="hidden" name="total_freight_charge" id="total_freight_charge">
                                                <input type="hidden" name="total_insurance_charge" id="total_insurance_charge">
                                                <input type="hidden" name="total_packing_charge" id="total_packing_charge">
                                                <input type="hidden" name="total_incidental_charge" id="total_incidental_charge">
                                                <input type="hidden" name="total_other_inclusive_charge" id="total_other_inclusive_charge">
                                                <input type="hidden" name="total_other_exclusive_charge" id="total_other_exclusive_charge">
                                            <?php } ?>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="file_upload"><h4>File upload</h4></label>
                                                        <input type="file" class="form-control" id="purchase_file" name="purchase_file" >
                                                        <span class="validation-color" id="error_purchase_file"></span>
                                                </div>
                                            </div>    
                                        </div>
                                            <!-- Hidden Field -->
                                            <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive mt-15" style="display: none;">
                                                <tr>
                                                    <td align="right"><?php echo 'Subtotal'; ?> (+)</td>
                                                    <td align='right'><span id="totalSubTotal">0.00</span></td>
                                                </tr>
                                                <tr <?= ($access_settings[0]->discount_visible == 'no' ? 'style="display: none;"' : ''); ?> class='totalDiscountAmount_tr'>
                                                    <td align="right"><?php echo 'Discount'; ?> (-)</td>
                                                    <td align='right'>
                                                        <span id="totalDiscountAmount">0.00</span>
                                                    </td>
                                                </tr>
                                                <tr <?= ($access_settings[0]->tds_visible == 'no' ? 'style="display: none;"' : ''); ?> class='tds_amount_tr'>
                                                    <td align="right"><?php echo 'TDS Amount'; ?></td>
                                                    <td align='right'>
                                                        <span id="totalTdsAmount">0.00</span>
                                                    </td>
                                                </tr>
                                                <tr <?= ($access_settings[0]->tds_visible == 'no' ? 'style="display: none;"' : ''); ?> class='tcs_amount_tr'>
                                                    <td align="right"><?php echo 'TCS Amount'; ?> (+)</td>
                                                    <td align='right'>
                                                        <span id="totalTcsAmount">0.00</span>
                                                    </td>
                                                </tr>                     
                                                <tr <?= ($access_settings[0]->tax_type == 'no_tax' ? 'style="display: none;"' : ''); ?> class='totalCGSTAmount_tr'>
                                                    <td align="right"><?php echo 'CGST'; ?> (+)</td>
                                                    <td align='right'>
                                                        <span id="totalCGSTAmount">0.00</span>
                                                    </td>
                                                </tr>
                                                <tr <?= ($access_settings[0]->tax_type == 'no_tax' ? 'style="display: none;"' : ''); ?> class='totalSGSTAmount_tr'>
                                                    <td align="right"><?php echo 'SGST'; ?> (+)</td>
                                                    <td align='right'>
                                                        <span id="totalSGSTAmount">0.00</span>
                                                    </td>
                                                </tr>
                                                <tr <?= ($access_settings[0]->tax_type == 'no_tax' ? 'style="display: none;"' : ''); ?> class='totalIGSTAmount_tr'>
                                                    <td align="right"><?php echo 'IGST'; ?> (+)</td>
                                                    <td align='right'>
                                                        <span id="totalIGSTAmount">0.00</span>
                                                    </td>
                                                </tr>
                                                <tr <?= ($access_settings[0]->tax_type == 'no_tax' ? 'style="display: none;"' : ''); ?> class='totalCessAmount_tr'>
                                                    <td align="right"><?php echo 'Cess'; ?> (+)</td>
                                                    <td align='right'>
                                                        <span id="totalTaxCessAmount">0.00</span>
                                                    </td>
                                                </tr>
                                                <?php if ($charges_sub_module == 1) { ?>
                                                    <tr id="freight_charge_tr">
                                                        <td align="right">Freight Charge (+)</td>
                                                        <td align='right'>
                                                            <span id="freight_charge">0.00</span>
                                                        </td>
                                                    </tr>
                                                    <tr id="insurance_charge_tr">
                                                        <td align="right">Insurance Charge (+)</td>
                                                        <td align='right'>
                                                            <span id="insurance_charge">0.00</span>
                                                        </td>
                                                    </tr>
                                                    <tr id="packing_charge_tr">
                                                        <td align="right">Packing & Forwarding Charge (+)</td>
                                                        <td align='right'>
                                                            <span id="packing_charge">0.00</span>
                                                        </td>
                                                    </tr>
                                                    <tr id="incidental_charge_tr">
                                                        <td align="right">Incidental Charge (+)</td>
                                                        <td align='right'>
                                                            <span id="incidental_charge">0.00</span>
                                                        </td>
                                                    </tr>
                                                    <tr id="other_inclusive_charge_tr">
                                                        <td align="right">Other Inclusive Charge (+)</td>
                                                        <td align='right'>
                                                            <span id="other_inclusive_charge">0.00</span>
                                                        </td>
                                                    </tr>
                                                    <tr id="other_exclusive_charge_tr">
                                                        <td align="right">Other Exclusive Charge (-)</td>
                                                        <td align='right'>
                                                            <span id="other_exclusive_charge">0.00</span>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                <?php if ($access_common_settings[0]->round_off_access == 'yes') { ?>
                                                    <tr class="round_off_minus_tr">
                                                        <td align="right">Round Off (-)</td>
                                                        <td align='right'>
                                                            <span id="round_off_minus_charge">0.00</span>
                                                        </td>
                                                    </tr>
                                                    <tr class="round_off_plus_tr">
                                                        <td align="right">Round Off (+)</td>
                                                        <td align='right'>
                                                            <span id="round_off_plus_charge">0.00</span>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                <?php if ($access_common_settings[0]->round_off_access == 'yes') { ?>
                                                    <tr id="round_off_option">
                                                        <td align="right" width="50%">Round Off</td>
                                                        <td align="right" width="50%">
                                                            <label>
                                                                <input class="minimal" type="radio" name="round_off_key" value="yes">yes
                                                            </label>
                                                            <label>
                                                                <input class="minimal" type="radio" name="round_off_key" value="no" checked>No
                                                            </label>
                                                        </td>
                                                    </tr>
                                                    <tr style="display: none;" id="round_off_select">
                                                        <td align="right" width="50%">Select the nearest value</td>
                                                        <td align="right" width="50%">
                                                            <select id="round_off_value" name="round_off_value">
                                                            </select>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                <tr>
                                                    <td align="right"><?php echo 'Grand Total'; ?> (=)</td>
                                                    <td align='right'><span id="totalGrandTotal">0.00</span></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="box-footer">
                                            <button type="submit" id="purchase_submit" name="submit" value="add" class="btn btn-info">Add</button>
                                            <?php if(in_array($payment_voucher_module_id, $active_add)){ ?>
                                                <button type="submit" id="purchase_pay_now" name="submit" value="pay_now" class="btn btn-success"> Pay Now </button>
                                            <?php } ?>
                                            <span class="btn btn-default" id="purchase_cancel" onclick="cancel('purchase')">Cancel</span>
                                        </div>
                                    </div>
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
<?php
$this->load->view('layout/footer');
$this->load->view('supplier/supplier_modal');
$this->load->view('category/category_modal');
$this->load->view('subcategory/subcategory_modal');
//$this->load->view('tax/tax_modal');
/*$this->load->view('discount/discount_modal');*/
$this->load->view('discount/discount_modal_product');
$this->load->view('sales/billing_address');
$this->load->view('tax/tax_modal_tds');
$this->load->view('tax/tax_modal_gst');
$this->load->view('uqc/uom_modal');
$this->load->view('service/hsn_modal');
//$this->load->view('service/tds_modal');
if ($item_modal == 1) {
    $this->load->view('layout/item_modal');
} /*else {
    if ($product_inventory_modal == 1) {
       $this->load->view('product/product_inventory_modal');
    }
    if ($product_modal == 1) {
        $this->load->view('product/product_modal');
    }
    if ($product_modal == 1 || $product_inventory_modal == 1) {
        $this->load->view('product/hsn_modal');
    }
    if ($service_modal == 1) {
        $this->load->view('service/service_modal');
        $this->load->view('service/sac_modal');
    }
}*/
$this->load->view('sub_modules/shipping_address_modal');
?>
<?php
if ($charges_sub_module == 1) {
    ?>
   <script src="<?php echo base_url('assets/js/sub_modules/'); ?>charges_sub_module.js"></script>
<?php }
?>
<script type="text/javascript">
    var purchase_data = new Array();
    var branch_state_list = <?php echo json_encode($state); ?>;
    var discount_ary = <?php echo json_encode($discount); ?>;
    var tax_ary = <?php echo json_encode($tax); ?>;
    var item_gst = new Array();
    var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
    var common_settings_amount_precision = "<?= $access_common_settings[0]->amount_precision ?>";
    var settings_tax_percentage = "<?= $access_common_settings[0]->tax_split_percentage ?>";
    var common_settings_inventory_advanced = "<?= $inventory_access ?>";
    var settings_tax_type = "<?= $access_settings[0]->tax_type ?>";
    var settings_discount_visible = "<?= $access_settings[0]->discount_visible ?>";
    var settings_description_visible = "<?= $access_settings[0]->description_visible ?>";
    var settings_tds_visible = "<?= $access_settings[0]->tds_visible ?>";
    var settings_gst_visible = "<?=$access_settings[0]->gst_visible?>";
    var settings_item_editable = "<?= $access_settings[0]->item_editable ?>";
</script>
<script src="<?php echo base_url('assets/js/purchase/'); ?>purchase.js"></script>
<script src="<?php echo base_url('assets/js/purchase/'); ?>purchase_basic_common.js"></script>
<script type="text/javascript">
    $('#err_purchase_code').text('Please select the supplier to do purchase.');
    $('.search_purchase_code').hide();
    $('#input_purchase_code').prop('disabled', true);
</script>
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