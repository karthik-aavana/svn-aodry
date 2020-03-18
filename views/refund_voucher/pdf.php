<!DOCTYPE html>
<html>
    <head>
        <title>
            Refund Voucher
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
        </style>	</head>
    <body>
        <table width="100%" class="table">
            <tr style="border: 0px;">
                <td style="border: 0px;text-align: center; font-size: 20px">Refund Voucher</td>

            </tr>
            <!-- <tr style="border: 0px;">
   <td style="border: 0px;text-align: right;">(<?php echo $invoice_type; ?>)</td>

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
                    Voucher Date<br>
                    <?php
                    $date     = $data[0]->voucher_date;
                    $date_for = date('d-m-Y', strtotime($date));
                    ?>
                    <p style="font-weight:bold"><?php
                        if (isset($data[0]->voucher_date))
                        {
                            echo $date_for;
                        }
                        ?></p>
                </td>
                <td valign="top" width="25%">
                    Voucher Number<br>
                    <p style="font-weight:bold"><?php
                        if (isset($data[0]->voucher_number))
                        {
                            echo $data[0]->voucher_number;
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
                    if (isset($state[0]->state_name))
                    {
                        echo $state[0]->state_name;
                    }
                    ?>, <?php
                    if (isset($country[0]->country_name))
                    {
                        echo $country[0]->country_name;
                        ?>.

                        <?php
                    }
                    if (isset($branch[0]->branch_mobile))
                    {
                        if ($branch[0]->branch_mobile != "" || $branch[0]->branch_mobile != null)
                        {
                            echo '<br>Mobile No : ' . $branch[0]->branch_mobile;
                        }
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
                            echo "<br />PAN : " . $branch[0]->branch_pan_number;
                        }
                        ?>

                        <?php
                    }
                    if (isset($branch[0]->branch_cin_number))
                    {
                        if ($branch[0]->branch_cin_number != "")
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
                            echo "<br/>GSTIN:";
                            echo $data[0]->customer_gstin_number;
                        }
                    }
                    ?><br>
                        <?php
                        if (isset($data[0]->customer_address))
                        {
                            echo str_replace(array(
                                    "\r\n",
                                    "\\r\\n",
                                    "\n",
                                    "\\n" ), "<br>", $data[0]->customer_address);
                        }
                        ?><br>
                    <?php
                    if (isset($data[0]->customer_city_name))
                    {
                        echo $data[0]->customer_city_name;
                    }
                    ?> <?php
                    if (isset($data[0]->customer_postal_code))
                    {
                        if ($data[0]->customer_postal_code != "")
                        {
                            echo "-" . $data[0]->customer_postal_code;
                        }
                    }
                    ?><br>
                    <?php
                    if (isset($data[0]->customer_state_name))
                    {
                        echo $data[0]->customer_state_name;
                    }
                    ?>,
                    <?php
                    if (isset($data[0]->customer_country_name))
                    {
                        echo $data[0]->customer_country_name;
                    }
                    ?>

                    <?php
                    if (isset($data[0]->customer_mobile))
                    {
                        if ($data[0]->customer_mobile != "")
                        {
                            echo "<br>Mobile No: " . $data[0]->customer_mobile;
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
                    if (isset($data[0]->customer_state_code) && $data[0]->customer_state_code != 0)
                    {
                        echo '<br>State Code : ' . $data[0]->customer_state_code;
                    }

                    if (isset($data[0]->place_of_supply))
                    {
                        if ($data[0]->place_of_supply != "")
                        {
                            echo '<br>Place of Supply : ' . $data[0]->place_of_supply;
                        }
                    }
                    ?>



            </tr>



        </table>

        <br>
        <table width="100%" cellspacing="0" border-collapse="collapse" class="table"  style="margin-top: 20px;">
            <thead>
                <tr>
                    <!-- <th width="4%" rowspan="2" >Sl<br/>No</th> -->
                    <th rowspan="2" >Item Description</th>
                    <!--  <?php
                    if ($dpcount > 0)
                    {
                        ?>
                                                 <th rowspan="2"  >Description</th>
                    <?php } ?>  -->
                     <!-- <th colspan="1" style="border-top: 1px solid #333; border-right: 0">qty</th> -->
                     <!-- <th rowspan="2">Quantity</th> -->
                    <th rowspan="2">Rate</th>
                    <!-- <th rowspan="2">Sub <br/> Total</th>

                    <th rowspan="2">Taxable <br/>Value</th> -->
                    <?php
                    if ($igst_tax < 1 && $cgst_tax > 1)
                    {
                        ?>
                        <th colspan="2">CGST</th>
                        <th colspan="2">SGST</th>

                        <?php
                    }
                    else if ($igst_tax > 1 && $cgst_tax < 1)
                    {
                        ?>

                        <th colspan="2" >IGST</th>
                        <?php
                    }
                    else if ($data[0]->customer_state_code == $branch[0]->branch_state_code)
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
                    <?php }
                    ?>
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
                            Amount
                        </th>
                        <th>
                            Rate %
                        </th>
                        <th>
                            Amount
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
                            Amount
                        </th>
                        <?php
                    }
                    else if ($data[0]->billing_state_id == $branch[0]->branch_state_id)
                    {
                        ?>
                        <th>
                            Rate %
                        </th>
                        <th>
                            Amount
                        </th>
                        <th>
                            Rate %
                        </th>
                        <th>
                            Amount
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
                            Amount
                        </th>
                    <?php }
                    ?>

                </tr>
            </thead>

            <tbody>
                <?php
                $i    = 1;
                $tot  = 0;
                $igst = 0;
                $cgst = 0;
                $sgst = 0;

