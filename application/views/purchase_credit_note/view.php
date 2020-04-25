<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
   <section class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="box">
               <div class="box-header with-border">
                  <h3 class="box-title">Purchase Debit note  View</h3>
                  <?php
$purchase_debit_note_id = $this->encryption_url->encode($data[0]->purchase_debit_note_id);
?>
             
                  <a class="btn btn-sm btn-default pull-right back_button" id="cancel" onclick1="cancel('purchase_debit_note')">Back</a>
               </div>
               <div class="box-body">
                  <div class="row">
                     <div class="col-xs-12">
                        <h2 class="page-header">
                           <i class="fa fa-building-o"></i> <?= $branch[0]->firm_name ?>
                           <small class="pull-right"> <?php
$date   = $data[0]->purchase_debit_note_date;
$c_date = date('d-m-Y', strtotime($date));
echo "Invoice Date : " . $c_date;
?></small>
                        </h2>
                     </div>
                     <!-- /.col -->
                  </div>
                  <!-- info row -->
                  <div class="row invoice-info">
                     <div class="col-sm-4">
                        From
                        <address>
                           <?php if (isset($data[0]->supplier_name) && $data[0]->supplier_name != "") { ?>
                               <strong><?= $data[0]->supplier_name ?></strong>
                           <?php } ?>
                           <br/>
                           <?php if (isset($data[0]->supplier_address) && $data[0]->supplier_address != "") { ?>
                               Address:
                               <?php
                               echo str_replace(array(
                                   "\r\n",
                                   "\\r\\n",
                                   "\n",
                                   "\\n"
                                       ), "<br>", $data[0]->supplier_address);
                           }
                           ?>
                           <br>
                           Mobile: <?php if (isset($data[0]->supplier_mobile) && $data[0]->supplier_mobile != "") { ?>
                               <?= $data[0]->supplier_mobile ?>
                           <?php } ?>
                           <br>
                           Email: <?php if (isset($data[0]->supplier_email) && $data[0]->supplier_email != "") { ?>
                               <?= $data[0]->supplier_email ?>
                               <br>
                               <?php
                           }
                           if (isset($data[0]->supplier_gstin_number) && $data[0]->supplier_gstin_number != "") {
                               ?>
                               GSTIN: <?= $data[0]->supplier_gstin_number ?>
                           <?php } ?>
                        </address>

                        To
                        <address>
                           <strong><?= $branch[0]->firm_name ?></strong>
                           <?php if (isset($branch[0]->firm_name) && $branch[0]->firm_name != "") { ?>
                           <?php } ?>
                           <br/>
                           <?php if (isset($branch[0]->branch_address) && $branch[0]->branch_address != "") { ?>
                               Address : <?php
                               echo str_replace(array(
                                   "\r\n",
                                   "\\r\\n",
                                   "\n",
                                   "\\n"
                                       ), "<br>", $branch[0]->branch_address);
                               ?>
                           <?php } ?>
                           <br/>
                           Location :
                           <?php
                           if (isset($branch[0]->branch_city_name) && $branch[0]->branch_city_name != "") {

                               if (isset($branch[0]->branch_city_name) && $branch[0]->branch_city_name != "") {
                                   echo $branch[0]->branch_city_name . ",";
                               }
                               if (isset($branch[0]->branch_state_name) && $branch[0]->branch_state_name != "") {
                                   echo $branch[0]->branch_state_name . ",";
                               }
                               if (isset($branch[0]->branch_country_name) && $branch[0]->branch_country_name != "") {
                                   echo $branch[0]->branch_country_name;
                               }
                           }
                           ?>
                           <br/>
                           Mobile: <?php if (isset($branch[0]->branch_mobile) && $branch[0]->branch_mobile != "") { ?>
                               <?= $branch[0]->branch_mobile ?>
                           <?php } ?>
                           <br>
                           Email: <?php if (isset($branch[0]->branch_email_address) && $branch[0]->branch_email_address != "") { ?>
                               <?= $branch[0]->branch_email_address ?>
                               <?php
                           }
                           if (isset($branch[0]->branch_gstin_number) && $branch[0]->branch_gstin_number != "") {
                               ?>
                               <br>
                               GSTIN: <?= $branch[0]->branch_gstin_number ?>
                           <?php } ?>
                        </address>
                     </div>
                     <!-- /.col -->
                     
                     <div class="col-sm-4"></div>
                     <div class="col-sm-4">
                        <div class="bg-light-table">
                              <strong>Invoice Details</strong><br>
                              Invoice Number: <?php
                              echo $data[0]->purchase_debit_note_invoice_number;
                              ?><br>
                              <?php
                              $date = $data[0]->purchase_debit_note_date;
                              $c_date = date('d-m-Y', strtotime($date));
                              echo "Date : " . $c_date;
                              ?>
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
                              <span class="bold"><?php
                                  if (isset($data[0]->purchase_debit_note_gst_payable)) {
                                      echo ucfirst($data[0]->purchase_debit_note_gst_payable);
                                  }
                                  ?></span>
                        </div>
                     </div>
                     <!-- /.col -->
                  </div>
                  <!-- /.row -->
                  <div class="row">
                     <div class="col-sm-12">
                        <table width="100%" class="table table-hover table-bordered table-responsive">
                           <thead>
                              <tr>
                                <th width="4%">#</th>
                                <th width="14%">Description</th>
                                <th width="6%">Quantity</th>
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
                                    <th>SGST</th>
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
                              $i           = 1;
                              $quantity    = 0;
                              $price       = 0;
                              $grand_total = 0;
                              foreach ($items as $value)
                               {
                              ?>
                              <tr>
                                <td ><?php echo $i; ?></td>
                                <?php
                                if ($value->item_type == 'product' || $value->item_type == 'product_inventory') {
                                    ?>
                                    <td style="text-align: left;">
                                        <?php echo $value->product_name; ?>
                                        <br>HSN/SAC: <?php echo $value->product_hsn_sac_code; ?>
                                        <?php
                                        if (isset($value->purchase_debit_note_item_description) && $value->purchase_debit_note_item_description != "") {
                                            echo "<br/>";
                                            echo $value->purchase_debit_note_item_description;
                                        }
                                        ?>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td style="text-align: left;"><?php echo $value->service_name; ?><br>HSN/SAC: <?php echo $value->service_hsn_sac_code; ?>
                                        <?php
                                        if (isset($value->purchase_debit_note_item_description) && $value->purchase_debit_note_item_description != "") {
                                            echo "<br/>";
                                            echo $value->purchase_debit_note_item_description;
                                        }
                                        ?>
                                    </td>
                                <?php } ?>
                                <td style="text-align: center;"><?php
                                    echo $value->purchase_debit_note_item_quantity;
                                    if ($value->product_unit != '') {
                                        $unit = explode("-", $value->product_unit);
                                        if($unit[0] != '') echo " <br>(" . $unit[0].')';
                                    }
                                    ?></td>
                                <td style="text-align: right;"><?php echo number_format($value->purchase_debit_note_item_unit_price, 2); ?></td>
                                <td style="text-align: right;"><?php echo number_format($value->purchase_debit_note_item_sub_total, 2); ?></td>
                                <?php
                                if ($discount_exist > 0) {
                                    ?>
                                    <td style="text-align: right;"><?php echo number_format($value->purchase_debit_note_item_discount_amount, 2); ?></td>
                                <?php } ?>
                                <?php
                                if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                                    ?>
                                    <td style="text-align: right;"><?php echo number_format($value->purchase_debit_note_item_taxable_value, 2); ?></td>
                                <?php } ?>
                                <?php
                                if ($tds_exist > 0) {
                                    ?>
                                    <td style="text-align: right;">
                                        <?php if($value->purchase_debit_note_item_tds_amount < 1  || $value->item_type == 'service'){ echo '-'; }else{ ?>
                                        <?php echo number_format($value->purchase_debit_note_item_tds_amount, 2); ?><br>(<?php echo round($value->purchase_debit_note_item_tds_percentage,2); ?>%)
                                        <?php } ?>
                                    </td>
                                <?php }
                                ?>
                                <?php
                                if ($tax_exist > 0) {
                                    ?>
                                    <td style="text-align: right;">
                                        <?php if($value->purchase_debit_note_item_tax_amount < 1){ echo '-'; }else{ ?>
                                        <?php echo number_format($value->purchase_debit_note_item_tax_amount, 2); ?><br>(<?php echo round($value->purchase_debit_note_item_tax_percentage,2); ?>%)
                                        <?php } ?>
                                    </td>
                                    <?php
                                } elseif ($igst_exist > 0) {
                                    ?>
                                    <td style="text-align: right;">
                                        <?php if($value->purchase_debit_note_item_igst_amount < 1){ echo '-'; }else{ ?>
                                        <?php echo number_format($value->purchase_debit_note_item_igst_amount, 2); ?><br>(<?php echo round($value->purchase_debit_note_item_igst_percentage,2); ?>%)
                                        <?php } ?>
                                    </td>
                                    <?php
                                } elseif ($cgst_exist > 0 || $sgst_exist > 0) {
                                    ?>
                                    <td style="text-align: right;">
                                        <?php if($value->purchase_debit_note_item_cgst_amount < 1){ echo '-'; }else{ ?>
                                        <?php echo number_format($value->purchase_debit_note_item_cgst_amount, 2); ?><br>(<?php echo round($value->purchase_debit_note_item_cgst_percentage,2); ?>%)
                                        <?php } ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <?php if($value->purchase_debit_note_item_sgst_amount < 1){ echo '-'; }else{ ?>
                                        <?php echo number_format($value->purchase_debit_note_item_sgst_amount, 2); ?><br>(<?php echo round($value->purchase_debit_note_item_sgst_percentage,2); ?>%)
                                        <?php } ?>
                                    </td>
                                <?php }
                                ?>
                                 <?php
                               if ($cess_exist > 0) {
                                    ?>
                                    <td style="text-align: right;">
                                        <?php if($value->purchase_debit_note_item_tax_cess_amount < 1){ echo '-'; }else{ ?>
                                        <?php echo number_format($value->purchase_debit_note_item_tax_cess_amount, 2); ?><br>(<?php echo round($value->purchase_debit_note_item_tax_cess_percentage,2); ?>%)
                                        <?php } ?>
                                    </td>
                                    <?php
                                }
                                    ?>
                                <td style="text-align: right;"><?php echo number_format($value->purchase_debit_note_item_grand_total, 2); ?></td>
                              </tr>
                              <?php
                                 $i++;
                                 $quantity = bcadd($quantity, $value->purchase_debit_note_item_quantity);
                                 $price    = bcadd($price, $value->purchase_debit_note_item_unit_price);
                                 
                                 $grand_total = bcadd($grand_total, $value->purchase_debit_note_item_grand_total);
                               }
                              ?>
                              <tr>
                                <th colspan="2"></th>
                                <th  ><!--<?= round($quantity) ?>--></th>
                                <th  ><?= number_format($price, 2) ?></th>
                                <th  style="text-align: right;"><?= number_format($data[0]->purchase_debit_note_sub_total, 2) ?></th>
                                <?php
                                if ($discount_exist > 0) {
                                    ?>
                                    <th  style="text-align: right;"><?= number_format($data[0]->purchase_debit_note_discount_amount, 2) ?></th>
                                <?php } ?>
                                <?php
                                if ($discount_exist > 0 && ($tax_exist > 0 || $igst_exist > 0 || $cgst_exist > 0 || $sgst_exist > 0 )) {
                                    ?>
                                    <th  style="text-align: right;"><?= number_format($data[0]->purchase_debit_note_taxable_value, 2) ?></th>
                                <?php } ?>
                                <?php
                                if ($tds_exist > 0) {
                                    ?>
                                    <th style="text-align: right;"><?= number_format($data[0]->purchase_debit_note_tcs_amount, 2); ?></th>
                                <?php }
                                ?>
                                <?php
                                if ($tax_exist > 0) {
                                    ?>
                                    <th style="text-align: right;"><?= number_format($data[0]->purchase_debit_note_tax_amount, 2) ?></th>
                                    <?php
                                } elseif ($igst_exist > 0) {
                                    ?>
                                    <th style="text-align: right;"><?= number_format($data[0]->purchase_debit_note_igst_amount, 2) ?></th>
                                    <?php
                                } elseif ($cgst_exist > 0 || $sgst_exist > 0) {
                                    ?>
                                    <th style="text-align: right;"><?= number_format($data[0]->purchase_debit_note_cgst_amount, 2) ?></th>
                                    <th style="text-align: right;"><?= number_format($data[0]->purchase_debit_note_sgst_amount, 2) ?></th>
                                <?php }
                                ?>
                                <?php
                               if ($cess_exist > 0) {
                                    ?>
                                    <th style="text-align: right;"><?= number_format($data[0]->purchase_debit_note_tax_cess_amount, 2) ?></th>
                                    <?php
                                } 
                                    ?>
                                <th style="text-align: right;"><?= bcadd($data[0]->purchase_debit_note_grand_total, $data[0]->round_off_amount, 2); ?></th>
                              </tr>
                           </tbody>
                        </table>
                        <table  id="table-total" class="table table-hover table-bordered table-responsive">
                           <tbody>
                              <tr>
                                 <td ><b>Total Value (<?php
echo $currency_symbol;
?>)</b></td>
                                 <td><?php
