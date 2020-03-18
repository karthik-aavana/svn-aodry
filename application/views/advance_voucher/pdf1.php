<!DOCTYPE html>

<html>

    <head>

        <title>

            Advance Voucher Invoice

        </title>

        <style type="text/css">            

            table b {

                font-size: 0.875em;

                font-weight: 600;

            }

            table {

                border-collapse: collapse;

                border-spacing: 0px;

                width: 100%;

                font-family: 'Mukta', sans-serif;

                line-height:10px;

                font-size: 0.8em;

            }

            .item-table tr th, .item-table tr td {

                border: 1px solid #444;

                padding: 4px;

            }

            .custom_table tr th {

                font-size: 0.8125em;

            }

            .custom_table tr td {

                white-space: nowrap;

                table-layout: auto;

                font-size: 0.7em;

            }

            #notices{

                padding-left: 6px;

                border-left: 6px solid #0087C3;  

            }           

            #notices .notice {

                font-size: 0.8125em;

            }

            .item-table thead, .item-table tbody {

                text-align: center;

            }

            .invoice-header {

                font-size: 100%;

            }

            .center {

                text-align: center;

            }

            .right {

                text-align: right;

            }

            .left {

                text-align: left;

            }

            .capitalize {

                text-transform: capitalize !important;

                font-weight: 600;

            }

            .uppercase {

                text-transform: uppercase !important;

            }

            .bold {

                font-weight: bold;

            }

            .p-5 {

                padding: 5px;

            }

            .mt-20 {

                margin-top: 20px;

            }

            .mt-50 {

                margin-top: 50px;

            }

            .h-5 {

                height: 5px;

            }

            .pad-rgt {

                padding: 10px 80px 10px 10px;

            }

            .border-0 {

                border: 0px !important;

            }

            .hr{

                border-bottom: 1px solid #444;

                margin: 8px 0;              

            }

            .spl-table{

                font-size: 1em

            }

            .dashed_table{

                font-size:0.7em;

                vertical-align: middle;  

                width: 85% !important;                              

            }            

            .dashed_table tr th, .dashed_table tr td{

                border: 1px dashed #333;                

                padding: 4px;

            }            

            textarea{

                border: 0.5px solid #444;

            }           

            .bg-table{

                background: #EEEEEE;

                padding: 8px;

            } 

        </style>

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

        <?php

        $sid = $this->session->userdata('SESS_BRANCH_ID');



        if ($pdf_results['heading_position'] == 'top') {

            ?>

            <table class="table header_table">

                <?php

                if ($pdf_results['theme'] == 'default') {

                    ?>

                    <tr style="text-align: center;">

                        <td style="text-align: center;font-size: 20px">Advance Voucher1 </td>

                    </tr>

                    <?php

                }

                ?>

                <?php

                if ($pdf_results['theme'] == 'custom') {

                    ?>

                    <tr style="text-align: center;background-color: <?= $pdf_results['background']; ?>;border:none!important">

                        <td style="text-align: center;font-size: 20px;" class="white-text">Advance Voucher </td>

                    </tr>

                    <?php

                }

                ?>

            </table>



            <table class="table" width="100%" style="margin-top: 20px">

                <tr>

                    <td valign="top" width="50%">

                        Advance Voucher Date<br>

                        <?php

                        $date = $data[0]->voucher_date;

                        $date_for = date('d-m-Y', strtotime($date));

                        ?>

                        <p style="font-weight:bold"><?php

                            if (isset($data[0]->voucher_date)) {

                                echo $date_for;

                            }

                            ?></p>

                    </td>

                    <td valign="top" width="50%">

                        Advance Voucher Number<br>

                        <p style="font-weight:bold"><?php

                            if (isset($data[0]->voucher_invoice_number)) {

                                echo $data[0]->voucher_invoice_number;

                            }

                            ?></p>

                    </td>

                </tr>

            </table>





            <?php

        }

        ?>





        <table width="100%" class="table" style="border:none!important;margin-top: 20px">

            <tr style="border: 0px;">

                <td style="border: 0px;text-align: <?= $pdf_results['logo_align'] ?>; font-size: 20px"><?php

                    if (isset($branch[0]->firm_logo) && $branch[0]->firm_logo != "" && $pdf_results['logo'] == 'yes') {

                        ?>

                        <img src="<?php echo base_url('assets/branch_files/') . $branch[0]->branch_id . '/' . $branch[0]->firm_logo; ?>" style="max-width: 150px !important;max-height: 50px !important;" />

                        <?php

                    } else {

                        ?>

                        <?php

                        if ($pdf_results['theme'] == 'default' && $pdf_results['company_name'] == 'yes') {

                            ?>



                            <b style="font-size: 14px; text-transform: uppercase">  <?= $branch[0]->firm_name ?></b>

                            <?php

                        }

                        ?>

                        <?php

                        if ($pdf_results['theme'] == 'custom' && $pdf_results['company_name'] == 'yes') {

                            ?>



                            <b style="font-size: 14px; text-transform: uppercase; border-bottom: solid 2px #0177a9;">  <?= $branch[0]->firm_name ?></b>

                            <?php

                        }

                    }

                    ?>

                </td>



            </tr>

           <!--  <tr style="border: 0px;">

               <td style="border: 0px;text-align: right;">(<?php echo $invoice_type; ?>)</td>



            </tr> -->

        </table>

       <!--  <table width="100%" cellspacing="0" border-collapse="collapse" class="table header_table" style="margin-top:20px">

            <tr>

                <td style="padding: 5px;" align="center" width="50%" >Credit Note </td>



                <td valign="top" width="25%">

                        Sales Date<br>

        <?php

        $date = $data[0]->voucher_date;

        $date_for = date('d-m-Y', strtotime($date));

        ?>

                    <p style="font-weight:bold"><?php

        if (isset($data[0]->voucher_date)) {

            echo $date_for;

        }

        ?></p>

                </td>

                <td valign="top" width="25%">

                      Sales Number<br>

                    <p style="font-weight:bold"><?php

        if (isset($data[0]->voucher_invoice_number)) {

            echo $data[0]->voucher_invoice_number;

        }

        ?></p>

                </td>

            </tr>

        </table> -->



        <table width="100%" cellspacing="0" border-collapse="collapse" class="table header_table" style="margin-top:20px;border:none!important;">

            <tr>

                <?php

                if ($pdf_results['show_from'] == 'yes') {

                    ?>

                    <td width="50%" valign="top" style="border:<?php echo ($pdf_results['bordered'] == 'yes') ? '' : 'none'; ?>;">





                        <b style="font-size: 11px;">From,</b><br><br>

                        <b style="font-size: 11px; text-transform: capitalize;"><?php

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

                                // echo 'Contact No : ' . $branch[0]->branch_mobile;

                            }

                            ?>

                            <?php

                            if ($pdf_results['landline'] == 'yes') {

                                if (isset($branch[0]->branch_land_number)) {

                                    if ($branch[0]->branch_land_number != "") {

                                        echo '<br>Landline No : ' . $branch[0]->branch_land_number;

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

                                echo "<br />PAN : " . $branch[0]->branch_pan_number;

                            }

                            ?>



                            <?php

                        }

                        ?>



                        <?php

                        if (isset($branch[0]->branch_import_export_code_number) && ($pdf_results['iec'] == 'yes')) {

                            if ($branch[0]->branch_import_export_code_number != "" || $branch[0]->branch_import_export_code_number != null) {

                                echo "<br />IEC : " . $branch[0]->branch_import_export_code_number;

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

                                echo "<br />CIN : " . $branch[0]->branch_cin_number;

                            }

                        }

                        ?>



                    </td>

                    <?php

                }



                if ($pdf_results['l_r'] == 'yes') {

                    ?>

                    <td style="border:<?php echo ($pdf_results['bordered'] == 'yes') ? '' : 'none'; ?>"><b style="font-size: 11px;">To,</b><br><br>

                        <b style="font-size: 11px; text-transform: capitalize;"><?php

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

                    <?php

                }

                ?>

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











                <!-- <?php

                // if($this->session->userdata('SESS_BRANCH_ID')==) {

                ?> -->



