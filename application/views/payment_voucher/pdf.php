<?php
$GLOBALS['common_settings_amount_precision'] = $access_common_settings[0]->amount_precision;
function precise_amount($val) {
    $val = (float) $val;    
    $dat = number_format($val, $GLOBALS['common_settings_amount_precision'], '.', '');
    return $dat;
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

?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            Payment Voucher
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
        <span style="float: right; margin-top: -25px"><h3><u><?= (@$title ? $title : 'Payment Voucher'); ?></u></h3></span>
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
                            echo "<br>GSTIN : " . $branch[0]->branch_gstin_number;
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
                     
                </td>               
                <td valign="top" width="30%" class="bg-table">
                    Voucher Date:
                    <?php
                    $date = $data[0]->voucher_date;
                    $date_for = date('d-m-Y', strtotime($date));
                    ?>
                    <span class="bold"><?php
                        if (isset($data[0]->voucher_date)) {
                            echo $date_for;
                        }
                        ?>
                    </span>
                    <br>
                    Voucher Number : <span class="bold"> <?php
                        if (isset($data[0]->voucher_number)) {
                            echo $data[0]->voucher_number;
                        }
                        ?></span>
                        <br><br>
                    <span class="capitalize">To,</span><br>
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
                            "\\n"), "<br>", $data[0]->supplier_address. ', ' .$data[0]->city_name . ' ,' . $data[0]->state_name . ' - ' . $data[0]->supplier_postal_code);
                    } else {
                        echo "<br/>";
                        echo str_replace(array(
                            "\r\n",
                            "\\r\\n",
                            "\n",
                            "\\n"), "<br>", $data[0]->supplier_address);
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
                </td>                
            </tr>
        </table>
      <!--  <table  class="table item-table mt-20">
            <thead>
                <tr>
                    <th width="4%" >#</th>
                    <th>Reference Number</th>
                    <th>Bill Amount</th>
                    <th>Paid Amount</th>
                    <th>Pending Amount</th>
                    <th>Receipt Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $bill_amount = 0;
                $paid_amount = 0;
                $pending_amount = 0;
                $receipt_amount = 0;
                $reference_arr = explode(",", $data[0]->reference_number);
                $pending_arr = explode(",", $data[0]->invoice_balance_amount);
                $paid_arr = explode(",", $data[0]->invoice_paid_amount);
                $bill_arr = explode(",", $data[0]->invoice_total);
                $receipt_arr = explode(",", $data[0]->imploded_receipt_amount);
                foreach ($reference_arr as $key => $value) {
                    ?>
                    <tr>
                        <td align="center"><?= $i ?></td>
                        <td align="center"><?= $value ?></td>
                        <td align="center"><?= precise_amount($bill_arr[$key]) ?></td>
                        <td align="center"><?= precise_amount($paid_arr[$key]) ?></td>
                        <td align="center"><?= precise_amount($pending_arr[$key]) ?></td>
                        <td align="center"><?= precise_amount($receipt_arr[$key]) ?></td>
                    </tr>
                    <?php
                    $i++;
                    $pending_amount = bcadd($pending_amount, $pending_arr[$key]);
                    $paid_amount = bcadd($paid_amount, $paid_arr[$key]);
                    $bill_amount = bcadd($bill_amount, $bill_arr[$key]);
                    $receipt_amount = bcadd($receipt_amount, $receipt_arr[$key]);
                }
                ?>
                <tr>
                    <th></th>
                    <th></th>
                    <th align="center"><?= '<span style="font-family: DejaVu Sans; sans-serif;"></span>&nbsp;' ?><?= precise_amount($bill_amount) ?></th>  ' . $data[0]->currency_code . ' 
                    <th align="center"><?= '<span style="font-family: DejaVu Sans; sans-serif;"></span>&nbsp;' ?><?= precise_amount($paid_amount) ?></th>
                    <th align="center"><?= '<span style="font-family: DejaVu Sans; sans-serif;"></span>&nbsp;' ?><?= precise_amount($pending_amount) ?></th>
                    <th align="center"><?= '<span style="font-family: DejaVu Sans; sans-serif;"></span>&nbsp;' ?><?= precise_amount($receipt_amount) ?></th>
                </tr>
            </tbody>
        </table>
        <table class="table total_sum mt-20">
            <tr>
                <td align="right"><b>Voucher Amount (<?php echo '<span style="font-family: DejaVu Sans; sans-serif;"></span>'; ?>)</b> </td>
                <td align="right"><b><?php echo precise_amount($data[0]->receipt_amount); ?></b></td>
            </tr>           
        </table> -->
        <div class="mt-50">
            <p style="line-height: 1.5">
                Paid to <b><?php echo strtoupper(strtolower($data[0]->supplier_name));?> </b>
                <br>Amount (<img class="img" src="<?php echo FCPATH . '/assets/images/currency/'.$currency_symbol_pdf; ?>" style="height: 9px"  />)<b> <?php echo formatinr($data[0]->receipt_amount); ?> /-</b>
                </p>
                <p class="amount_text">
                Amount (in Words) : <b><?php echo $currency_text . " " . $this->numbertowords->convert_number(precise_amount($data[0]->receipt_amount),$data[0]->unit,$data[0]->decimal_unit) . " Only"; ?> </b><br/>
            </p>
        </div>
         <!--<div class="mt-20">
            <p>
                 Amount (in Words): <b><?php echo $this->numbertowords->convert_number(precise_amount($data[0]->receipt_amount)) . " Only"; ?> </b><br/>
            </p>
        </div> -->
       <!--  <?php
        $notes_sub_module = 0;
        $this->load->view('note_template/display_note');
        ?> --> 
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
       <!--  <?php
        if (isset($access_common_settings[0]->invoice_footer) && $access_common_settings[0]->invoice_footer != "") {
            ?>
            <table>
                <tr>
                    <td colspan="14" style="border:0px;text-align: center;font-size: 13px;"><?php echo $access_common_settings[0]->invoice_footer; ?></td>
                </tr>
            </table>
            <?php
        }
        ?> -->
       

        <?php
        if (isset($access_common_settings[0]->invoice_footer)) {
            ?>
             <div class="footer">
                <?php echo $access_common_settings[0]->invoice_footer; ?>
             </div>
            <?php
        }
        ?>
    <br/>
</body>
</html>