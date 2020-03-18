<!DOCTYPE html>
<html>
    <head>
        <title>
            Purchase Order Invoice
        </title>
        <style type="text/css">

            .table {
                margin: 0px;
                font-size: 11px;
            }
            .table, th, td {
                border: 1px solid #333 !important;
                border-width: thin;
                font-size: 11px;
                padding: 2px 4px;
                border-collapse: collapse !important;

            }
            .header_table td{
                padding-left: 15px;

            }
            .footerpad {
                padding: 4px;
            }
            .minheight {
                min-height: 1000px;
            }
            .fontS {
                font-size: 11px;
            }
            .fontH {
                font-size: 12px;
            }
            p
            {
                font-size: 12px;
            }

            tr.no-bottom-border td
            {
                border-bottom: none !important;
            }
            tr.no-top-border td
            {
                border-top: none !important;
            }
        </style>
    </head>
    <body>
        <table width="100%" class="table">
            <tr style="border: 0px;">
                <td style="border: 0px;text-align: center; font-size: 20px">Purchase Order</td>

            </tr>
            <!-- <tr style="border: 0px;">
   <td style="border: 0px;text-align: right;">(ORIGINAL FOR RECIPIENT)</td>
</tr> -->
        </table>
        <table width="100%" cellspacing="0" border-collapse="collapse" class="table header_table" style="margin-top:20px">
            <tr>
                <td style="padding: 5px;" align="center" width="50%" >
                    <?php
                    if (isset($branch[0]->firm_logo) && $branch[0]->firm_logo != "")
                    {
                        ?>
                        <img src="<?php echo base_url('assets/branch_files/') . $branch[0]->branch_id . '/' . $branch[0]->firm_logo; ?>" style="max-width: 150px !important;max-height: 50px !important;" />
                        <?php
                    }
                    else
                    {
                        ?>
                        <b style="font-size: 14px; text-transform: uppercase;">  <?= $branch[0]->firm_name ?></b>
                        <?php
                    }
                    ?>
                </td>
                <td valign="top" width="25%">
                    Purchase Order Date<br>
                    <?php
                    $date     = $data[0]->purchase_order_date;
                    $date_for = date('d-m-Y', strtotime($date));
                    ?>
                    <p style="font-weight:bold"><?php
                        if (isset($data[0]->purchase_order_date))
                        {
                            echo $date_for;
                        }
                        ?></p>
                </td>
                <td valign="top" width="25%">
                    Purchase Order Number<br>
                    <p style="font-weight:bold"><?php
                        if (isset($data[0]->purchase_order_invoice_number))
                        {
                            echo $data[0]->purchase_order_invoice_number;
                        }
                        ?></p>
                </td>
            </tr>
        </table>
        <table width="100%" cellspacing="0" border-collapse="collapse" class="table header_table" style="margin-top:20px">
            <tr>
                <td width="50%" valign="top">
                    <b style="font-size: 11px;">From,</b><br><br>
                    <b style="font-size: 11px; text-transform: capitalize;">
                        <?php
                        if (isset($branch[0]->branch_name))
                        {
                            echo $branch[0]->branch_name;
                        }
                        ?>
                    </b>
                    <br>
                    <?php
                    if (isset($branch[0]->branch_address))
                    {
                        echo str_replace(array(
                                "\r\n",
                                "\\r\\n",
                                "\n",
                                "\\n" ), "<br>", $branch[0]->branch_address);
                        ?>
                        <br>
                        <?php
                    }
                    if (isset($state[0]->state_name))
                    {
                        echo $state[0]->state_name;
                    }
                    ?>,
                    <?php
                    if (isset($country[0]->country_name))
                    {
                        echo $country[0]->country_name;
                        ?>.

                        <?php
                    }
                    if (isset($branch[0]->branch_mobile))
                    {
                        if ($branch[0]->branch_mobile != "" && $branch[0]->branch_mobile != null)
                        {
                            echo '<br>Mobile No : ' . $branch[0]->branch_mobile;
                        }
                        ?>
                        <?php
                        if (isset($branch[0]->branch_land_number))
                        {
                            if ($branch[0]->branch_land_number != "")
                            {
                                echo '<br>Landline No ' . $branch[0]->branch_land_number;
                            }
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_email_address))
                    {
                        if ($branch[0]->branch_email_address != "" || $branch[0]->branch_email_address != null)
                        {
                            echo '<br>E-mail : ' . $branch[0]->branch_email_address;
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_gstin_number))
                    {
                        if ($branch[0]->branch_gstin_number != "")
                        {
                            echo "<br>GSTIN : " . $branch[0]->branch_gstin_number;
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_pan_number))
                    {
                        if ($branch[0]->branch_pan_number != "")
                        {
                            echo "<br>PAN : " . $branch[0]->branch_pan_number;
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_cin_number))
                    {
                        if ($branch[0]->branch_cin_number != "")
                        {
                            echo "<br>CIN : " . $branch[0]->branch_cin_number;
                        }
                    }
                    ?>
                </td>
                <td width="50%" valign="top">
                    <b style="font-size: 11px;">To,</b><br><br>
                    <b style="font-size: 11px; text-transform: capitalize;"><?php
                        if (isset($data[0]->supplier_name))
                        {
                            echo $data[0]->supplier_name;
                        }
                        ?></b>
                    <?php
                    if (isset($data[0]->supplier_gstin_number))
                    {
                        if ($data[0]->supplier_gstin_number != "")
                        {
                            echo "<br/>GSTIN:";
                            echo $data[0]->supplier_gstin_number;
                        }
                    }
                    ?><br>
                        <?php
                        if (isset($data[0]->supplier_address))
                        {
                            echo str_replace(array(
                                    "\r\n",
                                    "\\r\\n",
                                    "\n",
                                    "\\n" ), "<br>", $data[0]->supplier_address);
                        }
                        ?><br>
                    <?php
                    if (isset($data[0]->supplier_city_name))
                    {
                        echo $data[0]->supplier_city_name;
                    }
                    ?>  <?php
                    if (isset($data[0]->supplier_postal_code))
                    {
                        if ($data[0]->supplier_postal_code != "")
                        {
                            echo "-" . $data[0]->supplier_postal_code;
                        }
                    }
                    ?><br>
                    <?php
                    if (isset($data[0]->supplier_state_name))
                    {
                        echo $data[0]->supplier_state_name;
                    }
                    ?>,
                    <?php
                    if (isset($data[0]->supplier_country_name))
                    {
                        echo $data[0]->supplier_country_name;
                    }
                    ?>

                    <?php
                    if (isset($data[0]->supplier_mobile))
                    {
                        if ($data[0]->supplier_mobile != "" && $data[0]->supplier_mobile != null)
                        {
                            echo "<br/>Mobile No:" . $data[0]->supplier_mobile;
                        }
                    }
                    ?>
                    <?php
                    if (isset($data[0]->supplier_email))
                    {
                        if ($data[0]->supplier_email != "" && $data[0]->supplier_email != null)
                        {
                            echo '<br>E-mail : ' . $data[0]->supplier_email;
                        }
                    }
                    ?>

                    <?php
                    if (isset($data[0]->supplier_state_code) && $data[0]->supplier_state_code != 0)
                    {
                        echo '<br>State Code : ' . $data[0]->supplier_state_code;
                    }

                    if (isset($data[0]->place_of_supply))
                    {
                        if ($data[0]->place_of_supply != "" && $data[0]->place_of_supply != null)
                        {
                            echo '<br>Place of Supply : ' . $data[0]->place_of_supply;
                        }
                    }

                    if (isset($data[0]->shipping_address_sa))
                    {
                        if ($data[0]->shipping_address_sa != "" || $data[0]->shipping_address_sa != null)
                        {
                            echo '<br><br><b>Shipping Address</b><br>' . str_replace(array(
                                    "\r\n",
                                    "\\r\\n",
                                    "\n",
                                    "\\n" ), "<br>", $data[0]->shipping_address_sa);
                        }
                    }

                    if (isset($data[0]->shipping_gstin))
                    {
                        if ($data[0]->shipping_gstin != "" || $data[0]->shipping_gstin != null)
                        {
                            echo '<br>Shipping GSTIN : ' . $data[0]->shipping_gstin;
                        }
                    }
                    ?>

                </td>
            </tr>
        </table>

        <table width="100%" cellspacing="0" border-collapse="collapse" class="table header_table" style="margin-top:20px">
            <tr>
                <td valign="top" width="25%">
                    Billing Country<br>
                    <p style="font-weight:bold"><?php
                        if (isset($data_country[0]->country_name))
                        {
                            echo ucfirst($data_country[0]->country_name);
                        }
                        ?></p>
                </td>

                <td valign="top" width="25%">
                    Place of Supply<br>
                    <p style="font-weight:bold"><?php
                        if (isset($data_state[0]->state_name))
                        {
                            echo ucfirst($data_state[0]->state_name);
                        }
                        else
                        {
                            echo "-";
                        }
                        ?></p>
                </td>

                <td valign="top" width="22%">
                    Nature of Supply<br>
                    <p style="font-weight:bold"><?php
                        if (isset($nature_of_supply))
                        {
                            echo $nature_of_supply;
                        }
                        ?></p>
                </td>
                <td valign="top" width="28%">
                    GST Payable on Reverse Charge<br>
                    <p style="font-weight:bold"><?php
                        if (isset($data[0]->purchase_order_gst_payable))
                        {
                            echo ucfirst($data[0]->purchase_order_gst_payable);
                        }
                        ?></p>
                </td>
            </tr>
        </table>

        <table width="100%" cellspacing="0" border-collapse="collapse" class="table"  style="margin-top: 20px;">
            <thead>
                <?php
                if ($igst_tax > 1 || $cgst_tax > 1 || $sgst_tax > 1)
                {
                    ?>
                    <tr>
                        <!-- <th width="4%" rowspan="2" >Sl<br/>No</th> -->
                        <th rowspan="2" >Item Description</th>
                        <!-- <?php if ($dpcount > 0)
                {
                        ?>
                                    <th rowspan="2"  >Description</th>
    <?php } ?>   -->
                        <!-- <th colspan="1" style="border-top: 1px solid #333; border-right: 0">qty</th> -->
                        <th rowspan="2">Quantity</th>
                        <th rowspan="2">Rate</th>
                        <th rowspan="2">Sub <br/> Total</th>
                        <?php if ($dtcount > 0)
                        {
                            ?>
                            <th rowspan="2" >Disc<br />Amt</th>
                        <?php } ?>
                        <th rowspan="2">Taxable <br/>Value</th>
                        <?php
                        if ($igst_tax < 1 && $cgst_tax > 1)
                        {
                            ?>
                            <th colspan="2">CGST</th>
                            <th colspan="2">SGST</th>

                        <?php
                        }
                        else
                        {
                            ?>

                            <th colspan="2" >IGST</th>
                        <?php } ?>
                        <th rowspan="2">Total</th>
                    </tr>
                    <tr>
    <?php
    if ($igst_tax < 1 && $cgst_tax > 1)
    {
        ?>
                            <th>
                                Rate %
                            </th>
                            <th>
                                Amt
                            </th>
                            <th>
                                Rate %
                            </th>
                            <th>
                                Amt
                            </th>

    <?php
    }
    else if ($igst_tax > 1 && $cgst_tax < 1)
    {
        ?>

                            <th>
                                Rate %
                            </th>
                            <th>
                                Amt
                            </th>
    <?php
    }
    else if ($data[0]->purchase_order_billing_state_id == $branch[0]->branch_state_id)
    {
        ?>
                            <th>
                                Rate %
                            </th>
                            <th>
                                Amt
                            </th>
                            <th>
                                Rate %
                            </th>
                            <th>
                                Amt
                            </th>


        <?php
    }
    else
    {
        ?>
                            <th>
                                Rate %
                            </th>
                            <th>
                                Amt
                            </th>
                    <?php }
                    ?>

                    </tr>
    <?php
}
else
{
    ?>
                    <tr>
                       <!--  <th width="4%">Sl<br/>No</th> -->
                        <th>Item Description</th>
                        <!-- <?php if ($dpcount > 0)
                    {
        ?>
                                    <th>Description</th>
                        <?php } ?>   -->
                        <!-- <th colspan="1" style="border-top: 1px solid #333; border-right: 0">qty</th> -->
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Sub <br/> Total</th>
    <?php if ($dtcount > 0)
    {
        ?>
                            <th>Disc<br />Amt</th>
                    <?php } ?>
                        <th>Total</th>
                    </tr>
                <?php } ?>
            </thead>

            <tbody>
                <?php
                $i           = 1;
                $tot         = 0;
                $igst        = 0;
                $cgst        = 0;
                $sgst        = 0;
                $price       = 0;
                $quantity    = 0;
                $discount    = 0;
                $grand_total = 0;
                foreach ($items as $value)
                {
                    ?>
                    <tr>
                       <!--  <td align="center"><?php echo $i; ?></td> -->
                        <!-- <td ><?php echo $value->product_name; ?><br>HSN:<?php echo $value->product_hsn_sac_code; ?></td> -->
                        <?php if ($value->item_type == 'product' || $value->item_type == 'product_inventory')
                        {
                            ?>
                            <td><?php echo $value->product_name; ?><br>HSN:<?php echo $value->product_hsn_sac_code; ?><br><?php echo $value->purchase_order_item_description; ?></td>
    <?php
    }
    else
    {
        ?>
                            <td><?php echo $value->service_name; ?><br>SAC:<?php echo $value->service_hsn_sac_code; ?><br><?php echo $value->purchase_order_item_description; ?></td>
                            <?php } ?>

                        <!-- <?php if ($dpcount > 0)
                        {
                            ?>
                                    <td  ><?php echo $value->purchase_order_item_description; ?></td>
                        <?php } ?> -->

                        <td align="center"><?php
                    echo $value->purchase_order_item_quantity;
                    if ($value->item_type == 'product' || $value->item_type == 'product_inventory')
                    {
                        $unit = explode("-", $value->product_unit);
                        echo " " . $unit[0];
                    }
                    ?></td>
                        <td align="right"><?php echo $value->purchase_order_item_unit_price; ?></td>
                        <td align="right"><?php echo $value->purchase_order_item_sub_total; ?></td>

                        <?php if ($dtcount > 0)
                        {
                            ?>
                            <td align="right"><?php echo $value->purchase_order_item_discount_amount; ?></td>

    <?php } ?>

                        <?php if ($igst_tax < 1 && $cgst_tax > 1)
                        {
                            ?>
                            <td align="right"><?php echo $value->purchase_order_item_taxable_value; ?></td>
                            <td align="center"><?php echo $value->purchase_order_item_cgst_percentage; ?></td>
                            <td align="right"><?php echo $value->purchase_order_item_cgst_amount; ?></td>
                            <td align="center"><?php echo $value->purchase_order_item_sgst_percentage; ?></td>
                            <td align="right"><?php echo $value->purchase_order_item_sgst_amount; ?></td>

                        <?php
                        }
                        else if ($igst_tax > 1 && $cgst_tax < 1)
                        {
                            ?>
                            <td align="right"><?php echo $value->purchase_order_item_taxable_value; ?></td>
                            <td align="center"><?php echo $value->purchase_order_item_igst_percentage; ?></td>
                            <td align="right"><?php echo $value->purchase_order_item_igst_amount; ?></td>

                    <?php }
                    ?>
                    <?php
                    // $a = bcsub($value->sub_total, $value->discount, 2);
                    // $b = bcadd($a, $value->igst_tax, 2);
                    // $c = bcadd($b, $value->cgst_tax, 2);
                    // $d = bcadd($c, $value->sgst_tax, 2);
                    // $final = bcsub($d, $value->tds_amt, 2);
                    ?>
                        <td align="right"><?php echo $value->purchase_order_item_grand_total ?></td>
                    </tr>
                        <?php
                        $i++;
                        $price    = bcadd($price, $value->purchase_order_item_unit_price, 2);
                        $quantity = bcadd($quantity, $value->purchase_order_item_quantity, 2);
                        // $sub_total = bcadd($tot, $value->sub_total, 2);
                        // $discount = bcadd($discount, $value->discount_amt, 2);
                        // $taxable_value = bcadd($discount, $value->taxable_value, 2);
                        // $igst = bcadd($igst, $value->purchase_order_item_igst_amount, 2);
                        // $cgst = bcadd($cgst, $value->purchase_order_item_cgst_amount, 2);
                        // $sgst = bcadd($sgst, $value->purchase_order_item_sgst_amount, 2);

                        $grand_total = bcadd($grand_total, $value->purchase_order_item_grand_total, 2);
                    }
                    ?>
                <tr>


