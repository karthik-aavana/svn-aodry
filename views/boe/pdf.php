<?php
$GLOBALS['common_settings_amount_precision'] = $access_common_settings[0]->amount_precision;
if(!function_exists('precise_amount')){
function precise_amount($val) {
    $val = (float) $val;
    // $amt =  round($val,$GLOBALS['common_settings_amount_precision']);
    $dat = number_format($val, $GLOBALS['common_settings_amount_precision'], '.', '');
    return $dat;
}
}
$convert_rate = 1;
if(@$converted_rate) $convert_rate = $converted_rate;
?>
<!DOCTYPE html>
<html>
    <head>
        <title> <?= (@$title ? $title : 'BOE Invoice'); ?> </title>
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
        <span style="float: right; margin-top: -25px"><h3><u><?php echo 'BOE Invoice'; ?></u></h3>
           <br><p class="right"></p></span>
        <table class="table header_table mt-20" >
            <tr>
                <td valign="top">
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
                    }
                    if (isset($branch[0]->branch_gstin_number)) {
                        if ($branch[0]->branch_gstin_number != "" || $branch[0]->branch_gstin_number != null) {
                            echo "<br>GSTIN : " . $branch[0]->branch_gstin_number;
                        }
                    } 
                    if (isset($branch[0]->branch_pan_number) && $branch[0]->branch_pan_number != "") {
                        if ($branch[0]->branch_pan_number != "" || $branch[0]->branch_pan_number != null) {
                            echo "<br />PAN : " . $branch[0]->branch_pan_number;
                        }
                    } 
                    if (isset($branch[0]->branch_import_export_code_number)) {
                        if ($branch[0]->branch_import_export_code_number != "" || $branch[0]->branch_import_export_code_number != null) {
                            echo "| IEC : " . $branch[0]->branch_import_export_code_number;
                        }
                    }
                    if (isset($branch[0]->branch_lut_number)) {
                        if ($branch[0]->branch_lut_number != "" || $branch[0]->branch_lut_number != null) {
                            echo "<br />LUT : " . $branch[0]->branch_lut_number;
                        }
                    }
                    if (isset($branch[0]->branch_cin_number)) {
                        if ($branch[0]->branch_cin_number != "" || $branch[0]->branch_cin_number != null) {
                            echo " | CIN : " . $branch[0]->branch_cin_number;
                        }
                    }
                    ?>
                </td>
                <td width="33%" valign="top" class="bg-table">
                    <span class="capitalize">Invoice Details,</span>
                    <div class="h-5">
                    </div>
                    Invoice Date : 
                    <?php
                    $date = $data[0]->boe_date;
                    $date_for = date('d-m-Y', strtotime($date));
                    ?>                  
                    <span class="bold"><?php
                        if (isset($data[0]->boe_date)) {
                            echo $date_for;
                        }
                        ?></span>
                    <br>
                    Invoice Number:                  
                    <span class="bold"><?php
                        if (isset($data[0]->boe_number)) {
                            echo $data[0]->boe_number;
                        }
                        ?>
                    </span>
                    <?php
                    if (isset($data[0]->CIN)) {
                        if ($data[0]->CIN != "" || $data[0]->CIN != null) {
                            echo '<br>CIN : ' . $data[0]->CIN;
                        }
                        if ($data[0]->CIN_date != "" || $data[0]->CIN_date != null ) {
                            if($data[0]->CIN_date != '0000-00-00'){
                                echo '<br>CIN date : ' . date('d-m-Y', strtotime($data[0]->CIN_date));
                            }
                        }
                    } ?>
                </td>
            </tr>
        </table>
        <table class="item-table table mt-20">
            <thead>
                <tr>
                    <th height="30px">#</th>
                    <th>Items</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Subtotal</th>
                    <?php if ($bcd_exist > 0) { ?>
                        <th>BCD</th>
                    <?php } ?>
                    <?php if ($igst_exist > 0) { ?>
                        <th>IGST</th>
                    <?php } if ($cess_exist > 0) { ?>
                        <th style="text-align: center;">CESS</th>
                    <?php } ?>
                    <?php if ($other_duties > 0) { ?>
                        <th  style="text-align: center;">Other Duties</th>
                    <?php } ?>
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
                        <td><?php echo $i; ?></td>
                        <?php
                        if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {
                            ?>
                            <td style="text-align: left;">
                                <span class="capitalize"><?php echo strtoupper(strtolower($value->product_name)); ?></span>                               
                                <br>HSN/SAC: <?php echo $value->product_hsn_sac_code; ?>
                                <?php
                                if (isset($value->boe_item_description) && $value->boe_item_description != "") {
                                    echo "<br/>";
                                    echo $value->boe_item_description;
                                }
                                ?><br><?php echo $value->product_batch; ?>
                            </td>
                        <?php } else { ?>
                            <td style="text-align: left;"><?php echo strtoupper(strtolower($value->service_name)); ?><br>HSN/SAC: <?php echo $value->service_hsn_sac_code; ?>
                                <?php
                                if (isset($value->boe_item_description) && $value->boe_item_description != "") {
                                    echo "<br/>";
                                    echo $value->boe_item_description;
                                }
                                ?>
                            </td>
                        <?php } ?>
                        <td><?php
                            echo $value->boe_item_quantity;
                            if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {
                                $unit = explode("-", $value->product_unit);
                                echo " " . $unit[0];
                            }
                            ?></td>
                        <td style="text-align: right;"><?php echo precise_amount(($value->boe_item_unit_price * $convert_rate)); ?></td>
                        <td style="text-align: right;"><?php echo precise_amount(($value->boe_item_sub_total * $convert_rate)); ?></td>
                      	<?php if ($bcd_exist > 0) { ?>
                            <td style="text-align: right;">
                                 <?php if($value->boe_item_bcd_amount < 1){ echo '-'; }else{ ?>
                                <?=($value->boe_item_bcd_amount > 0 ?precise_amount($value->boe_item_bcd_amount * $convert_rate) : '-'); ?>
                                <?=($value->boe_item_bcd_percentage > 0 ? "<br>(".round(abs($value->boe_item_bcd_percentage),2).'%' : ''); ?>) <?php }?>
                            </td>
                        <?php } ?>
                        <?php if ($igst_exist > 0) { ?>
                            <td style="text-align: right;">
                                 <?php if($value->boe_item_igst_amount < 1){ echo '-'; }else{ ?>
                                <?=($value->boe_item_igst_amount > 0 ? precise_amount($value->boe_item_igst_amount * $convert_rate) : '-'); ?>
                                <?=($value->boe_item_igst_percentage > 0 ? "<br>(".round(abs($value->boe_item_igst_percentage),2).'%' : ''); ?>) <?php }?>
                            </td>
                        <?php } ?>
                        <?php if ($cess_exist > 0) { ?>
                            <td style="text-align: right;">
                                <?php if($value->boe_tax_cess_amount < 1){ echo '-'; }else{ ?><?= precise_amount(($value->boe_tax_cess_amount * $convert_rate)); ?><br>(<?= round($value->boe_item_tax_cess_percentage,2); ?>%)<?php } ?></td>
                        <?php } ?>
                        <?php if ($other_duties > 0) { ?>
                            <td style="text-align: right;">
                                <?php if($value->boe_item_tax_other_duties_amount < 1){ echo '-'; }else{ ?><?= precise_amount(($value->boe_item_tax_other_duties_amount * $convert_rate)); ?><br>(<?= round($value->boe_item_tax_other_duties_percentage,2); ?>%)<?php } ?></td>
                        <?php } ?>
                        <td style="text-align: right;"><?php echo precise_amount(($value->boe_item_grand_total * $convert_rate)); ?></td>
                    </tr>
                    <?php
                    $i++;
                    $quantity = bcadd($quantity, $value->boe_item_quantity);
                    $price = bcadd($price, $value->boe_item_unit_price, 2);
                    $grand_total = bcadd($grand_total, $value->boe_item_grand_total, 2);
                }
                ?>
                <tr>
                    <th colspan="2"></th>
                    <th  style="text-align: right;"><?= round($quantity,2) ?></th>
                    <th  style="text-align: right;"><?= precise_amount(($price * $convert_rate)) ?></th>
                    <th  style="text-align: right;"><?= precise_amount(($data[0]->boe_sub_total * $convert_rate)) ?></th>
                    <?php
                    if ($bcd_exist > 0) { ?>
                        <th style="text-align: right;"><?= precise_amount(($data[0]->boe_bcd_amount * $convert_rate)) ?></th>
                    <?php } 
                    if ($igst_exist > 0) { ?>
                        <th style="text-align: right;"><?= precise_amount(($data[0]->boe_tax_amount * $convert_rate)) ?></th>
                    <?php } ?>
                    <?php if ($cess_exist > 0) { ?>
                        <th style="text-align: right;"><?= precise_amount(($data[0]->boe_cess_amount * $convert_rate)) ?></th>
                    <?php } ?>
                    <?php if ($other_duties > 0) { ?>
                        <th style="text-align: right;"><?= precise_amount(($data[0]->boe_other_duties_amount * $convert_rate)) ?></th>
                    <?php } ?>
                    <th  style="text-align: right;"><?= precise_amount($data[0]->boe_grand_total); ?></th>
                </tr>
            </tbody>
        </table>
        <table class="mt-20">
            <tr>
                <td style="width:66%">
                    <table class="dashed_table">
        </table>
        </td>
        
        
       <td style="width:30%">
                <table class="total_sum">
            <tr>                 
                <td align="right"><b>Total Value</b></td>
                <td align="right"><?= $this->numbertowords->formatInr(($data[0]->boe_sub_total * $convert_rate)); ?></td>
            </tr>
            <?php if ($bcd_exist > 0) { ?>
	            <tr>
	                <td align="right">BCD</td>
	                <td align="right"><?= $this->numbertowords->formatInr(($data[0]->boe_bcd_amount * $convert_rate)); ?></td>
	            </tr>
            <?php } ?>
            <?php if ($igst_exist > 0) { ?>
	            <tr>
	                <td align="right">IGST</td>
	                <td align="right"><?= $this->numbertowords->formatInr(($data[0]->boe_tax_amount * $convert_rate)); ?></td>
	            </tr>
            <?php } ?>
            <?php if ($cess_exist > 0) { ?>
	            <tr>
	                <td align="right">CESS</td>
	                <td align="right"><?= $this->numbertowords->formatInr(($data[0]->boe_cess_amount * $convert_rate)); ?></td>
	            </tr>	
            <?php } ?>
            <?php if ($other_duties > 0) { ?>
                <tr>
                    <td align="right">Other Duties (+)</td>
                    <td align="right"><?= $this->numbertowords->formatInr(($data[0]->boe_other_duties_amount * $convert_rate)); ?></td>
                </tr>   
            <?php } ?>
            <tr>
                <td align="right"><b>Grand Total(<img class="img" src="<?php echo FCPATH . '/assets/images/currency/'.$currency_symbol_pdf; ?>" style="height: 9px"  />)</td>
                <td align="right"><b><?php echo $this->numbertowords->formatInr(($data[0]->boe_grand_total * $convert_rate)); ?>/-</td>
            </tr>
        </table>
        </td>
        </tr>
        </table>       
        
        <div class="mt-50">
            <p class="amount_text">
                Amount (in Words): <b><?php echo $currency_text . " " . $this->numbertowords->convert_number(precise_amount(($data[0]->boe_grand_total * $convert_rate)),$data[0]->unit,$data[0]->decimal_unit) . " Only"; ?> </b>
            </p>
        </div>  
        <?php if($data[0]->note1 != '' || $data[0]->note2 != '' ){?>   
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