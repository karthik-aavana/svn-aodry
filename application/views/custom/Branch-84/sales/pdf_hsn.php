<?php
$GLOBALS['common_settings_amount_precision'] = $access_common_settings[0]->amount_precision;
if (!function_exists('precise_amount')) {

    function precise_amount($val) {
        $val = (float) $val;
        // $amt =  round($val,$GLOBALS['common_settings_amount_precision']);
        $dat = number_format($val, $GLOBALS['common_settings_amount_precision'], '.', '');
        return $dat;
    }

}
$convert_rate = 1;
if (@$converted_rate)
    $convert_rate = $converted_rate;
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <style type="text/css">    
            .custom-page-start {
                margin-top: 25px;
            }           
            .total_table th, .total_table tr td{
                padding: 40px;    
                border: 1px solid #444;            
            }             
        </style>
        <style><?php $this->load->view('common/common_pdf'); ?></style>
    </head>
    <body>
        <span style="float: right; margin-top: -25px"><small style="font-size: 8px;line-height: 2"><?=($invoice_type != '' ? '('.$invoice_type.')' : ''); ?></small></h3>
           <br><p class="right"></p></span>
        
            <table class="item-table-th item-table-td table mt-20" style="min-height: 300px">
                <tr>
                    <th colspan="5" style="text-align: left;">Date : <?php
                                $date = $data[0]->sales_date;
                                $date_for = date('d-m-Y', strtotime($date));
                                echo $date_for;
                                ?> </th>
                <th colspan="6" style="text-align:center;">TAX INVOICE</th>
                <th colspan="5" style="text-align: right;">Bill No : <?php
                                if (isset($data[0]->sales_brand_invoice_number) && $data[0]->sales_brand_invoice_number != '') {
                                    echo $data[0]->sales_brand_invoice_number;
                                }else{
                                    echo $data[0]->sales_invoice_number;
                                }
                                ?> 
                </th>
            </tr>
            <tr>
                <td colspan="6" style="text-align: left;">
                    <table class="table" style="font-size:13px;">
                        <!-- <tr><td colspan="2"><b> From,</b></td></tr> -->
                        <tr><td style="width:35%"><b>From : </b></td>
                            <td class="uppercase"><?= strtolower($branch[0]->firm_name) ?></td>
                        </tr>
                        <tr><td><b>Address : </b></td>
                            <td style="text-align: left;"><?php
                                if (isset($branch[0]->branch_address)) {
                                    echo str_replace(array(
                                        "\r\n",
                                        "\\r\\n",
                                        "\n",
                                        "\\n"), "<br>", $branch[0]->branch_address);
                                }
                                ?>
                            </td>
                        </tr>
                        <tr><td><b>Contact No : </b></td>
                            <td><?php
                                if (isset($data[0]->branch_land_number)) {
                                    if ($data[0]->branch_land_number != "") {
                                        echo $data[0]->branch_land_number;
                                    }
                                }
                                ?></td>
                        </tr>
                        <!-- <tr><td>State code : </td>
                            <td><?php
                                if (isset($branch[0]->state_short_code)) {
                                    if ($branch[0]->state_short_code != "") {
                                        echo $branch[0]->state_short_code;
                                    }
                                }
                                ?></td>
                        </tr> -->
                        <!-- <tr><td><b>PAN :</b> </td>
                            <td><?php
                                if (isset($branch[0]->branch_pan_number)) {
                                    if ($branch[0]->branch_pan_number != "" || $branch[0]->branch_pan_number != null) {
                                        echo $branch[0]->branch_pan_number;
                                    }
                                    ?>
                                <?php } ?>
                            </td>
                        </tr> -->
                        <tr><td><b>GSTIN :</b> </td>
                            <td><?php
                                if (isset($branch[0]->branch_gstin_number)) {
                                    if ($branch[0]->branch_gstin_number != "" || $branch[0]->branch_gstin_number != null) {
                                        echo $branch[0]->branch_gstin_number;
                                    }
                                    ?>
                                <?php } ?></td>
                        </tr>
                        <tr><td><b>Drug Licence# :</b> </td>
                            <td><?=(@$branch[0]->drug_licence_no_1 && $branch[0]->drug_licence_no_1 != '' ? $branch[0]->drug_licence_no_1 : '')?></td>
                        </tr>
                        <tr><td><b>Food Licence# :</b> </td>
                            <td><?=(@$branch[0]->drug_licence_no_2 && $branch[0]->drug_licence_no_2 != '' ? $branch[0]->drug_licence_no_2 : '')?></td>
                        </tr>
                    </table>
                </td>
                <td colspan="6" style="text-align: left;">
                    <table class="table" style="font-size:13px;">
                        <!-- <tr>
                            <td style="width:45%">Bill No : </td>
                            <td class="uppercase" style="text-align: left;">
                                <?php
                                if (isset($data[0]->sales_invoice_number)) {
                                    echo $data[0]->sales_invoice_number;
                                }
                                ?>                            
                            </td>
                        </tr>
                        <tr>
                            <td style="width:33%">Date : </td>
                            <td class="uppercase" style="text-align: left;">
                                <?php
                                $date = $data[0]->sales_date;
                                $date_for = date('d-m-Y', strtotime($date));
                                echo $date_for;
                                ?>                            
                            </td>
                        </tr> -->
                        <tr>
                            <td style="width:35%"><b>Route : </b></td>
                            <td class="uppercase" style="text-align: left;">
                            </td>
                        </tr>
                        <tr>
                            <td style="width:35%"><b>Salesman : </b></td>
                            <td class="uppercase" style="text-align: left;">
                            </td>
                        </tr>
                    </table>
                </td>
                <td colspan="4" style="text-align: left;">
                    <table class="table" style="font-size:13px;">
                        <!-- <tr><td colspan="2"><b> BILL TO,</b></td></tr> -->
                        <tr><td style="width:32%"><b>Bill to : </b></td>
                            <td class="uppercase" style="text-align: left;"><?= strtolower($data[0]->customer_name) ?></td>
                        </tr>
                        <tr><td><b>Address : </b></td>
                            <td style="text-align: left;"><?php
                                /* if ($data[0]->shipping_address_id != $data[0]->billing_address_id) { */
                                if (!empty($billing_address)) {
                                    echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $billing_address[0]->shipping_address);
                                } elseif (isset($data[0]->customer_address) && $data[0]->customer_address != "") {
                                    echo "<br/>";
                                    echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $data[0]->customer_address);
                                } else {
                                    echo "<br/>";
                                    echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $data[0]->shipping_address);
                                }
                                /* } */
                                ?></td>
                        </tr>
                        <tr><td><b>Contact No : </b></td>
                            <td style="text-align: left;"><?php
                                if (isset($data[0]->customer_mobile)) {
                                    if ($data[0]->customer_mobile != "") {
                                        echo $data[0]->customer_mobile;
                                    }
                                }
                                ?></td>
                        </tr>
                        <!-- <tr><td>State code : </td>
                            <td style="text-align: left;"><?php
                                if (isset($data[0]->state_short_code)) {
                                    if ($data[0]->state_short_code != "") {
                                        echo $data[0]->state_short_code;
                                    }
                                }
                                ?></td>
                        </tr> -->
                        <!-- <tr><td><b>PAN : </b></td>
                            <td style="text-align: left;"><?php
                                if (isset($data[0]->customer_pan_number)) {
                                    if ($data[0]->customer_pan_number != "" || $data[0]->customer_pan_number != null) {
                                        echo $data[0]->customer_pan_number;
                                    }
                                    ?>
                                <?php } ?>

                            </td>
                        </tr> -->
                        <tr><td><b>GSTIN : </b></td>
                            <td style="text-align: left;"><?php
                                if (isset($data[0]->customer_gstin_number)) {
                                    if ($data[0]->customer_gstin_number != "") {
                                        echo $data[0]->customer_gstin_number;
                                    }
                                }
                                ?></td>
                        </tr>
                        <tr><td><b>Drug Licence# : </b></td>
                            <td style="text-align: left;"><?=(@$data[0]->drug_licence_no && $data[0]->drug_licence_no != '' ? str_replace(',','<br>',$data[0]->drug_licence_no) : '')?></td>
                        </tr>
                        <tr><td><b>Food Licence# : </b></td>
                            <td style="text-align: left;"><?=(@$data[0]->food_licence_number && $data[0]->food_licence_number != '' ? str_replace(',','<br>',$data[0]->food_licence_number) : '')?></td>
                        </tr>
                    </table>                        
                </td>
            </tr>
        </table>
        <table class="item-table table_hsn mt-5"  style="font-size:11px; margin-left: 0px; padding-left: 0px;">
                
                 
                <thead style="display: table-header-group;"> 
                    <tr >

                        <th rowspan="2">Sl<br>No</th>
                        <th rowspan="2">Product Name</th>
                        <th rowspan="2">HSN Code</th>
                        <th rowspan="2">Batch</th>
                        <th rowspan="2">MRP</th>
                        <th rowspan="2">Qty</th>
                        <th rowspan="2">Rate</th>
                        <th colspan="2">T.Disc</th>
                        <th colspan="2">Sch</th>
                        <th rowspan="2">Cash<br>Disc</th>
                        <th rowspan="2">Taxable<br>Amt</th>
                        <th colspan="2">Tax</th>               
                        <th rowspan="2">Total</th>
                    </tr>
                    <tr style="font-size:15px;">
                        <th align="right">%</th>
                        <th align="right">Amt</th>
                        <th align="right">%</th>
                        <th align="right">Amt</th>
                        <th align="right">%</th>
                        <th align="right">Amt</th>     
                    </tr>
                    </thead>
                    <tbody>  
                    <?php
            $i = 1;
            $quantity = 0;
            $price = 0;
            $gst = 0;
            $grand_total = $tot_cgst = $tot_sgst = $tot_igst = $tot_gst = $tot_mrp = $tot_qty = $tot_qty_free = $tot_price = $tot_discount = $tot_taxable = $tot_cgst_per = $tot_sgst_per = $tot_igst_per = 0;
            $discount_schme = 0;
            $discount_trade = 0;
            $i = 1;
            $gst_amount = 0;
            $discount_amount = 0;
            $discount_percentage = 0;
            $gst_summry = array();
            foreach ($items as $value) {
                $product_name = '';
                if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {
                    $product_name = $value->product_name;
                } else {
                    $product_name = $value->service_name;
                }
                $gst = round(abs($value->sales_item_igst_percentage), 2) + round(abs($value->sales_item_cgst_percentage), 2) + round(abs($value->sales_item_sgst_percentage), 2);

                $tot_cgst_per += $value->sales_item_cgst_percentage;
                $tot_sgst_per += $value->sales_item_sgst_percentage;
                $tot_igst_per += $value->sales_item_igst_percentage;

                $gst_amount = $value->sales_item_igst_amount + $value->sales_item_cgst_amount + $value->sales_item_sgst_amount;
                $discount_amount = $value->sales_item_scheme_discount_amount + $value->sales_item_discount_amount;
                if ($value->item_discount_percentage == NULL || $value->item_discount_percentage == '') {
                    $discount_per = 0;
                } else {
                    $discount_per = $value->item_discount_percentage;
                }
                $discount_percentage = $discount_per + $value->sales_item_scheme_discount_percentage;
                $grand_total += $value->sales_item_grand_total;
                $tot_gst += $gst_amount;
                $tot_mrp += $value->sales_item_mrp_price;
                $tot_qty += $value->sales_item_quantity;
                $tot_qty_free += $value->sales_item_free_quantity;
                $tot_price += $value->sales_item_unit_price;
                $tot_discount += $discount_amount;
                $tot_taxable += $value->sales_item_taxable_value;
                $tot_igst += $value->sales_item_igst_amount;
                $tot_sgst += $value->sales_item_sgst_amount;
                $tot_cgst += $value->sales_item_cgst_amount;
                ?>
                <tr>
                    <td align="left"><?php echo $i; ?></td>
                    <td align="left" style="width: 20%;"><?php echo strtoupper(strtolower($product_name)); ?></td>
                    <td align="left"><?php echo $value->product_hsn_sac_code; ?></td>
                    <td></td>                    
                    <td align="right"><?php echo precise_amount($value->sales_item_mrp_price); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_quantity); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_unit_price); ?></td>
                    <td align="right"><?php echo precise_amount($value->item_discount_percentage); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_discount_amount); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_scheme_discount_percentage); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_scheme_discount_amount); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_cash_discount_amount); ?></td>
                    <!-- <td align="right"><?php echo precise_amount($value->sales_item_discount_amount); ?><br>(<?php echo precise_amount($value->item_discount_percentage); ?>%)</td> -->
                    <!-- <td align="right"><?php echo precise_amount($value->sales_item_scheme_discount_amount); ?><br>(<?php echo precise_amount($value->sales_item_scheme_discount_percentage); ?>%)</td> -->
                    <td align="right"><?php echo precise_amount($value->sales_item_taxable_value); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_tax_percentage); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_tax_amount); ?></td>
                    <!-- <td align="right"><?php echo precise_amount($value->sales_item_sgst_percentage); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_sgst_amount); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_cgst_percentage); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_sgst_amount); ?></td> -->
                    <td align="right"><?php echo precise_amount($value->sales_item_grand_total); ?></td>
                </tr>
                <?php
                    if ($igst_exist > 0) { ?>
                            <?php

                            if ($value->sales_item_igst_amount <= 0) {
                                echo '-';
                            } else { 
                                $gst_summry['igst']['igst_'.$value->sales_item_igst_percentage]['percentage'] = $value->sales_item_igst_percentage;
                                if(@$gst_summry['igst']['igst_'.$value->sales_item_igst_percentage]['amount']){
                                    $gst_summry['igst']['igst_'.$value->sales_item_igst_percentage]['gst_amount'] += $value->sales_item_igst_amount;
                                    $gst_summry['igst']['igst_'.$value->sales_item_igst_percentage]['amount'] += $value->sales_item_taxable_value;
                                }else{
                                    $gst_summry['igst']['igst_'.$value->sales_item_igst_percentage]['gst_amount'] = $value->sales_item_igst_amount;
                                    $gst_summry['igst']['igst_'.$value->sales_item_igst_percentage]['amount'] = $value->sales_item_taxable_value;
                                } 
                            } ?>
                    <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                            <?php
                            if ($value->sales_item_cgst_amount <= 0) {
                                echo '-';
                            } else {
                                $gst_summry['cgst']['cgst_'.$value->sales_item_cgst_percentage]['percentage'] = $value->sales_item_cgst_percentage;
                                if(@$gst_summry['cgst']['cgst_'.$value->sales_item_cgst_percentage]['amount']){
                                    $gst_summry['cgst']['cgst_'.$value->sales_item_cgst_percentage]['gst_amount'] += $value->sales_item_cgst_amount;
                                    $gst_summry['cgst']['cgst_'.$value->sales_item_cgst_percentage]['amount'] += $value->sales_item_taxable_value;
                                }else{
                                    $gst_summry['cgst']['cgst_'.$value->sales_item_cgst_percentage]['gst_amount'] = $value->sales_item_cgst_amount;
                                    $gst_summry['cgst']['cgst_'.$value->sales_item_cgst_percentage]['amount'] = $value->sales_item_taxable_value;
                                } ?>
                            <?php } ?>
                            <?php
                            if ($value->sales_item_sgst_amount <= 0) {
                                echo '-';
                            } else {
                                $gst_summry['sgst']['cgst_'.$value->sales_item_sgst_percentage]['percentage'] = $value->sales_item_sgst_percentage;
                                if(@$gst_summry['sgst']['cgst_'.$value->sales_item_sgst_percentage]['amount']){
                                    $gst_summry['sgst']['cgst_'.$value->sales_item_sgst_percentage]['gst_amount'] += $value->sales_item_sgst_amount;
                                    $gst_summry['sgst']['cgst_'.$value->sales_item_sgst_percentage]['amount'] += $value->sales_item_taxable_value;
                                }else{
                                    $gst_summry['sgst']['cgst_'.$value->sales_item_sgst_percentage]['gst_amount'] = $value->sales_item_sgst_amount;
                                    $gst_summry['sgst']['cgst_'.$value->sales_item_sgst_percentage]['amount'] = $value->sales_item_taxable_value;
                                }?>
                            <?php } ?>
                        </td>
                    <?php } ?>
                <?php
                $i++;
            }
            ?>
           
            
             <!-- <tr><td colspan="16" style="border-bottom: 1px solid #444;">&nbsp;</td></tr> -->
        
                    </tbody>
        </table>    
        <table class="item-table mt-5"  style="font-size:11px; margin-left: 0px; padding-left: 0px;">
            <tr>
                <td colspan="10">
                    <table style="font-size:12px;">
                        <tr><td colspan="7" style="text-align: center; border-top: 1px solid #333; border-bottom: 1px solid #333"><b>HSN Summary</b></td></tr>
                        <tr style="border-top: 1px solid #333;">
                            <th><b>HSN</b></th>
                            <th><b>Taxable Amt</b></td>
                            <th><b>CGST %</b></th>
                            <th><b>Value</b></th>
                            <th><b>SGST %</b></th>
                            <th><b>Value</b></th>
                            <th><b>Tax Amt</b></th>
                        </tr>
                        <?php
                        foreach ($hsn as $key => $value) {
                            $tax_amount = $value->sales_item_sgst_amount + $value->sales_item_cgst_amount;
                            if($value->hsn_sac_code != ''){
                            ?>
                            <tr>
                                <td><?php echo $value->hsn_sac_code; ?></td>
                                <td><?php echo precise_amount($value->sales_item_taxable_value); ?></td>
                                <td><?php echo precise_amount($value->sales_item_cgst_percentage); ?></td>
                                <td><?php echo precise_amount($value->sales_item_cgst_amount); ?></td>
                                <td><?php echo precise_amount($value->sales_item_sgst_percentage); ?></td>
                                <td><?php echo precise_amount($value->sales_item_sgst_amount); ?></td>
                                <td><?php echo precise_amount($tax_amount); ?></td>
                            </tr>
                            <?php
                            }
                        }
                        ?>

                    </table>
                </td>
                <td colspan="5" style="text-align: left;">
                    <table style="font-size:14px;">
                        <tr><th style="text-align: right;">Taxable Amt:</th>
                            <td style="text-align: right;"><?= $this->numbertowords->formatInr(($data[0]->sales_sub_total)); ?></td>
                        </tr>
                        <tr><th style="text-align: right;">Total Disc:</th>
                            <td style="text-align: right;"><?= $this->numbertowords->formatInr(($data[0]->sales_discount_amount)); ?></td>
                        </tr>
                        <tr><th style="text-align: right;">Net Amt:</th>
                            <td style="text-align: right;"><?= $this->numbertowords->formatInr(($data[0]->sales_grand_total)); ?></td>
                        </tr>
                        <tr><th style="text-align: right;">Net Payable:</th>
                            <td style="text-align: right;"><?= $this->numbertowords->formatInr(($data[0]->sales_grand_total)); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!--<tr>
             <td colspan="11" style="text-align: left;">
                <table style="font-size:12px;">
                    <tr>
                        <th colspan="4">****GST Summary ****</th>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <th>Tax%</th>
                        <th>TBL Amt</th>
                        <th>Tax Amt</th>
                    </tr>
                    <?php if(@$gst_summry['igst'] && $igst_exist > 0){ ?>
                        <?php foreach ($gst_summry['igst'] as $key => $value) {?>
                        <tr>
                            <td>IGST</td>
                            <td><?php echo precise_amount($value['percentage']) ?></td>
                            <td><?php echo precise_amount($value['amount']) ?></td>
                            <td><?php echo precise_amount($value['gst_amount']) ?></td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                    <?php if(@$gst_summry['cgst'] && $cgst_exist > 0){ ?>
                        <?php foreach ($gst_summry['cgst'] as $key => $value) {?>
                        <tr>
                            <td>CGST</td>
                            <td><?php echo precise_amount($value['percentage']) ?></td>
                            <td><?php echo precise_amount($value['amount']) ?></td>
                            <td><?php echo precise_amount($value['gst_amount']) ?></td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                    <?php if(@$gst_summry['sgst'] && $sgst_exist > 0){ ?>
                        <?php foreach ($gst_summry['sgst'] as $key => $value) {?>
                        <tr>
                            <td>SGST</td>
                            <td><?php echo precise_amount($value['percentage']) ?></td>
                            <td><?php echo precise_amount($value['amount']) ?></td>
                            <td><?php echo precise_amount($value['gst_amount']) ?></td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </table>
            </td> -->
            <!-- <td colspan="2" style="text-align: left;" style="border: none;">&nbsp;
               <table>
                    <tr>
                        <td>SCh</td>

                    </tr>
                    <tr>
                        <td>-</td>

                    </tr>
                </table>
            </td> -->
            <!-- <td colspan="5" style="text-align: left; font-size:11px;">Bank Details:<br>Bank Name : <br> A/c No : <br> Branch Name : <br> IFSC Code :</td> 
        </tr>-->
        <tr>
            <th colspan="10" style="text-align: left;">Amount In Words : <b><?php echo $data[0]->currency_name . " " . $this->numbertowords->convert_number(precise_amount(($data[0]->sales_grand_total * $convert_rate)), $data[0]->unit, $data[0]->decimal_unit) . " Only"; ?></td>
            <th colspan="6" style="text-align: right;">Grand Total : <?php echo precise_amount($data[0]->sales_grand_total) ?></td>
        </tr>
        <tr>
            <td colspan="10" style="text-align: left;">Products Once Sold Cannot be taken back or exchanged. Please check items before taking  delivery. Subject to State jurisdiction Only.</td>
            <td colspan="6" style="text-align: right;">For, <b class="uppercase"><?= strtolower($branch[0]->firm_name) ?></b><br>Signature</td>
        </tr>
        </table>
    <!-- Custom HTML header --> 
    <!-- <div id="header"> 
      <span style="float: right; margin-top: -25px"><h3><small style="font-size: 8px;line-height: 2"><?= ($invoice_type != '' ? $invoice_type : ''); ?></small></h3> 
              <br><p class="right"></p></span> 
              <table class="item-table-td table mt-20">
                  <tr>
                      <td colspan="13" style="text-align: center;"><b>TAX INVOICE</b></td>
                  </tr>
                  </table>
    </div> -->  
    <!-- Custom HTML footer --> 
    <div class="footer">
        <?php echo $access_common_settings[0]->invoice_footer; ?>
    </div>
</body>
</html>
