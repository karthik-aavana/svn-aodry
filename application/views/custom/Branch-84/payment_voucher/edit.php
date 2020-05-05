<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');?>

<style type="text/css">
    .editable_disable {pointer-events: none;opacity: 0.8;}
    .row_count .btn-group a,.add_supplier_row_button{
        font-size: 20px;
        padding: 5px;
        border-radius: 50% !important;
        min-width: 35px;
        height: 35px;
        font-size: 15px;
        margin: 0px !important;
        -webkit-transition: width 2s;
        transition: width 2s;
        background-color: #012b72;
        color: #fff;
        margin: 5px !important;
    }
    .row_count .btn-group a i{
        line-height: 26px;
        font-size: 15px;
        display: block;
        text-align: center;
    }
    .disabled{opacity: 0.7; pointer-events: none;}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
  </section>
  <div class="fixed-breadcrumb">
    <ol class="breadcrumb abs-ol">
      <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
      <li><a href="<?php echo base_url('payment_voucher'); ?>">payment Voucher</a></li>
      <li class="active">Edit payment Voucher</li>
    </ol>
  </div>
  <!-- Main content -->
  <section class="content mt-50">
    <div class="row">
      <!-- right column -->
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Edit payment Voucher</h3>
            <a class="btn btn-default pull-right back_button" id="sale_cancel" onclick1="cancel('payment_voucher')">Back</a>
          </div>

          <!-- /.box-header -->
          <form role="form" id="form" method="post" action="<?php echo base_url('payment_voucher/edit_multi_payment'); ?>">
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="well">
                    <div class="row">
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="date">Voucher Date<span class="validation-color">*</span></label>
                          <div class="input-group date">
                            <input type="text" class="form-control datepicker" id="voucher_date" name="voucher_date" value="<?php echo date('d-m-Y', strtotime($payment_ids[0]->voucher_date)); ?>">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i>
                            </div>
                          </div>
                          <span class="validation-color" id="err_voucher_date"></span>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="reference_no">Voucher Number <span class="validation-color">*</span></label>
                          <input type="text" class="form-control" id="voucher_number" name="voucher_number" value="<?= $payment_ids[0]->voucher_number ?>" <?=($access_settings[0]->invoice_readonly == 'yes' ? "readonly" :'');
                          ?>>
                          <span class="validation-color" id="err_voucher_number"></span>
                        </div>
                      </div>
                    </div>
                    <?php
                    if(!empty($payment_ids)){
                      $i = $inv_row_count = 1;
                      $tbody = '';
                      $cust_ids = array();
                      $parent_payment_id = 0;
                      foreach ($payment_ids as $key => $value) { 
                        if($value->is_main_payment == '1') $parent_payment_id = $value->payment_id;
                        ?>
                        <div class="row row_count main_row_supplier" row="<?=$i;?>" id="ToClone" cust_id="<?=$value->party_id;?>">
                            <input type="hidden" class="form-control" id="payment_id" name="payment_id" value="<?= $value->payment_id ?>" >
                            <input type="hidden" name="payment_amount_old" value="<?=$value->receipt_amount;?>">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="supplier">supplier <span class="validation-color">*</span></label>
                                    
                                    <select class="form-control select2 supplier" id="supplier" name="supplier">
                                        <!-- <option value="">Select</option> -->
                                        <?php foreach ($supplier as $row) {  
                                          $selected = '';
                                          if($value->party_id == $row->supplier_id){ 
                                            array_push($cust_ids, $value->party_id);
                                            ?>
                                            <option value="<?= $row->supplier_id ?>" selected><?= $row->supplier_name.($row->supplier_mobile != '' ? '('.$row->supplier_mobile.')' : ''); ?></option>
                                         <?php } else { ?>
                                          <option value="<?= $row->supplier_id ?>"><?= $row->supplier_name.($row->supplier_mobile != '' ? '('.$row->supplier_mobile.')' : ''); ?></option>
                                         <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <span class="validation-color" id="err_supplier"></span>
                                </div>
                            </div>  
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="supplier">Actual payment amount<span class="validation-color">*</span></label>
                                    <input type="text" class="form-control number_only" id="total_payment_amount" name="total_payment_amount" value="<?=precise_amount($value->receipt_amount);?>">
                                    <span class="validation-color" id="err_total_payment_amount"></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" style="padding-top: 15px;">
                                    <div class="btn-group">
                                        <a type="buttion" href="javascript:void(0);" class="<?=($i == 1 ? 'add_supplier_row_button' : 'remove_row_button_supplier');?> pull-right"><i class="fa <?=($i == 1 ? 'fa-plus' : 'fa-minus');?>"></i></a>
                                        <a type="buttion" href="javascript:void(0);" class="invoice_edit pull-right"><i class="fa fa-pencil"></i></a>
                                    </div>
                                </div>
                            </div>                   
                        </div>
                      <?php $i++; }
                    }
                    ?>
                    <select id="common_select_supplier" style="display: none;">
                        <option value="">Select</option>
                        <?php foreach ($supplier as $row) { 
                            if($row->supplier_mobile != ''){  ?>
                            <option value="<?= $row->supplier_id ?>"><?= $row->supplier_name.'('.$row->supplier_mobile.')' ?></option>
                            <?php }else{ ?>
                            <option value="<?= $row->supplier_id ?>"><?= $row->supplier_name ?></option>
                        <?php }
                      } ?>
                    </select>
                    
                    <div id="add_row_field_supplier"></div>
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
                                      if (isset($bank_account) && !empty($bank_account)) {
                                          foreach ($bank_account as $key => $value){
                                            if($value->ledger_title != ''){ ?>
                                            <option value="<?php echo $value->bank_account_id . "/" . $value->ledger_title ?>" <?php if ($value->bank_account_id == $data[0]->payment_mode) echo "selected='selected'"; ?> ><?= $value->ledger_title ?></option>
                                            <?php  }
                                          }
                                      }
                              ?>
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
                              /*echo "<pre>";
                              print_r($data[0]);*/
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
              <input type="hidden" class="form-control" id="payment_id" name="payment_id" value="<?= $payment_id ?>" >
              <input type="hidden" name="parent_payment_id" value="<?=$parent_payment_id;?>">
              <input type="hidden" class="form-control" id="voucher_date_old" name="voucher_date_old" value="<?= $payment_ids[0]->voucher_date ?>" >
              <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" >
              <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" >
              <input type="hidden" class="form-control" id="voucher_number_old" name="voucher_number_old" value="<?= $payment_ids[0]->voucher_number ?>" >
              <input type="hidden" class="form-control" id="section_area" name="section_area" value="edit_payment_voucher" >
              <input type="hidden" class="form-control" id="reference_type" name="reference_type" value="<?= $payment_ids[0]->reference_type ?>" >
              <input type="hidden" name="invoice_data" id="invoice_data">
              <?php if(@$excess_data){ ?>
                <input type="hidden" name="excess_purchase_id" id="excess_purchase_id" value="<?=$excess_data['excess_purchase_id'];?>">
              <?php } ?>
              <div class="box-footer">
                <button type="submit" id="payment_submit" class="btn btn-info">Update</button>
                <span class="btn btn-default" id="payment_cancel" onclick="cancel('payment_voucher')">Cancel</span>
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
$this->load->view('payment_voucher/payment_popup_view');
$this->load->view('layout/footer');
?>
<script type="text/javascript">
  $(document).ready(function () {
    if ($('#payment_mode').val() == 'other payment mode'){
      $('#other_payment').show();
    }
  });
  var data_items = {};
  <?php if(@$purchase_data){?>
    data_items = '<?=json_encode($purchase_data);?>';
  <?php } ?>
  var row_count = '<?=$i;?>';
  var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
  var common_settings_amount_precision = "<?= $access_common_settings[0]->amount_precision ?>";
</script>
<script src="<?php echo base_url('assets/custom/branch-'.$this->session->userdata('SESS_BRANCH_ID').'/js/vouchers/') ?>payment.js"></script>
<script src="<?php echo base_url('assets/custom/branch-'.$this->session->userdata('SESS_BRANCH_ID').'/js/vouchers/') ?>payment_basic.js"></script>