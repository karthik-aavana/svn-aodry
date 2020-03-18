<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');

?>



<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">



    </section>

    <div class="fixed-breadcrumb">

        <ol class="breadcrumb abs-ol">

            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>

            <li class="active">UQC</li>

        </ol>

    </div>

    <!-- Main content -->

    <section class="content mt-50">

        <div class="row">

            <div class="col-md-12">

                <div class="box">

                    <div class="box-header with-border">

                        <h3 class="box-title">HSN</h3>

                      <a data-toggle="modal" data-target="#add_hsn_modal" class="btn btn-sm btn-info pull-right" href="" title="Add HSN or SAC" onclick="">Add HSN </a>

                    </div>

                    <!-- /.box-header -->

                    <div class="box-body"> 

                    	<table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">

                            <thead>

                                <tr>                               

                                    <th>Type</th>

                                    <th>HSN/SAC Code</th>

                                    <th>Description</th>

                                    <th>Action</th>

                                </tr>

                            </thead>

                            <tbody>

                            	<!--<tr>

                            		<td>HSN</td>

                            		<td>441070</td>

                            		<td>ACCOMODATION IN HOTELS/ INN/ GUEST HOUSE</td>

                            		<td><label class="switch">

									<input type="checkbox" class="checkbox" checked="">

									<span class="slider round"></span> </label></td>

                            	</tr>

                            	<tr>

                            		<td>SAC</td>

                            		<td>4587</td>

                            		<td>ADVERTISING AGENCY</td>

                            		<td><label class="switch">

									<input type="checkbox" class="checkbox" checked="">

									<span class="slider round"></span> </label></td>

                            	</tr>

                            	<tr>

                            		<td>HSN</td>

                            		<td>48768</td>

                            		<td>Pure-bred breeding animals</td>

                            		<td><label class="switch">

									<input type="checkbox" class="checkbox" checked="">

									<span class="slider round"></span> </label></td>

                            	</tr>

                            	<tr>

                            		<td>SAC</td>

                            		<td>4587</td>

                            		<td>ADVERTISING AGENCY</td>

                            		<td><label class="switch">

									<input type="checkbox" class="checkbox" checked="">

									<span class="slider round"></span> </label></td>

                            	</tr>

                            	<tr>

                            		<td>HSN</td>

                            		<td>441070</td>

                            		<td>ACCOMODATION IN HOTELS/ INN/ GUEST HOUSE</td>

                            		<td><label class="switch">

									<input type="checkbox" class="checkbox" checked="">

									<span class="slider round"></span> </label></td>

                            	</tr>

                            	<tr>

                            		<td>SAC</td>

                            		<td>4587</td>

                            		<td>ADVERTISING AGENCY</td>

                            		<td><label class="switch">

									<input type="checkbox" class="checkbox" checked="">

									<span class="slider round"></span> </label></td>

                            	</tr>-->

                            </tbody>

                        </table>

                    </div>

                    

                </div>

                

            </div>

            

        </div>

        

    </section>

    

</div>



<?php

$this->load->view('layout/footer');

$this->load->view('hsn_sac/add_hsn_modal');

$this->load->view('hsn_sac/edit_hsn_modal');

?>

<script>

	$(document).ready(function() {

		$('#list_datatable').DataTable({			

			"processing": true,

            "serverSide": true,

            "ajax": {

                "url": base_url + "hsn_sac",

                "dataType": "json",

                "type": "POST",

                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}

            },

            "columns": [                

                {"data": "hsn_type"},

                {"data": "itc_hs_codes"},

                {"data": "description"},                

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