// $quantity = 0;
                $price = 0;

                $grand_total = 0;
                foreach ($items as $value)
                {
                    ?>
                    <tr>
                       <!--  <td align="center"><?php echo $i; ?></td> -->
                        <?php
                        if ($value->item_type == 'product' || $value->item_type == 'product_inventory')
                        {
                            ?>
                            <td ><?php echo $value->product_name; ?><br>HSN/SAC: <?php echo $value->product_hsn_sac_code; ?><br><?php echo $value->item_description; ?></td>
                            <?php
                        }elseif($value->item_type == 'advance'){
                            ?>
                            <td ><?php echo $value->product_name; ?></td>
                            <?php
                        }else{
                            ?>
                            <td ><?php echo $value->service_name; ?><br>HSN/SAC: <?php echo $value->service_hsn_sac_code; ?><br><?php echo $value->item_description; ?></td>
                        <?php } ?>

                        <!--  <?php
                        if ($dpcount > 0)
                        {
                            ?>
                                                     <td  ><?php echo $value->item_description; ?></td>
                        <?php } ?> -->

                        <td align="right"><?php echo $value->item_sub_total; ?></td>
                        <!-- <td align="right"><?php echo $value->item_quantity; ?></td> -->
                        <!-- <td align="right"><?php echo $value->item_sub_total; ?></td> -->

                        <!--  <?php
                        if ($dtcount > 0)
                        {
                            ?>
                                                     <td align="right"></td>

                        <?php } ?> -->

                                            <!-- <td align="right"><?php echo $value->item_sub_total; ?></td> -->
                        <?php
                        if ($value->item_igst_amount < 1 && $value->item_cgst_amount > 1)
                        {
                            ?>
                            <td align="center"><?php echo $value->item_cgst_percentage; ?></td>
                            <td align="right"><?php echo $value->item_cgst_amount; ?></td>
                            <td align="center"><?php echo $value->item_sgst_percentage; ?></td>
                            <td align="right"><?php echo $value->item_sgst_amount; ?></td>

                            <?php
                        }
                        else if ($value->item_igst_amount > 1 && $value->item_cgst_amount < 1)
                        {
                            ?>
                            <td align="center"><?php echo $value->item_igst_percentage; ?></td>
                            <td align="right"><?php echo $value->item_igst_amount; ?></td>

                            <?php
                        }
                        else if ($data[0]->billing_state_id == $branch[0]->branch_state_id)
                        {
                            ?>
                            <td align="center"><?php echo $value->item_cgst_percentage; ?></td>
                            <td align="right"><?php echo $value->item_cgst_amount; ?></td>
                            <td align="center"><?php echo $value->item_sgst_percentage; ?></td>
                            <td align="right"><?php echo $value->item_sgst_amount; ?></td>

                            <?php
                        }
                        else
                        {
                            ?>
                            <td align="center"><?php echo $value->item_igst_percentage; ?></td>
                            <td align="right"><?php echo $value->item_igst_amount; ?></td>

                        <?php }
                        ?>
                        <?php
                        // $a = bcsub($value->sub_total, $value->discount, 2);
                        // $b = bcadd($a, $value->igst_tax, 2);
                        // $c = bcadd($b, $value->cgst_tax, 2);
                        // $d = bcadd($c, $value->sgst_tax, 2);
                        // $final = bcsub($d, $value->tds_amt, 2);
                        ?>
                        <td align="right"><?php echo $value->item_grand_total ?></td>
                    </tr>
                    <?php
                    $i++;
                    // $quantity = bcadd($quantity, $value->item_quantity, 2);
                    $price = bcadd($price, $value->item_sub_total, 2);
                    // $sub_total = bcadd($tot, $value->sub_total, 2);
                    // $discount = bcadd($discount, $value->discount_amt, 2);
                    // $taxable_value = bcadd($discount, $value->taxable_value, 2);
                    // $igst = bcadd($igst, $value->sales_item_igst_amount, 2);
                    // $cgst = bcadd($cgst, $value->sales_item_cgst_amount, 2);
                    // $sgst = bcadd($sgst, $value->sales_item_sgst_amount, 2);

                    $grand_total = bcadd($grand_total, $value->item_grand_total, 2);
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


  <!-- <th align="right"><?php echo $quantity; ?></th> -->
                    <th align="right"><?php echo $data[0]->voucher_sub_total; ?></th>
                    <!-- <th align="right"><?php echo $data[0]->voucher_sub_total; ?></th> -->

                    <?php
                    if ($dtcount > 0)
                    {
                        ?>
                          <!--  <th><?php echo $data[0]->sales_discount_value; ?></th> -->

                        <?php
                    }
                    ?>

                    <!-- <th align="right"><?php echo $data[0]->voucher_sub_total; ?></th> -->

                    <?php
                    if ($data[0]->voucher_igst_amount < 1 && $data[0]->voucher_cgst_amount > 1)
                    {
                        ?>

                        <th align="right" colspan="2"><?php echo bcdiv($data[0]->voucher_tax_amount, 2, 2); ?></th>

                        <th align="right" colspan="2"><?php echo bcdiv($data[0]->voucher_tax_amount, 2, 2); ?></th>

                        <?php
                    }
                    else if ($data[0]->voucher_igst_amount > 1 && $data[0]->voucher_cgst_amount < 1)
                    {
                        ?>
                        <th align="right" colspan="2"><?php echo $data[0]->voucher_tax_amount; ?></th>

                        <?php
                    }
                    else if ($data[0]->billing_state_id == $branch[0]->branch_state_id)
                    {
                        ?>
                        <th align="right" colspan="2"><?php echo bcdiv($data[0]->voucher_tax_amount, 2, 2); ?></th>

                        <th align="right" colspan="2"><?php echo bcdiv($data[0]->voucher_tax_amount, 2, 2); ?></th>
                        <?php
                    }
                    else
                    {
                        ?>
                        <th align="right" colspan="2"><?php echo $data[0]->voucher_tax_amount; ?></th>
                    <?php }
                    ?>

                    <th align="right"><?php echo $grand_total ?></th>

                </tr>
            </tbody>
        </table>

        <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">

            <tr>
                <td align="right" class="footerpad fontH"><b>Receipt Amount: <?= $data[0]->currency_code ?> <?php echo $data[0]->receipt_amount; ?> /-</td>
            </tr>
            <tr>
                <td style="" align="left" height="25">
                    Amount (in Words): <b><?php echo $data[0]->currency_text . " " . $this->numbertowords->convert_number($data[0]->receipt_amount) . " Only"; ?> </b><br/>
                </td>
            </tr>
        </table>
          <!--      <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">

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
                    <td colspan="14" style="border:0px;text-align: center;font-size: 13px;"><?php echo $access_common_settings[0]->invoice_footer; ?></td>
                </tr>
            </table>
            <?php
        }
        ?>
    </body>
</html>


