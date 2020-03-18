<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('purchase'); ?>">Purchase</a></li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <?php
            if ($this->session->flashdata('email_send') == 'success') {
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
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Purchase</h3>
                        <?php
                        if ($access_module_privilege->add_privilege == "yes") {
                            ?>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('purchase/add'); ?>">Add purchase </a>
                        <?php } ?>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th style="min-width: 100px;text-align: center;">Party</th>
                                    <th style="min-width: 100px;text-align: center;">Invoice Number</th>
                                    <th style="min-width: 150px;text-align: center;">Invoice Date</th>
                                    <th style="min-width: 100px;text-align: center;">Invoice Amount</th>
                                    <th style="min-width: 100px;text-align: center;"><?php echo 'Paid Amount'; ?></th>
                                    <th style="min-width: 100px;text-align: center;"><?php echo 'Balance Payable'; ?></th>
                                    <th style="min-width: 100px;text-align: center;">Payable Date</th>
                                    <th style="min-width: 150px;text-align: center;"><?php echo 'Due Days'; ?></th>
                                    <th style="min-width: 100px;text-align: center;"><?php echo ''; ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div id="myModal1" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" id="old-model">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Date</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="date">Date<span class="validation-color">*</span></label>
                            <input type="hidden" id="salesId" name="salesId" value="">
                            <input type="hidden" name="type" id="type" value="supplier">
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
                    <table id="follow_up_table" border="1" cellspacing ="5" class="custom_datatable table table-bordered table-striped table-hover table-responsive">
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
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
                "url": base_url + "report/purchase_report",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    'filter_customer_name': $('#filter_customer_name').val(),
                    'filter_invoice_number': $('#filter_invoice_number').val(),
                    'filter_from_date': $('#filter_from_date').val(),
                    'filter_to_date': $('#filter_to_date').val(),
                    'filter_invoice_amount': $('#filter_invoice_amount').val(),
                    'filter_received_amount': $('#filter_received_amount').val(),
                    'filter_pending_amount': $('#filter_pending_amount').val(),
                    'filter_payment_status': $('#filter_payment_status').val(),
                    'filter_receivable_date': $('#filter_receivable_date').val(),
                    'filter_due': $('#filter_due').val(),
                }
            },
            "columns": [
                {"data": "supplier"},
                {"data": "invoice"},
                {"data": "date"},
                {"data": "grand_total"},
                {"data": "currency_converted_amount"},
                {"data": "paid_amount"},
                {"data": "payment_status"},
                {"data": "due_date"},
                {"data": "action"},
            ],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
        var table = $('#list_datatable').DataTable();
        table.data(0);
        $("#post_notification_date").click(function (e) {
            e.preventDefault();
            var sales_id = $('#salesId').val();
            var sales_type = $('#type').val();
            var update_date = $('#invoice_date').val();
            var comments = $('#comments').val();
            if (update_date == null || update_date == "") {
                $('#err_date').text('please Select Date');
                return false;
            }
            $.ajax({
                url: base_url + "follow_up/follow_up",
                type: "POST",
                data: {'sales_id': sales_id, 'sales_type': sales_type, 'update_date': update_date, 'comments': comments},
                success: function (data) {
                    // $('#myModal2').trigger();
                    var obj = JSON.parse(data);
                    if (obj.status = 'success') {
                        $("#myModal2").modal({show: true, backdrop: 'static', keyboard: false});
                    }
                }
            });
        });
    });
    function addToModel(id) {
        document.getElementById("salesId").value = id;
        $.ajax({
            type: "GET",
            url: base_url + "follow_up/follow_purchase/" + id,
            success: function (data)
            {
                // alert(data)
                // $obj = JSON.parse(data);
                $("#follow_up_table").html(data);
            }
        });
    }
</script>