<?php
if ($dpcount > 0)
{
    ?>
                        <th colspan="1" align="right" height="30px;"><b>Total</b></th>

                        <?php
                    }
                    else
                    {
                        ?>
                        <th colspan="1" align="right">Total</th>
                        <?php
                    }
                    ?>

                    <th align="right"><?php echo $quantity; ?></th>
                    <th align="right"><?php echo $price; ?></th>

                    <th align="right"><?php echo $data[0]->purchase_order_sub_total; ?></th>
                    <?php
                    if ($dtcount > 0)
                    {
                        ?>
                        <th><?php echo $data[0]->purchase_order_discount_value; ?></th>

    <?php
}
?>

<?php if ($igst_tax < 1 && $cgst_tax > 1)
{
    ?>
                        <th align="right"><?php echo $data[0]->purchase_order_taxable_value; ?></th>

                        <th align="right" colspan="2"><?php echo bcdiv($data[0]->purchase_order_tax_amount, 2, 2); ?></th>

                        <th align="right" colspan="2"><?php echo bcdiv($data[0]->purchase_order_tax_amount, 2, 2); ?></th>

            <?php
            }
            else if ($igst_tax > 1 && $cgst_tax < 1)
            {
                ?>
                        <th align="right"><?php echo $data[0]->purchase_order_taxable_value; ?></th>

                        <th align="right" colspan="2"><?php echo $data[0]->purchase_order_tax_amount; ?></th>

            <?php }
            ?>

                    <th align="right"><?php echo $grand_total; ?></th>

                </tr>
            </tbody>
        </table>

        <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">

            <?php
            $charges_sub_module = 0;
            foreach ($access_sub_modules as $key => $value)
            {
                if (isset($charges_sub_module_id))
                {
                    if ($charges_sub_module_id == $value->sub_module_id)
                    {
                        $charges_sub_module = 1;
                    }
                }
            }
            if ($charges_sub_module == 1)
            {
                if ($data[0]->total_freight_charge > 0)
                {
                    ?>
                    <tr class="no-bottom-border no-top-border">
                        <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Freight Charge (<?php echo $data[0]->currency_code; ?>)</td>
                        <td align="right" class="footerpad fontH"><?php echo $data[0]->total_freight_charge; ?> /-</td>
                    </tr>
                    <?php
                }
                if ($data[0]->total_insurance_charge > 0)
                {
                    ?>
                    <tr class="no-bottom-border no-top-border">
                        <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Insurance Charge (<?php echo $data[0]->currency_code; ?>)</td>
                        <td align="right" class="footerpad fontH"><?php echo $data[0]->total_insurance_charge; ?> /-</td>
                    </tr>
                    <?php
                }
                if ($data[0]->total_packing_charge > 0)
                {
                    ?>
                    <tr class="no-bottom-border no-top-border">
                        <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Packing & Forwarding Charge (<?php echo $data[0]->currency_code; ?>)</td>
                        <td align="right" class="footerpad fontH"><?php echo $data[0]->total_packing_charge; ?> /-</td>
                    </tr>
                    <?php
                }
                if ($data[0]->total_incidental_charge > 0)
                {
                    ?>
                    <tr class="no-bottom-border no-top-border">
                        <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Incidental Charge (<?php echo $data[0]->currency_code; ?>)</td>
                        <td align="right" class="footerpad fontH"><?php echo $data[0]->total_incidental_charge; ?> /-</td>
                    </tr>
                    <?php
                }
                if ($data[0]->total_inclusion_other_charge > 0)
                {
                    ?>
                    <tr class="no-bottom-border no-top-border">
                        <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Other Inclusive Charge (<?php echo $data[0]->currency_code; ?>)</td>
                        <td align="right" class="footerpad fontH"><?php echo $data[0]->total_inclusion_other_charge; ?> /-</td>
                    </tr>
        <?php
    }
    if ($data[0]->total_exclusion_other_charge > 0)
    {
        ?>
                    <tr class="no-bottom-border no-top-border">
                        <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Other Exclusive Charge (<?php echo $data[0]->currency_code; ?>)</td>
                        <td align="right" class="footerpad fontH">-<?php echo $data[0]->total_exclusion_other_charge; ?> /-</td>
                    </tr>
        <?php
    }
}
?>
        </table>
        <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">

            <tr>
                <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;"><b>Grand Total (<?php echo $data[0]->currency_code; ?>)</td>
                <td align="right" class="footerpad fontH"><b><?php echo $data[0]->purchase_order_grand_total; ?> /-</td>
            </tr>
            <tr>
                <td style="" align="left" colspan="2" height="25">
                    Amount (in Words): <b><?php echo $data[0]->currency_text . " " . $this->numbertowords->convert_number($data[0]->purchase_order_grand_total) . " Only"; ?> </b><br/>
                </td>
            </tr>
        </table>
            <!--    <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">

              <tr>
                <td style="" valign="top" align="Right" height="65px">
                   <b>Authorised Signature: </b><b><br/><br/></b>
               </td>
           </tr>
        </table> -->

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
            $this->load->view('note_template/display_note');
        }
        ?>
        <br/>
<?php
if (isset($access_common_settings[0]->invoice_footer))
{
    ?>
            <table>
                <tr>
                    <td style="border:0px;text-align: center;font-size: 11px;"><?= $access_common_settings[0]->invoice_footer ?></td>
                </tr>
            </table>
    <?php
}
?>
    </body>
</html>
<script>window.print();</script>
