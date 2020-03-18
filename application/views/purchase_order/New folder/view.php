<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');
?>
<!-- <script type="text/javascript">
    function delete_id(id)
    {
        if (confirm('<?php echo $this->lang->line('product_delete_conform'); ?>'))
        {
            window.location.href = '<?php echo base_url('purchase_order/delete/'); ?>' + id;
        }
    }
</script> -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?php echo base_url('purchase_order'); ?>">Purchase Order</a></li>
                <li class="active">Purchase Order Details</li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Purchase Order Details</h3>
                        <span class="btn btn-sm btn-default pull-right" id="cancel" style="margin-left: 1%" onclick="cancel('purchase_order')">Back</span>
                        <?php
                        if ($data[0]->purchase_id == null)
                        {
                            ?>
                            <a class="btn btn-sm btn-info pull-right"  href="<?php echo base_url('purchase_order/add_invoice/'); ?><?php echo $data[0]->purchase_order_id; ?>">Generate Invoice</a>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="box-body">
                        <div class="well well-sm">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="col-sm-12">


                                        <div class="col-sm-10" >
                                            <!--  <h4 style="text-transform: capitalize;">From</h4> -->
                                        </div>
                                    </div>

                                    <?php
                                    if (isset($branch[0]->firm_name) && $branch[0]->firm_name != "")
                                    {
                                        ?>
                                        <b style="font-size: 15px;">From,</b><br><br>
                                        <div class="col-sm-12">
                                            <div class="col-sm-2">
                                                <i class="fa fa-3x fa-building-o padding010 text-muted"></i>
                                            </div>
                                            <!-- <h4 class="col-sm-3" style="text-transform: capitalize;">branch</h4> -->
                                            <!-- <h4 class="col-sm-1" style="text-transform: capitalize;">:</h4> -->

                                            <h4 style="text-transform: capitalize;"><?= $branch[0]->firm_name ?></h4>

                                        </div>

                                        <?php
                                    }
                                    ?>
                                    <br/>
                                    <?php
                                    if (isset($branch[0]->branch_address) && $branch[0]->branch_address != "")
                                    {
                                        ?>
                                        <div class="col-sm-12">
                                            <label class="col-sm-2">Address </label>
                                            <label class="col-sm-1">:</label>
                                            <div class="col-sm-9" >
                                                <?php
                                                echo str_replace(array(
                                                        "\r\n",
                                                        "\\r\\n",
                                                        "\n",
                                                        "\\n" ), "<br>", $branch[0]->branch_address);
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <br/>
                                    <?php
                                    if (isset($branch[0]->branch_city_name) && $branch[0]->branch_city_name != "")
                                    {
                                        ?>
                                        <div class="col-sm-12">
                                            <label class="col-sm-2">Location </label>
                                            <label class="col-sm-1">:</label>
                                            <div class="col-sm-9" >
                                                <?php
                                                if (isset($branch[0]->branch_city_name) && $branch[0]->branch_city_name != "")
                                                {
                                                    echo $branch[0]->branch_city_name . ",";
                                                }
                                                if (isset($branch[0]->branch_state_name))
                                                {
                                                    echo $branch[0]->branch_state_name . ",";
                                                }
                                                if (isset($branch[0]->branch_country_name))
                                                {
                                                    echo $branch[0]->branch_country_name;
                                                }
                                                ?>
                                            </div>

                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <br/>
                                    <?php
                                    if (isset($branch[0]->branch_mobile) && $branch[0]->branch_mobile != "")
                                    {
                                        ?>
                                        <div class="col-sm-12">
                                            <label class="col-sm-2">Mobile </label>
                                            <label class="col-sm-1">:</label>
                                            <div class="col-sm-9" >
                                        <?= $branch[0]->branch_mobile ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <br>
                                    <?php
                                    if (isset($branch[0]->branch_email_address) && $branch[0]->branch_email_address != "")
                                    {
                                        ?>
                                        <div class="col-sm-12">
                                            <label class="col-sm-2">Email </label>
                                            <label class="col-sm-1">:</label>
                                            <div class="col-sm-9" >
                                        <?= $branch[0]->branch_email_address ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    if (isset($branch[0]->branch_gstin_number) && $branch[0]->branch_gstin_number != "")
                                    {
                                        ?>
                                        <div class="col-sm-12">
                                            <label class="col-sm-2">GSTIN </label>
                                            <label class="col-sm-1">:</label>
                                            <div class="col-sm-9" >
                                        <?= $branch[0]->branch_gstin_number ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-12">


                                        <div class="col-sm-10" >
                                            <!-- <h4 style="text-transform: capitalize;">To</h4> -->
                                        </div>
                                    </div>

                                    <?php
                                    if (isset($data[0]->supplier_name) && $data[0]->supplier_name != "")
                                    {
                                        ?>
                                        <b style="font-size: 15px;">To,</b><br><br>

                                        <div class="col-sm-12">
                                            <div class="col-sm-2">
                                                <i class="fa fa-3x fa-building-o padding010 text-muted"></i>
                                            </div>
                                            <!-- <h4 class="col-sm-3" style="text-transform: capitalize;">supplier</h4>
                                            <h4 class="col-sm-1" style="text-transform: capitalize;">:</h4> -->

                                            <h4 style="text-transform: capitalize;"><?= $data[0]->supplier_name ?></h4>

                                        </div>

                                        <?php
                                    }
                                    ?>
                                    <br/>
                                    <?php
                                    if (isset($data[0]->supplier_address) && $data[0]->supplier_address != "")
                                    {
                                        ?>
                                        <div class="col-sm-12">
                                            <label class="col-sm-2">Address </label>
                                            <label class="col-sm-1">:</label>
                                            <div class="col-sm-9" >
                                                <?php
                                                echo str_replace(array(
                                                        "\r\n",
                                                        "\\r\\n",
                                                        "\n",
                                                        "\\n" ), "<br>", $data[0]->supplier_address);
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <br/>
<?php
if (isset($data[0]->supplier_city_name) && $data[0]->supplier_city_name != "")
{
    ?>
                                        <div class="col-sm-12">
                                            <label class="col-sm-2">Location </label>
                                            <label class="col-sm-1">:</label>
                                            <div class="col-sm-9" >
                                                <?php
                                                if (isset($data[0]->supplier_city_name) && $data[0]->supplier_city_name != "")
                                                {
                                                    echo $data[0]->supplier_city_name . ",";
                                                }
                                                if (isset($data[0]->supplier_state_name) && $data[0]->supplier_state_name != "")
                                                {
                                                    echo $data[0]->supplier_state_name . ",";
                                                }
                                                if (isset($data[0]->supplier_country_name) && $data[0]->supplier_country_name != "")
                                                {
                                                    echo $data[0]->supplier_country_name;
                                                }
                                                ?>
                                            </div>

                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <br>
<?php
if (isset($data[0]->supplier_mobile) && $data[0]->supplier_mobile != "")
{
    ?>
                                        <div class="col-sm-12">
                                            <label class="col-sm-2">Mobile </label>
                                            <label class="col-sm-1">:</label>
                                            <div class="col-sm-9" >
    <?= $data[0]->supplier_mobile ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <br>
<?php
if (isset($data[0]->supplier_email) && $data[0]->supplier_email != "")
{
    ?>
                                        <div class="col-sm-12">
                                            <label class="col-sm-2">Email </label>
                                            <label class="col-sm-1">:</label>
                                            <div class="col-sm-9" >
                                        <?= $data[0]->supplier_email ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    if (isset($data[0]->supplier_gstin_number) && $data[0]->supplier_gstin_number != "")
                                    {
                                        ?>
                                        <div class="col-sm-12">
                                            <label class="col-sm-2">GSTIN </label>
                                            <label class="col-sm-1">:</label>
                                            <div class="col-sm-9" >
                                        <?= $data[0]->supplier_gstin_number ?>
                                            </div>
                                        </div>
    <?php
}
?>



                                    <br>
                                </div>

                                <div class="col-sm-3">
                                    <i class="fa fa-3x fa-file-text-o padding010 text-muted"></i>
                                    <b><h4><?php echo $data[0]->purchase_order_invoice_number; ?></h4></b>
                                    <b><?php
                                        $date   = $data[0]->purchase_order_date;
                                        $c_date = date('d-m-Y', strtotime($date));
                                        echo "Date : " . $c_date;
?></b>
                                </div>
                            </div>
                        </div>

                        <?php

                        function in_array_res($needle, $haystack, $strict = false)
                        {
                            foreach ($haystack as $item)
                            {
                                if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict)))
                                {
                                    return true;
                                }
                            }

                            return false;
                        }

                        $b = $this->session->userdata('type');
                        // echo in_array_r("admin", $b) ? 'found' : 'not found';
                        ?>


                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover table-bordered table-responsive">
                                    <thead>
<?php
if ($igst_tax > 1 || $cgst_tax > 1 || $sgst_tax > 1)
{
    ?>
                                            <tr>
                                                <th width="4%" rowspan="2" >Sl<br/>No</th>
                                                <th rowspan="2" >Item <br/> Name</th>
    <?php if ($dpcount > 0)
    {
        ?>
                                                    <th rowspan="2"  >Description</th>
                                                <?php } ?>
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
    else if ($data[0]->purchase_order_billing_state_id == $branch[0]->branch_state_id)
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
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                            <tr>
                                                <th width="4%">Sl<br/>No</th>
                                                <th>Item <br/> Name</th>
                                                <?php if ($dpcount > 0)
                                                {
                                                    ?>
                                                    <th>Description</th>
    <?php } ?>
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
                                                <td align="center"><?php echo $i; ?></td>
                                                <!-- <td ><?php echo $value->product_name; ?><br>HSN:<?php echo $value->product_hsn_sac_code; ?></td> -->
    <?php if ($value->item_type == 'product' || $value->item_type == 'product_inventory')
    {
        ?>
                                                    <td><?php echo $value->product_name; ?><br>HSN:<?php echo $value->product_hsn_sac_code; ?></td>
                                                <?php
                                                }
                                                else
                                                {
                                                    ?>
                                                    <td><?php echo $value->service_name; ?><br>SAC:<?php echo $value->service_hsn_sac_code; ?></td>
                                                <?php } ?>

                                                <?php if ($dpcount > 0)
                                                {
                                                    ?>
                                                    <td  ><?php echo $value->purchase_order_item_description; ?></td>
    <?php } ?>

                                                <td align="right"><?php echo $value->purchase_order_item_quantity; ?></td>
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
                                                <th colspan="3" align="right" height="30px;"><b>Total</b></th>

                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <th colspan="2" align="right">Total</th>
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
                                <table  id="table-total" class="table table-hover table-bordered table-responsive">
                                    <tbody>
                                        <tr>
                                            <td ><b>Total Value (<?php echo $data[0]->currency_symbol; ?>)</b></td>
                                            <td><?php echo $data[0]->purchase_order_sub_total; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Purchase Discount (<?php echo $data[0]->currency_symbol; ?>)</td>
                                            <td style="color: green;">( - ) <?php echo $data[0]->purchase_order_discount_value; ?></td>
                                        </tr>
<?php if ($igst_tax > 0.00)
{
    ?>
                                            <tr>
                                                <td>IGST (<?php echo $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?php echo $igst_tax; ?></td>
                                            </tr>
<?php
}
elseif ($cgst_tax > 0.00 || $sgst_tax > 0.00)
{
    ?>
                                            <tr>
                                                <td>CGST (<?php echo $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?php echo $cgst_tax; ?></td>
                                            </tr>
                                            <tr>
                                                <td>SGST (<?php echo $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?php echo $sgst_tax; ?></td>
                                            </tr>
                                        <?php }
                                        ?>

                                        <?php if ($data[0]->total_freight_charge > 0)
                                        {
                                            ?>
                                            <tr>
                                                <td>Freight Charge (<?php echo $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: green;">( + ) <?php echo $data[0]->total_freight_charge; ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_insurance_charge > 0)
                                        {
                                            ?>
                                            <tr>
                                                <td>Insurance Charge (<?php echo $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: green;">( + ) <?php echo $data[0]->total_insurance_charge; ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_packing_charge > 0)
                                        {
                                            ?>
                                            <tr>
                                                <td>Packing & Forwarding Charge (<?php echo $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: green;">( + ) <?php echo $data[0]->total_packing_charge; ?></td>
                                            </tr>
    <?php
}
if ($data[0]->total_incidental_charge > 0)
{
    ?>
                                            <tr>
                                                <td>Incidental Charge (<?php echo $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: green;">( + ) <?php echo $data[0]->total_incidental_charge; ?></td>
                                            </tr>
    <?php
}
if ($data[0]->total_inclusion_other_charge > 0)
{
    ?>
                                            <tr>
                                                <td>Other Inclusive Charge (<?php echo $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: green;">( + ) <?php echo $data[0]->total_inclusion_other_charge; ?></td>
                                            </tr>
                                <?php
                            }
                            if ($data[0]->total_exclusion_other_charge > 0)
                            {
                                ?>
                                            <tr>
                                                <td>Other Exclusive Charge (<?php echo $data[0]->currency_symbol; ?>)</td>
                                                <td style="color: red;">( - ) <?php echo $data[0]->total_exclusion_other_charge; ?></td>
                                            </tr>
<?php } ?>

                                        <tr>
                                            <td><b>Grand Total (<?php echo $data[0]->currency_symbol; ?>)</b></td>
                                            <td style="color: blue;">( = ) <?php echo $data[0]->purchase_order_grand_total; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- <div class="col-sm-12">
                                <div class="form-group">
                                    <hr>
                                    <p class="form-text text-muted terms-cond">
<?php if ($data[0]->note)
{
    ?>
                                                    <strong>Note **:</strong> <?php
    echo str_replace(array(
            "\r\n",
            "\\r\\n" ), " &#10;", $data[0]->note);
    ?><br>
<?php } if ($data[0]->note)
{
    ?>
                                                    <strong>Note **:</strong> <?php
    echo str_replace(array(
            "\r\n",
            "\\r\\n" ), " &#10;", $data[0]->note2);
    ?>
                                        <?php } ?>
                                    </p>
                                </div>
                            </div> -->
                            <div class="col-sm-12">
                                <div class="buttons">
                                    <div class="btn-group btn-group-justified">
                                        <!-- <div class="btn-group">
                                            <a class="tip btn btn-info tip" href="<?php echo base_url('purchase_order/email/'); ?><?php echo $data[0]->purchase_order_id; ?>" title="Email">
                                                <i class="fa fa-envelope-o"></i>
                                                <span class="hidden-sm hidden-xs">Email</span>
                                            </a>
                                        </div> -->
                                        <?php
                                        $purchase_order_id = $this->encryption_url->encode($data[0]->purchase_order_id);
                                        if ($access_module_privilege->view_privilege == "yes")
                                        {
                                            ?>
                                            <div class="btn-group">
                                                <a class="tip btn btn-success" href="<?php echo base_url('purchase_order/pdf/'); ?><?php echo $purchase_order_id; ?>" title="Download as PDF" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                    <span class="hidden-sm hidden-xs">PDF</span>
                                                </a>
                                            </div>
<?php } ?>
                                        <!-- <div class="btn-group">
                                            <a class="tip btn btn-success" href="<?php echo base_url('purchase_order/print1/'); ?><?php echo $data[0]->purchase_order_id; ?>" title="Download as PDF" target="_blank">
                                                <i class="fa fa-file-pdf-o"></i>
                                                <span class="hidden-sm hidden-xs">Print</span>
                                            </a>
                                        </div> -->
<?php
if ($access_module_privilege->edit_privilege == "yes")
{
    if ($data[0]->purchase_id == null || $data[0]->purchase_id == "" || $data[0]->purchase_id == 0)
    {
        ?>
                                                <div class="btn-group">
                                                    <a class="tip btn btn-warning tip" href="<?php echo base_url('purchase_order/edit/'); ?><?php echo $purchase_order_id; ?>" title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                        <span class="hidden-sm hidden-xs">Edit</span>
                                                    </a>
                                                </div>
        <?php
    }
}
?>
<?php
if ($access_module_privilege->delete_privilege == "yes")
{
    ?>
                                            <div class="btn-group">
                                                <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="<?php echo $purchase_order_id; ?>" data-path="purchase_order/delete" class="delete_button tip btn btn-danger bpo" href="#" title="Delete Purchase">
                                                    <i class="fa fa-trash-o"></i>
                                                    <span class="hidden-sm hidden-xs">Delete</span>
                                                </a>
                                            </div>
<?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('general/delete_modal');
?>
