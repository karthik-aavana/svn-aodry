<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');
    $this->load->view('layout/header');
?>

<div class="content-wrapper">
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Closing Stock</h3>                    
                    </div>
                    <div class="well">
                        <div class="box-body">
                            <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                                <thead>
                                    <tr>
                                        <!-- <th>Store</th>-->
                                        <th>Store Name</th> 
                                        <th>Store Location</th> 
                                        <!-- <th>Department</th>
                                        <th>Sub Department</th> -->
                                        <th>Category</th>   
                                        <th>Sub Category</th>   
                                        <th>Brand Name</th> 
                                        <th>Article Number</th>
                                        <th>Product Name</th>
                                        <th>Product Batch</th>  
                                        <th>Barcode</th>
                                        <th>Size</th>   
                                        <th>Colour</th>
                                        <th>HSN Code</th>
                                        <th>Unit</th>
                                        <th>Opening Stock</th>
                                        <th>Purchase Stock</th>
                                        <th>Sales Stock</th>  
                                        <th>Closing Stock</th>  
                                        <th>MAP</th>
                                        <th>GST</th>
                                        <th>Selling Price</th>
                                        <th>Cost Price</th>
                                        <th>MRP</th>
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
    </section>
</div>
<?php 
    $this->load->view('layout/footer'); 
?>
<style type="text/css">
    
    #list_datatable_length{
        position: absolute;
        margin-left: 226px;
        margin-top: -2px;
    }
    
</style>
<script>
$(document).ready(function() {

        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "lengthMenu": [ [10, 25, 50, 100, 500, 1000, -1], [10, 25, 50, 100, 500, 1000, "All"] ],
            "ajax": {
                "url": base_url + "stock/closing_stock",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [                
                /*{"data": "branch_code"},*/
                {"data": "branch_name"},
                {"data": "store_location"},
                /*{"data": "department_name"},
                {"data": "sub_department_name"},*/
                {"data": "category_name"},
                {"data": "sub_category_name"},
                {"data": "brand_name"},
                {"data": "product_code"},
                {"data": "product_name"},
                {"data": "product_batch"},
                {"data": "product_barcode"},
                {"data": "size"},
                {"data": "colour"},
                {"data": "product_hsn_sac_code"},
                {"data": "uom"},
                {"data": "opening_stock"},
                {"data": "purchase_qty"},
                {"data": "sales_qty"},
                {"data": "product_quantity"},
                {"data": "map"},
                {"data": "gst"},
                {"data": "selling_price"},
                {"data": "cost_price"},
                {"data": "mrp"}
            ],
            "columnDefs": [{
                "targets": "_all",
                "orderable": false
            }],            
               
                buttons: [
                    {extend: 'csv', footer: true},
                    {extend: 'excel', footer: true},
                    {extend: 'pdfHtml5',
                        text: 'PDF',
                        filename: 'Closing Stock',
                        orientation: 'landscape', //portrait
                        pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                        exportOptions: {
                            columns: ':visible',
                            search: 'applied',
                            order: 'applied'
                        }}],
                         dom: 'Bfrltip',

        });

    });
</script>

