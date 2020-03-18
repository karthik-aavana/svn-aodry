<div class="modal fade" id="quantity_products" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Existing Stock</h4>
            </div>
            <!-- <form role="form" id="form" method="post" action="<?php echo base_url('receipt/pay_now'); ?>"> -->
            <div class="modal-body col-sm-12">

                <div class="form-group col-sm-6" id="Quantity_product">
                    Existing Stock <span class="validation-color">*</span><input class="form-control" type="text" name="quant" id="quant" >
                    <input type="hidden" name="id_product" id="id_product" >
                    <span class="validation-color" id="err_quantity"></span>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="quantity_products_button">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            <!-- </form> -->
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).on("click", '.quantity_change', function (e)
    {
        var qty = $(this).data('qty');
        $("#quant").val(qty);
        var pid = $(this).data('pid');
        $("#id_product").val(pid);
    });

    $(document).on("click", '#quantity_products_button', function (e) {
        var product_quantity = $('#quant').val();
        var product_id = $('#id_product').val();
        var quantity = $('#quant').val();
        if (quantity == null || quantity == "")
        {
            $("#err_quantity").text("Please Enter Quantity.");
            return false;
        } else if (!quantity.match(float_num_regex))
        {
            $('#err_quantity').text("  Please Enter Valid Quantity ");
            $('#err_quantity').focus();
            return false;
        } else
        {
            $("#err_quantity").text("");

            $.ajax(
                    {
                        url: base_url + 'product/update_existing_stock',
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'quantity': product_quantity,
                                    'product_id': product_id
                                },
                        success: function (result)
                        {
                            setTimeout(function () {// wait for 5 secs(2)
                                location.reload(); // then reload the page.(3)
                            });
                        }
                    });
        }
    });
    $("#quant").on("blur", function (event) {
        var quantity = $('#quant').val();
        if (quantity == null || quantity == "")
        {
            $("#err_quantity").text("Please Enter Quantity.");
            return false;
        } else
        {
            $("#err_quantity").text("");
        }
        if (!quantity.match(float_num_regex))
        {
            $('#err_quantity').text("  Please Enter Valid Quantity ");
            $('#err_quantity').focus();
            return false;
        } else
        {
            $("#err_quantity").text("");
        }

    });

</script>

