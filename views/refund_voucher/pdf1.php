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
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>
            Refund Voucher
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
        <span style="float: right; margin-top: -25px"><h3><u><?= (@$title ? $title : 'Refund Voucher'); ?></u></h3></span>
        <table class="table header_table mt-20">
            <tr>
                <?php
                if ($pdf_results['show_from'] == 'yes') {
                    ?>
                    <td valign="top" style="border:<?php echo ($pdf_results['bordered'] == 'yes') ? '' : 'none'; ?>;">
                        <b>From,</b><br>
                        <b><?php
                            if (isset($branch[0]->branch_name)) {
                                echo $branch[0]->branch_name;
                            }
                            ?></b>
                        <br>
                        <?php
                        if ($pdf_results['address'] == 'yes') {
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
                        }
                        if ($pdf_results['state'] == 'yes') {
                            if (isset($state[0]->state_name)) {
                                echo $state[0]->state_name . ",";
                            }
                        }
                        ?> <?php
                        if ($pdf_results['country'] == 'yes') {
                            if (isset($country[0]->country_name)) {
                                echo $country[0]->country_name . ".";
                                ?>
                                <?php
                            }
                        }
                        if ($pdf_results['mobile'] == 'yes') {
                            if (isset($branch[0]->branch_mobile)) {
                                if ($branch[0]->branch_mobile != "" || $branch[0]->branch_mobile != null) {
                                    echo '<br>Mobile No : ' . $branch[0]->branch_mobile;
                                }
                            }
                            ?>
                            <?php
                            if ($pdf_results['landline'] == 'yes') {
                                if (isset($branch[0]->branch_land_number)) {
                                    if ($branch[0]->branch_land_number != "") {
                                        echo ' | Landline No : ' . $branch[0]->branch_land_number;
                                    }
                                }
                            }
                            ?>
                            <?php
                        }
                        if (isset($branch[0]->branch_email_address) && ($pdf_results['email'] == 'yes')) {
                            if ($branch[0]->branch_email_address != "") {
                                echo '<br>E-mail : ' . $branch[0]->branch_email_address;
                            }
                            ?>
                            <?php
                        }
                        if (isset($branch[0]->branch_gstin_number) && ($pdf_results['gst'] == 'yes')) {
                            if ($branch[0]->branch_gstin_number != "" || $branch[0]->branch_gstin_number != null) {
                                echo "<br>GSTIN : " . $branch[0]->branch_gstin_number;
                            }
                            ?>
                            <?php
                        }
                        ?>
                        <?php
                        if (isset($branch[0]->branch_pan_number) && $branch[0]->branch_pan_number != "" && ($pdf_results['pan'] == 'yes')) {
                            if ($branch[0]->branch_pan_number != "" || $branch[0]->branch_pan_number != null) {
                                echo "<br>PAN : " . $branch[0]->branch_pan_number;
                            }
                            ?>
                            <?php
                        }
                        ?>
                        <?php
                        if (isset($branch[0]->branch_import_export_code_number) && ($pdf_results['iec'] == 'yes')) {
                            if ($branch[0]->branch_import_export_code_number != "" || $branch[0]->branch_import_export_code_number != null) {
                                echo " | IEC : " . $branch[0]->branch_import_export_code_number;
                            }
                            ?>
                            <?php
                        }
                        if (isset($branch[0]->branch_lut_number) && ($pdf_results['lut'] == 'yes')) {
                            if ($branch[0]->branch_lut_number != "" || $branch[0]->branch_lut_number != null) {
                                echo "<br />LUT : " . $branch[0]->branch_lut_number;
                            }
                            ?>

                            <?php
                        }
                        if (isset($branch[0]->branch_cin_number) && ($pdf_results['lut'] == 'yes')) {
                            if ($branch[0]->branch_cin_number != "" || $branch[0]->branch_cin_number != null) {
                                echo " | CIN : " . $branch[0]->branch_cin_number;
                            }
                        }
                        ?>
                        <?php
                    }

                    if ($pdf_results['l_r'] == 'yes') {
                        ?>
                        <br>
                        <br>
                        <b>To,</b><br>
                        <b><?php
                            if (isset($data[0]->customer_name) && ($pdf_results['to_company'] == 'yes')) {
                                echo $data[0]->customer_name;
                            }
                            ?></b>
                        <?php
                        if (isset($data[0]->customer_gstin_number)) {
                            if ($data[0]->customer_gstin_number != "") {
                                echo "<br/>GSTIN : ";
                                echo $data[0]->customer_gstin_number;
                            }
                        }
                        ?><br>
                            <?php
                            if (isset($data[0]->customer_address) && ($pdf_results['to_address'] == 'yes')) {
                                echo str_replace(array(
                                    "\r\n",
                                    "\\r\\n",
                                    "\n",
                                    "\\n"), "<br>", $data[0]->customer_address);
                            }
                            ?>
                        <br>
                        <?php
                        if (isset($data[0]->customer_city_name)) {
                            echo $data[0]->customer_city_name;
                        }
                        ?><?php
                        if (isset($data[0]->customer_postal_code)) {
                            if ($data[0]->customer_postal_code != "") {
                                echo "-" . $data[0]->customer_postal_code;
                            }
                        }
                        ?>
                        <br>
                        <?php
                        if (isset($data[0]->customer_state_name) && ($pdf_results['to_state'] == 'yes')) {
                            echo $data[0]->customer_state_name;
                        }
                        ?>,
                        <?php
                        if (isset($data[0]->customer_country_name) && ($pdf_results['to_country'] == 'yes')) {
                            echo $data[0]->customer_country_name;
                        }
                        ?>
                        <?php
                        if (isset($data[0]->customer_mobile) && ($pdf_results['to_mobile'] == 'yes')) {
                            if ($data[0]->customer_mobile != "") {
                                echo '<br>Mobile No : ' . $data[0]->customer_mobile;
                            }
                        }
                        ?>
                        <?php
                        if (isset($data[0]->customer_email) && ($pdf_results['to_email'] == 'yes')) {
                            if ($data[0]->customer_email != "") {
                                echo '<br>E-mail : ' . $data[0]->customer_email;
                            }
                        }
                        ?>
                        <?php
                        if (isset($data[0]->customer_state_code) && $data[0]->customer_state_code != 0 && ($pdf_results['to_state_code'] == 'yes')) {
                            echo '<br>State Code : ' . $data[0]->customer_state_code;
                        }
                        ?>
                        <?php
                        if (isset($data[0]->place_of_supply) && ($pdf_results['place_of_supply'] == 'yes')) {
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
                    <?php
                }
                ?>
                <td valign="top" width="30%" class="bg-table">               
                    Voucher Date :
                    <?php
                    $date = $data[0]->voucher_date;
                    $date_for = date('d-m-Y', strtotime($date));
                    ?>
                    <span style="font-weight:bold"><?php
                        if (isset($data[0]->voucher_date)) {
                            echo $date_for;
                        }
                        ?>
                    </span>  
                    <br>
                    Voucher Number :
                    <span style="font-weight:bold"><?php
                        if (isset($data[0]->voucher_number)) {
                            echo $data[0]->voucher_number;
                        }
                        ?>
                    </span>
                </td>  
                <?php
                if ($access_common_settings[0]->affliation_images != null) {
                    if ($pdf_results['l_r'] != 'yes' && $pdf_results['display_affliate'] == 'yes') {
                        $json_str = str_replace("\\", "", $access_common_settings[0]->affliation_images);
                        $arr = json_decode($json_str);
                        ?>
                        <td  width="50%" valign="top" style="text-align: center;border:<?php echo ($pdf_results['bordered'] == 'yes') ? '' : 'none'; ?>;">
                            <img width="200" height="150" style="margin: 20px 0px" src="<?= base_url('assets/affiliate/' . $sid . '/' . $arr[0]) ?>">
                        </td>
                        <?php
                    }
                }
                ?>
            </tr>
        </table>
        <table class="table header_table">
            <?php
            if ($pdf_results['l_r'] != 'yes') {
                ?>
                <tr style="border:<?php echo ($pdf_results['bordered'] == 'yes') ? '' : 'none'; ?>;">
                    <td> <b>To,</b>
                        <b><?php
                            if (isset($data[0]->customer_name) && ($pdf_results['to_company'] == 'yes')) {
                                echo $data[0]->customer_name;
                            }
                            ?></b>
                        <?php
                        if (isset($data[0]->customer_gstin_number)) {
                            if ($data[0]->customer_gstin_number != "") {
                                echo "<br/>GSTIN : ";
                                echo $data[0]->customer_gstin_number;
                            }
                        }
                        ?><br>
                            <?php
                            if (isset($data[0]->customer_address) && ($pdf_results['to_address'] == 'yes')) {
                                echo str_replace(array(
                                    "\r\n",
                                    "\\r\\n",
                                    "\n",
                                    "\\n"), "<br>", $data[0]->customer_address);
                            }
                            ?><br>
                        <?php
                        if (isset($data[0]->customer_city_name)) {
                            echo $data[0]->customer_city_name;
                        }
                        ?>  <?php
                        if (isset($data[0]->customer_postal_code)) {
                            if ($data[0]->customer_postal_code != "") {
                                echo "-" . $data[0]->customer_postal_code;
                            }
                        }
                        ?><br>
                        <?php
                        if (isset($data[0]->customer_state_name) && ($pdf_results['to_state'] == 'yes')) {
                            echo $data[0]->customer_state_name;
                        }
                        ?>,
                        <?php
                        if (isset($data[0]->customer_country_name) && ($pdf_results['to_country'] == 'yes')) {
                            echo $data[0]->customer_country_name;
                        }
                        ?>

                        <?php
                        if (isset($data[0]->customer_mobile) && ($pdf_results['to_mobile'] == 'yes')) {
                            if ($data[0]->customer_mobile != "") {
                                echo '<br>Mobile No : ' . $data[0]->customer_mobile;
                            }
                        }
                        ?>
                        <?php
                        if (isset($data[0]->customer_email) && ($pdf_results['to_email'] == 'yes')) {
                            if ($data[0]->customer_email != "") {
                                echo '<br>E-mail : ' . $data[0]->customer_email;
                            }
                        }
                        ?>

                        <?php
                        if (isset($data[0]->customer_state_code) && $data[0]->customer_state_code != 0 && ($pdf_results['to_state_code'] == 'yes')) {
                            echo '<br>State Code : ' . $data[0]->customer_state_code;
                        }
                        ?>

                        <?php
                        if (isset($data[0]->place_of_supply) && ($pdf_results['place_of_supply'] == 'yes')) {
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
                    <!-- <td colspan="2">
                         <b>Shipping Details,</b>
                         <br/><br/>
                           <b></b><?php
                    if (isset($data[0]->mode_of_shipment) && $data[0]->mode_of_shipment != "") {

                        echo "Shipment Mode: " . $data[0]->mode_of_shipment;
                        echo "<br/>";
                    }
                    ?>

                    <?php
                    if (isset($data[0]->ship_by) && $data[0]->ship_by != "") {



                        echo "Ship By : ";
                        echo $data[0]->ship_by;
                        echo "<br/>";
                    }
                    ?>

                    <?php
                    if (isset($data[0]->net_weight) && $data[0]->net_weight != "") {

                        echo "Net Weight : " . $data[0]->net_weight;
                        echo "<br/>";
                    }
                    ?>

                    <?php
                    if (isset($data[0]->gross_weight) && $data[0]->gross_weight != "") {

                        echo "Gross Weight : " . $data[0]->gross_weight;
                        echo "<br/>";
                    }
                    ?>

                    <?php
                    if (isset($data[0]->origin) && $data[0]->origin != "") {

                        echo "Orgin : " . $data[0]->origin;
                        echo "<br/>";
                    }
                    ?>

                    <?php
                    if (isset($data[0]->destination) && $data[0]->destination != "") {

                        echo "Destination : " . $data[0]->destination;
                    }
                    ?>
                <br/>
                    <?php
                    if (isset($data[0]->shipping_type) && $data[0]->shipping_type != "") {

                        echo $data[0]->shipping_type . ' : ' . $data[0]->shipping_type_place;

                        echo "<br/>";
                    }
                    ?>

                    <?php
                    if (isset($data[0]->lead_time) && $data[0]->lead_time != "") {

                        echo 'Lead Time : ' . $data[0]->lead_time;
                        echo "  ,  ";
                    }

                    if (isset($data[0]->warranty) && $data[0]->warrantys != "") {

                        echo 'Warranty : ' . $data[0]->warranty;
                    }
                    ?>
                    </td> --></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <table class="table item-table mt-20">
            <thead>
                <tr <?php
                if ($pdf_results['theme'] == 'custom') {
                    ?> style="background-color: <?= $pdf_results['background']; ?>"<?php } ?>>
                    <th>Items</th>   
                    <th>Description</th>                   
                    <?php
                    if ($pdf_results['price'] == 'yes') {
                        ?>
                        <th>Rate</th>
                        <?php
                    }
                    ?>
                    <?php
                    if ($igst_tax < 1 && $cgst_tax > 1) {
                        ?>
                        <?php
                        if ($pdf_results['cgst'] == 'yes') {
                            ?>
                            <th>CGST</th>
                            <?php
                        }
                        if ($pdf_results['sgst'] == 'yes') {
                            ?>
                            <th><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></th>

                            <?php
                        }
                    } else if ($igst_tax > 1 && $cgst_tax < 1) {
                        ?>
                        <?php
                        if ($pdf_results['igst'] == 'yes') {
                            ?>
                            <th>IGST</th>
                            <?php
                        }
                    } else if ($igst_tax > 1) {
                        if ($pdf_results['igst'] == 'yes') {
                            ?>
                            <th>IGST</th>
                            <?php
                        }
                    }
                    ?>
                    <?php
                    if ($cess_tax > 0) {
                        ?>
                        <th>CESS</th>
                        <?php
                    }
                    /*
                    else if ($data[0]->customer_state_code == $branch[0]->branch_state_code) {
                        ?>
                        <?php
                        if ($pdf_results['cgst'] == 'yes') {
                            ?>
                            <th colspan="2">CGST</th>
                            <?php
                        }
                        if ($pdf_results['sgst'] == 'yes') {
                            ?>
                            <th><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></th>

                            <?php
                        }
                    } 
                    */
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
                $cess = 0;
                $price = 0;
                $grand_total = 0;
                foreach ($items as $value) {
                    ?>
                    <tr>
                       
                        <?php if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {
                            ?>
                            <td><?php echo strtoupper(strtolower($value->product_name)); ?><br>(HSN/SAC: <?php echo $value->product_hsn_sac_code; ?>)<br><?php echo $value->product_batch; ?>
                            </td>
                            <td><?php echo $value->item_description; ?></td>
                            <?php
                        } elseif ($value->item_type == 'advance') {
                            ?>
                            <td><?php echo strtoupper(strtolower($value->product_name)); ?></td>
                            <td><?php echo $value->item_description; ?></td>
                            <?php
                        } else {
                            ?>
                            <td><?php echo strtoupper(strtolower($value->service_name)); ?><br>(HSN/SAC: <?php echo $value->service_hsn_sac_code; ?>)</td>                            
                            <td><?php echo $value->item_description; ?></td>
                        <?php } ?>
                        <?php
                        if ($pdf_results['price'] == 'yes') {
                            ?>
                            <td><?php echo precise_amount($value->item_sub_total, 2); ?></td>
                            <?php
                        }
                        ?>
    					<?php
                        if ($igst_tax < 1 && $cgst_tax > 1) {
                            ?>
                            <?php
                            if ($pdf_results['cgst'] == 'yes') {
                                ?>
                                <td><?php echo precise_amount($value->item_cgst_amount, 2); ?><br><?php echo (float)($value->item_cgst_percentage) . "%)"; ?></td>
                                <?php
                            }
                            if ($pdf_results['sgst'] == 'yes') {
                                ?>
                                <td><?php echo precise_amount($value->item_sgst_amount, 2); ?><br><?php echo "(" . (float)($value->item_sgst_percentage) . "%)"; ?></td>
                                <?php
                            }
                            ?>
                            <?php
                        } else if ($igst_tax > 1 && $cgst_tax < 1) {
                            ?>
                            <?php
                            if ($pdf_results['igst'] == 'yes') {
                                ?>
                                ?>
                                <td><?php echo precise_amount($value->item_igst_amount, 2); ?><br><?php echo "(" . (float)($value->item_igst_percentage) . "%)"; ?></td>

                                <?php
                            }
                        } else if ($igst_tax > 0) {
                            ?>
                            <?php
                            if ($pdf_results['igst'] == 'yes') {
                                ?>
                                <td><?php echo precise_amount($value->item_igst_amount, 2); ?><br><?php echo "(" . (float)($value->item_igst_percentage) . "%)"; ?></td>

                                <?php
                            }
                        }
                        ?>
                        <?php
                        /*else if ($data[0]->billing_state_id == $branch[0]->branch_state_id) {
                            ?>
                            <?php
                            if ($pdf_results['cgst'] == 'yes') {
                                ?>
                                ?>
                                <td><?php echo precise_amount($value->item_cgst_amount, 2); ?><br><?php echo "(" . precise_amount($value->item_cgst_percentage, 2) . "%)"; ?></td>
                                <?php
                            }
                            if ($pdf_results['sgst'] == 'yes') {
                                ?>
                                <td><?php echo precise_amount($value->item_sgst_amount, 2); ?><br><?php echo "(" . precise_amount($value->item_sgst_percentage, 2) . "%)"; ?></td>

                                <?php
                            }
                        } */
                        // $a = bcsub($value->sub_total, $value->discount, 2);
                        // $b = bcadd($a, $value->igst_tax, 2);
                        // $c = bcadd($b, $value->cgst_tax, 2);
                        // $d = bcadd($c, $value->sgst_tax, 2);
                        // $final = bcsub($d, $value->tds_amt, 2);
                        ?>
                        <?php if ($cess_tax > 0) {
                            ?>
                            <td><?php echo precise_amount($value->item_tax_cess_amount,2); ?><br><?php echo "(" . (float)($value->item_tax_cess_percentage) . "%)"; ?></td>
                        <?php }
                        ?>
                        <td><?php echo precise_amount($value->item_grand_total, 2); ?></td>
                    </tr>
                    <?php
                    $i++;
                    // $quantity = bcadd($quantity, $value->item_quantity, 2);
                    // $price = bcadd($price, $value->item_sub_total, 2);
                    // $sub_total = bcadd($tot, $value->sub_total, 2);
                    // $discount = bcadd($discount, $value->discount_amt, 2);
                    // $taxable_value = bcadd($discount, $value->taxable_value, 2);
                    $igst = bcadd($igst, $value->item_igst_amount, 2);
                    $cgst = bcadd($cgst, $value->item_cgst_amount, 2);
                    $sgst = bcadd($sgst, $value->item_sgst_amount, 2);
                    $cess = bcadd($cess, $value->item_tax_cess_amount, 2);

                    $grand_total = bcadd($grand_total, $value->item_grand_total, 2);
                }
                ?>
                <tr>
                	<th></th>
                    <?php
                    if ($dpcount > 0) {
                        ?>
                        <th height="30px;"><b>Total</b></th>
                        <?php
                    } else {
                        ?>
                        <th>Total</th>
                        <?php
                    }
                    ?>
  
                    <?php
                    if ($pdf_results['price'] == 'yes') {
                        ?>
                        <th><?php echo precise_amount($data[0]->voucher_sub_total, 2); ?></th>
                        <?php
                    }
                    ?>
                    <?php
                    if ($data[0]->voucher_igst_amount < 1 && $data[0]->voucher_cgst_amount > 1) {
                        ?>
                        <?php
                        if ($pdf_results['cgst'] == 'yes') {
                            ?>
                            <th><?php echo precise_amount($data[0]->voucher_cgst_amount, 2); ?></th>
                            <?php
                        }
                        if ($pdf_results['sgst'] == 'yes') {
                            ?>

                            <th ><?php echo precise_amount($data[0]->voucher_sgst_amount, 2); ?></th>

                            <?php
                        }
                    } else if ($data[0]->voucher_igst_amount > 1 && $data[0]->voucher_cgst_amount < 1) {
                        ?>
                        <?php
                        if ($pdf_results['igst'] == 'yes') {
                            ?>
                            <th><?php echo precise_amount($data[0]->voucher_igst_amount, 2); ?></th>

                            <?php
                        }
                    }  elseif ($igst_tax > 1) {
                        ?>
                        <th ><?php echo precise_amount($data[0]->voucher_igst_amount, 2); ?></th>
                    <?php }
                    ?>

                    <?php
                    if ($cess_tax > 0) {
                        ?>
                        <th ><?php echo precise_amount($data[0]->voucher_cess_amount, 2); ?></th>
                    <?php }
                    /*else if ($data[0]->billing_state_id == $branch[0]->branch_state_id) {
                        ?>
                        <th><?php echo precise_amount($data[0]->voucher_cgst_amount, 2); ?></th>

                        <th><?php echo precise_amount($data[0]->voucher_sgst_amount, 2); ?></th>
                        <?php
                    }*/
                    ?>
                    <th><?php echo precise_amount($grand_total, 2) ?></th>
                </tr>
            </tbody>
        </table>
        
        
        <table class="mt-20">
            <tr>
                <td style="width:66%">
                    <table class="dashed_table">
            <?php
            $charges_sub_module = 0;
            foreach ($access_sub_modules as $key => $value) {
                if (isset($charges_sub_module_id)) {
                    if ($charges_sub_module_id == $value->sub_module_id) {
                        $charges_sub_module = 1;
                    }
                }
            }
            if ($charges_sub_module == 1) {
                if ($data[0]->total_freight_charge > 0) {
                    ?>
                    <tr class="no-bottom-border no-top-border">
                        <td>Freight Charge (<img class="img" src="<?php echo FCPATH . '/assets/images/currency/'.$data[0]->currency_symbol_pdf; ?>" style="height: 9px"  />)</td>
                        <td><?php echo $data[0]->total_freight_charge; ?> /-</td>
                    </tr>
                    <?php
                }
                if ($data[0]->total_insurance_charge > 0) {
                    ?>
                    <tr class="no-bottom-border no-top-border">
                        <td>Insurance Charge (<img class="img" src="<?php echo FCPATH . '/assets/images/currency/'.$data[0]->currency_symbol_pdf; ?>" style="height: 9px"  />)</td>
                        <td><?php echo $data[0]->total_insurance_charge; ?> /-</td>
                    </tr>
                    <?php
                }
                if ($data[0]->total_packing_charge > 0) {
                    ?>
                    <tr class="no-bottom-border no-top-border">
                        <td>Packing & Forwarding Charge (<img class="img" src="<?php echo FCPATH . '/assets/images/currency/'.$data[0]->currency_symbol_pdf; ?>" style="height: 9px"  />)</td>
                        <td><?php echo $data[0]->total_packing_charge; ?> /-</td>
                    </tr>
                    <?php
                }
                if ($data[0]->total_incidental_charge > 0) {
                    ?>
                    <tr class="no-bottom-border no-top-border">
                        <td>Incidental Charge (<img class="img" src="<?php echo FCPATH . '/assets/images/currency/'.$data[0]->currency_symbol_pdf; ?>" style="height: 9px"  />)</td>
                        <td><?php echo $data[0]->total_incidental_charge; ?> /-</td>
                    </tr>
                    <?php
                }
                if ($data[0]->total_inclusion_other_charge > 0) {
                    ?>
                    <tr class="no-bottom-border no-top-border">
                        <td>Other Inclusive Charge (<img class="img" src="<?php echo FCPATH . '/assets/images/currency/'.$data[0]->currency_symbol_pdf; ?>" style="height: 9px"  />)</td>
                        <td><?php echo $data[0]->total_inclusion_other_charge; ?> /-</td>
                    </tr>
                    <?php
                }
                if ($data[0]->total_exclusion_other_charge > 0) {
                    ?>
                    <tr class="no-bottom-border no-top-border">
                        <td>Other Exclusive Charge (<img class="img" src="<?php echo FCPATH . '/assets/images/currency/'.$data[0]->currency_symbol_pdf; ?>" style="height: 9px"  />)</td>
                        <td>-<?php echo $data[0]->total_exclusion_other_charge; ?> /-</td>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
        </td>        
        <td style="width:30%">
                <table class="total_sum">
                 <tr>
                <td align="right"><b>Total Value</b></td>
                <td align="right"><b><?php echo precise_amount($data[0]->voucher_sub_total, 2); ?></b></td>
            </tr>
            <?php if ($igst_tax > 0.00) {
                ?>
                <tr>
                    <td align="right">IGST </td>
                    <td align="right"> <?php echo precise_amount($igst, 2); ?></td>
                </tr>
                <?php
            } else if ($sgst_tax > 0.00 && $cgst_tax > 0.00) {
                ?>
                <tr>
                    <td align="right"><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?> </td>
                    <td align="right"> <?php echo precise_amount($cgst, 2); ?></td>
                </tr>
                <tr>
                    <td align="right">CGST </td>
                    <td align="right"><?php echo precise_amount($sgst, 2); ?></td>
                </tr>
            <?php }
            ?>
            <?php
            if ($cess_tax > 0.00) {
                ?>
                <tr>
                    <td align="right">CESS </td>
                    <td align="right"> <?php echo precise_amount($cess, 2); ?></td>
                </tr>
            <?php }
            ?>
            <tr>
            	<td align="right"><b>Receipt Amount (<img class="img" src="<?php echo FCPATH . '/assets/images/currency/'.$data[0]->currency_symbol_pdf; ?>" style="height: 9px"  />) </b></td>
                <td align="right"><b><?php echo precise_amount($data[0]->receipt_amount, 2); ?>/-</b></td>
            </tr>           
        </table>   
        </td>
        </tr>
        </table>
        <table class="table mt-50">
        	 <tr>
                <td class="amount_text">
                    Amount (in Words): <b><?php echo $data[0]->currency_name." ".$this->numbertowords->convert_number(precise_amount($data[0]->receipt_amount),$data[0]->unit,$data[0]->decimal_unit) . " Only"; ?> </b><br/>
                </td>
            </tr>
        </table> 
        <?php
        if (isset($data[0]->voucher_type_of_supply) && $data[0]->voucher_type_of_supply != "regular") {
            ?>
            <table class="table mt-20">
                <tr>
                    <td valign="top">
                        <?php
                        if (isset($data[0]->voucher_type_of_supply) && $data[0]->voucher_type_of_supply == "export_with_payment") {
                            ?>
                            <p><b>NOTE : </b>SUPPLY MEANT FOR EXPORT ON PAYMENT OF INTEGRATED TAX</p>
                            <?php
                        } else if (isset($data[0]->voucher_type_of_supply) && $data[0]->voucher_type_of_supply == "export_without_payment") {
                            ?>
                            <p><b>NOTE : </b>SUPPLY MEANT FOR EXPORT UNDER BOND OR LETTER OF UNDERTAKING WITHOUT PAYMENT OF INTEGRATED TAX</p>
                        <?php } ?>
                    </td>
                </tr>
            </table>
        <?php } ?>
        <?php
        $notes_sub_module = 0;
        if (in_array($notes_sub_module_id, $access_sub_modules)) {
            $notes_sub_module = 1;
        }
       /* if ($notes_sub_module == 1) {
            $this->load->view('note_template/display_note');
        }*/
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
        if ($pdf_results['display_affliate'] == 'yes') {
            ?>
            <table>
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
                                <img height="50" src="<?= base_url('assets/affiliate/' . $sid . '/' . $key) ?>">
                                <?php
                            }
                            ?>
                        </td>
                    <?php } ?>
                </tr>
            </table>
            <?php
        }
        ?>        
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
