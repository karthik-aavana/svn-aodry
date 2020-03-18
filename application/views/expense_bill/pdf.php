<!DOCTYPE html>
<html>
    <head>
        <title>
            Expense Bill
        </title>        
    </head>
    <body>
        <table width="100%" class="table">
            <tr style="border: 0px;">
                <td style="border: 0px;text-align: center; font-size: 20px">Expense Bill </td>
            </tr>
        </table>
        <table width="100%" cellspacing="0" border-collapse="collapse" class="table header_table" style="margin-top:20px">
            <tr>
                <td style="padding: 5px;" align="center" width="50%" >
                    <?php
                    if (isset($branch[0]->firm_logo) && $branch[0]->firm_logo != "") {
                        ?>
                        <img src="<?php echo base_url('assets/branch_files/') . $branch[0] -> branch_id . '/' . $branch[0] -> firm_logo; ?>" style="max-width: 150px !important;max-height: 50px !important;" />
                        <?php } else { ?>
                        <b style="font-size: 14px; text-transform: uppercase;">  <?= $branch[0]->firm_name ?></b>
                        <?php } ?>
                </td>
                <td valign="top" width="25%">
                    Invoice Date<br>
                    <?php $date = $data[0] -> expense_bill_date;
					$date_for = date('d-m-Y', strtotime($date));
                    ?>
                    <p style="font-weight:bold"><?php
					if (isset($data[0] -> expense_bill_date)) {
						echo $date_for;
					}
                        ?></p>
                </td>
                <td valign="top" width="25%">
                    Invoice Number<br>
                    <p style="font-weight:bold"><?php
					if (isset($data[0] -> expense_bill_invoice_number)) {
						echo $data[0] -> expense_bill_invoice_number;
					}
                        ?></p>
                </td>
            </tr>
        </table>
        <table width="100%" cellspacing="0" border-collapse="collapse" class="table header_table" style="margin-top:20px">
            <tr>
                <td width="50%" valign="top">
                    <b style="font-size: 12px;">From,</b><br><br>
                    <b style="font-size: 11px; text-transform: capitalize;">
                        <?php
                        if (isset($data[0]->supplier_name) && $data[0]->supplier_name != "") {
                            echo $data[0]->supplier_name;
                        }
                        ?> </b>
                    <br/>
                    <?php
                    if (isset($data[0]->supplier_gstin_number) && $data[0]->supplier_gstin_number != "") {
                        ?>
                        <div class="col-sm-12">
                            <label class="col-sm-2">GSTIN </label>
                            <label class="col-sm-1">:</label>
                            <div class="col-sm-9" >
                                <?php echo "<br>" . $data[0]->supplier_gstin_number; ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <br/>
                    <?php
                    if (isset($data[0]->supplier_address)) {
                        echo str_replace(array(
                            "\r\n",
                            "\\r\\n",
                            "\n",
                            "\\n"), "<br>", $data[0]->supplier_address);
                        ?>
                        <br>
                        <?php
                    }

                    if (isset($data[0]->supplier_city_name) && $data[0]->supplier_city_name != "") {
                        ?>
                        <div class="col-sm-12">
                            <label class="col-sm-2">Location </label>
                            <label class="col-sm-1">:</label>
                            <div class="col-sm-9" >
                                <?php
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
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <br/>
                    <?php
                    if (isset($data[0]->supplier_mobile) && $data[0]->supplier_mobile != "") {
                        ?>
                        <div class="col-sm-12">
                            <label class="col-sm-2">Mobile </label>
                            <label class="col-sm-1">:</label>
                            <div class="col-sm-9" >
                                <?= $data[0]->supplier_mobile ?>
                            </div>
                        </div>
                    <?php } ?>
                    <br>
                </td>
                <td width="50%" valign="top">
                    <b style="font-size: 12px;">To,</b><br><br>
                    <b style="font-size: 11px; text-transform: capitalize;">
                        <?php
                        if (isset($branch[0]->firm_name) && $branch[0]->firm_name != "") {
                            ?>
                            <?= $branch[0]->firm_name ?>
                            <?php
                        }
                        ?></b>
                    <?php
                    if (isset($branch[0]->branch_gstin_number) && $branch[0]->branch_gstin_number != "") {
                        echo "<br/>GSTIN:";
                        echo $branch[0]->branch_gstin_number;
                    }
                    ?><br>
                        <?php
                        if (isset($branch[0]->branch_address) && $branch[0]->branch_address != "") {
                            echo str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "<br>", $branch[0]->branch_address);
                        }
                        ?>
                        <?php
                        if (isset($branch[0]->branch_city_name) && $branch[0]->branch_city_name != "") {
                            echo "<br>" . $branch[0]->branch_city_name;
                        }
                        ?> <?php
                        if (isset($data[0]->supplier_postal_code)) {
                            if ($data[0]->supplier_postal_code != "") {
                                echo "- " . $data[0]->supplier_postal_code;
                            }
                        }
                        ?>
                        <?php
                        if (isset($branch[0]->branch_state_name) && $branch[0]->branch_state_name != "") {
                            echo "<br>" . $branch[0]->branch_state_name;
                        }
                        ?>,
                    <?php
                    if (isset($branch[0]->branch_country_name) && $branch[0]->branch_country_name != "") {
                        echo $branch[0]->branch_country_name;
                    }
                    ?>
                    <?php
                    if (isset($branch[0]->branch_mobile) && $branch[0]->branch_mobile != "") {
                        echo " <br>Mobile No: " . $branch[0]->branch_mobile;
                    }
                    ?>
                    <?php
                    if (isset($branch[0]->branch_email_address) && $branch[0]->branch_email_address != "") {
                        echo '<br>E-mail : ' . $branch[0]->branch_email_address;
                    }
                    ?>
                    <?php
                    if (isset($data[0]->supplier_state_code) && $data[0]->supplier_state_code != 0) {
                        echo '<br>State Code : ' . $data[0]->supplier_state_code;
                    }
                    ?>
                </td>
            </tr>
        </table>
       <table class="item-table table mt-20">
            <thead>
                <?php
                if ($igst_tax > 1 || $cgst_tax > 1 || $sgst_tax > 1 || $tdscount >= 1) {
                    ?>
                    <tr>
                        <th class="span2" width="12%" rowspan="2">Expense Type</th>
                        <?php
                        if ($dpcount >= 1) {
                            ?>
                            <th class="span2" width="14%" rowspan="2">Item Description</th>
                        <?php } ?>
                        <th class="span2" width="10%" rowspan="2">Amt</th>
                        <?php
                        if ($tdscount >= 1) {
                            ?>
                            <th class="span2" width="22%" colspan="2">TDS</th>
                            <th class="span2" width="10%" rowspan="2">Net Amt</th>
                        <?php } ?>
                        <?php
                        if ($igst_tax < 1 && $cgst_tax > 1) {
                            ?>
                            <th colspan="2">CGST</th>
                            <th colspan="2">SGST</th>
                            <?php } else if ($igst_tax > 1 && ($cgst_tax < 1 || $sgst_tax < 1)) { ?>
                            <th colspan="2" width="20%">IGST</th>
                            <?php } else { ?>
                            <th colspan="2" width="22%">IGST</th>
                            <th colspan="2" width="22%">CGST</th>
                            <th colspan="2" width="22%">SGST</th>
                        <?php } ?>
                        <th class="span2" width="10%" rowspan="2">Grand Total</th>
                    </tr>
                    <tr>
                        <?php
                        if ($tdscount >= 1) {
                            ?>
                            <th>
                                Rate %
                            </th>
                            <th>
                                Amt
                            </th>
                        <?php } ?>
                        <?php
                        if ($igst_tax < 1 && $cgst_tax > 1) {
                            ?>
                            <th >
                                Rate %
                            </th>
                            <th >
                                Amt
                            </th>
                            <th >
                                Rate %
                            </th>
                            <th >
                                Amt
                            </th>
                            <?php } else if ($igst_tax > 1 && $cgst_tax < 1) { ?>
                            <th >
                                Rate %
                            </th>
                            <th >
                                Amt
                            </th>
                            <?php } else if ($igst_tax > 1 && ($cgst_tax > 1 || $sgst_tax > 1)) { ?>
                            <th >
                                Rate %
                            </th>
                            <th >
                                Amt
                            </th>
                            <th >
                                Rate %
                            </th>
                            <th >
                                Amt
                            </th>
                            <th >
                                Rate %
                            </th>
                            <th >
                                Amt
                            </th>
                            <?php } else { ?>
                            <th>
                                Rate %
                            </th>
                            <th>
                                Amt
                            </th>
                        <?php } ?>
                    </tr>
                    <?php } else { ?>
                    <tr>
                        <th >Expense Type</th>  
                        <th>Amt</th>
                        <th>Total</th>
                    </tr>
                <?php } ?>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($items as $value) {
                    ?>
                    <tr>
                        <td><?php echo $value -> expense_title; ?></td>
                        <?php if ($dpcount >= 1) { ?>
                            <td><?php echo $value -> expense_bill_item_description; ?></td>
                        <?php } ?>
                        <td align="right"><?php echo $value -> expense_bill_item_sub_total; ?></td>
                        <?php
                        if ($tdscount >= 1) {
                            ?>
                            <td align="right"><?php echo $value -> expense_bill_item_tds_percentage; ?></td>
                            <td><?php echo $value -> expense_bill_item_tds_amount; ?></td>
                            <td align="right"><?php echo $value -> expense_bill_item_net_amount; ?></td>
                        <?php } ?>
                        <?php
                        if ($igst_tax < 1 && $cgst_tax > 1) {
                            ?>
                            <td align="center"><?php echo $value -> expense_bill_item_cgst_percentage; ?></td>
                            <td align="right"><?php echo $value -> expense_bill_item_cgst_amount; ?></td>
                            <td align="center"><?php echo $value -> expense_bill_item_sgst_percentage; ?></td>
                            <td align="right"><?php echo $value -> expense_bill_item_sgst_amount; ?></td>
                            <?php } else if ($igst_tax > 1 && $cgst_tax < 1) { ?>
                            <td align="center"><?php echo $value -> expense_bill_item_igst_percentage; ?></td>
                            <td align="right"><?php echo $value -> expense_bill_item_igst_amount; ?></td>
                            <?php } else if ($igst_tax > 1 && $cgst_tax > 1) { ?>
                            <td align="center"><?php echo $value -> expense_bill_item_igst_percentage; ?></td>
                            <td align="right"><?php echo $value -> expense_bill_item_igst_amount; ?></td>
                            <td align="center"><?php echo $value -> expense_bill_item_cgst_percentage; ?></td>
                            <td align="right"><?php echo $value -> expense_bill_item_cgst_amount; ?></td>
                            <td align="center"><?php echo $value -> expense_bill_item_sgst_percentage; ?></td>
                            <td align="right"><?php echo $value -> expense_bill_item_sgst_amount; ?></td>
                        <?php } ?>                                     
                     
                        <td align="right"><?php echo $value->expense_bill_item_grand_total ?></td>
                    </tr>
                    <?php } ?>
            </tbody>
        </table>
        <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">
            <tr>
                <td align="right" class="footerpad fontH" width="80%" style="border-right: solid 1px #FFF;"><b>Grand Total (<?php echo $currency_code; ?>)</td>
                <td align="right" class="footerpad fontH"><b><?php echo $data[0] -> expense_bill_grand_total; ?> /-</td>
            </tr>
            <tr>
                <td style="" align="left" colspan="2" height="25">
                    Amount (in Words): <b><?php echo $currency_text . " " . $this -> numbertowords -> convert_number($data[0] -> expense_bill_grand_total) . " Only"; ?></b><br/>
                </td>
            </tr>
        </table>
        <!--        <table width="100%" cellspacing="0" border-collapse="collapse" class="table" style="margin-top: 30px;">
              <tr>
                <td style="" valign="top" align="Right" height="65px">
                   <b>Authorised Signature: </b><b><br/><br/></b>
               </td>
           </tr>
        </table> -->
        <?php $notes_sub_module = 0;
		if ($notes_sub_module_id > 0) {
			$this -> load -> view('note_template/display_note');
		}
        ?>
        <br/>
        <?php
        if (isset($access_common_settings[0]->invoice_footer)) {
            ?>
            <table>
                <tr>
                    <td colspan="14" style="border:0px;text-align: center;font-size: 13px;"><?php echo $access_common_settings[0] -> invoice_footer; ?></td>
                </tr>
            </table>
            <?php
			}
        ?>
    </body>
</html>
<script>window.print();</script>
