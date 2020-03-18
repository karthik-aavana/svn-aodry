<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$type = user_limit();
$scount = supplier_count();
$limit = $type['coun'] + $scount['scount'];
/* echo "<pre>";
  print_r($data);
  exit(); */
$this->load->view('layout/header');
$financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));
$date = date('Y-m-d');
if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1]))
    $date = $financial_year[0] . '-04-01';
?>
<style>
    #total_sum tr td:nth-last-child(2) {
        width: 85%;
        text-align: right;    
    }
    #total_sum tr td:last-child {
        text-align: right;
    }
</style>
<div class="content-wrapper" id="boe_voucher">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li>
                <a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li>
                <a href="<?php echo base_url('boe'); ?>">BOE Voucher</a>
            </li>
            <li class="active">
                Add BOE
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add BOE</h3>
                        <a class="btn btn-default pull-right back_button" id="sale_cancel" onclick1="cancel('boe')">Back</a>
                    </div>
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('boe/add_boe'); ?>">
                            <div class="row">
                                <div class="col-sm-3">
                                    <?php
                                    $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));
                                    if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1])) {
                                        $date = '01-04-' . $financial_year[0];
                                    } else {
                                        $date = date('d-m-Y');
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label for="date">BOE Date<span class="validation-color">*</span></label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" id="boe_date" name="boe_date" value="<?= $date; ?>">
                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                        </div>
                                        <span class="validation-color" id="err_boe_date"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="">BOE Reference Number<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="reference_number" name="reference_number" value="<?= $invoice_number; ?>" readonly> 
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="">BOE Voucher Number<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="boe_number" name="boe_number" autocomplete="off">
                                        <span class="validation-color" id="err_boe_number"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="">Purchase Invoice Number<span class="validation-color">*</span></label>
                                        <select multiple="" class="form-control select2 lect2-hidden-accessible" id="purchase_invoice" name="purchase_invoice[]" tabindex="-1" aria-hidden="true">
                                            <?php
                                            if (!empty($purchase_invoice)) {
                                                foreach ($purchase_invoice as $key => $value) {
                                                    ?>
                                                    <option value="<?= $value->purchase_id; ?>"><?= $value->purchase_invoice_number; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="validation-color" id="err_purchase_invoice"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="cin">CIN</label>
                                        <input type="text" class="form-control" id="cin" name="cin">
                                        <span class="validation-color" id="err_cin"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="cin_date">CIN Date</label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" id="cin_date" name="cin_date">
                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                        </div>
                                        <span class="validation-color" id="err_cin_date"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="Bank">Bank<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="bank_id" name="bank_id" tabindex="-1" aria-hidden="true">
                                            <option value="">Select</option>
                                            <?php
                                            if (!empty($bank_account)) {
                                                foreach ($bank_account as $key => $value) {
                                                    ?>
                                                    <option value="<?= $value->bank_account_id; ?>"><?= $value->ledger_title; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <input type="hidden" class="form-control" id="bank_name" name="bank_name">
                                        <span class="validation-color" id="err_bank_name"></span>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-sm-12">
                                    <span class="validation-color" id="err_product"></span>
                                </div>
                            <?php
                            $product_modal = 0;
                            $service_modal = 0;
                            $product_inventory_modal = 0;
                            $item_modal = 0;
                            if ($access_settings[0]->item_access == 'product') {
                                ?>
                                                <div class="col-sm-12 search_purchase_code">
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
                                                </div>
                            <?php } elseif ($access_settings[0]->item_access == 'service') { ?>
                                                <div class="col-sm-12 search_purchase_code">
                                <?php
                                if (in_array($service_module_id, $active_add)) {
                                    $service_modal = 1;
                                    ?>
                                                                    <a href="" data-toggle="modal" data-target="#service_modal" class="open_service_modal pull-left">+ Add Service</a>
                                <?php } ?>
                                                    <input id="input_purchase_code" class="form-control" type="text" name="input_purchase_code" placeholder="Enter Product Code/Name" >
                                                </div>
                            <?php } else { ?>
                                                <div class="col-sm-12 search_purchase_code">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                <?php
                                if (in_array($service_module_id, $active_add) && in_array($product_module_id, $active_add)) {
                                    $item_modal = 1;
                                    ?>
                                                                            <a href="" data-toggle="modal" data-target="#item_modal" class="pull-left">+</a>
                                <?php } ?>
                                                        </div>
                                                        <input id="input_purchase_code" class="form-control" type="text" name="input_purchase_code" placeholder="Enter Product/Service Code/Name" >
                                                    </div>
                                                </div>
                            <?php } ?>
                                <div class="col-sm-12">
                                    <span class="validation-color" id="err_purchase_code"></span>
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="box-header">
                                        <h3 class="box-title ml-0">Inventory Items</h3>                            
                                        <table class="table table-striped table-bordered table-condensed table-hover purchase_table table-responsive" id="purchase_data">
                                            <thead>
                                                <tr>
                                                    <th width="2%"><img src="<?php echo base_url(); ?>assets/images/bin1.png" /></th>
                                                    <th class="span2">Items</th>
                                                    <th class="span2" width="6%">Quantity</th>
                                                    <th class="span2" width="14%">Rate</th>
                                                    <th class="span2" width="10%">BCD TAX(%) </th>
                                                    <?php if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') { ?>
                                                        <th class="span2" width="10%">GST(%)</th>
                                                        <th class="span2" width="10%">Cess(%)</th>
                                                    <?php } ?>
                                                    <th class="span2" width="12%">Other Duties(%)</th>
                                                    <th class="span2" width="14%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="purchase_table_body"></tbody>
                                        </table>
                                        <!-- Hidden Field -->
                                        <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php echo $branch[0]->branch_country_id; ?>">
                                        <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php echo $branch[0]->branch_state_id; ?>">
                                        <input type="hidden" class="form-control" id="product_module_id" name="product_module_id" value="<?= $product_module_id ?>" >
                                        <input type="hidden" class="form-control" id="service_module_id" name="service_module_id" value="<?= $service_module_id ?>" >
                                        <input type="hidden" class="form-control" id="supplier_module_id" name="supplier_module_id" value="<?= $supplier_module_id ?>" >
                                        <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo $date; ?>" >
                                        <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $boe_module_id ?>" >
                                        <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" >
                                        <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $invoice_number ?>" >
                                        <input type="hidden" class="form-control" id="section_area" name="section_area" value="add_boe" >
                                        <input type="hidden" name="total_sub_total" id="total_sub_total">
                                        <input type="hidden" name="total_taxable_amount" id="total_taxable_amount">
                                        <input type="hidden" name="total_tax_amount" id="total_tax_amount">
                                        <input type="hidden" name="total_bcd_amount" id="total_bcd_amount">
                                        <input type="hidden" name="total_tax_cess_amount" id="total_tax_cess_amount">
                                        <input type="hidden" name="total_other_duties_amount" id="total_other_duties_amount">
                                        <input type="hidden" name="total_igst_amount" id="total_igst_amount">
                                        <input type="hidden" name="total_grand_total" id="total_grand_total">
                                        <input type="hidden" name="table_data" id="table_data">
                                        <input type="hidden" name="without_reound_off_grand_total" id="without_reound_off_grand_total">
                                        <!-- Hidden Field -->
                                        <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive" style="display: none;">
                                            <tr class="totalBCDAmount_tr">
                                                <td align="right"><?php echo 'Total BCD'; ?> (+)</td>
                                                <td align='right'><span id="totalBCD">0.00</span></td>
                                            </tr>

                                            <tr <?= ($access_settings[0]->tax_type == 'no_tax' ? 'style="display: none;"' : ''); ?> class='totalIGSTAmount_tr'>
                                                <td align="right"><?php echo 'IGST'; ?> (+)</td>
                                                <td align='right'><span id="totalIGSTAmount">0.00</span></td>
                                            </tr>
                                            <tr <?= ($access_settings[0]->tax_type == 'no_tax' ? 'style="display: none;"' : ''); ?> class='totalCessAmount_tr'>
                                                <td align="right"><?php echo 'Cess'; ?> (+)</td>
                                                <td align='right'><span id="totalTaxCessAmount">0.00</span></td>
                                            </tr>
                                            <tr  class='totalOtherDutiesAmount_tr'>
                                                <td align="right"><?php echo 'Other Duties'; ?> (+)</td>
                                                <td align='right'><span id="totalOtherDutiesAmount">0.00</span></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><?php echo 'Grand Total'; ?> (=)</td>
                                                <td align='right'><span id="totalGrandTotal">0.00</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" id="boe_submit" name="submit" value="add" class="btn btn-info">Add</button>
                                        <!-- <button type="submit" id="purchase_pay_now" name="submit" value="pay_now" class="btn btn-success"> Pay Now </button> -->
                                        <span class="btn btn-default" id="boe_cancel" onclick="cancel('boe')">Cancel</span>
                                    </div>
                                    <?php $this->load->view('sub_modules/notes_sub_module'); ?>
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
    $this->load->view('supplier/supplier_modal');
    $this->load->view('category/category_modal');
    $this->load->view('subcategory/subcategory_modal');
    $this->load->view('tax/tax_modal');
    $this->load->view('discount/discount_modal');
    if ($item_modal == 1) {
        $this->load->view('layout/item_modal');
    } else {
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
    }
    $this->load->view('sub_modules/shipping_address_modal');
    ?>
    <script type="text/javascript">
        var purchase_data = new Array();
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
    <script src="<?php echo base_url('assets/js/boe/'); ?>boe.js"></script>
    <script src="<?php echo base_url('assets/js/boe/'); ?>boe_common_basic.js"></script>