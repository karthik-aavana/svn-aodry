<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Purchase Order View</h3>
                        <?php
                        $purchase_order_id = $this->encryption_url->encode($data[0]->purchase_order_id);
                        ?>
                        <a class="btn btn-sm btn-default pull-right back_button" id="cancel" onclick1="cancel('purchase_order')">Back</a>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <h2 class="page-header">
                                    <i class="fa fa-building-o"></i> <?= $branch[0]->firm_name ?>
                                    <small class="pull-right"> <?php
                                        $date = $data[0]->purchase_order_date;
                                        $c_date = date('d-m-Y', strtotime($date));
                                        echo "Date : " . $c_date;
                                        ?></small>
                                </h2>
                            </div>                            
                        </div>                        
                        <div class="row">
                            <div class="col-sm-4">
                                From
                                <address>
                                    <?php if (isset($data[0]->supplier_name) && $data[0]->supplier_name != "") { ?>
                                        <strong><?= $data[0]->supplier_name ?></strong>
                                    <?php } ?>
                                    <br/>
                                    <?php if (isset($data[0]->supplier_address) && $data[0]->supplier_address != "") { ?>
                                        Address:
                                        <?php
                                        echo str_replace(array(
                                            "\r\n",
                                            "\\r\\n",
                                            "\n",
                                            "\\n"
                                                ), "<br>", $data[0]->supplier_address);
                                    }
                                    ?>
                                    <br>
                                    Mobile: <?php if (isset($data[0]->supplier_mobile) && $data[0]->supplier_mobile != "") { ?>
                                        <?= $data[0]->supplier_mobile ?>
                                    <?php } ?>
                                    <br>
                                    Email: <?php if (isset($data[0]->supplier_email) && $data[0]->supplier_email != "") { ?>
                                        <?= $data[0]->supplier_email ?>
                                        <br>
                                        <?php
                                    }
                                    if (isset($data[0]->supplier_gstin_number) && $data[0]->supplier_gstin_number != "") {
                                        ?>
                                        GSTIN: <?= $data[0]->supplier_gstin_number ?>
                                    <?php } ?>
                                </address>                          
                                <address>
                                    To<br>
                                    <strong><?= $branch[0]->firm_name ?></strong>
                                    <?php if (isset($branch[0]->firm_name) && $branch[0]->firm_name != "") { ?>
                                    <?php } ?>
                                    <br/>
                                    <?php if (isset($branch[0]->branch_address) && $branch[0]->branch_address != "") { ?>
                                        Address : <?php
                                        echo str_replace(array(
                                            "\r\n",
                                            "\\r\\n",
                                            "\n",
                                            "\\n"
                                                ), "<br>", $branch[0]->branch_address);
                                        ?>
                                    <?php } ?>
                                    <br/>
                                    Location :
                                    <?php
                                    if (isset($branch[0]->branch_city_name) && $branch[0]->branch_city_name != "") {

                                        if (isset($branch[0]->branch_city_name) && $branch[0]->branch_city_name != "") {
                                            echo $branch[0]->branch_city_name . ",";
                                        }
                                        if (isset($branch[0]->branch_state_name) && $branch[0]->branch_state_name != "") {
                                            echo $branch[0]->branch_state_name . ",";
                                        }
                                        if (isset($branch[0]->branch_country_name) && $branch[0]->branch_country_name != "") {
                                            echo $branch[0]->branch_country_name;
                                        }
                                    }
                                    ?>
                                    <br/>
                                    Mobile: <?php if (isset($branch[0]->branch_mobile) && $branch[0]->branch_mobile != "") { ?>
                                        <?= $branch[0]->branch_mobile ?>
                                    <?php } ?>
                                    <br>
                                    Email: <?php if (isset($branch[0]->branch_email_address) && $branch[0]->branch_email_address != "") { ?>
                                        <?= $branch[0]->branch_email_address ?>
                                        <?php
                                    }
                                    if (isset($branch[0]->branch_gstin_number) && $branch[0]->branch_gstin_number != "") {
                                        ?>
                                        <br>
                                        GSTIN: <?= $branch[0]->branch_gstin_number ?>
                                    <?php } ?>
                                </address>
                            </div>
                            <div class="col-sm-4 pull-right">
                                <div class="bg-light-table">
                                    <strong>Invoice Details</strong><br>
                                    PO Number: <?php
                                    echo $data[0]->purchase_order_invoice_number;
                                    ?><br>
                                    <?php
                                    $date = $data[0]->purchase_order_date;
                                    $c_date = date('d-m-Y', strtotime($date));
                                    echo "PO Date : " . $c_date;
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table width="100%" class="table table-hover table-bordered table-responsive">
                                    <thead>
                                        <tr>
                                            <th width="3%">#</th>
                                            <th width="">Description</th>
                                            <th width="%">Quantity</th>
                                            <th>Rate</th>
                                            <th>Subtotal</th>
                                            <?php
                                            if ($discount_exist > 0) {
                                                ?>
                                                <th>Discount</th>
                                            <?php } ?>
                                            <?php
                                            if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                                                ?>
                                                <th>Taxable Value</th>
                                            <?php } ?>
                                            <?php
                                            if ($tds_exist > 0) {
                                                ?>
                                                <th>TCS</th>
                                            <?php } ?>
                                            <?php
                                            if ($tax_exist > 0) {
                                                ?>
                                                <th>Tax</th>
                                                <?php
                                            } elseif ($igst_exist > 0) {
                                                ?>
                                                <th>IGST</th>
                                                <?php
                                            } elseif ($cgst_exist > 0 || $sgst_exist > 0) {
                                                ?>
                                                <th>CGST</th>
                                                <th><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></th>
                                            <?php }
                                            ?>
                                            <?php
                                            if ($cess_exist > 0) {
                                                ?>
                                                <th>CESS</th>
                                            <?php }
                                            ?>
                                            <th>Total</th>
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
                                                <td ><?php echo $i; ?></td>
                                                <?php
                                                if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {
                                                    ?>
                                                    <td style="text-align: left;">
                                                        <?php echo strtoupper(strtolower($value->product_name)); ?>
                                                        <br>(HSN/SAC: <?php echo $value->product_hsn_sac_code; ?>)
                                                        <?php
                                                        if (isset($value->purchase_order_item_description) && $value->purchase_order_item_description != "") {
                                                            echo "<br/>";
                                                            echo $value->purchase_order_item_description;
                                                        }
                                                        ?><br><?php echo $value->product_batch; ?>
                                                    </td>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <td style="text-align: left;"><?php echo strtoupper(strtolower($value->service_name)); ?><br>(HSN/SAC: <?php echo $value->service_hsn_sac_code; ?>)
                                                        <?php
                                                        if (isset($value->purchase_order_item_description) && $value->purchase_order_item_description != "") {
                                                            echo "<br/>";
                                                            echo $value->purchase_order_item_description;
                                                        }
                                                        ?>
                                                    </td>
                                                <?php } ?>
                                                <td style="text-align: center;"><?php
                                                    echo $value->purchase_order_item_quantity;
                                                    if ($value->product_unit != '') {
                                                        $unit = explode("-", $value->product_unit);
                                                        if($unit[0] != '') echo " <br>(" . $unit[0].')';
                                                    }
                                                    ?></td>
                                                <td style="text-align: right;"><?php echo precise_amount($value->purchase_order_item_unit_price); ?></td>
                                                <td style="text-align: right;"><?php echo precise_amount($value->purchase_order_item_sub_total); ?></td>
                                                <?php
                                                if ($discount_exist > 0) {
                                                    ?>
                                                    <td style="text-align: right;"><?php echo precise_amount($value->purchase_order_item_discount_amount); ?></td>
                                                <?php } ?>
                                                <?php
                                                if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                                                    ?>
                                                    <td style="text-align: right;"><?php echo precise_amount($value->purchase_order_item_taxable_value); ?></td>
                                                <?php } ?>
                                                <?php
                                                if ($tds_exist > 0) {
                                                    ?>
                                                    <td style="text-align: right;">
                                                        <?php
                                                        if ($value->purchase_order_item_tax_amount < 1  || $value->item_type == 'service') {
                                                            echo '-';
                                                        } else {
                                                            ?>
                                                            <?php echo precise_amount($value->purchase_order_item_tds_amount); ?><br>(<?php echo round($value->purchase_order_item_tds_percentage, 2); ?>%)
                                                        <?php } ?>
                                                    </td>
                                                <?php }
                                                ?>
                                                <?php
                                                if ($tax_exist > 0) {
                                                    ?>
                                                    <td style="text-align: right;">
                                                        <?php
                                                        if ($value->purchase_order_item_tax_amount < 1) {
                                                            echo '-';
                                                        } else {
                                                            ?><?php echo precise_amount($value->purchase_order_item_tax_amount); ?><br>(<?php echo round($value->purchase_order_item_tax_percentage, 2); ?>%)
                                                        <?php } ?>
                                                    </td>
                                                    <?php
                                                } elseif ($igst_exist > 0) {
                                                    ?>                            
                                                    <td style="text-align: right;">
                                                        <?php
                                                        if ($value->purchase_order_item_igst_amount < 1) {
                                                            echo '-';
                                                        } else {
                                                            ?><?php echo precise_amount($value->purchase_order_item_igst_amount); ?><br>(<?php echo round($value->purchase_order_item_igst_percentage, 2); ?>%)
                                                        <?php } ?>
                                                    </td>
                                                    <?php
                                                } elseif ($cgst_exist > 0 || $sgst_exist > 0) {
                                                    ?>
                                                    <td style="text-align: right;">
                                                        <?php
                                                        if ($value->purchase_order_item_cgst_amount < 1) {
                                                            echo '-';
                                                        } else {
                                                            ?><?php echo precise_amount($value->purchase_order_item_cgst_amount); ?><br>(<?php echo round($value->purchase_order_item_cgst_percentage, 2); ?>%)
                                                        <?php } ?>
                                                    </td>                            
                                                    <td style="text-align: right;">
                                                        <?php
                                                        if ($value->purchase_order_item_sgst_amount < 1) {
                                                            echo '-';
                                                        } else {
                                                            ?>
                                                            <?php echo precise_amount($value->purchase_order_item_sgst_amount); ?><br>(<?php echo round($value->purchase_order_item_sgst_percentage, 2); ?>%)
                                                        <?php } ?>
                                                    </td>
                                                <?php }
                                                ?>
                                                <?php
                                                if ($cess_exist > 0) {
                                                    ?>
                                                    <td style="text-align: right;">
                                                        <?php
                                                        if ($value->purchase_order_item_tax_cess_amount < 1) {
                                                            echo '-';
                                                        } else {
                                                            ?>
                                                            <?php echo precise_amount($value->purchase_order_item_tax_cess_amount); ?><br>(<?php echo round($value->purchase_order_item_tax_cess_percentage, 2); ?>%)
                                                        <?php } ?>
                                                    </td>
                                                <?php }
                                                ?>
                                                <td style="text-align: right;"><?php echo precise_amount($value->purchase_order_item_grand_total); ?></td>
                                            </tr>
                                            <?php
                                            $i++;
                                            $quantity += $value->purchase_order_item_quantity;
                                            $price = bcadd($price, $value->purchase_order_item_unit_price);
                                            $grand_total = bcadd($grand_total, $value->purchase_order_item_grand_total);
                                        }
                                        ?>
                                        <tr>
                                            <th colspan="2"></th>
                                            <th  ><!--<?= round($quantity) ?>--></th>
                                            <th style="text-align: right;"><?= precise_amount($price) ?></th>
                                            <th style="text-align: right;"><?= precise_amount($data[0]->purchase_order_sub_total) ?></th>
                                            <?php
                                            if ($discount_exist > 0) {
                                                ?>
                                                <th style="text-align: right;"><?= precise_amount($data[0]->purchase_order_discount_amount) ?></th>
                                            <?php } ?>
                                            <?php
                                            if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                                                ?>
                                                <th style="text-align: right;"><?= precise_amount($data[0]->purchase_order_taxable_value) ?></th>
                                            <?php } ?>
                                            <?php
                                            if ($tds_exist > 0) {
                                                ?>
                                                <th style="text-align: right;"><?= precise_amount($data[0]->purchase_order_tcs_amount) ?></th>
                                            <?php }
                                            ?>
                                            <?php
                                            if ($tax_exist > 0) {
                                                ?>
                                                <th style="text-align: right;"><?= precise_amount($data[0]->purchase_order_tax_amount) ?></th>
                                                <?php
                                            } elseif ($igst_exist > 0) {
                                                ?>
                                                <th style="text-align: right;"><?= precise_amount($data[0]->purchase_order_igst_amount) ?></th>
                                                <?php
                                            } elseif ($cgst_exist > 0 || $sgst_exist > 0) {
                                                ?>
                                                <th style="text-align: right;"><?= precise_amount($data[0]->purchase_order_cgst_amount) ?></th>
                                                <th style="text-align: right;"><?= precise_amount($data[0]->purchase_order_sgst_amount) ?></th>
                                            <?php }
                                            ?>
                                            <?php
                                            if ($cess_exist > 0) {
                                                ?>
                                                <th style="text-align: right;"><?php echo precise_amount($data[0]->purchase_order_tax_cess_amount); ?></th>
                                            <?php }
                                            ?>
                                            <th style="text-align: right;"><?= bcadd($data[0]->purchase_order_grand_total, $data[0]->round_off_amount, 2); ?></th>
                                        </tr>
                                    </tbody>
                                </table>
                                <table  id="table-total" class="table table-hover table-bordered table-responsive">
                                    <tbody>
                                        <tr>
                                            <td><b>Total Value (<?= $currency_symbol; ?>)</b></td>
                                            <td><?= precise_amount($data[0]->purchase_order_sub_total);?></td>
                                        </tr>
                                        <?php if ($discount_exist > 0) { ?>
                                            <tr>
                                                <td>Discount (<?= $currency_symbol; ?>)</td>
                                                <td style="color: green;">( - ) <?= precise_amount($data[0]->purchase_order_discount_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($tds_exist > 0) { ?>
                                            <!-- <?php if ($data[0]->purchase_order_tds_amount > 0) { ?>  
                                                <tr>
                                                    <td>TDS/TCS (<?= $currency_code; ?>)</td>
                                                    <td style="color: black;"> <?= precise_amount($data[0]->purchase_order_tds_amount); ?></td>
                                                </tr>
                                            <?php } ?> -->
                                            <?php if ($data[0]->purchase_order_tcs_amount > 0) { ?>                           
                                                <tr>
                                                    <td>TCS (<?= $currency_symbol; ?>)</td>
                                                    <td style="color: red;">( + ) <?= precise_amount($data[0]->purchase_order_tcs_amount); ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if ($igst_exist > 0) { ?>
                                            <tr>
                                                <td>IGST (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->purchase_order_igst_amount); ?></td>
                                            </tr>
                                        <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                            <tr>
                                                <td>CGST (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + )<?= precise_amount($data[0]->purchase_order_cgst_amount); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?> (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->purchase_order_sgst_amount); ?></td>
                                            </tr>
                                        <?php } elseif ($tax_exist > 0) { ?>
                                            <tr>
                                                <td>TAX (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->purchase_order_tax_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($cess_exist > 0) { ?>
                                            <tr>
                                                <td>Cess (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->purchase_order_tax_cess_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($data[0]->total_freight_charge > 0) { ?>
                                            <tr>
                                                <td>Freight Charge (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->total_freight_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_insurance_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Insurance Charge (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->total_insurance_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_packing_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Packing & Forwarding Charge (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->total_packing_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_incidental_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Incidental Charge (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->total_incidental_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_inclusion_other_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Other Inclusive Charge (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->total_inclusion_other_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_exclusion_other_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Other Exclusive Charge (<?= $currency_symbol; ?>)</td>
                                                <td style="color: green;">( - ) <?= precise_amount($data[0]->total_exclusion_other_charge); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($data[0]->round_off_amount > 0) { ?>
                                            <tr>
                                                <td>Round Off (<?= $currency_symbol; ?>)</td>
                                                <td style="color: green;">( - ) <?= precise_amount($data[0]->round_off_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($data[0]->round_off_amount < 0) { ?>
                                            <tr>
                                                <td>Round Off (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->round_off_amount * -1); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <td><b>Grand Total (<?= $currency_symbol; ?>)</b></td>
                                            <td style="color: blue;">( = ) <?= precise_amount($data[0]->purchase_order_grand_total); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <div class="buttons">
                                    <div class="btn-group btn-group-justified">
                                        <?php
                                        if (in_array($purchase_order_module_id, $active_view)) {
                                            ?>
                                            <div class="btn-group">
                                                <a class="tip btn btn-success" href="<?php echo base_url('purchase_order/pdf/'); ?><?php echo $purchase_order_id; ?>" title="Download as PDF" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                    <span class="hidden-sm hidden-xs">PDF</span>
                                                </a>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (in_array($purchase_order_module_id, $active_edit)) {
                                            if ($data[0]->purchase_order_paid_amount == 0) {
                                                ?>
                                                <div class="btn-group">
                                                    <a class="tip btn btn-warning tip" href="<?php
                                                    echo base_url('purchase_order/edit/');
                                                    ?><?php
                                                    echo $purchase_order_id;
                                                    ?>" title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                        <span class="hidden-sm hidden-xs">Edit</span>
                                                    </a>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                       <!--  <?php
                                        if (in_array($email_module_id, $active_view)) {
                                            if (in_array($email_sub_module_id, $access_sub_modules)) {
                                                ?>
                                                <div class="btn-group">
                                                    <a class="tip btn btn-warning tip" href="<?php
                                                    echo base_url('purchase_order/email/');
                                                    ?><?php
                                                    echo $purchase_order_id;
                                                    ?>" title="Send Mail">
                                                        <i class="fa fa-envelope-o"></i>
                                                        <span class="hidden-sm hidden-xs">Email</span>
                                                    </a>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?> -->
                                        
                                        <?php
                                        if (in_array($purchase_order_module_id, $active_delete)) {
                                            ?>
                                            <div class="btn-group">
                                                <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="<?php
                                                echo $purchase_order_id;
                                                ?>" data-path="purchase_order/delete" class="delete_button tip btn btn-danger bpo" href="#" title="Delete purchase_order">
                                                    <i class="fa fa-trash-o"></i>
                                                    <span class="hidden-sm hidden-xs">Delete</span>
                                                </a>
                                            </div>
                                            <?php
                                        }
                                        ?>
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