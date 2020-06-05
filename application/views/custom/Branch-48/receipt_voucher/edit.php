<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');?>
<style type="text/css">
  .editable_disable {pointer-events: none;opacity: 0.8;}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
  </section>
  <div class="fixed-breadcrumb">
    <ol class="breadcrumb abs-ol">
      <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
      <li><a href="<?php echo base_url('receipt_voucher'); ?>">Receipt Voucher</a></li>
      <li class="active">Edit Receipt Voucher</li>
    </ol>
  </div>
  <!-- Main content -->
  <section class="content mt-50">
    <div class="row">
      <!-- right column -->
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Edit Receipt Voucher</h3>
            <a class="btn btn-default pull-right back_button" id="sale_cancel" onclick1="cancel('receipt_voucher')">Back</a>
          </div>
          <!-- /.box-header -->
          <form role="form" id="form" method="post" action="<?php echo base_url('receipt_voucher/edit_receipt'); ?>">
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="well">
                    <div class="row">
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="date">Voucher Date<span class="validation-color">*</span></label>
                          <div class="input-group date">
                            <input type="text" class="form-control datepicker" id="voucher_date" name="voucher_date" value="<?php echo date('d-m-Y',strtotime($data[0]->voucher_date)); ?>">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i>
                            </div>
                          </div>
                          <span class="validation-color" id="err_voucher_date"></span>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="reference_no">Voucher Number <span class="validation-color">*</span></label>
                          <input type="text" class="form-control" id="voucher_number" name="voucher_number" value="<?= $data[0]->voucher_number ?>" <?=($access_settings[0]->invoice_readonly == 'yes' ? "readonly" :'');
                          ?>>
                          <span class="validation-color" id="err_voucher_number"></span>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="customer">Customer <span class="validation-color">*</span></label>
                          <select class="form-control" id="customer" name="customer" style="width: 100%;" readonly>
                            <?php 
                            if($data[0]->customer_mobile != ''){
                            ?>
                              <option value="<?php echo $data[0]->party_id ?>"><?php echo $data[0]->customer_name.'('.$data[0]->customer_mobile.')' ?></option> 
                            <?php
                            }else{
                            ?>
                              <option value="<?php echo $data[0]->party_id ?>"><?php echo $data[0]->customer_name ?></option>
                              <?php
                            }
                            ?>
                          </select>
                          <span class="validation-color" id="err_customer"></span>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="customer">Actual receipt amount<span class="validation-color">*</span></label>
                          <input type="text" class="form-control number_only" id="total_receipt_amount" name="total_receipt_amount" value="<?php echo precise_amount($data[0]->receipt_amount); ?>">
                          <span class="validation-color" id="err_total_receipt_amount"></span>
                        </div>
                      </div>
                    </div>
                    <select id="common_select" style="display: none;">
                      <option value="">Select</option>
                      <?php if(!empty($sales_data)){
                        $data_items = array();
                        foreach ($sales_data as $key => $sales) { 
                            $data_items['sales_'.$sales->sales_id] = $sales; ?>
                            <option value="<?=$sales->sales_id;?>"><?=$sales->sales_invoice_number;?></option>
                        <?php } 
                      } ?>
                    </select>
                    <?php if(!empty($data)){
                      $i = 1;
                      foreach ($data as $key => $value) {  ?>
                        <div class="row row_count main_row <?=($value->is_edit == '0' ? 'editable_disable' : '');?>" row="<?=$i;?>" id="ToClone" >
                          <input type="hidden" name="is_edit" value="<?=$value->is_edit;?>">
                          <div class="col-sm-2 reference_number_div" style="width: 20%">
                            <div class="form-group">
                              <label for="customer">Reference<br/>Number<span class="validation-color">*</span></label>
                              <a type="buttion" href="#" class="<?=($i == 1 ? 'add_row_button' : 'remove_row_button');?> pull-right">(<?=($i == 1 ? '+' : '-');?>)</a>
                              <select class="reference_number form-control select2" id="reference_number_<?=$i;?>" name="reference_number">
                                <option value="">Select</option>
                                <?php if(!empty($sales_data)){
                                  $data_items = array();
                                  foreach ($sales_data as $key => $sales) { 
                                      $selected = '';
                                      $invoice_total = 0;
                                      if($sales->sales_id == $value->reference_id){
                                        $sales->sales_grand_total = $sales->customer_payable_amount;
                                        $sales->sales_paid_amount -= $value->invoice_receipt_amount;
                                        $selected = 'selected';
                                      } 
                                      $data_items['sales_'.$sales->sales_id] = $sales;
                                      ?>
                                      <option value="<?=$sales->sales_id;?>" <?=$selected;?>><?=$sales->sales_invoice_number;?></option>
                                  <?php } ?>
                                <?php } ?>
                              </select>
                              <span>Invoice Amount: <strong class="total_invoice_amount"><?=precise_amount($value->invoice_total);?></strong>/-</span>
                              <span class="validation-color" id="err_reference_number_<?=$i;?>"></span>
                            </div>
                          </div>
                          <div class="col-sm-2 paid_amount_div">
                            <div class="form-group">
                              <label for="Total amount">Total Received Amount<span class="validation-color">*</span></label>
                              <input type="text" class="form-control" id="paid_amount_<?=$i;?>" name="paid_amount" value="<?=precise_amount($value->invoice_paid_amount);?>" readonly>
                              <input type="hidden" class="form-control" id="remaining_amount_<?=$i;?>" name="remaining_amount" value="<?=$value->invoice_pending;?>" >
                              <!-- <span id="err_remaining_amount_<?=$i;?>" class="remaining_class"></span> -->
                            </div>
                          </div>
                          <div class="col-sm-2 receipt_amount_div">
                            <div class="form-group">
                              <label for="Receipt Amount">Receipt<br/>Amount <span class="validation-color">*</span></label>
                              <input type="text" class="form-control number_only" id="receipt_amount_<?=$i;?>" name="receipt_amount" value="<?=precise_amount($value->invoice_receipt_amount);?>">
                              
                              <span class="validation-color" id="err_receipt_amount_<?=$i;?>"></span>
                            </div>
                          </div>
                          <!-- <div class="col-sm-2 gain_loss_amount_div">
                            <div class="form-group">
                              <label for="Invoice amount">Exchange Gain/loss</label>
                              <div class="input-group">
                                <input type="hidden" name="icon_gain_loss_amount" value="<?=$value->exchange_gain_loss_type;?>">
                                <span class="input-group-addon toggle_plus" id="Input_addon_plus" name='Gain'>
                                <i id="plus_button" class="fa <?=($value->exchange_gain_loss_type == 'plus' ? 'fa-plus' : 'fa-minus')?>"></i> 
                                </span>
                                <input type="text" class="form-control number_only" name="gain_loss_amount" id="gain_loss_amount_<?=$i;?>" value="<?=($value->exchange_gain_loss != '' ? precise_amount($value->exchange_gain_loss) : '0.00');?>">
                              </div>    
                              <span id='gain_visible' class="toggle_lbl">Gain(<?=($value->exchange_gain_loss_type == 'plus' ? '+' : '-')?>)</span>         
                              <span class="validation-color" id="err_grand_total_<?=$i;?>"></span>
                            </div>
                          </div> -->
                          <div class="col-sm-2 discount_div">
                            <div class="form-group">
                              <label for="discount_<?=$i;?>"><br/>Discount</label>
                              <input type="text" class="form-control number_only" id="discount_<?=$i;?>" name="discount"  value="<?=($value->discount != '' ? precise_amount($value->discount) : '0.00');?>">
                              <!-- <span class="validation-color" id="err_grand_total_<?=$i;?>"></span> -->
                            </div>
                          </div>
                          <!-- <div class="col-sm-2 other_charges_div">
                            <div class="form-group">
                              <label for="other_charges_<?=$i;?>"><br/>Other Charges</label>
                              <input  type="text" class="form-control number_only" id="other_charges_<?=$i;?>" name="other_charges" value="<?=($value->other_charges != '' ? precise_amount($value->other_charges) : '0.00');?>">
                              <span class="validation-color" id="err_grand_total_<?=$i;?>"></span>
                            </div>
                          </div> -->
                          <div class="col-sm-2 round_off_div">
                            <div class="form-group">
                              <label for="Invoice amount"><br/>Round Off(+/-)</label>
                              <div class="input-group">
                                <input type="hidden"  name="icon_round_off" value="<?=($value->round_off_icon == 'plus' ? 'plus' : 'minus')?>">
                                <span class="input-group-addon toggle_plus" id="Input_addon_roundoff" name='Round Off'>
                                <i id="roundoff_button" class="fa <?=($value->round_off_icon == 'plus' ? 'fa-plus' : 'fa-minus')?>"></i> 
                                </span>
                                <input type="text" class="form-control number_only" name="round_off" id="round_off_<?=$i;?>" value="<?=($value->round_off != '' ? precise_amount($value->round_off) : '0.00');?>">
                              </div>    
                              <span id='roundoff_gain' class="toggle_lbl">Round Off(<?=($value->round_off_icon == 'plus' ? '+' : '-')?>)</span> 
                              <span class="validation-color" id="err_grand_total_<?=$i;?>"></span>
                            </div>
                          </div>
                          <div class="col-sm-2 pending_amount_div">
                            <div class="form-group">
                              <label for="Invoice amount">Pending<br/>Amount<span class="validation-color">*</span></label>
                              
                              <input  type="text" class="float_number form-control" id="pending_amount_<?=$i;?>" name="pending_amount" value="<?=($value->invoice_pending != '' ? precise_amount($value->invoice_pending) : '0.00');?>" readonly>
                             
                              <input  type="hidden" class="float_number form-control" id="invoice_total_<?=$i;?>" name="invoice_total" value="<?=($value->invoice_total != '' ? precise_amount($value->invoice_total) : '0.00');?>">
                              <span class="validation-color" id="err_grand_total_<?=$i;?>"></span>
                            </div>
                          </div>
                          <!--hidden -->
                          <input type="hidden" class="form-control" id="reference_number_text_<?=$i;?>" name="reference_number_text"  value="<?=($value->reference_number != '' ? precise_amount($value->reference_number) : '0.00');?>" >
                          <input type="hidden" class="form-control" id="current_paid_amount_<?=$i;?>" name="current_paid_amount"  value="<?=($value->invoice_receipt_amount != '' ? precise_amount($value->invoice_receipt_amount) : '0.00');?>" >
                          <input type="hidden" class="form-control" id="current_invoice_amount_<?=$i;?>" name="current_invoice_amount"  value="<?=($value->invoice_total != '' ? precise_amount($value->invoice_total) : '0.00');?>" >
                          <input type="hidden" class="form-control" id="old_receipt_amount_<?=$i;?>" name="old_receipt_amount"  value="<?=($value->invoice_receipt_amount != '' ? precise_amount($value->invoice_receipt_amount) : '0.00');?>" >
                          <input type="hidden" name="receipt_amount_old" value="<?=$data[0]->receipt_amount;?>">
                          <input type="hidden" name="converted_receipt_amount_old" value="<?=$data[0]->receipt_amount;?>">
                          <input type="hidden" class="form-control" id="last_receipt_amount_<?=$i;?>" name="last_receipt_amount"  value="<?=($value->invoice_receipt_amount != '' ? precise_amount($value->invoice_receipt_amount) : '0.00');?>">
                          <!-- hidden -->
                        </div>
                      <?php $i++; }
                      if(@$excess_data){?>
                          <div class="row row_count main_row <?=($excess_data['is_used'] == '1' ? 'editable_disable' : '' );?>" row="<?=$i;?>" id="ToClone">
                          <div class="col-sm-2 reference_number_div" style="width: 20%">
                            <div class="form-group">
                              <label for="customer">Reference<br/>Number<span class="validation-color">*</span></label>
                              <a type="buttion" href="#" class="<?=($i == 1 ? 'add_row_button' : 'remove_row_button');?> pull-right">(<?=($i == 1 ? '+' : '-');?>)</a>
                              <select class="reference_number form-control select2" id="reference_number_<?=$i;?>" name="reference_number">
                                <option value="excess_amount">Excess Amount</option>
                              </select>
                              <span class="validation-color" id="err_reference_number_<?=$i;?>"></span>
                            </div>
                          </div>
                          <div class="col-sm-2 receipt_amount_div">
                            <div class="form-group">
                              <label for="Receipt Amount">Receipt<br/>Amount <span class="validation-color">*</span></label>
                              <input type="text" class="form-control" id="receipt_amount_<?=$i;?>" name="receipt_amount" value="<?=precise_amount($excess_data['excess_amount']);?>">
                              
                              <span class="validation-color" id="err_receipt_amount_<?=$i;?>"></span>
                            </div>
                          </div>
                        </div>
                        <?php  $i++;
                      } 
                    }?>
                    <div id="add_row_field"></div>
                    <div class="row">
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="paying_by">Payment Mode<span class="validation-color">*</span></label>
                          <?php
                            if (in_array($bank_account_module_id , $active_add)){?>
                                <div class="input-group">
                                  <div class="input-group-addon">
                                    <a href="" data-toggle="modal" data-target="#bank_account_modal" class="pull-right">+</a>
                                  </div>
                            <?php } ?>
                            <select class="form-control select2" id="payment_mode" name="payment_mode">
                              <option value="">Select</option>
                              <option value="cash" <?php
                              if ($data[0]->payment_mode == "cash")
                              {
                                  echo "selected";
                              }
                              ?>>Cash</option>
                              <!-- <option value="bank" <?php
                              if ($data[0]->payment_mode == "bank")
                              {
                                  echo "selected";
                              }
                              ?>>Bank</option> -->
                              <option value="other payment mode" <?php
                              if ($data[0]->payment_mode == "other payment mode")
                              {
                                  echo "selected";
                              }
                              ?>>Other payment mode</option>
                                      <?php
                                      if (isset($bank_account) && !empty($bank_account)){
                                          foreach ($bank_account as $key => $value){
                                            if($value->ledger_title != ''){ ?>
                                              <option value="<?php echo $value->bank_account_id . "/" . $value->ledger_title ?>" <?php if ($value->bank_account_id == $data[0]->payment_mode) echo "selected='selected'"; ?> ><?= $value->ledger_title ?></option>
                                            <?php
                                            }
                                          }
                                      } ?>
                            </select></div>
                          <span class="validation-color" id="err_payment_mode"></span>
                        </div>
                      </div>
                      <div id="other_payment" <?=($data[0]->payment_mode == "other payment mode" ? '' : 'style="display: none;"');?>>
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label for="bank_name">Payment Via</label>
                            <input type="text" class="form-control" id="payment_via" name="payment_via" value="<?= $data[0]->payment_via ?>">
                            <span class="validation-color" id="err_payment_via"></span>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label for="cheque_no">Reference Number</label>
                            <input type="text" class="form-control" id="reff_number" name="reff_number" value="<?= $data[0]->reff_number ?>">
                            <span class="validation-color" id="err_cheque_number"></span>
                          </div>
                        </div>
                      </div>
                      <div id="hide" <?=($data[0]->payment_mode != "other payment mode" && $data[0]->payment_mode != "cash" ? 'style="display: block !important;"' : '');?>>
                        <!-- <div class="col-sm-3">
                          <div class="form-group">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?= $data[0]->bank_name ?>">
                            <span class="validation-color" id="err_bank_name"></span>
                          </div>
                        </div> -->
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label for="cheque_no">Reference Number</label>
                            <input type="text" class="form-control" id="cheque_number" name="cheque_number" value="<?= $data[0]->cheque_number ?>">
                            <span class="validation-color" id="err_cheque_number"></span>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-group">
                              <?php
                              $cheque_date = explode('-' , $data[0]->cheque_date);
                              if ($cheque_date[0] > 0)
                              {
                                  $cheque_date = date('d-m-Y', strtotime($data[0]->cheque_date));
                              }
                              else
                              {
                                  $cheque_date = "";
                              }
                              ?>
                            <label for="cheque_date">Cheque Date (If Applicable)</label>
                            <div class="input-group date">                            
                                <input type="text" class="form-control datepicker" id="cheque_date" name="cheque_date" value="<?= $cheque_date ?>">
                                <div class="input-group-addon">
                                    <span class="fa fa-calendar"></span>
                                </div>
                            </div>
                            <span class="validation-color" id="err_cheque_date"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12">
                        <div class="form-group">
                          <label for="paying_by">Description</label>
                          <textarea class="form-control" id="description" name="description" ><?=
                              str_replace(array(
                                  "\r\n" ,
                                  "\\r\\n" ,
                                  "\n" ,
                                  "\\n" ) , "&#10;" , $data[0]->description);
                              ?></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Hidden Field -->
              <input type="hidden" class="form-control" id="receipt_id" name="receipt_id" value="<?= $receipt_id ?>" >
              <input type="hidden" class="form-control" id="voucher_date_old" name="voucher_date_old" value="<?= $data[0]->voucher_date ?>" >
              <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" >
              <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" >
              <input type="hidden" class="form-control" id="voucher_number_old" name="voucher_number_old" value="<?= $data[0]->voucher_number ?>" >
              <input type="hidden" class="form-control" id="section_area" name="section_area" value="receipt_voucher" >
              <input type="hidden" class="form-control" id="reference_type" name="reference_type" value="<?= $data[0]->reference_type ?>" >
              <input type="hidden" name="invoice_data" id="invoice_data">
              <?php if(@$excess_data){ ?>
                <input type="hidden" name="excess_sales_id" id="excess_sales_id" value="<?=$excess_data['excess_sales_id'];?>">
              <?php } ?>
              <div class="box-footer">
                <button type="submit" id="receipt_submit" class="btn btn-info">Update</button>
                <span class="btn btn-default" id="receipt_cancel" onclick="cancel('receipt_voucher')">Cancel</span>
              </div>
              <?php
              $notes_sub_module = 0;
              if (in_array($notes_sub_module_id , $access_sub_modules))
              {
                  $notes_sub_module = 1;
              }
              if ($notes_sub_module == 1)
              {
                  $this->load->view('sub_modules/notes_sub_module');
              }
              ?>
            </div>
          </form>
        </div>
        <!-- /.box-body -->
      </div>
      <!--/.col (right) -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php
$this->load->view('bank_account/bank_modal');
$this->load->view('layout/footer');
?>
<script type="text/javascript">
  $(document).ready(function () {
    if ($('#payment_mode').val() == 'other payment mode'){
      $('#other_payment').show();
    }
  });
  var data_items = {};
  <?php if(@$data_items){?>
    data_items = '<?=stripslashes(json_encode($data_items));?>';
  <?php } ?>
  var row_count = '<?=$i;?>';
  var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
  var common_settings_amount_precision = "<?= $access_common_settings[0]->amount_precision ?>";
</script>
<script src="<?php echo base_url('assets/js/vouchers/') ?>receipt.js"></script>
<script src="<?php echo base_url('assets/js/vouchers/') ?>receipt_basic.js"></script>