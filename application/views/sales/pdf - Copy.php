<!DOCTYPE html>
<html>
    <head>
        <title>
            Sales Invoice
        </title>
        <style type="text/css">

            .table {
                margin: 0px;
                font-size: 11px;
            }
            .table, th, td {
                border: 1px #333 !important;
                border-width: thin;
                font-size: 11px;
                padding: 2px 4px;
                border-collapse: collapse !important;

            }
            tr.noBorder td {
              border:none !important;

            }
            p
            {
                font-size: 12px;
            }
            .invoice-header
            {
                font-size: 20px;
            }
            .center
            {
                text-align: center;
            }
            .right
            {
                text-align: right;
            }
            .left
            {
                text-align: left;
            }
        </style>	</head>
    <body>
        <table width="100%" class="table">
            <tr>
                <td class="center invoice-header">Tax Invoice</td>

            </tr>
            <tr class="noBorder">
                <td class="right">(<?php echo $invoice_type; ?>)</td>

            </tr>
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
                    Invoice Date<br>
                    <?php
                    $date     = $data[0]->sales_date;
                    $date_for = date('d-m-Y', strtotime($date));
                    ?>
                    <p style="font-weight:bold"><?php
                        if (isset($data[0]->sales_date))
                        {
                            echo $date_for;
                        }
                        ?></p>
                </td>
                <td valign="top" width="25%">
                    Invoice Number<br>
                    <p style="font-weight:bold"><?php
                        if (isset($data[0]->sales_invoice_number))
                        {
                            echo $data[0]->sales_invoice_number;
                        }
                        ?></p>
                </td>
            </tr>
        </table>

        <table width="100%" cellspacing="0" border-collapse="collapse" class="table header_table" style="margin-top:20px">
            <tr>

                <td width="50%" valign="top">


                    <b style="font-size: 11px;">From,</b><br><br>
                    <b style="font-size: 11px; text-transform: capitalize;"><?php
                        if (isset($branch[0]->branch_name))
                        {
                            echo $branch[0]->branch_name;
                        }
                        ?></b>
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
                    
                    if (isset($branch[0]->branch_mobile))
                    {
                        if ($branch[0]->branch_mobile != "" || $branch[0]->branch_mobile != null)
                        {
                            echo '<br>Mobile No : ' . $branch[0]->branch_mobile;
                        }
                        // echo 'Contact No : ' . $branch[0]->branch_mobile;
                        ?>
                        <?php
                        if (isset($branch[0]->branch_land_number))
                        {
                            if ($branch[0]->branch_land_number != "")
                            {
                                echo '<br>Landline No : ' . $branch[0]->branch_land_number;
                            }
                        }
                        ?>

                        <?php
                    }
                    if (isset($branch[0]->branch_email_address))
                    {
                        if ($branch[0]->branch_email_address != "")
                        {
                            echo '<br>E-mail : ' . $branch[0]->branch_email_address;
                        }
                        ?>


                        <?php
                    }
                    if (isset($branch[0]->branch_gstin_number))
                    {
                        if ($branch[0]->branch_gstin_number != "" || $branch[0]->branch_gstin_number != null)
                        {
                            echo "<br>GSTIN : " . $branch[0]->branch_gstin_number;
                        }
                        ?>
                        <?php
                    }
                    ?>
                    <?php
                    if (isset($branch[0]->branch_pan_number) && $branch[0]->branch_pan_number != "")
                    {
                        if ($branch[0]->branch_pan_number != "" || $branch[0]->branch_pan_number != null)
                        {
                            echo "<br />PAN : " . $branch[0]->branch_pan_number;
                        }
                        ?>

                        <?php
                    }
                    ?>

                    <?php
                    if (isset($branch[0]->branch_import_export_code_number))
                    {
                        if ($branch[0]->branch_import_export_code_number != "" || $branch[0]->branch_import_export_code_number != null)
                        {
                            echo "<br />IEC : " . $branch[0]->branch_import_export_code_number;
                        }
                        ?>


                        <?php
                    }

                    if (isset($branch[0]->branch_lut_number))
                    {
                        if ($branch[0]->branch_lut_number != "" || $branch[0]->branch_lut_number != null)
                        {
                            echo "<br />LUT : " . $branch[0]->branch_lut_number;
                        }
                        ?>

                        <?php
                    }
                    if (isset($branch[0]->branch_cin_number))
                    {
                        if ($branch[0]->branch_cin_number != "" || $branch[0]->branch_cin_number != null)
                        {
                            echo "<br />CIN : " . $branch[0]->branch_cin_number;
                        }
                    }
                    ?>

                </td>
                <td width="50%" valign="top">

                    <b style="font-size: 11px;">To,</b><br><br>
                    <b style="font-size: 11px; text-transform: capitalize;"><?php
                        if (isset($data[0]->customer_name))
                        {
                            echo $data[0]->customer_name;
                        }
                        ?></b>
                    <?php
                    if (isset($data[0]->customer_gstin_number))
                    {
                        if ($data[0]->customer_gstin_number != "")
                        {
                            echo "<br/>GSTIN : ";
                            echo $data[0]->customer_gstin_number;
                        }
                    }
                    ?><br>
                        <?php
                        if (isset($data[0]->customer_address) && $data[0]->customer_address!="")
                        {
                            echo str_replace(array(
                                    "\r\n",
                                    "\\r\\n",
                                    "\n",
                                    "\\n" ), "<br>", $data[0]->customer_address);
                        }
                        else
                        {
                            echo str_replace(array(
                                    "\r\n",
                                    "\\r\\n",
                                    "\n",
                                    "\\n" ), "<br>", $data[0]->shipping_address);

                        }
                        ?><br>
                   
                    <?php
                    if (isset($data[0]->customer_mobile))
                    {
                        if ($data[0]->customer_mobile != "")
                        {
                            echo '<br>Mobile No : ' . $data[0]->customer_mobile;
                        }
                    }
                    ?>
                    <?php
                    if (isset($data[0]->customer_email))
                    {
                        if ($data[0]->customer_email != "")
                        {
                            echo '<br>E-mail : ' . $data[0]->customer_email;
                        }
                    }
                    ?>

                    <?php
                    if (isset($data[0]->place_of_supply))
                    {
                        if ($data[0]->place_of_supply != "" || $data[0]->place_of_supply != null)
                        {
                            echo '<br>Place of Supply : ' . $data[0]->place_of_supply;
                        }
                    }

                    if (isset($data[0]->shipping_address))
                    {
                        if ($data[0]->shipping_address != "" || $data[0]->shipping_address != null)
                        {
                            echo '<br><br><b>Shipping Address</b><br>' . str_replace(array(
                                    "\r\n",
                                    "\\r\\n",
                                    "\n",
                                    "\\n" ), "<br>", $data[0]->shipping_address);
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
                <td valign="top" width="34%">
                    Billing Country<br>
                    <p style="font-weight:bold"><?php
                        if (isset($data_country[0]->country_name))
                        {
                            echo ucfirst($data_country[0]->country_name);
                        }
                        ?></p>
                </td>

            
                <td valign="top" width="33%">
                    Nature of Supply<br>
                    <p style="font-weight:bold"><?php
                        if (isset($nature_of_supply))
                        {
                            echo $nature_of_supply;
                            // echo str_replace("_", " ", $type_of_supply);
                        }
                        ?></p>
                </td>
                <td valign="top" width="33%">
                    GST Payable on Reverse Charge<br>
                    <p style="font-weight:bold"><?php
                        if (isset($data[0]->sales_gst_payable))
                        {
                            echo ucfirst($data[0]->sales_gst_payable);
                        }
                        ?></p>
                </td>
            </tr>
        </table>

        <table width="100%" cellspacing="0" border-collapse="collapse" class="table"  style="margin-top: 20px;">
            
                                    <thead>
                                        <tr>
                                            <th width="4%" rowspan="2" style="text-align: center;">Sl<br/>No
                                            </th>
                                        <th rowspan="2">Particulars</th>
                                         <th rowspan="2" style="text-align: center;">Quantity</th>
                                         <th rowspan="2" style="text-align: center;">Rate</th>
                                         <th rowspan="2" style="text-align: center;">Subtotal</th> 
                                       <?php if ($discount_exist > 0)
                                                {
                                                 ?>
                                         <th rowspan="2" style="text-align: center;">Discount</th>
                                        <?php } ?>
                                        <?php if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 ))
                                                {
                                                 ?>
                                         <th rowspan="2" style="text-align: center;">Taxable Value</th>
                                        <?php } ?> 


                                        <?php if ($tax_exist > 0)
                                                {
                                                    ?>
                                         <th colspan="2" style="text-align: center;">Tax</th>
                                        <?php }
                                        elseif ($igst_exist > 0) {
                                           ?>
                                           <th colspan="2" style="text-align: center;">IGST</th>
                                           <?php
                                         } 
                                         elseif ($cgst_exist > 0 || $sgst_exist > 0) {
                                           ?>
                                           <th colspan="2" style="text-align: center;">CGST</th>
                                           <th colspan="2" style="text-align: center;">SGST</th>
                                           <?php
                                         } ?>
                                        <th rowspan="2" style="text-align: center;">Total</th>

                                        </tr>

                                            <tr>
                                                <?php if ($tax_exist > 0)
                                                {
                                                    ?>
                                         <th  style="text-align: center;">Rate(%)</th>
                                         <th  style="text-align: center;">Amount</th>
                                        <?php }
                                        elseif ($igst_exist > 0) {
                                           ?>
                                        <th  style="text-align: center;">Rate(%)</th>
                                         <th  style="text-align: center;">Amount</th>
                                           <?php
                                         } 
                                         elseif ($cgst_exist > 0 || $sgst_exist > 0) {
                                           ?>
                                           <th  style="text-align: center;">Rate(%)</th>
                                         <th  style="text-align: center;">Amount</th>
                                         <th  style="text-align: center;">Rate(%)</th>
                                         <th  style="text-align: center;">Amount</th>
                                           <?php
                                         } ?>
                                            </tr>


                                    </thead>
                                    <tbody>
                                            <?php
                                            $i           = 1;
                                            $quantity    = 0;
                                            $price       = 0;
                                            $grand_total = 0;
                                            foreach ($items as $value)
                                            {
                                                ?>
                                            <tr>
                                                <td style="text-align: center;"><?php echo $i; ?></td>
    <?php if ($value->item_type == 'product' || $value->item_type == 'product_inventory')
    {
        ?>
                                        <td style="text-align: left;">
                                            <?php echo $value->product_name; ?>
                                        <br>HSN:<?php echo $value->product_hsn_sac_code; ?>
                                        <?php if(isset($value->sales_item_description) && $value->sales_item_description !="")
                                        {
                                            echo "<br/>";
                                            echo $value->sales_item_description;
                                        } 
                                        ?>
                                        </td>
                                                <?php
                                                }
         else
          {
            ?>
                                     <td style="text-align: left;"><?php echo $value->service_name; ?><br>SAC:<?php echo $value->service_hsn_sac_code; ?>
                                 <?php if(isset($value->sales_item_description) && $value->sales_item_description !="")
                                        {
                                            echo "<br/>";
                                            echo $value->sales_item_description;
                                        } 
                                        ?>
                                    </td>

    <?php } ?>
                                    <td style="text-align: center;"><?php echo $value->sales_item_quantity; 
                                    if ($value->item_type == 'product' || $value->item_type == 'product_inventory')
                                    {
                                        $unit = explode("-", $value->product_unit);
                                        echo " " . $unit[0];
                                    }
                                    ?></td>
                                     <td style="text-align: right;"><?php echo $value->sales_item_unit_price; ?></td>
                                     <td style="text-align: right;"><?php echo $value->sales_item_sub_total; ?></td>

                                     <?php if ($discount_exist > 0)
                                                {
                                                 ?>
                                      <td style="text-align: right;"><?php echo $value->sales_item_discount_amount; ?></td>
                                        <?php } ?>
                                        <?php if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 ))
                                                {
                                                 ?>
                                        <td style="text-align: right;"><?php echo $value->sales_item_taxable_value; ?></td>
                                        <?php } ?> 


                                        <?php if ($tax_exist > 0)
                                                {
                                                    ?>
                                       <td style="text-align: center;"><?php echo $value->sales_item_tax_percentage; ?></td>
                                        <td style="text-align: right;"><?php echo $value->sales_item_tax_amount; ?></td>
                                        <?php }
                                        elseif ($igst_exist > 0) {
                                           ?>
                                         <td style="text-align: center;"><?php echo $value->sales_item_igst_percentage; ?></td>
                                                    <td style="text-align: right;"><?php echo $value->sales_item_igst_amount; ?></td>
                                           <?php
                                         } 
                                         elseif ($cgst_exist > 0 || $sgst_exist > 0) {
                                           ?>
                                          <td style="text-align: center;"><?php echo $value->sales_item_cgst_percentage; ?></td>
                                                    <td style="text-align: right;"><?php echo $value->sales_item_cgst_amount; ?></td>
                                                    <td style="text-align: center;"><?php echo $value->sales_item_sgst_percentage; ?></td>
                                                    <td style="text-align: right;"><?php echo $value->sales_item_sgst_amount; ?></td>
                                           <?php
                                         } ?>

                                       <td style="text-align: right;"><?php echo $value->sales_item_grand_total ?></td>
                                    </tr>
                                                <?php
                                                $i++;
                                $quantity    = bcadd($quantity, $value->sales_item_quantity, 2);
                                $price       = bcadd($price, $value->sales_item_unit_price, 2);
                                   
                                $grand_total = bcadd($grand_total, $value->sales_item_grand_total, 2);
                                            }
                                            ?>
                                           <tr>
                                               
                                        
                                        <th colspan="2"></th>
                                         <th  style="text-align: center;"><?= $quantity ?></th>
                                         <th  style="text-align: center;"><?= $price ?></th>
                                         <th  style="text-align: center;"><?= $data[0]->sales_sub_total ?></th> 
                                       <?php if ($discount_exist > 0)
                                                {
                                                 ?>
                                         <th  style="text-align: center;"><?= $data[0]->sales_discount_amount ?></th>
                                        <?php } ?>
                                        <?php if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 ))
                                                {
                                                 ?>
                                         <th  style="text-align: center;"><?= $data[0]->sales_taxable_value ?></th>
                                        <?php } ?> 


                                        <?php if ($tax_exist > 0)
                                                {
                                                    ?>
                                         <th colspan="2" style="text-align: center;"><?= $data[0]->sales_tax_amount ?></th>
                                        <?php }
                                        elseif ($igst_exist > 0) {
                                           ?>
                                           <th colspan="2" style="text-align: center;"><?= $data[0]->sales_igst_amount ?></th>
                                           <?php
                                         } 
                                         elseif ($cgst_exist > 0 || $sgst_exist > 0) {
                                           ?>
                                           <th colspan="2" style="text-align: center;"><?= $data[0]->sales_cgst_amount ?></th>
                                           <th colspan="2" style="text-align: center;"><?= $data[0]->sales_sgst_amount ?></th>
                                           <?php
                                         } ?>
                                        <th  style="text-align: center;"><?= bcadd($data[0]->sales_grand_total,$data[0]->round_off_amount,2); ?></th>

                                        

                                           </tr> 
                                       
                                    </tbody>
                                
        </table>

        <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">

            <?php
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
        
        ?>
        </table>

        <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">

            <tr>
                <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;"><b>Grand Total (<?php echo $data[0]->currency_code; ?>)</td>
                <td align="right" class="footerpad fontH"><b><?php echo $data[0]->sales_grand_total; ?> /-</td>
            </tr>
            <tr>
                <td style="" align="left" colspan="2" height="25">
                    Amount (in Words): <b><?php echo $data[0]->currency_text . " " . $this->numbertowords->convert_number($data[0]->sales_grand_total) . " Only"; ?> </b><br/>
                </td>
            </tr>
            <?php
            if ($data[0]->round_off_amount > 0 || $data[0]->round_off_amount < 0)
                            {
                                ?>
                                  <tr>
                            <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;"><b>Round Off (<?php echo $data[0]->currency_code; ?>)</td>
                <td align="right" class="footerpad fontH"><b><?php echo $data[0]->round_off_amount; ?> /-</td>
                                   </tr>

                                <?php } ?>
        </table>

        <?php
        if (isset($data[0]->sales_type_of_supply) && $data[0]->sales_type_of_supply != "regular")
        {
            ?>
            <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">
                <tr>
                    <td style="" valign="top">
    <?php
    if (isset($data[0]->sales_type_of_supply) && $data[0]->sales_type_of_supply == "export_with_payment")
    {
        ?>
                            <p><b>NOTE : </b>SUPPLY MEANT FOR EXPORT ON PAYMENT OF INTEGRATED TAX</p>
                <?php
            }
            else if (isset($data[0]->sales_type_of_supply) && $data[0]->sales_type_of_supply == "export_without_payment")
            {
                ?>
                            <p><b>NOTE : </b>SUPPLY MEANT FOR EXPORT UNDER BOND OR LETTER OF UNDERTAKING WITHOUT PAYMENT OF INTEGRATED TAX</p>
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
if (isset($access_common_settings[0]->invoice_footer))
{
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
