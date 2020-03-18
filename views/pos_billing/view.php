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

                <li><a href="<?php echo base_url('pos_billing'); ?>">POS Billing</a></li>

                <li class="active">POS Billing Details</li>

            </ol>

        </h5>

    </section>

    <section class="content">

        <div class="row">

            <div class="col-md-12">

                <div class="box">

                    <div class="box-header with-border">

                        <h3 class="box-title">POS Billing Details</h3>

                        <span class="btn btn-sm btn-default pull-right" id="cancel" style="margin-left: 1%" onclick="cancel('pos_billing')">Back</span>



                    </div>

                    <div class="box-body">

                        <div class="well well-sm">

                            <div class="row">

                                <div class="col-sm-4">



                                    <?php

                                    if (isset($branch[0]->firm_name))

                                    {

                                        ?>

                                        <b style="font-size: 15px;">From,</b><br><br>

                                        <div class="col-sm-12">



                                            <div class="col-sm-2">

                                                <i class="fa fa-3x fa-building-o padding010 text-muted"></i>

                                            </div>

                                            <div class="col-sm-10" >

                                                <h4 style="text-transform: capitalize;"><?= $branch[0]->firm_name ?></h4>

                                            </div>

                                        </div>



                                        <?php

                                    }

                                    ?>

                                    <br/>

                                    <?php

                                    if (isset($branch[0]->branch_address))

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

                                    if (isset($data[0]->customer_city))

                                    {

                                        ?>

                                        <div class="col-sm-12">

                                            <label class="col-sm-2">Location </label>

                                            <label class="col-sm-1">:</label>

                                            <div class="col-sm-9" >

                                                <?php

                                                if (isset($branch[0]->branch_city_name))

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

                                            <label class="col-sm-2">GSTID </label>

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

                                    <?php

                                    if ($data[0]->pos_billing_party_type || $data[0]->pos_billing_mobile || $data[0]->pos_billing_email)

                                    {

                                        ?>

                                        <b style="font-size: 15px;">To,</b><br><br>

                                        <?php

                                    }

                                    if (isset($data[0]->pos_billing_party_type) && $data[0]->pos_billing_party_type != "")

                                    {

                                        ?>

                                        <div class="col-sm-12">

                                            <div class="col-sm-2">

                                                <i class="fa fa-3x fa-building-o padding010 text-muted"></i>

                                            </div>

                                            <div class="col-sm-10">

                                                <h4 style="text-transform: capitalize;"><?= $data[0]->pos_billing_party_type ?></h4>

                                            </div>

                                        </div>



                                        <?php

                                    }

                                    ?>

                                    <?php

                                    if (isset($data[0]->pos_billing_mobile) && $data[0]->pos_billing_mobile != "")

                                    {

                                        ?>

                                        <br>

                                        <div class="col-sm-12">

                                            <label class="col-sm-2">Mobile </label>

                                            <label class="col-sm-1">:</label>

                                            <div class="col-sm-9" >

                                                <?= $data[0]->pos_billing_mobile ?>

                                            </div>

                                        </div>

                                    <?php } ?>



                                    <?php

                                    if (isset($data[0]->pos_billing_email) && $data[0]->pos_billing_email != "")

                                    {

                                        ?>

                                        <br>

                                        <div class="col-sm-12">

                                            <label class="col-sm-2">Email </label>

                                            <label class="col-sm-1">:</label>

                                            <div class="col-sm-9" >

                                                <?= $data[0]->pos_billing_email ?>

                                            </div>

                                        </div>

                                        <?php

                                    }

                                    ?>



                                </div>

                                <div class="col-sm-3">

                                    <i class="fa fa-3x fa-file-text-o padding010 text-muted"></i>

                                    <b><h4><?php echo $data[0]->pos_billing_invoice_number; ?></h4></b>

                                    <b><?php

                                        $date   = $data[0]->pos_billing_date;

                                        $c_date = date('d-m-Y', strtotime($date));

                                        echo "Date : " . $c_date;

                                        ?></b>

                                </div>

                            </div>

                        </div>





                        <div class="row">

                            <div class="col-sm-12">

                                <table class="table table-hover table-bordered table-responsive">

                                    <thead>

                                        <?php

                                        if ($igst_tax > 1 || $cgst_tax > 1 || $sgst_tax > 1)

                                        {

                                            ?>

                                            <tr>

                                                <th width="4%" rowspan="2" style="text-align: center;">Sl<br/>No</th>

                                                <th rowspan="2" style="text-align: center;">Item <br/> Name</th>

                                                <?php

                                                if ($dpcount > 0)

                                                {

                                                    ?>

                                                    <th rowspan="2"  style="text-align: center;">Description</th>

                                                <?php } ?>

                                <!-- <th colspan="1" style="border-top: 1px solid #333; border-right: 0">qty</th> -->

                                                <th rowspan="2" style="text-align: center;">Quantity</th>

                                                <th rowspan="2" style="text-align: center;">Rate</th>

                                                <th rowspan="2" style="text-align: center;">Sub <br/> Total</th>

                                                <?php

                                                if ($dtcount > 0)

                                                {

                                                    ?>

                                                    <th rowspan="2" style="text-align: center;">Disc<br />Amt</th>

                                                <?php } ?>

                                                <th rowspan="2" style="text-align: center;">Taxable <br/>Value</th>

                                                <!-- <?php

                                                if ($igst_tax < 1 && $cgst_tax > 1)

                                                {

                                                    ?>

                                                                     <th colspan="2" style="text-align: center;">CGST</th>

                                                                    <th colspan="2" style="text-align: center;">SGST</th>



                                                    <?php

                                                }

                                                else

                                                {

                                                    ?>



                                                                        <th colspan="2" style="text-align: center;">GST</th>

                                                <?php } ?> -->

                                                <th colspan="2" style="text-align: center;">GST</th>

                                                <th rowspan="2" style="text-align: center;">Total</th>

                                            </tr>

                                            <tr>

                                                <!-- <?php

                                                if ($igst_tax < 1 && $cgst_tax > 1)

                                                {

                                                    ?>

                                                                  <th style="text-align: center;">

                                                                Rate %

                                                             </th>

                                                              <th style="text-align: center;">

                                                                Amount

                                                             </th>

                                                              <th style="text-align: center;">

                                                                Rate %

                                                             </th>

                                                              <th style="text-align: center;">

                                                                Amount

                                                             </th>



                                                    <?php

                                                }

                                                else if ($igst_tax > 1 && $cgst_tax < 1)

                                                {

                                                    ?>



                                                             <th style="text-align: center;">

                                                                Rate %

                                                             </th>

                                                             <th style="text-align: center;">

                                                                Amount

                                                             </th>

                                                    <?php

                                                }

                                                else if ($data[0]->billing_state_id == $branch[0]->branch_state_id)

                                                {

                                                    ?>

                                                                       <th style="text-align: center;">

                                                                Rate %

                                                             </th>

                                                              <th style="text-align: center;">

                                                                Amount

                                                             </th>

                                                              <th style="text-align: center;">

                                                                Rate %

                                                             </th>

                                                              <th style="text-align: center;">

                                                                Amount

                                                             </th>





                                                    <?php

                                                }

                                                else

                                                {

                                                    ?>

                                                                 <th style="text-align: center;">

                                                                Rate %

                                                             </th>

                                                              <th style="text-align: center;">

                                                                Amount

                                                             </th>

                                                <?php }

                                                ?> -->

                                                <th style="text-align: center;">

                                                    Rate %

                                                </th>

                                                <th style="text-align: center;">

                                                    Amount

                                                </th>



                                            </tr>

                                            <?php

                                        }

                                        else

                                        {

                                            ?>

                                            <tr>

                                                <th width="4%">Sl<br/>No</th>

                                                <th>Item <br/> Name</th>

                                                <?php

                                                if ($dpcount > 0)

                                                {

                                                    ?>

                                                    <th>Description</th>

                                                <?php } ?>

                                <!-- <th colspan="1" style="border-top: 1px solid #333; border-right: 0">qty</th> -->

                                                <th>Quantity</th>

                                                <th>Rate</th>

                                                <th>Sub <br/> Total</th>

                                                <?php

                                                if ($dtcount > 0)

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

                                        $quantity    = 0;

                                        $price       = 0;

                                        $discount    = 0;

                                        $grand_total = 0;

                                        foreach ($items as $value)

                                        {

                                            ?>

                                            <tr>

                                                <td align="center"><?php echo $i; ?></td>

                                                <?php

                                                if ($value->item_type == 'product' || $value->item_type == 'product_inventory')

                                                {

                                                    ?>

                                                    <td ><?php echo $value->product_name; ?><br>HSN/SAC: <?php echo $value->product_hsn_sac_code; ?></td>

                                                    <?php

                                                }

                                                else

                                                {

                                                    ?>

                                                    <td ><?php echo $value->service_name; ?><br>HSN/SAC: <?php echo $value->service_hsn_sac_code; ?></td>

                                                <?php } ?>



                                                <?php

                                                if ($dpcount > 0)

                                                {

                                                    ?>

                                                    <td  ><?php echo $value->pos_billing_item_description; ?></td>

                                                <?php } ?>



                                                <td align="right"><?php echo $value->pos_billing_item_quantity; ?></td>

                                                <td align="right"><?php echo $value->pos_billing_item_unit_price; ?></td>

                                                <td align="right"><?php echo $value->pos_billing_item_sub_total; ?></td>



                                                <?php

                                                if ($dtcount > 0)

                                                {

                                                    ?>

                                                    <td align="right"><?php echo $value->pos_billing_item_discount_amount; ?></td>



                                                <?php } ?>



                                                <!-- <?php

                                                if ($igst_tax < 1 && $cgst_tax > 1)

                                                {

                                                    ?>

                                                                <td align="right"><?php echo $value->pos_billing_item_taxable_value; ?></td>

                                                                     <td align="center"><?php echo $value->pos_billing_item_cgst_percentage; ?></td>

                                                                        <td align="right"><?php echo $value->pos_billing_item_cgst_amount; ?></td>

                                                                         <td align="center"><?php echo $value->pos_billing_item_sgst_percentage; ?></td>

                                                                    <td align="right"><?php echo $value->pos_billing_item_sgst_amount; ?></td>



                                                    <?php

                                                }

                                                else if ($igst_tax > 1 && $cgst_tax < 1)

                                                {

                                                    ?>

                                                                <td align="right"><?php echo $value->pos_billing_item_taxable_value; ?></td>

                                                                    <td align="center"><?php echo $value->pos_billing_item_igst_percentage; ?></td>

                                                                <td align="right"><?php echo $value->pos_billing_item_igst_amount; ?></td>



                                                <?php }

                                                ?> -->

                                                <?php

                                                if ($igst_tax > 1 || $cgst_tax > 1 || $sgst_tax > 1)

                                                {

                                                    ?>

                                                    <td align="right"><?php echo $value->pos_billing_item_taxable_value; ?></td>

                                                    <td align="center"><?php echo bcadd($value->pos_billing_item_cgst_percentage, $value->pos_billing_item_sgst_percentage, 2); ?></td>

                                                    <td align="right"><?php echo bcadd($value->pos_billing_item_cgst_amount, $value->pos_billing_item_sgst_amount, 2); ?></td>

                                                <?php } ?>



                                                <td align="right"><?php echo $value->pos_billing_item_grand_total ?></td>

                                            </tr>

                                            <?php

                                            $i++;

                                            $quantity = bcadd($quantity, $value->pos_billing_item_quantity, 2);

                                            $price    = bcadd($price, $value->pos_billing_item_unit_price, 2);

                                            // $sub_total = bcadd($tot, $value->sub_total, 2);

                                            // $discount = bcadd($discount, $value->discount_amt, 2);

                                            // $taxable_value = bcadd($discount, $value->taxable_value, 2);

                                            // $igst = bcadd($igst, $value->sales_item_igst_amount, 2);

                                            // $cgst = bcadd($cgst, $value->sales_item_cgst_amount, 2);

                                            // $sgst = bcadd($sgst, $value->sales_item_sgst_amount, 2);



                                            $grand_total = bcadd($grand_total, $value->pos_billing_item_grand_total, 2);

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

                                            <th align="right"><?php echo $data[0]->pos_billing_sub_total; ?></th>



                                            <?php

                                            if ($dtcount > 0)

                                            {

                                                ?>

                                                <th><?php echo $data[0]->pos_billing_discount_value; ?></th>



                                                <?php

                                            }

                                            ?>



                                            <!-- <?php

                                            if ($igst_tax < 1 && $cgst_tax > 1)

                                            {

                                                ?>

                                                            <th align="right"><?php echo $data[0]->pos_billing_taxable_value; ?></th>



                                                                    <th align="right" colspan="2"><?php echo bcdiv($data[0]->pos_billing_tax_amount, 2, 2); ?></th>



                                                                <th align="right" colspan="2"><?php echo bcdiv($data[0]->pos_billing_tax_amount, 2, 2); ?></th>



                                                <?php

                                            }

                                            else if ($igst_tax > 1 && $cgst_tax < 1)

                                            {

                                                ?>

                                                            <th align="right"><?php echo $data[0]->pos_billing_taxable_value; ?></th>



                                                                <th align="right" colspan="2"><?php echo $data[0]->pos_billing_tax_amount; ?></th>



                                            <?php }

                                            ?> -->

                                            <?php

                                            if ($igst_tax > 1 || $cgst_tax > 1 || $sgst_tax > 1)

                                            {

                                                ?>

                                                <th align="right"><?php echo $data[0]->pos_billing_taxable_value; ?></th>

                                                <th align="right" colspan="2"><?php echo $data[0]->pos_billing_tax_amount; ?></th>

                                            <?php } ?>

                                            <th align="right"><?php echo $grand_total ?></th>



                                        </tr>

                                    </tbody>

                                </table>

                                <table  id="table-total" class="table table-hover table-bordered table-responsive">

                                    <tbody>

                                        <tr>

                                            <td ><b>Total Value (<?= $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') ?>)</b></td>

                                            <td><?php echo $data[0]->pos_billing_sub_total; ?></td>

                                        </tr>

                                        <tr>

                                            <td>Discount (<?= $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') ?>)</td>

                                            <td style="color: green;">( - ) <?php echo $data[0]->pos_billing_discount_value; ?></td>

                                        </tr>

                                        <?php

                                        if ($igst_tax > 0.00)

                                        {

                                            ?>

                                            <tr>

                                                <td>IGST (<?= $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') ?>)</td>

                                                <td style="color: red;">( + ) <?php echo $igst_tax; ?></td>

                                            </tr>

                                            <?php

                                        }

                                        elseif ($cgst_tax > 0.00 || $sgst_tax > 0.00)

                                        {

                                            ?>

                                            <tr>

                                                <td>CGST (<?= $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') ?>)</td>

                                                <td style="color: red;">( + ) <?php echo $cgst_tax; ?></td>

                                            </tr>

                                            <tr>

                                                <td>SGST (<?= $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') ?>)</td>

                                                <td style="color: red;">( + ) <?php echo $sgst_tax; ?></td>

                                            </tr>

                                        <?php }

                                        ?>







                                        <tr>

                                            <td><b>Grand Total (<?= $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') ?>)</b></td>

                                            <td style="color: blue;">( = ) <?php echo $data[0]->pos_billing_grand_total; ?></td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                            <!-- <div class="col-sm-12">

                                <div class="form-group">

                                    <hr>

                                    <p class="form-text text-muted terms-cond">

                            <?php

                            if ($data[0]->note)

                            {

                                ?>

                                                            <strong>Note **:</strong> <?php

                                echo str_replace(array(

                                        "\r\n",

                                        "\\r\\n" ), " &#10;", $data[0]->note);

                                ?><br>

                                <?php

                            } if ($data[0]->note)

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

                                            <a class="tip btn btn-info tip" href="<?php echo base_url('sales/email/'); ?><?php echo $data[0]->sales_id; ?>" title="Email">

                                                <i class="fa fa-envelope-o"></i>

                                                <span class="hidden-sm hidden-xs">Email</span>

                                            </a>

                                        </div> -->







                                        <?php

                                        $pos_billing_id = $this->encryption_url->encode($data[0]->pos_billing_id);

                                        if ($access_module_privilege->view_privilege == "yes")

                                        {

                                            ?>

                                            <div class="btn-group">

                                                <a class="tip btn btn-success pdf_button"  class="pdf_button"  href="<?php echo base_url('pos_billing/pdf/'); ?><?php echo $pos_billing_id; ?>" title="Download as PDF" target="_blank">

                                                    <i class="fa fa-file-pdf-o"></i>

                                                    <span class="hidden-sm hidden-xs">PDF</span></a>

                                                </li>

                                            </div>

                                        <?php } ?>



                                        <!-- <div class="btn-group">

                                            <a class="tip btn btn-success" href="<?php echo base_url('sales/print1/'); ?><?php echo $data[0]->sales_id; ?>" title="Download as PDF" target="_blank">

                                                <i class="fa fa-file-pdf-o"></i>

                                                <span class="hidden-sm hidden-xs">Print</span>

                                            </a>

                                        </div> -->

                                        <?php

                                        if ($access_module_privilege->edit_privilege == "yes")

                                        {

                                            ?>

                                            <div class="btn-group">

                                                <a class="tip btn btn-warning tip" href="<?php echo base_url('pos_billing/edit/'); ?><?php echo $pos_billing_id; ?>" title="Edit">

                                                    <i class="fa fa-pencil"></i>

                                                    <span class="hidden-sm hidden-xs">Edit</span>

                                                </a>

                                            </div>

                                            <?php

                                        }

                                        ?>

                                        <?php

                                        if ($access_module_privilege->delete_privilege == "yes")

                                        {

                                            ?>

                                            <div class="btn-group">



                                                <div class="btn-group">

                                                    <a data-backdrop="static" style="width: 500%;" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="<?php echo $pos_billing_id; ?>" data-path="pos_billing/delete" class="delete_button tip btn btn-danger" href="#" title="Delete Sales">

                                                        <i class="fa fa-trash-o"></i>

                                                        <span class="hidden-sm hidden-xs">Delete</span>

                                                    </a>

                                                </div>

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

$this->load->view('sales/pdf_type_modal');

$this->load->view('general/delete_modal');

?>



