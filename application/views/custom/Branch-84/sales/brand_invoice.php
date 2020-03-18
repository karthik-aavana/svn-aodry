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
						<h3 class="box-title">Brandwise Invoice</h3>					
					</div>
					<div class="well">
						<div class="box-body">
							<table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
								<thead>
									<tr>
										<th>Brand</th>
										<th>Customer Name</th>
										<th>Invoice Number</th>
										<th>Date</th>
										<th>Place of Supply</th>	
										<th>Taxable Amount</th>	
										<th>Discount</th>	
										<th>GST</th>
										<th>TCS</th>	
										<th>Cess</th>
										<th>Cash Discount</th>
										<th>Grand Total</th>
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
    	margin-top: -33px;
	}
	
</style>
<script>

$(document).ready(function() {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "ajax": {
                "url": base_url + "sales/brand_sales",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [                
            	{"data": "brand_name"},
                {"data": "customer_name"},
                {"data": "invoice_number"},
                {"data": "sales_date"},
                {"data": "place_of_supply"},
                {"data": "taxable_amount"},
                {"data": "discount_amount"},
                {"data": "gst_amount"},
                {"data": "tds_amount"},
                {"data": "cess_amount"},
                {"data": "cash_discount_amount"},
                {"data": "invoice_amount"}
            ]

    });

});

</script>

