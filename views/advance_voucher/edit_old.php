<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

    </section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?php echo base_url('advance_voucher'); ?>">Advance Voucher</a></li>
            <li class="active">Edit Advance Voucher</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Advance Voucher</h3>
                        <a class="btn btn-default pull-right" id="sale_cancel" onclick="cancel('advance_voucher')">Back</a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post" action="<?php echo base_url('advance_voucher/edit_advance'); ?>">
                                <div class="col-md-12">
                                    <div class="well">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="date">Voucher Date <span class="validation-color">*</span></label>
                                                    <div class="input-group date">
                                                        <input type="text" class="form-control datepicker" id="voucher_date" name="voucher_date" value="<?php echo $data[0]->voucher_date ?>">
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
                                                    if ($access_settings[0]->invoice_readonly == 'yes')
                                                    {
                                                        echo "readonly";
                                                    }
                                                    ?>>
                                                    <span class="validation-color" id="err_voucher_number"></span>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="customer">Customer <span class="validation-color">*</span></label>
                                                    <select class="form-control select2" autofocus="on" id="customer" name="customer" style="width: 100%;">
                                                        <!--  <option value="">Select</option> -->
                                                        <?php
                                                        foreach ($customer as $row)
                                                        {
                                                            if ($row->customer_id == $data[0]->party_id)
                                                            {
                                                                ?>
                                                                <option value="<?= $row->customer_id ?>"><?= $row->customer_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <span class="validation-color" id="err_customer"></span>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="amount">Receipt Amount <span class="validation-color">*</span></label>
                                                    <input type="text" class="float_number form-control" id="receipt_amount" name="receipt_amount" value="<?= $data[0]->receipt_amount ?>">
                                                    <span class="validation-color" id="err_receipt_amount"><?php echo form_error('receipt_amount'); ?></span>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-3">
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
                                                        <?php
                                                        if ($data[0]->billing_country_id == $branch[0]->branch_country_id)
                                                        {
                                                            ?>
                                                            <?php
                                                            foreach ($state as $key => $value)
                                                            {
                                                                ?>
                                                                <option value="<?= $value->state_id ?>" <?php if ($value->state_id == $data[0]->billing_state_id) echo "selected='selected'"; ?> ><?= $value->state_name ?></option>

                                                            <?php } ?>
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
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="paying_by">Payment Mode <span class="validation-color">*</span></label>
<?php
if (isset($other_modules_present['bank_account_module_id']) && $other_modules_present['bank_account_module_id'] != "")
{
    ?>
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <a href="" data-toggle="modal" data-target="#bank_account_modal" class="pull-right">+</a>
                                                            </div>
                                                            <?php } ?>
                                                        <!-- <a href="" data-toggle="modal" data-target="#bank_account_modal" class="pull-right">+ Add Account</a> -->
                                                        <select class="form-control select2" id="payment_mode" name="payment_mode">
                                                            <option value="">Select</option>
                                                            <option value="cash" <?php
                                                            if ($data[0]->payment_mode == "cash")
                                                            {
                                                                echo "selected";
                                                            }
                                                            ?>>Cash</option>
                                                            <option value="bank" <?php
                                                                    if ($data[0]->payment_mode == "bank")
                                                                    {
                                                                        echo "selected";
                                                                    }
                                                                    ?>>Bank</option>
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

                                            <div id="hide">
                                                <div class="col-sm-3">
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
$cheque_date = explode('-', $data[0]->cheque_date);
if ($cheque_date[0] > 0)
{
    $cheque_date = $data[0]->cheque_date;
}
else
{
    $cheque_date = "";
}
?>
                                                        <label for="cheque_date">Cheque Date</label>
                                                        <input type="text" class="form-control datepicker" id="cheque_date" name="cheque_date" value="<?= $cheque_date ?>">
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
                                        "\\n" ), "&#10;", $data[0]->description);
                                ?></textarea>
                                                </div>
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
                                            $this->load->view('sub_modules/transporter_sub_module', $data);
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
                                ?>
                        </div>

                        <div class="well">
                            <div class="row">
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
                                    <span class="validation-color" id="err_product"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Inventory Items</label>
                                    <table class="table table-striped table-bordered table-condensed table-hover sales_table" name="sales_data" id="sales_data">
                                        <thead>
                                            <tr>
                                                <th width="2%"><img src="<?php echo base_url(); ?>assets/images/bin1.png" /></th>
                                                <th class="span2" width="10%">Product/Service Name</th>
                                                <th class="span2" width="17%">Product/Service Description</th>
                                                <th class="span2" width="6%" hidden="true">Quantity</th>
                                                <th class="span2" >Rate</th>
                                                <!--  <th class="span2" >Discount <a href="" data-toggle="modal" data-target="#discount_modal"><strong>+</strong></a></th> -->
                                                <th class="span2" hidden="true">Taxable Value</th>
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
                                                        <a class='deleteRow'> <img src='<?php echo base_url(); ?>assets/images/bin_close.png' /> </a>
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
                                                        <td><?php echo $key->product_name; ?><br>HSN/SAC: <?php echo $key->product_hsn_sac_code; ?></td>
                                                                <?php
                                                                }
                                                                else
                                                                {
                                                                    ?>
                                                        <td><?php echo $key->service_name; ?><br>HSN/SAC: <?php echo $key->service_hsn_sac_code; ?></td>
                                                                <?php } ?>
                                                    <td><input type='text' name='item_description' class='form-control form-fixer' value="<?php echo $key->item_description ?>"></td>

                                                    <td hidden="true"><input type="text" class="float_number form-control form-fixer text-center" value="1" data-rule="quantity" name='item_quantity' readonly>
                                                    </td>
                                                    <td>
                                                        <input type='text' class='float_number form-control form-fixer text-right' name='item_price' value='<?php echo $key->item_sub_total ?>'>
                                                        <span id='item_sub_total_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?php echo $key->item_sub_total; ?></span>
                                                        <input type='hidden' class='form-control' style='' value='<?php echo $key->item_sub_total ?>' name='item_sub_total' readonly>
                                                    </td>
                                                    <td align="right" style="display: none;">
                                                        <div class="form-group" >
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
                                                    <td>
    <?php
    $item_tax_percentage                             = $key->item_igst_percentage + $key->item_cgst_percentage + $key->item_sgst_percentage;
    $item_tax_amount                                 = $key->item_igst_amount + $key->item_cgst_amount + $key->item_sgst_amount;
    ?>
                                                        <input type='hidden' name='item_tax_percentage' value="<?= $item_tax_percentage ?>">
                                                        <input type='hidden' name='item_tax_amount' value="<?= $item_tax_amount ?>">
                                                        <input type="text" name="item_igst" class="float_number form-control form-fixer text-center" value="<?= $key->item_igst_percentage ?>" <?php if ($data[0]->billing_state_id == $branch[0]->branch_state_id) echo "readonly"; ?>>
                                                        <input type="hidden" name="item_igst_amount" class="form-control form-fixer" value="<?php echo $key->item_igst_amount; ?>" >

                                                        <span id="item_igst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red;"><?php echo $key->item_igst_amount; ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="item_cgst" class="float_number form-control form-fixer text-center" value="<?= $key->item_cgst_percentage ?>" <?php if ($data[0]->billing_state_id != $branch[0]->branch_state_id) echo "readonly"; ?>>
                                                        <input type="hidden" name="item_cgst_amount" class="form-control form-fixer" value="<?php echo $key->item_cgst_amount; ?>">
                                                        <span id="item_cgst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red;">
                                                    <?php echo $key->item_cgst_amount; ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="item_sgst" class="float_number form-control form-fixer text-center" value="<?= $key->item_sgst_percentage ?>" <?php if ($data[0]->billing_state_id != $branch[0]->branch_state_id) echo "readonly"; ?>>
                                                        <input type="hidden" name="item_sgst_amount" class="form-control form-fixer text-center" value="<?php echo $key->item_sgst_amount; ?>">
                                                        <span id="item_sgst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red;"><?php echo $key->item_sgst_amount; ?></span>
                                                    </td>
                                                    <td align="right">
                                                        <input type="text" class="float_number form-control form-fixer text-right" name="item_grand_total" value="<?php echo $key->item_grand_total; ?>">
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
                                                if ($key->item_type == 'product' || $key->item_type == 'product_inventory')
                                                {
                                                    $sales_data[$i]['item_code'] = $key->product_code;
                                                }
                                                else
                                                {
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
                                    <input type="hidden" class="form-control" id="advance_id" name="advance_id" value="<?= $advance_id ?>" readonly>
                                    <input type="hidden" class="form-control" id="voucher_date_old" name="voucher_date_old" value="<?= $data[0]->voucher_date ?>" readonly>
                                    <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" readonly>
                                    <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>
                                    <input type="hidden" class="form-control" id="voucher_number_old" name="voucher_number_old" value="<?= $data[0]->voucher_number ?>" readonly>
                                    <input type="hidden" class="form-control" id="section_area" name="section_area" value="edit_advance_voucher" readonly>
                                    <!-- <input type="hidden" class="form-control" id="current_invoice_amount" name="current_invoice_amount" value="<?= $data[0]->invoice_total ?>" readonly> -->
                                    <input type="hidden" class="form-control" id="receipt_amount_old" name="receipt_amount_old" value="<?= $data[0]->receipt_amount ?>" readonly>
                                    <input type="hidden" name="total_sub_total" id="total_sub_total" value="<?= $data[0]->voucher_sub_total; ?>">
                                    <input type="hidden" name="total_tax_amount" id="total_tax_amount" value="<?= $data[0]->voucher_tax_amount; ?>">
                                    <input type="hidden" name="total_igst_amount" id="total_igst_amount" value="<?= $data[0]->voucher_igst_amount; ?>">
                                    <input type="hidden" name="total_cgst_amount" id="total_cgst_amount" value="<?= $data[0]->voucher_cgst_amount; ?>">
                                    <input type="hidden" name="total_sgst_amount" id="total_sgst_amount" value="<?= $data[0]->voucher_sgst_amount; ?>">
                                    <input type="hidden" name="total_grand_total" id="total_grand_total" value="<?= $data[0]->receipt_amount; ?>">
                                    <input type="hidden" name="table_data" id="table_data" value="<?php echo $sales; ?>">
                                    <!-- Hidden Field -->
                                    <table id="table-total" class="table table-striped table-bordered table-condensed table-hover">
                                        <tr>
                                            <td align="right">Total Value (+)</td>
                                            <td align='right'><span id="totalSubTotal"><?php echo $data[0]->voucher_sub_total; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td align="right">Total Tax (+)</td>
                                            <td align='right'><span id="totalTaxAmount"><?php echo $data[0]->voucher_tax_amount; ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">Grand Total (=)</td>
                                            <td align='right'><span id="totalGrandTotal"><?php echo $data[0]->receipt_amount; ?></span></td>
                                        </tr>
                                        <tr><td></td>
                                            <td align="right" >
                                                <span class="validation-color" id="err_amount_exceeds"></span>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="box-footer">
                                        <button type="submit" id="receipt_submit" class="btn btn-info">Update</button>
                                        <span class="btn btn-default" id="receipt_cancel" onclick="cancel('advance_voucher')">Cancel</span>
                                    </div>
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
$this->load->view('bank_account/bank_modal');
?>
            <script type="text/javascript">
                $(document).ready(function () {
                    if ($("#payment_mode").val() == "other payment mode") {
                        $("#other_payment").show();
                    }
                })
                var sales_data = <?php echo json_encode($sales_data); ?>;
                var branch_state_list = <?php echo json_encode($state); ?>;
                var item_gst = <?php echo json_encode($item_gst); ?>;
                var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
                var common_settings_tax_split = "<?= $access_common_settings[0]->tax_split_equaly ?>";
            </script>
            <script src="<?php echo base_url('assets/js/vouchers/') ?>advance.js"></script>
            <script src="<?php echo base_url('assets/js/vouchers/') ?>advance_basic.js"></script>
