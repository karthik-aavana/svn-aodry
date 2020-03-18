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
                margin-top: 50px;
            }           
            .total_table th, .total_table td{
                padding: 40px;                
            }             
        </style>
        <style><?php $this->load->view('common/common_pdf'); ?></style>
    </head>
    <body>
        <span style="float: right; margin-top: -25px"><h3><small style="font-size: 8px;line-height: 2"><?= ($invoice_type != '' ? '(' . $invoice_type . ')' : ''); ?></small></h3>
            <br><p class="right"></p></span>
        <table class="item-table-th item-table-td table mt-49" style="min-height: 300px">
            <tr><th colspan="5" style="text-align: left;">Date : <?php
                                $date = $data[0]->sales_date;
                                $date_for = date('d-m-Y', strtotime($date));
                                echo $date_for;
                                ?> </th>
                <th colspan="5" style="text-align:left;">TAX INVOICE</th>
                <th colspan="5" style="text-align: right;">Bill No : <?php
                                if (isset($data[0]->sales_invoice_number)) {
                                    echo $data[0]->sales_invoice_number;
                                }
                                ?> 
                </th>
            </tr>
            <tr>
                <td colspan="6" style="text-align: left;">
                    <table class="table" style="font-size:16px;">
                        <!-- <tr><td colspan="2"><b> From,</b></td></tr> -->
                        <tr><td>From : </td>
                            <td class="uppercase"><?= strtolower($branch[0]->firm_name) ?></td>
                        </tr>
                        <tr><td>Address : </td>
                            <td><?php
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
                        <tr><td>Contact No : </td>
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
                        <tr><td>PAN : </td>
                            <td><?php
                                if (isset($branch[0]->branch_pan_number)) {
                                    if ($branch[0]->branch_pan_number != "" || $branch[0]->branch_pan_number != null) {
                                        echo $branch[0]->branch_pan_number;
                                    }
                                    ?>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr><td>GSTIN : </td>
                            <td><?php
                                if (isset($branch[0]->branch_gstin_number)) {
                                    if ($branch[0]->branch_gstin_number != "" || $branch[0]->branch_gstin_number != null) {
                                        echo $branch[0]->branch_gstin_number;
                                    }
                                    ?>
                                <?php } ?></td>
                        </tr>
                        <tr><td>Drug Licence# : </td>
                            <td><?=(@$branch[0]->drug_licence_no_1 && $branch[0]->drug_licence_no_1 != '' ? $branch[0]->drug_licence_no_1 : '')?></td>
                        </tr>
                        <tr><td>Food Licence# : </td>
                            <td><?=(@$branch[0]->drug_licence_no_2 && $branch[0]->drug_licence_no_2 != '' ? $branch[0]->drug_licence_no_2 : '')?></td>
                        </tr>
                    </table>
                </td>
                <td colspan="4" style="text-align: left;">
                    <table class="table" style="font-size:16px;">
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
                            <td style="width:33%">Route : </td>
                            <td class="uppercase" style="text-align: left;">
                            </td>
                        </tr>
                        <tr>
                            <td style="width:33%">Salesman : </td>
                            <td class="uppercase" style="text-align: left;">
                            </td>
                        </tr>
                    </table>
                </td>
                <td colspan="5" style="text-align: left;">
                    <table class="table" style="font-size:16px;">
                        <!-- <tr><td colspan="2"><b> BILL TO,</b></td></tr> -->
                        <tr><td style="width:45%">Bill to : </td>
                            <td class="uppercase" style="text-align: left;"><?= strtolower($data[0]->customer_name) ?></td>
                        </tr>
                        <tr><td>Address : </td>
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
                        <tr><td>Contact No : </td>
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
                        <tr><td>PAN : </td>
                            <td style="text-align: left;"><?php
                                if (isset($data[0]->customer_pan_number)) {
                                    if ($data[0]->customer_pan_number != "" || $data[0]->customer_pan_number != null) {
                                        echo $data[0]->customer_pan_number;
                                    }
                                    ?>
                                <?php } ?>

                            </td>
                        </tr>
                        <tr><td>GSTIN : </td>
                            <td style="text-align: left;"><?php
                                if (isset($data[0]->customer_gstin_number)) {
                                    if ($data[0]->customer_gstin_number != "") {
                                        echo $data[0]->customer_gstin_number;
                                    }
                                }
                                ?></td>
                        </tr>
                        <tr><td>Drug Licence# : </td>
                            <td style="text-align: left;"><?=(@$data[0]->drug_licence_no && $data[0]->drug_licence_no != '' ? str_replace(',','<br>',$data[0]->drug_licence_no) : '')?></td>
                        </tr>
                        <tr><td>Food Licence# : </td>
                            <td style="text-align: left;"><?=(@$data[0]->food_licence_number && $data[0]->food_licence_number != '' ? str_replace(',','<br>',$data[0]->food_licence_number) : '')?></td>
                        </tr>
                    </table>                        
                </td>
            </tr>

            <tr class="total_table">
                <th>Sl<br>No</th>
                <th>Product Name</th>
                <th>HSN Code</th>
                <th>Batch</th>
                <th>MRP</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>T.Disc</th>
                <th>Sch</th>
                <th>Taxable<br>Amt</th>
                <th>SGST%</th>
                <th>Amt</th>
                <th>CGST%</th>
                <th>Amt</th>
                <th>Total</th>
            </tr>
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
                <tr class="total_table">
                    <td align="left"><?php echo $i; ?></td>
                    <td align="left"><?php echo strtoupper(strtolower($product_name)); ?></td>
                    <td align="right"><?php echo $value->product_hsn_sac_code; ?></td>
                    <td></td>                    
                    <td align="right"><?php echo precise_amount($value->sales_item_mrp_price); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_quantity); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_unit_price); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_discount_amount); ?><br>(<?php echo precise_amount($value->item_discount_percentage); ?>%)</td>
                    <td align="right"><?php echo precise_amount($value->sales_item_scheme_discount_amount); ?><br>(<?php echo precise_amount($value->sales_item_scheme_discount_percentage); ?>%)</td>
                    <td align="right"><?php echo precise_amount($value->sales_item_taxable_value); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_sgst_percentage); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_sgst_amount); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_cgst_percentage); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_sgst_amount); ?></td>
                    <td align="right"><?php echo precise_amount($value->sales_item_grand_total); ?></td>
                </tr>
                <?php
                $i++;
            }
            ?>
            <tr><td style="height: 15px"></td></tr>
            <tr>
                <td colspan="10">
                    <table class="table" style="font-size:18px;">
                        <tr><td colspan="7" style="text-align: center; border-top: 1px solid #333; border-bottom: 1px solid #333"><b>HSN Summary</b></td></tr>
                        <tr style="border-top: 1px solid #333;">
                            <td><b>HSN</b></td>
                            <td><b>Taxable Amt</b></td>
                            <td><b>CGST %</b></td>
                            <td><b>Value</b></td>
                            <td><b>SGST %</b></td>
                            <td><b>Value</b></td>
                            <td><b>Tax Amt</b></td>
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
            <tr>
                <td colspan="5"></td>
            </tr>
        </tr>
        <tr><td colspan="15" style="border-bottom: 1px solid #444;">&nbsp;</td></tr>
        <tr>
            <td colspan="4" style="text-align: left;">
                <table>
                    <tr>
                        <td>****GST Summary ****</td>

                    </tr>
                    <tr>
                        <td>-</td>

                    </tr>
                </table>
            </td>
            <td colspan="2" style="text-align: left;">
                <table>
                    <tr>
                        <td>SCh</td>

                    </tr>
                    <tr>
                        <td>-</td>

                    </tr>
                </table>
            </td>
            <td colspan="5" style="text-align: left;">Bank Details:<br>Bank Name : <br> A/c No : <br> Branch Name : <br> IFSC Code :</td>
            <td colspan="5" style="text-align: left;">
                <table class="table" style="font-size:16px;">
                    <tr><td style="text-align: right;">Taxable Amt:</td>
                        <td style="text-align: right;"><?= $this->numbertowords->formatInr(($data[0]->sales_sub_total)); ?></td>
                    </tr>
                    <tr><td style="text-align: right;">Total Disc:</td>
                        <td style="text-align: right;"><?= $this->numbertowords->formatInr(($data[0]->sales_discount_amount)); ?></td>
                    </tr>
                    <tr><td style="text-align: right;">Net Amt:</td>
                        <td style="text-align: right;"><?= $this->numbertowords->formatInr(($data[0]->sales_grand_total)); ?></td>
                    </tr>
                    <tr><td style="text-align: right;">Net Payable:</td>
                        <td style="text-align: right;"><?= $this->numbertowords->formatInr(($data[0]->sales_grand_total)); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th colspan="10" style="text-align: left;">Amount In Words : <b><?php echo $data[0]->currency_name . " " . $this->numbertowords->convert_number(precise_amount(($data[0]->sales_grand_total * $convert_rate)), $data[0]->unit, $data[0]->decimal_unit) . " Only"; ?></td>
                    <th colspan="5" style="text-align: right;">Grand Total : <?php echo precise_amount($data[0]->sales_grand_total) ?></td>
                        </tr>
                    <tr>
                        <td colspan="10" style="text-align: left;">Products Once Sold Cannot be taken back or exchanged. Please check items before taking  delivery. Subject to State jurisdiction Only.</td>
                        <td colspan="5" style="text-align: right;">For, <b class="uppercase"><?= strtolower($branch[0]->firm_name) ?></b><br>Signature</td>
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
