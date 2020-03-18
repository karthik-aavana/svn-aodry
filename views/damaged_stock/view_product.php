<div id="view_product" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Reported Damaged Stock</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <table id="view_damaged_stock" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                        <thead>
                            <tr>
                                <th> Reported Date </th>
                                <th> Reference Number</th>
                                <th> Quantity </th>
                                <th> Movement Type</th>
                                <th> Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var comp_table = $('#view_damaged_stock').DataTable();
        $(document).on("click", ".view_damage ", function () {
            var id = $(this).data('id');
            console.log(id);
            comp_table.destroy();
            table_load(id);
        });

        function table_load(id) {
            comp_table = $('#view_damaged_stock').DataTable({
                'ajax': {
                    url: base_url + 'damaged_stock/damaged_history/' + id,
                    type: 'post'
                },
                'columns': [
                    {'data': 'report_date'},                    
                    {'data': 'reference_number'},
                    {'data': 'qty'},
                    {'data': 'type'},
                    {'data': 'comment'},
                ]
            });
        }
    });
</script>