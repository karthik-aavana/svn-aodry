<div id="view_reference" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Purchase Reference</h4>
            </div>
            <div class="modal-body">
                <table id="table_history" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Purchase Reference Number </th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var comp_table = $('#table_history').DataTable();
        $(document).on("click", ".reference", function () {
            var id = $(this).data('id');
            comp_table.destroy();
            table_load(id);
        });
        function table_load(id) {
            comp_table = $('#table_history').DataTable({
                'ajax': {
                    url: base_url + 'product_stock/reference_history/' + id,
                    type: 'post'
                },
                'columns': [
                    {'data': 'sno'},
                    {'data': 'reference_number'},
                    {'data': 'qty'},
                ]
            });
        }
    });


    /*  $(document).ready(function () {
     var ref_table = $('#table_history').DataTable();
     $(document).on("click", ".reference", function () {
     var id = $(this).data('id');
     console.log(id);
     ref_table.destroy();
     tableref_load(id);
     
     function tableref_load(id) {
     console.log(id);
     comp_table = $('#table_history').DataTable({
     'ajax': {
     url: base_url + 'product_stock/reference_history/' + id,
     type: 'post'
     },
     'columns': [
     {'data': 'sno'},
     {'data': 'reference_number'},
     {'data': 'qty'},
     ]
     });
     }
     });
     
     });
     */

</script>