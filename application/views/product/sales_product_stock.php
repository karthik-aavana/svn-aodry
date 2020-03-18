<?php defined('BASEPATH') OR exit('No direct script access allowed');
$this -> load -> view('layout/header');
?>
<div class="content-wrapper">
	<section class="content mt-50">
		<div class="row">
			<div class="col-md-12">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Sales Stock</h3>
						<!--<a class="btn btn-sm btn-info pull-right btn-flat" data-toggle="modal" data-target="#add_p_stock_modal" href="#">Add Product Stock</a> -->
					</div>			
						<div class="box-body">
							<table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
								<thead>
									<tr>
										<th>Product</th>
									<th>SKU</th>
									<th>Sales Reference</th>
										<th>Quantity</th>
										<th>Selling Price/Unit</th>
										<th>Total Sales Value</th>
										<th>Purchase Price/Unit</th>
										<th>Total Purchase Cost</th>
										<th>Gross Profit</th>
										<th>Sales Margin(%)</th>
										<!-- <th width="10%">Percentage of Margin on Sales </th> -->
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php $this -> load -> view('layout/footer'); ?>
<script>
$(document).ready(function() {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": base_url + "product/sales_product",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [               
                {"data": "product_name"},
                {"data": "product_sku"},
                {"data": "reference_number"},
                {"data": "qty"},
                {"data": "amt"},
                {"data": "sales_cost"},
                {"data": "pur_amt"},
                {"data": "purchase_cost"},
                {"data": "gross_profit"},
                {"data": "margin_percntage"}
            ],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });
             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
    });
</script>

