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
                <li><a href="<?php echo base_url('sales'); ?>">Sales</a></li>
                <li class="active">Advance Voucher List</li>
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
                        <h3 class="box-title">Advance Voucher</h3>
                        <div align="center">
                            <h4><b>Customer: <?php echo $customer[0]->customer_name; ?></b></h4>
                            <h4><b>Sales Invoice Number: <?php echo $sales_invoice_number; ?></b></h4>
                            <h4><b>Paid Amount: <?php echo $sales_paid_amount; ?></b></h4>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <input type="hidden" name="s_id" id="s_id" value="<?php echo $sales_id; ?>">
                    <div class="box-body" style="overflow-y: auto;">
                        <table id="advance_list_datatable" class="table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th>Voucher Date</th>
                                    <th>Voucher No</th>
                                    <th>Invoice Number</th>
                                    <th><?php echo 'Grand Total '; ?></th>

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
// $this->load->view('sales/pay_now_modal');
// $this->load->view('sales/pdf_type_modal');
// $this->load->view('advance_voucher/advance_voucher_modal');
$this->load->view('general/delete_modal');
?>

<script>
    $(document).ready(function () {
        var s_id = $('#s_id').val();

        $('#advance_list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": base_url + "advance_voucher/show_advance_voucher" + "/" + s_id,
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "voucher_date"},
                {"data": "voucher_number"},
                {"data": "reference_number"},
                {"data": "grand_total"},
                {"data": "action"},
            ]

        });
    });
</script>