<!-- <img width="250" src="<?= base_url('assets/img/Street-view-trusted-logo-new-grey.png') ?>"> -->

            </tr>



            <!--  <?php

            // }

            ?> -->



        </table>



        <?php

        if ($pdf_results['heading_position'] == 'center') {

            ?>

            <table width="100%" cellspacing="0" border-collapse="collapse" class="table header_table" style="margin-top:20px 0px;border:none!important">

                <?php

                if ($pdf_results['theme'] == 'default') {

                    ?>

                    <tr style="text-align: center;">

                        <td style="text-align: center;font-size: 20px">Advance Voucher </td>

                    </tr>

                    <?php

                }

                ?>

                <?php

                if ($pdf_results['theme'] == 'custom') {

                    ?>

                    <tr style="text-align: center;background-color: <?= $pdf_results['background']; ?>;border:none!important">

                        <td style="text-align: center;font-size: 20px;" class="white-text"> Advance Voucher </td>

                    </tr>

                    <?php

                }

                ?>

            </table>

            <table width="100%" style="margin-top: 20px">

                <tr>

                    <td valign="top" width="50%">

                        Advance Voucher Date<br>

                        <?php

                        $date = $data[0]->voucher_date;

                        $date_for = date('d-m-Y', strtotime($date));

                        ?>

                        <p style="font-weight:bold"><?php

                            if (isset($data[0]->voucher_date)) {

                                echo $date_for;

                            }

                            ?></p>

                    </td>

                    <td valign="top" width="50%">

                        Advance Voucher Number<br>

                        <p style="font-weight:bold"><?php

                            if (isset($data[0]->voucher_number)) {

                                echo $data[0]->voucher_number;

                            }

                            ?></p>

                    </td>

                </tr>

            </table>

            <?php

        }

        ?>

        <table width="100%" cellspacing="0" border-collapse="collapse" class="table header_table" style="margin-top:20px;border:none!important">

            <?php

            if ($pdf_results['l_r'] != 'yes') {

                ?>

                <tr style="border:<?php echo ($pdf_results['bordered'] == 'yes') ? '' : 'none'; ?>;">

                    <td> <b style="font-size: 11px;">To,</b><br><br>

                        <b style="font-size: 11px; text-transform: capitalize;"><?php

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

                         <b style="font-size: 11px;">Shipping Details,</b>

                         <br/><br/>

                           <b style="font-size: 11px; text-transform: capitalize;"></b><?php

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

        <table width="100%" cellspacing="0" border-collapse="collapse" class="table"  style="margin-top: 20px;">

            <thead>

                <tr <?php

                if ($pdf_results['theme'] == 'custom') {

                    ?> style="background-color: <?= $pdf_results['background']; ?>"<?php } ?>>

                    <!-- <th width="4%" rowspan="2" >Sl<br/>No</th> -->

                    <th rowspan="2" >Item Description</th>

                    <!-- <?php

                    if ($dpcount > 0) {

                        ?>

                                                <th rowspan="2"  >Description</th>

                    <?php } ?>  -->

                    <!-- <th colspan="1" style="border-top: 1px solid #333; border-right: 0">qty</th> -->

                    <th rowspan="2">Quantity</th>

                    <?php

                    if ($pdf_results['price'] == 'yes') {

                        ?>

                        <th rowspan="2">Rate</th>

                        <?php

                    }

                    ?>

<!-- <th rowspan="2">Sub <br/> Total</th>



<th rowspan="2">Taxable <br/>Value</th> -->

                    <?php

                    if ($igst_tax < 1 && $cgst_tax > 1) {

                        ?>

                        <?php

                        if ($pdf_results['cgst'] == 'yes') {

                            ?>

                            <th colspan="2">CGST</th>

                            <?php

                        }

                        if ($pdf_results['sgst'] == 'yes') {

                            ?>

                            <th colspan="2"><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></th>



                            <?php

                        }

                    } else if ($igst_tax > 1 && $cgst_tax < 1) {

                        ?>

                        <?php

                        if ($pdf_results['igst'] == 'yes') {

                            ?>

                            <th colspan="2" >IGST</th>

                            <?php

                        }

                    } else if ($data[0]->customer_state_code == $branch[0]->branch_state_code) {

                        ?>

                        <?php

                        if ($pdf_results['cgst'] == 'yes') {

                            ?>

                            <th colspan="2">CGST</th>

                            <?php

                        }

                        if ($pdf_results['sgst'] == 'yes') {

                            ?>

                            <th colspan="2"><?= ($is_utgst == '1' ? 'UTGST' : 'SGST'); ?></th>



                            <?php

                        }

                    } else {

                        if ($pdf_results['igst'] == 'yes') {

                            ?>

                            <th colspan="2" >IGST</th>

                            <?php

                        }

                    }

                    if ($cess_tax > 1) {

                        ?>

                        <th colspan="2" >CESS</th>

                        <?php

                    }

                    ?>

                    <th rowspan="2">Total</th>



                </tr>

                <tr>

                    <?php

                    if ($igst_tax < 1 && $cgst_tax > 1) {

                        if ($pdf_results['cgst'] == 'yes') {

                            ?>

                            <th>

                                Rate %

                            </th>

                            <th>

                                Amt

                            </th>

                            <?php

                        }



                        if ($pdf_results['sgst'] == 'yes') {

                            ?>

                            <th>

                                Rate %

                            </th>

                            <th>

                                Amt

                            </th>



                            <?php

                        }

                    } else if ($igst_tax > 1 && $cgst_tax < 1) {

                        ?>

                        <?php

                        if ($pdf_results['igst'] == 'yes') {

                            ?>

                            <th>

                                Rate %

                            </th>

                            <th>

                                Amt

                            </th>

                            <?php

                        }

                    } else if ($data[0]->billing_state_id == $branch[0]->branch_state_id) {

                        ?>



                        <?php

                        if ($pdf_results['cgst'] == 'yes') {

                            ?>

                            <th>

                                Rate %

                            </th>

                            <th>

                                Amt

                            </th>

                            <?php

                        }

                        if ($pdf_results['sgst'] == 'yes') {

                            ?>

                            <th>

                                Rate %

                            </th>

                            <th>

                                Amt

                            </th>





                            <?php

                        }

                    } else {

                        ?>

                        <?php

                        if ($pdf_results['igst'] == 'yes') {

                            ?>

                            <th>

                                Rate %

                            </th>

                            <th>

                                Amt

                            </th>

                            <?php

                        }

                    }

                    if ($cess_tax > 1) {

                        ?>

                        <th>Rate %</th>

                        <th>Amt</th>

                    </tr>

                    <?php

                }

                ?>

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

                       <!--  <td align="center"><?php echo $i; ?></td> -->

                        <?php if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {

                            ?>

                            <td ><?php echo $value->product_name; ?><br>HSN/SAC: <?php echo $value->product_hsn_sac_code; ?><br><?php echo $value->item_description; ?></td><?php

                        } elseif ($value->item_type == 'advance') {

                            ?>

                            <td ><?php echo $value->product_name; ?></td>

                            <?php

                        } else {

                            ?>

                            <td ><?php echo $value->service_name; ?><br>HSN/SAC: <?php echo $value->service_hsn_sac_code; ?><br><?php echo $value->item_description; ?></td>

                        <?php } ?>



                        <!--  <?php

                        if ($dpcount > 0) {

                            ?>

                                                     <td  ><?php echo $value->item_description; ?></td>

                        <?php } ?> -->



                        <td align="right"><?php echo $value->item_quantity; ?></td>

                        <?php

                        if ($pdf_results['price'] == 'yes') {

                            ?>

                            <td align="right"><?php echo $value->item_sub_total; ?></td>

                            <?php

                        }

                        ?>

    <!-- <td align="right"><?php echo $value->item_sub_total; ?></td> -->



                        <!--  <?php

                        if ($dtcount > 0) {

                            ?>

                                                     <td align="right"></td>



                        <?php } ?> -->



                                            <!-- <td align="right"><?php echo $value->item_sub_total; ?></td> -->

                        <?php

                        if ($value->item_igst_amount < 1 && $value->item_cgst_amount > 1) {

                            ?>

                            <?php

                            if ($pdf_results['cgst'] == 'yes') {

                                ?>

                                <td align="center"><?php echo $value->item_cgst_percentage; ?></td>

                                <td align="right"><?php echo $value->item_cgst_amount; ?></td>

                                <?php

                            }

                            if ($pdf_results['sgst'] == 'yes') {

                                ?>

                                <td align="center"><?php echo $value->item_sgst_percentage; ?></td>

                                <td align="right"><?php echo $value->item_sgst_amount; ?></td>

                                <?php

                            }

                            ?>

                            <?php

                        } else if ($value->item_igst_amount > 1 && $value->item_cgst_amount < 1) {

                            ?>

                            <?php

                            if ($pdf_results['igst'] == 'yes') {

                                ?>

                                ?>

                                <td align="center"><?php echo $value->item_igst_percentage; ?></td>

                                <td align="right"><?php echo $value->item_igst_amount; ?></td>



                                <?php

                            }

                        } else if ($data[0]->billing_state_id == $branch[0]->branch_state_id) {

                            ?>

                            <?php

                            if ($pdf_results['cgst'] == 'yes') {

                                ?>

                                ?>

                                <td align="center"><?php echo $value->item_cgst_percentage; ?></td>

                                <td align="right"><?php echo $value->item_cgst_amount; ?></td>

                                <?php

                            }

                            if ($pdf_results['sgst'] == 'yes') {

                                ?>

                                <td align="center"><?php echo $value->item_sgst_percentage; ?></td>

                                <td align="right"><?php echo $value->item_sgst_amount; ?></td>



                                <?php

                            }

                        } else {

                            ?>

                            <?php

                            if ($pdf_results['igst'] == 'yes') {

                                ?>

                                <td align="center"><?php echo $value->item_igst_percentage; ?></td>

                                <td align="right"><?php echo $value->item_igst_amount; ?></td>



                                <?php

                            }

                        }

                        ?>

                        <?php

                        // $a = bcsub($value->sub_total, $value->discount, 2);

                        // $b = bcadd($a, $value->igst_tax, 2);

                        // $c = bcadd($b, $value->cgst_tax, 2);

                        // $d = bcadd($c, $value->sgst_tax, 2);

                        // $final = bcsub($d, $value->tds_amt, 2);



                        if ($cess_tax > 1) {

                            ?>

                            <td align="center"><?php echo $value->item_cess_percentage; ?></td>

                            <td align="right"><?php echo $value->item_cess_amount; ?></td>

                            <?php

                        }

                        ?>

                        <td align="right"><?php echo $value->item_grand_total ?></td>

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



                        <?php

                    } else {

                        ?>

                        <th colspan="1" align="right">Total</th>

                        <?php

                    }

                    ?>



                    <th colspan="1" align="right">&nbsp;</th>

  <!-- <th align="right">1</th> -->

                    <?php

                    if ($pdf_results['price'] == 'yes') {

                        ?>

                        <th align="right"><?php echo $data[0]->voucher_sub_total; ?></th>

                        <?php

                    }

                    ?>

