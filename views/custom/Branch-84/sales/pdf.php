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
   
$convert_rate = 1;
if (@$converted_rate)
    $convert_rate = $converted_rate;
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        

.custom-page-start {
    margin-top: 50px;
}
 tr:last-child { page-break-after: never; }

</style>
 <style><?php $this->load->view('common/common_pdf');?></style>
</head>
<body>
            <span style="float: right; margin-top: -25px"><h3><small style="font-size: 8px;line-height: 2"><?=($invoice_type != '' ? '('.$invoice_type.')' : ''); ?></small></h3>
           <br><p class="right"></p></span>
                <table class="item-table table mt-49" style="min-height: 300px;">
                
                        <tr>
                            <th colspan="13">TAX INVOICE</th>
                        </tr>
                    
                <tr>
                    <th colspan="4" style="text-align: left;">
                        <span>
                            <b class="uppercase">  <?= strtolower($branch[0]->firm_name) ?></b><br>
                            <span style="font-weight: normal;">
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
                        ?>
                    </span>
                        
                        <?php
                        if (isset($branch[0]->branch_email_address)) {
                        if ($branch[0]->branch_email_address != "") {
                            echo '<br>Email Id : <span style="font-weight: normal;">' . $branch[0]->branch_email_address.'</span>';
                        }                        
                        }
                        ?>

                        <?php
                        if (isset($branch[0]->branch_land_number)) {
                            if ($branch[0]->branch_land_number != "") {
                                echo '<br>Phone No : <span style="font-weight: normal;">' . $branch[0]->branch_land_number.'</span>';
                            }
                        }
                        ?>
                        <?php
                        if (isset($branch[0]->branch_gstin_number)) {
                        if ($branch[0]->branch_gstin_number != "" || $branch[0]->branch_gstin_number != null) {
                            echo '<br>GSTIN : <span style="font-weight: normal;">' . $branch[0]->branch_gstin_number.'</span>';
                        }
                        ?>
                        <?php } ?>

                         <?php
                        if (isset($branch[0]->branch_state_name)) {
                        if ($branch[0]->branch_state_name != "" || $branch[0]->branch_state_name != null) {
                            echo '<br>State : <span style="font-weight: normal;">' . $branch[0]->branch_state_name.' ['. $branch[0]->branch_state_code.']</span>';
                        }
                        ?>
                        <?php } ?>
                   
                            
                        </span>
                    </th>
                    <th colspan="6" style="text-align: left;">
                        Buyer Details<br>
                        <span style="font-weight: normal;">
                         <?php
                    /*if ($data[0]->shipping_address_id != $data[0]->billing_address_id) {*/
                        if (!empty($billing_address)) {
                            echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $billing_address[0]->shipping_address);
                        } elseif (isset($data[0]->customer_address) && $data[0]->customer_address != "") {
                            echo "<br/>";
                            echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $data[0]->customer_address);
                        } else {
                            echo "<br/>";
                            echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $data[0]->shipping_address);
                        }
                    /*}*/
                    ?></span><br>

                        State: <span style="font-weight: normal;"><?php if (isset($data[0]->place_of_supply)) {
                        if ($data[0]->place_of_supply != "" || $data[0]->place_of_supply != null) {
                            echo $data[0]->place_of_supply;
                        }
                    }?> Code: <?php echo $data[0]->customer_state_code;?></span><br>
                        Phone No : <span style="font-weight: normal;"><?php
                    if (isset($data[0]->customer_mobile)) {
                        if ($data[0]->customer_mobile != "") {
                            echo $data[0]->customer_mobile;
                        }
                    }
                    ?></span><br>
                        GSTIN : <span style="font-weight: normal;"><?php
                    if (isset($data[0]->customer_gstin_number)) {
                        if ($data[0]->customer_gstin_number != "") {                           
                            echo $data[0]->customer_gstin_number;
                        }
                    }
                    ?></span><br>
                    </th>
                    <th colspan="3" style="text-align: left;">
                        Invoice No. :<span style="font-weight: normal;"><?php
                        if (isset($data[0]->sales_invoice_number)) {
                            echo $data[0]->sales_invoice_number;
                        }
                        ?></span><br>
                        Date :<span style="font-weight: normal;"><?php
                    $date = $data[0]->sales_date;
                    $date_for = date('d-m-Y', strtotime($date));
                    echo $date_for;
                    ?></span> <br>
                        Order No. :<br>
                        Lorry No. : <span style="font-weight: normal;"><?php $order_det = $data[0]; if ($order_det->lr_no != '') {
                                echo $order_det->lr_no;
                            }?></span><br>
                        Agent     :<br>
                        E-Way No. :  <br>
                    </th>
                </tr>
                </tr>
                      <thead style="display: table-header-group;">
                <tr>
                    <th>Sl<br>No</th>
                    <th style="width:20%">Item Description</th>
                    <th>HSN/SAC</th>
                    <th>GST%</th>
                    <th>MRP</th>
                    <th>Qty</th>
                    <th>Free</th>
                    <th>Rate</th>
                    <th>T. Disc</th>
                    <th>Sch. Dis</th>
                    <th>Amount</th>
                    <th>GST Amt</th>
                    <th>Net Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $quantity = 0;
                $price = 0;
                $gst = 0;
                $grand_total = $tot_cgst = $tot_sgst = $tot_igst = $tot_gst = $tot_mrp = $tot_qty = $tot_qty_free = $tot_price = $tot_discount = $tot_taxable = $tot_cgst_per = $tot_sgst_per = $tot_igst_per =  0;
                $discount_schme = 0;
                $discount_trade = 0;
                $i = 1;
                $gst_amount = 0; 
                $discount_amount  = 0;
                $discount_percentage = 0;
                $sch_discount_amount = 0;
                $tot_sch_discount = 0;
                foreach ($items as $value) { 
                    $product_name = '';
                    if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {
                         $product_name = $value->product_name;
                    }else{
                         $product_name = $value->service_name;
                    }
                    $gst = round(abs($value->sales_item_igst_percentage), 2) + round(abs($value->sales_item_cgst_percentage), 2) + round(abs($value->sales_item_sgst_percentage), 2);

                    $tot_cgst_per += $value->sales_item_cgst_percentage;
                    $tot_sgst_per += $value->sales_item_sgst_percentage;
                    $tot_igst_per += $value->sales_item_igst_percentage;

                    $gst_amount = $value->sales_item_igst_amount + $value->sales_item_cgst_amount + $value->sales_item_sgst_amount;
                    $discount_amount =  $value->sales_item_discount_amount;
                     $sch_discount_amount =$value->sales_item_scheme_discount_amount;
                    if($value->item_discount_percentage == NULL || $value->item_discount_percentage == ''){
                        $discount_per = 0;
                    }else{
                      $discount_per =   $value->item_discount_percentage;
                    }
                    $discount_percentage = $discount_per + $value->sales_item_scheme_discount_percentage; 
                    $grand_total += $value->sales_item_grand_total;
                    $tot_gst += $gst_amount;
                    $tot_mrp += $value->sales_item_mrp_price;
                    $tot_qty += $value->sales_item_quantity;
                    $tot_qty_free += $value->sales_item_free_quantity;
                    $tot_price += $value->sales_item_unit_price;
                    $tot_discount += $discount_amount;
                    $tot_sch_discount += $sch_discount_amount;
                    $tot_taxable += $value->sales_item_taxable_value;
                    $tot_igst += $value->sales_item_igst_amount;
                    $tot_sgst += $value->sales_item_sgst_amount;
                    $tot_cgst += $value->sales_item_cgst_amount;
                    ?>                
                <tr style="page-break-before: always; page-break-after: always;">
                    <td><?php echo $i;?></td>
                    <td style="text-align: left"><?php echo strtoupper(strtolower($product_name));?></td>
                    <td><?php echo $value->product_hsn_sac_code; ?></td>
                    <td><?php echo $gst;?></td>
                    <td><?php echo precise_amount($value->sales_item_mrp_price);?></td>
                    <td><?php echo precise_amount($value->sales_item_quantity);?></td>
                    <td><?php echo precise_amount($value->sales_item_free_quantity);?></td>
                    <td><?php echo precise_amount($value->sales_item_unit_price);?></td>
                    <td><?php echo precise_amount($value->sales_item_discount_amount); ?><br>(<?php echo precise_amount($value->item_discount_percentage);?>%)</td>
                   <td><?php echo precise_amount($value->sales_item_scheme_discount_amount); ?><br>(<?php echo precise_amount($value->sales_item_scheme_discount_percentage)?>%)</td>
                    <td><?php echo precise_amount($value->sales_item_taxable_value); ?></td>
                    <td><?php echo precise_amount($gst_amount); ?></td>
                    <td><?php echo precise_amount($value->sales_item_grand_total); ?></td>
                </tr>
                <?php
                $i++;
                    }
                ?> 
                 <tr style="page-break-before: always; page-break-after: always;">
                    <th></th>
                    <th style="text-align: left"></th>
                    <th></th>
                    <th></th>
                    <th><?php echo precise_amount($tot_mrp);?></th>
                    <th><?php echo precise_amount($tot_qty);?></th>
                    <th><?php echo precise_amount($tot_qty_free);?></th>
                    <th><?php echo precise_amount($tot_price);?></th>
                    <th><?php echo precise_amount($tot_discount); ?></th>
                    <th><?php echo precise_amount($tot_sch_discount); ?></th>
                    <th><?php echo precise_amount($tot_taxable); ?></th>
                    <th><?php echo precise_amount($tot_gst); ?></th>
                    <th><?php echo precise_amount($grand_total); ?></th>
                </tr>
                <tr style="page-break-before: always; page-break-after: always;">
                    <td colspan="8" style="text-align: left;">
                        <table>
                            <tr>
                                <th>Taxable Value</th>
                                <th>CGST %</th>
                                <th>CGST AMT</th>
                                <th>SGST %</th>
                                <th>SGST AMT</th>
                                <th>IGST %</th>
                                <th>IGST AMT</th>
                            </tr>
                            <tr>
                                <td><?php echo precise_amount($tot_taxable); ?></td>
                                <th><?php echo precise_amount($tot_cgst_per);?></th>
                                <th><?php echo precise_amount($tot_cgst);?></th>
                                <th><?php echo precise_amount($tot_sgst_per);?></th>
                                <th><?php echo precise_amount($tot_sgst);?></th>
                                <th><?php echo precise_amount($tot_igst_per);?></th>
                                <th><?php echo precise_amount($tot_igst);?></th>
                            </tr>
                             <tr>
                            <th>Total</th>
                            <th>CGST</th>
                            <th><?php echo precise_amount($tot_cgst);?></th>
                            <th>SGST</th>
                            <th><?php echo precise_amount($tot_sgst);?></th>
                            <th>IGST</th>
                            <th><?php echo precise_amount($tot_igst);?></th>
                        </tr>
                        </table>
                    </td>
                    <td colspan="5" style="text-align: left;"><b>Bank Details:</b><br><b>Bank Name :</b> <br> <b>A/c No : </b><br> <b>Branch Name :</b> <br> <b>IFSC Code :</b></td>
                </tr>
                <tr>
                    <td colspan="8" style="text-align: left;"><b>Amount In Words : </b><?php echo $data[0]->currency_name . " " . $this->numbertowords->convert_number(precise_amount(($data[0]->sales_grand_total)),$data[0]->unit,$data[0]->decimal_unit) . " Only"; ?></td>
                    <td colspan="5" style="text-align: left;"></td>
                </tr>
                <tr>
                    <td colspan="8" style="text-align: left;"><b>Terms & Condition:</b></td>
                    <td colspan="5" style="text-align: left;"></td>
                </tr>
            </tbody>
            </table>
  <!-- Custom HTML header --> 
  <div id="header"> 
    <!--TAX INVOICE -->
    <!-- <span style="float: right; margin-top: -25px"><h3><small style="font-size: 8px;line-height: 2"><?=($invoice_type != '' ? $invoice_type: ''); ?></small></h3> 
            <br><p class="right"></p></span> 
            <table class="item-table table mt-20">
                <tr>
                    <th colspan="13">TAX INVOICE</th>
                </tr>
                </table> -->
  </div>  
  <!-- Custom HTML footer --> 
  <!-- <div id="footer"> 
        <?php echo $access_common_settings[0]->invoice_footer; ?>
  </div>  -->
  <div class="footer">
                <?php echo $access_common_settings[0]->invoice_footer; ?>
    </div>
 
</body>
</html>
