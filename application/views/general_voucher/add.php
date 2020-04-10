<?phpdefined('BASEPATH') OR exit('No direct script access allowed');/* $type = user_limit();  $scount = supplier_count();  $limit = $type['coun'] + $scount['scount']; */$this->load->view('layout/header');?><style type="text/css">    .form-group{        min-height: 75px;    }</style><div class="content-wrapper">       <section class="content mt-50">        <div class="row">            <div class="col-md-12">                <div class="box">                    <div class="box-header with-border">                        <h3 class="box-title">Add General Voucher</h3>                        <a class="btn btn-default pull-right back_button" id="cancel" onclick1="cancel('')">Back</a>                    </div>                    <form role="form" id="form" method="post" action="<?php echo base_url('general_voucher/add_general_voucher'); ?>">                        <div class="box-body">                            <div class="row">                                <div class="col-sm-3">                                    <div class="form-group">                                        <label for="date">Voucher Date <span class="validation-color">*</span></label>                                        <?php                                        $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));                                        if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1])) {                                            $date = '01-04-' . $financial_year[0];                                        } else {                                            $date = date('d-m-Y');                                        }                                        ?>                                        <div class="input-group date">                                            <input type="text" class="form-control datepicker" id="voucher_date" name="voucher_date" value="<?php echo $date; ?>">                                            <div class="input-group-addon">                                                <i class="fa fa-calendar"></i>                                            </div>                                        </div>                                        <span class="validation-color" id="err_voucher_date"></span>                                    </div>                                </div>                                <div class="col-sm-3">                                    <div class="form-group">                                        <label for="trans_purpsose">Transaction Purpose <span class="validation-color">*</span></label>                                         <select class="form-control select2" autofocus="on" id="trans_purpose" name="trans_purpose" style="width: 100%;">                                            <option value="">Select</option>                                            <?php                                            foreach ($transaction_purpose as $value) {                                                echo "<option value='" . $value->id . "'>" . $value->purpose_option . "</option>";                                            }                                            ?>                                        </select>                                                                         <span class="validation-color" id="err_trans_purpsose"></span>                                    </div>                                </div>                                                           <div class="col-sm-3" id="div_receipt" style="display: none">                                    <div class="form-group">                                        <label for="amount" id="lbl_amount">Amount <span class="validation-color">*</span></label>                                        <input type="text" class="float_number form-control" id="receipt_amount" name="receipt_amount" value="">                                        <span class="validation-color" id="err_receipt_amount"><?php echo form_error('receipt_amount'); ?></span>                                    </div>                                </div>                                 <div class="col-sm-3" style="display: none">                                    <div class="form-group">                                        <label for="customer">Payee <span class="validation-color">*</span></label>                                              <div class="input-group">                                            <div class="input-group-addon">                                                <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#customer_modal" data-reference_type="general_voucher" class="open_customer_modal pull-right">+</a></div>                                            <input type="hidden" class="form-control" id="customer_module_id" name="customer_module_id" value="<?= $customer_module_id ?>" >                                            <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" >                                            <select class="form-control select2" autofocus="on" id="payee" name="payee" style="width: 100%;">                                                <option value="">Select</option>                                                      </select>                                        </div>                                        <span class="validation-color" id="err_customer"></span>                                    </div>                                </div>                                 <div class="col-sm-3" id="div_interest" style="display: none">                                    <div class="form-group">                                        <label for="Interest" id="lbl_interest">Interest </label>                                        <input type="text" class="float_number form-control" id="interest_amount" name="interest_amount" value="">                                        <span class="validation-color" id="err_interest_amount"><?php echo form_error('interest_amount'); ?></span>                                    </div>                                </div>                                <div class="col-sm-3" id="div_others" style="display: none">                                    <div class="form-group">                                        <label for="others" id="lbl_others">Others </label>                                        <input type="text" class="float_number form-control" id="others_amount" name="others_amount" value="">                                        <span class="validation-color" id="err_others_amount"><?php echo form_error('others'); ?></span>                                    </div>                                </div>                                <div class="col-sm-3" id="div_cgst" style="display: none">                                    <div class="form-group">                                        <label for="cgst">CGST </label>                                        <input type="text" class="float_number form-control" id="txt_cgst" name="txt_cgst" value="">                                        <span class="validation-color" id="err_txt_cgst"><?php echo form_error('cgst'); ?></span>                                    </div>                                </div>                                 <div class="col-sm-3" id="div_sgst" style="display: none">                                    <div class="form-group">                                        <label for="sgst">SGST </label>                                        <input type="text" class="float_number form-control" id="txt_sgst" name="txt_sgst" value="">                                        <span class="validation-color" id="err_txt_sgst"><?php echo form_error('sgst'); ?></span>                                    </div>                                </div>                                 <div class="col-sm-3" id="div_igst" style="display: none">                                    <div class="form-group">                                        <label for="igst">IGST </label>                                        <input type="text" class="float_number form-control" id="txt_igst" name="txt_igst" value="">                                        <span class="validation-color" id="err_txt_igst"><?php echo form_error('igst'); ?></span>                                    </div>                                </div>                                <div class="col-sm-3" id="div_utgst" style="display: none">                                    <div class="form-group">                                        <label for="utgst" >UTGST </label>                                        <input type="text" class="float_number form-control" id="txt_utgst" name="txt_utgst" value="">                                        <span class="validation-color" id="err_txt_utgst"><?php echo form_error('utgst'); ?></span>                                    </div>                                </div>                                 <div class="col-sm-3" id="div_cess" style="display: none">                                    <div class="form-group">                                        <label for="cess">Cess </label>                                        <input type="text" class="float_number form-control" id="txt_cess" name="txt_cess" value="">                                        <span class="validation-color" id="err_txt_cess"><?php echo form_error('cess'); ?></span>                                    </div>                                </div>                                <div class="col-sm-3" id="div_tds" style="display: none">                                    <div class="form-group">                                        <label for="tds">TDS </label>                                        <input type="text" class="float_number form-control" id="txt_tds" name="txt_tds" value="">                                        <span class="validation-color" id="err_txt_tds"><?php echo form_error('tds'); ?></span>                                    </div>                                </div>                                               <div class="col-sm-3" id="trans_mode" style="display: none;">                                    <div class="form-group">                                        <label for="paying_by">Mode of Payment <span class="validation-color">*</span></label>                                        <div class="input-group" >                                                <div class="input-group-addon" >                                                    <a href="" id="plus_mode" data-toggle="modal" data-target="#bank_account_modal" class="pull-right" >+</a>                                                </div>                                            <select class="form-control select2" id="payment_mode" name="payment_mode">                                            <option value="">Select</option>                                            <option value="cash">Cash</option>                                              <?php                                            foreach ($bank_account as $key => $value) {                                                ?>                                                <option value='<?php echo $value->bank_account_id . "/" . $value->ledger_title ?>'> <?php echo $value->ledger_title; ?> </option>                                                <?php                                            }                                            ?>                                        </select>                                    </div>                                        <span class="validation-color" id="err_payment_mode"></span>                                    </div>                                                                    </div>                                <div id="hide" style="display: none">                                                                   <div class="col-sm-3">                                        <div class="form-group">                                            <label for="cheque_no">Cheque/Reference Number</label>                                            <input type="text" class="form-control" id="cheque_number" name="cheque_number" value="">                                            <span class="validation-color" id="err_cheque_number"></span>                                        </div>                                    </div>                                    <div class="col-sm-3">                                                                      <div class="form-group">                                            <label for="cheque_date">Cheque/Reference Date</label>                                            <div class="input-group date">                                                <input type="text" class="form-control datepicker" id="cheque_date" name="cheque_date" value=""><div class="input-group-addon">                                                    <i class="fa fa-calendar"></i>                                                </div>                                            </div>                                            <span class="validation-color" id="err_cheque_date"></span>                                        </div>                                    </div>                                </div>                                <div class="col-sm-3" id="div_partner" style="display: none;">                                    <div class="form-group">                                        <label for="cheque_date" id="partner">Shareholder / Partner</label>                                        <select class="form-control select2" id="cmb_partner" name="cmb_partner">                                            <option value="">Select</option>                                        </select>                                        <span class="validation-color" id="err_partner"></span>                                    </div>                                </div>                                <div id="other_payment" style="display: none;">                                    <div class="col-sm-3">                                        <div class="form-group">                                            <label for="bank_name">Payment Via</label>                                            <input type="text" class="form-control" id="payment_via" name="payment_via" value="">                                            <span class="validation-color" id="err_payment_via"></span>                                        </div>                                    </div>                                    <div class="col-sm-3">                                        <div class="form-group">                                            <label for="bank_name">Reference Number</label>                                            <input type="text" class="form-control" id="ref_number" name="ref_number" value="">                                            <span class="validation-color" id="err_ref_number"></span>                                        </div>                                    </div>                                </div>                            </div>                            <input type="hidden" name="voucher_type" id="voucher_type">                            <input type="hidden" name="input_type" id="input_type">                            <input type="hidden" name="transaction_purpose" id="transaction_purpose">                            <input type="hidden" name="transaction_cat" id="transaction_cat">                            <div class="row">                                <div class="col-sm-12">                                    <div class="form-group">                                        <label for="paying_by">Comments</label>                                        <textarea type="text" class="form-control" id="description" name="description" value=""></textarea>                                    </div>                                </div>                            </div>                             <div class="box-footer">                                <button type="submit" id="general_submit" class="btn btn-info">Pay</button>                                <span class="btn btn-default" id="receipt_cancel" onclick="cancel('general_voucher/general_voucher_list')">Cancel</span>                            </div>                        </div>                    </form>                </div>            </div>        </div></div></section></div><?php$this->load->view('layout/footer');//$this->load->view('customer/customer_modal');//$this->load->view('category/category_modal');//$this->load->view('subcategory/subcategory_modal');?><?php $this->load->view('general_voucher/bank_modal'); ?><script type="text/javascript">    $(document).ready(function () {        $('.cust_type .select2').select2({            minimumResultsForSearch: -1        });           });    $('#hide').hide();    var sales_data = new Array();    var branch_state_list = <?php echo json_encode($state); ?>;    var item_gst = new Array();    var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";    var common_settings_amount_precision = "<?= $access_common_settings[0]->amount_precision ?>";    var settings_tax_percentage = "<?= $access_common_settings[0]->tax_split_percentage ?>";    var settings_tax_type = "<?= $access_settings[0]->tax_type ?>";    var settings_discount_visible = "<?= $access_settings[0]->discount_visible ?>";    var settings_description_visible = "<?= $access_settings[0]->description_visible ?>";    var settings_tds_visible = "<?= $access_settings[0]->tds_visible ?>";    var settings_item_editable = "<?= $access_settings[0]->item_editable ?>";</script><script src="<?php echo base_url('assets/js/vouchers/') ?>general_voucher.js"></script><script src="<?php echo base_url('assets/js/vouchers/') ?>general_voucher_submit.js"></script>