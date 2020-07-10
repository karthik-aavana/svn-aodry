<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('sales'); ?>">Outlet</a></li>
            <li class="active">Add Outlet</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Outlet</h3>
                        <a class="btn btn-sm btn-default pull-right back_button" href1="<?php echo base_url('outlet'); ?>">Back </a>
                    </div>
                    <div class="box-body">
                        <form name="form" id="form" method="post" action="<?php echo base_url('outlet/add_outlet'); ?>">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="date">Invoice Date<span class="validation-color">*</span></label>
                                        <?php
                                            $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));
                                            if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1])) {
                                                $date = '01-04-'.$financial_year[0];
                                            } else {
                                                $date = date('d-m-Y');
                                            }
                                            ?>
                                        <!-- <input type="text" class="form-control" id="invoice_date" name="invoice_date" value="<?php echo $date; ?>"> -->
                                        <div class="input-group date">
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
                                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?= $invoice_number ?>" <?= ($access_settings[0]->invoice_readonly == 'yes' ? "readonly" : ''); ?>>
                                        <span class="validation-color" id="err_invoice_number"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="to_branch_id">Branch <span class="validation-color">*</span></label>
                                        <select class="form-control select2"  id="to_branch_id" name="to_branch_id">
                                            <option value="">Select branch</option>
                                            <?php
                                            foreach ($branches as $row) {
                                                if($row->branch_id != $this->session->userdata('SESS_BRANCH_ID'))
                                                echo "<option value='$row->branch_id' state_id='$row->state_id'>$row->branch_name</option>";
                                            }
                                            ?>
                                        </select>
                                        <span class="validation-color" id="err_to_branch_id"><?php echo form_error('branch'); ?></span>
                                    </div>
                                </div>
                                <!-- <div class="col-sm-3 default_hide">
                                    <div class="form-group">
                                        <label for="nature_of_supply">Nature of Supply <span class="validation-color">*</span></label>
                                        <select class="form-control select2"  id="nature_of_supply" name="nature_of_supply">
                                            <option value="">Select</option>
                                            <option value="product">Product</option>
                                            <option value="service">Service</option>
                                            <option value="both" selected="selected">Both</option>
                                        </select>                      
                                        <span class="validation-color" id="err_nature_supply"></span>
                                    </div>
                                </div> -->
                            </div>
                            <div class="row default_hide">
                                <div class="col-sm-3" style='display: none;'>
                                    <div class="form-group">
                                        <label for="type_of_supply">Billing Country <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="billing_country" name="billing_country">
                                            <option value="0">Select</option>
                                            <?php foreach ($country as $key) { ?>
                                                <option value='<?php echo $key->country_id; ?>' <?= ($key->country_id == $branch[0]->branch_country_id ? "selected" : ''); ?>><?php echo $key->country_name; ?>
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
                                    <div class="form-group">
                                        <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="type_of_supply" name="type_of_supply" >
                                            <option value="regular" <?php echo "selected"; ?>>Regular</option>
                                        </select>
                                        <span class="validation-color" id="err_type_supply"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3 gst_payable_div" id="reverse_charge">
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
                                <div class="col-sm-3" style="display: none">
                                    <div class="form-group">
                                        <label for="currency_id">Billing Currency <span class="validation-color">*</span></label>
                                        <?php $this->load->view('currency_dropdown');?>
                                        <span class="validation-color" id="err_currency_id"></span>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="row default_hide">
                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="invoice_number">Order Number</label>
                                        <input type="text" class="form-control" id="order_number" name="order_number" value="" >
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="due_days">Credit Period</label>
                                    <input class="form-control" type="number" name="due_days" id="due_days" min = '0' max = '365' />
                                    <span class="validation-color" id="err_due_days"><?php echo form_error('due_days'); ?></span>
                                </div>
                            </div> -->

                            <div class="default_hide">
                                <?php
                                if (in_array($transporter_sub_module_id, $access_sub_modules)) {
                                    $this->load->view('sub_modules/transporter_sub_module');
                                }
                                if (in_array($shipping_sub_module_id, $access_sub_modules)) {
                                    $this->load->view('sub_modules/shipping_sub_module');
                                }
                                ?>
                            </div>                            
                            <div class="row default_hide mt-15">
                                <div class="col-sm-12">
                                    <span class="validation-color" id="err_product"></span>
                                </div>
                                <?php
                                $product_modal = 0;
                                $service_modal = 0;
                                $product_inventory_modal = 0;
                                $item_modal = 0;
                                if ($access_settings[0]->item_access == 'product') {  ?>
                                    <div class="col-sm-12 search_outlet_code">
                                        <input id="input_outlet_code" class="form-control" type="text" name="input_outlet_code" placeholder="Enter Product Code/Name" >
                                    </div>
                                <?php } ?>
                                <div class="col-sm-12">
                                    <span class="validation-color" id="err_outlet_code"></span>
                                </div>
                            </div>
                            <div class="row default_hide">
                                <div class="col-sm-12">
                                    <div class="box-header">
                                        <h3 class="box-title ml-0">Inventory Items</h3>
                                        <table class="table table-striped table-bordered table-condensed table-hover sales_table table-responsive" id="sales_data">
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
                                                    <th class="span2" width="10%">Rate</th>
                                                    <?php
                                                    if ($access_settings[0]->discount_visible == 'yes') {
                                                        ?>
                                                        <th class="span2" width="9%">Discount
                                                            <?php
                                                            if (in_array($discount_module_id, $active_add)) {
                                                                ?><!-- <a href="" data-toggle="modal" data-target="#discount_modal"><strong>+</strong></a> -->
                                                            <?php } ?>
                                                        </th>
                                                    <?php } ?>
                                                    <?php
                                                    if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') {
                                                        if ($access_settings[0]->discount_visible == 'yes') {
                                                            ?>
                                                            <th class="span2" width="10%">Taxable Value</th>
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
                                            </tbody>
                                        </table>
                                        <!-- Hidden Field -->
                                        <input type="hidden" name="is_utgst" id="is_utgst" value="0">
                                        <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php echo $branch[0]->branch_country_id; ?>">
                                        <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php echo $branch[0]->branch_state_id; ?>">
                                        <input type="hidden" class="form-control" id="product_module_id" name="product_module_id" value="<?= $product_module_id ?>" >
                                        <input type="hidden" class="form-control" id="service_module_id" name="service_module_id" value="<?= $service_module_id ?>" >
                                        <input type="hidden" class="form-control" id="customer_module_id" name="customer_module_id" value="<?= $customer_module_id ?>" >
                                        <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo $date; ?>" >
                                        <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $outlet_module_id ?>" >
                                        <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" >
                                        <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $invoice_number ?>" >
                                        <input type="hidden" class="form-control" id="section_area" name="section_area" value="add_sales" >
                                        <input type="hidden" name="total_sub_total" id="total_sub_total">
                                        <input type="hidden" name="total_taxable_amount" id="total_taxable_amount">
                                        <input type="hidden" name="total_other_taxable_amount" id="total_other_taxable_amount">
                                        <input type="hidden" name="total_igst_amount" id="total_igst_amount">
                                        <input type="hidden" name="total_cgst_amount" id="total_cgst_amount">
                                        <input type="hidden" name="total_sgst_amount" id="total_sgst_amount">
                                        <input type="hidden" name="total_discount_amount" id="total_discount_amount">
                                        <input type="hidden" name="total_tax_amount" id="total_tax_amount">
                                        <input type="hidden" name="total_tax_cess_amount" id="total_tax_cess_amount">
                                        <input type="hidden" name="total_tds_amount" id="total_tds_amount">
                                        <input type="hidden" name="total_tcs_amount" id="total_tcs_amount">
                                        <input type="hidden" name="round_off_plus" id="round_off_plus">
                                        <input type="hidden" name="round_off_minus" id="round_off_minus">
                                        <input type="hidden" name="total_grand_total" id="total_grand_total">
                                        <input type="hidden" name="without_reound_off_grand_total" id="without_reound_off_grand_total">
                                        <!-- <input type="hidden" name="table_data" id="table_data"> -->
                                        <input type="hidden" name="total_other_amount" id="total_other_amount">
                                        <?php
                                        $charges_sub_module = 0;
                                        if (in_array($charges_sub_module_id, $access_sub_modules)) {
                                            $this->load->view('sub_modules/charges_sub_module');
                                            $charges_sub_module = 1;
                                        }
                                        if ($charges_sub_module == 1) { ?>
                                            <input type="hidden" name="total_freight_charge" id="total_freight_charge">
                                            <input type="hidden" name="total_insurance_charge" id="total_insurance_charge">
                                            <input type="hidden" name="total_packing_charge" id="total_packing_charge">
                                            <input type="hidden" name="total_incidental_charge" id="total_incidental_charge">
                                            <input type="hidden" name="total_other_inclusive_charge" id="total_other_inclusive_charge">
                                            <input type="hidden" name="total_other_exclusive_charge" id="total_other_exclusive_charge">
                                        <?php } ?>
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
                                        <button type="submit" id="outlet_submit" name="submit" value="add" class="btn btn-info">Add</button>
                                        <?php if (in_array($receipt_voucher_module_id, $active_add)) { ?>
                                            <button type="submit" id="sales_pay_now" name="submit" value="pay_now" class="btn btn-success"> Receive Now </button>
                                        <?php } ?>
                                        <span class="btn btn-default" id="sale_cancel" onclick="cancel('sales')">Cancel</span>
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
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('tax/tax_modal_tds');
$this->load->view('sub_modules/shipping_address_modal');
?>
<script type="text/javascript">
var sales_data = new Array();
var branch_state_list =<?php echo json_encode($state); ?>;
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
var settings_item_editable = "<?= $access_settings[0]->item_editable ?>";</script>
<script src="<?php echo base_url('assets/js/outlet/'); ?>outlet.js"></script>
<script src="<?php echo base_url('assets/js/outlet/'); ?>outlet_basic_common.js"></script>
<script type="text/javascript">
    $('#err_outlet_code').text('Please select the customer to do sales.');
    $('.search_outlet_code').hide();
    $('#input_outlet_code').prop('disabled', true);
</script>