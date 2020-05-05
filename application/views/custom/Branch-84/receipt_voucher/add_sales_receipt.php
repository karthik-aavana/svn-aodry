<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?php echo base_url('sales'); ?>">Sales</a></li>
            <li class="active">Add Receipt Voucher</li>
        </ol>

    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Receipt Voucher</h3>
                        <a class="btn btn-default pull-right back_button" id="sale_cancel" onclick1="cancel('sales')">Back</a>
                    </div>
                    <select id="common_select" style="display: none;">
                        <option value="">Select</option>
                        <?php
                        if (!empty($data)) {
                            $data_items = array();
                            foreach ($data as $key => $sales) {
                                $sales->sales_grand_total = $sales->customer_payable_amount;
                                $data_items['sales_' . $sales->sales_id] = $sales;
                                ?>
                                <option value="<?= $sales->sales_id; ?>"><?= $sales->sales_invoice_number; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>                    
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
                                        <span class="validation-color" id="err_voucher_date"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="reference_no">Voucher Number <span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="voucher_number" name="voucher_number" value="<?= $voucher_number ?>" <?php
                                        if ($access_settings[0]->invoice_readonly == 'yes') {
                                            echo "readonly";
                                        }
                                        ?>>
                                        <span class="validation-color" id="err_voucher_number"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="customer">Customer <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="customer" name="customer" style="width: 100%;">
                                            <?php
                                            foreach ($customer as $row) {
                                                if ($row->customer_id == $data[0]->sales_party_id) {
                                                    if($row->customer_mobile != ''){
                                                    ?>
                                                        <option value="<?= $row->customer_id ?>"><?= $row->customer_name.'('.$row->customer_mobile.')'?></option>
                                                    <?php
                                                    }else{
                                                    ?>
                                                        <option value="<?= $row->customer_id ?>"><?= $row->customer_name ?></option>
                                                    <?php
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="validation-color" id="err_customer"></span>
                                    </div>
                                </div>

                                <!-- <div class="col-sm-3">
                                  <div class="form-group">
                                    <label for="currency_id">Billing Currency <span class="validation-color">*</span></label>
                                    <select class="form-control select2" id="currency_id" name="currency_id">
                                <?php
                                foreach ($currency as $key => $value) {
                                    if ($value->currency_id == $data[0]->currency_id) {
                                        echo "<option value='" . $value->currency_id . "' selected>" . $value->currency_name . "</option>";
                                    }
                                }
                                ?>
                                    </select>
                                    <span class="validation-color" id="err_currency_id"></span>
                                  </div>
                                </div> -->
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="customer">Actual receipt amount<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="total_receipt_amount" name="total_receipt_amount" value="">
                                        <span class="validation-color" id="err_total_receipt_amount"></span>
                                    </div>
                                </div>    
                            </div>
                            <?php
                            $reference_id_arr = explode(",", $data[0]->sales_id);
                            $reference_number_arr = explode(",", $data[0]->sales_invoice_number);
                            $receipt_amount_arr = explode(",", "0");
                            $paid_amount_arr = explode(",", precise_amount($data[0]->sales_paid_amount));
                            /* $balance_amount_arr   = explode("," , precise_amount(($data[0]->sales_grand_total + $data[0]->credit_note_amount - $data[0]->debit_note_amount - $data[0]->sales_paid_amount))); */
                            $balance_amount_arr = explode(",", precise_amount(($data[0]->sales_grand_total - $data[0]->sales_paid_amount)));
                            $invoice_total_arr = explode(",", precise_amount(($data[0]->sales_grand_total + $data[0]->credit_note_amount - $data[0]->debit_note_amount)));
                            $reference_id_count = count($reference_id_arr);
                            ?>
                            <div class="row row_count main_row" row="1" id="ToClone">
                                <div class="col-sm-2 reference_number_div" style="width: 20%">
                                    <div class="form-group">
                                        <label for="customer">Reference<br/>Number<span class="validation-color">*</span></label>
                                        <a type="buttion" href="#" class="add_row_button pull-right">(+)</a>
                                        <select class="reference_number form-control select2"  id="reference_number_1" name="reference_number">
                                            <option value="<?= $reference_id_arr[0] ?>"><?= $reference_number_arr[0] ?></option>
                                        </select>
                                        <span>Invoice Amount: <strong class="total_invoice_amount"><?= precise_amount($data[0]->sales_grand_total) ?></strong>/-</span>
                                        <span class="validation-color" id="err_reference_number_1"></span>
                                    </div>
                                </div>
                                <div class="col-sm-2 paid_amount_div">
                                    <div class="form-group">
                                        <label for="Total amount">Total Received Amount<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="paid_amount_1" name="paid_amount" value="<?= ($paid_amount_arr[0]); ?>" readonly>
                                        <input type="hidden" class="form-control" id="remaining_amount_1" name="remaining_amount" value="<?= $balance_amount_arr[0]; ?>" >
                                        <!-- <span id="err_remaining_amount_1" class="remaining_class"></span> -->
                                    </div>
                                </div>
                                <div class="col-sm-2 receipt_amount_div">
                                    <div class="form-group">
                                        <label for="Receipt Amount">Receipt<br/>Amount <span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="receipt_amount_1" name="receipt_amount" value="<?= $receipt_amount_arr[0]; ?>">

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
                                            <input type="text" class="form-control" name="gain_loss_amount" id="gain_loss_amount_1">
                                        </div>    
                                        <span id='gain_visible' class="toggle_lbl">Gain(+)</span>         
                                        <span class="validation-color" id="err_grand_total_1"></span>
                                    </div>
                                </div>
                                <div class="col-sm-2 discount_div">
                                    <div class="form-group">
                                        <label for="discount_1"><br/>Discount</label>
                                        <input type="text" class="form-control" id="discount_1" name="discount" value="">
                                        <!-- <span class="validation-color" id="err_grand_total_1"></span> -->
                                    </div>
                                </div>
                                <div class="col-sm-2 other_charges_div" style="display: none;">
                                    <div class="form-group">
                                        <label for="other_charges_1"><br/>Other Charges</label>
                                        <input  type="text" class="form-control" id="other_charges_1" name="other_charges" value="">
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
                                            <input type="text" class="form-control" name="round_off" id="round_off_1" >
                                        </div>    
                                        <span id='roundoff_gain' class="toggle_lbl">Round Off(+)</span> 
                                        <span class="validation-color" id="err_grand_total_1"></span>
                                    </div>
                                </div>
                                <div class="col-sm-2 pending_amount_div">
                                    <div class="form-group">
                                        <label for="Invoice amount">Pending<br/>Amount<span class="validation-color">*</span></label>
                                        <input  type="text" class="float_number form-control" id="pending_amount_1" name="pending_amount" value="<?= $balance_amount_arr[0]; ?>" readonly>
                                        <input  type="hidden" class="float_number form-control" id="invoice_total_1" name="invoice_total" value="">
                                        <span class="validation-color" id="err_grand_total_1"></span>
                                    </div>
                                </div>
                                <!--hidden -->
                                <input type="hidden" class="form-control" id="reference_number_text_1" name="reference_number_text" value="<?= $reference_number_arr[0] ?>" >
                                <input type="hidden" class="form-control" id="current_paid_amount_1" name="current_paid_amount" value="<?= precise_amount($paid_amount_arr[0]) ?>" >
                                <input type="hidden" class="form-control" id="current_invoice_amount_1" name="current_invoice_amount" value="<?= precise_amount($invoice_total_arr[0]) ?>" >
                                <input type="hidden" class="form-control" id="old_receipt_amount_1" name="old_receipt_amount" value="<?= precise_amount($receipt_amount_arr[0]) ?>" >
                                <input type="hidden" class="form-control" id="last_receipt_amount_1" name="last_receipt_amount" value="<?= precise_amount($receipt_amount_arr[0]) ?>">
                                <!-- hidden -->
                            </div>   
                            <div id="add_row_field">
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="paying_by">Payment Mode<span class="validation-color">*</span></label>
                                        <?php
                                        if (in_array($bank_account_module_id, $active_add)) {
                                            ?>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#bank_account_modal" class="pull-right">+</a>
                                                </div>
                                            <?php } ?>
                                            <!-- <a href="" data-toggle="modal" data-target="#bank_account_modal" class="pull-right">+ Add Account</a> -->
                                            <select class="form-control select2" id="payment_mode" name="payment_mode">
                                                <option value="">Select</option>
                                                <option value="cash">Cash</option>
                                                <option value="other payment mode">Other payment mode</option>
                                                <!-- <option value="bank">Bank</option> -->
                                                <?php
                                                if (isset($bank_account) && !empty($bank_account)) {
                                                    foreach ($bank_account as $key => $value) {
                                                        ?>
                                                        <option value='<?php echo $value->bank_account_id . "/" . $value->ledger_title ?>'> <?php echo $value->ledger_title; ?> </option>

                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
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
                                            <label for="cheque_no">Reference Number</label>
                                            <input type="text" class="form-control" id="cheque_number" name="cheque_number" value="">
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
                                        <textarea class="form-control" id="description" name="description" ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Field -->
                            <input type="hidden" class="form-control" id="voucher_date_old" name="voucher_date_old" value="<?php echo $date; ?>" >
                            <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" >
                            <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" >
                            <input type="hidden" class="form-control" id="voucher_number_old" name="voucher_number_old" value="<?= $voucher_number ?>" >
                            <input type="hidden" class="form-control" id="section_area" name="section_area" value="sales" >
                            <input type="hidden" class="form-control" id="reference_type" name="reference_type" value="sales" >
                            <input type="hidden" name="invoice_data" id="invoice_data">
                            <input type="hidden" name="excess_sales_id" id="excess_sales_id">
                            <!-- Hidden Field -->
                            <div class="box-footer">
                                <button type="submit" id="receipt_submit" class="btn btn-info">Receive</button>
                                <span class="btn btn-default" id="receipt_cancel" onclick="cancel('sales')">Cancel</span>
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
<?php
$this->load->view('bank_account/bank_modal');
$this->load->view('layout/footer');
if (in_array($bank_account_module_id, $active_modules)) {
    if (in_array($bank_account_module_id, $active_add)) {
        $this->load->view('bank_account/bank_modal');
    }
}
?>
<script type="text/javascript">
    $(document).ready(function () {
        if ($('#payment_mode').val() == 'other payment mode') {
            $('#other_payment').show();
        }
    });
    var data_items = {};
    <?php if (@$data_items) { ?>
            data_items = '<?= json_encode($data_items); ?>';
    <?php } ?>
    var row_count = 2;
    var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
    var common_settings_amount_precision = "<?= $access_common_settings[0]->amount_precision ?>";
</script>
<script src="<?php echo base_url('assets/js/vouchers/') ?>receipt.js"></script>
<script src="<?php echo base_url('assets/js/vouchers/') ?>receipt_basic.js"></script>