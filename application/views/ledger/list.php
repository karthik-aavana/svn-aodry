<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">Ledgers</li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Ledger</h3>
                        <?php
                        if (in_array($accounts_module_id, $active_add)) {
                            ?>
                            <a data-toggle="modal" data-target="#add_ledger_modal" class="add_ledger btn btn-sm btn-info pull-right" href="">Add Ledger</a>
                        <?php } ?>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">
                           <thead>
                                <tr>
                                    <th style="text-align: center;">Ledger Title</th>
                                    <th style="text-align: center;">Opening Balance</th>
                                    <th style="text-align: center;">Debit</th>
                                    <th style="text-align: center;">Credit</th>
                                    <th style="text-align: center;">Closing Balance</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td id="total_list_record1"></td>
                                    <td id="total_list_record2"></td>
                                    <td id="total_list_record3"></td>
                                    <td id="total_list_record4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
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
$this->load->view('ledger/edit_modal');
$this->load->view('ledger/add_modal');
?>
<script>
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax":
                    {
                        "url": base_url + "ledger",
                        "dataType": "json",
                        "type": "POST",
                        "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
                        "dataSrc": function (result) {
                            if (result.total_list_record[0].tot_cr != 0)
                            {
                                var tfoot = 'Total Credit Amount: <br>\r\n<b>' + parseFloat(result.total_list_record[0].tot_cr).toFixed(2) + '</b><br>\n\r\n';
                                $('#total_list_record1').html(tfoot);
                            }
                            if (result.total_list_record[0].tot_dr != 0)
                            {
                                var tfoot = 'Total Debit Amount: <br>\r\n<b>' + parseFloat(result.total_list_record[0].tot_cr).toFixed(2) + '</b><br>\n\r\n';
                                $('#total_list_record2').html(tfoot);
                            }
                            if (result.total_list_record[0].tot_open != 0)
                            {
                                var tfoot = 'Total Opening Balance: <br>\r\n<b>' + parseFloat(result.total_list_record[0].tot_open).toFixed(2) + '</b><br>\n\r\n';
                                $('#total_list_record3').html(tfoot);
                            }
                            if (result.total_closing_balance != 0)
                            {
                                var tfoot = 'Total Closing Balance: <br>\r\n<b>' + parseFloat(result.total_closing_balance).toFixed(2) + '</b><br>\n\r\n';
                                $('#total_list_record4').html(tfoot);
                            }
                            return result.data;
                        }
                    },
            "columns": [
                {"data": "ledger_title"},
                {"data": "opening_balance"},
                {"data": "dr_amount"},
                {"data": "cr_amount"},
                {"data": "closing_balance"},
                {"data": "action"}
            ],
            "columnDefs": [{
                    "targets": "_all",
                    "orderable": false
                }]
        });
    });
</script>