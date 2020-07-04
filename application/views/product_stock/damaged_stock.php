<div id="damaged_product" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Reported Damaged/Missing Stock</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <table id="editable_damaged_product" class="custom_datatable table table-bordered table-striped table-hover table-responsive">
                        <thead>
                            <tr>
                                <th>Reported Dated</th>
                                <th>Reference Number</th>
                                <th>Quantity</th>
                                <th>Movement Type</th>
                                <th>Comments</th>
                                <th style="width: 85px">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    $(document).ready(function () {
        var disable = true;
        <?php if(!in_array($missing_stock_module_id, $active_edit) && !in_array($damaged_stock_module_id, $active_edit)){ ?>
            disable = false;
        <?php } ?>
        var comp_table = $('#editable_damaged_product').DataTable();
        $(document).on("click", ".damaged_product", function () {
            var id = $(this).data('id');
            comp_table.destroy();
            comp_table = $('#editable_damaged_product').DataTable({
                "iDisplayLength": 15,
                    "lengthMenu": [ [15, 25, 50,100, -1], [15, 25, 50,100, "All"] ],
                'ajax': {
                    url: base_url + 'product_stock/product_details',
                    type: 'post',
                    data: {'id': id},
                },               
                'columns': [                   
                    {'data': 'reference_date'},
                    {'data': 'reference_number'},
                    {'data': 'product_quantity'},
                    {'data': 'movement'},
                    {'data': 'comments'}, 
                     {'data': 'action','visible': disable},
                ]
            });
        });
        $('#editable_damaged_product tbody tr td').find('input, select').addClass('disable_in');
        $(document).on('click', '.edit_damage_cell', function () {
            var id = $(this).attr('data-id');
            $('#editable_damaged_product tbody tr td').find('input[name="reported_date"][data-id="' + id + '"]').addClass('dt-pkr-bdr');
            $('#editable_damaged_product tbody tr td').find('input[name="reported_date"][data-id="' + id + '"]').removeClass('disable_in');
            $('#editable_damaged_product tbody tr td').find('input[name="fixed_date"][data-id="' + id + '"]').addClass('dt-pkr-bdr');
            $('#editable_damaged_product tbody tr td').find('input[name="reference_no"][data-id="' + id + '"]').removeClass('disable_in');
            $('#editable_damaged_product tbody tr td').find('input[name="quality"][data-id="' + id + '"]').addClass('dt-pkr-bdr');
            $('#editable_damaged_product tbody tr td').find('input[name="quality"][data-id="' + id + '"]').removeClass('disable_in');
            $('#editable_damaged_product tbody tr td').find('#movement_' + id + '[data-id="' + id + '"]').addClass('dt-pkr-bdr');
            $('#editable_damaged_product tbody tr td').find('#movement_' + id + '[data-id="' + id + '"]').removeClass('disable_in');
            $('#editable_damaged_product tbody tr td').find('input[name="comments"][data-id="' + id + '"]').addClass('dt-pkr-bdr');
            $('#editable_damaged_product tbody tr td').find('input[name="comments"][data-id="' + id + '"]').removeClass('disable_in');
            $(this).closest('tr').find('.input-group-addon').removeClass('hide');
            $(this).closest('tr').find('.input-group-addon').css('border-color', '#337ab7');
            var dateToday = new Date();
            $('.datepicker').datepicker({
                minDate: dateToday,
                dateFormat: "dd-mm-yy"
            });
        });
        $(document).on('click', '.save_damage_cell', function () {
            var id = $(this).attr('data-id');
            var clas = $('#quality_' + id).attr('class').split(' ')[1];
            if (clas == 'dt-pkr-bdr') {
                var reference_date = $('#reported_date_' + id).val() ? $('#reported_date_' + id).val() : "";
                var old_type = $('#old_type_' + id).val() ? $('#old_type_' + id).val() : "";
                var product_id = $('#product_id_' + id).val() ? $('#product_id_' + id).val() : "";
                var quantity = $('#quality_' + id).val() ? $('#quality_' + id).val() : "";
                var move_product = $('#movement_' + id).val() ? $('#movement_' + id).val() : "";
                var comments = $('#comments_' + id).val() ? $('#comments_' + id).val() : "";
                var quantity_old = $('#old_quantity_' + id).val() ? $('#old_quantity_' + id).val() : "";
                $.ajax({
                    url: base_url + 'product_stock/update_damage_stock',
                    dataType: 'JSON',
                    method: 'POST',
                    data: {'reference_date': reference_date, 'quantity': quantity, 'move_product': move_product, 'comments': comments, 'old_type': old_type, 'product_id': product_id, 'quantity_old': quantity_old, 'id': id},
                    success: function (result) {
                        if(result.flag){                    
                            dTable.destroy();
                            dTable = getAllProductStock();
                            alert_d.text = result.msg;
                            PNotify.success(alert_d);
                            $('#damaged_product').modal('hide');
                        }else{
                            alert_d.text = result.msg;
                            PNotify.error(alert_d);
                            
                        }
                    },
                    error: function (result){
                        alert_d.text ='Something Went Wrong!';
                        PNotify.error(alert_d);
                    }
                });
                $('#editable_damaged_product tbody tr td').find('[data-id="' + id + '"]').removeClass('dt-pkr-bdr');
                $('#editable_damaged_product tbody tr td').find('input[name="reported_date"][data-id="' + id + '"]').addClass('disable_in');
                $('#editable_damaged_product tbody tr td').find('input[name="fixed_date"][data-id="' + id + '"]').addClass('disable_in');
                $('#editable_damaged_product tbody tr td').find('input[name="reference_no"][data-id="' + id + '"]').addClass('disable_in');
                $('#editable_damaged_product tbody tr td').find('input[name="quality"][data-id="' + id + '"]').addClass('disable_in');
                //$('#editable_damaged_product tbody tr td').find('input[name="movement_"][data-id="' + id + '"]').addClass('disable_in');
                $('#editable_damaged_product tbody tr td').find('#movement_' + id + '[data-id="' + id + '"]').addClass('disable_in');
                $('#editable_damaged_product tbody tr td').find('input[name="comments"][data-id="' + id + '"]').addClass('disable_in');
                $(this).closest('tr').find('.input-group-addon').addClass('hide');
            }
        });
    });
</script>
<script src="<?php echo base_url('assets/'); ?>plugins/datepicker/bootstrap-datepicker.js"></script>

