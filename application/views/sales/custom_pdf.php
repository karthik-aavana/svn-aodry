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

                <td style="border: 0px;text-align: center; font-size: 20px">Bill</td>



            </tr>



        </table>





        <table width="100%" cellspacing="0" border-collapse="collapse" class="table header_table" style="margin-top:20px">

            <tr>



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

                    ?>  <?php

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

                    if (isset($data[0]->customer_state_code) && $data[0]->customer_state_code != 0)

                    {

                        echo '<br>State Code : ' . $data[0]->customer_state_code;

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

                <td width="50%" valign="top">

                    Sales Date<br>

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

                    <br>

                    Sales Number<br>

                    <p style="font-weight:bold"><?php

                        if (isset($data[0]->sales_invoice_number))

                        {

                            echo $data[0]->sales_invoice_number;

                        }

                        ?></p>



                </td>













        </table>







        <table width="100%" cellspacing="0" border-collapse="collapse" class="table"  style="margin-top: 20px;">

            <thead>



                <tr>



                    <th >Item  Description</th>



                    <th>Quantity</th>

                    <th>Rate</th>





                    <th>Total</th>

                </tr>



            </thead>



            <tbody>

                <?php

                $i           = 1;

                $tot         = 0;

                $igst        = 0;

                $cgst        = 0;

                $sgst        = 0;

                $quantity    = 0;

                $price       = 0;

                $discount    = 0;

                $grand_total = 0;

                foreach ($items as $value)

                {

                    ?>

                    <tr>

                        <!-- <td align="center"><?php echo $i; ?></td> -->

                        <?php if ($value->item_type == 'product' || $value->item_type == 'product_inventory')

                        {

                            ?>

                            <td ><?php echo $value->product_name; ?><br>HSN/SAC: <?php echo $value->product_hsn_sac_code; ?><br><?php echo $value->sales_item_description; ?></td>

                        <?php

                        }

                        else

                        {

                            ?>

                            <td ><?php echo $value->service_name; ?><br>HSN/SAC: <?php echo $value->service_hsn_sac_code; ?><br><?php echo $value->sales_item_description; ?></td>

                        <?php } ?>



                        <!-- <?php if ($dpcount > 0)

                    {

                        ?>

                                    <td  ><?php echo $value->sales_item_description; ?></td>

                            <?php } ?> -->



                        <td align="center"><?php

                            echo $value->sales_item_quantity;

                            if ($value->item_type == 'product' || $value->item_type == 'product_inventory')

                            {

                                $unit = explode("-", $value->product_unit);

                                echo " " . $unit[0];

                            }

                            ?>



                        </td>

                        <td align="right"><?php echo $value->sales_item_unit_price; ?></td>







                        <td align="right"><?php echo $value->sales_item_grand_total ?></td>

                    </tr>

                    <?php

                    $i++;

                    $quantity = bcadd($quantity, $value->sales_item_quantity, 2);

                    $price    = bcadd($price, $value->sales_item_unit_price, 2);

                    // $sub_total = bcadd($tot, $value->sub_total, 2);

                    // $discount = bcadd($discount, $value->discount_amt, 2);

                    // $taxable_value = bcadd($discount, $value->taxable_value, 2);

                    // $igst = bcadd($igst, $value->sales_item_igst_amount, 2);

                    // $cgst = bcadd($cgst, $value->sales_item_cgst_amount, 2);

                    // $sgst = bcadd($sgst, $value->sales_item_sgst_amount, 2);



                    $grand_total = bcadd($grand_total, $value->sales_item_grand_total, 2);

                }

                ?>

                <tr>





                    <th colspan="1" align="right">Total</th>





                    <th align="right"><?php echo $quantity; ?></th>

                    <th align="right"><?php echo $price; ?></th>









                    <th align="right"><?php echo $grand_total ?></th>



                </tr>

            </tbody>

        </table>









    </body>

</html>

