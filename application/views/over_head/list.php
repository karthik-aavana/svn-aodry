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
            <li class="active">Over Head</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Over Head</h3>
                        <?php
                         if(in_array($module_id, $active_add))
                        {
                        ?>
                        <a class="btn btn-sm btn-info pull-right btn-flat" href="<?php echo base_url('over_head/add'); ?>">Add Over Head</a>
                        <?php } ?>
                    </div>
                    <div class="well">
                        <div class="box-body">
                            <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                                <thead>
                                    <tr>
                                        <!-- <th>Date</th> -->
                                        <th>Name</th>
                                        <th>Unit</th>
                                        <th>Cost per unit</th>
                                        <th> Quantity</th>
                                        <th>User</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
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
"responsive": true,
"ajax": {
"url": base_url + "over_head",
"dataType": "json",
"type": "POST",
"data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
},
"columns": [
{"data": "over_head_name"},
{"data": "over_head_unit"},
{"data": "over_head_cost_per_unit"},
{"data": "quantity"},
{"data": "added_user"},
{"data": "action"}
]
});
});
</script>