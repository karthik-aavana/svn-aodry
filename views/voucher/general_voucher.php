<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url(''); ?>">Voucher</a></li>
            <li class="active">Add Voucher</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Voucher</h3>
                        <a class="btn btn-sm btn-default pull-right" href="<?php echo base_url(''); ?>">Back </a>
                    </div>
                    <form name="form" id="form" method="post" action="#">
                        <div class="box-body">                        
                            <div class="row">       
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="TransactionPurpose">Transaction Purpose<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="transaction_purpose">
                                            <option value="">Select Transaction Purpose</option>
                                            <option value="1">Advance Taken from Customer</option>
                                            <option value="2">Advance Given to Vendor/Suppliers</option>
                                            <option value="3">Advance Tax  to Govt</option>
                                            <option value="4">Advance Refund to Customer</option>
                                            <option value="5">Advance Repaid by Vendor</option>
                                            <option value="3">Advance Tax Refund by Govt</option>
                                            <option value="7">Amount Transferred between Banks</option>
                                            <option value="8">Capital Invested from Self</option>
                                            <option value="9">Capital Invested from Partner</option>
                                            <option value="10">Capital Invested from shareholder</option>
                                            <option value="8">Additional Capital Invested from Self</option>
                                            <option value="9">Additional Capital Invested from Partner</option>
                                            <option value="10">Additional Capital Invested from shareholder	</option>
                                            <option value="8">Capital withdrawn by self</option>
                                            <option value="9">Capital withdrawn by partner</option>
                                            <option value="10">Capital withdrawn by shareholder</option>
                                            <option value="17">Cash deposited in bank</option>
                                            <option value="18">Cash withdrawal from bank</option>
                                            <option value="19">Cash Receipt/Received from Customer</option>
                                            <option value="20">Cash Receipt/Received from Vendor/Suppliers</option>
                                            <option value="21">Cash Receipt/Received from others</option>
                                            <option value="22">Cash Paid/Payment to Customer</option>
                                            <option value="23">Cash Paid/Payment to Vendor/Suppliers</option>
                                            <option value="24">Cash Paid/Payment to others</option>
                                            <option value="25">Amount Deposited for FD</option>
                                            <option value="26">Amount Deposited for RD</option>
                                            <option value="27">Amount Deposited for RENT</option>
                                            <option value="28">Amount Deposited for ELECTRICITY</option>
                                            <option value="29">Amount Deposited for WATER</option>
                                            <option value="30">Amount Deposited TO XXXX (other)</option>
                                            <option value="31">Deposit Amount Withdrawal from FD</option>
                                            <option value="32">Deposit Amount Withdrawal from RD</option>
                                            <option value="33">Deposit Amount Withdrawal from RENT</option>
                                            <option value="34">Deposit Amount Withdrawal from ELECTRICITY</option>
                                            <option value="35">Deposit Amount Withdrawal from WATER</option>
                                            <option value="36">Deposit Amount Withdrawal from (other)</option>
                                            <option value="37">Tax received from Income Tax</option>
                                            <option value="38">Tax received from CGST</option>
                                            <option value="39">Tax received from SGST</option>
                                            <option value="40">Tax received from IGST</option>
                                            <option value="41">Tax received from VAT</option>
                                            <option value="42">Tax received from CESS</option>
                                            <option value="43">Tax paid to INCOME TAX</option>
                                            <option value="44">Tax paid to CGST</option>
                                            <option value="45">Tax paid to SGST</option>
                                            <option value="46">Tax paid to IGST</option>
                                            <option value="47">Tax paid to VAT</option>
                                            <option value="48">Tax paid to CESS</option>
                                            <option value="49">Purchase of Fixed Asset</option>
                                            <option value="50">Sales/Disposal of Fixed Asset</option>
                                            <option value="51">Income earned</option>
                                            <option value="52">Investments</option>
                                            <option value="53">Investments (Withdraw/Sold/Redeem/Mature)</option>
                                            <option value="54">Loan borrowed from bank</option>
                                            <option value="55">Loan borrowed from Firm	</option>
                                            <option value="56">Loan borrowed from Individuals</option>
                                            <option value="57">Loan Repaid to Bank</option>
                                            <option value="58">Loan Repaid to Firm</option>
                                            <option value="59">Loan Repaid to Individuals</option>
                                            <option value="60">Loan Installment/EMI paid to bank</option>
                                            <option value="61">Loan Installment/EMI paid to Firm</option>
                                            <option value="62">Loan Installment/EMI paid to Individual</option>
                                        </select>
                                        <span class="validation-color" id="err_type"></span>
                                    </div>
                                </div>
                                <div class="col-md-3 trans_pur_hide" data-id="1">
                                    <div class="form-group">
                                        <label for="customer">Customer<span class="validation-color">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#customer_modal" data-reference_type="sales" class="open_customer_modal pull-right">+</a>
                                            </div>
                                            <select class="form-control select2"  id="customer" name="customer" style="width: 100%;">
                                                <option value="">Select Customer</option>                                               
                                            </select>                                           
                                        </div>
                                        <span class="validation-color" id="err_customer"><?php echo form_error('customer'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3 trans_pur_hide" data-id="2">
                                    <div class="form-group">
                                        <label>Supplier<span class="validation-color">*</span></label>
                                        <div class="input-group">  
                                            <div class="input-group-addon">
                                                <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#add_vendor" data-reference_type="sales" class="open_customer_modal pull-right">+</a>
                                            </div>
                                            <select class="form-control select2" name="" id="">
                                                <option value="">Select Suppliers Address</option>
                                            </select>       
                                        </div>
                                        <span class="validation-color" id="err_shipping_address"><?php echo form_error('shipping_address'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3 trans_pur_hide" data-id="3">
                                    <div class="form-group">
                                        <label for="">Advance Tax</label>
                                        <input type="text" class="form-control" id="ref_number" name="ref_number" value="Advance Tax" readonly="">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div> 
                                <div class="col-md-3 trans_pur_hide" data-id="5">
                                    <div class="form-group">
                                        <label>Vendor Address<span class="validation-color">*</span></label>
                                        <div class="input-group">  
                                            <div class="input-group-addon">
                                                <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#customer_modal" data-reference_type="sales" class="open_customer_modal pull-right">+</a>
                                            </div>
                                            <select class="form-control select2" name="" id="">
                                                <option value="">Select Vendor Address</option>
                                            </select>       
                                        </div>
                                        <span class="validation-color" id="err_shipping_address"><?php echo form_error('shipping_address'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3 trans_pur_hide" data-id="7">
                                    <div class="form-group">
                                        <label>Deposited Bank<span class="validation-color">*</span></label>
                                        <div class="input-group">  
                                            <div class="input-group-addon">
                                                <a href="#" data-target="#customer_modal" data-reference_type="sales" class="open_customer_modal pull-right">+</a>
                                            </div>
                                            <select class="form-control select2" name="" id="">
                                                <option value="">Select Deposited Bank</option>
                                            </select>       
                                        </div>
                                        <span class="validation-color" id="err_shipping_address"><?php echo form_error('shipping_address'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3 trans_pur_hide" data-id="8">
                                    <div class="form-group">
                                        <label>Owner Name</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div>                                                              
                                <div class="col-md-3 trans_pur_hide" data-id="9">
                                    <div class="form-group">
                                        <label>Partner Name</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div> 
                                <div class="col-md-3 trans_pur_hide" data-id="10">
                                    <div class="form-group">
                                        <label>Shareholder Name</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div>
                                <div class="col-md-3 trans_pur_hide" data-id="17">
                                    <div class="form-group">
                                        <label>Deposit Bank Name</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div>
                                <div class="col-md-3 trans_pur_hide" data-id="17">
                                    <div class="form-group">
                                        <label>Account Number</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div>
                                <div class="col-md-3 trans_pur_hide" data-id="18">
                                    <div class="form-group">
                                        <label>Withdrawal Bank Name</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div>
                                <div class="col-md-3 trans_pur_hide" data-id="18">
                                    <div class="form-group">
                                        <label>Account Number</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div>                                
                                <div class="col-md-3 trans_pur_hide" data-id="19">
                                    <div class="form-group">
                                        <label>Customer Name</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div>                                
                                <div class="col-md-3 trans_pur_hide" data-id="20">
                                    <div class="form-group">
                                        <label>Vendor/Supplier Name</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div>         

                                <div class="col-md-3 trans_pur_hide" data-id="21">
                                    <div class="form-group">
                                        <label>Others</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div>

                                <div class="col-md-3 trans_pur_hide" data-id="22">
                                    <div class="form-group">
                                        <label>Customer Name</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div>                                
                                <div class="col-md-3 trans_pur_hide" data-id="23">
                                    <div class="form-group">
                                        <label>Vendor/Supplier Name</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div>         

                                <div class="col-md-3 trans_pur_hide" data-id="24">
                                    <div class="form-group">
                                        <label>Others</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div>

                                <div class="col-md-3 trans_pur_hide" data-id="30">
                                    <div class="form-group">
                                        <label>Particulars</label>
                                        <input type="text" class="form-control">
                                        <span class="validation-color" ></span>
                                    </div>
                                </div>
                                <div class="col-md-3 trans_pur_hide" data-id="36">
                                    <div class="form-group">
                                        <label>Vendor Address<span class="validation-color">*</span></label>
                                        <div class="input-group">  
                                            <div class="input-group-addon">
                                                <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#customer_modal" data-reference_type="sales" class="open_customer_modal pull-right">+</a>
                                            </div>
                                            <select class="form-control select2" name="" id="">
                                                <option value="">Select Vendor Address</option>
                                            </select>       
                                        </div>
                                        <span class="validation-color" id="err_shipping_address"><?php echo form_error('shipping_address'); ?></span>
                                    </div>
                                </div>                                
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Payment Mode<span class="validation-color">*</span></label>                                      
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <a href="" data-toggle="modal" data-target="#bank_account_modal" class="pull-right">+</a>
                                            </div>
                                            <select class="form-control select2" id="payment_mode" name="payment_mode">
                                                <option value="">Select Payment Mode</option>
                                                <option value="cash">Cash</option>
                                                <option value="other payment mode">Other payment mode</option>
                                            </select>
                                        </div>
                                        <span class="validation-color" id="err_payment_mode"></span>
                                    </div> 
                                </div>   
                                <div id="other_payment" style="display: none;">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="bank_name">Payment Via</label>
                                            <input type="text" class="form-control" id="payment_via" name="payment_via" value="">
                                            <span class="validation-color" id="err_payment_via"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cheque_no">Reference Number</label>
                                            <input type="text" class="form-control" id="ref_number" name="ref_number" value="">
                                            <span class="validation-color" ></span>
                                        </div>
                                    </div>
                                </div>            
                            </div>
                            <div class="row">                               
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="account_number">Amount<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="" name="" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">                                        
                                        <label for="account_number">Narration<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="" name="">
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" id="customer_submit" class="btn btn-info">
                                    Add</button>
                                <button class="btn btn-default" id="cancel" onclick="cancel('customer')"><!-- Cancel -->
                                    Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('bank_account/bank_modal');
$this->load->view('customer/customer_modal');
$this->load->view('supplier/add_vendor_modal');
?>
<script>
    $(document).on('change', '#payment_mode', function () {
        var pay_mode = $(this).val();
        if (pay_mode == "other payment mode") {
            $("#other_payment").show();
        } else {
            $("#other_payment").hide();
        }
    });
    $(".trans_pur_hide").hide();
    $(document).on('change', '#transaction_purpose', function () {
        var transaction_purpose = $(this).val();
        $(".trans_pur_hide").hide();
        $(".trans_pur_hide[data-id=" + transaction_purpose + "]").show();
    });
</script>