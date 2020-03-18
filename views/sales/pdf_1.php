<?php

$GLOBALS['common_settings_amount_precision'] = $access_common_settings[0]->amount_precision;



function precise_amount($val) {

    $val = (float) $val;

    // $amt =  round($val,$GLOBALS['common_settings_amount_precision']);

    $dat = number_format($val, $GLOBALS['common_settings_amount_precision'], '.', '');

    return $dat;

}

?>

<!DOCTYPE html>

<html>

    <head>

        <title> Sales Invoice </title>

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

                width: 85%;          	            	

            }            

            .dashed_table tr th, .dashed_table tr td{

                border: 1px dashed #333;            	

                padding: 4px;

            }            

            textarea{

                border: 0.5px solid #444;

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

        <span style="float: right; margin-top: -25px"><h3><u>Sales Tax Invoice</u></h3></span>

       

<!--        <p class="right"><small>(<?php echo $invoice_type; ?>)</small></p>

        <h3 align="center"><u></u></h3>-->

        <table class="table header_table mt-20" >

            <tr>

                <td class="p-5" align="center" >

                    <?php

                    if (isset($branch[0]->firm_logo) && $branch[0]->firm_logo != "") {

                        ?>

                        <img class="img" src="<?php echo FCPATH . '/assets/branch_files/' . $branch[0]->branch_id . '/' . $branch[0]->firm_logo; ?>" height="50" />

                    <?php } else { ?>

                        <b class="uppercase">  <?= strtolower($branch[0]->firm_name) ?></b>

                    <?php } ?>

                </td>

                <td valign="top">

                    Invoice Date<br>

                    <?php

                    $date = $data[0]->sales_date;

                    $date_for = date('d-m-Y', strtotime($date));

                    ?>                  

                    <span class="bold"><?php

                        if (isset($data[0]->sales_date)) {

                            echo $date_for;

                        }

                        ?></span>

                </td>

                <td valign="top">

                    Invoice Number<br>                   

                    <span class="bold"><?php

                        if (isset($data[0]->sales_invoice_number)) {

                            echo $data[0]->sales_invoice_number;

                        }

                        ?></span>

                </td>

            </tr>

        </table>

        <table class="table header_table mt-20" >

            <tr>

                <td width="50%" valign="top" class="pad-rgt">

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

                        ?>

                        <?php

                        if (isset($branch[0]->branch_land_number)) {

                            if ($branch[0]->branch_land_number != "") {

                                echo '<br>Landline No : ' . $branch[0]->branch_land_number;

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

                            echo "<br />IEC : " . $branch[0]->branch_import_export_code_number;

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

                            echo "<br />CIN : " . $branch[0]->branch_cin_number;

                        }

                    }

                    ?>

                </td>

                <td width="50%" valign="top" class="pad-rgt">

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

                    if (isset($data[0]->customer_address) && $data[0]->customer_address != "") {

                        echo "<br/>";

                        echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $data[0]->customer_address);

                    } else {

                        echo "<br/>";

                        echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $data[0]->shipping_address);

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

                </td>

            </tr>

        </table>

        <table class="table header_table mt-20">

            <tr>

                <td valign="top">

                    Billing Country<br>

                    <div class="h-5">

                    </div>

                    <span class="bold"><?php

                    if (isset($data[0]->billing_country)) {

                        echo ucfirst($data[0]->billing_country);

                    }

                    ?></span>

                </td>

                <td valign="top">

                    Nature of Supply<br>

                    <div class="h-5"></div>

                    <span class="bold"><?php

                        if (isset($nature_of_supply)) {

                            echo $nature_of_supply;

                            // echo str_replace("_", " ", $type_of_supply);

                        }

                    ?></span>

                </td>

                <td valign="top">

                    GST Payable on Reverse Charge<br>

                    <div class="h-5">

                    </div>

                    <span class="bold"><?php

                        if (isset($data[0]->sales_gst_payable)) {

                            echo ucfirst($data[0]->sales_gst_payable);

                        }

                    ?></span>

                </td>

            </tr>

        </table>

        <table class="item-table table mt-20 custom_table">

            <thead>

                <tr>

                    <th rowspan="2" >#</th>

                    <th rowspan="2">Particulars</th>

                    <th rowspan="2">Quantity</th>

                    <th rowspan="2">Rate</th>

                    <th rowspan="2">Subtotal</th>

                    <?php

                    if ($discount_exist > 0) {

                        ?>

                        <th rowspan="2" >Discount</th>

                    <?php } ?>

                    <?php

                    if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {

                        ?>

                        <th rowspan="2">Taxable Value</th>

                    <?php } ?>

                    <?php

                    if ($tds_exist > 0) {

                        ?>

                        <th colspan="2" >TDS/TCS</th>

                    <?php } ?>



<?php

