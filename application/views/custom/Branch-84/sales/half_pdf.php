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


        </style>
        <style><?php $this->load->view('common/common_pdf'); ?></style>
    </head>
    <body>
        <span style="float: right; margin-top: -25px"><h3><small style="font-size: 8px;line-height: 2"><?= ($invoice_type != '' ? '(' . $invoice_type . ')' : ''); ?></small></h3>
            <br><p class="right"></p></span>
        <table class="item-table-th item-table-td table mt-49" style="min-height: 300px">                
            <tr>
                <th colspan="12" style="text-align:center;">CASH/CREDIT</th>
            </tr>                    
            <tr>
                <th colspan="4" style="text-align: left;">
                    <b class="uppercase"><?= strtolower($branch[0]->firm_name) ?></b><br>
                    <span style="font-weight: normal;">
                        Address : 
                        <?php
                        if (isset($branch[0]->branch_address)) {
                            echo str_replace(array(
                                "\r\n",
                                "\\r\\n",
                                "\n",
                                "\\n"), "<br>", $branch[0]->branch_address);
                        }
                        ?><br>
                        Email : 
                        <?php
                        if (isset($branch[0]->branch_email_address)) {
                            if ($branch[0]->branch_email_address != "") {
                                echo $branch[0]->branch_email_address;
                            }
                        }
                        ?>
                    </span>
                </th>
                <th colspan="5" style="text-align: left;">

                </th>
                <th colspan="3" style="text-align: left;">
                    GSTIN : <span style="font-weight: normal;"><?php
                        if (isset($branch[0]->branch_gstin_number)) {
                            if ($branch[0]->branch_gstin_number != "" || $branch[0]->branch_gstin_number != null) {
                                echo $branch[0]->branch_gstin_number;
                            }
                            ?>
                        <?php } ?></span><br>
                    Bill No : <span style="font-weight: normal;"><?php
                        if (isset($data[0]->sales_invoice_number)) {
                            echo $data[0]->sales_invoice_number;
                        }
                        ?></span><br>
                    Bill Date : <span style="font-weight: normal;"><?php
                        $date = $data[0]->sales_date;
                        $date_for = date('d-m-Y', strtotime($date));
                        echo $date_for;
                        ?></span><br>
                    State : <span style="font-weight: normal;"><?php
                        if (isset($branch[0]->branch_state_name)) {
                            if ($branch[0]->branch_state_name != "" || $branch[0]->branch_state_name != null) {
                                echo $branch[0]->branch_state_name . ' [' . $branch[0]->branch_state_code . ']';
                            }
                            ?>
                        <?php } ?></span><br>
                </th>
            </tr>
            <tr>
                <th colspan="4" style="text-align: left;">
                    Customer Details :  <span class="uppercase" style="font-weight: normal;"><?= strtolower($data[0]->customer_name) ?></span><br>
                    <span style="font-weight: normal;">
                        <?php
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
                        ?>
                    </span>

                </th>
                <th colspan="5" style="text-align: left;">

                </th>
                <th colspan="3" style="text-align: left;">
                    GSTIN : <span style="font-weight: normal;"><?php
                        if (isset($data[0]->customer_gstin_number)) {
                            if ($data[0]->customer_gstin_number != "") {
                                echo $data[0]->customer_gstin_number;
                            }
                        }
                        ?></span><br>
                    Phone : <span style="font-weight: normal;"><?php
                        if (isset($data[0]->customer_mobile)) {
                            if ($data[0]->customer_mobile != "") {
                                echo $data[0]->customer_mobile;
                            }
                        }
                        ?></span><br>
                    State : <span style="font-weight: normal;"><?php
                        if (isset($data[0]->place_of_supply)) {
                            if ($data[0]->place_of_supply != "" || $data[0]->place_of_supply != null) {
                                echo $data[0]->place_of_supply;
                            }
                        }
                        ?>[<?php echo $data[0]->customer_state_code; ?>]</span>
                </th>
            </tr>
            <thead style="display: table-header-group;">
                <tr>
                    <th>Sl<br>No</th>
                    <th>Item Name</th>
                    <th>HSN/SAC</th>
                    <th>Qty</th>
                    <th>Free</th>
                    <th>Unit</th>
                    <th>Basic Rate</th>                    
                    <th>MRP</th>
                    <th>T. Disc</th>
                    <th>Sch. Disc<img class="img" src="<?php echo FCPATH . '/assets/images/currency/' . $data[0]->currency_symbol_pdf; ?>" style="width: 9px"  /></th>
                    <th>GST%</th>
                    <th>Total<img class="img" src="<?php echo FCPATH . '/assets/images/currency/' . $data[0]->currency_symbol_pdf; ?>" style="width: 9px"  /></th>
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
                    <tr style="page-break-before: always; page-break-after: always;">
                        <td><?php echo $i; ?></td>
                        <td style="text-align: left"><?php echo strtoupper(strtolower($product_name)); ?></td>
                        <td><?php echo $value->product_hsn_sac_code; ?></td>
                        <td><?php echo precise_amount($value->sales_item_quantity); ?></td>
                        <td><?php echo precise_amount($value->sales_item_free_quantity); ?></td>
                        <td><?php
                            if ($value->product_unit != '') {
                                $unit = explode("-", $value->product_unit);
                                if ($unit[0] != '')
                                    echo $unit[0];
                            }
                            ?></td>
                        <td><?php echo precise_amount($value->sales_item_unit_price); ?></td>
                        <td><?php echo precise_amount($value->sales_item_mrp_price); ?></td>
                        <td><?php echo precise_amount($value->sales_item_discount_amount); ?><br>(<?php echo precise_amount($value->item_discount_percentage); ?>%)</td>
                        <td><?php echo precise_amount($value->sales_item_scheme_discount_amount); ?><br>(<?php echo precise_amount($value->sales_item_scheme_discount_percentage); ?>%)</td>
                        <td><?php echo $gst; ?></td>
                        <td><?php echo precise_amount($value->sales_item_grand_total); ?></td>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
                <tr style="page-break-before: always; page-break-after: always;">
                    <td colspan="8" style="text-align: left; margin-top: 10px">
                        <b class="uppercase">Reverse Charge: 
                            <span><?php
                                if (isset($data[0]->sales_gst_payable)) {
                                    echo ucfirst($data[0]->sales_gst_payable);
                                }
                                ?></b>
                        <table class="item-table table" style="font-size:16px; margin-top: 15px;margin-right: 10px">
                            <tr><td colspan="8" style="text-align: center; border: 1px solid #444;"><b>HSN Summary</b></td></tr>
                            <tr>
                                <td style="border: 1px solid #444;"><b>HSNCODE</b></td>
                                <td style="border: 1px solid #444;"><b>Taxable AMT</b></td>
                                <td style="border: 1px solid #444;"><b>CGST RATE</b></td>
                                <td style="border: 1px solid #444;"><b>CGST AMT</b></td>
                                <td style="border: 1px solid #444;"><b>SGST RATE</b></td>
                                <td style="border: 1px solid #444;"><b>SGST AMT</b></td>
                                <td style="border: 1px solid #444;"><b>IGST RATE</b></td>
                                <td style="border: 1px solid #444;"><b>IGST AMT</b></td>
                            </tr>
                            <?php
                            $cgst = $sgst = $igst = 0;
                            foreach ($hsn as $key => $value) {
                                $tax_amount = + $value->sales_item_cgst_amount;
                                $cgst += $value->sales_item_cgst_amount;
                                $sgst += $value->sales_item_sgst_amount;
                                $igst += $value->sales_item_igst_amount;
                                if ($value->hsn_sac_code != '') {
                                    ?>
                                    <tr style="border: 1px solid #444;">
                                        <td style="border: 1px solid #444; text-align: left;"><?php echo $value->hsn_sac_code; ?></td>
                                        <td style="border: 1px solid #444;  text-align: right;"><?php echo precise_amount($value->sales_item_taxable_value); ?></td>
                                        <td style="border: 1px solid #444;  text-align: right;"><?php echo precise_amount($value->sales_item_cgst_percentage); ?></td>
                                        <td style="border: 1px solid #444;  text-align: right;"><?php echo precise_amount($value->sales_item_cgst_amount); ?></td>
                                        <td style="border: 1px solid #444;  text-align: right;"><?php echo precise_amount($value->sales_item_sgst_percentage); ?></td>
                                        <td style="border: 1px solid #444;  text-align: right;"><?php echo precise_amount($value->sales_item_sgst_amount); ?></td>
                                        <td style="border: 1px solid #444;  text-align: right;"><?php echo precise_amount($value->sales_item_igst_percentage); ?></td>
                                        <td style="border: 1px solid #444;  text-align: right;"><?php echo precise_amount($value->sales_item_igst_amount); ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            <tr style="border: 1px solid #444;">
                                <td colspan="2" style="border: 1px solid #444;"></td>
                                <td style="border: 1px solid #444;">CGST</td>
                                <td style="border: 1px solid #444;  text-align: right;"><b><?php echo precise_amount($cgst); ?></b></td>
                                <td style="border: 1px solid #444;">SGST</td>
                                <td style="border: 1px solid #444;  text-align: right;"><b><?php echo precise_amount($sgst); ?></b></td>
                                <td style="border: 1px solid #444;">IGST</td>
                                <td style="border: 1px solid #444;  text-align: right;"><b><?php echo precise_amount($igst); ?></b></td>
                            </tr>
                        </table>
                    </td>                    
                    <td colspan="5" style="text-align: left;">
                        <table class="item-table table" style="font-size:18px; border: 1px solid #444; margin-top: 30px">
                            <tr> 
                                <td style=" text-align: left; padding-left: 8px;"><b>Total Amount</b></td>
                                <td>:</td>
                                <td style=" text-align: right;padding-right: 8px"><b><?php echo precise_amount($data[0]->sales_sub_total); ?></b></td>
                            </tr>
                            <tr> 
                                <td style=" text-align: left;padding-left: 8px;">Pretax</td>
                                <td>:</td>
                                <td style=" text-align: right;padding-right: 8px">0:00</td>
                            </tr>
                            <tr> 
                                <td style=" text-align: left;padding-left: 8px;">Total Discount</td>
                                <td>:</td>
                                <td style=" text-align: right;padding-right: 8px"> <?= precise_amount($data[0]->sales_discount_amount); ?></td>
                            </tr>
                            <tr>
                                <td style=" text-align: left;padding-left: 8px;">Total Taxable</td>
                                <td>:</td>
                                <td style=" text-align: right;padding-right: 8px"><?php echo $tot_taxable; ?></td>
                            </tr>
                            <tr>
                                <td style=" text-align: left;padding-left: 8px;">CGST</td>
                                <td>:</td>
                                <td style=" text-align: right;padding-right: 8px"><?php echo $tot_cgst; ?></td>
                            </tr>
                            <tr>
                                <td style=" text-align: left;padding-left: 8px;">SGST</td>
                                <td>:</td>
                                <td style=" text-align: right;padding-right: 8px"><?php echo $tot_sgst; ?></td>
                            </tr>
                            <tr>
                                <td style=" text-align: left;padding-left: 8px;">Round off Amount</td>
                                <td>:</td>
                                <td style=" text-align: right;padding-right: 8px"><?= precise_amount($data[0]->round_off_amount); ?></td>
                            </tr>
                            <tr>
                                <td style=" text-align: left;padding-left: 8px;"><b>Net Payable</b></td>
                                <td>:</td>
                                <td style=" text-align: right;padding-right: 8px"><b><?= precise_amount($data[0]->sales_grand_total); ?></b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" style="text-align: left;"><b>Amount In Words :</b> <?php echo $data[0]->currency_name . " " . $this->numbertowords->convert_number(precise_amount(($data[0]->sales_grand_total)), $data[0]->unit, $data[0]->decimal_unit) . " Only"; ?></td>
                    <td colspan="5" style="text-align: left;"></td>
                </tr>
                <tr>
                    <td colspan="8" style="text-align: left;"><b>For </b>&nbsp;&nbsp;<b class="uppercase"><?= strtolower($branch[0]->firm_name) ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No. of Items: <?php echo $i - 1; ?> </td>
                    <td></td>
                    <td colspan="4" style="text-align: left;"></td>
                </tr>
            </tbody>
        </table>
        <!-- Custom HTML header --> 
        <div class="footer">
            <?php echo $access_common_settings[0]->invoice_footer; ?>
        </div>
    </body>
</html>