<!-- <th align="right">

    <?php echo $data[0]->voucher_sub_total; ?></th> -->



<!-- <th align="right">

    <?php echo $data[0]->voucher_sub_total; ?></th> -->



                    <?php

                    if ($data[0]->voucher_igst_amount < 1 && $data[0]->voucher_cgst_amount > 1) {

                        ?>

                        <?php

                        if ($pdf_results['cgst'] == 'yes') {

                            ?>

                            <th align="right" colspan="2"><?php echo $data[0]->voucher_cgst_amount; ?></th>

                            <?php

                        }

                        if ($pdf_results['sgst'] == 'yes') {

                            ?>



                            <th align="right" colspan="2"><?php echo $data[0]->voucher_sgst_amount; ?></th>



                            <?php

                        }

                    } else if ($data[0]->voucher_igst_amount > 1 && $data[0]->voucher_cgst_amount < 1) {

                        ?>

                        <?php

                        if ($pdf_results['igst'] == 'yes') {

                            ?>

                            <th align="right" colspan="2"><?php echo $data[0]->voucher_igst_amount; ?></th>



                            <?php

                        }

                    } else if ($data[0]->billing_state_id == $branch[0]->branch_state_id) {

                        ?>

                        <th align="right" colspan="2"><?php echo $data[0]->voucher_cgst_amount; ?></th>



                        <th align="right" colspan="2"><?php echo $data[0]->voucher_sgst_amount; ?></th>

                        <?php

                    } else {

                        ?>

                        <th align="right" colspan="2"><?php echo $data[0]->voucher_igst_amount; ?></th>

                        <?php

                    }

                    if ($cess_tax > 1) {

                        ?>

                        <th align="right" colspan="2"><?php echo $data[0]->voucher_cess_amount; ?></th>



                        <?php

                    }

                    ?>

                    <th align="right"><?php echo $grand_total ?></th>

                </tr>

            </tbody>

        </table>

        <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">

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

                        <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Freight Charge (<?php echo $data[0]->currency_code; ?>)</td>

                        <td align="right" class="footerpad fontH"><?php echo $data[0]->total_freight_charge; ?> /-</td>

                    </tr>

                    <?php

                }

                if ($data[0]->total_insurance_charge > 0) {

                    ?>

                    <tr class="no-bottom-border no-top-border">

                        <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Insurance Charge (<?php echo $data[0]->currency_code; ?>)</td>

                        <td align="right" class="footerpad fontH"><?php echo $data[0]->total_insurance_charge; ?> /-</td>

                    </tr>

                    <?php

                }

                if ($data[0]->total_packing_charge > 0) {

                    ?>

                    <tr class="no-bottom-border no-top-border">

                        <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Packing & Forwarding Charge (<?php echo $data[0]->currency_code; ?>)</td>

                        <td align="right" class="footerpad fontH"><?php echo $data[0]->total_packing_charge; ?> /-</td>

                    </tr>

                    <?php

                }

                if ($data[0]->total_incidental_charge > 0) {

                    ?>

                    <tr class="no-bottom-border no-top-border">

                        <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Incidental Charge (<?php echo $data[0]->currency_code; ?>)</td>

                        <td align="right" class="footerpad fontH"><?php echo $data[0]->total_incidental_charge; ?> /-</td>

                    </tr>

                    <?php

                }

                if ($data[0]->total_inclusion_other_charge > 0) {

                    ?>

                    <tr class="no-bottom-border no-top-border">

                        <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Other Inclusive Charge (<?php echo $data[0]->currency_code; ?>)</td>

                        <td align="right" class="footerpad fontH"><?php echo $data[0]->total_inclusion_other_charge; ?> /-</td>

                    </tr>

                    <?php

                }

                if ($data[0]->total_exclusion_other_charge > 0) {

                    ?>

                    <tr class="no-bottom-border no-top-border">

                        <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;">Other Exclusive Charge (<?php echo $data[0]->currency_code; ?>)</td>

                        <td align="right" class="footerpad fontH">-<?php echo $data[0]->total_exclusion_other_charge; ?> /-</td>

                    </tr>

                    <?php

                }

            }

            ?>

        </table>

        <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">

            <tr>

                <td align="right" class="footerpad fontH"><b>Receipt Amount: <?= $data[0]->currency_code ?> <?php echo $data[0]->receipt_amount; ?> /-</td>

            </tr>

            <tr>

                <td style="" align="left" height="25">

                    Amount (in Words): <b><?php echo $data[0]->currency_text . " " . $this->numbertowords->convert_number($data[0]->receipt_amount) . " Only"; ?> </b><br/>

                </td>

            </tr>

        </table>

        <?php

        if (isset($data[0]->voucher_type_of_supply) && $data[0]->voucher_type_of_supply != "regular") {

            ?>

            <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">

                <tr>

                    <td style="" valign="top">

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

        $this->load->view('note_template/display_note');

        ?>

        <br/>

        <?php

        if (isset($access_common_settings[0]->invoice_footer)) {

            ?>

            <table>

                <tr>

                    <td colspan="14" style="border:0px;text-align: center;font-size: 13px;"><?php echo $access_common_settings[0]->invoice_footer; ?></td>

                </tr>

            </table>



            <?php

        }

        if ($pdf_results['display_affliate'] == 'yes') {

            ?>

            <table width="100%" style="margin:50px 0px 0px 0px;">

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

        }

        ?>

    </body>

</html>

