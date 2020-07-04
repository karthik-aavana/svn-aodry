<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');
if (!function_exists('precise_amount')) {
    $GLOBALS['common_settings_amount_precision'] = $access_common_settings[0]->amount_precision;

    function precise_amount($val) {
        $val = (float) $val;
        $dat = number_format($val, $GLOBALS['common_settings_amount_precision'], '.', '');
        return $dat;
    }
}
?>
<?php $inlet_id = $this->encryption_url->encode($data[0]->inlet_id); ?>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Inlet  Invoice</h3>
                        <a class="btn btn-sm btn-default pull-right back_button" id="cancel" onclick1="cancel('inlet')">Back</a>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <h2 class="page-header">
                                    <i class="fa fa-building-o"></i> <?= $branch[0]->firm_name ?>
                                    <small class="pull-right"> <?php
                                        $date = $data[0]->inlet_date;
                                        $c_date = date('d-m-Y', strtotime($date));
                                        echo "Date : " . $c_date;
                                        ?></small>
                                </h2>
                            </div>
                        </div>
                        <div class="row invoice-info">
                            <div class="col-sm-4">
                                From,
                                <address>
                                    <?php if (isset($data[0]->company_name) && $data[0]->company_name != "") { ?>
                                        <strong><?= $data[0]->company_name ?></strong>
                                    <?php } ?>
                                    <br/>
                                    <?php 
                                    if (!empty($data[0]->branch_address)) {
                                        echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $data[0]->branch_address);
                                    } 
                                    ?>
                                    <br>
                                    Mobile: <?php if (isset($data[0]->branch_mobile) && $data[0]->branch_mobile != "") { ?>
                                        <?= $data[0]->branch_mobile ?>
                                    <?php } ?>
                                    <br>
                                    Email: <?php if (isset($data[0]->branch_email_address) && $data[0]->branch_email_address != "") { ?>
                                        <?= $data[0]->branch_email_address ?>
                                        <br>
                                        <?php
                                    }
                                    if (isset($data[0]->branch_email_address) && $data[0]->branch_email_address != "") {
                                        ?>
                                        GSTIN: <?= $data[0]->branch_email_address ?>
                                    <?php } ?>
                                </address>
                                <br>
                                To,
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
                            </div>     
                            <?php
                            $is_transport = false;
                            $is_shipment = false;
                            $order_det = $data[0];
                            if ($order_det->transporter_name != '' || $order_det->transporter_gst_number != '' || $order_det->lr_no != '' || $order_det->vehicle_no != '')
                                $is_transport = true;
                            if ($order_det->mode_of_shipment != '' || $order_det->ship_by != '' || $order_det->net_weight != '' || $order_det->gross_weight != '' || $order_det->origin != '' || $order_det->destination != '' || $order_det->shipping_type != '' || $order_det->shipping_type_place != '' || $order_det->lead_time != '' || $order_det->warranty != '')
                                $is_shipment = true;
                            if ($is_shipment == false && $is_transport == false) { ?>
                                <div class="col-sm-4 pr-0"></div>
                            <?php } ?>

                            <div class="col-sm-4 pr-0">                                   
                                <div class="bg-light-table">
                                    <strong>Invoice Details</strong><br>
                                    Invoice Number: <?= $data[0]->inlet_invoice_number; ?><br>
                                    <?php
                                    $date = $data[0]->inlet_date;
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
                                    <br>                                   
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
                                        if (isset($data[0]->inlet_gst_payable)) {
                                            echo ucfirst($data[0]->inlet_gst_payable);
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
                            <form role="form" id="form" method="post" action="<?php echo base_url('inlet/received_stock'); ?>">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="9px"><input type="checkbox" name="check_all"></th>
                                            <th>Items</th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                            <th>Subtotal</th>
                                            <?php
                                            if ($discount_exist > 0) { ?>
                                                <th>Discount</th>
                                            <?php } ?>
                                            <?php
                                            if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
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
                                        
                                    </thead>
                                    <tbody id="inlet_table_body">
                                        <?php
                                        $i = 1;
                                        $quantity = 0;
                                        $price = 0;
                                        $grand_total = 0;
                                        $items_data = array();
                                        foreach ($items as $value) { ?>
                                            <tr>
                                                <td><input type="checkbox" name="check_items" value="<?=$value->item_id;?>" checked="checked"></td>
                                                
                                                <td style="text-align: left;">
                                                    <span class="capitalize"><?php echo strtoupper(strtolower($value->product_name)); ?></span>                               
                                                    <br>(HSN/SAC: <?php echo $value->product_hsn_sac_code; ?>)
                                                    <?php
                                                    if (isset($value->inlet_item_description) && $value->inlet_item_description != "") {
                                                        echo "<br/>";
                                                        echo $value->inlet_item_description;
                                                    }
                                                    ?><br>
                                                </td>
                                                
                                                <td style="text-align: center;">
                                                    <input type="text" name="inlet_item_quantity" value="<?=$value->inlet_item_quantity;?>" quantity="<?=$value->inlet_item_quantity;?>">
                                                    <input type="hidden" name="inlet_item_id" value="<?=$value->inlet_item_id;?>">
                                                    <input type="hidden" name="inlet_item_quantity" value="<?=$value->inlet_item_quantity;?>">
                                                    <input type="hidden" name="inlet_item_transferred_quantity" value="<?=$value->inlet_item_transferred_quantity;?>">
                                                </td>
                                                <td style="text-align: right;"><?php echo precise_amount(($value->inlet_item_unit_price)); ?></td>
                                                <td style="text-align: right;"><?php echo precise_amount(($value->inlet_item_sub_total)); ?></td>
                                                <?php
                                                if ($discount_exist > 0) { ?>
                                                    <td style="text-align: right;"><?php echo precise_amount(($value->inlet_item_discount_amount)); ?></td>
                                                <?php } ?>
                                                <?php
                                                if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                                                    ?>
                                                    <td style="text-align: right;"><?php echo precise_amount(($value->inlet_item_taxable_value)); ?></td>
                                                <?php } ?>
                                                <?php
                                                if ($tds_exist > 0) { ?>
                                                    <td style="text-align: right;">
                                                        
                                                    <?php if($value->inlet_item_tds_amount < 1){ echo '-'; }else{ ?><?=precise_amount($value->inlet_item_tds_amount); ?><br>(<?=round($value->inlet_item_tds_percentage,2);?>%)<?php } ?>
                                                        
                                                    </td>
                                                <?php } ?>
                                                <?php
                                                if ($tax_exist > 0) {
                                                    ?>
                                                    <td style="text-align: right;">
                                                        <?php if($value->inlet_item_tax_amount < 1){ echo '-'; }else{ ?>
                                                        <?php echo precise_amount(($value->inlet_item_tax_amount)); ?><br>(<?=($value->inlet_item_tax_percentage > 0 ? round(abs($value->inlet_item_tax_percentage),2).'%' : '-'); ?>)<?php } ?>
                                                    </td>
                                                <?php } elseif ($igst_exist > 0) { ?>
                                                    <td style="text-align: right;">
                                                        <?php if($value->inlet_item_igst_amount < 1){ echo '-'; }else{ ?>
                                                        <?=($value->inlet_item_igst_amount > 0 ?precise_amount($value->inlet_item_igst_amount) : '-'); ?>
                                                        <?=($value->inlet_item_igst_percentage > 0 ? "<br>(".round(abs($value->inlet_item_igst_percentage),2).'%' : ''); ?>) <?php }?>
                                                    </td>
                                                <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                                    <td style="text-align: right;">
                                                        <?php if($value->inlet_item_cgst_amount < 1){ echo '-'; }else{ ?>
                                                        <?php echo precise_amount(($value->inlet_item_cgst_amount)); ?><br>(<?php echo round($value->inlet_item_cgst_percentage,2); ?>%)<?php } ?></td>
                                                    <td style="text-align: right;">
                                                        <?php if($value->inlet_item_sgst_amount < 1){ echo '-'; }else{?><?php echo precise_amount(($value->inlet_item_sgst_amount)); ?><br>(<?php echo round($value->inlet_item_sgst_percentage,2); ?>%)<?php } ?></td>
                                                <?php } ?>
                                                <?php if ($cess_exist > 0) { ?>
                                                    <td style="text-align: right;">
                                                        <?php if($value->inlet_item_tax_cess_amount < 1){ echo '-'; }else{ ?><?= precise_amount(($value->inlet_item_tax_cess_amount)); ?><br>(<?= round($value->inlet_item_tax_cess_percentage,2); ?>%)<?php } ?></td>
                                                <?php } ?>
                                                <td style="text-align: right;"><?php echo precise_amount(($value->inlet_item_grand_total)); ?></td>
                                            </tr>   
                                            <?php
                                            $quantity += $value->inlet_item_quantity;
                                            $price = bcadd($price, $value->inlet_item_unit_price);

                                            $grand_total = bcadd($grand_total, $value->inlet_item_grand_total);
                                            $items_data['item_'.$value->item_id] = array('item_id' => $value->item_id,'inlet_item_id'=>$value->inlet_item_id,'quantity' => $value->inlet_item_quantity);
                                            $i++;
                                        }
                                        ?>
                                        <tr>
                                            <th colspan="2"></th>
                                            <th  style="text-align: right;"><!--<?= round($quantity,2) ?> --></th>
                                            <th  style="text-align: right;"><?= precise_amount(($price )) ?></th>
                                            <th  style="text-align: right;"><?= precise_amount(($data[0]->inlet_sub_total )) ?></th>
                                            <?php
                                            if ($discount_exist > 0) {
                                                ?>
                                                <th  style="text-align: right;"><?= precise_amount(($data[0]->inlet_discount_amount )) ?></th>
                                            <?php } ?>
                                            <?php
                                            if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                                                ?>
                                                <th  style="text-align: right;"><?= precise_amount(($data[0]->inlet_taxable_value )) ?></th>
                                            <?php } ?>
                                            <?php
                                            if ($tds_exist > 0) {  ?>
                                                
                                                <th style="text-align: right;"><?= precise_amount((($data[0]->inlet_tcs_amount) )) ?></th>
                                            <?php } ?>
                                            <?php
                                            if ($tax_exist > 0) {
                                                ?>
                                                <th style="text-align: right;"><?= precise_amount(($data[0]->inlet_tax_amount )) ?></th>
                                            <?php } elseif ($igst_exist > 0) { ?>
                                                <th style="text-align: right;"><?= precise_amount(($data[0]->inlet_igst_amount )) ?></th>
                                            <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                                <th style="text-align: right;"><?= precise_amount(($data[0]->inlet_cgst_amount )) ?></th>
                                                <th style="text-align: right;"><?= precise_amount(($data[0]->inlet_sgst_amount )) ?></th>
                                            <?php } ?>
                                            <?php if ($cess_exist > 0) { ?>
                                                <th style="text-align: right;"><?= precise_amount(($data[0]->inlet_tax_cess_amount )) ?></th>
                                            <?php } ?>
                                            <th  style="text-align: right;"><?= bcadd($data[0]->inlet_grand_total, $data[0]->round_off_amount, 2); ?></th>
                                        </tr>
                                    </tbody>
                                </table>

                                <table  id="table-total" class="table table-hover table-bordered table-responsive">
                                    <tbody>
                                        <tr>
                                            <td><b>Total Value (<?= $data[0]->currency_symbol; ?>)</b></td>
                                            <td><?= precise_amount($data[0]->inlet_sub_total); ?></td>
                                        </tr>
                                        <?php if ($discount_exist > 0) { ?>
                                            <tr>
                                                <td>Discount (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( - ) <?= precise_amount($data[0]->inlet_discount_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($tds_exist > 0) { ?>
                                            
                                            <?php if ($data[0]->inlet_tcs_amount > 0) { ?>                           
                                                <tr>
                                                    <td>TCS (<?= $data[0]->currency_symbol; ?>)</td>
                                                    <td style="color: red;">( + ) <?= precise_amount($data[0]->inlet_tcs_amount); ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if ($igst_exist > 0) { ?>
                                            <tr>
                                                <td>IGST (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->inlet_igst_amount); ?></td>
                                            </tr>
                                        <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                            <tr>
                                                <td>CGST (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + )<?= precise_amount($data[0]->inlet_cgst_amount); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?> (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->inlet_sgst_amount); ?></td>
                                            </tr>
                                        <?php } elseif ($tax_exist > 0) { ?>
                                            <tr>
                                                <td>TAX (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->inlet_tax_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($cess_exist > 0) { ?>
                                            <tr>
                                                <td>Cess (<?= $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->inlet_tax_cess_amount); ?></td>
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
                                            <td><b>Grand Total (<?= $data[0]->currency_symbol; ?>)</b></td>
                                            <td><b>( = ) <?= precise_amount($data[0]->inlet_grand_total); ?><b></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <input type="hidden" name="inlet_id" value="<?=$data[0]->inlet_id;?>">
                                <input type="hidden" name="outlet_id" value="<?=$data[0]->outlet_id;?>">
                                <input type="hidden" name="table_data" value="">
                            </form>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="box-footer">
                                            <button type="submit" id="received_stock" name="received_stock" class="btn btn-info">Received</button> <span class="btn btn-default" id="sale_cancel" onclick="cancel('sales')">Cancel</span>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($data[0]->note1 != '' || $data[0]->note2 != '') { ?>   
                                    <table class="mt-50" style=" border-collapse: separate; border-spacing:10px;">
                                        <tbody>
                                            <tr>
                                                <?php if ($data[0]->note1 != '') { 
                                                    $content1 = $data[0]->note1;
                                                    if(!empty($template1)){
                                                        foreach ($template1[0] as $key => $value) {
                                                            $content1 = $value->content;
                                                        }
                                                    }
                                                    
                                                    ?>
                                                    <td style="width: 300px; border: 1px solid #333; padding: 10px"><?= str_replace(array(
                                                    "\r\n",
                                                    "\\r\\n",
                                                    "\n",
                                                    "\\n"), "<br>", $content1); ?></td>
                                                <?php } if ($data[0]->note2 != '') { 
                                                    $content2 = $data[0]->note2;
                                                    if(!empty($template2)){
                                                        foreach ($template2[0] as $key => $value) {
                                                            $content2 = $value->content;
                                                        }
                                                    } ?>
                                                     <td style="width: 300px; border: 1px solid #333; padding: 10px"><?= str_replace(array(
                                                    "\r\n",
                                                    "\\r\\n",
                                                    "\n",
                                                    "\\n"), "<br>", $content2); ?></td>
                                                <?php } ?>
                                            </tr>
                                        </tbody>
                                    </table>  
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    var items_data = <?=json_encode($items_data);?>;
    $(document).ready(function(){
        $('[name=check_all]').on('change',function(){
            var is_checked = $(this).is(':checked');
            $('#inlet_table_body').find('[name=check_items]').each(function(){
                if(is_checked){
                    $(this).prop('checked',true).trigger('change');
                }else{
                    $(this).prop('checked',false).trigger('change');
                }
            })
        });

        $('#inlet_table_body').on('change','[name=check_items]',function(){
            var item_id = $(this).val();
            if($(this).is(':checked')){
                inlet_item_id = $(this).parents('tr').find('[name=inlet_item_id]').val();
                items_data['item_'+item_id] = {item_id:item_id,inlet_item_id:inlet_item_id,quantity:$(this).parents('tr').find('[name=inlet_item_quantity]').val()};
            }else{
                delete items_data['item_'+item_id];
                $('[name=check_all]').prop('checked',false);
            }
        });

        $(document).on('change','[name=inlet_item_quantity]',function(){
            var q = $(this).val();
            var h = $(this).attr('quantity');
            if(q > h){
                $(this).val(h);
                alert_d.text ='Quantity should not increase more than total quantity';
                PNotify.error(alert_d);
            }
        })

        $('#received_stock').click(function(){
            var kys = Object.keys(items_data);
            if(kys.length < 1){
                alert_d.text ='Please select items to transfer stocks';
                PNotify.error(alert_d);
                return false;
            }
            
            $('[name=table_data]').val(JSON.stringify(items_data));
            $('form#form').submit();
        })
    })
</script>
<?php

    $this->load->view('layout/footer');
    /*$this->load->view('inlet/compose_mail');*/
    $this->load->view('general/delete_modal');

?>