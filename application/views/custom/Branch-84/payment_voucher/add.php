<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<style type="text/css">
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
<div class="content-wrapper">    
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?php echo base_url('payment_voucher'); ?>">Payment Voucher</a></li>
            <li class="active">Add payment Voucher</li>
        </ol>
    </div>
    
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add payment Voucher</h3>
                        <a class="btn btn-default pull-right back_button" id="sale_cancel" onclick1="cancel('payment_voucher')">Back</a>
                    </div>
                    <form role="form" id="form" method="post" action="<?php echo base_url('payment_voucher/add_multi_supplier_payment'); ?>">
                        <div class="box-body">             
                            <div class="well">
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
                                                       
                                </div>
                                <select id="common_select_supplier" style="display: none;">
                                    <option value="">Select</option>
                                    <?php foreach ($supplier as $row) { 
                                        if($row->supplier_mobile != ''){
                                        ?>
                                        <option value="<?= $row->supplier_id ?>"><?= $row->supplier_name.'('.$row->supplier_mobile.')' ?></option>
                                        <?php 
                                        }else{
                                            ?>
                                            <option value="<?= $row->supplier_id ?>"><?= $row->supplier_name ?></option>
                                        
                                    <?php }} ?>
                                </select>
                                <div class="row row_count main_row_supplier" row="1" id="ToClone">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="supplier">Supplier <span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="supplier" name="supplier">
                                                <option value="">Select</option>
                                                <?php foreach ($supplier as $row) { 
                                                    if($row->supplier_mobile != ''){
                                                    ?>
                                                    <option value="<?= $row->supplier_id ?>"><?= $row->supplier_name.'('.$row->supplier_mobile.')' ?></option>
                                                    <?php 
                                                    }else{
                                                        ?>
                                                        <option value="<?= $row->supplier_id ?>"><?= $row->supplier_name ?></option>
                                                    
                                                <?php }} ?>
                                            </select>
                                            <span class="validation-color" id="err_supplier"></span>
                                        </div>
                                    </div>  
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="total_payment_amount">Actual Payment Amount<span class="validation-color">*</span></label>
                                            <input type="text" class="form-control number_only" id="total_payment_amount" name="total_payment_amount" value="">
                                            <span class="validation-color" id="err_total_payment_amount"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group" style="padding-top: 15px;">
                                            <div class="btn-group">
                                                <a type="buttion" href="javascript:void(0);" class="add_supplier_row_button pull-right disabled"><i class="fa fa-plus"></i></a>
                                                <a type="buttion" href="javascript:void(0);" class="invoice_edit pull-right disabled"><i class="fa fa-pencil"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>                    
                                <div id="add_row_field_supplier">
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
                                                <label for="cheque_no">Cheque/Reference Number</label>
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
                            </div>
                            <!-- Hidden file Field -->
                            <input type="hidden" class="form-control" id="voucher_date_old" name="voucher_date_old" value="<?php echo $date; ?>" >
                            <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" >
                            <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" >
                            <input type="hidden" class="form-control" id="voucher_number_old" name="voucher_number_old" value="<?= $voucher_number ?>" >
                            <input type="hidden" class="form-control" id="section_area" name="section_area" value="add_payment_voucher" >
                            <input type="hidden" class="form-control" id="reference_type" name="reference_type" value="<?= $payment_type_check ?>" >
                            <input type="hidden" name="invoice_data" id="invoice_data">
                            <input type="hidden" name="excess_sales_id" id="excess_sales_id">
                            <!-- Hidden Field -->
                            <div class="box-footer">
                                <button type="submit" id="payment_submit" class="btn btn-info">Pay</button>
                                <span class="btn btn-default" id="payment_cancel" onclick="cancel('payment_voucher')">Cancel</span>
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
                   </div>              
               </div>
            </form>          
        </div>        
    </section>        
</div>      
<?php $this->load->view('bank_account/bank_modal'); ?>
<?php $this->load->view('payment_voucher/payment_popup_view'); ?>
<?php $this->load->view('layout/footer'); ?>
<?php $i = 2; ?>
<script type="text/javascript">
    var row_count = '<?= $i; ?>';
    var inv_row_count = '<?= $i--; ?>';
var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
var common_settings_amount_precision = "<?= $access_common_settings[0]->amount_precision ?>";
</script>
<script src="<?php echo base_url('assets/custom/branch-'.$this->session->userdata('SESS_BRANCH_ID').'/js/vouchers/') ?>payment.js"></script>
<script src="<?php echo base_url('assets/custom/branch-'.$this->session->userdata('SESS_BRANCH_ID').'/js/vouchers/') ?>payment_basic.js"></script>
