<?php 
	defined('BASEPATH') OR exit('No direct script access allowed');
	$this->load->view('layout/header');
?>
<style type="text/css">    
    .form-group {
        margin: 10px 5px;
    }
    .label-style{
        background: #ddd;
        color: #333;
        padding: 2px 10px;
        font-size: 11px;
        border-radius: 10px;
    }
    .modal-footer button:nth-child(2) {
        display: inline-block;
    }
    div.dataTables_wrapper div.dataTables_filter {
        float: right;
    }
</style>
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
                            <div class="box-header" id="filters_applied">
                            </div><br>
                                 <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">
								<thead>
									<tr>
                                        <th>
                                        <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#brand_modal">Brand Name<span class="fa fa-toggle-down"></span></a>
                                        </th>								
										<th>Category</th>	
										<th>Sub Category</th>	
										<th>Product Name</th>	
										<th>Barcode</th>
										<th>HSN Code</th>
										<th>Unit</th>
										<th>Closing Stock Qty</th>	
										<th>MAP</th>
										<th>GST</th>
										<th>Selling Price</th>
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
<div class="modal fade" id="brand_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select Brand</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_brand"  name="filter_brand">
                                <option value="">Select Brand</option>
                                <?php
                                foreach ($brand as $key => $value) {
                                        ?>
                                        <option value="<?= $value->brand_name ?>"><?= $value->brand_name ?></option>
                                        <?php
                                }
                                ?>                               
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-brand" >Reset</button>
            </div>
        </div>      
    </div>
</div>
<?php 
	$this->load->view('layout/footer'); 
?>
<style type="text/css">
	
	#list_datatable_length{
		position: absolute;
    	margin-left: 326px;
    	margin-top: -2px;
	}
	
</style>
<script>
$(document).ready(function() { 

    $("#reset-brand").click(function () {
        $("#brand_modal .select2-selection__rendered").empty();
        $('#filter_brand option:selected').prop("selected", false);
    })  
    function resetall() {
        $("#brand_modal .select2-selection__rendered").empty();
        $('#filter_brand option:selected').prop("selected", false);
            appendFilter();
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable()
    }
    
    function appendFilter() { //button filter event click
        var filter_brand = "";
        var label  = '';
        $("#filter_brand option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_brand += $this.text() + ", ";
                }
            });
            if (filter_brand != '') {
                label += "<label id='lbl_brand' class='label-style'><b> Brand : </b> " + filter_brand.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }

            if (label != "") {
                $('#filters_applied').html(label);
            } else {
                $('#filters_applied').html('<label></label>');
            }
            
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable()
    }

    $(document).on('click',".fa-times",function(){
            $(this).parent('label').remove();
            var label1=$(this).parent('label').attr('id');
           
            if(label1 == 'lbl_brand'){
                $("#brand_modal .select2-selection__rendered").empty();
                $('#filter_brand option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
    })

generateOrderTable() 
    function generateOrderTable() {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "ajax": {
                "url": base_url + "stock/brand_closing_stock",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>', 'filter_brand': $('#filter_brand').val()}
            },
            "columns": [
                {"data": "brand_name"},
                {"data": "category_name"},
                {"data": "sub_category_name"},
                {"data": "product_name"},
                {"data": "product_code"},
                {"data": "product_hsn_sac_code"},
                {"data": "uom"},
                {"data": "product_quantity"},
                {"data": "map"},
                {"data": "gst"},
                {"data": "selling_price"}
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
                        }},
                    {
                        text: 'Reset All',
                        action: function (e, dt, node, config) {
                            resetall();
                        }
                    }],
                         dom: 'Bfrltip',


        });
    }
        $('.filter_search').click(function () {
            appendFilter();
        });
       
    });
</script>