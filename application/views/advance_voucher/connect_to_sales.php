<style>
    /*#connectSaleList tr th:last-child, #connectSaleList tr td:last-child{
        width: 400px !important;
    }*/
    #connectSales .modal-lg{
        width: 1100px !important;
    }    
    #connectSales tr td:last-child{
    	white-space: nowrap;    	
    }    
     #connectSales tr td span{
     	color: red;
     }    
</style>
<div id="connectSales" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Connect to Sales</h4>
            </div>
            <div class="modal-body">
                  <div id="loader">
                        <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                    </div>
                <div class="table-responsive">
                    <table id="connectSaleList" class="table custom_datatable table-bordered table-striped table-hover" >
                        <thead>
                        <th>Customer </th>
                        <th>Advance Voucher</th>
                        <th>Total Advance Voucher Amount</th>
                        <th>Unadjusted</th>
                        <th>Adjusted Amount</th>
                        <th>Reference Invoice</th>
                        <th>Action</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary tbl-btn" id="btn_submit" type="button" name="btn_submit">
                    Submit
                </button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script>
//data:{'value': jsonvalue, 'key': jsonkey,'product_code':product_code},
//var table =  $('#connectSaleList').DataTable({});
    $(document).ready(function () {
        var comp_table = $('#connectSaleList').DataTable();
        $(document).on("click", ".connectSales", function () {
            var id = $(this).data('id');
            comp_table.destroy();
            table_load(id);
        });
        function table_load(id) {
            comp_table = $('#connectSaleList').DataTable({
                'ajax': {
                    url: base_url + 'advance_voucher/get_advance_sales/' + id,
                    type: 'post',
                    data: {'id': id}
                },
                'columns': [
                    {'data': 'customer'},
                    {'data': 'advance_voucher'},
                    {'data': 'advance_voucher_amount'},
                    {'data': 'unadjusted_amount'},
                    {'data': 'adjusted_amount'},
                    {'data': 'reference_invoice'},
                    {'data': 'action'},
                ]
            });
        }
    });
    $(document).ready(function () {
        $(document).on("click", "#btn_submit", function () {
            var arr = [];
            var total = 0;
            $(".adj_amount").each(function () {
                //advance_amount = parseFloat(advance_amount);
                var data_id = $(this).attr('data-id');
                var disable_mode = $(this).attr('class').split(' ')[2];
                var val = 0;
                var sales_id;
                if (disable_mode == 'disable_in') {
                    val = $('#connectSales tbody tr td').find('input[name="adj_amount"][data-id="' + data_id + '"]').val();
                    sales_id = $('#connectSales tbody tr td').find('select[name="reference_invoice"][data-id="' + data_id + '"]').val();
                    arr.push({
                        id: sales_id,
                        amount: val
                    });
                    total = parseFloat(total) + parseFloat(val);
                }
            });
            var advance_amount = $('#hidden_unadjusted').val();
            var customer_id = $('#customer_id').val();
            var advance_id = $('#advance_id').val();
            var valueJSON = JSON.stringify(arr);
            if (total == 0 || total > parseFloat(advance_amount)) {
                alert_d.text ='Please select Valid Amount';
                PNotify.error(alert_d); 
                return false;
            } else {
                $.ajax({
                    url: base_url + 'advance_voucher/update_advance_sales',
                    dataType: 'JSON',
                    method: 'POST',
                    data: {'advance_amount': total, 'advance_id': advance_id, 'valueJSON': valueJSON, 'customer_id': customer_id},
                       beforeSend: function(){
                     // Show image container
                    $("#connectSales #loader").show();
                    },

                    success: function (result) {                       
                        setTimeout(function () {
                            location.reload();
                        });
                         $("#connectSales #loader").hide();
                    }
                });
            }
        });
        $(document).on("keyup", ".adj_amount", function () {
            var data_id = $(this).attr('data-id');
            var val = $('#connectSales tbody tr td').find('input[name="adj_amount_hidden"][data-id="' + data_id + '"]').val();
            var val_enter = $('#connectSales tbody tr td').find('input[name="adj_amount"][data-id="' + data_id + '"]').val();
            val_enter = parseFloat(val_enter);
            val = parseFloat(val);
            if(val != 0){
                if (val < val_enter) {
                    alert_d.text ='Please enter less than Invoice Pending amount';
                    PNotify.error(alert_d);
                    $('#connectSales tbody tr td').find('input[name="adj_amount"][data-id="' + data_id + '"]').val('');
                }
            }
            else {
                if (val < val_enter) {
                    alert_d.text ='Please select Invoice';
                    PNotify.error(alert_d);
                    $('#connectSales tbody tr td').find('input[name="adj_amount"][data-id="' + data_id + '"]').val('');
                }
            }
        });
        $(document).on("change", ".sales_inv", function () {
            var id = $(this).attr('data-id');
            var inv_id = $('#connectSales tbody tr td').find('select[name="reference_invoice"][data-id="' + id + '"]').val();
            console.log(inv_id);
            $.ajax({
                url: base_url + 'advance_voucher/get_invoice_amount/' + inv_id,
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'inv_id': inv_id
                },
                success: function (result) {
                    // console.log(result[0].tax_value);
                    console.log(inv_id);
                    // $('#connectSales tbody tr td').find('input[name="adj_amount"][data-id="' + id + '"]').val(result.adjusted_amount);
                    $('#connectSales tbody tr td').find('input[name="adj_amount_hidden"][data-id="' + id + '"]').val(result.adjusted_amount);
                    $('#connectSales tbody tr td').find('span[name="orginal_value"][data-id="' + id + '"]').text('Pending Amt: ' + result.adjusted_amount);
                    // $('#service_gst_code').val(adjusted_amount.remaiming);
                }
            });
        });
        //	var comp_table = $("#connectSaleList").dataTable({});
        /*$("#connectSaleList").dataTable({
         "fnDrawCallback" : function(oSettings) {
         var rowCount = this.fnSettings().fnRecordsDisplay();
         if (rowCount <= 10) {
         $('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate').hide();
         } else {
         $('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate').show();
         }
         }
         });*/
        var i = 1;
        $(document).on("click", "#add_row", function () {
            i++;
            $("#connectSaleList tbody").append('<tr><td colspan="4"><td> <input type="number" name="adj_amount" data-id="' + i + '" class="form-control adj_amount" value="" style="width: 140px"/><input type="hidden" name="adj_amount_hidden" data-id="' + i + '" class="adj_amount_hidden" value="0"/></td><td> <select class="form-control sales_inv" name="reference_invoice" data-id="' + i + '" style="width: 140px"><option value="">select</option></select><br><span name="orginal_value" data-id="' + i + '"></span></td><td width="90px"><a href="JavaScript:void(0);" class="btn btn-info btn-xs edit_cell" data-id="' + i + '"><i class="fa fa-pencil"></i></a> | <a class="btn btn-info btn-xs save_cell" href="JavaScript:void(0);" data-id="' + i + '"><i class="fa fa-save"></i></a> | <a href="JavaScript:void(0);" class="btn btn-info btn-xs" id="add_row"><i class="fa fa-plus"></i></a> | <a class="btn btn-info btn-xs remove_row" href="JavaScript:void(0);" data-id="' + i + '"><i class="fa fa-minus"></i></a></td></tr>');
            $('#connectSales tbody tr td').find('input[name="adj_amount"][data-id="' + i + '"], select[name="reference_invoice"][data-id="' + i + '"]').addClass('dt-pkr-bdr');
            invoice_array = JSON.parse($('#invoice_array').val());
            for (var x = 0; x < invoice_array.length; x++) {
                $('#connectSales tbody tr td').find('select[name="reference_invoice"][data-id="' + i + '"]').append('<option value="' + invoice_array[x].sales_id + '">' + invoice_array[x].invoice_number + '</option>');
            }
        });
        $('#connectSales tbody').find('input, select').addClass('disable_in');
        $(document).on('click', '.edit_cell', function () {
            var id = $(this).attr('data-id');
            $('#connectSales tbody tr td').find('input[name="adj_amount"][data-id="' + id + '"], select[name="reference_invoice"][data-id="' + id + '"]').addClass('dt-pkr-bdr');
            $('#connectSales tbody tr td').find('input[name="adj_amount"][data-id="' + id + '"], select[name="reference_invoice"][data-id="' + id + '"]').removeClass('disable_in');
        });
        $(document).on('click', '.save_cell', function () {
            var id = $(this).attr('data-id');
            $('#connectSales tbody tr td').find('[data-id="' + id + '"]').removeClass('dt-pkr-bdr');
            $('#connectSales tbody tr td').find('input[name="adj_amount"][data-id="' + id + '"], select[name="reference_invoice"][data-id="' + id + '"]').addClass('disable_in');
        });
        $(document).on("click", ".remove_row", function () {
            $(this).closest('tr').remove();
        });
    });
</script>