<div class="modal fade" id="quantity_history_modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Stock</h4>
            </div>
            <!-- <form role="form" id="form" method="post" action="<?php echo base_url('receipt/pay_now'); ?>"> -->
           <div class="well">
                        <div class="box-body">
                            <table id="list_datatable1" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                                <thead>
                                    <tr>
                                      <!-- <th>Date</th> -->
                                     
                                      
                                        <th > Quantity</th>
                                        <th >Stock Type</th>
                                        <th >Added Date</th>
                                        <th >User</th>
                                       
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
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
 

<script>
    
    // $(document).on("click", '.quantity_history_view', function (e) 
    // {
    //   $.fn.dataTable.ext.errMode = 'none';
    //     var id = $(this).data('id');
     
       
    // });
   //  $( document ).on("click", '.quantity_history_view', function (e) {
   //   var id = $(this).data('id');
   //   history(id);
 

   // });
    

    function history(id)
    {
        if(id!="")
     {
        // Destroy the table
        $("#list_datatable1").dataTable().fnDestroy()
        
        $('#list_datatable1').DataTable({
            
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "product/quantity_history_list/"+ id,
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
              
                {"data": "quantity"},
                {"data": "stock_type"},
                {"data": "added_date"},             
                {"data": "added_user"}
             
            ]                  
        });
    }
       
       //$.fn.dataTable.ext.errMode = 'none';
     
    }
</script>