echo precise_amount($data[0]->purchase_debit_note_sub_total);
?></td>
                              </tr>
                              <?php
if ($discount_exist > 0)
 {
?>
                              <tr>
                                 <td>Discount (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: green;">( - ) <?php
   echo precise_amount($data[0]->purchase_debit_note_discount_amount);
?></td>
                              </tr>
                              <?php
 }
?>

                              <?php
if ($tds_exist > 0)
 {
?>
                                 <!-- <?php
if ($data[0]->purchase_debit_note_tds_amount > 0)
 {
?>  
                              <tr>
                                 <td>TDS (<?php
   echo $currency_code;
?>)</td>
                                 <td style="color: black;"><?php
   echo precise_amount($data[0]->purchase_debit_note_tds_amount);
?></td>
                              </tr>
                             <?php
 }
?> -->
                                 <?php
if ($data[0]->purchase_debit_note_tcs_amount > 0)
 {
?>                           
                              <tr>
                                 <td>TCS (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: red;">( + ) <?php
   echo precise_amount($data[0]->purchase_debit_note_tcs_amount);
?></td>
                              </tr>
                             <?php
 }
?>
                              <?php
 }
?>
                              <?php
if ($igst_exist > 0)
 {
?>
                              <tr>
                                 <td>IGST (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: red;">( + ) <?php
   echo precise_amount($data[0]->purchase_debit_note_igst_amount);
?></td>
                              </tr>
                              <?php
 }
elseif ($cgst_exist > 0 || $sgst_exist > 0)
 {
?>
                              <tr>
                                 <td>CGST (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: red;">( + ) <?php
   echo precise_amount($data[0]->purchase_debit_note_cgst_amount);
   ;
?></td>
                              </tr>
                              <tr>
                                 <td><?=($is_utgst == '1' ? 'UTGST' : 'SGST');?> (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: red;">( + ) <?php
   echo precise_amount($data[0]->purchase_debit_note_sgst_amount);
   ;
?></td>
                              </tr>
                              <?php
 }
elseif ($tax_exist > 0)
 {
?>
                              <tr>
                                 <td>TAX (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: red;">( + ) <?php
   echo precise_amount($data[0]->purchase_debit_note_tax_amount);
   ;
?></td>
                              </tr>
                              <?php
 }
?>
                              <?php if ($cess_exist > 0) { ?>
                                 <tr>
                                    <td>Cess (<?=$currency_symbol;?>)</td>
                                    <td style="color: red;">( + ) <?= precise_amount($data[0]->purchase_debit_note_tax_cess_amount);?></td>
                                 </tr>
                              <?php } ?>
                              <?php
if ($data[0]->total_freight_charge > 0)
 {
?>
                              <tr>
                                 <td>Freight Charge (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: red;">( + ) <?php
   echo precise_amount($data[0]->total_freight_charge);
?></td>
                              </tr>
                              <?php
 }
if ($data[0]->total_insurance_charge > 0)
 {
?>
                              <tr>
                                 <td>Insurance Charge (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: red;">( + ) <?php
   echo precise_amount($data[0]->total_insurance_charge);
?></td>
                              </tr>
                              <?php
 }
if ($data[0]->total_packing_charge > 0)
 {
?>
                              <tr>
                                 <td>Packing & Forwarding Charge (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: red;">( + ) <?php
   echo precise_amount($data[0]->total_packing_charge);
?></td>
                              </tr>
                              <?php
 }
if ($data[0]->total_incidental_charge > 0)
 {
?>
                              <tr>
                                 <td>Incidental Charge (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: red;">( + ) <?php
   echo precise_amount($data[0]->total_incidental_charge);
?></td>
                              </tr>
                              <?php
 }
if ($data[0]->total_inclusion_other_charge > 0)
 {
?>
                              <tr>
                                 <td>Other Inclusive Charge (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: red;">( + ) <?php
   echo precise_amount($data[0]->total_inclusion_other_charge);
?></td>
                              </tr>
                              <?php
 }
if ($data[0]->total_exclusion_other_charge > 0)
 {
?>
                              <tr>
                                 <td>Other Exclusive Charge (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: green;">( - ) <?php
   echo precise_amount($data[0]->total_exclusion_other_charge);
?></td>
                              </tr>
                              <?php
 }
?>
                              <?php
if ($data[0]->round_off_amount > 0)
 {
?>
                              <tr>
                                 <td>Round Off (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: green;">( - ) <?php
   echo precise_amount($data[0]->round_off_amount);
?></td>
                              </tr>
                              <?php
 }
?>
                              <?php
if ($data[0]->round_off_amount < 0)
 {
?>
                              <tr>
                                 <td>Round Off (<?php
   echo $currency_symbol;
?>)</td>
                                 <td style="color: red;">( + ) <?php
   echo precise_amount($data[0]->round_off_amount * -1);
?></td>
                              </tr>
                              <?php
 }
?>
                              <tr>
                                 <td><b>Grand Total (<?php
echo $currency_symbol;
?>)</b></td>
                                 <td style="color: blue;">( = ) <?php
echo precise_amount($data[0]->purchase_debit_note_grand_total);
?></td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="col-sm-12">
                        <div class="buttons">
                           <div class="btn-group btn-group-justified">
                              <?php
if (in_array($purchase_debit_note_module_id, $active_view))
 {
?>
                              <div class="btn-group">
                                 <a class="tip btn btn-success" href="<?php echo base_url('purchase_debit_note/pdf/'); ?><?php echo $purchase_debit_note_id; ?>" title="Download as PDF" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                    <span class="hidden-sm hidden-xs">PDF</span>
                                                </a>
                              </div>
                              <?php
 }
 if (in_array($purchase_debit_note_module_id, $active_edit))
 {
?>
<div class="btn-group">
   <a class="tip btn btn-success" href="<?php echo base_url('purchase_debit_note/edit/'); ?><?php echo $purchase_debit_note_id; ?>" title="Edit" target="_blank">
      <i class="fa fa-pencil"></i>
      <span class="hidden-sm hidden-xs">Edit</span>
   </a>
</div>
<?php } ?>
<!-- <?php
if (in_array($email_module_id, $active_view))
 {
   if (in_array($email_sub_module_id, $access_sub_modules))
    {
?>
                              <div class="btn-group">
                                 <a class="tip btn btn-warning tip" href="<?php
      echo base_url('purchase_debit_note/email/');
?><?php
      echo $purchase_debit_note_id;
?>" title="Send Mail">
                                 <i class="fa fa-envelope-o"></i>
                                 <span class="hidden-sm hidden-xs">Email</span>
                                 </a>
                              </div>
                              <?php
    }
 }
?> -->


                              <?php
if (in_array($purchase_debit_note_module_id, $active_delete))
 {
?>
                              <div class="btn-group">
                                 <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="<?php
   echo $purchase_debit_note_id;
?>" data-path="purchase_debit_note/delete" class="delete_button tip btn btn-danger bpo" href="#" title="Delete purchase">
                                 <i class="fa fa-trash-o"></i>
                                 <span class="hidden-sm hidden-xs">Delete</span>
                                 </a>
                              </div>
                              <?php
 }
?>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('general/delete_modal');
?>