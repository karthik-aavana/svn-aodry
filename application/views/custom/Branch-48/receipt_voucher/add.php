<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">    
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?php echo base_url('receipt_voucher'); ?>">Receipt Voucher</a></li>
            <li class="active">Add Receipt Voucher</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Receipt Voucher</h3>
                        <a class="btn btn-default pull-right back_button" id="sale_cancel" onclick1="cancel('receipt_voucher')">Back</a>
                    </div>
                    <form role="form" id="form" method="post" action="<?php echo base_url('receipt_voucher/add_receipt'); ?>">
                        <div class="box-body">   
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="date">Voucher Date<span class="validation-color">*</span></label>
                                        <?php
                                            $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));
                                            if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1])) {
                                                $date = '01-04-'.$financial_year[0];
                                            } else {
                                                $date = date('d-m-Y');
                                            }
                                            ?>
                                            <div class="input-group date">
                                              <input type="text" class="form-control datepicker" id="voucher_date" name="voucher_date" value="<?php echo $date; ?>">
                                              <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                              </div>
                                            </div>
                                        <!-- <input type="text" class="form-control" id="voucher_date" name="voucher_date" value="<?php echo $date; ?>"> -->
                                        <span class="validation-color" id="err_voucher_date"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="voucher No">Voucher Number <span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="voucher_number" name="voucher_number" value="<?= $voucher_number ?>"
                                               <?= ($access_settings[0]->invoice_readonly == 'yes' ? "readonly" : ''); ?> >
                                        <span class="validation-color" id="err_voucher_number"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="customer">Customer <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="customer" name="customer">
                                            <option value="">Select</option>
                                            <?php foreach ($customer as $row) { 
                                                if($row->customer_mobile != ''){
                                                ?>
                                                    <option value="<?= $row->customer_id ?>"><?= $row->customer_name.'('.$row->customer_mobile.')' ?></option>
                                                <?php
                                                }else{
                                                    ?>
                                                    <option value="<?= $row->customer_id ?>"><?= $row->customer_name ?></option>
                                                    <?php
                                                }
                                                ?>
                                            <?php } ?>
                                        </select>
                                        <span class="validation-color" id="err_customer"></span>
                                    </div>
                                </div>  
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="customer">Actual receipt amount<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control number_only" id="total_receipt_amount" name="total_receipt_amount" value="">
                                        <span class="validation-color" id="err_total_receipt_amount"></span>
                                    </div>
                                </div>                       
                            </div>
                            <select id="common_select" style="display: none;">
                            </select>
                            <div class="row row_count main_row" row="1" id="ToClone">
                                <div class="col-sm-2 reference_number_div" style="width: 21.5%">
                                    <div class="form-group">
                                        <label for="customer">Reference<br/>Number<span class="validation-color">*</span></label>
                                        <a type="buttion" href="#" class="add_row_button pull-right">(+)</a>
                                        <select class="reference_number form-control select2"  id="reference_number_1" name="reference_number">
                                            <option value="">Select</option>
                                        </select>
                                        <span>Invoice Amount: <strong class="total_invoice_amount">0.00</strong>/-</span>
                                        <span class="validation-color" id="err_reference_number_1"></span>
                                    </div>
                                </div>
                                <div class="col-sm-2 paid_amount_div">
                                    <div class="form-group">
                                        <label for="Total amount">Total Received Amount<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control number_only" id="paid_amount_1" name="paid_amount" value="" readonly>
                                        <input type="hidden" class="form-control" id="remaining_amount_1" name="remaining_amount" value="" >
                                        <!-- <span id="err_remaining_amount_1" class="remaining_class"></span> -->
                                    </div>
                                </div>
                                <div class="col-sm-2 receipt_amount_div">
                                    <div class="form-group">
                                        <label for="Receipt Amount">Receipt<br/> Amount <span class="validation-color">*</span></label>
                                        <input type="text" class="form-control number_only" id="receipt_amount_1" name="receipt_amount" value="">
                                        <span class="validation-color" id="err_receipt_amount_1"></span>
                                    </div>
                                </div>
                                <div class="col-sm-2 gain_loss_amount_div" style="display: none;">
                                    <div class="form-group">
                                        <label for="Invoice amount">Exchange Gain/loss</label>
                                        <div class="input-group">
                                            <input type="hidden" name="icon_gain_loss_amount" value="plus">
                                            <span class="input-group-addon toggle_plus" id="Input_addon_plus" name='Gain'>
                                                <i id="plus_button" class="fa fa-plus"></i>	
                                            </span>
                                            <input type="text" class="form-control number_only" name="gain_loss_amount" id="gain_loss_amount_1">
                                        </div>		
                                        <span id='gain_visible' class="toggle_lbl">Gain(+)</span>					
                                        <span class="validation-color" id="err_grand_total_1"></span>
                                    </div>
                                </div>
                                <div class="col-sm-2 discount_div">
                                    <div class="form-group">
                                        <label for="discount_1"><br/>Discount</label>
                                        <input type="text" class="form-control number_only" id="discount_1" name="discount" value="">
                                        <!-- <span class="validation-color" id="err_grand_total_1"></span> -->
                                    </div>
                                </div>
                                <div class="col-sm-2 other_charges_div" style="display: none;">
                                    <div class="form-group">
                                        <label for="other_charges_1"><br/>Other Charges</label>
                                        <input  type="text" class="form-control number_only" id="other_charges_1" name="other_charges" value="">
                                        <!-- <span class="validation-color" id="err_grand_total_1"></span> -->
                                    </div>
                                </div>
                                <div class="col-sm-2 round_off_div">
                                    <div class="form-group">
                                        <label for="Invoice amount"><br/>Round Off(+/-)</label>
                                        <div class="input-group">
                                            <input type="hidden"  name="icon_round_off" value="plus">
                                            <span class="input-group-addon toggle_plus" id="Input_addon_roundoff" name='Round Off'>
                                                <i id="roundoff_button" class="fa fa-plus"></i>	
                                            </span>
                                            <input type="text" class="form-control number_only" name="round_off" id="round_off_1" >
                                        </div>		
                                        <span id='roundoff_gain' class="toggle_lbl">Round Off(+)</span>	
                                        <span class="validation-color" id="err_grand_total_1"></span>
                                    </div>
                                </div>
                                <div class="col-sm-2 pending_amount_div">
                                    <div class="form-group">
                                        <label for="Invoice amount">Pending<br/>Amount<span class="validation-color">*</span></label>
                                        <input  type="text" class="float_number form-control" id="pending_amount_1" name="pending_amount" value="" readonly>
                                        <input  type="hidden" class="float_number form-control" id="invoice_total_1" name="invoice_total" value="">
                                        <span class="validation-color" id="err_grand_total_1"></span>
                                    </div>
                                </div>
                                <!--hidden -->
                                <input type="hidden" class="form-control" id="reference_number_text_1" name="reference_number_text" value="" >
                                <input type="hidden" class="form-control" id="current_paid_amount_1" name="current_paid_amount" value="" >
                                <input type="hidden" class="form-control" id="current_invoice_amount_1" name="current_invoice_amount" value="" >
                                <input type="hidden" class="form-control" id="old_receipt_amount_1" name="old_receipt_amount" value="" >
                                <input type="hidden" class="form-control" id="last_receipt_amount_1" name="last_receipt_amount" value="">                                
                            </div>                    
                            <div id="add_row_field">
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="paying_by">Payment Mode<span class="validation-color">*</span></label>
                                        <?php if (in_array($bank_account_module_id, $active_add)) { ?>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#bank_account_modal" class="pull-right">+</a>
                                                </div>
                                            <?php } ?>
                                            <select class="form-control select2" id="payment_mode" name="payment_mode">
                                                <option value="">Select</option>
                                                <option value="cash">Cash</option>
                                                <option value="other payment mode">Other payment mode</option>
                                                <!-- <option value="bank">Bank</option> -->
                                                <?php
                                                if (isset($bank_account) && !empty($bank_account)) {
                                                    foreach ($bank_account as $key => $value) {
                                                        if($value->ledger_title != ''){
                                                        ?>
                                                        <option value='<?php echo $value->bank_account_id . "/" . $value->ledger_title ?>'> <?php echo $value->ledger_title; ?> </option>
                                                        <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select></div>
                                        <span class="validation-color" id="err_payment_mode"></span>
                                    </div>
                                </div>
                                <div id="hide">
                                    <!-- <div class="col-sm-3">
                                      <div class="form-group">
                                        <label for="bank_name">Bank Name</label>
                                        <input type="text" class="form-control" id="bank_name" name="bank_name" value="">
                                        <span class="validation-color" id="err_bank_name"></span>
                                      </div>
                                    </div> -->
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="cheque_no">Cheque/Reference Number<span class="validation-color">*</span> </label>
                                            <input type="number" class="form-control" id="cheque_number" name="cheque_number" value="">
                                            <span class="validation-color" id="err_cheque_number"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="cheque_date">Cheque/Reference Date</label>
                                            <div class="input-group date">                            
                                                <input type="text" class="form-control datepicker" id="cheque_date" name="cheque_date" value="">
                                                <div class="input-group-addon">
                                                    <span class="fa fa-calendar"></span>
                                                </div>
                                            </div>
                                            <span class="validation-color" id="err_cheque_date"></span>
                                        </div>
                                    </div>
                                </div>
                                <div id="other_payment" style="display: none;">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="bank_name">Payment Via</label>
                                            <input type="text" class="form-control" id="payment_via" name="payment_via" value="">
                                            <span class="validation-color" id="err_payment_via"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="cheque_no">Reference Number</label>
                                            <input type="text" class="form-control" id="ref_number" name="ref_number" value="">
                                            <span class="validation-color" ></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="paying_by">Description</label>
                                        <textarea class="form-control" id="description" name="description" value=""></textarea>
                                    </div>
                                </div>
                            </div>
                            <!-- Hidden file Field -->
                            <input type="hidden" class="form-control" id="voucher_date_old" name="voucher_date_old" value="<?php echo $date; ?>" >
                            <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" >
                            <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" >
                            <input type="hidden" class="form-control" id="voucher_number_old" name="voucher_number_old" value="<?= $voucher_number ?>" >
                            <input type="hidden" class="form-control" id="section_area" name="section_area" value="receipt_voucher" >
                            <input type="hidden" class="form-control" id="reference_type" name="reference_type" value="sales" >
                            <input type="hidden" name="invoice_data" id="invoice_data">
                            <input type="hidden" name="excess_sales_id" id="excess_sales_id">
                            <!-- Hidden Field -->
                            <div class="box-footer">
                                <button type="submit" id="receipt_submit" class="btn btn-info">Receive</button>
                                <span class="btn btn-default" id="receipt_cancel" onclick="cancel('receipt_voucher')">Cancel</span>
                            </div>
                            <?php
                            $notes_sub_module = 0;
                            if (in_array($notes_sub_module_id, $access_sub_modules)) {
                                $notes_sub_module = 1;
                            }
                            if ($notes_sub_module == 1) {
                                $this->load->view('sub_modules/notes_sub_module');
                            }
                            ?>
                        </div>
                    </form>   
                </div>              
               </div>
           </div>     
    </section>        
</div>      
<?php $this->load->view('bank_account/bank_modal'); ?>
<?php $this->load->view('layout/footer'); ?>
<?php $i = 2; ?>
<script type="text/javascript">var row_count = '<?= $i; ?>';
var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
var common_settings_amount_precision = "<?= $access_common_settings[0]->amount_precision ?>";
</script>

<script src="<?php echo base_url('assets/js/vouchers/') ?>receipt_basic.js"></script>
<script src="<?php echo base_url('assets/custom/branch-'.$this->session->userdata('SESS_BRANCH_ID').'/js/vouchers/'); ?>receipt.js"></script>
