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
            <li class="active">Advance Voucher</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <?php
            if ($this->session->flashdata('email_send') == 'success')
            {
                ?>
                <div class="col-sm-12">
                    <div class="alert alert-success">
                        <button class="close" data-dismiss="alert" type="button">×</button>
                        Email has been send with the attachment.
                        <div class="alerts-con"></div>
                    </div>
                </div>
                <?php
            }
            ?>
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Advance Voucher</h3>
                        <?php
                        if (in_array($advance_voucher_module_id , $active_add)){
                            ?>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('advance_voucher/add'); ?>" title="Add Advance voucher">Add Advance voucher </a>
                        <?php } ?>

<!--   <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('receipt_voucher/add'); ?>" title="Receipt Voucher Add">Add Receipt Voucher </a>  -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th style="min-width: 100px;text-align: center;">Voucher Date</th>
                                    <th style="max-width: 150px;text-align: center;">Voucher Number</th>
                                    <th style="min-width: 200px;text-align: center;">Customer</th>
                                    <th style="min-width: 150px;text-align: center;">Reference Number</th>
                                    <th style="min-width: 150px;text-align: center;"><?php echo 'Amount'; ?></th>
                                    <th style="min-width: 150px;text-align: center;"><?php echo 'Converted Amount (' . $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ')'; ?></th>
                                    <th style="min-width: 100px;text-align: center;">Payment Mode</th>
                                    <th style="min-width: 200px;text-align: center;">User</th>
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
                "url": base_url + "advance_voucher",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "voucher_date"},
                {"data": "voucher_number"},
                {"data": "customer"},
                {"data": "reference_number"},
                {"data": "amount"},
                {"data": "currency_converted_amount"},
                {"data": "from_account"},
                {"data": "added_user"},
                {"data": "action"},
            ]
        });
    });
</script>
