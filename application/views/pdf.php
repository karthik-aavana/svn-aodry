<?php
$GLOBALS['common_settings_amount_precision'] = $access_common_settings[0]->amount_precision;
if(!function_exists('precise_amount')){
function precise_amount($val) {
    $val = (float) $val;
    // $amt =  round($val,$GLOBALS['common_settings_amount_precision']);
    $dat = number_format($val, $GLOBALS['common_settings_amount_precision'], '.', '');
    return $dat;
}
}
$convert_rate = 1;
if(@$converted_rate) $convert_rate = $converted_rate;
?>
<!DOCTYPE html>
<html>
    <head>
        <title> <?= (@$title ? $title : 'Sales Invoice'); ?> </title>
        <style type="text/css">            
            table b {
                font-size: 0.875em;
                font-weight: 600;
            }
            table {
                border-collapse: collapse;
                border-spacing: 0px;
                width: 100%;
                font-family: 'Mukta', sans-serif;
                line-height:10px;
                font-size: 0.8em;
            }
            .item-table tr th, .item-table tr td {
                border: 1px solid #444;
                padding: 4px;
            }
            .custom_table tr th {
                font-size: 0.8125em;
            }
            .custom_table tr td {
                white-space: nowrap;
                table-layout: auto;
                font-size: 0.7em;
            }
            #notices{
                padding-left: 6px;
                border-left: 6px solid #0087C3;  
            }			
            #notices .notice {
                font-size: 0.8125em;
            }
            .item-table thead, .item-table tbody {
                text-align: center;
            }
            .invoice-header {
                font-size: 100%;
            }
            .center {
                text-align: center;
            }
            .right {
                text-align: right;
            }
            .left {
                text-align: left;
            }
            .capitalize {
                text-transform: capitalize !important;
                font-weight: 600;
            }
            .uppercase {
                text-transform: uppercase !important;
            }
            .bold {
                font-weight: bold;
            }
            .p-5 {
                padding: 5px;
            }
            .mt-20 {
                margin-top: 20px;
            }
            .mt-50 {
                margin-top: 50px;
            }
            .h-5 {
                height: 5px;
            }
            .pad-rgt {
                padding: 10px 80px 10px 10px;
            }
            .border-0 {
                border: 0px !important;
            }
            .hr{
                border-bottom: 1px solid #444;
                margin: 8px 0;				
            }
            .spl-table{
                font-size: 1em
            }
            .dashed_table{
                font-size:0.7em;
                vertical-align: middle;  
                width: 85% !important;          	            	
            }            
            .dashed_table tr th, .dashed_table tr td{
                border: 1px dashed #333;            	
                padding: 4px;
            }            
            textarea{
                border: 0.5px solid #444;
            }           
            .bg-table{
                background: #EEEEEE;
                padding: 8px;
            } 
        </style>
    </head>
    <body>
        <span>
            <?php
            if (isset($branch[0]->firm_logo) && $branch[0]->firm_logo != "") {
                ?>
                <img class="img" src="<?php echo FCPATH . '/assets/branch_files/' . $branch[0]->branch_id . '/' . $branch[0]->firm_logo; ?>" height="30" />
                <?php
            } else {
                ?>
                <b class="uppercase">  <?= strtolower($branch[0]->firm_name) ?></b>
            <?php } ?>
        </span>
        <span style="float: right; margin-top: -25px"><h3><u><?= (@$title ? $title : 'Tax Invoice'); ?></u></h3>
           <br><p class="right"><small>(<?php echo $invoice_type; ?>)</small></p></span>
        <table class="table header_table mt-20" >
            <tr>
                <td valign="top">
                    <span class="capitalize">From,</span><br>
                    <div class="h-5">
                    </div>
                    <b class="capitalize"><?php
                        if (isset($branch[0]->branch_name)) {
                            echo strtolower($branch[0]->branch_name);
                        }
                        ?></b>
                    <br>
                    <?php
                    if (isset($branch[0]->branch_address)) {
                        echo str_replace(array(
                            "\r\n",
                            "\\r\\n",
                            "\n",
                            "\\n"), "<br>", $branch[0]->branch_address);
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_mobile)) {
                        if ($branch[0]->branch_mobile != "" || $branch[0]->branch_mobile != null) {
                            echo '<br>Mobile No : ' . $branch[0]->branch_mobile;
                        }
                        if (isset($branch[0]->branch_land_number)) {
                            if ($branch[0]->branch_land_number != "") {
                                echo ' | Landline No : ' . $branch[0]->branch_land_number;
                            }
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_email_address)) {
                        if ($branch[0]->branch_email_address != "") {
                            echo '<br>E-mail : ' . $branch[0]->branch_email_address;
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_gstin_number)) {
                        if ($branch[0]->branch_gstin_number != "" || $branch[0]->branch_gstin_number != null) {
                            echo "<br>GSTIN : " . $branch[0]->branch_gstin_number;
                        }
                        ?>
                    <?php } ?>
                    <?php
                    if (isset($branch[0]->branch_pan_number) && $branch[0]->branch_pan_number != "") {
                        if ($branch[0]->branch_pan_number != "" || $branch[0]->branch_pan_number != null) {
                            echo "<br />PAN : " . $branch[0]->branch_pan_number;
                        }
                        ?>
                    <?php } ?>
                    <?php
                    if (isset($branch[0]->branch_import_export_code_number)) {
                        if ($branch[0]->branch_import_export_code_number != "" || $branch[0]->branch_import_export_code_number != null) {
                            echo "| IEC : " . $branch[0]->branch_import_export_code_number;
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_lut_number)) {
                        if ($branch[0]->branch_lut_number != "" || $branch[0]->branch_lut_number != null) {
                            echo "<br />LUT : " . $branch[0]->branch_lut_number;
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_cin_number)) {
                        if ($branch[0]->branch_cin_number != "" || $branch[0]->branch_cin_number != null) {
                            echo " | CIN : " . $branch[0]->branch_cin_number;
                        }
                    }
                    ?>
                    <div class="h-5"></div><br>
                    <span class="capitalize">To,</span><br>
                    <div class="h-5">
                    </div>
                    <b class="capitalize"><?php
                        if (isset($data[0]->customer_name)) {
                            echo strtolower($data[0]->customer_name);
                        }
                        ?></b>
                    <?php
                    if (isset($data[0]->customer_gstin_number)) {
                        if ($data[0]->customer_gstin_number != "") {
                            echo "<br/>GSTIN : ";
                            echo $data[0]->customer_gstin_number;
                        }
                    }
                    ?>

                    <?php
                    if($data[0]->shipping_address_id != $data[0]->billing_address_id){
                        if(!empty($billing_address)){
                            echo "<br/>";
                            echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $billing_address[0]->shipping_address);
                        }elseif (isset($data[0]->customer_address) && $data[0]->customer_address != "") {
                            echo "<br/>";
                            echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $data[0]->customer_address);
                        } else {
                            echo "<br/>";
                            echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $data[0]->shipping_address);
                        }
                    }
                    ?>
                    <?php
                    if (isset($data[0]->customer_mobile)) {
                        if ($data[0]->customer_mobile != "") {
                            echo '<br>Mobile No : ' . $data[0]->customer_mobile;
                        }
                    }
                    ?>
                    <?php
                    if (isset($data[0]->customer_email)) {
                        if ($data[0]->customer_email != "") {
                            echo '<br>E-mail : ' . $data[0]->customer_email;
                        }
                    }
                    ?>
                </td>
                <td width="33%" valign="top" class="bg-table">
                    <span class="capitalize">Invoice Details,</span>
                    <div class="h-5">
                    </div>
                    Invoice Date : 
                    <?php
                    $date = $data[0]->sales_date;
                    $date_for = date('d-m-Y', strtotime($date));
                    ?>                  
                    <span class="bold"><?php
                        if (isset($data[0]->sales_date)) {
                            echo $date_for;
                        }
                        ?></span>
                    <br>
                    Invoice Number:                  
                    <span class="bold"><?php
                        if (isset($data[0]->sales_invoice_number)) {
                            echo $data[0]->sales_invoice_number;
                        }
                        ?></span>   
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
                    if(!empty($billing_address)){
                        if($billing_address[0]->country_name != '')$Country = ucfirst($billing_address[0]->country_name);
                    }
                    echo $Country; ?>
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
                        if (isset($data[0]->sales_gst_payable)) {
                            echo ucfirst($data[0]->sales_gst_payable);
                        }
                        ?>
                    </span>
                </td>   
                <?php 
                $is_transport = false;
                $is_shipment = false;
                $order_det = $data[0];
                if($order_det->transporter_name != '' || $order_det->transporter_gst_number != '' || $order_det->lr_no != '' || $order_det->vehicle_no != ''  ) $is_transport = true;

                if($order_det->mode_of_shipment != '' || $order_det->ship_by != '' || $order_det->net_weight != '' || $order_det->gross_weight != '' || $order_det->origin != '' || $order_det->destination != '' || $order_det->shipping_type != '' || $order_det->shipping_type_place != '' || $order_det->lead_time != '' || $order_det->warranty != '' || $order_det->payment_mode != '') $is_shipment = true;

                if($is_shipment == true || $is_transport == true){ 
                ?>             
                <td width="33%" valign="top" align="right" class="bg-table">
                    <?php if($is_shipment) { ?>
                        <span class="capitalize">Shipping Details,</span>
                        <br />
                        <div class="h-5"></div>
                        <?php if($order_det->mode_of_shipment != ''){ 
                            echo '<span>Mode of Shipment: '.$order_det->mode_of_shipment.' </span><br>';
                        } 
                        if($order_det->ship_by != ''){ 
                            echo '<span>Ship By : '.$order_det->ship_by.' </span>'; 
                        } 
                        if($order_det->ship_by != '' && $order_det->net_weight != '') echo " | ";
                        if($order_det->net_weight != ''){ 
                            echo '<span>Net Weight : '.$order_det->net_weight.' </span>';
                        }
                        if($order_det->ship_by != '' || $order_det->net_weight != '') echo "<br>";

                        if($order_det->gross_weight != ''){ 
                            echo '<span>Gross Weight : '.$order_det->gross_weight.' </span>'; 
                        } 
                        if($order_det->gross_weight != '' && $order_det->origin != '') echo " | ";
                        if($order_det->origin != ''){ 
                            echo '<span>Origin : '.$order_det->origin.' </span>';
                        }
                        if($order_det->gross_weight != '' || $order_det->origin != '') echo "<br>";
                      
                        if($order_det->mode_of_shipment != ''){ ?>                               
                        <?php echo '<span>Destination : '.$order_det->mode_of_shipment.'</span><br>';
                        ?>
                        <?php } 
                        if($order_det->shipping_type_place != ''){ 
                            echo '<span>Shipping Type Place : '.$order_det->shipping_type_place.'</span><br>';
                        }
                        if($order_det->lead_time != ''){ 
                            echo '<span>Lead Time : '.$order_det->lead_time.' </span>'; 
                        } 
                        if($order_det->lead_time != '' && $order_det->warranty != '') echo " | ";
                        if($order_det->warranty != ''){ 
                            echo '<span>Warranty : '.$order_det->warranty.' </span>';
                        }
                        if($order_det->lead_time != '' || $order_det->warranty != '') echo "<br>";
                        if($order_det->payment_mode != '') echo '<span>Payment Mode : '.$order_det->payment_mode.'</span><br>';
                    }

                    if($is_transport){ ?>
                        <div class="h-5"></div>
                        <span class="capitalize">Transporter Details,</span>
                        <br />
                        <div class="h-5"></div> 
                        <?php if($order_det->transporter_name != ''){                    
                            echo '<span>Name: '.$order_det->transporter_name.'</span><br>';
                        } 
                        if($order_det->transporter_gst_number != ''){
                            echo '<span>GST Number: '.$order_det->transporter_gst_number.'</span><br>';
                        } 
                        if($order_det->lr_no != ''){
                            echo '<span>LR Number: '.$order_det->lr_no.'</span><br>';
                        } 
                        if($order_det->vehicle_no != ''){
                         echo '<span>Vehicle Number: '.$order_det->vehicle_no.'</span><br>';
                        }
                    } ?>
                </td>
            <?php } ?>
            </tr>
        </table>
        <table class="item-table table mt-20 custom_table">
            <thead>
                <tr>
                    <th rowspan="2" >#</th>
                    <th rowspan="2">Particulars</th>
                    <th rowspan="2">Qty</th>
                    <th rowspan="2">Price</th>
                    <th rowspan="2">Subtotal</th>
                    <?php
                    if ($discount_exist > 0) {
                        ?>
                        <th rowspan="2" >Disc</th>
                    <?php } ?>
                    <?php
                    if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                        ?>
                        <th rowspan="2">Taxable Value</th>
                    <?php } ?>
                    <?php
                    if ($tds_exist > 0) {
                        ?>
                        <th colspan="2" >TCS</th>
                    <?php } ?>
                    <?php
                    if ($tax_exist > 0) {
                        ?>
                        <th colspan="2">Tax</th>
                    <?php } elseif ($igst_exist > 0) { ?>
                        <th colspan="2">IGST</th>
                    <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                        <th colspan="2">CGST</th>
                        <th colspan="2"><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></th>
                    <?php } ?>
                    <?php if ($cess_exist > 0) { ?>
                        <th colspan="2" style="text-align: center;">Cess</th>
                    <?php } ?>
                    <th rowspan="2">Total</th>
                </tr>
                <tr>
                    <?php
                    if ($tds_exist > 0) {
                        ?>
                        <th>Rate</th>
                        <th>Amt</th>
                    <?php } ?>
                    <?php
                    if ($tax_exist > 0) {
                        ?>
                        <th>Rate</th>
                        <th>Amt</th>
                    <?php } elseif ($igst_exist > 0) { ?>
                        <th>Rate</th>
                        <th>Amt</th>
                    <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                        <th>Rate</th>
                        <th>Amt</th>
                        <th>Rate</th>
                        <th>Amt</th>
                    <?php } ?>
                    <?php if ($cess_exist > 0) { ?>
                        <th  style="text-align: center;">Rate</th>
                        <th  style="text-align: center;">Amt</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $quantity = 0;
                $price = 0;
                $grand_total = 0;
                foreach ($items as $value) {
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <?php
                        if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {
                            ?>
                            <td style="text-align: left;">
                                <span class="capitalize"><?php echo $value->product_name; ?></span>                               
                                <br>HSN:<?php echo $value->product_hsn_sac_code; ?>
                                <?php
                                if (isset($value->sales_item_description) && $value->sales_item_description != "") {
                                    echo "<br/>";
                                    echo $value->sales_item_description;
                                }
                                ?>
                            </td>
                        <?php } else { ?>
                            <td style="text-align: left;"><?php echo $value->service_name; ?><br>SAC:<?php echo $value->service_hsn_sac_code; ?>
                                <?php
                                if (isset($value->sales_item_description) && $value->sales_item_description != "") {
                                    echo "<br/>";
                                    echo $value->sales_item_description;
                                }
                                ?>
                            </td>
                        <?php } ?>
                        <td><?php
                            echo $value->sales_item_quantity;
                            if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {
                                $unit = explode("-", $value->product_unit);
                                echo " " . $unit[0];
                            }
                            ?></td>
                        <td style="text-align: right;"><?php echo precise_amount(($value->sales_item_unit_price * $convert_rate)); ?></td>
                        <td style="text-align: right;"><?php echo precise_amount(($value->sales_item_sub_total * $convert_rate)); ?></td>

                        <?php
                        if ($discount_exist > 0) {
                            ?>
                            <td style="text-align: right;"><?php echo precise_amount(($value->sales_item_discount_amount * $convert_rate)); ?></td>
                        <?php } ?>
                        <?php
                        if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                            ?>
                            <td style="text-align: right;"><?php echo precise_amount(($value->sales_item_taxable_value * $convert_rate)); ?></td>
                        <?php } ?>
                        <?php
                        if ($tds_exist > 0) { ?>
                            <td><?=(strtolower($value->tds_module_type) == 'tcs' ? precise_amount($value->sales_item_tds_percentage) : '-' );?></td>
                            <td style="text-align: right;"><?=(strtolower($value->tds_module_type) == 'tcs' ? precise_amount($value->sales_item_tds_amount * $convert_rate) : '-'); ?></td>
                        <?php } ?>
                        <?php
                        if ($tax_exist > 0) {
                            ?>
                            <td><?=($value->sales_item_tax_percentage > 0 ? abs($value->sales_item_tax_percentage).'%' : '-'); ?></td>
                            <td style="text-align: right;"><?php echo precise_amount(($value->sales_item_tax_amount * $convert_rate)); ?></td>
                        <?php } elseif ($igst_exist > 0) { ?>
                            <td><?=($value->sales_item_igst_percentage > 0 ? abs($value->sales_item_igst_percentage).'%' : '-'); ?></td>
                            <td style="text-align: right;"><?=($value->sales_item_igst_amount > 0 ?precise_amount($value->sales_item_igst_amount * $convert_rate) : '-'); ?>
                                <?=($value->sales_item_igst_percentage > 0 ? "<br>".abs($value->sales_item_igst_percentage).'%' : ''); ?>
                            </td>
                        <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                            <td><?php echo precise_amount($value->sales_item_cgst_percentage); ?></td>
                            <td style="text-align: right;"><?php echo precise_amount(($value->sales_item_cgst_amount * $convert_rate)); ?></td>
                            <td><?php echo precise_amount($value->sales_item_sgst_percentage); ?></td>
                            <td style="text-align: right;"><?php echo precise_amount(($value->sales_item_sgst_amount * $convert_rate)); ?></td>
                        <?php } ?>
                        <?php if ($cess_exist > 0) { ?>
                            <td style="text-align: center;"><?= precise_amount($value->sales_item_tax_cess_percentage); ?></td>
                            <td style="text-align: right;"><?= precise_amount(($value->sales_item_tax_cess_amount * $convert_rate)); ?></td>
                        <?php } ?>
                        <td style="text-align: right;"><?php echo precise_amount(($value->sales_item_grand_total * $convert_rate)); ?></td>
                    </tr>
                    <?php
                    $i++;
                    $quantity = bcadd($quantity, $value->sales_item_quantity);
                    $price = bcadd($price, $value->sales_item_unit_price, 2);
                    $grand_total = bcadd($grand_total, $value->sales_item_grand_total, 2);
                }
                ?>
                <tr>
                    <th colspan="2"></th>
                    <th  ><?= $quantity ?></th>
                    <th  ><?= precise_amount(($price * $convert_rate)) ?></th>
                    <th  ><?= precise_amount(($data[0]->sales_sub_total * $convert_rate)) ?></th>
                    <?php
                    if ($discount_exist > 0) {
                        ?>
                        <th  ><?= precise_amount(($data[0]->sales_discount_amount * $convert_rate)) ?></th>
                    <?php } ?>
                    <?php
                    if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                        ?>
                        <th  ><?= precise_amount(($data[0]->sales_taxable_value * $convert_rate)) ?></th>
                    <?php } ?>
                    <?php
                    if ($tds_exist > 0) {
                        ?>
                        <!-- <th colspan="2" ><?= precise_amount((($data[0]->sales_tds_amount + $data[0]->sales_tcs_amount) * $convert_rate)) ?></th> -->
                        <th colspan="2" ><?= precise_amount((($data[0]->sales_tcs_amount) * $convert_rate)) ?></th>
                    <?php } ?>
                    <?php
                    if ($tax_exist > 0) {
                        ?>
                        <th colspan="2" ><?= precise_amount(($data[0]->sales_tax_amount * $convert_rate)) ?></th>
                    <?php } elseif ($igst_exist > 0) { ?>
                        <th colspan="2" ><?= precise_amount(($data[0]->sales_igst_amount * $convert_rate)) ?></th>
                    <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                        <th colspan="2" ><?= precise_amount(($data[0]->sales_cgst_amount * $convert_rate)) ?></th>
                        <th colspan="2" ><?= precise_amount(($data[0]->sales_sgst_amount * $convert_rate)) ?></th>
                    <?php } ?>
                    <?php if ($cess_exist > 0) { ?>
                        <th colspan="2" style="text-align: center;"><?= precise_amount(($data[0]->sales_tax_cess_amount * $convert_rate)) ?></th>
                    <?php } ?>
                    <th  ><?= bcadd($data[0]->sales_grand_total, $data[0]->round_off_amount, 2); ?></th>
                </tr>
            </tbody>
        </table>
        <table class="table mt-20">
            <tr>
                <td rowspan="7" width="65%"> 
                    <?php if($data[0]->total_other_amount > 0){ ?>
                    <table align="left" class="dashed_table mt-20">
                        <thead>
                            <tr>
                                <?php if ($data[0]->total_freight_charge > 0){ ?>
                                <th>Freight Charges</th>
                                <?php }
                                if ($data[0]->total_insurance_charge > 0){ ?>
                                <th>Insurance Charges</th>
                                <?php }
                                if ($data[0]->total_packing_charge > 0){ ?>
                                <th>Pack & Forward Charges</th>
                                <?php }
                                if ($data[0]->total_incidental_charge > 0){ ?>
                                <th>Incidental Charges</th>
                                <?php }
                                if ($data[0]->total_inclusion_other_charge > 0){ ?>
                                <th>Other Inclusive Charges</th>
                                <?php }
                                if ($data[0]->total_exclusion_other_charge > 0){ ?>
                                <th>Other Exclusive Charge</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php if ($data[0]->total_freight_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_freight_charge * $convert_rate)); ?> /-</th>
                                <?php }
                                if ($data[0]->total_insurance_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_insurance_charge * $convert_rate)); ?> /-</th>
                                <?php }
                                if ($data[0]->total_packing_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_packing_charge * $convert_rate)); ?> /-</th>
                                <?php }
                                if ($data[0]->total_incidental_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_incidental_charge * $convert_rate)); ?> /-</th>
                                <?php }
                                if ($data[0]->total_inclusion_other_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_inclusion_other_charge * $convert_rate)); ?> /-</th>
                                <?php }
                                if ($data[0]->total_exclusion_other_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_exclusion_other_charge * $convert_rate)); ?> /-</th>
                                <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                    <?php } ?>                  
                </td>  
                <td align="right"><b>Total Value</b></td>
                <td align="right"><?= precise_amount(($data[0]->sales_sub_total * $convert_rate)); ?></td>
            </tr>
            <?php if ($discount_exist > 0) { ?>
            <tr>
                <td align="right">Discount</td>
                <td align="right">(-) <?= precise_amount(($data[0]->sales_discount_amount * $convert_rate)); ?></td>
            </tr>
            <?php } ?>
            <?php if ($tds_exist > 0) { ?>
                <!-- <?php if ($data[0]->sales_tds_amount > 0) { ?> 
                    <tr>
                        <td align="right">TDS</td>
                        <td align="right"> (-) <?= precise_amount(($data[0]->sales_tds_amount * $convert_rate)); ?></td>
                    </tr>
                <?php } ?> -->
                <?php if ($data[0]->sales_tcs_amount > 0) { ?>
                    <tr>
                        <td align="right">TCS</td>
                        <td align="right">(+) <?= precise_amount(($data[0]->sales_tcs_amount * $convert_rate)); ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            <?php if ($igst_exist > 0) { ?>
            <tr>
                <td align="right">IGST</td>
                <td align="right">(+) <?= precise_amount(($data[0]->sales_igst_amount * $convert_rate)); ?></td>
            </tr>
            <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                <tr>
                    <td align="right"><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></td>
                    <td align="right">(+) <?= precise_amount(($data[0]->sales_sgst_amount * $convert_rate)); ?></td>
                </tr>
                <tr>
                    <td align="right">CGST</td>
                    <td align="right">(+) <?= precise_amount(($data[0]->sales_cgst_amount * $convert_rate)); ?></td>
                </tr>
            <?php } ?>
            <?php if ($cess_exist > 0) { ?>
            <tr>
                <td align="right">Cess</td>
                <td align="right">(+) <?= precise_amount(($data[0]->sales_tax_cess_amount * $convert_rate)); ?></td>
            </tr>	
            <?php } ?>
            <?php if ($data[0]->round_off_amount > 0) { ?>
                <tr>
                    <td align="right" ><b>Round Off (<?php echo '<span style="font-family: DejaVu Sans; sans-serif;">' . $data[0]->currency_code . '</span>'; ?>)</td>
                    <td align="right"><b>(-) <?php echo precise_amount(($data[0]->round_off_amount * $convert_rate)); ?></td>
                </tr>
            <?php } ?>  
            <?php
            if ($data[0]->round_off_amount < 0) {
                ?>
                <tr>
                    <td align="right" ><b>Round Off (<?php echo '<span style="font-family: DejaVu Sans; sans-serif;">' . $data[0]->currency_code . '</span>'; ?>)</td>
                    <td align="right"><b>(+) <?php echo precise_amount((abs($data[0] * $convert_rate)->round_off_amount)); ?></td>
                </tr>
            <?php } ?>            
            <tr>
                <td align="right"><b>Grand Total (<?php echo $data[0]->currency_code . '</span>'; ?>)</td>
                <td align="right"><b><?php echo precise_amount(($data[0]->sales_grand_total * $convert_rate)); ?>/-</td>
            </tr>
        </table>
        <div class="mt-50">
            <p>
                Amount (in Words): <b><?php echo $data[0]->currency_text . " " . $this->numbertowords->convert_number(precise_amount(($data[0]->sales_grand_total * $convert_rate))) . " Only"; ?> </b>
            </p>
        </div>
        <?php
        if (isset($data[0]->sales_type_of_supply) && $data[0]->sales_type_of_supply != "regular") {
            ?>        
            <div id="notices mt-50">
                <div>
                    NOTICE:
                </div>
                <div class="notice">
                    <?php
                    if (isset($data[0]->sales_type_of_supply) && $data[0]->sales_type_of_supply == "export_with_payment") {
                        ?>
                        <p><small> SUPPLY MEANT FOR EXPORT ON PAYMENT OF INTEGRATED TAX</small></p>
                    <?php } else if (isset($data[0]->sales_type_of_supply) && $data[0]->sales_type_of_supply == "export_without_payment") { ?>
                        <p><small>SUPPLY MEANT FOR EXPORT UNDER BOND OR LETTER OF UNDERTAKING WITHOUT PAYMENT OF INTEGRATED TAX</small></p>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php
        $notes_sub_module = 0;
        /*$this->load->view('note_template/display_note');*/
        ?>
        <!-- <?php
        if (isset($access_common_settings[0]->invoice_footer) && $access_common_settings[0]->invoice_footer != "") {
            ?>
            <table>
                <tr>
                    <td colspan="14" style="border:0px;text-align: center;font-size: 13px;"><?php echo $access_common_settings[0]->invoice_footer; ?></td>
                </tr>
            </table>
            <?php
        }
        ?>    -->  
        <?php if($data[0]->note1 != '' || $data[0]->note2 != '' ){?>   
        <table class="mt-50">
            <tbody>
                <tr>
                    <?php if($data[0]->note1 != '' ){ ?>
                    <td><textarea rows="3"><?=$data[0]->note1;?></textarea></td>
                    <?php } if($data[0]->note2 != '' ){ ?>
                    <td><textarea rows="3"><?=$data[0]->note2;?></textarea></td>
                <?php } ?>
                </tr>
            </tbody>
        </table>    
        <?php } ?>    
    </body>
</html>
