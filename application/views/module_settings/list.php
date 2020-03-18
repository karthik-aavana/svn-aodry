<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');

?>



<div class="content-wrapper">

        <div class="fixed-breadcrumb">

        <ol class="breadcrumb abs-ol">

            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>

            <li class="active">Module Settings</li>

        </ol>

    </div>

    <section class="content mt-50">

        <div class="row">

            <div class="col-md-12">

                <div class="box">

                	<div id="plus_btn">

	                    <div class="box-header with-border">

	                        <h3 class="box-title">Module Settings</h3>

	                        <?php

	                       /* if (in_array($privilege_module_id, $active_add))

	                        {

	                            ?>

	                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('module_settings/add'); ?>">Add Module Settings</a>

	                        <?php } */ ?>

	                    </div>

                    </div>

                    <div id="filter">

						<div class="box-header with-border box-body filter_body"></div>

					</div>                  

                    <div class="box-body">

                        <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">

                            <thead>

                                <tr>

                                	<th width="9px">#</th>

                                    <th>Module Name</th>

                                    <th>Invoice<br>First Prefix</th>

                                    <th>Invoice<br>Last Prefix</th>

                                    <th>Invoice<br>Separation</th>

                                    <th>Invoice<br>Type</th>

                                    <th>Invoice<br>Creation</th>

                                    <th>Invoice<br>Readonly</th>

                                    <th>Item<br>Access</th>

                                    <th>Note<br>Split</th>                                    

                                </tr>

                            </thead>

                            <tbody></tbody>

                        </table>

                    </div>

                    <!-- /.box-body -->

                </div>

                <!-- /.box -->

            </div>

            <!--/.col (right) -->

        </div>

        <!-- /.row -->

    </section>

    <!-- /.content -->

</div>

<!-- /.content-wrapper -->

<?php

$this->load->view('layout/footer');

$this->load->view('general/delete_modal');

?>



<script>

    $(document).ready(function () {

        $('#list_datatable').DataTable({

            "processing": true,

            "serverSide": true,

            "ajax": {

                "url": base_url + "module_settings",

                "dataType": "json",

                "type": "POST",

                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}

            },

            "columns": [

            	{"data": "action"},

                {"data": "module_name"},

                {"data": "invoice_first_prefix"},

                {"data": "invoice_last_prefix"},

                {"data": "invoice_seperation"},

                {"data": "invoice_type"},

                {"data": "invoice_creation"},

                {"data": "invoice_readonly"},

                {"data": "item_access"},

                {"data": "note_split"},                

            ],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});

    });

</script>



