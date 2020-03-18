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
						<h3 class="box-title">Brandwise Purchase Stock</h3>
					</div>
					<div class="well">
						<div class="box-body">
							<table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
								<thead>
									<tr>										
										<th>Brand</th>
										<th>Category</th>	
										<th>Sub Category</th>	
										<th>Product Name</th>	
										<th>Product Code</th>
										<th>HSN Code</th>
										<th>Date</th>
										<th>Supplier</th>	
										<th>Invoice No</th>
										<th>Unit</th>
										<th>Purchase Qty</th>
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
<?php $this -> load -> view('layout/footer'); ?>
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
            "ajax": {
                "url": base_url + "stock/brand_purchase_stock",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [                
            	{"data": "brand_name"},
                {"data": "category_name"},
                {"data": "sub_category_name"},
                {"data": "product_name"},
                {"data": "product_code"},
                {"data": "product_hsn_sac_code"},
                {"data": "purchase_date"},
                {"data": "supplier_name"},
                {"data": "purchase_invoice_number"},
                {"data": "uom"},
                {"data": "purchase_item_quantity"}
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
                        filename: 'Purchase Stock',
                        orientation: 'landscape', //portrait
                        pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                        exportOptions: {
                            columns: ':visible',
                            search: 'applied',
                            order: 'applied'
                        }}],
                         dom: 'Bfrltip',
                         'language': {
                            'loadingRecords': '&nbsp;',
                            'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                            },
                        });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});

    });
</script>

