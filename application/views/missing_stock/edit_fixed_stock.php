<div id="edit_fixed_stock" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close editclose" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Reported Found/Damaged</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <table id="editable_damaged_product" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                        <thead>
                            <tr>
                                <th> Reported Date </th>
                                <th> Reference Number</th>
                                <th> Quantity</th>
                                <th> Movement Type</th>
                                <!--<th> Attachement</th> -->
                                <th> Comments</th>
                                <th style="width: 85px"> Action</th>
                            </tr>
                        </thead>
                        <tbody>
                                        <!--<tr>
                                                <td>
                                                <div class="input-group date">
                                                        <input type="text" class="form-control datepicker" data-id="1" name="reported_date" value="2019-04-28" autocomplete="off">
                                                        <div class="input-group-addon hide">
                                                                <i class="fa fa-calendar"></i>
                                                        </div>
                                                </div></td>
                                                <td>
                                                <input type="text" name="reference_no" value="454528" data-id="1" class="form-control"/>
                                                </td>
                                                <td>
                                                <input type="number" name="quality" value="75" data-id="1" min="0" max="9" class="form-control"/>
                                                </td>
                                                <td>
                                                <select class="form-control" id="movement" name="movement" tabindex="-1" aria-hidden="true" data-id="1">
                                                        <option value="">Select Movement Type</option>
                                                        <option value="damaged">Damaged</option>
                                                        <option value="missing">Missing</option>
                                                </select></td>
                                                <td>
                                                <input type="text" name="" value="785000" data-id="1" class="form-control"/>
                                                </td>
                                                <td>
                                                <input type="" name="comments" rows="2" class="form-control disable_in" data-id="1" value="comment abt product">
                                                <td><a href="JavaScript:void(0);" class="btn btn-info btn-xs edit_cell" data-id="1"><i class="fa fa-pencil"></i></a> | <a class="btn btn-info btn-xs save_cell" href="JavaScript:void(0);" data-id="1"><i class="fa fa-save"></i></a> | <a class="btn btn-info btn-xs" href="JavaScript:void(0);"><i class="fa fa-trash"></i></a></td>
                                        </tr> -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- datepicker -->

<script>
    $(document).ready(function () {


        var comp_table = $('#editable_damaged_product').DataTable();
        $(document).on("click", ".edit_fixed_stock ", function () {
            var id = $(this).data('id');

            comp_table.destroy();
            comp_table = $('#editable_damaged_product').DataTable({
                'ajax': {
                    url: base_url + 'missing_stock/product_details',
                    type: 'post',
                    data: {'id': id},
                },
                'columns': [
                    {'data': 'reference_date'},
                    {'data': 'reference_number'},
                    {'data': 'product_quantity'},
                    {'data': 'movement'},
                    {'data': 'comments'},
                    {'data': 'action'}
                ]
            });
            /*
             $('#editable_damaged_product').DataTable({
             "fnDrawCallback" : function(oSettings) {
             var rowCount = this.fnSettings().fnRecordsDisplay();
             if (rowCount <= 10) {
             $('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate').hide();
             } else {
             $('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate').show();
             }
             }
             });*/
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
            //console.log(clas);
            if (clas == 'dt-pkr-bdr') {
                var reference_date = $('#reported_date_' + id).val() ? $('#reported_date_' + id).val() : "";
                var old_type = $('#old_type_' + id).val() ? $('#old_type_' + id).val() : "";
                var product_id = $('#product_id_' + id).val() ? $('#product_id_' + id).val() : "";
                var quantity = $('#quality_' + id).val() ? $('#quality_' + id).val() : "";
                var move_product = $('#movement_' + id).val() ? $('#movement_' + id).val() : "";
                var comments = $('#comments_' + id).val() ? $('#comments_' + id).val() : "";
                var quantity_old = $('#old_quantity_' + id).val() ? $('#old_quantity_' + id).val() : "";
                $.ajax({
                    url: base_url + 'missing_stock/update_missing_stock',
                    dataType: 'JSON',
                    method: 'POST',
                    data: {'reference_date': reference_date, 'quantity': quantity, 'move_product': move_product, 'comments': comments, 'old_type': old_type, 'product_id': product_id, 'quantity_old': quantity_old, 'id': id},
                    success: function (result) {
                        if(result.flag){                    
                            dTable.destroy();
                            dTable = getAllMissingStock();
                            alert_d.text = result.msg;
                            PNotify.success(alert_d);
                            $('#edit_found_stock').modal('hide');
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
