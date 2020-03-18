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
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>
            Purchase Return
        </title>
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
        <span style="float: right; margin-top: -25px"><h3><u><?= (@$title ? $title : 'Purchase Return'); ?></u></h3></span>        
        <table class="table header_table mt-20" >
            <tr>
                <td width="" valign="top">
                    <span class="capitalize">From,</span><br>
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
                    ?>
                    <br><div class="h-5">
                    </div><br>               
                    <span class="capitalize">To,</span><br>
                    <div class="h-5">
                    </div>                   
                    <b style="text-transform: capitalize;"><?php
                        if (isset($data[0]->supplier_name)) {
                            echo $data[0]->supplier_name;
                        }
                        ?></b>
                    <?php
                    if (isset($data[0]->supplier_gstin_number)) {
                        if ($data[0]->supplier_gstin_number != "") {
                            echo "<br/>GSTIN:";
                            echo $data[0]->supplier_gstin_number;
                        }
                    }
                    ?><br>
                        <?php
                        if (isset($data[0]->supplier_address)) {
                            echo str_replace(array(
                                "\r\n",
                                "\\r\\n",
                                "\n",
                                "\\n"), "<br>", $data[0]->supplier_address);
                        }
                        ?><br>
                    <?php
                    if (isset($data[0]->supplier_city_name)) {
                        echo $data[0]->supplier_city_name;
                    }
                    ?> <?php
                    if (isset($data[0]->supplier_postal_code)) {
                        if ($data[0]->supplier_postal_code != "" || $data[0]->supplier_postal_code != null) {
                            echo "-" . $data[0]->supplier_postal_code;
                        }
                    }
                    ?><br>
                    <?php
                    if (isset($data[0]->supplier_state_name)) {
                        echo $data[0]->supplier_state_name;
                    }
                    ?>,
                    <?php
                    if (isset($data[0]->supplier_country_name)) {
                        echo $data[0]->supplier_country_name;
                    }
                    ?>

                    <?php
                    if (isset($data[0]->supplier_mobile)) {
                        if ($data[0]->supplier_mobile != "" || $data[0]->supplier_mobile != null) {
                            echo "<br>Mobile No: " . $data[0]->supplier_mobile;
                        }
                    }
                    ?>
                    <?php
                    if (isset($data[0]->supplier_email)) {
                        if ($data[0]->supplier_email != "" || $data[0]->supplier_email != null) {
                            echo '<br>E-mail : ' . $data[0]->supplier_email;
                        }
                    }
                    ?>

                    <?php
                    if (isset($data[0]->supplier_state_code) && $data[0]->supplier_state_code != 0) {
                        echo '<br>State Code : ' . $data[0]->supplier_state_code;
                    }

                    if (isset($data[0]->place_of_supply)) {
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
                <td width="35%" valign="top" class="bg-table">
                    <span class="capitalize">Invoice Details,</span>
                    <div class="h-5">
                    </div>    
                    Purchase Return Date:
                    <?php
                    $date = $data[0]->purchase_return_date;
                    $date_for = date('d-m-Y', strtotime($date));
                    ?>
                   <span  class="bold"><?php
                        if (isset($data[0]->purchase_return_date)) {
                            echo $date_for;
                        }
                        ?>
                    </span>                    
                    <br>
                    Purchase Return Number:<span  class="bold"><?php
                    if (isset($data[0]->purchase_return_invoice_number)) {
                        echo $data[0]->purchase_return_invoice_number;
                    }
                    ?> </span>                   
                    <br>
                    Billing Country:
                    <span><?php
                        if (isset($data_country[0]->country_name)) {
                            echo ucfirst($data_country[0]->country_name);
                        }
                        ?>
                    </span>
                    <br>                
                    Place of Supply :
                    <span><?php
                        if (isset($data_state[0]->state_name)) {
                            echo ucfirst($data_state[0]->state_name);
                        }
                        ?>
                    </span>
                    <br>
                    Purchase Number:
                    <span><?php
                        if (isset($data[0]->purchase_invoice_number)) {
                            echo $data[0]->purchase_invoice_number;
                        }
                        ?>
                    </span>
                    <br>
                    GST Payable on Reverse Charge:
                    <span><?php
                        if (isset($data[0]->purchase_return_gst_payable)) {
                            echo ucfirst($data[0]->purchase_return_gst_payable);
                        }
                        ?>
                    </span>
                </td>
                <?php
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
                        ?>
                    </td>
                <?php } ?> 
            </tr>
        </table>  
        <table class="table item-table mt-20">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Taxable <br/>Value</th>
                    <?php
                    if ($igst_tax < 1 && $cgst_tax > 1) {
                        ?>
                        <th>CGST</th>
                        <th><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></th>
                        <?php
                    } elseif ($igst_tax > 1) {
                        ?>
                        <th>IGST</th>
                    <?php } ?>
                    <?php
                        if ($cess_tax > 1){
                        ?>
                         <th>CESS</th>
                        <?php 
                        }
                        ?>
                    <th>Total</th>
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
                $grand_total = 0;
                $cess = 0;
                foreach ($items as $value) {
                    ?>
                    <tr>
                       <!--  <td align="center"><?php echo $i; ?></td> -->
                        <!-- <td ><?php echo $value->product_name; ?><br>HSN:<?php echo $value->product_hsn_sac_code; ?></td> -->
                        <?php
                        if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {
                            ?>
                            <td align="left"><?php echo $value->product_name; ?><br>HSN/SAC: <?php echo $value->product_hsn_sac_code; ?><br><?php echo $value->purchase_return_item_description; ?></td>
                            <?php
                        } else {
                            ?>
                            <td align="left"><?php echo $value->service_name; ?><br>HSN/SAC: <?php echo $value->service_hsn_sac_code; ?><br><?php echo $value->purchase_return_item_description; ?></td>
                        <?php } ?>
                        <td align="center"><?php
                            echo round($value->purchase_return_item_quantity, 2);
                            if ($value->product_unit != '') {
                                 $unit = explode("-", $value->product_unit);
                                if($unit[0] != '') echo " <br>(" . $unit[0].')';
                            }
                            ?></td>
                        <td align="right"><?php echo precise_amount($value->purchase_return_item_unit_price); ?></td>
                        <td align="right"><?php echo precise_amount($value->purchase_return_item_taxable_value); ?></td>

                        <?php
                        if ($sgst_tax > 1 && $cgst_tax > 1) {
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
                        } else if ($igst_tax > 1) {
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
                        <?php }
                        ?>
                        <?php
                    if ($cess_tax > 1){
                    ?>
                           
                    <td align="right">
                        <?php if($value->purchase_return_item_cess_amount < 1){ echo '-'; }else{ ?>
                        <?php echo precise_amount($value->purchase_return_item_cess_amount); ?><br>(<?php echo round($value->purchase_return_item_cess_percentage,2); ?>%)
                        <?php } ?>
                    </td>

                    <?php }
                    ?>
                        <td align="right"><?php echo precise_amount($value->purchase_return_item_grand_total); ?></td>
                    </tr>
                    <?php
                    $i++;
                    $quantity = bcadd($quantity, $value->purchase_return_item_quantity, 2);
                    $price = bcadd($price, $value->purchase_return_item_unit_price, 2);
                    // $sub_total = bcadd($tot, $value->sub_total, 2);
                    // $discount = bcadd($discount, $value->discount_amt, 2);
                    // $taxable_value = bcadd($discount, $value->taxable_value, 2);
                    $igst = bcadd($igst, $value->purchase_return_item_igst_amount, 2);
                    $cgst = bcadd($cgst, $value->purchase_return_item_cgst_amount, 2);
                    $sgst = bcadd($sgst, $value->purchase_return_item_sgst_amount, 2);
                    $cess = bcadd($cess, $value->purchase_return_item_cess_amount, 2);

                    $grand_total = bcadd($grand_total, $value->purchase_return_item_grand_total, 2);
                }
                ?>
                <tr>
                    <?php
                    if ($dpcount > 0) {
                        ?>
                        <th colspan="1" align="right" height="30px;"><b>Total</b></th>

                        <?php
                    } else {
                        ?>
                        <th colspan="1" align="right">Total</th>
                        <?php
                    }
                    ?>

                    <th><!--<?php //echo round($quantity, 2); ?>--></th>
                    <th align="right"><?php echo precise_amount($price); ?></th>

                    <th align="right"><?php echo precise_amount($data[0]->purchase_return_sub_total); ?></th> 
                    <?php if ($cgst_tax > 1 && $sgst_tax > 1) {
                        ?>
                        <th align="right"><?php echo precise_amount($data[0]->purchase_return_cgst_amount); ?></th>

                        <th align="right" ><?php echo precise_amount($data[0]->purchase_return_sgst_amount); ?></th>
                        <?php
                    } else if ($igst_tax > 1 && $cgst_tax < 1) {
                        ?>

                        <th align="right"><?php echo precise_amount($data[0]->purchase_return_igst_amount); ?></th>
                    <?php }
                    ?>
                     <?php
                    if ($cess_tax > 1){
                    ?>
                           
                     <th align="right"><?php echo precise_amount($data[0]->purchase_return_cess_amount); ?></th>

                    <?php }
                    ?>
                    <th align="right"><?php echo precise_amount($grand_total) ?></th>

                </tr>
            </tbody>
        </table>
        
        <table class="mt-20">
            <tr>
                <td style="width:66%">
                    <table class="dashed_table">       
        
         <?php if ($data[0]->total_freight_charge > 0 || $data[0]->total_insurance_charge > 0 || $data[0]->total_packing_charge > 0 || $data[0]->total_incidental_charge > 0 || $data[0]->total_inclusion_other_charge > 0 || $data[0]->total_exclusion_other_charge > 0) { ?>
        
            <?php
            $charges_sub_module = 0;
            if (in_array($charges_sub_module_id, $access_sub_modules)) {

                $charges_sub_module = 1;
            }
            if ($charges_sub_module == 1) {
                ?>
                <thead>
                    <tr>
                        <th>Freight Charges</th>
                        <th>Insurance Charges</th>
                        <th>Pack & Forward Charges</th>
                        <th>Incidental Charges</th>
                        <th>Other Inclusive <br> Charge</th>
                        <th>Other Exclusive <br> Charge</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php
                            if ($data[0]->total_freight_charge > 0) {
                                echo precise_amount($data[0]->total_freight_charge);
                            } else {
                                echo '-';
                            }
                            ?></td>
                        <td><?php
                            if ($data[0]->total_insurance_charge > 0) {
                                echo precise_amount($data[0]->total_insurance_charge);
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td><?php
                            if ($data[0]->total_packing_charge > 0) {
                                echo precise_amount($data[0]->total_packing_charge);
                            } else {
                                echo '-';
                            }
                            ?></td>
                        <td><?php
                            if ($data[0]->total_incidental_charge > 0) {
                                echo precise_amount($data[0]->total_incidental_charge);
                            } else {
                                echo '-';
                            }
                            ?></td>
                        <td><?php
                            if ($data[0]->total_inclusion_other_charge > 0) {
                                echo precise_amount($data[0]->total_inclusion_other_charge);
                            } else {
                                echo '-';
                            }
                            ?></td>
                        <td><?php
                            if ($data[0]->total_exclusion_other_charge > 0) {
                                echo precise_amount($data[0]->total_exclusion_other_charge);
                            } else {
                                echo '-';
                            }
                            ?>

                        </td>
                    </tr>
                <?php } ?>
            </tbody>        
        <?php
        }
        ?> 
        </table>
        </td>
        <td style="width:31%">
                <table class="total_sum">
            <tr>
                <td align="right"><b>Total Value</b></td>
                <td align="right"><?php echo $this->numbertowords->formatInr($data[0]->purchase_return_sub_total);?></td>
            </tr>
            <?php
            if ($igst_tax > 0.00) {
                ?>
                <tr>
                    <td align="right">IGST</td>
                    <td align="right"><?php echo $this->numbertowords->formatInr($igst); ?></td>
                </tr>
                <?php
            }else if ($cgst_tax > 0.00 || $cgst_tax > 0.00) { 
                ?>
                <tr>
                    <td align="right">CGST</td>
                    <td align="right"><?php echo $this->numbertowords->formatInr($cgst); ?></td>
                </tr>
                <tr>
                    <td align="right"><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></td>
                    <td align="right"><?php echo $this->numbertowords->formatInr($sgst); ?></td>
                </tr>
            <?php }
            ?>
            <?php
                if ($cess_tax > 1){
                    ?>
                     <tr>
                    <td align="right">CESS</td>
                    <td align="right"><?php echo $this->numbertowords->formatInr($cess); ?></td>
                </tr>               
            <?php }
            ?>
            <tr>
                <td align="right"><b>Grand Total(<img class="img" src="<?php echo FCPATH . '/assets/images/currency/'.$data[0]->currency_symbol_pdf; ?>" style="height: 9px"/>)</b></td>
                <td align="right"><b><?php echo $this->numbertowords->formatInr($data[0]->purchase_return_grand_total); ?>/-</td>
            </tr>            
        </table>       
        </td>
        </tr>
        </table> 
        <div class="mt-50">
            <p class="amount_text">
                Amount (in Words): <b><?php echo $data[0]->currency_name . " " . $this->numbertowords->convert_number($data[0]->purchase_return_grand_total,$data[0]->unit,$data[0]->decimal_unit) . " Only"; ?></b><br/>
            </p>
        </div>  
        <?php
        $notes_sub_module = 0;
        if (in_array($notes_sub_module_id, $access_sub_modules)) {
            $notes_sub_module = 1;
        }
        if ($notes_sub_module == 1) {
            //$this->load->view('note_template/display_note');
        }
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
        if (isset($access_common_settings[0]->invoice_footer)) {
            ?>
             <div class="footer">
                <?php echo $access_common_settings[0]->invoice_footer; ?>
             </div>
            <?php
        }
        ?>
        
    </body>
</html>
<script>window.print();</script>