if ($tax_exist > 0) {

    ?>

                        <th colspan="2">Tax</th>

                    <?php } elseif ($igst_exist > 0) { ?>

                        <th colspan="2">IGST</th>

<?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>

                        <th colspan="2">CGST</th>

                        <th colspan="2">SGST</th>

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

                                <br>HSN/SAC: <?php echo $value->product_hsn_sac_code; ?>

                            <?php

                            if (isset($value->sales_item_description) && $value->sales_item_description != "") {

                                echo "<br/>";

                                echo $value->sales_item_description;

                            }

                            ?>

                            </td>

                            <?php } else { ?>

                            <td style="text-align: left;"><?php echo $value->service_name; ?><br>HSN/SAC: <?php echo $value->service_hsn_sac_code; ?>

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

                        <td style="text-align: right;"><?php echo precise_amount($value->sales_item_unit_price); ?></td>

                        <td style="text-align: right;"><?php echo precise_amount($value->sales_item_sub_total); ?></td>



                        <?php

                        if ($discount_exist > 0) {

                            ?>

                            <td style="text-align: right;"><?php echo precise_amount($value->sales_item_discount_amount); ?></td>

                        <?php } ?>

    <?php

    if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {

        ?>

                            <td style="text-align: right;"><?php echo precise_amount($value->sales_item_taxable_value); ?></td>

                        <?php } ?>

                        <?php

                        if ($tds_exist > 0) {

                            ?>

                            <td><?php echo precise_amount($value->sales_item_tds_percentage); ?></td>

                            <td style="text-align: right;"><?php echo precise_amount($value->sales_item_tds_amount); ?></td>

    <?php } ?>



                        <?php

                        if ($tax_exist > 0) {

                            ?>

                            <td><?php echo precise_amount($value->sales_item_tax_percentage); ?></td>

                            <td style="text-align: right;"><?php echo precise_amount($value->sales_item_tax_amount); ?></td>

                        <?php } elseif ($igst_exist > 0) { ?>

                            <td><?php echo precise_amount($value->sales_item_igst_percentage); ?></td>

                            <td style="text-align: right;"><?php echo precise_amount($value->sales_item_igst_amount); ?></td>

                        <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>

                            <td><?php echo precise_amount($value->sales_item_cgst_percentage); ?></td>

                            <td style="text-align: right;"><?php echo precise_amount($value->sales_item_cgst_amount); ?></td>

                            <td><?php echo precise_amount($value->sales_item_sgst_percentage); ?></td>

                            <td style="text-align: right;"><?php echo precise_amount($value->sales_item_sgst_amount); ?></td>

                    <?php } ?>

                    <?php if ($cess_exist > 0) { ?>

                            <td style="text-align: center;"><?= precise_amount($value->sales_item_tax_cess_percentage); ?></td>

                            <td style="text-align: right;"><?= precise_amount($value->sales_item_tax_cess_amount); ?></td>

                    <?php } ?>

                        <td style="text-align: right;"><?php echo precise_amount($value->sales_item_grand_total); ?></td>

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

                    <th  ><?= precise_amount($price) ?></th>

                    <th  ><?= precise_amount($data[0]->sales_sub_total) ?></th>

                    <?php

                    if ($discount_exist > 0) {

                        ?>

                        <th  ><?= precise_amount($data[0]->sales_discount_amount) ?></th>

                    <?php } ?>

                    <?php

                    if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {

                        ?>

                        <th  ><?= precise_amount($data[0]->sales_taxable_value) ?></th>

                    <?php } ?>



                    <?php

                    if ($tds_exist > 0) {

                        ?>

                        <th colspan="2" ><?= precise_amount($data[0]->sales_tds_amount + $data[0]->sales_tcs_amount) ?></th>

<?php } ?>



                    <?php

                    if ($tax_exist > 0) {

                        ?>

                        <th colspan="2" ><?= precise_amount($data[0]->sales_tax_amount) ?></th>

<?php } elseif ($igst_exist > 0) { ?>

                        <th colspan="2" ><?= precise_amount($data[0]->sales_igst_amount) ?></th>

<?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>

                        <th colspan="2" ><?= precise_amount($data[0]->sales_cgst_amount) ?></th>

                        <th colspan="2" ><?= precise_amount($data[0]->sales_sgst_amount) ?></th>

            <?php } ?>

            <?php if ($cess_exist > 0) { ?>

                        <th colspan="2" style="text-align: center;"><?= precise_amount($data[0]->sales_tax_cess_amount) ?></th>

<?php } ?>

                    <th  ><?= bcadd($data[0]->sales_grand_total, $data[0]->round_off_amount, 2); ?></th>

                </tr>

            </tbody>

        </table>

        <table class="table mt-20">

            <?php

            if ($data[0]->total_freight_charge > 0) {

                ?>

                <tr class="no-bottom-border no-top-border">

                    <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Freight Charge (<?php echo '<span style="font-family: DejaVu Sans; sans-serif;">' . $data[0]->currency_code . '</span>'; ?>)</td>

                    <td align="right" class="footerpad fontH"><?php echo precise_amount($data[0]->total_freight_charge); ?> /-</td>

                </tr>

            <?php

            }

            if ($data[0]->total_insurance_charge > 0) {

                ?>

                <tr class="no-bottom-border no-top-border">

                    <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Insurance Charge (<?php echo '<span style="font-family: DejaVu Sans; sans-serif;">' . $data[0]->currency_code . '</span>'; ?>)</td>

                    <td align="right" class="footerpad fontH"><?php echo precise_amount($data[0]->total_insurance_charge); ?> /-</td>

                </tr>

<?php

}

if ($data[0]->total_packing_charge > 0) {

    ?>

                <tr class="no-bottom-border no-top-border">

                    <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Packing & Forwarding Charge (<?php echo '<span style="font-family: DejaVu Sans; sans-serif;">' . $data[0]->currency_code . '</span>'; ?>)</td>

                    <td align="right" class="footerpad fontH"><?php echo precise_amount($data[0]->total_packing_charge); ?> /-</td>

                </tr>

<?php

}

if ($data[0]->total_incidental_charge > 0) {

    ?>

                <tr class="no-bottom-border no-top-border">

                    <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Incidental Charge (<?php echo '<span style="font-family: DejaVu Sans; sans-serif;">' . $data[0]->currency_code . '</span>'; ?>)</td>

                    <td align="right" class="footerpad fontH"><?php echo precise_amount($data[0]->total_incidental_charge); ?> /-</td>

                </tr>

<?php

}

if ($data[0]->total_inclusion_other_charge > 0) {

    ?>

                <tr class="no-bottom-border no-top-border">

                    <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Other Inclusive Charge (<?php echo '<span style="font-family: DejaVu Sans; sans-serif;">' . $data[0]->currency_code . '</span>'; ?>)</td>

                    <td align="right" class="footerpad fontH"><?php echo precise_amount($data[0]->total_inclusion_other_charge); ?> /-</td>

                </tr>

<?php

}

if ($data[0]->total_exclusion_other_charge > 0) {

    ?>

                <tr class="no-bottom-border no-top-border">

                    <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Other Exclusive Charge (<?php echo '<span style="font-family: DejaVu Sans; sans-serif;">' . $data[0]->currency_code . '</span>'; ?>)</td>

                    <td align="right" class="footerpad fontH">-<?php echo precise_amount($data[0]->total_exclusion_other_charge); ?> /-</td>

                </tr>

            <?php } ?>

        </table>



        <table class="table">

            <tr>

                <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;"><b>Grand Total (<?php echo $data[0]->currency_code . '</span>'; ?>)</td>

                <td align="right" class="footerpad fontH"><b><?php echo precise_amount($data[0]->sales_grand_total); ?>/-</td>

            </tr>

            <tr>

                <td style="" align="left" colspan="2">

                    Amount (in Words): <b><?php echo $data[0]->currency_text . " " . $this->numbertowords->convert_number(precise_amount($data[0]->sales_grand_total)) . " Only"; ?> </b><br/>

                </td>

            </tr>

<?php

if ($data[0]->round_off_amount > 0 || $data[0]->round_off_amount < 0) {

    ?>

                <tr>

                    <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;"><b>Round Off (<?php echo '<span style="font-family: DejaVu Sans; sans-serif;">' . $data[0]->currency_code . '</span>'; ?>)</td>

                    <td align="right" class="footerpad fontH"><b><?php echo precise_amount($data[0]->round_off_amount); ?> /-</td>

                </tr>



                    <?php } ?>

        </table>



<?php

if (isset($data[0]->sales_type_of_supply) && $data[0]->sales_type_of_supply != "regular") {

    ?>

            <table class="table mt-20">

                <tr>

                    <td style="" valign="top">

            <?php

            if (isset($data[0]->sales_type_of_supply) && $data[0]->sales_type_of_supply == "export_with_payment") {

                ?>

                            <p><b>NOTE : </b><small> SUPPLY MEANT FOR EXPORT ON PAYMENT OF INTEGRATED TAX</small></p>

            <?php } else if (isset($data[0]->sales_type_of_supply) && $data[0]->sales_type_of_supply == "export_without_payment") { ?>

                            <p><b>NOTE : </b><small>SUPPLY MEANT FOR EXPORT UNDER BOND OR LETTER OF UNDERTAKING WITHOUT PAYMENT OF INTEGRATED TAX</small></p>

            <?php } ?>

                    </td>

                </tr>

            </table>

<?php } ?>







        <?php

        $notes_sub_module = 0;



        $this->load->view('note_template/display_note');

        ?>

        <br/>

<?php

if (isset($access_common_settings[0]->invoice_footer) && $access_common_settings[0]->invoice_footer != "") {

    ?>

            <table>

                <tr>

                    <td colspan="14" style="border:0px;text-align: center;font-size: 13px;"><?php echo $access_common_settings[0]->invoice_footer; ?></td>

                </tr>

            </table>

    <?php

}

?>

    </body>

</html>

