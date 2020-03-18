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
            <li class="active">Recurrence List</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Recurrence Invoice</h3>
                    </div>
                    <!-- /.box-header  -->
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th style="min-width: 100px;text-align: center;">Date</th>
                                    <!-- <th>Invoice</th> -->
                                    <th style="min-width: 250px;text-align: center;">Invoice Number</th>
                                    <th style="min-width: 150px;text-align: center;">Invoice Type</th>
                                    <th style="min-width: 150px;text-align: center;">Recurrence Date</th>
                                    <th style="min-width: 150px;text-align: center;">Next Generation Date</th>
                                    <th style="min-width: 200px;text-align: center;">Grand Total</th>
                                    <th style="min-width: 150px;text-align: center;">User</th>
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
"url": base_url + "recurrence",
"dataType": "json",
"type": "POST",
"data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
},
"columns": [
{"data": "added_date"},
{"data": "invoice_number"},
{"data": "invoice_type"},
{"data": "recurrence_date"},
{"data": "next_generation_date"},
{"data": "grand_total"},
{"data": "added_user"},
{"data": "action"},
]
});
});
</script>