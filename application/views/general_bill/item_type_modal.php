<div id="item_type_select_modal" class="modal fade" role="dialog" >
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Add Item</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form id="itemForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a href="" data-toggle="modal"  class="" data-dismiss="modal" id="first"></a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a href="" data-toggle="modal"  class="" data-dismiss="modal" id="second"></a>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="box-footer">
                <button type="close" id="item_modal_close" class="btn btn-info" data-dismiss="modal">Close</button>

            </div>

        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        $("#type_of_transaction").change(function (event)
        {
            var type_of_transaction = $('#type_of_transaction').val();
            var purpose_of_transaction = $('#purpose_of_transaction').val();
            if (type_of_transaction == "cash receipt" || type_of_transaction == "cash payment")
            {
                $('#first').attr('data-target', '#customer_modal');
                $('#first').text('+Add Customer');
                $('#second').attr('data-target', '#supplier_modal_general_bill');
                $('#second').text('+ Add Supplier');

            } else if (type_of_transaction == "Loan Borrowed" || type_of_transaction == "Loan Repaid To Lender" || type_of_transaction == "Instalment or EMI Paid To Lender" || type_of_transaction == "Loan Given" || type_of_transaction == "Loan Given" || type_of_transaction == "Loan Repaid By Borrower" || type_of_transaction == "Instalment or EMI Repaid By Borrower")
            {
                $('#first').attr('data-target', '#bank_account_modal');
                $('#first').text('+ Add Bank');
                $('#second').attr('data-target', '#add_director_org_modal');
                $('#second').text('+ Add Person/Organization');
            }
        });

    });

</script>
