<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">Purchase</li>
            </ol>
        </h5>
    </section>
    <!-- Main content -->
    <section class="content">
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
                        <h3 class="box-title">Purchase</h3>
                        <?php
                        if ($access_module_privilege->add_privilege == "yes")
                        {
                            ?>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('purchase/add'); ?>" title="Add purchase">Add purchase </a>
                        <?php } ?>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" style="overflow-y: auto;">
                        <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th><?php echo 'Grand Total' . '(' . $currency[0]->currency_symbol . ')'; ?></th>
                                    <th><?php echo 'Paid Amount' . '(' . $currency[0]->currency_symbol . ')'; ?></th>
                                    <th><?php echo 'Payment Status'; ?></th>
                                    <th>User</th>
                                    <th><?php echo 'Action'; ?></th>
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
// $this->load->view('purchase/pay_now_modal');
// $this->load->view('purchase/pdf_type_modal');
$this->load->view('general/delete_modal');
?>

<script>
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": base_url + "notification/partial_purchase",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "date"},
                {"data": "supplier"},
                {"data": "grand_total"},
                {"data": "paid_amount"},
                {"data": "payment_status"},
                {"data": "added_user"},
                {"data": "action"},
            ]

        });
    });
</script>
