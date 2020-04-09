<!--Subcategory Modal -->

<div id="edit_trans" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Transaction Purpose</h4>
            </div>
            <form name="form_trans" id="form_trans" method="post">
                <div class="modal-body">
                    <input type="hidden" id="trans_id" name="trans_id">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                               <label for="transaction">Transaction Purpose</label>
                                <select class="form-control select2" id="txt_transaction_e" name="txt_transaction_e">
                                    <option value="">Select Transaction Purpose</option>
                                    <option value="Advances">Advances</option>
                                    <option value="Capital Invested">Capital Invested</option>
                                  <option value="Cash transactions">Cash transactions</option>
                                    <option value="Deposits">Deposits</option>
                                    <option value="Tax receivables">Tax receivables</option>
                                     <option value="Tax payable">Tax payable</option>
                                     <option value="Fixed Assset">Fixed Assset</option>
                                     <option value="Interest">Interest</option>
                                     <option value="Investments">Investments</option>
                                     <option value="Loan Borrowed and repaid">Loan Borrowed and repaid</option>
                                     <option value="Capital Invested">Capital Invested</option>
                                </select>
                                <span class="validation-color" id="err_transaction_e"></span>
                            </div>
                        </div>                                                   
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                               <label for="transaction">Transaction Category</label>
                                <input type="text" class="form-control" id="txt_transaction_category_e" name="txt_transaction_category_e">
                                <span class="validation-color" id="err_transaction_category_e"></span>
                            </div>
                        </div>
                                                   
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="option">Customise Option</label>
                                <input type="text" class="form-control" id="txt_customise_option_e" name="txt_customise_option_e">
                                <span class="validation-color" id="err_customise_option_e"></span>
                            </div>                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="type_e">Transaction Type</label>
                                <select class="form-control select2" id="cmb_type_e" name="cmb_type_e">
                                    <option value="">Select Transaction Type</option>
                                    <option value="suppliers">suppliers</option>
                                    <option value="financial year">Financial Year</option>
                                    <option value="proprietor">Proprietor</option>
                                    <option value="partner">Partner</option>
                                    <option value="shareholder">Shareholder</option>
                                    <option value="bank loan">Bank Loan</option>
                                    <option value="cash">Cash</option>
                                    <option value="director loan">Director Loan</option>
                                    <option value="electricity">Electricity</option>
                                    <option value="fixed">Fixed</option>
                                    <option value="fixed asset">Fixed Asset</option>
                                    <option value="interest fixed">Interest Fixed</option>
                                    <option value="interest other">Interest Other</option>
                                    <option value="interest recurring">Interest Recurring</option>
                                    <option value="made">Made</option>
                                    <option value="mature">Mature</option>
                                    <option value="other">Other</option>
                                    <option value="others loan">Others Loan</option>
                                    <option value="recurring">Recurring</option>
                                    <option value="redeem">Redeem</option>
                                    <option value="rent">Rent</option>
                                    <option value="sold">Sold</option>
                                    <option value="tax paid">Tax Paid</option>
                                    <option value="tax refund">Tax Refund</option>
                                    <option value="water">Water</option>
                                    <option value="withdraw">Withdraw</option>
                                    <option value="bank">Bank</option>
                                    <option value="interest liability">Interest Liability</option>
                                    <option value="interest loan">Interest Loan</option>
                                </select>  
                                <span class="validation-color" id="err_cmb_type_e"></span>
                            </div>
                            
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="voucher_type_e">Voucher Type</label>
                                <select class="form-control select2" id="voucher_type_e" name="voucher_type_e">
                                    <option value="">Select Voucher Type</option>
                                    <option value="RECEIPTS">RECEIPTS</option>
                                    <option value="PAYMENT">PAYMENT</option>
                                    <option value="CONTRA A/C">CONTRA A/C</option>
                                    <option value="JOURNAL">JOURNAL</option>
                                </select>  
                                <span class="validation-color" id="err_voucher_type_e"></span>
                            </div>
                        </div> 
                    </div> 
                </div>                
                <div class="modal-footer">
                    <button type="button" id="edit_submit" class="btn btn-info">
                        Update
                    </button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).on("click", ".edit_trans", function() {
        var id = $(this).data('id');
        $.ajax({
            url : base_url + 'general_voucher/get_tansaction/' + id,
            dataType : 'JSON',
            method : 'POST',
            success : function(result) {
                $('#trans_id').val(result[0].id);
                $('#txt_transaction_e').val(result[0].transaction_purpose).change();
                $('#txt_transaction_category_e').val(result[0].transaction_category);
                var voucher_type =  result[0].voucher_type;
                var input_type =  result[0].input_type;
                $('#voucher_type_e').val(voucher_type).change(); 
                $('#cmb_type_e').val(input_type).change();
                $('#txt_customise_option_e').val(result[0].customise_option);
            }
        });
    });

    </script>

    








