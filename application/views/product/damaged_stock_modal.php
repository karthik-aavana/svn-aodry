<div class="modal fade" id="damaged_products" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Damaged Products</h4>
            </div>
            <!-- <form role="form" id="form" method="post" action="<?php echo base_url('receipt/pay_now'); ?>"> -->
            <div class="modal-body col-sm-12">
                <input type="hidden" name="product_id" id="product_id" value="">
                <div class="form-group col-sm-6" id="productQuantity">

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="damaged_products_button">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            <!-- </form> -->
        </div>
    </div>
</div>

<div class="modal fade" id="return_stock" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Return to Stock</h4>
            </div>
            <!-- <form role="form" id="form" method="post" action="<?php echo base_url('receipt/pay_now'); ?>"> -->
            <div class="modal-body col-sm-12">
                <input type="hidden" name="product_id1" id="product_id1" value="">
                <div class="form-group col-sm-6" id="damagedQuantity">

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="return_stock_button">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            <!-- </form> -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).on("click", '.damaged_products', function (e) {
        var id = $(this).data('id');
        $("#product_id").val(id);
        $.ajax(
                {
                    url: base_url + 'product/get_product_quantity',
                    dataType: 'JSON',
                    method: 'POST',
                    data: {id: id},
                    success: function (result)
                    {
                        var quantity = '<label for="product_quantity">Products Quantity<span class="validation-color">*</span></label> <br><select class="form-control select2" id="product_quantity" name="product_quantity">';
                        for (var i = 1; i <= result[0].product_quantity; i++)
                        {
                            quantity += '<option value="' + i + '">' + i + '</option>';
                        }
                        quantity += '</select>';
                        $('#productQuantity').html(quantity);
                    }
                });
    });
    $(document).on("click", '#damaged_products_button', function (e) {
        var product_quantity = $('#product_quantity').val();
        var product_id = $('#product_id').val();

        $.ajax(
                {
                    url: base_url + 'product/add_damaged_products',
                    dataType: 'JSON',
                    method: 'POST',
                    data:
                            {
                                'damaged_quantity': product_quantity,
                                'product_id': product_id
                            },
                    success: function (result)
                    {
                        setTimeout(function () {// wait for 5 secs(2)
                            location.reload(); // then reload the page.(3)
                        });
                    }
                });
    });

    $(document).on("click", '.return_stock', function (e) {
        var id = $(this).data('id');
        $("#product_id1").val(id);
        $.ajax(
                {
                    url: base_url + 'product/get_product_quantity',
                    dataType: 'JSON',
                    method: 'POST',
                    data: {id: id},
                    success: function (result)
                    {
                        var quantity = '<label for="damaged_quantity">Damaged Products Quantity<span class="validation-color">*</span></label> <br><select class="form-control select2" id="damaged_quantity" name="damaged_quantity">';
                        for (var i = 1; i <= result[0].product_damaged_quantity; i++)
                        {
                            quantity += '<option value="' + i + '">' + i + '</option>';
                        }
                        quantity += '</select>';
                        $('#damagedQuantity').html(quantity);
                    }
                });
    });
    $(document).on("click", '#return_stock_button', function (e) {
        var damaged_quantity = $('#damaged_quantity').val();
        var product_id1 = $('#product_id1').val();

        $.ajax(
                {
                    url: base_url + 'product/edit_damaged_products',
                    dataType: 'JSON',
                    method: 'POST',
                    data:
                            {
                                'product_quantity': damaged_quantity,
                                'product_id': product_id1
                            },
                    success: function (result)
                    {
                        setTimeout(function () {// wait for 5 secs(2)
                            location.reload(); // then reload the page.(3)
                        });
                    }
                });
    });

</script>
