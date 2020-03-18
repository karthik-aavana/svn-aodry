<?php
$GLOBALS['common_settings_amount_precision'] = $access_common_settings[0]->amount_precision;
$convert_rate = 1;
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
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>
            Purchase Invoice
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
        <span style="float: right; margin-top: -25px"><h3><u><?= (@$title ? $title : 'Purchase Invoice'); ?></u></h3></span>
        <table class="table header_table mt-20" >
            <tr>
                <td width="" valign="top">
                    <span class="capitalize">From,</span><br>
                    <div class="h-5">
                    </div>
                    <b class="capitalize"><?php
                        if (isset($data[0]->supplier_name)) {
                            echo strtolower($data[0]->supplier_name);
                        }
                        ?></b>
                    <?php
                    if (isset($data[0]->supplier_gstin_number)) {
                        if ($data[0]->supplier_gstin_number != "") {
                            echo "<br/>GSTIN : ";
                            echo $data[0]->supplier_gstin_number;
                        }
                    }
                    ?>
                    <?php
                    if (isset($data[0]->supplier_address) && $data[0]->supplier_address != "") {
                        echo "<br/>";
                        echo str_replace(array(
                            "\r\n",
                            "\\r\\n",
                            "\n",
                            "\\n"), "<br>", $data[0]->supplier_address);
                    } else {
                        echo "<br/>";
                        echo str_replace(array(
                            "\r\n",
                            "\\r\\n",
                            "\n",
                            "\\n"), "<br>", $data[0]->shipping_address);
                    }
                    ?>
                    <?php
                    if (isset($data[0]->supplier_mobile)) {
                        if ($data[0]->supplier_mobile != "") {
                            echo '<br>Mobile No : ' . $data[0]->supplier_mobile;
                        }
                    }
                    ?>
                    <?php
                    if (isset($data[0]->supplier_email)) {
                        if ($data[0]->supplier_email != "") {
                            echo '<br>E-mail : ' . $data[0]->supplier_email;
                        }
                    }
                    ?>
                    <?php
                    if (isset($data[0]->place_of_supply)) {
                        if ($data[0]->place_of_supply != "" || $data[0]->place_of_supply != null) {
                            echo '<br>Place of Supply : ' . $data[0]->place_of_supply;
                        }
                    }
                    ?>
                    <br><div class="h-5">
                    </div><br>               
                    <span class="capitalize">To,</span><br>
                    <div class="h-5">
                    </div>
                    
                    <b class="capitalize"><?php
                        if (isset($branch[0]->branch_name)) {
                            echo strtolower($branch[0]->branch_name);
                        }
                        ?></b>
                    <br>
                    <?php
                    if (isset($branch[0]->branch_address)) {
                        echo str_replace(array(
                            "\r\n",
                            "\\r\\n",
                            "\n",
                            "\\n"), "<br>", $branch[0]->branch_address);
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_mobile)) {
                        if ($branch[0]->branch_mobile != "" || $branch[0]->branch_mobile != null) {
                            echo '<br>Mobile No : ' . $branch[0]->branch_mobile;
                        }
                        ?>
                        <?php
                        if (isset($branch[0]->branch_land_number)) {
                            if ($branch[0]->branch_land_number != "") {
                                echo ' | Landline No : ' . $branch[0]->branch_land_number;
                            }
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_email_address)) {
                        if ($branch[0]->branch_email_address != "") {
                            echo '<br>E-mail : ' . $branch[0]->branch_email_address;
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_gstin_number)) {
                        if ($branch[0]->branch_gstin_number != "" || $branch[0]->branch_gstin_number != null) {
                            echo "<br>GSTIN : " . $branch[0]->branch_gstin_number;
                        }
                        ?>
                        <?php
                    }
                    ?>
                    <?php
                    if (isset($branch[0]->branch_pan_number) && $branch[0]->branch_pan_number != "") {
                        if ($branch[0]->branch_pan_number != "" || $branch[0]->branch_pan_number != null) {
                            echo "<br />PAN : " . $branch[0]->branch_pan_number;
                        }
                        ?>
                        <?php
                    }
                    ?>
                    <?php
                    if (isset($branch[0]->branch_import_export_code_number)) {
                        if ($branch[0]->branch_import_export_code_number != "" || $branch[0]->branch_import_export_code_number != null) {
                            echo " | IEC : " . $branch[0]->branch_import_export_code_number;
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_lut_number)) {
                        if ($branch[0]->branch_lut_number != "" || $branch[0]->branch_lut_number != null) {
                            echo "<br />LUT : " . $branch[0]->branch_lut_number;
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_cin_number)) {
                        if ($branch[0]->branch_cin_number != "" || $branch[0]->branch_cin_number != null) {
                            echo " | CIN : " . $branch[0]->branch_cin_number;
                        }
                    }
                    /*if (isset($data[0]->shipping_address)) {
                        if ($data[0]->shipping_address != "" || $data[0]->shipping_address != null) {
                            echo '<br><br><b>Billing Address</b><br>' . str_replace(array(
                                "\r\n",
                                "\\r\\n",
                                "\n",
                                "\\n"), "<br>", $data[0]->shipping_address);
                        }
                    }*/
                    /*if (isset($data[0]->shipping_gstin)) {
                        if ($data[0]->shipping_gstin != "" || $data[0]->shipping_gstin != null) {
                            echo '<br>Shipping GSTIN : ' . $data[0]->shipping_gstin;
                        }
                    }*/
                    ?>
                </td>
                <td width="30%" valign="top" class="bg-table">
                    <span class="capitalize">Invoice Details,</span>
                    <div class="h-5">
                    </div>    
                    Invoice Date:
                    <span  class="bold">
                    <?php
                    $date = $data[0]->purchase_date;
                    $date_for = date('d-m-Y', strtotime($date));
                    ?>
                    <?php
                    if (isset($data[0]->purchase_date)) {
                        echo $date_for;
                    }
                    ?></span></span>
                    <br>
                    Invoice Number:<span  class="bold"><?php
                    if (isset($data[0]->purchase_invoice_number)) {
                        echo $data[0]->purchase_invoice_number;
                    }
                    ?></span>                 
                    <?php
                    if (isset($data[0]->place_of_supply)) {
                        if ($data[0]->place_of_supply != "" || $data[0]->place_of_supply != null) {
                            echo '<br>Place of Supply : ' . $data[0]->place_of_supply;
                        }
                    }
                    if (isset($data[0]->shipping_address)) {
                        if ($data[0]->shipping_address != "" || $data[0]->shipping_address != null) {
                            echo '<br><br><b>Billing Address</b><br>' . str_replace(array(
                                "\r\n",
                                "\\r\\n",
                                "\n",
                                "\\n"), "<br>", $data[0]->shipping_address);
                        }
                    }
                    if (isset($data[0]->shipping_gstin)) {
                        if ($data[0]->shipping_gstin != "" || $data[0]->shipping_gstin != null) {
                            echo '<br>Billing GSTIN : ' . $data[0]->shipping_gstin;
                        }
                    }
                    ?>
                    <br>
                    Billing Country : 
                    <span><?php
                        if (isset($data[0]->billing_country)) {
                            echo ucfirst($data[0]->billing_country);
                        }
                        ?>
                    </span>
                    <br>
                    Nature of Supply : 
                    <span><?php
                        if (isset($nature_of_supply)) {
                            echo $nature_of_supply;
                        }
                        ?>
                    </span>
                    <br>
                    GST Payable on Reverse Charge:
                    <span><?php
                        if (isset($data[0]->purchase_gst_payable)) {
                            echo ucfirst($data[0]->purchase_gst_payable);
                        }
                        ?>
                    </span>
                    <span><?php
                    if (isset($data[0]->contact_person)) {
                        if ($data[0]->contact_person != "") {
                            echo '<br>Contact Person : ' . $data[0]->contact_person;
                        }
                    }
                    ?>
                    </span> 
                    <span><?php
                    if (isset($data[0]->department)) {
                        if ($data[0]->department != "") {
                            echo '<br>Department : ' . $data[0]->department;
                        }
                    }
                    ?>
                    </span> 
                </td>
                <!-- <?php
                $is_transport = false;
                $is_shipment = false;
                $order_det = $data[0];
                if ($order_det->transporter_name != '' || $order_det->transporter_gst_number != '' || $order_det->lr_no != '' || $order_det->vehicle_no != '')
                    $is_transport = true;
                if ($order_det->mode_of_shipment != '' || $order_det->ship_by != '' || $order_det->net_weight != '' || $order_det->gross_weight != '' || $order_det->origin != '' || $order_det->destination != '' || $order_det->shipping_type != '' || $order_det->shipping_type_place != '' || $order_det->lead_time != '' || $order_det->warranty != '' || $order_det->payment_mode != '')
                    $is_shipment = true;
                if ($is_shipment == true || $is_transport == true) {
                    ?>             
                    <td width="33%" valign="top" align="right" class="bg-table">
                        <?php if ($is_shipment) { ?>
                            <span class="capitalize">Shipping Details,</span>
                            <br />
                            <div class="h-5"></div>
                            <?php
                            if ($order_det->mode_of_shipment != '') {
                                echo '<span>Mode of Shipment: ' . $order_det->mode_of_shipment . ' </span><br>';
                            }
                            if ($order_det->ship_by != '') {
                                echo '<span>Ship By : ' . $order_det->ship_by . ' </span>';
                            }
                            if ($order_det->ship_by != '' && $order_det->net_weight != '')
                                echo " | ";
                            if ($order_det->net_weight != '') {
                                echo '<span>Net Weight : ' . $order_det->net_weight . ' </span>';
                            }
                            if ($order_det->ship_by != '' || $order_det->net_weight != '')
                                echo "<br>";
                            if ($order_det->gross_weight != '') {
                                echo '<span>Gross Weight : ' . $order_det->gross_weight . ' </span>';
                            }
                            if ($order_det->gross_weight != '' && $order_det->origin != '')
                                echo " | ";
                            if ($order_det->origin != '') {
                                echo '<span>Origin : ' . $order_det->origin . ' </span>';
                            }
                            if ($order_det->gross_weight != '' || $order_det->origin != '')
                                echo "<br>";
                            if ($order_det->mode_of_shipment != '') {
                                ?>                               
                                <?php echo '<span>Destination : ' . $order_det->mode_of_shipment . '</span><br>';
                                ?>
                                <?php
                            }
                            if ($order_det->shipping_type_place != '') {
                                echo '<span>Shipping Type Place : ' . $order_det->shipping_type_place . '</span><br>';
                            }
                            if ($order_det->lead_time != '') {
                                echo '<span>Lead Time : ' . $order_det->lead_time . ' </span>';
                            }
                            if ($order_det->lead_time != '' && $order_det->warranty != '')
                                echo " | ";
                            if ($order_det->warranty != '') {
                                echo '<span>Warranty : ' . $order_det->warranty . ' </span>';
                            }
                            if ($order_det->lead_time != '' || $order_det->warranty != '')
                                echo "<br>";
                            if ($order_det->payment_mode != '')
                                echo '<span>Payment Mode : ' . $order_det->payment_mode . '</span><br>';
                        }
                        if ($is_transport) {
                            ?>
                            <div class="h-5"></div>
                            <span class="capitalize">Transporter Details,</span>
                            <br />
                            <div class="h-5"></div> 
                            <?php
                            if ($order_det->transporter_name != '') {
                                echo '<span>Name: ' . $order_det->transporter_name . '</span><br>';
                            }
                            if ($order_det->transporter_gst_number != '') {
                                echo '<span>GST Number: ' . $order_det->transporter_gst_number . '</span><br>';
                            }
                            if ($order_det->lr_no != '') {
                                echo '<span>LR Number: ' . $order_det->lr_no . '</span><br>';
                            }
                            if ($order_det->vehicle_no != '') {
                                echo '<span>Vehicle Number: ' . $order_det->vehicle_no . '</span><br>';
                            }
                        }
                        ?> -->
                    </td>
                <?php } ?> 
