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
                        <h3 class="box-title">BOE Details</h3>
                        <?php
                        $boe_id = $this->encryption_url->encode($data[0]->boe_id);
                        ?>
                        <a class="btn btn-sm btn-default pull-right back_button" id="cancel" onclick1="cancel('boe')">Back</a>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <h2 class="page-header">
                                    <i class="fa fa-building-o"></i> <?= $branch[0]->firm_name ?>
                                    <small class="pull-right"> <?php
                                        $date = $data[0]->boe_date;
                                        $c_date = date('d-m-Y', strtotime($date));
                                        echo "Date : " . $c_date;
                                        ?></small>
                                </h2>
                            </div>
                        </div>
                        <div class="row invoice-info">
                            <div class="col-sm-4">
                                From
                                <address>
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
                            <div class="col-sm-4"></div>
                            <div class="col-sm-4">
                                <div class="bg-light-table">
                                    <strong>Invoice Details</strong><br>
                                    Invoice Number: <?php
                                    echo $data[0]->boe_number;
                                    ?><br>
                                    <?php
                                    $date = $data[0]->boe_date;
                                    $c_date = date('d-m-Y', strtotime($date));
                                    echo "Date : " . $c_date;
                                    ?>
                                    <?php
                                    if (isset($data[0]->CIN)) {
                                        if ($data[0]->CIN != "" || $data[0]->CIN != null) {
                                            echo '<br>CIN : ' . $data[0]->CIN;
                                        }
                                        if ($data[0]->CIN_date != "" || $data[0]->CIN_date != null) {
                                            if($data[0]->CIN_date != '0000-00-00'){
                                                echo '<br>CIN date : ' . date('d-m-Y', strtotime($data[0]->CIN_date));
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table width="100%" class="table table-hover table-bordered table-responsive">
                                    <thead>
                                        <tr>
                                            <th width="6%" style="text-align: center;">Sl No</th>
                                            <th >Items</th>
                                            <th style="text-align: center;">Quantity</th>
                                            <th style="text-align: center;">Rate</th>
                                            <th style="text-align: center;">Subtotal</th>
                                            
                                            <?php if ($bcd_exist > 0) { ?>
                                                <th  style="text-align: center;">BCD TAX (%)</th>
                                            <?php } ?>
                                            <?php if ($igst_exist > 0) { ?>
                                                <th  style="text-align: center;">IGST</th>
                                            <?php } ?>
                                            <?php if ($cess_exist > 0) { ?>
                                                <th  style="text-align: center;">Cess</th>
                                            <?php } ?>
                                            <?php if ($other_duties > 0) { ?>
                                                <th  style="text-align: center;">Other Duties</th>
                                            <?php } ?>
                                            <th  style="text-align: center;">Total</th>
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
                                                <td style="text-align: center;"><?= $i; ?></td>
                                                <?php if ($value->item_type == 'product' || $value->item_type == 'product_inventory') { ?>
                                                    <td style="text-align: left;">
                                                        <?= $value->product_name; ?>
                                                        <br>HSN/SAC: <?= $value->product_hsn_sac_code; ?>
                                                        <?php
                                                        if (isset($value->boe_item_description) && $value->boe_item_description != "") {
                                                            echo "<br/>";
                                                            echo $value->boe_item_description;
                                                        }
                                                        ?>
                                                    </td>
                                                <?php } else { ?>
                                                    <td style="text-align: left;"><?= $value->service_name; ?>
                                                        <br>HSN/SAC: <?= $value->service_hsn_sac_code; ?>
                                                        <?php
                                                        if (isset($value->boe_item_description) && $value->boe_item_description != "") {
                                                            echo "<br/>";
                                                            echo $value->boe_item_description;
                                                        }
                                                        ?>
                                                    </td>
                                                <?php } ?>
                                                <td style="text-align: center;"><?php echo $value->boe_item_quantity; 
                                                if ($value->product_unit != '') {
                                                        $unit = explode("-", $value->product_unit);
                                                        if($unit[0] != '') echo " <br>(" . $unit[0].')';
                                                    }?></td>
                                                <td style="text-align: right;"><?= precise_amount($value->boe_item_unit_price); ?></td>
                                                <td style="text-align: right;"><?= precise_amount($value->boe_item_sub_total); ?></td>
                                                <?php if ($bcd_exist > 0) { ?>
                                                    <td style="text-align: right;"><?= precise_amount($value->boe_item_bcd_amount); ?><br>
                                                        (<?= (float)($value->boe_item_bcd_percentage); ?>%)</td>
                                                <?php } ?>
                                                <?php if ($igst_exist > 0) { ?>
                                                    <td style="text-align: right;"><?= precise_amount($value->boe_item_igst_amount); ?><br>
                                                        (<?= (float)($value->boe_item_igst_percentage); ?>%)</td>
                                                <?php } ?>
                                                <?php if ($cess_exist > 0) { ?>
                                                    
                                                    <td style="text-align: right;"><?= precise_amount($value->boe_tax_cess_amount); ?><br>
                                                        (<?= (float)($value->boe_item_tax_cess_percentage); ?>%)</td>
                                                <?php } ?>
                                                <?php if ($other_duties > 0) { ?>
                                                    
                                                    <td style="text-align: right;"><?= precise_amount($value->boe_item_tax_other_duties_amount); ?><br>
                                                        (<?= (float)($value->boe_item_tax_other_duties_percentage); ?>%)</td>
                                                <?php } ?>
                                                <td style="text-align: right;"><?= precise_amount($value->boe_item_grand_total); ?></td>
                                            </tr>
                                            <?php
                                            $i++;
                                            $quantity += $value->boe_item_quantity;
                                            $price = bcadd($price, $value->boe_item_unit_price);
                                            $grand_total = bcadd($grand_total, $value->boe_item_grand_total);
                                        }
                                        ?>
                                        <tr>
                                            <th colspan="2"></th>
                                            <th  style="text-align: center;"><!--<?= $quantity ?>--></th>
                                            <th  style="text-align: center;"><?= precise_amount($price) ?></th>
                                            <th  style="text-align: center;"><?= precise_amount($data[0]->boe_sub_total) ?></th>
                                            <?php if ($bcd_exist  > 0) { ?>
                                                <th  style="text-align: center;"><?= precise_amount($data[0]->boe_bcd_amount) ?></th>
                                            <?php } ?>
                                            <?php if ($igst_exist > 0) { ?>
                                                <th style="text-align: center;"><?= precise_amount($data[0]->boe_tax_amount) ?></th>
                                            <?php } ?>
                                            <?php if ($cess_exist > 0) { ?>
                                                <th style="text-align: center;"><?= precise_amount($data[0]->boe_cess_amount) ?></th>
                                            <?php } ?>
                                            <?php if ($other_duties > 0) { ?>
                                                <th style="text-align: center;"><?= precise_amount($data[0]->boe_other_duties_amount) ?></th>
                                            <?php } ?>
                                            <th  style="text-align: center;"><?= precise_amount($data[0]->boe_grand_total); ?></th>
                                        </tr>
                                    </tbody>
                                </table>
                                <table  id="table-total" class="table table-hover table-bordered table-responsive">
                                    <tbody>
                                        <tr>
                                            <td ><b>Subtotal Value (<?= $currency_symbol; ?>)</b></td>
                                            <td><?= precise_amount($data[0]->boe_sub_total); ?></td>
                                        </tr>
                                        <?php if ($bcd_exist > 0) { ?>
                                            <tr>
                                                <td>BCD (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->boe_bcd_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($igst_exist > 0) { ?>
                                            <tr>
                                                <td>IGST (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->boe_tax_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($cess_exist > 0) { ?>
                                            <tr>
                                                <td>Cess (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->boe_cess_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($other_duties > 0) { ?>
                                            <tr>
                                                <td>Other Duties (<?= $currency_symbol; ?>)</td>
                                                <td style="color: red;">( + ) <?= precise_amount($data[0]->boe_other_duties_amount); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <td><b>Grand Total (<?= $currency_symbol; ?>)</b></td>
                                            <td style="color: blue;">( = ) <?= precise_amount($data[0]->boe_grand_total); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <div class="buttons">
                                    <div class="btn-group btn-group-justified">
                                        <?php
                                        if (in_array($boe_module_id, $active_view)) {
                                            ?>
                                            <div class="btn-group">
                                                <a class="tip btn btn-success" href="<?php echo base_url('boe/pdf/'); ?><?php echo $boe_id; ?>" title="Download as PDF" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                    <span class="hidden-sm hidden-xs">PDF</span>
                                                </a>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (in_array($boe_module_id, $active_edit)) {
                                                ?>
                                            <div class="btn-group">
                                                <a class="tip btn btn-warning tip" href="<?php
                                                echo base_url('boe/edit/');
                                                ?><?php
                                                echo $boe_id;
                                                ?>" title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                    <span class="hidden-sm hidden-xs">Edit</span>
                                                </a>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (in_array($boe_module_id, $active_delete)) {
                                            ?>
                                            <div class="btn-group">
                                                <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="<?php
                                                echo $boe_id;
                                                ?>" data-path="boe/delete_boe" class="delete_button tip btn btn-danger bpo" href="#" title="Delete boe">
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