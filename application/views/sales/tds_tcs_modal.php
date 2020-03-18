<div id="tds_tcs" class="modal fade z_index_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4>TCS/TDS</h4>
            </div>
            <div class="modal-body">
                <table id="tds_table" class="table table-bordered table-striped sac_table ">
                    <thead>
                    <th>TAX Name</th>						
                    <th>Action</th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Apply</button>
                <span type="button" class="btn btn-default remove_tds" data-dismiss="modal">Remove</span>                
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
     $(document).ready(function () {
        $.fn.DataTable.ext.pager.numbers_length = 4;
    });
</script>
