<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');
?>

<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <h5>

        <ol class="breadcrumb">

            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>

            <li><a href="<?php echo base_url('refund_voucher'); ?>">Refund Voucher</a></li>

            <li class="active">Edit Refund Voucher</li>

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

                        <h3 class="box-title">Edit Refund Voucher</h3>

                        <a class="btn btn-default pull-right back_button" id="sale_cancel" onclick1="cancel('refund_voucher')">Back</a>

                    </div>

                    <!-- /.box-header -->

                    <div class="box-body">                       

                            <form role="form" id="form" method="post" action="<?php echo base_url('refund_voucher/edit_refund'); ?>">

                                        <div class="row">

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="date">Voucher Date<span class="validation-color">*</span></label>

                                                    <div class="input-group date">

                                            <input type="text" class="form-control datepicker" id="voucher_date" name="voucher_date" value="<?php echo date('d-m-Y', strtotime($data[0]->voucher_date)) ?>">

                                                        <div class="input-group-addon">

                                                            <i class="fa fa-calendar"></i>

                                                        </div>

                                                    </div>

                                                    <span class="validation-color" id="err_voucher_date"></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="reference_no">Voucher Number<span class="validation-color">*</span></label>

                                                    <input type="text" class="form-control" id="voucher_number" name="voucher_number" value="<?= $data[0]->voucher_number; ?>" readonly>

                                                    <span class="validation-color" id="err_voucher_number"></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="customer">Customer <span class="validation-color">*</span></label>

                                                    <select class="form-control select2" autofocus="on" id="customer" name="customer" style="width: 100%;">

                                                        <!--  <option value="">Select</option> -->

                                                        <?php
                                            foreach ($customer as $row) {

                                                if ($row->customer_id == $data[0]->party_id) {
                                                    if($row->customer_mobile != ''){
                                                        ?>
                                                        <option value="<?= $row->customer_id ?>"><?= $row->customer_name.'('.$row->customer_mobile.')' ?></option>

                                                        <?php
                                                    }else{
                                                        ?>
                                                        <option value="<?= $row->customer_id ?>"><?= $row->customer_name ?></option>
                                                        <?php
                                                        }
                                                    }
                                                        }
                                                        ?>

                                                    </select>

                                                    <span class="validation-color" id="err_customer"></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="customer">Advance Reference Number <span class="validation-color">*</span></label>

                                                    <select class="form-control select2" autofocus="on" id="reference_number" name="reference_number" style="width: 100%;">

                                                        <option value="<?= $data[0]->reference_number; ?>"><?php echo $data[0]->reference_number ?></option>

                                                    </select>

                                                    <span class="validation-color" id="err_reference_number"></span>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="row " >

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="amount">Refund Amount <span class="validation-color">*</span></label>

                                                    <input type="text" class="float_number form-control" id="receipt_amount" name="receipt_amount" value="<?= precise_amount($data[0]->receipt_amount) ?>" readonly>

                                                    <span class="validation-color" id="err_receipt_amount"></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="type_of_supply">Billing Country <span class="validation-color">*</span></label>

                                                    <select class="form-control select2" id="billing_country" name="billing_country">

                                                        <?php
                                            foreach ($country as $key => $value) {
                                                        ?>

                                                        <option value="<?= $value->country_id ?>" <?php if ($value->country_id == $data[0]->billing_country_id) echo "selected='selected'"; ?> ><?= $value->country_name ?></option>

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
                                            if ($data[0]->billing_country_id == $branch[0]->branch_country_id) {
                                                        ?>

                                                        <?php
                                                foreach ($state as $key => $value) {
                                                        ?>

                                                        <option value="<?= $value->state_id ?>" <?php if ($value->state_id == $data[0]->billing_state_id) echo "selected='selected'"; ?>  utgst='<?= $value->is_utgst; ?>'><?= $value->state_name ?></option>

                                                        <?php } ?>

                                                        <?php
                                                        }

                                            else {
                                                        ?>

                                                        <option value="0" utgst='0'>Out of Country</option>

                                                        <?php } ?>

                                                    </select>

                                                    <span class="validation-color" id="err_billing_state"><?php echo form_error('billing_state'); ?></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="currency_id">Billing Currency <span class="validation-color">*</span></label>

                                                    <select class="form-control select2" id="currency_id" name="currency_id">

                                                        <?php
                                            foreach ($currency as $key => $value) {

                                                if ($value->currency_id == $data[0]->currency_id) {

                                                        echo "<option value='" . $value->currency_id . "' selected>" . $value->currency_name  . ' - ' . $value->country_shortname. "</option>";
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

                                        <div id="add_row_field">

                                        </div>

                                        <div class="row">

                                            <div class="col-sm-3 gst_payable_div">

                                                <div class="form-group">

                                                    <label for="gst_payable">GST Payable(Reverse Charge) <span class="validation-color">*</span></label>                                        

                                                    <div class="radio">

                                                    <label class="radio-inline">

                                                        <input type="radio" name="gst_payable" value="yes"  <?php
                                                                if ($data[0]->gst_payable == "yes") {

                                                                    echo 'checked="checked"';
                                                                }
                                                                ?> /> Yes

                                                    </label>

                                                    <label class="radio-inline">

                                                        <input type="radio" name="gst_payable" value="no"  <?php
                                                                if ($data[0]->gst_payable == "no") {

                                                                    echo 'checked="checked"';
                                                                }
                                                                ?> ="checked"/> No

                                                    </label>

                                                </div>

                                                    <br/>

                                                    <span class="validation-color" id="err_gst_payable"></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="paying_by">Payment Mode<span class="validation-color">*</span></label>

                                                    <?php
                                        if (isset($other_modules_present['bank_account_module_id']) && $other_modules_present['bank_account_module_id'] != "") {
                                                    ?>

                                                    <div class="input-group">

                                                        <div class="input-group-addon">

                                                            <a href="" data-toggle="modal" data-target="#bank_account_modal" class="pull-right">+</a>

                                                        <?php } ?></div>

                                                        <!-- <a href="" data-toggle="modal" data-target="#bank_account_modal" class="pull-right">+ Add Account</a> -->

                                                        <select class="form-control select2" id="payment_mode" name="payment_mode">
                                                            <option value="">Select</option>
                                                            <option value="cash" <?php
                                                            if ($data[0]->payment_mode == "cash") {
                                                                echo "selected";
                                                            }
                                                            ?>>Cash</option>
                                                            <option value="other payment mode" <?php
                                                            if ($data[0]->payment_mode == "other payment mode"){

                                                                echo "selected";
                                                                }
                                                            ?>>Other payment mode</option>
                                                            <?php
                                                if (isset($other_modules_present['bank_account_module_id']) && $other_modules_present['bank_account_module_id'] != "") {

                                                    foreach ($bank_account as $key => $value) {
                                                            ?>

                                                            <option value="<?php echo $value->bank_account_id . "/" . $value->ledger_title ?>" <?php if ($value->bank_account_id == $data[0]->payment_mode) echo "selected='selected'"; ?> ><?= $value->ledger_title ?></option>

                                                            <?php
                                                            }
                                                            }
                                                            ?>

                                                        </select></div>

                                                        <span class="validation-color" id="err_payment_mode"></span>

                                                    </div>

                                                </div>

                                                <div id="other_payment" style="display: none;">

                                                    <div class="col-sm-3">

                                                        <div class="form-group">

                                                            <label for="bank_name">Payment Via</label>

                                                            <input type="text" class="form-control" id="payment_via" name="payment_via" value="<?= $data[0]->payment_via ?>">

                                                            <span class="validation-color" id="err_payment_via"></span>

                                                        </div>

                                                    </div>

                                                    <div class="col-sm-3">

                                                        <div class="form-group">

                                                            <label for="cheque_no">Reference Number</label>

                                                            <input type="text" class="form-control" id="ref_number" name="ref_number" value="<?= $data[0]->ref_number ?>">

                                                            <span class="validation-color" id="err_cheque_number"></span>

                                                        </div>

                                                    </div>

                                                </div>

                                                <div id="hide" >

                                                    <div class="col-sm-3" style="display: none;">

                                                        <div class="form-group">

                                                            <label for="bank_name">Bank Name</label>

                                                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?= $data[0]->bank_name ?>">

                                                            <span class="validation-color" id="err_bank_name"></span>

                                                        </div>

                                                    </div>

                                                    <div class="col-sm-3">

                                                        <div class="form-group">

                                                            <label for="cheque_no">Cheque Number</label>

                                                            <input type="text" class="form-control" id="cheque_number" name="cheque_number" value="<?= $data[0]->cheque_number ?>">

                                                            <span class="validation-color" id="err_cheque_number"></span>

                                                        </div>

                                                    </div>

                                                    <div class="col-sm-3">

                                                        <div class="form-group">

                                                             <?php
                                                        //$cheque_date = explode('-', $data[0]->cheque_date);
                                                        if ( $data[0]->cheque_date != '0000-00-00' && $data[0]->cheque_date != '' && $data[0]->cheque_date != '1970-01-01'){
                                                            $cheque_date = date('d-m-Y', strtotime($data[0]->cheque_date));
                                                        }else{
                                                            $cheque_date = "";
                                                        }
                                                        ?>

                                                            <label for="cheque_date">Cheque Date</label>
                                                        <div class="input-group date">
                                                            <input type="text" class="form-control datepicker" id="cheque_date" name="cheque_date" value="<?= $cheque_date ?>">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                           </div>
                                                       </div>

                                                            <span class="validation-color" id="err_cheque_date"></span>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="row">

                                                <div class="col-sm-12">

                                                    <div class="form-group">

                                                        <label for="paying_by">Description</label>

                                                        <textarea type="text" class="form-control" id="description" name="description" ><?=
                                                        str_replace(array(
                                                        "\r\n",
                                                        "\\r\\n",
                                                        "\n",
                                                "\\n"), "&#10;", $data[0]->description);
                                                        ?></textarea>

                                                    </div>

                                                </div>





                                    <div class="col-sm-12">

                                        <div class="form-group">

                                            <label><?php echo $this->lang->line('purchase_inventory_items'); ?></label>

                                            <div class="col-sm-8">
                                                <span class="validation-color" id="err_product"></span>
                                            </div>

                                            <table class="table table-striped table-bordered table-condensed table-hover sales_table" name="sales_data" id="sales_data">

                                                <thead>

                                                    <tr>

                                                        <!-- <th width="2%"><img src="<?php echo base_url(); ?>assets/images/bin1.png" /></th> -->

                                                        <th class="span2">Product/Service Name</th>

                                                        <th class="span2">Product/Service Description</th>

                                                        <th class="span2" width="6%" hidden="true">Quantity</th>

                                                        <th class="span2" >Taxable Amount</th>

                                                        <!--  <th class="span2" >Discount <a href="" data-toggle="modal" data-target="#discount_modal"><strong>+</strong></a></th> -->

                                                        <th class="span2" hidden="true">Taxable Value</th>

                                                        <th class="span2" width="10%">GST(%)</th>

                                                        <th class="span2" >CESS(%)</th>

                                                        <th class="span2" >Total</th>

                                                    </tr>

                                                </thead>

                                                <tbody id="sales_table_body">

                                                    <?php
                                                    $i   = 0;

                                                    $tot = 0;

                                                foreach ($items as $key) {
                                                    ?>

                                                    <tr id="<?= $i ?>">

                                                        <!-- <td>

                                                            <a class='deleteRow'> <img src='<?php echo base_url(); ?>assets/images/bin_close.png' /> </a>

                                                        </td> -->

                                                        <?php
                                                        if ($key->item_type == 'product' || $key->item_type == 'product_inventory') {
                                                        ?>

                                                        <td><?php echo $key->product_name; ?><br>HSN/SAC: <?php echo $key->product_hsn_sac_code; ?></td><?php
                                                        } else if ($key->item_type == 'advance') {
                                                        ?>

                                                        <td>

                                                        <input type='hidden' name='item_code' value=''>

                                                        <?php
                                                        echo $key->product_name;
                                                        ?>

                                                    </td>

                                                    <?php
                                                        } else {
                                                    ?>

                                                        <td><?php echo $key->service_name; ?><br>HSN/SAC: <?php echo $key->service_hsn_sac_code; ?></td>

                                                        <?php } ?>

                                                        <td>

                                                            <input type='hidden' name='item_key_value' value="<?php echo $i ?>">

                                                            <input type='hidden' name='item_id' value="<?php echo $key->item_id ?>">

                                                            <input type='hidden' name='item_type' value="<?php echo $key->item_type ?>">

                                                            <?php
                                                            if ($key->item_type == 'product' || $key->item_type == 'product_inventory') {
                                                            ?>

                                                            <input type='hidden' name='item_code' value='<?php echo $key->product_code ?>'>

                                                            <?php
                                                            } else if ($key->item_type == 'advance') {
                                                            ?>

                                                            <input type='hidden' name='item_code' value='<?php echo $key->product_code ?>'>

                                                            <?php
                                                            } else {
                                                            ?>

                                                            <input type='hidden' name='item_code' value='<?php echo $key->service_code ?>'>

                                                            <?php } ?>

                                                            <input type='text' name='item_description' class='form-control form-fixer' value="<?php echo $key->item_description ?>"></td>

                                                            <td hidden="true"><input type="text" class="float_number form-control form-fixer text-center" value="1" data-rule="quantity" name='item_quantity'>

                                                            </td>

                                                            <td>

                                                            <input type='text' class='float_number form-control form-fixer text-right' name='item_price' value='<?php echo precise_amount($key->item_sub_total, 2) ?>'>

                                                                <span id='item_sub_total_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?php echo precise_amount($key->item_sub_total); ?></span>

                                                                <input type='hidden' class='form-control' style='' value='<?php echo $key->item_sub_total ?>' name='item_sub_total' readonly>

                                                            </td>

                                                            <td align="right" style="display: none;">

                                                                <div class="form-group">

                                                                    <select class="form-control" name="item_discount" style="width: 100%;">

                                                                        <option value="">Select</option>

                                                                        <?php
                                                                        $item_discount_value                             = 0;

                                                                        /* foreach ($discount as $dis) {

                                                                        if ($key->sales_item_discount_id == $dis->discount_id) {

                                                                        $item_discount_value=$dis->discount_value;

                                                                        ?>

                                                                        <option value='<?php echo $dis->discount_id.'-'.$dis->discount_value ?>' selected="selected"><?php echo $dis->discount_value.'%' ; ?></option>

                                                                        <?php }

                                                                        else{

                                                                        ?>

                                                                        <option value='<?php echo $dis->discount_id.'-'.$dis->discount_value ?>'>

                                                                        <?php echo $dis->discount_value.'%' ; ?></option>

                                                                        <?php

                                                                        }} */
                                                                        ?>

                                                                    </select>

                                                                </div>

                                                                <input type="hidden" name="item_discount_amount" value="0">

                                                                <input type="hidden" name="item_discount_value" value="0">

                                                            </td>

                                                            <td align="right" hidden="true">

                                                                <input type='hidden' name='item_taxable_value' value="<?= $key->item_sub_total ?>">

                                                                <span id="item_taxable_value_<?= $i ?>"><?= $key->item_sub_total ?></span>

                                                            </td>

                                                            <td><select class="form-control open_tax select2" name="item_tax" style="width: 100%;" readonly ><option value="">Select</option>

                                                                    <?php
                                                                foreach ($tax as $key3 => $value3) {

                                                                    if ($value3->tax_name == 'GST') {



                                                                    echo "<option value='" . $value3->tax_id . "-" . (float)($value3->tax_value) . "' ".($value3->tax_id == $key->item_tax_id ? 'selected' : '' ).">" . (float)($value3->tax_value) . "%</option>";

                                                                  }
                                                                }
                                                                ?>

                                                            </select><span id='item_tax_lbl_<?php echo $i; ?>' class='pull-right' style='color:red;'><?php echo precise_amount($key->item_tax_amount); ?></span>

                                                                <?php
                                                                $item_tax_percentage                             = $key->item_igst_percentage + $key->item_cgst_percentage + $key->item_sgst_percentage;

                                                                $item_tax_amount                                 = $key->item_igst_amount + $key->item_cgst_amount + $key->item_sgst_amount;
                                                                ?>

                                                                <input type='hidden' name='item_tax_percentage' value="<?= $key->item_tax_percentage ?>">

                                                                <input type='hidden' name='item_tax_amount' value="<?= $key->item_tax_amount ?>">

                                                                <input type="hidden" name="item_igst" class="float_number form-control form-fixer text-center" value="<?= $key->item_igst_percentage ?>">

                                                                <input type="hidden" name="item_igst_amount" class="form-control form-fixer" value="<?php echo $key->item_igst_amount; ?>" >

                                                               <input type="hidden" name="item_tax_id" class="form-control form-fixer" value="<?php echo $key->item_tax_id; ?>" >

                                                                <input type="hidden" name="item_cgst" class="float_number form-control form-fixer text-center" value="<?= $key->item_cgst_percentage ?>" >

                                                                <input type="hidden" name="item_cgst_amount" class="form-control form-fixer" value="<?php echo $key->item_cgst_amount; ?>">

                                                                <input type="hidden" name="item_sgst" class="float_number form-control form-fixer text-center" value="<?= $key->item_sgst_percentage ?>" >

                                                                <input type="hidden" name="item_sgst_amount" class="form-control form-fixer text-center" value="<?php echo $key->item_sgst_amount; ?>">



                                                            </td>

                                                            <td><select class="form-control open_tax select2" name="item_tax_cess" style="width: 100%;" readonly ><option value="">Select</option>

                                                                    <?php
                                                                foreach ($tax as $key3 => $value3) {

                                                                    if ($value3->tax_name == 'CESS') {

                                                                    echo "<option value='" . $value3->tax_id . "-" . (float)($value3->tax_value) . "' ".($value3->tax_id == $key->item_tax_cess_id ? 'selected' : '' ).">" . (float)($value3->tax_value) . "%</option>";

                                                                  }
                                                                }
                                                                ?>

                                                            </select><span id='item_tax_cess_lbl_<?php echo $i; ?>' class='pull-right' style='color:red;'><?php echo precise_amount($key->item_tax_amount); ?></span>

                                                    <input type="hidden" name="item_tax_cess_id" value="<?php echo $key->item_tax_cess_id; ?>">

                                                    <input type="hidden" name="item_tax_cess_percentage" value="<?= $key->item_tax_cess_percentage ?>" >

                                                    <input type="hidden" name="item_tax_cess_amount" value="<?php echo $key->item_tax_cess_amount; ?>">

                                                            </td>

                                                            <td align="right">

                                                                <input type="text" class="form-control form-fixer text-right" name="item_grand_total" value="<?php echo precise_amount($key->item_grand_total); ?>">

                                                            </td>

                                                            <?php
                                                            $temp                                            = array(
                                                            "item_id"   => $key->item_id,
                                                            "item_type" => $key->item_type,
                                                            "item_igst" => $key->item_igst_percentage,
                                                            "item_cgst" => $key->item_cgst_percentage,
                                                            "item_sgst" => $key->item_sgst_percentage
                                                            );

                                                            $item_gst[$key->item_id . '-' . $key->item_type] = $temp;
                                                            ?>

                                                        </tr>

                                                        <?php
                                                        $sales_data[$i]['item_id']                       = $key->item_id;

                                                        $sales_data[$i]['item_type']                     = $key->item_type;

                                                    if ($key->item_type == 'product' || $key->item_type == 'product_inventory') {

                                                        $sales_data[$i]['item_code'] = $key->product_code;
                                                    } elseif ($key->item_type == 'advance') {

                                                            $sales_data[$i]['item_code'] = $key->product_code;
                                                    } else {

                                                        $sales_data[$i]['item_code'] = $key->service_code;
                                                        }

                                                        $sales_data[$i]['item_price']       = $key->item_sub_total;

                                                        $sales_data[$i]['item_key_value']   = $i;

                                                        $sales_data[$i]['item_sub_total']   = $key->item_sub_total;

                                                        $sales_data[$i]['item_description'] = $key->item_description;

                                                        $sales_data[$i]['item_quantity']    = 1;

                                                        $sales_data[$i]['item_discount_amount'] = 0;

                                                        $sales_data[$i]['item_discount_value']  = 0;

                                                        $sales_data[$i]['item_discount']        = 0;

                                                        $sales_data[$i]['item_igst']            = $key->item_igst_percentage;

                                                        $sales_data[$i]['item_igst_amount']     = $key->item_igst_amount;

                                                        $sales_data[$i]['item_cgst']            = $key->item_cgst_percentage;

                                                        $sales_data[$i]['item_cgst_amount']     = $key->item_cgst_amount;

                                                        $sales_data[$i]['item_sgst']            = $key->item_sgst_percentage;

                                                        $sales_data[$i]['item_sgst_amount']     = $key->item_sgst_amount;

                                                        $sales_data[$i]['item_tax_percentage']  = $item_tax_percentage;

                                                        $sales_data[$i]['item_tax_amount']      = $item_tax_amount;

                                                        $sales_data[$i]['item_taxable_value']   = $key->item_sub_total;

                                                        $sales_data[$i]['item_grand_total']     = $key->item_grand_total;

                                                        $sales_data[$i]['item_tax_cess_amount']     = $key->item_tax_cess_amount;

                                                        $sales_data[$i]['item_tax_cess_percentage']     = $key->item_tax_cess_percentage;

                                                        $sales_data[$i]['item_tax_id']     = $key->item_tax_id;

                                                        $sales_data[$i]['item_tax_cess_id']     = $key->item_tax_cess_id;

                                                        //array_push($product_data,$product);

                                                        $i++;
                                                        }

                                                        $sales            = htmlspecialchars(json_encode($sales_data));
                                                        ?>

                                                    </tbody>

                                                </table>

                                                <table id="table-total" class="table table-striped table-bordered table-condensed table-hover">

                                                    <tr>

                                                        <td align="right">Total Value (+)</td>

                                                        <td align='right'><span id="totalSubTotal"><?php echo precise_amount($data[0]->voucher_sub_total); ?></span></td>

                                                    </tr>

                                                    <tr style="display: none">

                                                        <td align="right">Total Tax (+)</td>

                                                        <td align='right'><span id="totalTaxAmount"><?php echo $data[0]->voucher_tax_amount; ?></span>

                                                    </td>

                                                </tr>

                                            <tr <?= ($cgst_exist != 1 || $data[0]->voucher_cgst_amount <= 0 ? 'style="display: none;"' : ''); ?> class='totalCGSTAmount_tr'>

                                                <td align="right"><?php echo 'CGST'; ?> (+)</td>

                                                <td align='right'>

                                                    <span id="totalCGSTAmount"><?= precise_amount($data[0]->voucher_cgst_amount); ?></span>

                                                </td>

                                            </tr>

                                            <tr <?= ($sgst_exist != 1 || $data[0]->voucher_sgst_amount <= 0 ? 'style="display: none;"' : ''); ?> class='totalSGSTAmount_tr'>

                                                <?php
                                                $lbl = 'SGST';

                                                if ($is_utgst == '1')
                                                    $lbl = 'UTGST';
                                                ?>

                                                <td align="right <?= $data[0]->billing_state_id; ?>"><?= $lbl; ?> (+)</td>

                                                <td align='right'>

                                                    <span id="totalSGSTAmount"><?= precise_amount($data[0]->voucher_sgst_amount); ?></span>

                                                </td>

                                            </tr>

                                            <tr  <?= ($igst_exist != 1 || $data[0]->voucher_igst_amount <= 0 ? 'style="display: none;"' : ''); ?>  class='totalIGSTAmount_tr'>

                                                <td align="right"><?php echo 'IGST'; ?> (+)</td>

                                                <td align='right'>

                                                    <span id="totalIGSTAmount"><?= precise_amount($data[0]->voucher_igst_amount); ?></span>

                                                </td>

                                            </tr>

                                            <tr <?= ($cess_exist != 1 || $data[0]->voucher_cess_amount <= 0 ? 'style="display: none;"' : ''); ?> class='totalCessAmount_tr'>

                                                <td align="right"><?php echo 'Cess'; ?> (+)</td>

                                                <td align='right'>

                                                    <span id="totalTaxCessAmount"><?= precise_amount($data[0]->voucher_cess_amount); ?></span>

                                                </td>

                                            </tr>

                                                <tr>

                                                    <td align="right">Grand Total (=)</td>

                                                <td align='right'><span id="totalGrandTotal"><?php echo precise_amount($data[0]->receipt_amount, 2); ?></span></td>

                                                </tr>                                                

                                        </table>

                                        <p class="validation-color" id="err_amount_exceeds"></p>

                                    </div>

                                </div>

                            </div>

                            <!--hidden -->

                            <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php echo $branch[0]->branch_country_id; ?>">

                            <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php echo $branch[0]->branch_state_id; ?>">

                            <input type="hidden" class="form-control" id="advance_voucher_id" name="advance_voucher_id" value="<?php echo $data[0]->reference_id; ?>" readonly>

                            <input type="hidden" class="form-control" id="refund_id" name="refund_id" value="<?php echo $data[0]->refund_id; ?>" readonly>

                            <input type="hidden" class="form-control" id="voucher_date_old" name="voucher_date_old" value="<?php echo date('d-m-Y', strtotime($data[0]->voucher_date)); ?>" readonly>

                            <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" readonly>

                            <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>

                            <input type="hidden" class="form-control" id="voucher_number_old" name="voucher_number_old" value="<?php echo $data[0]->voucher_number; ?>" readonly>

                            <input type="hidden" class="form-control" id="section_area" name="section_area" value="edit_refund_voucher" readonly>

                            <input type="hidden" class="form-control" id="reference_number_text" name="reference_number_text" value="" readonly>

                            <input type="hidden" class="form-control" id="receipt_amount_old" name="receipt_amount_old" value="<?= $data[0]->receipt_amount ?>" readonly>

                            <input type="hidden" name="total_sub_total" id="total_sub_total" value="<?= $data[0]->voucher_sub_total; ?>">

                            <input type="hidden" name="total_tax_amount" id="total_tax_amount" value="<?= $data[0]->voucher_tax_amount; ?>">

                             <input type="hidden" name="total_tax_cess_amount" id="total_tax_cess_amount" value="<?= $data[0]->voucher_cess_amount; ?>">

                            <input type="hidden" name="total_igst_amount" id="total_igst_amount" value="<?= $data[0]->voucher_igst_amount; ?>">

                            <input type="hidden" name="total_cgst_amount" id="total_cgst_amount" value="<?= $data[0]->voucher_cgst_amount; ?>">

                            <input type="hidden" name="total_sgst_amount" id="total_sgst_amount" value="<?= $data[0]->voucher_sgst_amount; ?>">

                            <input type="hidden" name="total_grand_total" id="total_grand_total" value="<?= $data[0]->receipt_amount; ?>">

                            <input type="hidden" name="table_data" id="table_data" value="<?php echo $sales; ?>">

                            <!-- hidden -->



                                <div class="box-footer">

                                    <button type="submit" id="receipt_submit" class="btn btn-info">Update</button>

                                    <button class="btn btn-default" id="receipt_cancel" onclick="cancel('refund_voucher')">Cancel</button>

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

$this->load->view('bank_account/bank_modal');
?>

<script type="text/javascript">

   /* $(document).ready(function () {

        if ($('#payment_mode').val() == 'other payment mode') {

            $('#other_payment').show();

        }
    })*/

        
    

    var sales_data = new Array();

    var sales_data = <?php echo json_encode($sales_data); ?>;

    var branch_state_list = <?php echo json_encode($state); ?>;

    var item_gst = <?php echo json_encode($item_gst); ?>;

    var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";

    var common_settings_tax_split = "<?= $access_common_settings[0]->tax_split_equaly ?>";



  var common_settings_amount_precision = "<?= $access_common_settings[0]->amount_precision ?>";

    var settings_tax_percentage = "<?= $access_common_settings[0]->tax_split_percentage ?>";

    var settings_tax_type = "<?= $access_settings[0]->tax_type ?>";

    var settings_discount_visible = "<?= $access_settings[0]->discount_visible ?>";

    var settings_description_visible = "<?= $access_settings[0]->description_visible ?>";

    var settings_tds_visible = "<?= $access_settings[0]->tds_visible ?>";

    var settings_item_editable = "<?= $access_settings[0]->item_editable ?>";

</script>

<script src="<?php echo base_url('assets/js/vouchers/') ?>refund.js"></script>

<script src="<?php echo base_url('assets/js/vouchers/') ?>refund_basic.js"></script>

<script type="text/javascript">
    $(document).ready(function (){
        var payment_mode = '<?php echo $data[0]->to_account;?>';
        if (payment_mode == null || payment_mode == "") {
            $('#hide').hide();
            $("#other_payment").hide();            
        } else {
            if (payment_mode != "cash" && payment_mode != "" && payment_mode != "others" ) {
                //&& payment_mode == "bank"
                $('#hide').show();
                $("#other_payment").hide();                
            }else{
                $('#hide').hide();
                $("#bank_name").val('');
                $("#cheque_number").val('');
                $("#cheque_date").val('');
            }

            if (payment_mode != "cash" && payment_mode != "" && payment_mode != "bank" && payment_mode == "other payment mode") {
                $("#other_payment").show();
                $('#hide').hide();
                $("#bank_name").val('');
                $("#cheque_number").val('');
                $("#cheque_date").val('');
            }else{
                $("#other_payment").hide();
                $("#payment_via").val('');
                $("#ref_number").val('');
            }
        }
});

</script>