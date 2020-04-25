<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$type           = user_limit();
$scount         = supplier_count();
$limit          = $type['coun'] + $scount['scount'];
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('advance_voucher'); ?>">Advance Voucher</a></li>
            <li class="active">Edit Advance Voucher</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Advance Voucher</h3>
                        <a class="btn btn-default pull-right back_button" id="sale_cancel" onclick1="cancel('advance_voucher')">Back</a>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post" action="<?php echo base_url('advance_voucher/edit_advance'); ?>">
                                <div class="col-md-12">
                                    <div class="well">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <?php
                                                    $date = $data[0]->voucher_date;
                                                    ?>
                                                    <label for="date">Voucher Date <span class="validation-color">*</span></label>
                                                    <div class="input-group date">
                                                        <input type="text" class="form-control datepicker" id="voucher_date" name="voucher_date" value="<?php echo date('d-m-Y', strtotime($data[0]->voucher_date)); ?>">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                    <span class="validation-color" id="err_voucher_date"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Voucher Number <span class="validation-color">*</span></label>
                                                    <input type="text" class="form-control" id="voucher_number" name="voucher_number" value="<?= $data[0]->voucher_number ?>" <?php
                                                    if ($access_settings[0]->invoice_readonly == 'yes'){ echo "readonly"; }?>>
                                                    <span class="validation-color" id="err_voucher_number"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="customer">Customer <span class="validation-color">*</span></label>
                                                    <?php
                                                    if (isset($other_modules_present['customer_module_id']) && $other_modules_present['customer_module_id'] != ""){
                                                        ?>
                                                       <!--  <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#customer_modal" data-reference_type="advance_voucher" class="open_customer_modal pull-right">+</a></div> -->
                                                        <?php } ?>
                                                        <select class="form-control" id="customer" name="customer" readonly>
                                                            <option value="">Select</option>
                                                            <?php
                                                            foreach ($customer as $row){
                                                                if ($row->customer_id == $data[0]->party_id){
                                                                    if($row->customer_mobile != ''){
                                                                        echo "<option value='$row->customer_id-$row->customer_country_id-$row->customer_state_id' selected>$row->customer_name($row->customer_mobile)</option>";
                                                                    }else{
                                                                        echo "<option value='$row->customer_id-$row->customer_country_id-$row->customer_state_id' selected>$row->customer_name</option>";
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    <!-- </div> -->
                                                    <span class="validation-color" id="err_customer"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="amount">Advance Amount <span class="validation-color">*</span></label>
                                                    <input type="text" class="float_number form-control" id="receipt_amount" name="receipt_amount" value="<?= round($data[0]->receipt_amount,2) ?>">
                                                    <span class="validation-color" id="err_receipt_amount"><?php echo form_error('receipt_amount'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                             <div class="col-sm-3" style="display: none">
                    <div class="form-group">
                      <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>
                      <select class="form-control select2" id="type_of_supply" name="type_of_supply" >
                        <option value="regular" <?php echo "selected"; ?>>Regular</option>
                      </select>

                      <span class="validation-color" id="err_type_supply"></span>
                    </div>
                  </div>
                                            <div class="col-sm-3" style="display: none">
                                                <div class="form-group">
                                                    <label for="type_of_supply">Billing Country <span class="validation-color">*</span></label>
                                                    <select class="form-control select2" id="billing_country" name="billing_country">
                                                        <?php
                                                        foreach ($country as $key => $value)
                                                        {
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
                                                        <option value="">Select</option>
                                                      
                                                            <?php
                                                            foreach ($state as $key => $value){
                                                                ?>
                                                                <option value="<?= $value->state_id ?>" <?php if ($value->state_id == $data[0]->billing_state_id) echo "selected='selected'"; ?> utgst='<?= $value->is_utgst; ?>'><?= $value->state_name ?></option>

                                                            <?php } 
                                                            if($data[0]->billing_state_id == 0){
                                                                 ?>
                                                                <option value="0" utgst='0' selected='selected'>Out of Country</option>
                                                                <?php
                                                            }else{
                                                                ?>
                                                                <option value="0" utgst='0' >Out of Country</option>
                                                                <?php
                                                            }?>
                                                            
                                                    </select>
                                                    <span class="validation-color" id="err_billing_state"><?php echo form_error('billing_state'); ?></span>
                                                </div>
                                            </div>
                                          <div class="col-sm-3" style="display: none">
                                                <div class="form-group">
                                                    <label for="currency_id">Billing Currency <span class="validation-color">*</span></label>
                                                    <select class="form-control select2" id="currency_id" name="currency_id">
                                                        <?php
                                                        foreach ($currency as $key => $value)
                                                        {
                                                            if ($value->currency_id == $data[0]->currency_id) {
                                                                echo "
                                                                    <option value='" . $value->currency_id . "' selected>" . $value->currency_name.' - ' . $value->country_shortname. "</option>";
                                                            } else {
                                                                echo "
                                                                    <option value='" . $value->currency_id . "'>" . $value->currency_name . ' - ' . $value->country_shortname."</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <span class="validation-color" id="err_currency_id"></span>
                                                </div>
                                            </div>
                                             <div class="col-sm-3 gst_payable_div" >
                                                <div class="form-group">
                                            <label for="gst_payable">GST Payable(Reverse Charge) <span class="validation-color">*</span></label>
                                            <br/>
                                            <div class="radio">
                                            <label class="radio-inline">
                                                        <input type="radio" name="gst_payable" value="yes" <?php
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
                                                                ?> /> No
                                                    </label>
                                                </div>
                                            <br/>
                                            <span class="validation-color" id="err_gst_payable"></span>
                                        </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="paying_by">Payment Mode <span class="validation-color">*</span></label>
                                                    <!-- <?php
                                                    if (isset($other_modules_present['bank_account_module_id']) && $other_modules_present['bank_account_module_id'] != "")
                                                    {
                                                        ?> -->
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <?php 
                                                                if(in_array($bank_account_module_id, $active_add)){?>
                                                                <a href="" data-toggle="modal" data-target="#bank_account_modal" class="pull-right">+</a>
                                                                <?php } ?>
                                                            </div>
                                                        <!-- <?php } ?> -->
                                                        <select class="form-control select2" id="payment_mode" name="payment_mode">
                                                            <option value="">Select</option>
                                                           <option value="cash" <?php
                                                            if ($data[0]->payment_mode == "cash")
                                                            {
                                                                echo "selected";
                                                            }
                                                            ?>>Cash</option>                   
                                                            <option value="other payment mode" <?php
                                                            if ($data[0]->payment_mode == "other payment mode")
                                                            {
                                                                echo "selected";
                                                            }
                                                            ?>>Other payment mode</option>

                                                            <?php
                                                            if (isset($other_modules_present['bank_account_module_id']) && $other_modules_present['bank_account_module_id'] != "")
                                                            {
                                                                foreach ($bank_account as $key => $value)
                                                                {
                                                                    ?>
                                                                    <option value="<?php echo $value->bank_account_id . "/" . $value->ledger_title ?>" <?php if ($value->bank_account_id == $data[0]->payment_mode) echo "selected='selected'"; ?> ><?= $value->ledger_title ?></option>
                                                            <?php
                                                            }
                                                        }
                                                        ?>
                                                        </select>
                                                    </div>
                                                    <span class="validation-color" id="err_payment_mode"></span>
                                                </div>
                                            </div>
                                            <div id="hide">                               
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="cheque_no">Cheque/Reference Number</label>
                                                        <input type="text" class="form-control" id="cheque_number" name="cheque_number" value="<?= $data[0]->cheque_number ?>">
                                                        <span class="validation-color" id="err_cheque_number"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">                              
                                                    <div class="form-group">
                                                        <label for="cheque_date">Cheque/Reference Date</label>
                                                        <?php
                                                        //$cheque_date = explode('-', $data[0]->cheque_date);
                                                        if ( $data[0]->cheque_date != '0000-00-00' && $data[0]->cheque_date != '' && $data[0]->cheque_date != '1970-01-01'){
                                                            $cheque_date = date('d-m-Y', strtotime($data[0]->cheque_date));
                                                        }else{
                                                            $cheque_date = "";
                                                        }
                                                        ?>
                                                       
                                                        <div class="input-group date">
                                                        <input type="text" class="form-control datepicker" id="cheque_date" name="cheque_date" value="<?= $cheque_date ?>"><div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                                        <span class="validation-color" id="err_cheque_date"></span>
                                                    </div>
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
                                                        <label for="bank_name">Reference Number</label>
                                                        <input type="text" class="form-control" id="ref_number" name="ref_number" value="<?= $data[0]->ref_number ?>">
                                                        <span class="validation-color" id="err_ref_number"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--<div class="row">
                                            <div id="other_payment" style="display: none;">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="bank_name">Payment Via</label>
                                                        <input type="text" class="form-control" id="payment_via" name="payment_via" value="">
                                                        <span class="validation-color" id="err_payment_via"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="bank_name">Reference Number</label>
                                                        <input type="text" class="form-control" id="ref_number" name="ref_number" value="">
                                                        <span class="validation-color" id="err_ref_number"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div >
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="bank_name">Bank Name</label>
                                                        <input type="text" class="form-control" id="bank_name" name="bank_name" value="">
                                                        <span class="validation-color" id="err_bank_name"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="cheque_no">Cheque Number</label>
                                                        <input type="text" class="form-control" id="cheque_number" name="cheque_number" value="">
                                                        <span class="validation-color" id="err_cheque_number"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="cheque_date">Cheque Date</label>
                                                        <input type="text" class="form-control datepicker" id="cheque_date" name="cheque_date" value="">
                                                        <span class="validation-color" id="err_cheque_date"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="paying_by">Description</label>
                                                    <textarea type="text" class="form-control" id="description" name="description"><?=
                                str_replace(array(
                                        "\r\n",
                                        "\\r\\n",
                                        "\n",
                                        "\\n" ), "&#10;", $data[0]->description);
                                ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
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
                                        <a href="" data-toggle="modal" data-target="#product_modal" class="open_product_modal pull-left">+ Add Product</a>
                                    <?php } ?>
                                    <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product Code/Name" >
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
                                        <a href="" data-toggle="modal" data-target="#service_modal" class="open_service_modal pull-left">+ Add Service</a>
                                    <?php } ?>
                                    <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product Code/Name" >
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
                                                <!-- <a href="" data-toggle="modal" data-target="#item_modal" class="pull-left">+</a> -->
                                            <?php } ?>
                                        </div>
                                        <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product/Service Code/Name" >
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <span class="validation-color" id="err_sales_code"></span>
                            </div>
                            <div class="col-sm-12">
                                <div class="box-header">
                                    <h3 class="box-title ml-0">Inventory Items</h3>
                                <table class="table table-striped table-bordered table-condensed table-hover sales_table table-responsive" id="sales_data">
                                    <thead>
                                        <tr>
                                            <th width="2%"><img src="<?php echo base_url(); ?>assets/images/bin1.png" /></th>
                                            <th class="span2">Items</th>
                                          <?php
                                            if ($access_settings[0]->description_visible == 'yes'){
                                            ?>
                                            <th class="span2">Description</th>
                                            <?php } ?>
                                            <th class="span2" width="6%">Quantity</th>
                                            <th class="span2" width="12%">Rate</th>
                                            <?php
                                                if ($access_settings[0]->discount_visible == 'yes'){
                                            ?>
                                            <th class="span2" width="8%">Discount
                                            <?php
                                             if (in_array($discount_module_id, $active_add)){
                                            ?>
                                            <a href="" data-toggle="modal" data-target="#discount_modal"><strong>+</strong></a>
                                         <?php }?>
                                        </th>
                                        <?php }
                                        if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax') {
                                        ?>
                                    <th class="span2" >Taxable Value</th>
                                    <?php } ?>
                                <?php if ($access_settings[0]->tax_type == 'gst' || $access_settings[0]->tax_type == 'single_tax'){ ?>
                                <th class="span2" width="10%">GST(%) </th>
                                <th class="span2" width="10%">Cess(%)</th>
                                <?php } ?>
                                <th class="span2" width="12%">Total</th>
                            </tr>
                        </thead>
                            <tbody id="sales_table_body">
                                 <?php
                                                $i   = 0;
                                                $tot = 0;
                                                foreach ($items as $key){?>
                                                <tr id="<?=$i?>">
                                                    <td><a class='deleteRow'> <img src='<?=base_url(); ?>assets/images/bin_close.png' /></a><input type='hidden' name='item_key_value'  value="<?=$i?>"><input type='hidden' name='item_id' value="<?=$key->item_id;?>"><input type='hidden' name='item_type' value="<?=$key->item_type;?>"></td>
                                                  <?php if ($key->item_type == 'product' || $key->item_type == 'product_inventory'){ ?>
                                                    <td>
                                                        <input type='hidden' name='item_code' value='<?php
                                                               echo $key->product_code;
                                                               ?>'>
                                                        <?php
                                                        echo $key->product_name;
                                                        ?><br>(P) HSN/SAC: <?php
                                                        echo $key->product_hsn_sac_code;
                                                        ?><br><?php
                                                        echo $key->product_batch;
                                                        ?>
                                                    </td>
                                                    <?php
                                                    }elseif($key->item_type == 'advance'){ ?>
                                                    <td>
                                                        <input type='hidden' name='item_code' value='<?php
                                                               echo $key->product_code;
                                                               ?>'>
                                                        <?php
                                                        echo $key->product_name;
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
                                                        ?><br>(S) HSN/SAC: <?php
                                                        echo $key->service_hsn_sac_code;
                                                        ?>
                                                    </td>
                                                    <?php
                                                    }
                                                    ?>
                                                   
                                                    <td>
                                                      <input type='text' class='form-control form-fixer' name='item_description' value='<?=$key->item_description;?>'>
                                                    </td>
                                                   
                                                    <td>
                                                      <input type='text' class='form-control form-fixer text-center float_number' value='<?php
                                                               echo $key->item_quantity ? round($key->item_quantity,2) : 0;
                                                               ?>' data-rule='quantity' name='item_quantity'>
                                                    </td>
                                                    <td>
                                                      <input type='text' class='form-control form-fixer text-right float_number' name='item_price' value='<?php
                                                               echo $key->item_price ? precise_amount($key->item_price) : 0;
                                                               ?>'><span id='item_sub_total_lbl_<?=$i?>' class='pull-right' style='color:red;'><?php
                                                            echo $key->item_sub_total ? precise_amount($key->item_sub_total) : 0;
                                                            ?></span><input type='hidden' class='form-control form-fixer text-right' style='' value='<?php
                                                                        echo $key->item_sub_total ? precise_amount($key->item_sub_total) : 0;
                                                                        ?>' name='item_sub_total'>
                                                    </td>
                                                    
                                                    <td style="display: none;">
                                                        <input type='hidden' name='item_discount_id' value='0'>
                                                        <input type='hidden' name='item_discount_percentage' value='0'>
                                                        <input type='hidden' name='item_discount_amount' value='0'>
                                                        <div class="form-group" style="margin-bottom:0px !important; display: none;">
                                                            <select class="form-control open_discount form-fixer select2" name="item_discount" style="width: 100%;">
                                                                <option value="">Select</option>
                                                                <?php
                                                                foreach ($discount as $key3 => $value3)
                                                                {
                                                                
                                                                echo "<option value='" . $value3->discount_id . "-" . (float)($value3->discount_value) . "'>" . (float)($value3->discount_value) . "%</option>";
                                                                
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <span id='item_discount_lbl_<?=$i?>' class='pull-right' style='color:red;'></span>
                                                    </td>
                                                    
                                                    <!-- tax area -->
                                                    <td align='right'>
                                                      <input type='hidden' name='item_taxable_value' value='<?=($key->item_sub_total ? $key->item_sub_total : 0);?>' >
                                                        <span id='item_taxable_value_lbl_<?=$i?>'><?=$key->item_sub_total ? precise_amount($key->item_sub_total) : 0;?></span>
                                                    </td>
                                                   
                                                   
                                                    
                                                    <td><input type='hidden' name='item_tax_id' value='<?=$key->item_gst_id ? $key->item_gst_id : 0?>'>
                                                        <input type='hidden' name='item_tax_percentage' value='<?=$key->item_tax_percentage ? precise_amount($key->item_tax_percentage) : 0?>'>
                                                        <input type="hidden" name="item_tax_amount" value="<?=$key->item_tax_amount ? precise_amount($key->item_tax_amount) : 0?>">
                                                        <input type="hidden" name="item_tax_amount_igst" value="<?=$key->item_igst_amount ? precise_amount($key->item_igst_amount) : 0?>">
                                                        <input type="hidden" name="item_tax_amount_cgst" value="<?=$key->item_cgst_amount ? precise_amount($key->item_cgst_amount) : 0?>">
                                                        <input type="hidden" name="item_tax_amount_sgst" value="<?=$key->item_sgst_amount ? precise_amount($key->item_sgst_amount) : 0?>"><input type="hidden" name="item_tax_amount_igst" value="<?=$key->item_sgst_percentage ? precise_amount($key->item_sgst_percentage) : 0?>">
                                                        <input type="hidden" name="item_percentage_cgst" value="<?=$key->item_cgst_percentage ? precise_amount($key->item_cgst_percentage) : 0?>">
                                                        <input type="hidden" name="item_percentage_sgst" value="<?=$key->item_sgst_percentage ? precise_amount($key->item_sgst_percentage) : 0?>">
                                                        <input type="hidden" name="item_percentage_igst" value="<?=$key->item_igst_percentage ? precise_amount($key->item_igst_percentage) : 0?>">
                                                        <div class="form-group" style="margin-bottom:0px !important;">
                                                        <div class="form-group" style="margin-bottom:0px !important;">
                                                            <select class="form-control open_tax form-fixer select2" name="item_tax" style="width: 100%;">
                                                                <option value="">Select</option>
                                                                <?php
                                                                foreach ($tax as $key3 => $value3){
                                                                  if($value3->tax_name == 'GST'){
                                                                
                                                                    echo "<option value='" . $value3->tax_id . "-" . (float)($value3->tax_value) . "' ".($value3->tax_id == $key->item_gst_id ? 'selected' : '' ).">" . (float)($value3->tax_value) . "%</option>";
                                                                  }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <span id='item_tax_lbl_<?=$i?>' class='pull-right' style='color:red;'><?=$key->item_tax_amount ? precise_amount($key->item_tax_amount) : 0?></span>
                                                    </td>
                                                    
                                                    <td><input type='hidden' name='item_tax_cess_id' value='<?=($key->item_cess_id ? $key->item_cess_id : 0);?>'>
                                                        <input type='hidden' name='item_tax_cess_percentage' value='<?=$key->item_cess_percentage ? precise_amount($key->item_cess_percentage) : 0;?>'>
                                                        <input type='hidden' name='item_tax_cess_amount' value='<?=$key->item_cess_amount ? precise_amount($key->item_cess_amount) : 0?>'>
                                                        <div class="form-group" style="margin-bottom:0px !important;">
                                                            <select class="form-control open_tax form-fixer select2" name="item_tax_cess" style="width: 100%;" <?=$key->item_cess_id;?>>
                                                                <option value="">Select</option>
                                                                <?php
                                                                foreach ($tax as $key3 => $value3) {
                                                                  if($value3->tax_name == 'CESS'){
                                                                    echo "<option value='" . $value3->tax_id . "-" . (float)($value3->tax_value) . "' ".($value3->tax_id == $key->item_cess_id ? 'selected' : '' ).">" . (float)($value3->tax_value) . "%</option>";
                                                                  }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <span id='item_tax_cess_lbl_<?=$i?>' class='pull-right' style='color:red;'><?=$key->item_cess_amount ? precise_amount($key->item_cess_amount) : 0?></span>
                                                    </td>
                                                   
                                                    <!-- tax area  -->
                                                    <td>
                                                      <input type='text' class='float_number form-control form-fixer text-right' name='item_grand_total' value='<?php
                                                               echo $key->item_grand_total ? precise_amount($key->item_grand_total) : 0;
                                                               ?>'>
                                                    </td>
                                                    <?php    
                                                    $sales_temp = array(
                                                      "item_key_value"           => $i,
                                                      "item_id"                  => $key->item_id,
                                                      "item_type"                => $key->item_type,
                                                      "item_quantity"            => $key->item_quantity,
                                                      "item_price"               => $key->item_price ? precise_amount($key->item_price) : 0,
                                                      "item_description"         => $key->item_description,
                                                      "item_sub_total"           => $key->item_sub_total ? precise_amount($key->item_sub_total) : 0,
                                                      "item_discount_amount"     =>  0,
                                                      "item_discount_id"         =>  0,
                                                      "item_discount_percentage" =>  0,
                                                      "item_tax_amount"          => $key->item_tax_amount ? precise_amount($key->item_tax_amount) : 0,
                                                      "item_tax_id"              => $key->item_gst_id ? $key->item_gst_id : 0,
                                                      "item_tax_cess_amount"  => $key->item_cess_amount ? precise_amount($key->item_cess_amount) : 0,
                                                      "item_tax_cess_id"              => $key->item_cess_id ? $key->item_cess_id : 0,
                                                      "item_tax_cess_percentage" => $key->item_cess_percentage ? precise_amount($key->item_cess_percentage) : 0,
                                                      "item_tax_percentage"      => $key->item_tax_percentage ? precise_amount($key->item_tax_percentage) : 0,
                                                      "item_tds_amount"          => $key->item_tds_amount ? precise_amount($key->item_tds_amount) : 0,
                                                      "item_tds_id"              => $key->item_tds_id ? $key->item_tds_id : 0,
                                                      "item_tds_percentage"      => $key->item_tds_percentage ? precise_amount($key->item_tds_percentage) : 0,
                                                      "item_taxable_value"       => $key->item_sub_total ? precise_amount($key->item_sub_total) : 0,
                                                      "item_grand_total"         => $key->item_grand_total ? precise_amount($key->item_grand_total) : 0,
                                                      "item_tax_amount_igst" => $key->item_igst_amount ? precise_amount($key->item_igst_amount) : 0,
                                                      "item_tax_amount_cgst" => $key->item_cgst_amount ? precise_amount($key->item_cgst_amount) : 0,
                                                      "item_tax_amount_sgst" => $key->item_sgst_amount ? precise_amount($key->item_sgst_amount) : 0,
                                                      "item_percentage_igst" => $key->item_igst_percentage ? precise_amount($key->item_igst_percentage) : 0,
                                                      "item_percentage_sgst" => $key->item_sgst_percentage ? precise_amount($key->item_sgst_percentage) : 0,
                                                      "item_percentage_cgst" => $key->item_cgst_percentage ? precise_amount($key->item_cgst_percentage) : 0
                                                    );
                                                    if ($key->item_type == 'product' || $key->item_type == 'product_inventory')
                                                    {
                                                    $sales_temp['item_code'] = $key->product_code;
                                                    }
                                                    else
                                                    {
                                                    $sales_temp['item_code'] = $key->service_code;
                                                    }
                                                    ?>
                                                </tr>
                                                <?php
                                                $sales_data[$i] = $sales_temp;
                                                $i++;
                                              }
                                                $sales     = htmlspecialchars(json_encode($sales_data));
                                                $countData = $i;
                                                ?>
                            </tbody>
                    </table>
                      <!-- Hidden Field -->
                    <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php echo $branch[0]->branch_country_id; ?>">
                    <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php echo $branch[0]->branch_state_id; ?>">
                    <input type="hidden" name="is_utgst" id="is_utgst" value="0">
                  <!--  <input type="hidden" class="form-control" id="product_module_id" name="product_module_id" value="<?=$product_module_id?>" > -->
                  
                   
                    <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo $date; ?>" >
                    <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?=$module_id?>" >
                    <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="" >
                    <input type="hidden" name="total_sub_total" id="total_sub_total" value="<?php echo precise_amount($data[0]->voucher_sub_total);?>">
                    <input type="hidden" name="total_taxable_amount" id="total_taxable_amount" value="<?=precise_amount($data[0]->voucher_sub_total);?>">
                    <input type="hidden" name="total_other_taxable_amount" id="total_other_taxable_amount">
                    <input type="hidden" name="total_igst_amount" id="total_igst_amount" value="<?=precise_amount($data[0]->voucher_igst_amount);?>">
                    <input type="hidden" name="total_cgst_amount" id="total_cgst_amount" value="<?=precise_amount($data[0]->voucher_cgst_amount);?>">
                    <input type="hidden" name="total_sgst_amount" id="total_sgst_amount" value="<?=precise_amount($data[0]->voucher_sgst_amount);?>">
                    <input type="hidden" name="total_discount_amount" id="total_discount_amount" value="">
                    <input type="hidden" name="total_tax_amount" id="total_tax_amount" value="<?=precise_amount($data[0]->voucher_tax_amount);?>">
                    <input type="hidden" name="total_tax_cess_amount" id="total_tax_cess_amount" value="<?=precise_amount($data[0]->voucher_cess_amount);?>">
                    <input type="hidden" name="total_tds_amount" id="total_tds_amount" value="<?=precise_amount($data[0]->voucher_tds_amount);?>">
                    <input type="hidden" name="total_tcs_amount" id="total_tcs_amount" value="">
                    <?php
                    $round_off_amount = $data[0]->round_off_amount;
                    if($round_off_amount  > 0 ){
                        $round_off_plus = $round_off_amount;
                        $round_off_minus = 0;
                    }elseif($round_off_amount < 0 ){
                        $round_off_plus = 0;
                        $round_off_minus = $round_off_amount;
                    }else{
                        $round_off_plus = 0;
                        $round_off_minus = 0;  
                    }?>
                    <input type="hidden" name="round_off_plus" id="round_off_plus" value="<?php echo $round_off_plus;?>">
                    <input type="hidden" name="round_off_minus" id="round_off_minus" value="<?php echo $round_off_minus;?>">

                    <input type="hidden" name="total_grand_total" id="total_grand_total" value="<?=precise_amount($data[0]->receipt_amount);?>">
                    <input type="hidden" name="without_reound_off_grand_total" id="without_reound_off_grand_total"  value="<?=precise_amount($data[0]->grand_total_without_roundoff);?>">
                    <input type="hidden" name="table_data" id="table_data" value="<?php echo $sales; ?>">
                    <input type="hidden" name="total_other_amount" id="total_other_amount">
                    
                <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive">
                    <tr>
                        <td align="right">Total Value (+)</td>
                        <td align='right'><span id="totalSubTotal"><?php echo precise_amount($data[0]->voucher_sub_total);?></span>
                        </td>
                    </tr>
                      <tr <?=($access_settings[0]->discount_visible == 'no'? 'style="display: none;"' : '');?> class='totalDiscountAmount_tr'>
                        <td align="right"><?php echo 'Discount'; ?> (-)</td>
                        <td align='right'>
                          <span id="totalDiscountAmount">0.00</span>
                        </td>
                      </tr>
                                    <tr <?=($tds_exist != 1 || $data[0]->voucher_tds_amount <= 0 ? 'style="display: none;"' :'');?>>
                                                <td align="right"><?php echo 'TDS Amount'; ?> (+-)</td>
                                                <td align='right'>
                                                    <span id="totalTdsAmount"><?=precise_amount($data[0]->voucher_tds_amount);?></span>
                                                </td>
                                            </tr>
                                            
                      
                       <tr <?=($cgst_exist != 1 || $data[0]->voucher_cgst_amount <= 0 ? 'style="display: none;"' : '');?> class='totalCGSTAmount_tr'>
                                                <td align="right">CGST (+)</td>
                                                <td align='right'><span id="totalCGSTAmount"><?=precise_amount($data[0]->voucher_cgst_amount);?></span>
                                                </td>
                                            </tr>
                                            <tr <?=($sgst_exist != 1 || $data[0]->voucher_sgst_amount <= 0 ? 'style="display: none;"' : '');?> class='totalSGSTAmount_tr'>
                                                <?php $lbl = 'SGST';
                                                if ($is_utgst == '1')
                                                    $lbl = 'UTGST';
                                                ?>
                                                <td align="right <?=$data[0]->billing_state_id;?>"><?=$lbl;?> (+)</td>
                                                <td align='right'><span id="totalSGSTAmount"><?=precise_amount($data[0]->voucher_sgst_amount);?></span>
                                                </td>
                                            </tr>
                                            <tr <?=($igst_exist != 1 || $data[0]->voucher_igst_amount <= 0 ? 'style="display: none;"' : '');?> class='totalIGSTAmount_tr'>
                                                <td align="right">IGST (+)</td>
                                                <td align='right'><span id="totalIGSTAmount"><?=precise_amount($data[0]->voucher_igst_amount);?></span>
                                                </td>
                                            </tr>
                                            <tr <?=($cess_exist != 1 || $data[0]->voucher_cess_amount <= 0 ? 'style="display: none;"' : '');?> class='totalCessAmount_tr'>
                                                <td align="right">Cess (+)</td>
                                                <td align='right'><span id="totalTaxCessAmount"><?=precise_amount($data[0]->voucher_cess_amount);?></span>
                                                </td>
                                            </tr>
                                            <?php if ($access_common_settings[0]->round_off_access == 'yes'){ ?>
                                                <tr class="round_off_minus_tr" <?=($data[0]->round_off_amount >= 0 ? 'style="display: none;"' : '');?>>
                                                  <td align="right">Round Off (-)</td>
                                                  <td align='right'>
                                                    <span id="round_off_minus_charge"><?=precise_amount($data[0]->round_off_amount);?></span>
                                                  </td>
                                                </tr>
                                                <tr class="round_off_plus_tr" <?=($data[0]->round_off_amount <= 0 ? 'style="display: none;"' : '');?>>
                                                  <td align="right">Round Off (+)</td>
                                                  <td align='right'>
                                                    <span id="round_off_plus_charge"><?=precise_amount($data[0]->round_off_amount);?></span>
                                                  </td>
                                                </tr>
                                            <?php    $checked        = "";
                                        $value_selected = "";
                                        if ($data[0]->round_off_amount > 0 || $data[0]->round_off_amount < 0 ){
                                        $checked     = "checked";
                                        $grand_total = precise_amount($data[0]->receipt_amount + $data[0]->round_off_amount);
                                        $next_val    = ceil($grand_total);
                                        $prev_val    = floor($grand_total);
                                        if ($data[0]->round_off_amount < 0)
                                        {
                                        $value_selected = "selected";
                                        }
                                        }
                                        ?>
                                        <tr>
                                                <td align="right" width="50%">Round Off</td>
                                                <td align="right" width="50%">
                                                     <label>
                                                        <input type="radio" name="round_off_key" value="yes" class="minimal" <?=$checked?>>yes
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="round_off_key" value="no" class="minimal" <?php
                                                               if ($checked == "")
                                                               {
                                                               echo "checked";
                                                               }
                                                               ?>>No
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
                                                        <option value="<?=$next_val?>"><?=$next_val?></option>
                                                        <?php
                                                        }
                                                        else
                                                        {
                                                        ?>
                                                        <option value="<?=$prev_val?>" <?php
                                                                if ($value_selected == "")
                                                                {
                                                                echo "selected";
                                                                }
                                                                ?> ><?=$prev_val?></option>
                                                        <option value="<?=$next_val?>" <?=$value_selected?>><?=$next_val?></option>
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

                                <?php
                                    } ?>
                      <tr>
                        <td align="right"><?php echo 'Grand Total'; ?> (=)</td>
                        <td align='right'><span id="totalGrandTotal"><?php echo precise_amount($data[0]->receipt_amount); ?></span></td>
                      </tr>
                      <tr>
                        <td colspan="2" align="right">
                            <span class="validation-color" id="err_amount_exceeds"></span>
                        </td>
                      </tr>
                    </table>
                    
                                            
                                       
                                </div>
                            </div>
                          
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
                            <input type="hidden" class="form-control" id="voucher_date_old" name="voucher_date_old" value="<?php echo $date; ?>" readonly>
                            <input type="hidden" class="form-control" id="advance_voucher_id" name="advance_voucher_id" value="<?php echo $data[0]->advance_voucher_id; ?>" readonly>
                            <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>
                            <input type="hidden" class="form-control" id="voucher_number_old" name="voucher_number_old" value="<?= $data[0]->voucher_number ?>" readonly>
                            <input type="hidden" class="form-control" id="section_area" name="section_area" value="edit_advance_voucher" readonly>
                            <input type="hidden" class="form-control" id="reference_number_text" name="reference_number_text" value="" readonly>
                            <input type="hidden" class="form-control" id="current_invoice_amount" name="current_invoice_amount" value="" readonly>
                            <div class="col-sm-12">
                                <div class="box-footer">
                                    <button type="submit" id="receipt_submit" class="btn btn-info">Update</button>
                                    <span class="btn btn-default" id="receipt_cancel" onclick="cancel('advance_voucher')">Cancel</span>
                                </div>
                            </div>
                        </div>
                        <?php
                            $notes_sub_module = 0;
                            $notes_sub_module = 0;

                        if (in_array($notes_sub_module_id, $access_sub_modules)){
                            $notes_sub_module = 1;
                        }

                        if ($notes_sub_module == 1){
                            $this->load->view('sub_modules/notes_sub_module');
                        }
                        ?>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');

$this->load->view('customer/customer_modal');
//$this->load->view('product/product_modal');
//$this->load->view('service/service_modal');
//$this->load->view('layout/item_modal');
$this->load->view('category/category_modal');
$this->load->view('subcategory/subcategory_modal');
//$this->load->view('tax/tax_modal');
//$this->load->view('discount/discount_modal');
//$this->load->view('service/sac_modal');
//$this->load->view('product/hsn_modal');
$this->load->view('bank_account/bank_modal');
//$this->load->view('product/product_inventory_modal');
?>
<script type="text/javascript">
   // $('#hide').hide();
    var sales_data = <?php echo json_encode($sales_data); ?>;
    var count_data = <?= $countData; ?>;
    var branch_state_list = <?php echo json_encode($state); ?>;
 //   var item_gst = new Array();
    var common_settings_round_off = "<?=$access_common_settings[0]->round_off_access?>";
    var common_settings_amount_precision = "<?=$access_common_settings[0]->amount_precision?>";
    var settings_tax_percentage = "<?=$access_common_settings[0]->tax_split_percentage?>";
    var common_settings_inventory_advanced = "<?=$inventory_access?>";
    var settings_tax_type = "<?=$access_settings[0]->tax_type?>";
    var settings_discount_visible = "<?=$access_settings[0]->discount_visible?>";
    var settings_description_visible = "<?=$access_settings[0]->description_visible?>";
    var settings_tds_visible = "<?=$access_settings[0]->tds_visible?>";
    var settings_item_editable = "<?=$access_settings[0]->item_editable?>";
</script>
<script src="<?php echo base_url('assets/js/vouchers/') ?>advance.js"></script>
<script src="<?php echo base_url('assets/js/vouchers/') ?>advance_basic.js"></script>
<script type="text/javascript">
    <?php
    if(empty($items)){
    ?>
        $('#err_sales_code').text('Please select the customer to add advance voucher.');
        $('.search_sales_code').hide();
        $('#input_sales_code').prop('disabled', true);
    <?php }else{ ?>
        $('#err_sales_code').text('');
        $('.search_sales_code').show();
        $('#input_sales_code').prop('disabled', false);
    <?php } ?>

    $(document).ready(function (){
        var payment_mode = '<?php echo $data[0]->from_account;?>';
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
