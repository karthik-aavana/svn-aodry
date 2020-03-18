<div class="modal fade" id="pay_now_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Payment Methods</h4>
            </div>
            <form role="form" id="sales_pay_form" method="post" action="<?php echo base_url('receipt/add'); ?>">
                <div class="modal-body" id="account_selector">
                    <input type="hidden" name="sales_id" id="pay_now_sales_id" >

                    <label class="checkbox"><input type="checkbox" name="payment_method_check" value="cash" checked="checked">Cash</label>
                    <label class="checkbox"><input type="checkbox" name="payment_method_check" value="bank">Bank</label>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="pay_button">Pay</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('input[name="payment_method_check"]').on('change', function () {
            $('input[name="payment_method_check"]').not(this).prop('checked', false);
        });
    });
    $(document).on("click", '.pay_now_button', function (e) {
        var id = $(this).data('id');
        if (id != "")
        {

            $("#pay_now_sales_id").val(id);
        } else
        {
            location.reload();
        }

    });
</script>
