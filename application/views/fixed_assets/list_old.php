<?php defined('BASEPATH') OR exit('No direct script access allowed');
$this -> load -> view('layout/header');
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

    </section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Customer</li>
        </ol>
    </div>


    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Customer</h3>
                        <?php
                        if (in_array($customer_module_id, $active_add))
                        {
                            ?>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('customer/add'); ?>" title="Add Customer">Add Customer </a>
                        <?php } ?>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                 <!-- <th style="min-width: 100px;text-align: center;">Date</th> -->
                                    <th style="min-width: 60px;text-align: center;">Customer Code</th>
                                    <th style="min-width: 60px;text-align: center;">Reference Number</th>
                                    <th style="min-width: 200px;text-align: center;">Customer Name</th>
                                    <!-- <th style="min-width: 150px;text-align: center;">Contact Person Name</th> -->
                                    <th style="min-width: 100px;text-align: center;">Phone</th>
                                    <th style="min-width: 100px;text-align: center;">Email Address</th>
                                    <th style="min-width: 100px;text-align: center;">Country</th>
                                    <th style="min-width: 90px;text-align: center;">State</th>
                                    <th style="min-width: 90px;text-align: center;">City</th>
                                    <th style="min-width: 100px;text-align: center;">User</th>
                                    <th style="min-width: 250px;text-align: center;"><?php echo ''; ?></th>
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
                "url": base_url + "customer",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this -> security -> get_csrf_token_name(); ?>': '<?php echo $this -> security -> get_csrf_hash(); ?>'}
					},
					"columns": [
					{"data": "customer_code"},
					{"data": "reference_number"},
					{"data": "customer_name"},
					// { "data": "contact_person" },
					{"data": "phone"},
					{"data": "email"},
					{"data": "country"},
					{"data": "state"},
					{"data": "city"},
					{"data": "added_user"},
					{"data": "action"}
					]
					});
					});
</script>
