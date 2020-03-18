<div class="modal fade" id="payment_type_modal" role="dialog">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title"> Payment To </h4>

            </div>

            <form role="form" id="payment_type_form" method="post" action="<?= base_url('payment_voucher/add') ?>">

                <div class="modal-body" >

                    <div class="row">

                        <div class="col-sm-12 ">

                            <div class="form-group">

                                <label class="radio"><input type="radio" class="minimal" name="payment_type_check" value="purchase" checked="checked">PURCHASE BILL </label>

                                <label class="radio"><input type="radio" class="minimal" name="payment_type_check" value="expense">EXPENSE BILL </label>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="payment_type_go">Go</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {

        $('input[name="payment_type_check"]').on('change', function () {

            $('input[name="payment_type_check"]').not(this).prop('checked', false);

        });

    });



</script>

