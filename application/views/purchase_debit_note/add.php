<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    </section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('purchase_debit_note'); ?>">Purchase debit Note</a></li>
            <li class="active">Add Purchase debit Note</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Purchase debit Note</h3>
                        <a class="btn btn-sm btn-default pull-right back_button" href1="<?php echo base_url('purchase_debit_note'); ?>">Back </a>
                    </div>
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('purchase_debit_note/add_purchase_debit_note'); ?>">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="date">Date<span class="validation-color">*</span></label>
                                        <div class="input-group date">
                                            <?php
                                            $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));
                                            if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1])) {
                                                $date = '01-04-'.$financial_year[0];
                                            } else {
                                                $date = date('d-m-Y');
                                            }
                                            ?>
                                            <input type="text" style="background: #fff;" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?php echo $date; ?>">
                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                        </div>
                                        <span class="validation-color" id="err_date"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="invoice_number">debit Note Number<span class="validation-color">*</span></label>
                                        <input type="hidden" name="shipping_address_id" id="shipping_address_id" value="0">
                                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?= $invoice_number ?>" <?= ($access_settings[0]->invoice_readonly == 'yes' ? "readonly" : ''); ?>>
                                        <span class="validation-color" id="err_invoice_number"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="supplier">Supplier <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="supplier" name="supplier" style="width: 100%;">
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
                                        <span class="validation-color" id="err_supplier"><?php echo form_error('supplier'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="purchase_invoice_number">Purchase Reference Number <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="purchase_invoice_number" name="purchase_invoice_number" style="width: 100%;">
                                            <option value=''>Select</option>
                                        </select>
                                        <span class="validation-color" id="err_purchase_invoice_number"><?php echo form_error('purchase_invoice_number'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group disable_div">
                                        <label for="nature_of_supply">Nature of Supply <span class="validation-color">*</span></label>
                                        <select class="form-control select2"  id="nature_of_supply" name="nature_of_supply" style="width: 100%;">
                                            <option value="">Select</option>
                                            <option value="product">Product</option>
                                            <option value="service">Service</option>
                                            <option value="both" selected="selected">Both</option>
                                        </select>         
                                        <!-- <?php if ($access_settings[0]->item_access == 'product') { ?>
                                                                                            <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="product" >
                                                                                            <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Goods" readonly>
                                        <?php } elseif ($access_settings[0]->item_access == 'service') { ?>
                                                                                            <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="service" >
                                                                                            <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Service" readonly>
                                        <?php } else { ?>
                                                                                            <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="both" >
                                                                                            <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Product/Service" readonly>
                                        <?php } ?> -->
                                        <span class="validation-color" id="err_nature_supply"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3" style="display: none;">
                                    <div class="form-group">
                                        <label for="type_of_supply">Billing Country <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="billing_country" name="billing_country">
                                            <option value="">Select</option>
                                            <?php foreach ($country as $key) { ?>
                                                <option value='<?php echo $key->country_id ?>' <?= ($key->country_id == $branch[0]->branch_country_id ? "selected" : ''); ?>><?php echo $key->country_name; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="validation-color" id="err_billing_country"><?php echo form_error('billing_country'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3 disable_div">
                                    <div class="form-group">
                                        <label for="type_of_supply">Place of Supply <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="billing_state" name="billing_state">
                                            <?php foreach ($state as $key) { ?>
                                                <option value='<?php echo $key->state_id; ?>' <?= ($key->state_id == $branch[0]->branch_state_id ? "selected" : ''); ?> utgst='<?= $key->is_utgst; ?>'><?= $key->state_name; ?></option>
                                            <?php } ?>
                                            <option value="0">Out of Country</option>
                                        </select>
                                        <span class="validation-color" id="err_billing_state"><?php echo form_error('billing_state'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3 disable_div">
                                    <div class="form-group">
                                        <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="type_of_supply" name="type_of_supply">
                                            <option value="regular">Regular</option>
                                        </select>
                                        <span class="validation-color" id="err_type_supply"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3 disable_div">
                                    <div class="form-group">
                                        <label for="gst_payable">GST Payable on Reverse Charge<span class="validation-color">*</span></label>
                                        <label class="radio-inline p_l_20">
                                            <input type="radio" name="gst_payable" value="yes" class="" /> Yes
                                        </label>
                                        <label class="radio-inline p_l_20">
                                            <input type="radio" name="gst_payable" value="no" class="" checked="checked"/> No
                                        </label>
                                    </div>
                                    <br/>
                                    <span class="validation-color" id="err_gst_payable"></span>
                                </div>
                            </div>
                            <div class="row">
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
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="debit_note_no">Supplier Credit Note No</label>
                                        <input type="text" class="form-control" id="supplier_debit_note_no" name="supplier_debit_note_no" value="">
                                        <span class="validation-color" id="err_supplier_debit_note_no"><?php echo form_error('debit_note_no'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="debit_note_no">Supplier Credit Note Date</label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" id="invoice_date" name="purchase_supplier_debit_note_date" value="">
                                            <span class="validation-color" id="err_supplier_debit_note_date"><?php echo form_error('debit_note_no'); ?></span>
                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            /* if (in_array($transporter_sub_module_id, $access_sub_modules))
                              {
                              $this->load->view('sub_modules/transporter_sub_module');
                              }

                              if (in_array($shipping_sub_module_id, $access_sub_modules))
                              {
                              $this->load->view('sub_modules/shipping_sub_module');
                              } */
                            ?>
                            <!-- <div class="well">
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
                                                                                    <div class="col-sm-12 search_purchase_debit_note_code">
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
                                                                                      <input id="input_purchase_debit_note_code" class="form-control" type="text" name="input_purchase_debit_note_code" placeholder="Enter Product Code/Name" >
                                                                                    </div>
                            <?php } elseif ($access_settings[0]->item_access == 'service') { ?>
                                                                                    <div class="col-sm-12 search_purchase_debit_note_code">
                                <?php
                                if (in_array($service_module_id, $active_add)) {
                                    $service_modal = 1;
                                    ?>
                                                                                                                                          <a href="" data-toggle="modal" data-target="#service_modal" class="open_service_modal pull-left">+ Add Service</a>
                                <?php } ?>
                                                                                      <input id="input_purchase_debit_note_code" class="form-control" type="text" name="input_purchase_debit_note_code" placeholder="Enter Product Code/Name" >
                                                                                    </div>
                            <?php } else { ?>
                                                                                    <div class="col-sm-12 search_purchase_debit_note_code">
                                                                                      <div class="input-group">
                                                                                        <div class="input-group-addon">
                                <?php
                                if (in_array($service_module_id, $active_add) && in_array($product_module_id, $active_add)) {
                                    $item_modal = 1;
                                    ?>
                                                                                                                                              <a href="" data-toggle="modal" data-target="#item_modal" class="pull-left">+</a>
                                <?php } ?>
                                                                                        </div>
                                                                                        <input id="input_purchase_debit_note_code" class="form-control" type="text" name="input_purchase_debit_note_code" placeholder="Enter Product/Service Code/Name" >
                                                                                      </div>
                                                                                    </div>
                            <?php } ?>
                                <div class="col-sm-12">
                                  <span class="validation-color" id="err_purchase_debit_note_code"></span>
                                </div>
                              </div>
                            </div> -->
                            <div class="row">
                                <div class="col-sm-12">                                    
                                    <div class="box-header">
                                        <h3 class="box-title ml-0">Inventory Items</h3>
                                        <table class="table table-striped table-bordered table-condensed table-hover purchase_debit_note_table table-responsive" id="purchase_debit_note_data">
                                            <thead>
                                                <tr>
                                                    <th width="2%"><img src="<?php echo base_url(); ?>assets/images/bin1.png" /></th>
                                                    <th class="span2" width="10%">Items</th>
                                                    <?php if ($access_settings[0]->description_visible == 'yes') { ?>
                                                        <th class="span2">Description</th>
                                                    <?php } ?>
                                                    <th class="span2" width="6%">Quantity</th>
                                                    <!-- <th class="span2" >Rate</th> -->
                                                    <!-- <?php if ($access_settings[0]->discount_visible == 'yes') { ?>
                                                                                                      <th class="span2" width="6%" >Discount
                                                        <?php if (in_array($discount_module_id, $active_add)) { ?>
                                                                                                                                                              <a href="" data-toggle="modal" data-target="#discount_modal"><strong>+</strong></a>
                                                        <?php } ?>
                                                                                                      </th>
                                                    <?php } ?> -->
                                                    <th class="span2" >Taxable Value</th>
                                                    <?php
                                                    if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') {
                                                        if ($access_settings[0]->discount_visible == 'yes') {
                                                            ?>
                                                            <?php
                                                        }
                                                    }

                                                    if ($access_settings[0]->tds_visible == 'yes') {
                                                        ?>
                                                        <th class="span2" width="10%">TDS/TCS(%)</th>
                                                    <?php } ?>
                                                    <?php if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') { ?>
                                                        <th class="span2" width="10%">GST(%)</th>
                                                        <th class="span2" width="10%">Cess(%)</th>
                                                    <?php } ?>
                                                    <th class="span2" >Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="purchase_debit_note_table_body">
                                            </tbody>
                                        </table>
                                        <!-- Hidden Field -->
                                        <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php echo $branch[0]->branch_country_id; ?>">
                                        <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php echo $branch[0]->branch_state_id; ?>">
                                        <input type="hidden" class="form-control" id="product_module_id" name="product_module_id" value="<?= $product_module_id ?>" >
                                        <input type="hidden" class="form-control" id="service_module_id" name="service_module_id" value="<?= $service_module_id ?>" >
                                        <input type="hidden" class="form-control" id="supplier_module_id" name="supplier_module_id" value="<?= $supplier_module_id ?>" >
                                        <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo $date; ?>" >
                                        <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $purchase_debit_note_module_id ?>" >
                                        <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" >
                                        <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $invoice_number ?>" >
                                        <input type="hidden" class="form-control" id="section_area" name="section_area" value="add_purchase_debit_note" >
                                        <input type="hidden" name="total_sub_total" id="total_sub_total">
                                        <input type="hidden" name="total_taxable_amount" id="total_taxable_amount">
                                        <input type="hidden" name="total_igst_amount" id="total_igst_amount">
                                        <input type="hidden" name="total_cgst_amount" id="total_cgst_amount">
                                        <input type="hidden" name="total_sgst_amount" id="total_sgst_amount">
                                        <input type="hidden" name="total_discount_amount" id="total_discount_amount">
                                        <input type="hidden" name="total_tax_amount" id="total_tax_amount">
                                        <input type="hidden" name="total_tax_cess_amount" id="total_tax_cess_amount">
                                        <input type="hidden" name="total_tds_amount" id="total_tds_amount">
                                        <input type="hidden" name="total_tcs_amount" id="total_tcs_amount">
                                        <input type="hidden" name="without_reound_off_grand_total" id="without_reound_off_grand_total" value="">
                                        <input type="hidden" name="total_grand_total" id="total_grand_total">
                                        <input type="hidden" name="table_data" id="table_data">
                                        <input type="hidden" name="total_other_amount" id="total_other_amount">
                                        <input type="hidden" name="total_other_taxable_amount" id="total_other_taxable_amount" value="">
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
                                        <!-- Hidden Field -->
                                        <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive mt-15">
                                            <tr>
                                                <td align="right"><?php echo 'Subtotal'; ?> (+)</td>
                                                <td align='right'><span id="totalSubTotal">0.00</span></td>
                                            </tr>
                                            <tr <?= ($access_settings[0]->discount_visible == 'no' ? 'style="display: none;"' : '') ?> class='totalDiscountAmount_tr'>
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
                                            <tr <?php if ($access_settings[0]->tcs_visible == 'no') { ?>
                                                    style="display: none;" <?php } ?> class='tcs_amount_tr'>
                                                <td align="right"><?php echo 'TCS Amount'; ?> (+)</td>
                                                <td align='right'>
                                                    <span id="totalTcsAmount">0.00</span>
                                                </td>
                                            </tr>
                                            <!-- <tr <?php if ($access_settings[0]->tax_type == 'no_tax') { ?>
                                                                                                    style="display: none;" <?php } ?>>
                                              <td align="right"><?php echo 'Tax Amount'; ?> (+)</td>
                                              <td align='right'>
                                                <span id="totalTaxAmount">0.00</span>
                                              </td>
                                            </tr> -->
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
                                            <?php } ?>
                                            <?php if ($access_common_settings[0]->round_off_access == 'yes') { ?>
                                                <!-- Round Off -->
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
                                        <button type="submit" id="purchase_debit_note_submit" name="submit" value="add" class="btn btn-info">Add</button>
                                        <span class="btn btn-default" id="purchase_debit_note_cancel" onclick="cancel('purchase_debit_note')">Cancel</span>
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
$this->load->view('discount/discount_modal');

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
                                            var purchase_debit_note_data = new Array();
                                            var branch_state_list = <?php echo json_encode($state); ?>;
                                            var item_gst = new Array();
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
<script src="<?php echo base_url('assets/js/purchase_debit_note/'); ?>purchase_debit_note.js"></script>
<script src="<?php echo base_url('assets/js/purchase_debit_note/'); ?>purchase_debit_note_basic_common.js"></script>

<script type="text/javascript">
    $('#err_purchase_debit_note_code').text('Please select the supplier to do purchase debit note.');
    $('.search_purchase_debit_note_code').hide();
    $('#input_purchase_debit_note_code').prop('disabled', true);
</script>
