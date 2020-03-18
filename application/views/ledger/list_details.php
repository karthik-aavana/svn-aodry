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
                <li><a href="<?php echo base_url('ledger'); ?>">Ledger List</a></li>
                <li class="active">Ledger Details</li>
            </ol>
        </h5>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Ledger Details</h3>/ <b><?php echo $title[0]->ledger_title; ?></b>

                    </div>
                    <div class="well">
                        <div class="box-body">
                            <input type="hidden" name="idd" id="idd" value="<?= $idd ?>">
                            <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">Voucher Date</th>
                                        <th style="text-align: center;">Voucher Number</th>
                                        <th style="text-align: center;">Reference Number</th>
                                        <th style="text-align: center;">Particulars Title</th>
                                        <th style="text-align: center;">Voucher Type</th>
                                        <th style="text-align: center;">Dr Amount</th>
                                        <th style="text-align: center;">Cr Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
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
?>
<script>
    $(document).ready(function () {
        var idd = $('#idd').val();
        $('#list_datatable').DataTable({
            dom: "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-6'i><'col-sm-6'p>>",
                "pageLength": 50,
                "scrollY": "400px",
                "processing": true,
                "serverSide": true,
                "responsive": false,
            "ajax": {
                "url": base_url + "ledger/view_ledger_details/" + idd,
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                }
            },
            "columns": [
                {"data": "voucher_date"},
                {"data": "voucher_number"},
                {"data": "reference_number"},
                {"data": "particulars_title"},
                {"data": "voucher_type"},
                {"data": "dr_amount"},
                {"data": "cr_amount"}
            ]
        });
    });
</script>
