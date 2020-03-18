<?php
$GLOBALS['common_settings_amount_precision'] = $access_common_settings[0]->amount_precision;
function precise_amount($val) {
    $val = (float) $val;
    // $amt =  round($val,$GLOBALS['common_settings_amount_precision']);
    $dat = number_format($val, $GLOBALS['common_settings_amount_precision'], '.', '');
    return $dat;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            Expense Bill Invoice
        </title>
         <!-- <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/common_pdf.css">-->
        <style><?php $this->load->view('common/common_pdf');?></style>
    </head>
    <body>
        <span>
            <?php
            if (isset($branch[0]->firm_logo) && $branch[0]->firm_logo != "") {
                ?>
                <img class="img" src="<?php echo FCPATH . '/assets/branch_files/' . $branch[0]->branch_id . '/' . $branch[0]->firm_logo; ?>" height="30" />
                <?php
            } else {
                ?>
                <b class="uppercase">  <?= strtolower($branch[0]->firm_name) ?></b>
            <?php } ?>
        </span>  
        <span style="float: right; margin-top: -25px"><h3><u><?= (@$title ? $title : 'Expense Bill Invoice'); ?></u></h3></span>
        <table class="table header_table mt-20">
            <tr>
                <?php
                /*if ($pdf_results['show_from'] == 'yes') {*/
                    ?>
                    <td width="45%" valign="top" style="border:<?php echo ($pdf_results['bordered'] == 'yes') ? '' : 'none'; ?>;">
                        <b>From,</b><br>
                        <b style="text-transform: capitalize;">
                            <?php
                            if (isset($branch[0]->branch_name)) {
                                echo $branch[0]->branch_name;
                            }
                            ?>
                        </b>
                        <br>
                        <?php
                        /*if ($pdf_results['address'] == 'yes') {*/
                            if (isset($branch[0]->branch_address)) {
                                echo str_replace(array(
                                    "\r\n",
                                    "\\r\\n",
                                    "\n",
                                    "\\n"), "<br>", $branch[0]->branch_address);
                                ?>
                                <br>
                                <?php
                            }
                        /*}*/
                        /*if ($pdf_results['state'] == 'yes') {*/
                            if (isset($state[0]->state_name)) {
                                echo $state[0]->state_name . ",";
                            }
                        /*}*/
                        ?> <?php
                        /*if ($pdf_results['country'] == 'yes') {*/
                            if (isset($country[0]->country_name)) {
                                echo $country[0]->country_name . ".";
                                ?>
                                <?php
                            }
                        /*}*/
                        /*if ($pdf_results['mobile'] == 'yes') {*/
                            if (isset($branch[0]->branch_mobile)) {
                                if ($branch[0]->branch_mobile != "" || $branch[0]->branch_mobile != null) {
                                    echo '<br>Mobile No : ' . $branch[0]->branch_mobile;
                                }
                            }
                            ?>
                            <?php
                            /*if ($pdf_results['landline'] == 'yes') {*/
                                if (isset($branch[0]->branch_land_number)) {
                                    if ($branch[0]->branch_land_number != "") {
                                        echo ' | Landline No : ' . $branch[0]->branch_land_number;
                                    }
                                }
                           /* }*/
                            ?>
                            <?php
                        /*}*/
                        if (isset($branch[0]->branch_email_address)) {
                            // && ($pdf_results['email'] == 'yes')
                            if ($branch[0]->branch_email_address != "") {
                                echo '<br>E-mail : ' . $branch[0]->branch_email_address;
                            }
                            ?>
                            <?php
                        }
                        if (isset($branch[0]->branch_gstin_number)) {
                            // && ($pdf_results['gst'] == 'yes')
                            if ($branch[0]->branch_gstin_number != "" || $branch[0]->branch_gstin_number != null) {
                                echo " | GSTIN : " . $branch[0]->branch_gstin_number;
                            }
                            ?>
                            <?php
                        }
                        ?>
                        <?php
                        if (isset($branch[0]->branch_pan_number) && $branch[0]->branch_pan_number != "" ) {
                            //&& ($pdf_results['pan'] == 'yes')
                            if ($branch[0]->branch_pan_number != "" || $branch[0]->branch_pan_number != null) {
                                echo "<br />PAN : " . $branch[0]->branch_pan_number;
                            }
                            ?>
                            <?php
                        }
                        ?>
                        <?php
                        if (isset($branch[0]->branch_import_export_code_number)) {
                            // && ($pdf_results['iec'] == 'yes')
                            if ($branch[0]->branch_import_export_code_number != "" || $branch[0]->branch_import_export_code_number != null) {
                                echo " | IEC : " . $branch[0]->branch_import_export_code_number;
                            }
                            ?>
                            <?php
                        }
                        if (isset($branch[0]->branch_lut_number)) {// && ($pdf_results['lut'] == 'yes')
                            if ($branch[0]->branch_lut_number != "" || $branch[0]->branch_lut_number != null) {
                                echo "<br />LUT : " . $branch[0]->branch_lut_number;
                            }
                            ?>
                            <?php
                        }
                        if (isset($branch[0]->branch_cin_number)) {// && ($pdf_results['lut'] == 'yes')
                            if ($branch[0]->branch_cin_number != "" || $branch[0]->branch_cin_number != null) {
                                echo " | CIN : " . $branch[0]->branch_cin_number;
                            }
                        }
                        ?>
                        <br><br>
                        <b>To,</b><br>
                        <b style="text-transform: capitalize;"><?php
                            if (isset($data[0]->supplier_name)) {
                                // && ($pdf_results['to_company'] == 'yes')
                                echo $data[0]->supplier_name;
                            }
                            ?>
                        </b>
                        <?php
                        if (isset($data[0]->supplier_gstin_number)) {
                            if ($data[0]->supplier_gstin_number != "") {
                                echo "<br/>GSTIN : ";
                                echo $data[0]->supplier_gstin_number;
                            }
                        }
                        ?><br>
                            <?php
                            if (isset($data[0]->supplier_address)) {
                                // && ($pdf_results['to_address'] == 'yes')
                                echo str_replace(array(
                                    "\r\n",
                                    "\\r\\n",
                                    "\n",
                                    "\\n"), "<br>", $data[0]->supplier_address);
                            }
                            ?>
                        <br>
                        <?php
                        if (isset($data[0]->supplier_city_name)) {
                            echo $data[0]->supplier_city_name;
                        }
                        ?>  <?php
                        if (isset($data[0]->supplier_postal_code)) {
                            if ($data[0]->supplier_postal_code != "") {
                                echo "-" . $data[0]->supplier_postal_code;
                            }
                        }
                        ?>
                        <br>
                        <?php
                        if (isset($data[0]->supplier_state_name)) {
                            // && ($pdf_results['to_state'] == 'yes')
                            echo $data[0]->supplier_state_name;
                        }
                        ?>,
                        <?php
                        if (isset($data[0]->supplier_country_name)) {
                            // && ($pdf_results['to_country'] == 'yes')
                            echo $data[0]->supplier_country_name;
                        }
                        ?>
                        <?php
                        if (isset($data[0]->contact_person)) {
                            if ($data[0]->contact_person != "") {
                            echo '<br>Contact Person : ' . $data[0]->contact_person;
                            }
                        }
                        ?>
                        <?php
                        if (isset($data[0]->department)) {
                            if ($data[0]->department != "") {
                            echo '<br>Department : ' . $data[0]->department;
                            }
                        }
                        ?>
                        <?php
                        if (isset($data[0]->supplier_mobile)) {
                            // && ($pdf_results['to_mobile'] == 'yes')
                            if ($data[0]->supplier_mobile != "") {
                                echo '<br>Mobile No : ' . $data[0]->supplier_mobile;
                            }
                        }
                        ?>
                        <br>
                        <?php
                        if (isset($data[0]->supplier_email)) {
                            // && ($pdf_results['to_email'] == 'yes')
                            if ($data[0]->supplier_email != "") {
                                echo ' E-mail : ' . $data[0]->supplier_email;
                            }
                        }
                        ?>
                        <?php
                        if (isset($data[0]->supplier_state_code) && $data[0]->supplier_state_code != 0) {
                            // && ($pdf_results['to_state_code'] == 'yes')
                            echo '<br>State Code : ' . $data[0]->supplier_state_code;
                        }
                        ?>
                        <?php
                        if (isset($data[0]->place_of_supply)) {
                            // && ($pdf_results['place_of_supply'] == 'yes')
                            if ($data[0]->place_of_supply != "" || $data[0]->place_of_supply != null) {
                                echo '<br>Place of Supply : ' . $data[0]->place_of_supply;
                            }
                        }
                        if (isset($data[0]->shipping_address_sa)) {
                            if ($data[0]->shipping_address_sa != "" || $data[0]->shipping_address_sa != null) {
                                echo '<br><br><b>Shipping Address</b><br>' . str_replace(array(
                                    "\r\n",
                                    "\\r\\n",
                                    "\n",
                                    "\\n"), "<br>", $data[0]->shipping_address_sa);
                            }
                        }
                        if (isset($data[0]->shipping_gstin)) {
                            if ($data[0]->shipping_gstin != "" || $data[0]->shipping_gstin != null) {
                                echo '<br>Shipping GSTIN : ' . $data[0]->shipping_gstin;
                            }
                        }
                    ?>
                    </td>                    
                    <td valign="top" width="34%" class="bg-table">                   
                    Expense Bill Date:
                    <?php
                    $date = $data[0]->expense_bill_date;
                    $date_for = date('d-m-Y', strtotime($date));
                    ?>
                    <span style="font-weight:bold"><?php
                        if (isset($data[0]->expense_bill_date)) {
                            echo $date_for;
                        }
                        ?>
                    </span>
                    <br>
                    Expense Bill Number:
                    <span style="font-weight:bold"><?php
                        if (isset($data[0]->expense_bill_invoice_number)) {
                            echo $data[0]->expense_bill_invoice_number;
                        }
                        ?>
                    </span>
                </td> 
                <?php
                if (@$access_common_settings[0]->affliation_images) {
                    if ($access_common_settings[0]->affliation_images != null) {
                        /*if ($pdf_results['l_r'] != 'yes' && $pdf_results['display_affliate'] == 'yes') {*/
                            $json_str = str_replace("\\", "", $access_common_settings[0]->affliation_images);
                            $arr = json_decode($json_str);
                            ?>
                            <td  width="50%" valign="top" style="text-align: center;border:<?php echo ($pdf_results['bordered'] == 'yes') ? '' : 'none'; ?>;">
                                <img width="200" height="150" style="margin: 20px 0px" src="<?= base_url('assets/affiliate/' . $sid . '/' . $arr[0]) ?>">
                            </td>
                            <?php
                       /* }*/
                    }
                }
                ?>
            </tr>
        </table>        
        <table class="table mt-20 item-table">
            <thead>
                <?php
                if ($igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 || $tdscount > 0) {
                    ?>
                    <tr style="background-color: <?= $pdf_results['background']; ?>">
                        <th width="3%">#</th>
                        <th>Expense Type</th>
                        <?php
                        if ($dpcount > 0) {
                            ?>
                            <th>Item Description</th>
                        <?php } ?>
                        <th>Amount</th>
                        <?php if ($discount_exist > 0) { ?>
                            <th>Discount</th>
                        <?php } ?>
                        <?php
                        if ($tdscount > 0) {
                            ?>
                            <th>TDS/TCS</th>
                            <?php
                        }
                        ?>
                        <?php
                        if ($cgst_exist > 0 && $sgst_exist > 0) {
                            ?>
                            <th>CGST</th>
                            <th>SGST</th>
                            <?php
                        } else if ($igst_exist > 0) {
                            ?>
                            <th>IGST</th>
                            <?php
                        }
                        ?>
                        <?php
                        if ($cess_exist > 0) {
                            ?>
                            <th>CESS</th>
                            <?php
                        }
                        ?>
                        <th>Grand Total</th>
                    </tr>
                <?php } ?>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $grand_total = $tot_cgst = $tot_sgst = $tot_igst = 0;
                foreach ($items as $value) {
                    ?>
                    <tr>
                        <td><?php echo $i;?></td>
                        <td><?php echo $value->expense_title; ?>
                            <?php if($value->expense_hsn_code != ''){ ?>
                            <br>(P) (HSN/SAC: 
                                <?php echo $value->expense_hsn_code; ?>)
                            <?php } ?>
                        </td>
                        <?php
                        if ($dpcount >= 1) {
                            ?>
                            <td><?php echo $value->expense_bill_item_description; ?></td>
                        <?php } ?>
                        <td align="right"><?php echo precise_amount($value->expense_bill_item_sub_total); ?></td>
                        <?php if ($discount_exist > 0) { ?>
                            <td align="right"> <?= precise_amount($value->expense_bill_item_discount_amount); ?></td>
                        <?php } ?>
                        <?php
                        if ($tdscount > 0) {
                            ?>
                            <td align="right"><?php echo precise_amount($value->expense_bill_item_tds_amount); ?><br>(<?php echo round($value->expense_bill_item_tds_percentage, 2); ?>%)</td>
                            <?php
                        }
                        ?>
                        <?php
                        if ($sgst_exist > 0 && $cgst_exist > 0) {
                            ?>
                            <td align="right"><?php echo precise_amount($value->expense_bill_item_cgst_amount); ?><br>(<?php echo round($value->expense_bill_item_cgst_percentage, 2); ?>%)</td>
                            <td align="right"><?php echo precise_amount($value->expense_bill_item_sgst_amount); ?><br>(<?php echo round($value->expense_bill_item_sgst_percentage, 2); ?>%)</td>
                            <?php
                        } else if ($igst_exist > 0) {
                            ?>
                            <td align="right"><?php echo precise_amount($value->expense_bill_item_igst_amount); ?><br>(<?php echo round($value->expense_bill_item_igst_percentage, 2); ?>%)</td>
                            <?php
                        }
                        ?> 
                        <?php
                        if ($cess_exist > 0) {
                            ?>
                            <td align="right"><?php echo precise_amount($value->expense_bill_item_tax_cess_amount); ?><br>(<?php echo round($value->expense_bill_item_tax_cess_percentage, 2); ?>%)</td>
                            <?php
                        }
                        ?>                                           
                        <td align="right"><?php echo precise_amount($value->expense_bill_item_grand_total); ?></td>
                    </tr>
                    <?php
                    $grand_total = bcadd($grand_total, $value->expense_bill_item_grand_total, 2);
                    if ($igst_exist > 0) $tot_igst += $value->expense_bill_item_igst_amount;
                    if ($sgst_exist > 0) $tot_sgst += $value->expense_bill_item_sgst_amount;
                    if ($cgst_exist > 0) $tot_cgst += $value->expense_bill_item_cgst_amount;
                    $i++;
                }
                ?>
                <tr>
                    <th colspan="2" align="right">Total</th>
                    <?php
                    if ($dpcount > 0) {
                        ?>
                        <th></th>
                    <?php } ?>
                    <th style="text-align: right;"><?= precise_amount($data[0]->expense_bill_sub_total); ?></th>
                    <?php
                    if ($discount_exist > 0) {
                        ?>
                        <th style="text-align: right;"><?= precise_amount($data[0]->expense_bill_discount_amount); ?></th>
                    <?php } ?>
                    <?php
                    if ($tdscount > 0) {
                        ?>
                        <th style="text-align: right;"><?= precise_amount($data[0]->expense_bill_tds_amount); ?></th>
                    <?php } ?>
                    <?php
                    if ($sgst_exist > 0 && $cgst_exist > 0) {
                        ?>
                        <th style="text-align: right;"><?= precise_amount($tot_cgst); ?></th>
                        <th style="text-align: right;"><?= precise_amount($tot_sgst); ?></th>
                    <?php } ?>
                    <?php
                    if ($igst_exist > 0) {
                        ?>
                        <th style="text-align: right;"><?= precise_amount($tot_igst); ?></th>
                    <?php } ?>
                    <?php
                    if ($cess_exist) {
                        ?>
                        <th style="text-align: right;"><?= precise_amount($data[0]->expense_bill_tax_cess_amount); ?></th>
                    <?php } ?>
                    <th style="text-align: right;"><?= precise_amount($grand_total); ?></th>
                </tr>
            </tbody>
        </table>
        <table class="mt-20">
            <tr>
                <td style="width:68%; margin-right: 2%">
                    <table class="dashed_table">
                        <?php if ($data[0]->total_other_amount != 0) { ?>
                        <thead>
                            <tr>
                                <th>Particulars</th>
                                <th>Amount</th>
                                <?php if ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                <th>CGST</th>
                                <th><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></th>
                                <?php }else{ ?>
                                <th>IGST</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $tax_split_percentage = $access_common_settings[0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            ?>
                            <?php if ($data[0]->total_freight_charge > 0) { ?>
                            <tr>
                                <th>Freight Charges</th>
                                <td><?php echo precise_amount($data[0]->total_freight_charge); ?> /-</td>

                                <?php if ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                <td><?=($data[0]->freight_charge_tax_amount > 0 ? precise_amount(($data[0]->freight_charge_tax_amount * $cgst_amount_percentage) / 100).' ('.(float)(($data[0]->freight_charge_tax_percentage * $cgst_amount_percentage) / 100).'%)' : '-');?>
                                   
                                </td>
                                <td><?=($data[0]->freight_charge_tax_amount > 0 ? precise_amount(($data[0]->freight_charge_tax_amount * $sgst_amount_percentage) / 100).' ('.(float)(($data[0]->freight_charge_tax_percentage * $sgst_amount_percentage) / 100).'%)' : '-');?></td>
                                <?php }else{ ?>
                                <td><?=($data[0]->freight_charge_tax_amount > 0 ? precise_amount($data[0]->freight_charge_tax_amount).' ('.(float)($data[0]->freight_charge_tax_percentage).'%)' : '-');?></td>
                                <?php } ?>
                            </tr>
                            <?php } 
                            if ($data[0]->total_insurance_charge > 0) { ?>
                             <tr>
                                <th>Insurance Charges</th>
                                <td><?php echo precise_amount($data[0]->total_insurance_charge); ?> /-</td>
                                <?php if ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                <td><?=($data[0]->insurance_charge_tax_amount > 0 ? precise_amount(($data[0]->insurance_charge_tax_amount * $cgst_amount_percentage) / 100).' ('.(float)(($data[0]->insurance_charge_tax_percentage * $sgst_amount_percentage) / 100).'%)' : '-' );?></td>
                                <td><?=($data[0]->insurance_charge_tax_amount > 0 ? precise_amount(($data[0]->insurance_charge_tax_amount * $sgst_amount_percentage) / 100).' ('.(float)(($data[0]->insurance_charge_tax_percentage * $sgst_amount_percentage) / 100).'%)' : '-' );?></td>
                                <?php }else{ ?>
                                <td><?=($data[0]->insurance_charge_tax_amount > 0 ? precise_amount($data[0]->insurance_charge_tax_amount).' ('.(float)($data[0]->insurance_charge_tax_percentage).'%)' : '-');?></td>
                                <?php } ?>
                            </tr>
                            <?php }
                            if ($data[0]->total_packing_charge > 0) { ?>
                             <tr>
                                <th>Pack & Forward Charges</th>
                                <td><?php echo precise_amount(($data[0]->total_packing_charge )); ?> /-</td>
                                <?php $data[0]->packing_charge_tax_amount = ($data[0]->packing_charge_tax_amount ); ?>
                                <?php if ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                <td><?=($data[0]->packing_charge_tax_amount > 0 ? precise_amount(($data[0]->packing_charge_tax_amount * $cgst_amount_percentage) / 100).' ('.(float)(($data[0]->packing_charge_tax_percentage * $sgst_amount_percentage) / 100).'%)' : '-');?></td>
                                <td><?=($data[0]->packing_charge_tax_amount > 0 ? precise_amount(($data[0]->packing_charge_tax_amount * $sgst_amount_percentage) / 100).' ('.(float)(($data[0]->packing_charge_tax_percentage * $sgst_amount_percentage) / 100).'%)' : '-');?></td>
                                <?php }else{ ?>
                                <td><?=($data[0]->packing_charge_tax_amount > 0 ? precise_amount($data[0]->packing_charge_tax_amount).' ('.(float)($data[0]->packing_charge_tax_percentage).'%)' : '-');?></td>
                                <?php } ?>
                            </tr>
                            <?php }
                            if ($data[0]->total_incidental_charge > 0) { ?>
                             <tr>
                                <th>Incidental Charges</th>
                                <td><?php echo precise_amount(($data[0]->total_incidental_charge )); ?> /-</td>
                                <?php $data[0]->incidental_charge_tax_amount = ($data[0]->incidental_charge_tax_amount ); ?>
                                <?php if ($cgst_exist > 0 || $sgst_exist > 0) {?>
                                <td><?=($data[0]->incidental_charge_tax_amount > 0 ? precise_amount(($data[0]->incidental_charge_tax_amount * $cgst_amount_percentage) / 100).' ('.(float)(($data[0]->incidental_charge_tax_percentage * $sgst_amount_percentage) / 100).'%)' : '-' );?></td>
                                <td><?=($data[0]->incidental_charge_tax_amount > 0 ? precise_amount(($data[0]->incidental_charge_tax_amount * $sgst_amount_percentage) / 100).' ('.(float)(($data[0]->incidental_charge_tax_percentage * $sgst_amount_percentage) / 100).'%)' : '-' );?></td>
                                <?php }else{ ?>
                                <td><?=($data[0]->incidental_charge_tax_amount > 0 ? precise_amount($data[0]->incidental_charge_tax_amount).' ('.(float)($data[0]->incidental_charge_tax_percentage).'%)' : '-');?></td>
                                <?php } ?>
                            </tr>
                            <?php }
                            if ($data[0]->total_inclusion_other_charge > 0) { ?>
                            <tr>
                                <th>Other Inclusive Charges</th>
                                <td><?php echo precise_amount($data[0]->total_inclusion_other_charge); ?> /-</td>
                                <?php if ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                <td><?=($data[0]->inclusion_other_charge_tax_amount > 0 ? precise_amount(($data[0]->inclusion_other_charge_tax_amount * $cgst_amount_percentage) / 100).' ('.(float)(($data[0]->inclusion_other_charge_tax_percentage * $sgst_amount_percentage) / 100).'%)' : '-');?></td>
                                <td><?=($data[0]->inclusion_other_charge_tax_amount > 0 ? precise_amount(($data[0]->inclusion_other_charge_tax_amount * $sgst_amount_percentage) / 100).' ('.(float)(($data[0]->inclusion_other_charge_tax_percentage * $sgst_amount_percentage) / 100).'%)' : '-');?></td>
                                <?php }else{ ?>
                                <td><?=($data[0]->inclusion_other_charge_tax_amount > 0 ? precise_amount($data[0]->inclusion_other_charge_tax_amount).' ('.(float)($data[0]->inclusion_other_charge_tax_percentage).'%)' : '-');?></td>
                                <?php } ?>
                            </tr>
                            <?php }
                            if ($data[0]->total_exclusion_other_charge > 0) { ?>
                            <tr>
                                <th>Other Exclusive Charge</th>
                                <td><?php echo precise_amount($data[0]->total_exclusion_other_charge); ?> /-</td>
                                <?php if ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                                <td><?=($data[0]->exclusion_other_charge_tax_amount > 0 ? precise_amount(($data[0]->exclusion_other_charge_tax_amount * $cgst_amount_percentage) / 100).' ('.(float)(($data[0]->exclusion_other_charge_tax_percentage * $sgst_amount_percentage) / 100).'%)' : '-');?></td>
                                <td><?=($data[0]->exclusion_other_charge_tax_amount > 0 ? precise_amount(($data[0]->exclusion_other_charge_tax_amount * $sgst_amount_percentage) / 100).' ('.(float)(($data[0]->exclusion_other_charge_tax_percentage * $sgst_amount_percentage) / 100).'%)' : '-');?></td>
                                <?php }else{ ?>
                                <td><?=($data[0]->exclusion_other_charge_tax_amount > 0 ? precise_amount($data[0]->exclusion_other_charge_tax_amount).' ('.(float)($data[0]->exclusion_other_charge_tax_percentage).'%)' : '-');?></td>
                                <?php } ?>
                            </tr>
                            <?php } ?>
                        </tbody>
                    <?php } ?>
                    </table>     
                        <!-- <?php if ($data[0]->total_freight_charge > 0 || $data[0]->total_insurance_charge > 0 || $data[0]->total_packing_charge > 0 || $data[0]->total_incidental_charge > 0 || $data[0]->total_inclusion_other_charge > 0 || $data[0]->total_exclusion_other_charge > 0) { ?>
                        <thead>
                            <tr>
                                <?php if ($data[0]->total_freight_charge > 0){ ?>
                                <th>Freight Charges</th>
                                <?php }
                                if ($data[0]->total_insurance_charge > 0){ ?>
                                <th>Insurance Charges</th>
                                <?php }
                                if ($data[0]->total_packing_charge > 0){ ?>
                                <th>Pack & Forward Charges</th>
                                <?php }
                                if ($data[0]->total_incidental_charge > 0){ ?>
                                <th>Incidental Charges</th>
                                <?php }
                                if ($data[0]->total_inclusion_other_charge > 0){ ?>
                                <th>Other Inclusive Charges</th>
                                <?php }
                                if ($data[0]->total_exclusion_other_charge > 0){ ?>
                                <th>Other Exclusive Charge</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php if ($data[0]->total_freight_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_freight_charge )); ?> /-</th>
                                <?php }
                                if ($data[0]->total_insurance_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_insurance_charge )); ?> /-</th>
                                <?php }
                                if ($data[0]->total_packing_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_packing_charge )); ?> /-</th>
                                <?php }
                                if ($data[0]->total_incidental_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_incidental_charge )); ?> /-</th>
                                <?php }
                                if ($data[0]->total_inclusion_other_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_inclusion_other_charge )); ?> /-</th>
                                <?php }
                                if ($data[0]->total_exclusion_other_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_exclusion_other_charge )); ?> /-</th>
                                <?php } ?>
                            </tr>
                        </tbody>
                        <?php  }  ?>
                    </table> -->   
                </td>
                <td style="width:30%">
                    <table class="total_sum">
                        <tr>
                            <td align="right"><b>Total Value</b></td>
                            <td align="right"><?php echo $this->numbertowords->formatInr($data[0]->expense_bill_sub_total); ?></td>
                        </tr>
                        <?php
                        if ($discount_exist > 0) {
                            ?>
                            <tr>
                                <td align="right">Discount (-)</td>
                                <td align="right"><?php echo $this->numbertowords->formatInr($data[0]->expense_bill_discount_amount); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <?php
                        if ($data[0]->expense_bill_tds_amount > 0) {
                            ?>                           
                            <tr>
                                <td align="right">TDS/TCS</td>
                                <td align="right"><?php echo $this->numbertowords->formatInr($data[0]->expense_bill_tds_amount); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <?php
                        if ($igst_exist > 0) {
                            ?>
                            <tr>
                                <td align="right">IGST (+)</td>
                                <td align="right"><?php echo $this->numbertowords->formatInr($data[0]->expense_bill_igst_amount); ?></td>
                            </tr>
                            <?php
                        } elseif ($cgst_exist > 0 || $sgst_exist > 0) {
                            ?>
                            <tr>
                                <td align="right">CGST (+)</td>
                                <td align="right"> <?php echo $this->numbertowords->formatInr($data[0]->expense_bill_cgst_amount); ?></td>
                            </tr>
                            <tr>
                                <td align="right"><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?> (+)</td>
                                <td align="right"><?php echo $this->numbertowords->formatInr($data[0]->expense_bill_sgst_amount); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <?php if ($cess_exist > 0) { ?>
                            <tr>
                                <td align="right">CESS (+)</td>
                                <td align="right"> <?= $this->numbertowords->formatInr($data[0]->expense_bill_tax_cess_amount); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($data[0]->total_freight_charge > 0) { ?>
                            <tr>
                                <td align="right">Freight Charge</td>
                                <td align="right"><?= $this->numbertowords->formatInr($data[0]->total_freight_charge); ?></td>
                            </tr>
                            <?php
                        }
                        if ($data[0]->total_insurance_charge > 0) { ?>
                            <tr>
                                <td align="right">Insurance Charge</td>
                                <td align="right"><?= $this->numbertowords->formatInr($data[0]->total_insurance_charge); ?></td>
                            </tr>
                            <?php
                        }
                        if ($data[0]->total_packing_charge > 0) {
                            ?>
                            <tr>
                                <td align="right">Packing & Forwarding Charge</td>
                                <td align="right"><?= $this->numbertowords->formatInr($data[0]->total_packing_charge); ?></td>
                            </tr>
                            
                            <?php
                        }
                        if ($data[0]->total_incidental_charge > 0) {
                            ?>
                            <tr>
                                <td align="right">Incidental Charge </td>
                                <td align="right"><?= $this->numbertowords->formatInr($data[0]->total_incidental_charge); ?></td>
                            </tr>
                            
                            <?php
                        }
                        if ($data[0]->total_inclusion_other_charge > 0) {
                            ?>
                            <tr>
                                <td align="right">Other Inclusive Charge </td>
                                <td align="right"><?= $this->numbertowords->formatInr($data[0]->total_inclusion_other_charge); ?></td>
                            </tr>
                           
                            <?php
                        }
                        if ($data[0]->total_exclusion_other_charge > 0) {
                            ?>
                            <tr>
                                <td align="right">Other Exclusive Charge (-) </td>
                                <td align="right"><?= $this->numbertowords->formatInr($data[0]->total_exclusion_other_charge); ?></td>
                            </tr>
                        <?php } ?>
                        <?php
                        if ($data[0]->round_off_amount > 0) {
                            ?>
                            <tr>
                                <td align="right">Round Off (+)</td>
                                <td align="right"><?php echo $this->numbertowords->formatInr($data[0]->round_off_amount); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <?php
                        if ($data[0]->round_off_amount < 0) {
                            ?>
                            <tr>
                                <td align="right">Round Off (-)</td>
                                <td align="right"> <?php echo $this->numbertowords->formatInr($data[0]->round_off_amount * -1);
                            ?></td>
                            </tr>
                            <?php
                        }
                        ?>  
                        <tr> 
                            <td align="right"><b>Grand Total(<img class="img" src="<?php echo FCPATH . '/assets/images/currency/'.$currency_symbol_pdf; ?>" style="height: 9px"  />)</b></td>
                            <td align="right"><?php echo $this->numbertowords->formatInr($data[0]->expense_bill_grand_total); ?>/-</td>
                        </tr>       
                    </table>
                </td>
            </tr>
        </table>
        <div class="mt-50">            
            <p class="amount_text">
                Amount (in Words) : <b><?php echo $currency_text . " " . $this->numbertowords->convert_number($data[0]->expense_bill_grand_total,$data[0]->unit,$data[0]->decimal_unit) . " Only"; ?> </b><br/>
            </p>
        </div>
        <?php
        if (isset($data[0]->expense_bill_type_of_supply) && $data[0]->expense_bill_type_of_supply != "regular") {
            ?>
            <table class="mt-50">
                <tr>
                    <td valign="top">
                        <?php
                        if (isset($data[0]->expense_bill_type_of_supply) && $data[0]->expense_bill_type_of_supply == "export_with_payment") {
                            ?>
                            <p><b>NOTE : </b>SUPPLY MEANT FOR EXPORT ON PAYMENT OF INTEGRATED TAX</p>
                            <?php
                        } else if (isset($data[0]->expense_bill_type_of_supply) && $data[0]->expense_bill_type_of_supply == "export_without_payment") {
                            ?>
                            <p><b>NOTE : </b>SUPPLY MEANT FOR EXPORT UNDER BOND OR LETTER OF UNDERTAKING WITHOUT PAYMENT OF INTEGRATED TAX</p>
                        <?php } ?>
                    </td>
                </tr>
            </table>
        <?php } ?>
       <!--  <?php
        if ($notes_sub_module_id > 0) {
            $this->load->view('note_template/display_note');
        }
        ?>  -->
 <?php if ($data[0]->note1 != '' || $data[0]->note2 != '') { ?>   
           <table class="mt-50" style=" border-collapse: separate; border-spacing:10px;">
                <tbody>
                    <tr>
                        <?php if ($data[0]->note1 != '') { 
                            $content1 = $data[0]->note1;
                            if(!empty($template1)){
                                foreach ($template1[0] as $key => $value) {
                                    $content1 = $value->content;
                                }
                            }
                            
                            ?>
                            <td style="width: 300px; border: 1px solid #333; padding: 10px"><?= str_replace(array(
                            "\r\n",
                            "\\r\\n",
                            "\n",
                            "\\n"), "<br>", $content1); ?></td>
                        <?php } if ($data[0]->note2 != '') { 
                            $content2 = $data[0]->note2;
                            if(!empty($template2)){
                                foreach ($template2[0] as $key => $value) {
                                    $content2 = $value->content;
                                }
                            } ?>
                             <td style="width: 300px; border: 1px solid #333; padding: 10px"><?= str_replace(array(
                            "\r\n",
                            "\\r\\n",
                            "\n",
                            "\\n"), "<br>", $content2); ?></td>
                        <?php } ?>
                    </tr>
                </tbody>
            </table>  
        <?php } ?>  


              
        <?php        
        /*if ($pdf_results['display_affliate'] == 'yes') {*/
            ?>
            <table class="table mt-20">
                <tr>
                    <?php
                    if ($access_common_settings[0]->affliation_images != null) {
                        $json_str = str_replace("\\", "", $access_common_settings[0]->affliation_images);
                        $arr = json_decode($json_str);
                        ?>
                        <td class="text-center" style="text-align: center;border:none">
                            <?php
                            foreach ($arr as $key) {
                                ?>
                                <img  width="100" height="50" src="<?= base_url('assets/affiliate/' . $sid . '/' . $key) ?>">
                                <?php
                            }
                            ?>
                        </td>
                    <?php } ?>
                </tr>
            </table>
            <?php
       /* }*/
        ?>
        <?php
        if (isset($access_common_settings[0]->invoice_footer) && $access_common_settings[0]->invoice_footer != "") {
            ?>
            <div class="footer"><?php echo $access_common_settings[0]->invoice_footer; ?></div>            
        <?php  }  ?> 
</body>
</html>