<!-- <td width="30%" valign="top" align="right" class="bg-table">
<span class="capitalize">Shipping Details,</span>
<br />
<div class="h-5"></div>
                <?php echo '<span>Mode of Shipment: Goods </span><br>' ?>
                <?php echo '<span>Ship By : HArish </span> | <span>Net Weight : 780002 </span><br>'
                ?>                                
                <?php echo '<span>Gross Weight : 7500 </span> | <span>Origin : 45 </span><br>'
                ?>                               
                <?php echo '<span>Destination : Bengaluru</span><br>'
                ?>
                <?php echo '<span>Shipping Type Place : Bengaluru</span><br>'
                ?>
                <?php echo '<span>Lead Time : 57hr</span> | <span>Warranty : 2years </span><br>'
                ?>                               
                <?php echo '<span>Payment Mode : Cash</span><br>'
                ?>
<div class="h-5"></div>
<span class="capitalize">Transporter Details,</span>
<br />
<div class="h-5"></div>                    
                <?php echo '<span>Name: Vijay Kumar K</span><br>'
                ?>
                <?php echo '<span>GST Number: 295466575082</span><br>'
                ?>
                <?php echo '<span>LR Number: 8855</span><br>'
                ?>
                <?php echo '<span>Vehicle Number: JA-45 FU-4521</span><br>'
                ?>
                <?php echo '<span>Other Charges: 250.00</span>'
                ?></td> -->
            </tr>
        </table>        
        <table class="item-table table mt-20">
            <thead>
                <tr>
                    <th width="3%">#</th>
                    <th width="">Items</th>
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
                $grand_total = $tot_cgst = $tot_sgst = $tot_igst = 0;
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
                                if (isset($value->purchase_item_description) && $value->purchase_item_description != "") {
                                    echo "<br/>";
                                    echo $value->purchase_item_description;
                                }
                                ?><br><?php echo $value->product_batch; ?>
                            </td>
                            <?php
                        } else {
                            ?>
                            <td style="text-align: left;"><?php echo strtoupper(strtolower($value->service_name)); ?><br>HSN/SAC: <?php echo $value->service_hsn_sac_code; ?>
                                <?php
                                if (isset($value->purchase_item_description) && $value->purchase_item_description != "") {
                                    echo "<br/>";
                                    echo $value->purchase_item_description;
                                }
                                ?>
                            </td>
                        <?php } ?>
                        <td ><?php
                            echo $value->purchase_item_quantity;
                            if ( $value->product_unit != '' ) {
                                $unit = explode("-", $value->product_unit);
                                if($unit[0] != '') echo " <br>(" . $unit[0].')';
                            }
                            ?></td>
                        <td style="text-align: right;"><?php echo precise_amount($value->purchase_item_unit_price); ?></td>
                        <td style="text-align: right;"><?php echo precise_amount($value->purchase_item_sub_total); ?></td>
                        <?php
                        if ($discount_exist > 0) {
                            ?>
                            <td style="text-align: right;"><?php echo precise_amount($value->purchase_item_discount_amount); ?></td>
                        <?php } ?>
                        <?php
                        if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                            ?>
                            <td style="text-align: right;"><?php echo precise_amount($value->purchase_item_taxable_value); ?></td>
                        <?php } ?>
                        <?php
                        if ($tds_exist > 0) {
                            ?>
                            <td style="text-align: right;">
                                <?php
                                if ($value->purchase_item_tax_amount < 1 || $value->item_type == 'service') {
                                    echo '-';
                                } else {
                                    ?>
                                    <?php echo precise_amount($value->purchase_item_tds_amount); ?><br>(<?php echo round($value->purchase_item_tds_percentage, 2); ?>%)
                                <?php } ?>
                            </td>
                        <?php }
                        ?>
                        <?php
                        if ($tax_exist > 0) {
                            ?>
                            <td style="text-align: right;">
                                <?php
                                if ($value->purchase_item_tax_amount < 1) {
                                    echo '-';
                                } else {
                                    ?><?php echo precise_amount($value->purchase_item_tax_amount); ?><br>(<?php echo round($value->purchase_item_tax_percentage, 2); ?>%)
                                <?php } ?>
                            </td>
                            <?php
                        } elseif ($igst_exist > 0) {
                            ?>                            
                            <td style="text-align: right;">
                                <?php
                                if ($value->purchase_item_igst_amount < 1) {
                                    echo '-';
                                } else {
                                    ?><?php echo precise_amount($value->purchase_item_igst_amount); ?><br>(<?php echo round($value->purchase_item_igst_percentage, 2); ?>%)
                                <?php } ?>
                            </td>
                            <?php
                        } elseif ($cgst_exist > 0 || $sgst_exist > 0) {
                            ?>
                            <td style="text-align: right;">
                                <?php
                                if ($value->purchase_item_cgst_amount < 1) {
                                    echo '-';
                                } else {
                                    ?><?php echo precise_amount($value->purchase_item_cgst_amount); ?><br>(<?php echo round($value->purchase_item_cgst_percentage, 2); ?>%)
                                <?php } ?>
                            </td>                            
                            <td style="text-align: right;">
                                <?php
                                if ($value->purchase_item_sgst_amount < 1) {
                                    echo '-';
                                } else {
                                    ?>
                                    <?php echo precise_amount($value->purchase_item_sgst_amount); ?><br>(<?php echo round($value->purchase_item_sgst_percentage, 2); ?>%)
                                <?php } ?>
                            </td>
                        <?php }
                        ?>
                        <?php
                        if ($cess_exist > 0) {
                            ?>
                            <td style="text-align: right;">
                                <?php
                                if ($value->purchase_item_tax_cess_amount < 1) {
                                    echo '-';
                                } else {
                                    ?>
                                    <?php echo precise_amount($value->purchase_item_tax_cess_amount); ?><br>(<?php echo round($value->purchase_item_tax_cess_percentage, 2); ?>%)
                                <?php } ?>
                            </td>
                        <?php }
                        ?>
                        <td style="text-align: right;"><?php echo precise_amount($value->purchase_item_grand_total); ?></td>
                    </tr>
                    <?php
                    $i++;
                    $quantity = bcadd($quantity, $value->purchase_item_quantity, 2);
                    $price = bcadd($price, $value->purchase_item_unit_price, 2);
                    $grand_total = bcadd($grand_total, $value->purchase_item_grand_total, 2);
                    if ($igst_exist > 0) $tot_igst += $value->purchase_item_igst_amount;
                    if ($sgst_exist > 0) $tot_sgst += $value->purchase_item_sgst_amount;
                    if ($cgst_exist > 0) $tot_cgst += $value->purchase_item_cgst_amount;
                }
                ?>
                <tr>
                    <th colspan="2"></th>
                    <th  ><!-- <?= round($quantity) ?> --></th>
                    <th style="text-align: right;"><?= precise_amount($price) ?></th>
                    <th style="text-align: right;"><?= precise_amount($data[0]->purchase_sub_total) ?></th>
                    <?php
                    if ($discount_exist > 0) {
                        ?>
                        <th style="text-align: right;"><?= precise_amount($data[0]->purchase_discount_amount) ?></th>
                    <?php } ?>
                    <?php
                    if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                        ?>
                        <th style="text-align: right;"><?= precise_amount($data[0]->purchase_taxable_value) ?></th>
                    <?php } ?>
                    <?php
                    if ($tds_exist > 0) {
                        ?>
                        <th style="text-align: right;"><?= precise_amount($data[0]->purchase_tcs_amount) ?></th>
                    <?php }
                    ?>
                    <?php
                    if ($tax_exist > 0) {
                        ?>
                        <th style="text-align: right;"><?= precise_amount($data[0]->purchase_tax_amount) ?></th>
                        <?php
                    } elseif ($igst_exist > 0) {
                        ?>
                        <th style="text-align: right;"><?= precise_amount($tot_igst) ?></th>
                        <?php
                    } elseif ($cgst_exist > 0 || $sgst_exist > 0) {
                        ?>
                        <th style="text-align: right;"><?= precise_amount($tot_cgst) ?></th>
                        <th style="text-align: right;"><?= precise_amount($tot_sgst) ?></th>
                    <?php }
                    ?>
                    <?php
                    if ($cess_exist > 0) {
                        ?>
                        <th style="text-align: right;"><?php echo precise_amount($data[0]->purchase_tax_cess_amount); ?></th>
                    <?php }
                    ?>
                    <th style="text-align: right;"><?= precise_amount($grand_total, 2); ?></th>
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
                                <td><?php echo precise_amount(($data[0]->total_freight_charge * $convert_rate)); ?> /-</td>
                                <?php $data[0]->freight_charge_tax_amount = ($data[0]->freight_charge_tax_amount * $convert_rate); ?>
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
                                <td><?php echo precise_amount(($data[0]->total_insurance_charge * $convert_rate)); ?> /-</td>
                                <?php $data[0]->insurance_charge_tax_amount = ($data[0]->insurance_charge_tax_amount * $convert_rate); ?>
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
                                <td><?php echo precise_amount(($data[0]->total_packing_charge * $convert_rate)); ?> /-</td>
                                <?php $data[0]->packing_charge_tax_amount = ($data[0]->packing_charge_tax_amount * $convert_rate); ?>
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
                                <td><?php echo precise_amount(($data[0]->total_incidental_charge * $convert_rate)); ?> /-</td>
                                <?php $data[0]->incidental_charge_tax_amount = ($data[0]->incidental_charge_tax_amount * $convert_rate); ?>
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
                                <td><?php echo precise_amount(($data[0]->total_inclusion_other_charge * $convert_rate)); ?> /-</td>
                                <?php $data[0]->inclusion_other_charge_tax_amount = ($data[0]->inclusion_other_charge_tax_amount * $convert_rate); ?>
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
                                <td><?php echo precise_amount(($data[0]->total_exclusion_other_charge * $convert_rate)); ?> /-</td>
                                <?php $data[0]->exclusion_other_charge_tax_amount = ($data[0]->exclusion_other_charge_tax_amount * $convert_rate); ?>
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
                                <th><?php echo precise_amount(($data[0]->total_freight_charge * $convert_rate)); ?> /-</th>
                                <?php }
                                if ($data[0]->total_insurance_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_insurance_charge * $convert_rate)); ?> /-</th>
                                <?php }
                                if ($data[0]->total_packing_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_packing_charge * $convert_rate)); ?> /-</th>
                                <?php }
                                if ($data[0]->total_incidental_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_incidental_charge * $convert_rate)); ?> /-</th>
                                <?php }
                                if ($data[0]->total_inclusion_other_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_inclusion_other_charge * $convert_rate)); ?> /-</th>
                                <?php }
                                if ($data[0]->total_exclusion_other_charge > 0){ ?>
                                <th><?php echo precise_amount(($data[0]->total_exclusion_other_charge * $convert_rate)); ?> /-</th>
                                <?php } ?>
                            </tr>
                        </tbody>
                        <?php  }  ?>
                    </table> -->   
                </td>
                <td style="width:30%">
                    <table class="total_sum">
                        <tr>
                            <td align="right"><b>Total Value </b></td>
                            <td align="right"><?= $this->numbertowords->formatInr($data[0]->purchase_sub_total); ?></td>
                        </tr>
                        <?php if ($discount_exist > 0) { ?>
                            <tr>
                                <td align="right">Discount (-)</td>
                                <td align="right"><?= $this->numbertowords->formatInr($data[0]->purchase_discount_amount); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($tds_exist > 0) { ?>
                           <!--  <?php if ($data[0]->purchase_tds_amount > 0) { ?>  
                                <tr>
                                    <td>TDS (-)</td>
                                    <td align="right"> <?= $this->numbertowords->formatInr($data[0]->purchase_tds_amount); ?></td>
                                </tr>
                            <?php } ?> -->
                            <?php if ($data[0]->purchase_tcs_amount > 0) { ?>                           
                                <tr>
                                    <td align="right">TCS</td>
                                    <td align="right"><?= $this->numbertowords->formatInr($data[0]->purchase_tcs_amount); ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($igst_exist > 0) { ?>
                            <tr>
                                <td align="right">IGST</td>
                                <td align="right"> <?= $this->numbertowords->formatInr($data[0]->purchase_igst_amount); ?></td>
                            </tr>
                        <?php } elseif ($cgst_exist > 0 || $sgst_exist > 0) { ?>
                            <tr>
                                <td align="right">CGST</td>
                                <td align="right"><?= $this->numbertowords->formatInr($data[0]->purchase_cgst_amount); ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="right"><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></td>
                                <td align="right"> <?= $this->numbertowords->formatInr($data[0]->purchase_sgst_amount); ?></td>
                            </tr>
                        <?php } elseif ($tax_exist > 0) { ?>
                            <tr>
                                <td align="right">TAX</td>
                                <td align="right"><?= $this->numbertowords->formatInr($data[0]->purchase_tax_amount); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($cess_exist > 0) { ?>
                            <tr>
                                <td align="right">CESS</td>
                                <td align="right"><?= $this->numbertowords->formatInr($data[0]->purchase_tax_cess_amount); ?></td>
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
                                <td align="right">Other Exclusive Charge(-) </td>
                                <td align="right"><?= $this->numbertowords->formatInr($data[0]->total_exclusion_other_charge); ?></td>
                            </tr>
                        <?php } ?>                               
                        <?php if ($data[0]->round_off_amount > 0) { ?>
                            <tr>
                                <td>Round Off</td>
                                <td align="right"><?= $this->numbertowords->formatInr($data[0]->round_off_amount); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($data[0]->round_off_amount < 0) { ?>
                            <tr>
                                <td>Round Off (-)</td>
                                <td align="right"><?= $this->numbertowords->formatInr($data[0]->round_off_amount * -1); ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td align="right"><b>Grand Total(<img class="img" src="<?php echo FCPATH . '/assets/images/currency/'.$currency_symbol_pdf; ?>" style="height: 9px"  />)</b></td>
                            <td align="right"> <?= $this->numbertowords->formatInr($data[0]->purchase_grand_total); ?>/-</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>   
        <table class="table mt-50">
            <tr>
                <td class="amount_text">
                    Amount (in Words): <b><?php echo $currency_text . " " . $this->numbertowords->convert_number(precise_amount($data[0]->purchase_grand_total),$data[0]->unit,$data[0]->decimal_unit) . " Only"; ?>
                </td>
            </tr>
        </table>             
        <?php
        if (isset($data[0]->purchase_type_of_supply) && $data[0]->purchase_type_of_supply == "import") {
            ?>        
            <div id="notices mt-50">
                <div>
                    NOTICE:
                </div>
                <div class="notice">
                    <?php
                    if (isset($data[0]->purchase_type_of_supply) && $data[0]->purchase_type_of_supply == "import") {
                        ?>
                        <p>SUPPLY MEANT FOR EXPORT ON PAYMENT OF INTEGRATED TAX</p>
                        <?php
                    } else if (isset($data[0]->purchase_type_of_supply) && $data[0]->purchase_type_of_supply == "export_without_payment") {
                        ?>
                        <p>SUPPLY MEANT FOR EXPORT UNDER BOND OR LETTER OF UNDERTAKING WITHOUT PAYMENT OF INTEGRATED TAX</p>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php
        $notes_sub_module = 0;
        /*$this->load->view('note_template/display_note');*/
        ?>       
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
        if (isset($access_common_settings[0]->invoice_footer) && $access_common_settings[0]->invoice_footer != "") {
            ?>
            <div class="footer"><?php echo $access_common_settings[0]->invoice_footer; ?></div>            
        <?php  }  ?> 
    </body>
</html>