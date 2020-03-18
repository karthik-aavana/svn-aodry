<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if (!$is_only_view)
    $this->load->view('layout/header');
if (!function_exists('precise_amount')) {
    $GLOBALS['common_settings_amount_precision'] = $access_common_settings[0]->amount_precision;

    function precise_amount($val) {

        $val = (float) $val;

        // $amt =  round($val,$GLOBALS['common_settings_amount_precision']);

        $dat = number_format($val, $GLOBALS['common_settings_amount_precision'], '.', '');

        return $dat;
    }

}
?>
<?php $sales_id = $this->encryption_url->encode($data[0]->sales_id); ?>
<?php if (!$is_only_view) { ?>
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Sales  Invoice</h3>
                            <a class="btn btn-sm btn-info pull-right" href="<?php
                            echo base_url('receipt_voucher/add_sales_receipt/') . $sales_id;
                            ?>" title="Receive Now">Receive Now</a>
                            <a class="btn btn-sm btn-default pull-right back_button" id="cancel" onclick1="cancel('sales')">Back</a>
                        </div>
                    <?php } ?>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <h2 class="page-header">
                                    <i class="fa fa-building-o"></i> <?= $branch[0]->firm_name ?>
                                    <small class="pull-right"> <?php
                                        $date = $data[0]->sales_date;
                                        $c_date = date('d-m-Y', strtotime($date));
                                        echo "Date : " . $c_date;
                                        ?></small>
                                </h2>
                            </div>
                        </div>
                        <div class="row invoice-info">
                            <div class="col-sm-4">
                                From
                                <address>
                                    <strong><?= $branch[0]->firm_name ?></strong>
                                    <?php if (isset($branch[0]->firm_name) && $branch[0]->firm_name != "") { ?>
                                    <?php } ?>
                                    <br/>
                                    <?php if (isset($branch[0]->branch_address) && $branch[0]->branch_address != "") { ?>
                                        Address : <?php
                                        echo str_replace(array(
                                            "\r\n",
                                            "\\r\\n",
                                            "\n",
                                            "\\n"
                                                ), "<br>", $branch[0]->branch_address);
                                        ?>
                                    <?php } ?>
                                    <br/>
                                    Location :
                                    <?php
                                    if (isset($branch[0]->branch_city_name) && $branch[0]->branch_city_name != "") {

                                        if (isset($branch[0]->branch_city_name) && $branch[0]->branch_city_name != "") {
                                            echo $branch[0]->branch_city_name . ",";
                                        }
                                        if (isset($branch[0]->branch_state_name) && $branch[0]->branch_state_name != "") {
                                            echo $branch[0]->branch_state_name . ",";
                                        }
                                        if (isset($branch[0]->branch_country_name) && $branch[0]->branch_country_name != "") {
                                            echo $branch[0]->branch_country_name;
                                        }
                                    }
                                    ?>
                                    <br/>
                                    Mobile: <?php if (isset($branch[0]->branch_mobile) && $branch[0]->branch_mobile != "") { ?>
                                        <?= $branch[0]->branch_mobile ?>
                                    <?php } ?>
                                    <br>
                                    Email: <?php if (isset($branch[0]->branch_email_address) && $branch[0]->branch_email_address != "") { ?>
                                        <?= $branch[0]->branch_email_address ?>
                                        <?php
                                    }
                                    if (isset($branch[0]->branch_gstin_number) && $branch[0]->branch_gstin_number != "") {
                                        ?>
                                        <br>
                                        GSTIN: <?= $branch[0]->branch_gstin_number ?>
                                    <?php } ?>
                                </address>
                                <!--                            </div>
                                                            <div class="col-sm-4">-->
                                To
                                <address>
                                    <?php if (isset($data[0]->customer_name) && $data[0]->customer_name != "") { ?>
                                        <strong><?= $data[0]->customer_name ?></strong>
                                    <?php } ?>
                                    <br/>
                                    <?php 
                                    if (!empty($billing_address)) {
                                        echo "<br/>";
                                        echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $billing_address[0]->shipping_address);
                                    } elseif (isset($data[0]->customer_address) && $data[0]->customer_address != "") {
                                        echo "<br/>";
                                        echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $data[0]->customer_address);
                                    } else {
                                        echo "<br/>";
                                        echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $data[0]->shipping_address);
                                    }
                                    ?>
                                    <br>
                                    Mobile: <?php if (isset($data[0]->customer_mobile) && $data[0]->customer_mobile != "") { ?>
                                        <?= $data[0]->customer_mobile ?>
                                    <?php } ?>
                                    <br>
                                    Email: <?php if (isset($data[0]->customer_email) && $data[0]->customer_email != "") { ?>
                                        <?= $data[0]->customer_email ?>
                                        <br>
                                        <?php
                                    }
                                    if (isset($data[0]->customer_gstin_number) && $data[0]->customer_gstin_number != "") {
                                        ?>
                                        GSTIN: <?= $data[0]->customer_gstin_number ?>
                                    <?php } ?>
                                </address>
                            </div>     
                            <?php
                            $is_transport = false;
                            $is_shipment = false;
                            $order_det = $data[0];
                            if ($order_det->transporter_name != '' || $order_det->transporter_gst_number != '' || $order_det->lr_no != '' || $order_det->vehicle_no != '')
                                $is_transport = true;
                            if ($order_det->mode_of_shipment != '' || $order_det->ship_by != '' || $order_det->net_weight != '' || $order_det->gross_weight != '' || $order_det->origin != '' || $order_det->destination != '' || $order_det->shipping_type != '' || $order_det->shipping_type_place != '' || $order_det->lead_time != '' || $order_det->warranty != '' || $order_det->payment_mode != '')
                                $is_shipment = true;
                            if ($is_shipment == false && $is_transport == false) { ?>
                                <div class="col-sm-4 pr-0"></div>
                            <?php } ?>

                            <div class="col-sm-4 pr-0">                                   
                                <div class="bg-light-table">
                                    <strong>Invoice Details</strong><br>
                                    Invoice Number: <?= $data[0]->sales_invoice_number; ?><br>
                                    <?php
                                    $date = $data[0]->sales_date;
                                    $c_date = date('d-m-Y', strtotime($date));
                                    echo "Invoice Date : " . $c_date;
                                    ?>
                                    
                                    <?php
                                    if (isset($data[0]->place_of_supply)) {
                                        if ($data[0]->place_of_supply != "" || $data[0]->place_of_supply != null) {
                                            echo '<br>Place of Supply : ' . $data[0]->place_of_supply;
                                        }
                                    }
                                    if (isset($data[0]->shipping_address)) {
                                        if ($data[0]->shipping_address != "" || $data[0]->shipping_address != null) {
                                            echo '<br><br><b>Shipping Address</b><br>' . str_replace(array("\r\n", "\\r\\n", "\n", "\\n", "\\"), "", $data[0]->shipping_address);
                                        }
                                    }
                                    if (isset($data[0]->shipping_gstin)) {
                                        if ($data[0]->shipping_gstin != "" || $data[0]->shipping_gstin != null) {
                                            echo '<br>Shipping GSTIN : ' . $data[0]->shipping_gstin;
                                        }
                                    }
                                    ?> 
                                    <br>
                                    Billing Country : 
                                    <span><?php
                                        $Country = (isset($data[0]->billing_country) ? ucfirst($data[0]->billing_country) : '');
                                        if (!empty($billing_address)) {
                                            if ($billing_address[0]->country_name != '')
                                                $Country = ucfirst($billing_address[0]->country_name);
                                        }
                                        echo $Country;
                                        ?>
                                    </span>                                   
                                    Nature of Supply : 
                                    <span><?php
                                        if (isset($nature_of_supply)) {
                                            echo $nature_of_supply;
                                        }
                                        ?>
                                    </span>
                                    <br>
                                    GST Payable on Reverse Charge:
                                    <span><?php
                                        if (isset($data[0]->sales_gst_payable)) {
                                            echo ucfirst($data[0]->sales_gst_payable);
                                        }
                                        ?>
                                    </span>
                                </div> 
                            </div>
                            <?php
                            if ($is_shipment == true || $is_transport == true) {
                                ?> 
                            <div class="col-sm-4 pl-0">                                   
                                <div class="bg-light-table text-right">     
                                    <?php if ($is_shipment) { ?>
                                        <b class="capitalize">Shipping Details,</b>
                                        <br />                                              
                                        <?php
                                        if ($order_det->mode_of_shipment != '') {
                                            echo '<span>Mode of Shipment: ' . $order_det->mode_of_shipment . ' </span><br>';
                                        }
                                        if ($order_det->ship_by != '') {
                                            echo '<span>Ship By : ' . $order_det->ship_by . ' </span>';
                                        }
                                        if ($order_det->ship_by != '' && $order_det->net_weight != '')
                                            echo " | ";
                                        if ($order_det->net_weight != '') {
                                            echo '<span>Net Weight : ' . $order_det->net_weight . ' </span>';
                                        }
                                        if ($order_det->ship_by != '' || $order_det->net_weight != '')
                                            echo "<br>";

                                        if ($order_det->gross_weight != '') {
                                            echo '<span>Gross Weight : ' . $order_det->gross_weight . ' </span>';
                                        }
                                        if ($order_det->gross_weight != '' && $order_det->origin != '')
                                            echo " | ";
                                        if ($order_det->origin != '') {
                                            echo '<span>Origin : ' . $order_det->origin . ' </span>';
                                        }
                                        if ($order_det->gross_weight != '' || $order_det->origin != '')
                                            echo "<br>";
                                        if ($order_det->destination != '') {
                                            ?>                               
                                            <?php echo '<span>Destination : ' . $order_det->destination . '</span><br>';
                                            ?>
                                            <?php
                                        }
                                        if ($order_det->shipping_type_place != '') {
                                            echo '<span>Shipping Type Place : ' . $order_det->shipping_type_place . '</span><br>';
                                        }
                                        if ($order_det->lead_time != '') {
                                            echo '<span>Lead Time : ' . $order_det->lead_time . ' </span>';
                                        }
                                        if ($order_det->lead_time != '' && $order_det->warranty != '')
                                            echo " | ";
                                        if ($order_det->warranty != '') {
                                            echo '<span>Warranty : ' . $order_det->warranty . ' </span>';
                                        }
                                        if ($order_det->lead_time != '' || $order_det->warranty != '')
                                            echo "<br>";
                                        if ($order_det->payment_mode != '')
                                            echo '<span>Payment Mode : ' . $order_det->payment_mode . '</span><br><br>';
                                    }
                                    if ($is_transport) {
                                        ?>                                              
                                        <b class="capitalize">Transporter Details,</b>
                                        <br/>
                                        <?php
                                        if ($order_det->transporter_name != '') {
                                            echo '<span>Name: ' . $order_det->transporter_name . '</span><br>';
                                        }
                                        if ($order_det->transporter_gst_number != '') {
                                            echo '<span>GST Number: ' . $order_det->transporter_gst_number . '</span><br>';
                                        }
                                        if ($order_det->lr_no != '') {
                                            echo '<span>LR Number: ' . $order_det->lr_no . '</span><br>';
                                        }
                                        if ($order_det->vehicle_no != '') {
                                            echo '<span>Vehicle Number: ' . $order_det->vehicle_no . '</span><br>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div> 
                            <?php } ?>
                        </div>                        
                      
                        
                        <div class="row">
                            <div class="col-sm-12 table-responsive mt-15">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="9px">#</th>
                                            <th>Items</th>
                                            <th>Quantity</th>
                                            <th>Free Quantity</th>
                                            <th>MRP</th>
                                            <th>Rate</th>
                                            <th>Subtotal</th>
                                            <?php
                                            if ($discount_exist > 0) {
                                                ?>
                                                <th>Trade Discount</th>
                                            <?php } 
                                            if($schemediscountExist > 0){
                                            ?>
                                            <th>Scheme Discount</th>
                                            <?php
                                            }
                                            if ($discount_exist > 0 && $schemediscountExist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                                                ?>
                                                <th>Taxable Value</th>
                                            <?php } ?>
                                            <?php
                                            if ($tds_exist > 0) {
                                                ?>
                                                <th>TCS</th>
                                            <?php } ?>
                                            <?php
                                            if ($tax_exist > 0) {
                                                ?>
                                                <th>Tax</th>
                                            <?php } elseif ($igst_exist > 0) { ?>
                                                <th>IGST</th>
                                            <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                                <th>CGST</th>
                                                <th><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></th>
                                            <?php } ?>
                                            <?php if ($cess_exist > 0) { ?>
                                                <th style="text-align: center;">CESS</th>
                                            <?php } ?>
                                            <th>Total</th>
                                        </tr>
                                        <!-- <tr>
                                            <?php if ($tds_exist > 0) { ?>
                                                <th>Rate(%)</th>
                                                <th>Amount</th>
                                            <?php } ?>
                                            <?php if ($tax_exist > 0) { ?>
                                                <th>Rate(%)</th>
                                                <th>Amount</th>
                                            <?php } elseif ($igst_exist > 0) { ?>
                                                <th>Rate(%)</th>
                                                <th>Amount</th>
                                            <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                                <th>Rate(%)</th>
                                                <th>Amount</th>
                                                <th>Rate(%)</th>
                                                <th>Amount</th>
                                            <?php } ?>
                                            <?php if ($cess_exist > 0) { ?>
                                                <th>Rate(%)</th>
                                                <th>Amount</th>
                                            <?php } ?>
                                        </tr> -->
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $quantity = 0;
                                        $price = 0;
                                        $grand_total = 0;
                                        $discount_schme = 0;
                                        $discount_trade = 0;
                                        foreach ($items as $value) {
                                            $discount_schme += $value->sales_item_scheme_discount_amount;
                                            $discount_trade += $value->sales_item_discount_amount;
                                            ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <?php
                                                if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {
                                                    ?>
                                                    <td style="text-align: left;">
                                                        <span class="capitalize"><?php echo strtoupper(strtolower($value->product_name)); ?></span>                               
                                                        <br>(HSN/SAC: <?php echo $value->product_hsn_sac_code; ?>)
                                                        <?php
                                                        if (isset($value->sales_item_description) && $value->sales_item_description != "") {
                                                            echo "<br/>";
                                                            echo $value->sales_item_description;
                                                        }
                                                        ?><br><?php echo $value->product_batch; ?>
                                                    </td>
                                                <?php } else { ?>
                                                    <td style="text-align: left;"><?php echo strtoupper(strtolower($value->service_name)); ?><br>(HSN/SAC: <?php echo $value->service_hsn_sac_code; ?>)
                                                        <?php
                                                        if (isset($value->sales_item_description) && $value->sales_item_description != "") {
                                                            echo "<br/>";
                                                            echo $value->sales_item_description;
                                                        }
                                                        ?>
                                                    </td>
                                                <?php } ?>

                                                <td style="text-align: center;"><?php
                                                    echo $value->sales_item_quantity;
                                                    if ($value->product_unit_sales != '') {
                                                        $unit = explode("-", $value->product_unit_sales);
                                                        if($unit[0] != '') echo " <br>(" . $unit[0].')';
                                                    }
                                                    ?></td>
                                                    <td style="text-align: center;"><?php echo $value->sales_item_free_quantity;
                                                    if ($value->product_unit_sales != '') {
                                                        $unit = explode("-", $value->product_unit_sales);
                                                        if($unit[0] != '') echo " <br>(" . $unit[0].')';
                                                    } ?></td>
                                                    <td style="text-align: center;"><?php echo precise_amount(($value->sales_item_mrp_price)); ?></td>
                                                <td style="text-align: right;"><?php echo precise_amount(($value->sales_item_unit_price)); ?></td>
                                                <td style="text-align: right;"><?php echo precise_amount(($value->sales_item_sub_total)); ?></td>
                                                <?php
                                                if ($discount_exist > 0) {
                                                    ?>
                                                    <td style="text-align: right;"><?php echo precise_amount(($value->sales_item_discount_amount)); ?></td>
                                                <?php } 
                                                if($schemediscountExist > 0){
                                                ?>
                                                 
                                                <td style="text-align: center;"><?php echo precise_amount(($value->sales_item_scheme_discount_amount)); ?></td>
                                                <?php
                                                }
                                            if ($discount_exist > 0 && $schemediscountExist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                                                    ?>
                                                    <td style="text-align: right;"><?php echo precise_amount(($value->sales_item_taxable_value)); ?></td>
                                                <?php } ?>
                                               
                                                <?php
                                                if ($tds_exist > 0) { ?>
                                                    <td style="text-align: right;">
                                                        <?php if($value->item_type == 'product' || $value->item_type == 'product_inventory'){ ?>
                                                         <?php if($value->sales_item_tds_amount < 1){ echo '-'; }else{ ?><?=precise_amount($value->sales_item_tds_amount); ?><br>(<?=round($value->sales_item_tds_percentage,2);?>%)<?php } ?>
                                                        <?php }else{ 
                                                            echo '-';
                                                        } ?>
                                                    </td>
                                                <?php } ?>
                                                <?php
                                                if ($tax_exist > 0) {
                                                    ?>
                                                    <td style="text-align: right;">
                                                         <?php if($value->sales_item_tax_amount < 1){ echo '-'; }else{ ?>
                                                         <?php echo precise_amount(($value->sales_item_tax_amount)); ?><br>(<?=($value->sales_item_tax_percentage > 0 ? round(abs($value->sales_item_tax_percentage),2).'%' : '-'); ?>)<?php } ?></td>
                                                <?php } elseif ($igst_exist > 0) { ?>
                                                    <td style="text-align: right;">
                                                         <?php if($value->sales_item_igst_amount < 1){ echo '-'; }else{ ?>
                                                        <?=($value->sales_item_igst_amount > 0 ?precise_amount($value->sales_item_igst_amount) : '-'); ?>
                                                        <?=($value->sales_item_igst_percentage > 0 ? "<br>(".round(abs($value->sales_item_igst_percentage),2).'%' : ''); ?>) <?php }?>
                                                    </td>
                                                <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                                    <td style="text-align: right;">
                                                        <?php if($value->sales_item_cgst_amount < 1){ echo '-'; }else{ ?>
                                                        <?php echo precise_amount(($value->sales_item_cgst_amount)); ?><br>(<?php echo round($value->sales_item_cgst_percentage,2); ?>%)<?php } ?></td>
                                                    <td style="text-align: right;">
                                                        <?php if($value->sales_item_sgst_amount < 1){ echo '-'; }else{?><?php echo precise_amount(($value->sales_item_sgst_amount)); ?><br>(<?php echo round($value->sales_item_sgst_percentage,2); ?>%)<?php } ?></td>
                                                <?php } ?>
                                                <?php if ($cess_exist > 0) { ?>
                                                    <td style="text-align: right;">
                                                        <?php if($value->sales_item_tax_cess_amount < 1){ echo '-'; }else{ ?><?= precise_amount(($value->sales_item_tax_cess_amount)); ?><br>(<?= round($value->sales_item_tax_cess_percentage,2); ?>%)<?php } ?></td>
                                                <?php } ?>
                                                <td style="text-align: right;"><?php echo precise_amount(($value->sales_item_grand_total)); ?></td>
                                            </tr>   
                                            <?php
                                            $i++;
                                            $quantity += $value->sales_item_quantity;
                                            $price = bcadd($price, $value->sales_item_unit_price);

                                            $grand_total = bcadd($grand_total, $value->sales_item_grand_total);
                                        }
                                        ?>
                                        <tr>
                                            <th colspan="4"></th>
                                            <th  style="text-align: right;"><!--<?= round($quantity,2) ?> --></th>
                                            <th  style="text-align: right;"><?= precise_amount(($price )) ?></th>
                                            
                                            <th  style="text-align: right;"><?= precise_amount(($data[0]->sales_sub_total )) ?></th>
                                            <?php
                                            if ($discount_exist > 0) {
                                                ?>
                                                <th  style="text-align: right;"><?= precise_amount(($discount_trade )) ?></th>
                                            <?php } 

                                            if($schemediscountExist > 0){
                                            ?>
                                            <td style="text-align: center;"><?= precise_amount(($discount_schme )) ?></td>
                                            <?php
                                            }
                                            if ($discount_exist > 0 && $schemediscountExist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                                                ?>
                                                <th  style="text-align: right;"><?= precise_amount(($data[0]->sales_taxable_value )) ?></th>
                                            <?php } ?>

                                            <?php
                                            if ($tds_exist > 0) {
                                                ?>
                                                <!-- <th colspan="2" ><?= precise_amount((($data[0]->sales_tds_amount + $data[0]->sales_tcs_amount) )) ?></th> -->
                                                <th style="text-align: right;"><?= precise_amount((($data[0]->sales_tcs_amount) )) ?></th>
                                            <?php } ?>
                                            <?php
                                            if ($tax_exist > 0) {
                                                ?>
                                                <th style="text-align: right;"><?= precise_amount(($data[0]->sales_tax_amount )) ?></th>
                                            <?php } elseif ($igst_exist > 0) { ?>
                                                <th style="text-align: right;"><?= precise_amount(($data[0]->sales_igst_amount )) ?></th>
                                            <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                                <th style="text-align: right;"><?= precise_amount(($data[0]->sales_cgst_amount )) ?></th>
                                                <th style="text-align: right;"><?= precise_amount(($data[0]->sales_sgst_amount )) ?></th>
                                            <?php } ?>
                                            <?php if ($cess_exist > 0) { ?>
                                                <th style="text-align: right;"><?= precise_amount(($data[0]->sales_tax_cess_amount )) ?></th>
                                            <?php } ?>
                                            <th  style="text-align: right;"><?= $grand_total; ?></th>
                                        </tr>
                                    </tbody>
                                </table>
                                <table  id="table-total" class="table table-hover table-bordered table-responsive">
                                    <tbody>
                                        <tr>
                                            <td><b>Total Value (<?= $data[0]->currency_symbol; ?>)</b></td>
                                            <td><?= precise_amount($data[0]->sales_sub_total); ?></td>
                                        </tr>
                                        <?php if ($discount_exist > 0) { ?>
                                            <tr>
                                                <td>Discount (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( - ) <?= precise_amount($data[0]->sales_discount_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($tds_exist > 0) { ?>
                                            <?php if ($data[0]->sales_tds_amount > 0) { ?>  
                                                <tr>
                                                    <td>TDS (<?= $data[0]->currency_symbol; ?>)</td>
                                                    <td style="color: black;"> <?= precise_amount($data[0]->sales_tds_amount); ?></td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($data[0]->sales_tcs_amount > 0) { ?>                           
                                                <tr>
                                                    <td>TCS (<?= $data[0]->currency_symbol; ?>)</td>
                                                    <td style="color: red;">( + ) <?= precise_amount($data[0]->sales_tcs_amount); ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if ($igst_exist > 0) { ?>
                                            <tr>
                                                <td>IGST (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->sales_igst_amount); ?></td>
                                            </tr>
                                        <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                            <tr>
                                                <td>CGST (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + )<?= precise_amount($data[0]->sales_cgst_amount); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?> (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->sales_sgst_amount); ?></td>
                                            </tr>
                                        <?php } elseif ($tax_exist > 0) { ?>
                                            <tr>
                                                <td>TAX (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->sales_tax_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($cess_exist > 0) { ?>
                                            <tr>
                                                <td>Cess (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->sales_tax_cess_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($data[0]->total_freight_charge > 0) { ?>
                                            <tr>
                                                <td>Freight Charge (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->total_freight_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_insurance_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Insurance Charge (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->total_insurance_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_packing_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Packing & Forwarding Charge (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->total_packing_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_incidental_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Incidental Charge (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->total_incidental_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_inclusion_other_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Other Inclusive Charge (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->total_inclusion_other_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_exclusion_other_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Other Exclusive Charge (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: green;">( - ) <?= precise_amount($data[0]->total_exclusion_other_charge); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($data[0]->round_off_amount > 0) { ?>
                                            <tr>
                                                <td>Round Off (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: green;">( - ) <?= precise_amount($data[0]->round_off_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($data[0]->round_off_amount < 0) { ?>
                                            <tr>
                                                <td>Round Off (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->round_off_amount * -1); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <td>Cash Discount</td>
                                            <td style="color: green;">(-) <?= precise_amount($data[0]->sales_cash_discount); ?></td>
                                        <tr>
                                            <td><b>Grand Total (<?= $data[0]->currency_symbol; ?>)</b></td>
                                            <td><b>( = ) <?= precise_amount($data[0]->sales_grand_total); ?><b></td>
                                                        </tr>
                                                        </tbody>
                                                        </table>
                                                        </div>
                                                        <?php if (!$is_only_view) { ?>
                                                            <div class="col-sm-12">
                                                                <div class="buttons">
                                                                    <div class="btn-group btn-group-justified">
                                                                        <?php if (in_array($sales_module_id, $active_view)) { ?>
                                                                            <div class="btn-group">
                                                                                <a class="tip btn btn-success pdf_button" data-backdrop="static" data-keyboard="false" href1="<?= base_url('sales/pdf/') . $sales_id; ?>" data-toggle="modal"  class="btn btn-app pdf_button" b_curr="<?= $currency_id; ?>"  b_code="<?= $currency_code; ?>" c_code="<?= $cust_currency_code ?>" c_curr="<?= $data[0]->currency_id; ?>" data-id="<?php echo $sales_id; ?>" data-name="regular" data-target="#pdf_type_modal" href="#" title="Download PDF" title="Download as PDF" >
                                                                                    <i class="fa fa-download"></i>
                                                                                    <span class="hidden-sm hidden-xs">PDF</span></a>
                                                                            </div>
                                                                        <?php } ?>
                                                                        <?php
                                                                        if (in_array($sales_module_id, $active_edit)) {
                                                                            if ($data[0]->sales_paid_amount == 0) {
                                                                                ?>
                                                                                <div class="btn-group">
                                                                                    <a class="tip btn btn-warning tip" href="<?= base_url('sales/edit/') . $sales_id; ?>" title="Edit">
                                                                                        <i class="fa fa-pencil"></i>
                                                                                        <span class="hidden-sm hidden-xs">Edit</span>
                                                                                    </a>
                                                                                </div>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <?php
                                                                        if (in_array($email_module_id, $active_view)) {
                                                                            if (in_array($email_sub_module_id, $access_sub_modules)) {
                                                                                ?>
                                                                                <div class="btn-group">
                                                                                    <button type="button" class="btn btn-info composeMail" data-toggle="modal" data-target="#composeMail"  data-id="<?php echo $sales_id; ?>"> <i class="fa fa-envelope"></i> <span class="hidden-sm hidden-xs"> Email</span></button>
                                                                                   <!-- <a class="tip btn btn-warning tip" href="<?= base_url('sales/email/') . $sales_id; ?>" title="Send Mail">
                                                                                   <i class="fa fa-envelope"></i>
                                                                                   <span class="hidden-sm hidden-xs">Email</span>
                                                                                   </a> -->
                                                                                </div>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <?php if (in_array($sales_module_id, $active_delete) && $data[0]->sales_paid_amount == 0) { ?>
                                                                            <div class="btn-group">
                                                                                <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="<?= $sales_id; ?>" data-path="sales/delete" class="delete_button tip btn btn-danger bpo" href="#" title="Delete sales">
                                                                                    <i class="fa fa-trash-o"></i>
                                                                                    <span class="hidden-sm hidden-xs">Delete</span>
                                                                                </a>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        </div>
                                                        </div>
                                                        <?php if (!$is_only_view) { ?>
                                                            </div>
                                                            </div>
                                                            </div>
                                                            </section>
                                                            </div>
                                                        <?php } ?>
                                                        <?php
                                                        if (!$is_only_view) {
                                                            $this->load->view('layout/footer');
                                                            $this->load->view('sales/pdf_type_modal');
                                                            $this->load->view('sales/compose_mail');
                                                            $this->load->view('general/delete_modal');
                                                        }
                                                        ?>