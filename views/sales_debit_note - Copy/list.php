<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    </section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('sales_debit_note'); ?>">Sales Debit Note</a></li>
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
                        <h3 class="box-title">Sales Debit Note</h3>
                        <?php

if (in_array($sales_debit_note_module_id, $active_add))
{
    ?>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('sales_debit_note/add'); ?>" title="Add sales debit note">Add sales Debit Note </a>
                        <?php }
?>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th style="min-width: 100px;text-align: center;">Date</th>
                                    <th style="min-width: 250px;text-align: center;">Customer</th>
                                    <th style="min-width: 200px;text-align: center;"><?php echo 'Grand Total'; ?></th>
                                    <th style="min-width: 180px;text-align: center;"><?php echo 'Converted Amount (' . $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ')'; ?></th>
                                    <th style="min-width: 100px;text-align: center;"><?php echo 'Invoice Status'; ?></th>
                                    <th style="min-width: 150px;text-align: center;">User</th>
                                    <th style="min-width: 100px;text-align: center;"><?php echo ''; ?></th>
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
    <div id="myModal1" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content" id="old-model">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Update Date</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="date">Date<span class="validation-color">*</span></label>
                                    <input type="hidden" id="salesId" name="salesId" value="">
                                    <input type="hidden" name="type" id="type" value="sales_debit_note">
                                    <input type="text" style="background: #fff;" class="form-control datepicker" id="invoice_date" name="invoice_date" value="" readonly="">
                                    <span class="validation-color" id="err_date"></span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="date">Comments<span class="validation-color">*</span></label>
                                    <textarea class="form-control" id="comments" name="comments"></textarea><br>
                                    <div class="form-group text-center">
                                        <input type="submit" class="btn btn-info" id="post_notification_date" name="post_notification_date">
                                        <span class="validation-color" id="err_date"></span>
                                    </div>
                                </div>

                            </div>
                    </form>
                    <table id="follow_up_table" border="1" cellspacing ="5" class="custom_datatable table table-bordered table-striped table-hover table-responsive">
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- model ok  -->
    <div id="myModal2" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Status</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <strong>Success!</strong> Updated Follow Up date.
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php
$this->load->view('layout/footer');

// $this->load->view('sales_debit_note/pay_now_modal');
// $this->load->view('sales_debit_note/pdf_type_modal');
$this->load->view('general/delete_modal');
?>
<script>
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "sales_debit_note",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "date"},
                {"data": "customer"},
                {"data": "grand_total"},
                {"data": "converted_grand_total"},
                {"data": "invoice_status"},
                {"data": "added_user"},
                {"data": "action"},
            ]
        });

    });

</script>
