<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<script type="text/javascript">
    function delete_id(id)
    {
        if (confirm('<?php echo $this->lang->line('product_delete_conform'); ?>'))
        {
            window.location.href = '<?php echo base_url('expense/delete/'); ?>' + id;
        }
    }
</script>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
                <li><a href="<?php echo base_url('expense_bill'); ?>">Expense Bill</a></li>
                <li class="active">View</li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <?php
                        $expense_bill_id = $this->encryption_url->encode($data[0]->expense_bill_id);
                        ?>
                        <h3 class="box-title">Expense Bill View</h3>
                        <div class="pull-right">
                            <a class="btn btn-sm btn-info" href="<?php echo base_url('payment_voucher/add_expense_payment/') . $expense_bill_id; ?>" title="Pay Now">Pay Now</a>
                            <a class="btn btn-sm btn-info back_button" id="cancel" onclick1="cancel('expense_bill')">Back</a>
                        </div>
                    </div>
                    <div class="box-body">                       
                        <div class="row">
                            <div class="col-sm-4">
                                <?php
                                if (isset($data[0]->supplier_name) && $data[0]->supplier_name != "") {
                                    ?>
                                    <h4 style="text-transform: capitalize;"><?= $data[0]->supplier_name ?></h4>
                                    <?php
                                }
                                ?>                                  
                                <?php
                                if (isset($data[0]->supplier_gstin_number) && $data[0]->supplier_gstin_number != "") {
                                    ?>
                                    <?php echo "<br> GSTIN:" . $data[0]->supplier_gstin_number; ?>
                                    <?php
                                }
                                ?>                                
                                <?php
                                if (isset($data[0]->supplier_address) && $data[0]->supplier_address != "") {
                                    ?>                                       
                                    <?php
                                    echo 'Address : ';
                                    echo str_replace(array(
                                        "\r\n",
                                        "\\r\\n",
                                        "\n",
                                        "\\n"), "<br>", $data[0]->supplier_address);
                                    ?>
                                    <?php
                                }
                                ?>
                                <?php
                                if (isset($data[0]->supplier_city_name) && $data[0]->supplier_city_name != "") {
                                    ?>                                       
                                    <?php
                                    echo ' <br/> Location : ';
                                    if (isset($data[0]->supplier_city_name) && $data[0]->supplier_city_name != "") {
                                        echo $data[0]->supplier_city_name . ",";
                                    }
                                    if (isset($data[0]->supplier_state_name) && $data[0]->supplier_state_name != "") {
                                        echo $data[0]->supplier_state_name . ",";
                                    }
                                    if (isset($data[0]->supplier_country_name) && $data[0]->supplier_country_name != "") {
                                        echo $data[0]->supplier_country_name;
                                    }
                                    ?>
                                    <?php
                                }
                                ?>                               
                                <?php
                                if (isset($data[0]->supplier_mobile) && $data[0]->supplier_mobile != "") {
                                    ?>
                                    <?=
                                    '<br/> Mobile:';
                                    $data[0]->supplier_mobile
                                    ?>
                                <?php } ?>
                                <br>                               
                                <?php
                                if (isset($branch[0]->firm_name) && $branch[0]->firm_name != "") {
                                    ?>
                                    <h4 style="text-transform: capitalize;"><?= $branch[0]->firm_name ?></h4>
                                    <?php
                                }
                                ?>                              
                                <?php
                                if (isset($branch[0]->branch_address) && $branch[0]->branch_address != "") {
                                    ?>
                                    Address: <?php
                                    echo str_replace(array(
                                        "\r\n",
                                        "\\r\\n",
                                        "\n",
                                        "\\n"), "<br>", $branch[0]->branch_address);
                                    ?>
                                    <?php
                                }
                                ?>
                                <br/>
                                <?php
                                if (isset($branch[0]->branch_city_name) && $branch[0]->branch_city_name != "") {
                                    ?>
                                    Location <?php
                                    if (isset($branch[0]->branch_city_name) && $branch[0]->branch_city_name != "") {
                                        echo $branch[0]->branch_city_name . ",";
                                    }
                                    if (isset($branch[0]->branch_state_name) && $branch[0]->branch_state_name != "") {
                                        echo $branch[0]->branch_state_name . ",";
                                    }
                                    if (isset($branch[0]->branch_country_name) && $branch[0]->branch_country_name != "") {
                                        echo $branch[0]->branch_country_name;
                                    }
                                    ?>
                                    <?php
                                }
                                ?>
                                <br/>
                                <?php
                                if (isset($branch[0]->branch_mobile) && $branch[0]->branch_mobile != "") {
                                    ?>
                                    Mobile: <?= $branch[0]->branch_mobile ?>
                                <?php } ?>
                                <br>
                                <?php
                                if (isset($branch[0]->branch_email_address) && $branch[0]->branch_email_address != "") {
                                    ?>
                                    Email:  <?= $branch[0]->branch_email_address ?>
                                    <?php
                                }
                                if (isset($branch[0]->branch_gstin_number) && $branch[0]->branch_gstin_number != "") {
                                    ?>
                                    GSTIN: <?= $branch[0]->branch_gstin_number ?>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="col-sm-4 pull-right">                                   
                                <div class="bg-light-table">
                                    <b>Invoice Details</b>
                                    <?php
                                    echo' Invoice Number : ';
                                    echo $data[0]->expense_bill_invoice_number;
                                    ?>
                                    <?php
                                    echo '<br> Invoice Date : ';
                                    $date = $data[0]->expense_bill_date;
                                    $c_date = date('d-m-Y', strtotime($date));
                                    if (isset($data)) {
                                        echo $c_date;
                                    }
                                    ?>
                                </div> 
                            </div>  
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover table-bordered mt-15">
                                    <thead>
                                        <tr>
                                            <th class="span2" width="20%">Expense Type</th>
                                            <th class="span2" width="20%">Item Description</th>
                                            <th class="span2" width="12%">Amount</th>
                                            <?php if ($discount_exist > 0) { ?>
                                                <th rowspan="" style="text-align: center;">Discount</th>
                                            <?php } ?>
                                            <?php if ($data[0]->expense_bill_tds_amount > 0) { ?>
                                                <th class="span2" width="10%">TDS/TCS(%)</th>
                                            <?php } ?>
                                            <th class="span2" width="12%">Net Amount</th>
                                            <?php if ($igst_exist > 0) { ?>
                                                <th class="span2" width="10%">IGST(%)</th>
                                            <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                                <th class="span2" width="10%">CGST(%)</th>
                                                <th class="span2" width="10%"><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?>(%)</th>
                                            <?php } ?>
                                            <?php if ($cess_exist > 0) { ?>
                                                <th colspan="" style="text-align: center;">Cess</th>
                                            <?php } ?>
                                            <th class="span2" width="16%">Grand Total</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-weight: normal;">
                                        <?php
                                        $i = 1;
                                        foreach ($items as $value) {
                                            ?>
                                            <tr>
                                                <td><?php echo $value->expense_title; ?>
                                                    <?php if($value->expense_hsn_code != ''){ ?>
                                                    <br>(P) (HSN/SAC:
                                                        <?php echo $value->expense_hsn_code; ?>)
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $value->expense_bill_item_description; ?></td>
                                                <td align="right"><?php echo precise_amount($value->expense_bill_item_sub_total); ?></td>
                                                <?php if ($discount_exist > 0) { ?>
                                                    <td style="color: green;">( - ) <?= precise_amount($value->expense_bill_item_discount_amount); ?></td>
                                                <?php } ?>
                                                <?php if ($data[0]->expense_bill_tds_amount > 0) { ?>
                                                    <td align="right"><?php echo precise_amount($value->expense_bill_item_tds_amount); ?>(<?php echo (float)($value->expense_bill_item_tds_percentage); ?>%)</td>
                                                <?php } ?>
                                                <td align="right"><?php echo precise_amount($value->expense_bill_item_taxable_value); ?></td>
                                                <?php if ($igst_exist > 0) { ?>
                                                    <td align="right"><?php echo precise_amount($value->expense_bill_item_igst_amount); ?>(<?php echo (float)($value->expense_bill_item_igst_percentage); ?>%)</td>
                                                <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                                    <td align="right"><?php echo precise_amount($value->expense_bill_item_cgst_amount); ?>(<?php echo (float)($value->expense_bill_item_cgst_percentage); ?>%)</td>
                                                    <td align="right"><?php echo precise_amount($value->expense_bill_item_sgst_amount); ?>(<?php echo (float)($value->expense_bill_item_sgst_percentage); ?>%)</td>
                                                <?php } ?>
                                                <?php if ($cess_exist > 0) { ?>
                                                    <td align="right"><?php echo precise_amount($value->expense_bill_item_tax_cess_amount); ?>(<?php echo (float)($value->expense_bill_item_tax_cess_percentage); ?>%)</th>
                                                    <?php } ?>
                                                    <?php
                                                    // $a = bcsub($value->amount, $value->discount, 2);
                                                    // $b = bcadd($value->expense_bill_item_sub_total, $value->expense_bill_item_igst_amount, 2);
                                                    // $c = bcadd($b, $value->expense_bill_item_cgst_amount, 2);
                                                    // $d = bcadd($c, $value->expense_bill_sgst_amount, 2);
                                                    // $final = bcsub($d, $value->expense_bill_sgst_amount, 2);
                                                    ?>
                                                <td align="right"><?php echo precise_amount($value->expense_bill_item_grand_total); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <table  id="table-total" class="table table-hover table-bordered">
                                    <tbody>
                                        <tr>
                                            <td ><b>Total Value (<?php echo $currency_code; ?>)</b></td>
                                            <td><?php echo precise_amount($data[0]->expense_bill_sub_total); ?></td>
                                        </tr>
                                        <?php if ($discount_exist > 0) { ?>
                                            <tr>
                                                <td>Discount (<?= $currency_code; ?>)</td>
                                                <td style="color: green;">( - ) <?= precise_amount($data[0]->expense_bill_discount_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($data[0]->expense_bill_tds_amount > 0) { ?>
                                            <tr>
                                                <td ><b>TDS (<?php echo $currency_code; ?>)</b></td>

                                                <td style="color: red;">( + ) <?php echo precise_amount($data[0]->expense_bill_tds_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($igst_exist > 0) { ?>
                                            <tr>
                                                <td>IGST (<?= $currency_code; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->expense_bill_igst_amount); ?></td>
                                            </tr>
                                        <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                            <tr>
                                                <td>CGST (<?= $currency_code; ?>)</td>
                                                <td style="color: red;">( + )<?= precise_amount($data[0]->expense_bill_cgst_amount); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?> (<?= $currency_code; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->expense_bill_sgst_amount); ?></td>
                                            </tr>
                                        <?php } ?>                              
                                        <?php if ($cess_exist > 0) { ?>
                                            <tr>
                                                <td>Cess (<?= $currency_code; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->expense_bill_tax_cess_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($data[0]->total_freight_charge > 0) { ?>
                                            <tr>
                                                <td>Freight Charge (<?= $currency_code; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->freight_charge_tax_amount); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_insurance_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Insurance Charge (<?= $currency_code; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->insurance_charge_amount); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_packing_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Packing & Forwarding Charge (<?= $currency_code; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->packing_charge_amount); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_incidental_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Incidental Charge (<?= $currency_code; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->incidental_charge_amount); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_inclusion_other_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Other Inclusive Charge (<?= $currency_code; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->inclusion_other_charge_amount); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_exclusion_other_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Other Exclusive Charge (<?= $currency_code; ?>)</td>
                                                <td style="color: green;">( - ) <?= precise_amount($data[0]->exclusion_other_charge_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($data[0]->round_off_amount > 0) { ?>
                                            <tr>
                                                <td>Round Off (<?= $currency_code; ?>)</td>
                                                <td style="color: green;">( - ) <?= precise_amount($data[0]->round_off_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($data[0]->round_off_amount < 0) { ?>
                                            <tr>
                                                <td>Round Off (<?= $currency_code; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->round_off_amount * -1); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <td><b>Grand Total (<?= $currency_code; ?>)</b></td>
                                            <td style="color: blue;">( = ) <?= precise_amount($data[0]->expense_bill_grand_total); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <div class="buttons">
                                    <div class="btn-group btn-group-justified">
                                        <?php
                                        if (in_array($expense_bill_module_id, $active_view)) {
                                            ?>
                                            <div class="btn-group">
                                                <a class="tip btn btn-success" href="<?php echo base_url('expense_bill/pdf/'); ?><?php echo $expense_bill_id; ?>" title="Download as PDF" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                    <span class="hidden-sm hidden-xs">PDF</span>
                                                </a>
                                            </div>
                                            <?php
                                        }
                                        if (in_array($expense_bill_module_id, $active_edit)) {
                                            ?>
                                            <div class="btn-group">
                                                <a class="tip btn btn-warning tip" href="<?php echo base_url('expense_bill/edit/'); ?><?php echo $expense_bill_id; ?>" title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                    <span class="hidden-sm hidden-xs">Edit</span>
                                                </a>
                                            </div>
                                            <?php
                                        }
                                        if (in_array($expense_bill_module_id, $active_delete)) {
                                            ?>
                                            <div class="btn-group">
                                                <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="<?php echo $expense_bill_id; ?>" data-path="expense_bill/delete" class="delete_button tip btn btn-danger bpo" href="#" title="Delete Expense Bill">
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
