<?php
$GLOBALS['common_settings_amount_precision'] = $access_common_settings[0]->amount_precision;
if (!function_exists('precise_amount')) {

    function precise_amount($val) {
        $val = (float) $val;
        // $amt =  round($val,$GLOBALS['common_settings_amount_precision']);
        $dat = number_format($val, $GLOBALS['common_settings_amount_precision'], '.', '');
        return $dat;
    }
}
function formatinr($input){
        $dec = "";
        $pos = strpos($input, ".");
        if ($pos === FALSE){
            //no decimals
           
        }else{
            //decimals
            $dec   = substr(round(substr($input, $pos), 2), 1);
            $input = substr($input, 0, $pos);
        }
        $num   = substr($input, -3);    // get the last 3 digits
        $input = substr($input, 0, -3); // omit the last 3 digits already stored in $num
        // loop the process - further get digits 2 by 2
        while (strlen($input) > 0)
        {
            $num   = substr($input, -2).",".$num;
            $input = substr($input, 0, -2);
        }
        if($dec == ""){
            $dec = '.00';
        }
        return $num.$dec;
    }
$convert_rate = 1;
if (@$converted_rate)
    $convert_rate = $converted_rate;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            Advance Voucher
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
        <span style="float: right; margin-top: -25px"><h3><u><?= (@$title ? $title : 'Advance Voucher'); ?></u></h3></span>        
        <table class="table header_table mt-20">
            <tr>
                <td width="45%" valign="top">
                    <b>From,</b><br>
                    <b style="text-transform: capitalize;"><?php
                        if (isset($branch[0]->branch_name)) {
                            echo $branch[0]->branch_name;
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
                        <br>
                        <?php
                    }
                    if (isset($state[0]->state_name)) {
                        echo $state[0]->state_name;
                    }
                    ?>, <?php
                    if (isset($country[0]->country_name)) {
                        echo $country[0]->country_name;
                        ?>.
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
                                echo " | Landline No: " . $branch[0]->branch_land_number;
                            }
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_email_address)) {
                        if ($branch[0]->branch_email_address != "" || $branch[0]->branch_email_address != null) {
                            echo ' <br>E-mail : ' . $branch[0]->branch_email_address;
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_gstin_number)) {
                        if ($branch[0]->branch_gstin_number != "") {
                            echo " | GSTIN : " . $branch[0]->branch_gstin_number;
                        }
                        ?>
                        <?php
                    }
                    if (isset($branch[0]->branch_pan_number)) {
                        if ($branch[0]->branch_pan_number != "") {
                            echo "<br />PAN : " . $branch[0]->branch_pan_number;
                        }
                        ?>

                        <?php
                    }
                    if (isset($branch[0]->branch_cin_number)) {
                        if ($branch[0]->branch_cin_number != "") {
                            echo " | CIN : " . $branch[0]->branch_cin_number;
                        }
                    }
                    ?>
                    <br><br>
                    <b>To,</b><br>
                    <b style="text-transform: capitalize;"><?php
                        if (isset($data[0]->customer_name)) {
                            echo $data[0]->customer_name;
                        }
                        ?></b>
                    <?php
                    if (isset($data[0]->customer_gstin_number)) {
                        if ($data[0]->customer_gstin_number != "") {
                            echo "<br/>GSTIN:";
                            echo $data[0]->customer_gstin_number;
                        }
                    }
                    ?><br>
                        <?php
                        if (isset($data[0]->customer_address)) {
                            if ($data[0]->customer_address != "" || $data[0]->customer_address != null) {
                                echo str_replace(array(
                                    "\r\n",
                                    "\\r\\n",
                                    "\n",
                                    "\\n"), "<br>", $data[0]->customer_address);
                            }
                        }
                        ?><br>
                    <?php
                    if (isset($data[0]->customer_city_name)) {
                        echo $data[0]->customer_city_name;
                    }
                    ?> <?php
                    if (isset($data[0]->customer_postal_code)) {
                        if ($data[0]->customer_postal_code != "") {
                            echo "-" . $data[0]->customer_postal_code;
                        }
                    }
                    ?><br>
                    <?php
                    if (isset($data[0]->customer_state_name)) {
                        echo $data[0]->customer_state_name;
                    }
                    ?>,
                    <?php
                    if (isset($data[0]->customer_country_name)) {
                        echo $data[0]->customer_country_name;
                    }
                    ?>

                    <?php
                    if (isset($data[0]->customer_mobile)) {
                        if ($data[0]->customer_mobile != "") {
                            echo "<br>Mobile No:" . $data[0]->customer_mobile;
                        }
                    }
                    ?>
                    <?php
                    if (isset($data[0]->customer_email)) {
                        if ($data[0]->customer_email != "") {
                            echo '<br>E-mail : ' . $data[0]->customer_email;
                        }
                    }
                    ?>
                    <?php
                    if (isset($data[0]->customer_state_code) && $data[0]->customer_state_code != 0) {
                        echo '<br>State Code : ' . $data[0]->customer_state_code;
                    }
                    if (isset($data[0]->place_of_supply)) {
                        if ($data[0]->place_of_supply != "") {
                            echo '<br>Place of Supply : ' . $data[0]->place_of_supply;
                        }
                    }
                    ?>
                </td>                
                <td valign="top" width="30%" class="bg-table">
                    Voucher Date:
                    <?php
                    $date = $data[0]->voucher_date;
                    $date_for = date('d-m-Y', strtotime($date));
                    ?>
                    <span  class="bold"><?php
                        if (isset($data[0]->voucher_date)) {
                            echo $date_for;
                        }
                        ?>
                    </span>
                    <br>
                    Voucher Number : <span  class="bold"> <?php
                        if (isset($data[0]->voucher_number)) {
                            echo $data[0]->voucher_number;
                        }
                        ?>
                        </span>
                </td>               
            </tr>
        </table>
        <br>
        <table class="item-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <?php
                    if ($igst_tax < 1 && $cgst_tax > 1) {
                        ?>
                        <th >CGST</th>
                        <th ><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></th>
                        <?php
                    } else if ($igst_tax > 1 && $cgst_tax < 1) {
                        ?>
                        <th >IGST</th>
                        <?php
                    } else if ($data[0]->customer_state_code == $branch[0]->branch_state_code) {
                        ?>
                        <th >CGST</th>
                        <th ><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></th>
                        <?php
                    } else if ($igst_tax > 1) {
                        ?>
                        <th  >IGST</th>
                        <?php
                    }
                    if ($cess_tax > 1) {
                        ?>
                        <th >CESS</th>
                    <?php } ?>

                    <th >Total</th>                    
                </tr>                
            </thead>
            <tbody>
                <?php
                $i = 1;
                $tot = 0;
                $igst = 0;
                $cgst = 0;
                $sgst = 0;
                $price = 0;
                $grand_total = 0;
                foreach ($items as $value) {
                    ?>
                    <tr>                       
                        <?php if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {
                            ?>
                            <td align="left"><?php echo strtoupper(strtolower($value->product_name)); ?><br>HSN/SAC: <?php echo $value->product_hsn_sac_code; ?><br><?php echo $value->product_batch; ?><br><?php echo $value->item_description; ?></td>
                            <?php
                        } elseif ($value->item_type == 'advance') {
                            ?>
                            <td align="left"><?php echo strtoupper(strtolower($value->product_name)); ?></td>
                            <?php
                        } else {
                            ?>
                            <td align="left"><?php echo strtoupper(strtolower($value->service_name)); ?><br>HSN/SAC: <?php echo $value->service_hsn_sac_code; ?><br><?php echo $value->item_description; ?></td>
                        <?php } ?>
                        <td align="center"><?php echo round($value->item_quantity);

                            if ($value->product_unit != '') {
                                $unit = explode("-", $value->product_unit);
                                if($unit[0] != '') echo " <br>(" . $unit[0].')';
                            }
                        ?></td>
                        <td align="right"><?php echo precise_amount($value->item_sub_total*$convert_rate,2); ?></td>>
                        <?php
                        if ($igst_tax < 1 && $cgst_tax > 1) {
                            ?>
                            <!--<td align="center"></td> -->
                            <td align="right"><?php if($value->item_cgst_amount < 1){ echo '-'; }else{ echo precise_amount($value->item_cgst_amount*$convert_rate,2); ?><br><?php echo "(".(float)($value->item_cgst_percentage)."%)"; } ?></td>
                           <!-- <td align="center"></td> -->
                            <td align="right"><?php if($value->item_sgst_amount < 1){ echo '-'; }else{
                                echo  precise_amount($value->item_sgst_amount*$convert_rate,2); ?><br><?php echo "(".(float)($value->item_sgst_percentage)."%)";
                            }
                             ?></td>

                            <?php
                        } else if ($igst_tax > 1 && $cgst_tax < 1) {
                            ?>
                            <!--<td align="center"></td> -->
                            <td align="right"><?php if($value->item_igst_amount < 1){ echo '-'; }else{ echo precise_amount($value->item_igst_amount*$convert_rate,2); ?> <br><?php echo "(".(float)($value->item_igst_percentage)."%)"; }?></td>

                            <?php
                        }  elseif ($igst_tax > 1) {
                            ?>
                            <td align="center"></td>
                            <td align="right"><?php if($value->item_igst_amount < 1){
                                echo '-';
                            }else{echo precise_amount($value->item_igst_amount*$convert_rate,2); ?><br><?php echo "(".(float)($value->item_igst_percentage)."%)"; }?></td>

                        <?php }
                        ?>
                        <?php
                        /*else if ($data[0]->billing_state_id == $branch[0]->branch_state_id) {
                            ?>
                            <!--<td align="center"></td> -->
                            <td align="right"><?php  if($value->item_cgst_amount < 1){ echo '-'; }else{
                              echo round($value->item_cgst_amount,2); ?><br><?php echo "(".round($value->item_cgst_percentage,2)."%)";   
                            } ?></td>
                           <!-- <td align="center"></td> -->
                            <td align="right"><?php if($value->item_sgst_amount < 1){
                                echo '-';
                            }else{ echo round($value->item_sgst_amount,2); ?><br><?php echo "(".round($value->item_sgst_percentage,2)."%)"; 
                            }?></td>

                            <?php
                        }*/
                        // $a = bcsub($value->sub_total, $value->discount, 2);
                        // $b = bcadd($a, $value->igst_tax, 2);
                        // $c = bcadd($b, $value->cgst_tax, 2);
                        // $d = bcadd($c, $value->sgst_tax, 2);
                        // $final = bcsub($d, $value->tds_amt, 2);
                        if ($cess_tax > 1) {
                            ?>
                           <!-- <td align="center"></td> -->
                            <td align="right"><?php if($value->item_cess_amount < 1){
                                echo '-';
                            }else{
                             echo precise_amount($value->item_cess_amount*$convert_rate,2); ?><br><?php echo "(".(float)($value->item_cess_percentage)."%)";    
                            }
                            
                            ?></td>
                            <?php
                        }
                        ?>
                        <td align="right"><?php echo precise_amount($value->item_grand_total*$convert_rate,2) ?></td>
                    </tr>
                    <?php
                    $i++;
                    // $quantity = bcadd($quantity, $value->item_quantity, 2);
                    // $price = bcadd($price, $value->item_sub_total, 2);
                    // $sub_total = bcadd($tot, $value->sub_total, 2);
                    // $discount = bcadd($discount, $value->discount_amt, 2);
                    // $taxable_value = bcadd($discount, $value->taxable_value, 2);
                    // $igst = bcadd($igst, $value->sales_item_igst_amount, 2);
                    // $cgst = bcadd($cgst, $value->sales_item_cgst_amount, 2);
                    // $sgst = bcadd($sgst, $value->sales_item_sgst_amount, 2);
                    $grand_total = bcadd($grand_total, $value->item_grand_total, 2);
                }
                ?>
                <tr>
                    <?php
                    if ($dpcount > 0) {
                        ?>
                        <th colspan="1" align="right" height="30px;"><b>Total</b></th>
                        <th colspan="1" align="right">&nbsp;</th>
                        <?php
                    } else {
                        ?>
                        <th colspan="1" align="right" height="30px;">Total</th>
                        <th colspan="1" align="right">&nbsp;</th>
                        <?php
                    }
                    ?>
                    <th align="right"><?php echo precise_amount($data[0]->voucher_sub_total*$convert_rate,2); ?></th>
                    <?php
                    if ($data[0]->voucher_igst_amount < 1 && $data[0]->voucher_cgst_amount > 1) {
                        ?>
                        <th align="right"><?php echo precise_amount($data[0]->voucher_cgst_amount*$convert_rate,2); ?></th>
                        <th align="right"><?php echo precise_amount($data[0]->voucher_sgst_amount*$convert_rate,2); ?></th>
                        <?php
                    } else if ($data[0]->voucher_igst_amount > 1 && $data[0]->voucher_cgst_amount < 1) {
                        ?>
                        <th align="right"><?php echo precise_amount($data[0]->voucher_tax_amount*$convert_rate,2); ?></th>
                        <?php
                    }elseif ($igst_tax > 1) {
                        ?>
                        <th align="right"><?php echo precise_amount($data[0]->voucher_igst_amount*$convert_rate,2); ?></th>
                        <?php
                    }
                    if ($cess_tax > 1) {
                        ?>
                        <th align="right"><?php echo precise_amount($data[0]->voucher_cess_amount*$convert_rate,2); ?></th>
                        <?php
                    }
                    /* else if ($data[0]->billing_state_id == $branch[0]->branch_state_id) {
                        ?>
                        <th align="right"><?php echo round($data[0]->voucher_cgst_amount,2); ?></th>
                        <th align="right"><?php echo round($data[0]->voucher_sgst_amount,2); ?></th>
                        <?php
                    } */
                    ?>
                    <th align="right"><?php echo precise_amount($grand_total*$convert_rate,2) ?></th>
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
                <td align="right"><b>Total Value </b></td>
                <td align="right"><?php echo formatinr($data[0]->voucher_sub_total*$convert_rate, 2); ?></td>
            </tr>
            <?php if ($igst_tax > 0) { ?>
            <tr>
                <td align="right">IGST</td>
                <td align="right"><?= formatinr($data[0]->voucher_igst_amount*$convert_rate,2); ?></td>
            </tr>
            <?php } elseif ($cgst_tax > 0 || $sgst_tax > 0) { ?>
                <tr>
                    <td align="right"><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></td>
                    <td align="right"> <?= formatinr($data[0]->voucher_sgst_amount*$convert_rate,2); ?></td>
                </tr>
                <tr>
                    <td align="right">CGST</td>
                    <td align="right"> <?= formatinr($data[0]->voucher_cgst_amount*$convert_rate,2); ?></td>
                </tr>
            <?php } ?>
            <?php if ($cess_tax > 0) { ?>
            <tr>
                <td align="right">CESS</td>
                <td align="right"><?= formatinr($data[0]->voucher_cess_amount*$convert_rate,2); ?></td>
            </tr>   
            <?php } ?>
            <?php if ($data[0]->round_off_amount > 0) { ?>
                <tr>
                    <td align="right" >Round Off</td>
                    <td align="right"> <?php echo formatinr($data[0]->round_off_amount*$convert_rate,2); ?></td>
                </tr>
            <?php } ?>  
            <?php
            if ($data[0]->round_off_amount < 0) {
                ?>
                <tr>
                    <td align="right" >Round Off(-)</td>
                    <td align="right"><?php echo formatinr(abs($data[0])*$convert_rate,2); ?></td>
                </tr>
            <?php } ?>  
            <tr>
            	<td align="right"><b>Receipt Amount</b> (<img class="img" src="<?php echo FCPATH . '/assets/images/currency/'.$data[0]->currency_symbol_pdf; ?>" style="height: 9px"  />)</td>
                <td align="right"><b><?php echo formatinr($data[0]->receipt_amount*$convert_rate,2); ?>/-</b></td>
            </tr>    
        </table>
        </td>
        </tr>
        </table>
        <div class="mt-50">
            <p class="amount_text">
                Amount (in Words): <b><?php echo $data[0]->currency_name." ".$this->numbertowords->convert_number(precise_amount($data[0]->receipt_amount*$convert_rate),$data[0]->unit,$data[0]->decimal_unit) . " Only"; ?> </b><br/>
            </p>
        </div>
       <!--  <?php
        $notes_sub_module = 0;
        $this->load->view('note_template/display_note');
        ?>   -->
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
             <div class="footer"><?php echo $access_common_settings[0]->invoice_footer; ?></div>
            <?php  } ?>       
    </body>
</html>