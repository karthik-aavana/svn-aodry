<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// $p = array('admin', 'purchaser', 'manager');
// if (!(in_array($this->session->userdata('type'), $p))) {
//     redirect('auth');
// }
$this->load->view('layout/header');
?>
<div class="content-wrapper">    
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('purchase_return'); ?>">Purchase Return</a></li>
            <li class="active">Add Purchase Return</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">            
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Purchase Return</h3>
                        <a class="btn btn-default pull-right back_button" id="cancel" onclick1="cancel('purchase_return')">Back</a>
                    </div>                   
                    <div class="box-body">                       
                        <form role="form" id="form" method="post" action="<?php echo base_url('purchase_return/add_purchase_return'); ?>">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="date">Purchase Return Date<span class="validation-color">*</span></label>
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
                                        <span class="validation-color" id="err_date"><?php echo form_error('date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="reference_no">Reference Number<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo $invoice_number; ?>" <?php
                                        if ($access_settings[0]->invoice_readonly == 'yes') {
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
                                            <option value="">Select</option>
                                            <?php
                                            foreach ($supplier as $key) {
                                                ?>
                                                <option value='<?php echo $key->supplier_id . '-' . $key->supplier_country_id . '-' . $key->supplier_state_id; ?>'>
                                                    <?php if($key->supplier_mobile != ''){
                                                        echo $key->supplier_name.'('.$key->supplier_mobile.')';
                                                    }else{
                                                        echo $key->supplier_name;
                                                    }?>
                                                </option>
                                                <?php
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
                                <div class="col-sm-3" style="display: none;">
                                    <div class="form-group" >
                                        <label for="type_of_supply">Billing Country <span class="validation-color">*</span></label>

                                        <select class="form-control select2" id="billing_country" name="billing_country">
                                            <option value="">Select</option>
                                            <?php
                                            foreach ($country as $key => $value) {
                                                ?>
                                                <option value="<?= $value->country_id ?>"><?= $value->country_name ?></option>
                                                <?php
                                            }
                                            ?>

                                        </select>
                                        <span class="validation-color" id="err_billing_country"><?php echo form_error('type_of_supply'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3 disable_div">
                                    <div class="form-group">
                                        <label for="type_of_supply">Place of Supply <span class="validation-color">*</span></label>

                                        <select class="form-control select2" id="billing_state" name="billing_state" style="width: 100%;">
                                            <option value="">Select</option>
                                            <?php
                                            foreach ($state as $key) {
                                                ?>
                                                <option value='<?php echo $key->state_id ?>'>
                                                    <?php echo $key->state_name; ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="validation-color" id="err_billing_state"><?php echo form_error('type_of_supply'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3" style="display: none;">
                                    <div class="form-group">
                                        <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="type_of_supply" name="type_of_supply">
                                            <option value="regular">Regular</option>
                                        </select>
                                        <span class="validation-color" id="err_type_supply"><?php echo form_error('type_of_supply'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3" style="display: none;">
                                    <div class="form-group">
                                        <label for="gst_payable">GST Payable on Reverse Charge <span class="validation-color">*</span></label>
                                        <label class="radio-inline">
                                            <input class="minimal" type="radio" name="gstPayable" value="yes" /> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input class="minimal" type="radio" name="gstPayable" value="no" checked="checked" /> No
                                        </label>
                                        <br/>
                                        <span class="validation-color" id="err_gst_payable"><?php echo form_error('gst_payable'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3" style="display: none;">
                                    <div class="form-group">
                                        <label for="currency_id">Billing Currency <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="currency_id" name="currency_id">
                                            <?php
                                            foreach ($currency as $key => $value) {
                                                if ($value->currency_id == $this->session->userdata('SESS_DEFAULT_CURRENCY')) {
                                                    echo "<option value='" . $value->currency_id . "' selected>" . $value->currency_name.' - ' . $value->country_shortname."</option>";
                                                } /*else {
                                                    echo "<option value='" . $value->currency_id . "'>" . $value->currency_name .' - ' . $value->country_shortname. "</option>";
                                                }*/
                                            }
                                            ?>
                                        </select>
                                        <span class="validation-color" id="err_currency_id"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!--<div class="col-sm-12">
                                <?php
                                foreach ($access_sub_modules as $key => $value) {
                                    if (isset($transporter_sub_module_id)) {
                                        if ($transporter_sub_module_id == $value->sub_module_id) {
                                            $this->load->view('sub_modules/transporter_sub_module');
                                        }
                                    }
                                }
                                foreach ($access_sub_modules as $key => $value) {
                                    if (isset($shipping_sub_module_id)) {
                                        if ($shipping_sub_module_id == $value->sub_module_id) {
                                            $this->load->view('sub_modules/shipping_sub_module');
                                        }
                                    }
                                }
                                ?>
                                </div> -->
                                <div class="col-sm-12" style="display: none;"> 
                                    <?php
                                    $this->load->view('sub_modules/transporter_sub_module');
                                    $this->load->view('sub_modules/shipping_sub_module');
                                    ?>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('purchase_inventory_items'); ?></label>
                                        <p><span class="validation-color" id="err_product"></span></p>
                                        <table class="table items table-striped table-bordered table-condensed table-hover product_table table-responsive" name="product_data" id="product_data">
                                            <thead>
                                                <tr>
                                                    <th width="2%"><!--<img src="<?php echo base_url(); ?>assets/images/bin1.png" />-->&nbsp;</th>
                                                    <th class="span2">Items</th>
                                                    <th class="span2">Description</th>
                                                    <th class="span2" width="6%">Quantity </th>
                                                    <th class="span2" width="12%">Rate</th>                                                       
                                                    <th class="span2" width="12%">Taxable Value</th>                                                           
                                                    <th class="span2" width="8%">GST(%)</th>
                                                    <th class="span2" width="8%">CESS(%)</th>
                                                    <th class="span2" width="12%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="purchase_table_body">
                                            </tbody>
                                        </table>
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
                                        <input type="hidden" class="form-control" id="supplier_module_id" name="supplier_module_id" value="<?php
                                        if (isset($other_modules_present['supplier_module_id'])) {
                                            echo $other_modules_present['supplier_module_id'];
                                        }
                                        ?>" readonly>
                                        <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo $date; ?>" readonly>
                                        <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" readonly>
                                        <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>
                                        <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $invoice_number ?>" readonly>
                                        <input type="hidden" class="form-control" id="section_area" name="section_area" value="add_purchase_return" readonly>
                                        <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php echo $branch[0]->branch_country_id; ?>">
                                        <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php echo $branch[0]->branch_state_id; ?>">
                                        <input type="hidden" name="total_sub_total" id="total_sub_total">
                                        <input type="hidden" name="total_taxable_amount" id="total_taxable_amount">
                                        <input type="hidden" name="total_discount_amount" id="total_discount_amount">
                                        <input type="hidden" name="total_tax_amount" id="total_tax_amount">
                                        <input type="hidden" name="total_igst_amount" id="total_igst_amount">
                                        <input type="hidden" name="total_cgst_amount" id="total_cgst_amount">
                                        <input type="hidden" name="total_sgst_amount" id="total_sgst_amount">
                                        <input type="hidden" name="total_tax_cess_amount" id="total_tax_cess_amount">
                                        <input type="hidden" name="total_grand_total" id="total_grand_total">
                                        <input type="hidden" name="table_data" id="table_data">
                                        <input type="hidden" name="total_other_amount" id="total_other_amount">
                                        <input type="hidden" name="grand_total_check" id="grand_total_check" value="1">
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
                                            <tr class='totalCessAmount_tr'>
                                                <td align="right"><?php echo 'CESS'; ?> (+)</td>
                                                <td align='right'>
                                                    <span id="totalTaxCessAmount">0.00</span>
                                                </td>
                                            </tr>
                                            <?php
                                            if ($charges_sub_module == 1) {
                                                ?>
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
                                            <tr>
                                                <td align="right"><?php echo 'Grand Total'; ?> (=)</td>
                                                <td align='right'><span id="totalGrandTotal">0.00</span></td>
                                            </tr>                                           
                                        </table>                                       
                                    </div>
                                </div>
                            </div>                                
                            <div class="box-footer">
                                <button type="submit" id="purchase_submit" class="btn btn-info">Add</button>
                                <span class="btn btn-default" id="cancel" onclick="cancel('purchase_return')">Cancel</span>
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
<?php
if ($charges_sub_module == 1) {
    ?>
    <script src="<?php echo base_url('assets/js/sub_modules/') ?>charges_sub_module.js"></script>
    <?php
}
?>
<?php
if (isset($items)) {
    if (count($items) > 0) {
        $count_data = count($items);
    } else {
        $count_data = 0;
    }
} else {
    $count_data = 0;
}
?>
<script type="text/javascript">var count_data = "<?php echo $count_data; ?>";</script>
<script type="text/javascript">
    var purchase_data = new Array();
    var branch_state_list = <?php echo json_encode($state); ?>;
    var item_gst = new Array();
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