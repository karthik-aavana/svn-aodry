<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>
                    Dashboard</a>
            </li>
            <li><a href="<?php echo base_url('bank_account'); ?>">
                    Bank Account
                </a></li>
            <li class="active">
                Add Bank Account
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Add Bank Account
                        </h3>
                    </div>
                    <div class="box-body">                                            
                        <form name="form" id="form" method="post" action="<?php echo base_url('bank_account/add_bank_account'); ?>">
                            <div class="row">        
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bank_name1">Account Holder Name<span class="validation-color">*</span></label>
                                        <input type="hidden" name="selection_type" id="selection_type" value="">
                                        <input type="hidden" name="ledger_id" id="ledger_id" value="0">
                                        <input type="text" class="form-control" id="account_holder" name="account_holder" >
                                        <span class="validation-color" id="err_account_holder"><?php echo form_error('account_holder'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bank_name1">Bank Name<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="bank_name1" name="bank_name1" >
                                        <span class="validation-color" id="err_bank_name1"><?php echo form_error('bank_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="short_name">Reference Name<span class="validation-color">*</span></label>
                                        <input type="hidden" class="form-control" id="reference_status" name="reference_status" value="0">
                                        <input type="text" class="form-control" id="short_name" name="short_name" placeholder="Reference name for Bank">
                                        <span class="validation-color" id="err_short_name"><?php echo form_error('short_name'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">        
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="account_number">Account Number<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="account_number" name="account_number" value="<?php echo set_value('account_number'); ?>">
                                        <span class="validation-color" id="err_account_number"><?php echo form_error('account_number'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="type">Account Type<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="type" name="type" style="width: 100%;">
                                            <option value="">Select</option>
                                            <option value="Savings Account">Savings Account</option>
                                            <option value="Current Account">Current Account</option>
                                            <option value="OD Account">OD Account</option>
                                            <option value="Term Loan Account">Term Loan Account</option>
                                        </select>
                                        <span class="validation-color" id="err_type"><?php echo form_error('type'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group ">
                                        <input type="hidden" class="form-control" id="hidden_id" name="hidden_id" value="0">
                                        <label for="account_number">Ledger Title<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="ledger_title" name="ledger_title" readonly="">
                                    </div>
                                    <!-- <div class="form-group">
                                        <label for="balance">Opening Balance<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="balance" name="balance" value="<?php echo set_value('balance'); ?>">
                                        <span class="validation-color" id="err_balance"><?php echo form_error('balance'); ?></span>
                                    </div> -->
                                </div>
                            </div>
                            <div class="row">        
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="address">Bank Address</label>
                                        <input type="text" class="form-control" id="bank_address" name="bank_address" value="<?php echo set_value('address'); ?>">
                                        <span class="validation-color" id="err_address"><?php echo form_error('address'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="address">Bank Branch</label>
                                        <input type="text" class="form-control" id="branch_name" name="branch_name" value="<?php echo set_value('branch_name'); ?>">
                                        <span class="validation-color" id="err_branch_name"><?php echo form_error('branch_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="address">Bank IFSC</label>
                                        <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="<?php echo set_value('ifsc_code'); ?>">
                                        <span class="validation-color" id="err_ifsc_code"><?php echo form_error('ifsc_code'); ?></span>
                                    </div>
                                    <!-- <div class="form-group ">
                                        <label for="default">Default Account</label>
                                        <select class="form-control select2" id="default" name="default" style="width: 100%;">
                                            <option value="NO">NO</option>
                                            <option value="YES">YES</option>
                                        </select>
                                        <span class="validation-color" id="err_default"><?php echo form_error('default'); ?></span>
                                    </div> -->
                                </div>    
                            </div>                               
                            <div class="box-footer">
                                <button type="submit" id="add_bank_account" class="btn btn-info">                                                
                                    Add</button>
                                <span class="btn btn-default" id="cancel" onclick="cancel('bank_account')"><!-- Cancel -->
                                    Cancel</span>
                            </div>                                   
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
?>
<script>
    var state_url = '<?php echo base_url('common/getState/') ?>';
    var city_url = '<?php echo base_url('common/getCity/') ?>';
    var state_code_url = '<?php echo base_url('common/getStateCode/') ?>';
    var customer_add_url = '<?php echo base_url('customer/add_customer_ajax') ?>';
    var ledger_check_url = '<?php echo base_url('common/get_check_ledger/') ?>';
</script>
<script src="<?php echo base_url('assets/js/') ?>bank_account.js"></script>
