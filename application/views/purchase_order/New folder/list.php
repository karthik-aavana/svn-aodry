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
            <li class="active">Purchase Order List</li>
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
                        <h3 class="box-title">Purchase Order</h3>
                        <?php
                        if (in_array($purchase_order_module_id, $active_delete))
                        {
                            ?>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('purchase_order/add'); ?>" title="Add Sales">Add Purchase Order </a>
                        <?php } ?>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th style="min-width: 100px;text-align: center;">Date</th>
                                    <th style="min-width: 250px;text-align: center;">Supplier</th>
                                    <th style="min-width: 200px;text-align: center;"><?php echo 'Grand Total'; ?></th>
                                    <th style="min-width: 200px;text-align: center;"><?php echo 'Converted Amount (' . $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ')'; ?></th>
                                    <th style="min-width: 150px;text-align: center;"><?php echo 'Status'; ?></th>
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
                "url": base_url + "purchase_order",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "date"},
                {"data": "supplier"},
                {"data": "grand_total"},
                {"data": "currency_converted_amount"},
                {"data": "status"},
                {"data": "added_user"},
                {"data": "action"},
            ]

        });
    });
</script>
