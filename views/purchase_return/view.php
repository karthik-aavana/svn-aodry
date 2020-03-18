<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if (!$is_only_view)
    $this->load->view('layout/header');
if (!function_exists('precise_amount')) {
    $GLOBALS['common_settings_amount_precision'] = $access_common_settings[0]->amount_precision;

    function precise_amount($val) {
        $val = (float) $val;
        // $amt =  round($val,$GLOBALS['common_settings_amount_precision']);
        $dat = number_format($val, $GLOBALS['common_settings_amount_precision'], '.', '');
        return $dat;
    }
}
?>
<?php if (!$is_only_view) { ?>
    <div class="content-wrapper">        
        <section class="content-header">
            <h5>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="<?php echo base_url('purchase_return'); ?>">Purchase Return </a></li>
                    <li class="active">Purchase Return Details</li>
                </ol>
            </h5>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Purchase Return  Details</h3>
                            <a class="btn btn-sm btn-default pull-right back_button" id="cancel" onclick1="cancel('purchase_return')">Back</a>
                        </div>
                    <?php } ?>
                    <div class="box-body">                        
                        <div class="row">
                            <div class="col-sm-4">
                                <address>
                                    <?php
                                    if (isset($branch[0]->firm_name) && $branch[0]->firm_name != "") {
                                        ?>
                                        From,  <br>
                                        <strong>
                                            <?= $branch[0]->firm_name ?>
                                        </strong>
                                        <?php
                                    }
                                    ?>
                                    <br/>
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
                                        Location : <?php
                                        if (isset($branch[0]->branch_city_name) && $branch[0]->branch_city_name != "") {
                                            echo $branch[0]->branch_city_name . ",";
                                        }
                                        if (isset($branch[0]->branch_state_name) && $branch[0]->branch_city_name != "") {
                                            echo $branch[0]->branch_state_name . ",";
                                        }
                                        if (isset($branch[0]->branch_country_name) && $branch[0]->branch_city_name != "") {
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
                                        Email : <?= $branch[0]->branch_email_address ?>
                                        <?php
                                    }
                                    if (isset($branch[0]->branch_gstin_number) && $branch[0]->branch_gstin_number != "") {
                                        ?>
                                        GSTIN: <?= $branch[0]->branch_gstin_number ?>
                                        <?php
                                    }
                                    ?>
                                </address>
                                <address>
                                    To <br>
                                    <?php
                                    if (isset($data[0]->supplier_name) && $data[0]->supplier_name != "") {
                                        ?>
                                        <strong> <?= $data[0]->supplier_name ?></strong>
                                        <?php
                                    }
                                    ?>
                                    <br/>
                                    <?php
                                    if (isset($data[0]->supplier_address) && $data[0]->supplier_address != "") {
                                        ?> Address : <?php
                                        echo str_replace(array(
                                            "\r\n",
                                            "\\r\\n",
                                            "\n",
                                            "\\n"), "<br>", $data[0]->supplier_address);
                                        ?>                                        
                                        <?php
                                    }
                                    ?>
                                    <br/>
                                    <?php
                                    if (isset($data[0]->supplier_city_name) && $data[0]->supplier_city_name != "") {
                                        ?>                                   
                                        Location: <?php
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
                                    <br>
                                    <?php
                                    if (isset($data[0]->supplier_mobile) && $data[0]->supplier_mobile != "") {
                                        ?>
                                        Mobile : <?= $data[0]->supplier_mobile ?>
                                    <?php } ?>
                                    <br>
                                    <?php
                                    if (isset($data[0]->supplier_email) && $data[0]->supplier_email != "") {
                                        ?>
                                        Email : <?= $data[0]->supplier_email ?>
                                        <?php
                                    }
                                    if (isset($data[0]->supplier_gstin_number) && $data[0]->supplier_gstin_number != "") {
                                        ?>
                                        GSTIN : <?= $data[0]->supplier_gstin_number ?>
                                        <?php
                                    }
                                    ?>
                                </address>
                            </div>                            
                            <div class="col-sm-4 pull-right">
                                <div class="bg-light-table">
                                    <strong>Invoice Details</strong><br>
                                    Invoice Number :
                                    <?php echo $data[0]->purchase_return_invoice_number; ?>
                                    <br>                                    
                                    Invoice Date :
                                    <?php
                                    $date = $data[0]->purchase_return_date;
                                    $c_date = date('d-m-Y', strtotime($date));
                                    echo $c_date;
                                    ?>
                                </div>                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover table-bordered table-responsive">
                                    <thead>
                                        <tr>
                                            <th width="4%" align="center">S.No</th>
                                            <th align="center">Items</th>
                                            <?php
                                            if ($dpcount > 0) {
                                                ?>
                                                <th align="center">Description</th>
                                            <?php } ?>
                                            <th align="center">Quantity</th>
                                            <th align="center">Rate</th>
                                            <th align="center">Taxable Value</th>
                                            <?php
                                            if ($igst_tax < 1 && $cgst_tax > 1) {
                                                ?>
                                                <th align="center">CGST</th>
                                                <th align="center"><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></th>
                                                <?php
                                            } else if ($igst_tax > 1 && $cgst_tax < 1) {
                                                ?>
                                                <th align="center">IGST</th>
                                                <?php
                                            } else if ($data[0]->supplier_state_id == $branch[0]->branch_state_id) {
                                                ?>
                                                <th align="center">CGST</th>
                                                <th align="center"><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></th>
                                                <?php
                                            } else {
                                                ?>
                                                <th align="center">IGST</th>
                                                <?php
                                            }

                                            if ($cess_tax > 1) {
                                                ?>
                                                <th align="center">CESS</th>
                                            <?php }
                                            ?>
                                            <th class="center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $tot = 0;
                                        $igst = 0;
                                        $cgst = 0;
                                        $sgst = 0;
                                        $quantity = 0;
                                        $price = 0;
                                        $discount = 0;
                                        $cess = 0;
                                        foreach ($items as $value) {
                                            ?>
                                            <tr>
                                                <td align="center"><?php echo $i; ?></td>
                                                <?php
                                                if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {
                                                    ?>
                                                    <td ><?php echo $value->product_name; ?><br>HSN/SAC: <?php echo $value->product_hsn_sac_code; ?></td>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <td><?php echo $value->service_name; ?><br>HSN/SAC: <?php echo $value->service_hsn_sac_code; ?></td>
                                                <?php } ?>
                                                <?php
                                                if ($dpcount > 0) {
                                                    ?>
                                                    <td><?php echo $value->purchase_return_item_description; ?></td>
                                                <?php } ?>    
                                                <td style="text-align: center;"><?php echo round($value->purchase_return_item_quantity, 2); 
                                                if ($value->product_unit != '') {
                                                        $unit = explode("-", $value->product_unit);
                                                        if($unit[0] != '') echo " <br>(" . $unit[0].')';
                                                    }?></td>
                                                <td align="right"><?php echo precise_amount($value->purchase_return_item_unit_price); ?></td>
                                                <td align="right"><?php echo precise_amount($value->purchase_return_item_taxable_value); ?></td>
                                                <?php
                                                if ($igst_tax < 1 && $cgst_tax > 1) {
                                                    ?>
                                                    <td align="right">
                                                        <?php
                                                        if ($value->purchase_return_item_cgst_amount < 1) {
                                                            echo '-';
                                                        } else {
                                                            ?>
                                                            <?php echo precise_amount($value->purchase_return_item_cgst_amount); ?><br>(<?php echo round($value->purchase_return_item_cgst_percentage, 2); ?>%)
                                                        <?php } ?>
                                                    </td>
                                                    <td align="right">
                                                        <?php
                                                        if ($value->purchase_return_item_sgst_amount < 1) {
                                                            echo '-';
                                                        } else {
                                                            ?>
                                                            <?php echo precise_amount($value->purchase_return_item_sgst_amount); ?><br>(<?php echo round($value->purchase_return_item_sgst_percentage, 2); ?>%)
                                                        <?php } ?>
                                                    </td>
                                                    <?php
                                                } else if ($igst_tax > 1 && $cgst_tax < 1) {
                                                    ?>
                                                    <td align="right">
                                                        <?php
                                                        if ($value->purchase_return_item_igst_amount < 1) {
                                                            echo '-';
                                                        } else {
                                                            ?>
                                                            <?php echo precise_amount($value->purchase_return_item_igst_amount); ?><br>(<?php echo round($value->purchase_return_item_igst_percentage, 2); ?>%)
                                                        <?php } ?>
                                                    </td>
                                                    <?php
                                                } else if ($data[0]->supplier_state_id == $branch[0]->branch_state_id) {
                                                    ?>   
                                                    <td align="right">
                                                        <?php
                                                        if ($value->purchase_return_item_cgst_amount < 1) {
                                                            echo '-';
                                                        } else {
                                                            ?>
                                                            <?php echo precise_amount($value->purchase_return_item_cgst_amount); ?><br>(<?php echo round($value->purchase_return_item_cgst_percentage, 2); ?>%)
                                                        <?php } ?>
                                                    </td>
                                                    <td align="right">
                                                        <?php
                                                        if ($value->purchase_return_item_sgst_amount < 1) {
                                                            echo '-';
                                                        } else {
                                                            ?>
                                                            <?php echo precise_amount($value->purchase_return_item_sgst_amount); ?><br>(<?php echo round($value->purchase_return_item_sgst_percentage, 2); ?>%)
                                                        <?php } ?>
                                                    </td>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <td align="right">
                                                        <?php
                                                        if ($value->purchase_return_item_igst_amount < 1) {
                                                            echo '-';
                                                        } else {
                                                            ?>
                                                            <?php echo precise_amount($value->purchase_return_item_igst_amount); ?><br>(<?php echo round($value->purchase_return_item_igst_percentage, 2); ?>%)
                                                        <?php } ?>
                                                    </td>
                                                    <?php
                                                }
                                                if ($cess_tax > 1) {
                                                    ?>
                                                    <td align="right">
                                                        <?php
                                                        if ($value->purchase_return_item_cess_amount < 1) {
                                                            echo '-';
                                                        } else {
                                                            ?>
                                                            <?php echo precise_amount($value->purchase_return_item_cess_amount); ?><br>(<?php echo round($value->purchase_return_item_cess_percentage, 2); ?>%)
                                                        <?php } ?>
                                                    </td>
                                                <?php }
                                                ?>
                                                <?php ?>
                                                <td align="right"><?php echo precise_amount($value->purchase_return_item_grand_total); ?></td>
                                            </tr>
                                            <?php
                                            $i++;
                                            $quantity = bcadd($quantity, $value->purchase_return_item_quantity, 2);
                                            $price = bcadd($price, $value->purchase_return_item_unit_price, 2);
                                            $igst = bcadd($igst, $value->purchase_return_item_igst_amount, 2);
                                            $cgst = bcadd($cgst, $value->purchase_return_item_cgst_amount, 2);
                                            $sgst = bcadd($sgst, $value->purchase_return_item_sgst_amount, 2);
                                            $cess = bcadd($cess, $value->purchase_return_item_cess_amount, 2);
                                        }
                                        ?>
                                        <tr class="text-right">
                                            <?php
                                            if ($dpcount > 0) {
                                                ?>
                                                <td colspan="3"><b class="text-right">Total</b></td>
                                                <?php
                                            } else {
                                                ?>
                                                <td colspan="2" align="right">Total</td>
                                                <?php
                                            }
                                            ?>
                                            <th align="right"><!--<?php echo round($quantity, 2); ?>--></th>
                                            <th align="right"><?php echo precise_amount($price); ?></th>
                                            <th align="right"><?php echo precise_amount($data[0]->purchase_return_sub_total); ?></th>
                                            <?php
                                            if ($data[0]->purchase_return_igst_amount < 1 && $data[0]->purchase_return_cgst_amount > 1) {
                                                ?>
                                                <th align="right" ><?php echo precise_amount($cgst); ?></th>
                                                <th align="right"><?php echo precise_amount($sgst); ?></th>
                                                <?php
                                            } else if ($data[0]->purchase_return_igst_amount > 1 && $data[0]->purchase_return_cgst_amount < 1) {
                                                ?>
                                                <th align="right" ><?php echo precise_amount($igst); ?></th>
                                                <?php
                                            } else if ($data[0]->supplier_state_id == $branch[0]->branch_state_id) {
                                                ?>
                                                <th align="right"><?php echo precise_amount($cgst); ?></th>
                                                <th align="right"><?php echo precise_amount($sgst); ?></th>
                                                <?php
                                            } else {
                                                ?>
                                                <th align="right"><?php echo precise_amount($igst); ?></th>
                                                <?php
                                            }
                                            if ($cess_tax > 0) {
                                                ?>
                                                <th align="right"><?php echo precise_amount($cess); ?></th>
                                            <?php }
                                            ?>
                                            <?php ?>
                                            <th align="right"><?php echo precise_amount($data[0]->purchase_return_grand_total); ?></th>
                                        </tr>
                                    </tbody>
                                </table>
                                <table  id="table-total" class="table table-hover table-bordered table-responsive">
                                    <tbody>
                                        <tr>
                                            <td><b>Total Value</b></td>
                                            <td><?php echo precise_amount($data[0]->purchase_return_sub_total); ?></td>
                                        </tr>
                                        <?php
                                        if ($igst_tax > 0.00) {
                                            ?>
                                            <tr>
                                                <td>IGST ( + )</td>
                                                <td><?php echo precise_amount($igst); ?></td>
                                            </tr>
                                            <?php
                                        } else {
                                            ?>
                                            <tr>
                                                <td>CGST ( + )</td>
                                                <td> <?php echo precise_amount($cgst); ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?> ( + )</td>
                                                <td > <?php echo precise_amount($sgst); ?></td>
                                            </tr>
                                        <?php }
                                        ?>
                                        <?php
                                        if ($cess_tax > 0.00) {
                                            ?>
                                            <tr>
                                                <td>CESS ( + )</td>
                                                <td><?php echo precise_amount($cess); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if ($data[0]->total_freight_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Freight Charge ( + )</td>
                                                <td> <?php echo precise_amount($data[0]->total_freight_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_insurance_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Insurance Charge ( + )</td>
                                                <td > <?php echo precise_amount($data[0]->total_insurance_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_packing_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Packing & Forwarding Charge ( + )</td>
                                                <td > <?php echo precise_amount($data[0]->total_packing_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_incidental_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Incidental Charge ( + )</td>
                                                <td > <?php echo precise_amount($data[0]->total_incidental_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_inclusion_other_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Other Inclusive Charge ( + ) </td>
                                                <td><?php echo precise_amount($data[0]->total_inclusion_other_charge); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($data[0]->total_exclusion_other_charge > 0) {
                                            ?>
                                            <tr>
                                                <td>Other Exclusive Charge (-)</td>
                                                <td><?php echo precise_amount($data[0]->total_exclusion_other_charge); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <td><b>Grand Total (<?php echo $data[0]->currency_symbol; ?>)</b></td>
                                            <td style="color: blue;"><?php echo precise_amount($data[0]->purchase_return_grand_total); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- <div class="col-sm-12">
                                <div class="form-group">
                                    <hr>
                                    <p class="form-text text-muted terms-cond">
                            <?php
                            if ($data[0]->note) {
                                ?>
                                                                                                                                                                                                                            <strong>Note **:</strong> <?php
                                echo str_replace(array(
                                    "\r\n",
                                    "\\r\\n"), " &#10;", $data[0]->note);
                                ?><br>
                                <?php
                            } if ($data[0]->note) {
                                ?>
                                                                                                                                                                                                                            <strong>Note **:</strong> <?php
                                echo str_replace(array(
                                    "\r\n",
                                    "\\r\\n"), " &#10;", $data[0]->note2);
                                ?>
                            <?php } ?>
                                    </p>
                                </div>
                            </div> -->
                            <?php if (!$is_only_view) { ?>
                                <div class="col-sm-12">
                                    <div class="buttons">
                                        <div class="btn-group btn-group-justified">
                                            <!-- <div class="btn-group">
                                                <a class="tip btn btn-info tip" href="<?php echo base_url('purchase_return/email/'); ?><?php echo $data[0]->purchase_return_id; ?>" title="Email">
                                                    <i class="fa fa-envelope-o"></i>
                                                    <span class="hidden-sm hidden-xs">Email</span>
                                                </a>
                                            </div> -->
                                            <?php
                                            $purchase_return_id = $this->encryption_url->encode($data[0]->purchase_return_id);
                                            if (in_array($module_id, $active_view)) {
                                                ?>
                                                <div class="btn-group">
                                                    <a class="tip btn btn-success" href="<?php echo base_url('purchase_return/pdf/'); ?><?php echo $purchase_return_id; ?>" title="Download as PDF" target="_blank">
                                                        <i class="fa fa-file-pdf-o"></i>
                                                        <span class="hidden-sm hidden-xs">PDF</span>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                            <!-- <div class="btn-group">
                                                <a class="tip btn btn-success" href="<?php echo base_url('purchase_return/print1/'); ?><?php echo $purchase_return_id; ?>" title="Download as PDF" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                    <span class="hidden-sm hidden-xs">Print</span>
                                                </a>
                                            </div> -->
                                            <?php
                                            if (in_array($module_id, $active_edit)) {
                                                ?>
                                                <div class="btn-group">
                                                    <a class="tip btn btn-warning tip" href="<?php echo base_url('purchase_return/edit/'); ?><?php echo $purchase_return_id; ?>" title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                        <span class="hidden-sm hidden-xs">Edit</span>
                                                    </a>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <?php
                                            if (in_array($module_id, $active_delete)) {
                                                ?>
                                                <div class="btn-group">
                                                    <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="<?php echo $purchase_return_id; ?>" data-path="purchase_return/delete" class="delete_button tip btn btn-danger bpo" href="#" title="Delete Purchase Return">
                                                        <i class="fa fa-trash-o"></i>
                                                        <span class="hidden-sm hidden-xs">Delete</span>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if (!$is_only_view) { ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
    $this->load->view('layout/footer');
    $this->load->view('general/delete_modal');
    ?>
<?php } ?>