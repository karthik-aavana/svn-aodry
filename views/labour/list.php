<?php defined('BASEPATH') OR exit('No direct script access allowed');

$this -> load -> view('layout/header');

?>

<div class="content-wrapper">

	<!-- Content Header (Page header) -->	

	<div class="fixed-breadcrumb">

		<ol class="breadcrumb abs-ol">

			<li>

				<a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a>

			</li>

			<li class="active">

				Labour

			</li>

		</ol>

	</div>

	<section class="content mt-50">

		<div class="row">

			<div class="col-md-12">

				<div class="box">

					<div class="box-header with-border">

						<h3 class="box-title">Labour</h3>

						<?php

							if(in_array($module_id, $active_add))

							{

						?>

						<a class="btn btn-sm btn-info pull-right btn-flat" href="<?php echo base_url('labour/add'); ?>">Add Labour</a>

						<?php } ?>

					</div>				

					<div class="box-body">

						<table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >

							<thead>

								<tr>

									<!-- <th>Date</th> -->

									<th>Activity Name</th>

									<th>Classification</th>

									<th>Type</th>

									<th> Total Hours</th>

									<th>User</th>

									<th>Action</th>

								</tr>

							</thead>

							<tbody></tbody>

						</table>

					</div>

				</div>

			</div>

		</div>

	</section>

</div>

<?php $this -> load -> view('layout/footer');

$this -> load -> view('general/delete_modal');

?>

<script>
	$(document).ready(function () {
		$('#list_datatable').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"ajax": {
			"url": base_url + "labour",
			"dataType": "json",
			"type": "POST",
			"data": {'<?php echo $this -> security -> get_csrf_token_name(); ?>': '<?php echo $this -> security -> get_csrf_hash(); ?>'}
			},
			"columns": [
				{"data": "activity_name"},
				{"data": "labour_classification_name"},
				{"data": "type"},
				{"data": "total_no_hours"},
				{"data": "added_user"},
				{"data": "action"}
			],
            'language': {
               'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});

	});

</script>