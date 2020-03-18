<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Expense Bill</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <!-- <?php
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
            ?> -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border" id="plus_btn">
                        <h3 class="box-title">Expense Bill</h3>
                        <?php
                        if (in_array($expense_bill_module_id, $active_add)) {
                            ?>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('expense_bill/add'); ?>" >Add Expense Bill</a>
                        <?php } ?>
                    </div>
                    <div id="filter">
                        <div class="box-body  box-header with-border filter_body">
                            <div class="btn-group">
                                <span><a href="#" class="btn btn-app view_btn" data-toggle="tooltip" data-placement="bottom" data-original-title="View Expense Bill"> <i class="fa fa-eye"></i> </a></span>
                                <span><a href="javascript:void(0);" class="btn btn-app edit" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Expense Bill" > <i class="fa fa-pencil"></i> </a></span>
                                <span data-backdrop="static" data-keyboard="false"><a href="#" class="btn btn-app pdf_btn" data-toggle="tooltip" target="_blank" data-placement="bottom" title="Download PDF"><i class="fa fa-file-pdf-o"></i></a></span>
                                <span data-toggle="modal" data-target="#composeMail"><a href="#" class="btn btn-app composeMail" data-id="" data-name="regular" data-toggle="tooltip" data-placement="bottom" title="Mail Expense Bill"> <i class="fa fa-envelope-o"></i> </a></span>
                                <span><a href="javascript:void(0);" class="btn btn-app payment" data-toggle="tooltip" data-placement="bottom" title="Pay Now"><i class="fa fa-credit-card"></i></a></span>
                                <span><a href="#" target="_blank" class="btn btn-app view_voucher" data-toggle="tooltip" data-placement="bottom" title="View Voucher"><i class="fa fa-eye"></i></a></span>
                                <span><form action="#" method="POST" target="_blank"><input type="hidden" name="reference_id" value="0"> <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" class="btn btn-app view_ledger" title="View Ledger"><button type="submit" class="view_ledger sales_action"><i class="fa fa-eye" aria-hidden="true"></i></button></a></form></span>

                                <span class="delete_button" data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#delete_modal" data-id="" data-path="expense_bill/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="Delete Expense Bill"> <i class="fa fa-trash-o"></i> </a></span>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th width="9px">#</th>
                                    <th>Voucher Number</th>
                                    <th>Voucher Date</th>
                                    <th>Supplier Name</th>
                                    <th>Invoice Value</th>
                                    <th>Amount Payable</th>
                                    <th>Amount Paid</th>
                                    <th>Balance Payable</th>
                                    <!-- <th>View</th> -->
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
    <!--<div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
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
                                    <input type="hidden" name="type" id="type" value="expense_bill">
                                    <input type="text" style="background: #fff;" class="form-control datepicker" id="invoice_date" name="invoice_date" readonly="">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </form>
            </div>
        </div>
    </div> -->
</div>
    <!--<div id="myModal2" class="modal fade" role="dialog">
        <div class="modal-dialog">
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
    </div> -->

<?php
$this->load->view('layout/footer');
$this->load->view('general/delete_modal');
$this->load->view('recurrence/recurrence_invoice_modal');
$this->load->view('expense_bill/compose_mail');
?>
<script>
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "expense_bill",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "expense_bill_id"},
                {"data": "invoice"},
                {"data": "date"},
                {"data": "payee"},
                {"data": "grand_total", "className" : "text-right"},
                {"data": "payable_amount", "className" : "text-right"},
                {"data": "paid_amount", "className" : "text-right"},
                {"data": "pending_amount", "className" : "text-right"},
                /*{"data": "expence_voucher_view"},*/
            ],
            "order": [[ 0, "desc" ]],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
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
                    var obj = JSON.parse(data);
                    if (obj.status = 'success') {
                        $("#myModal2").modal({show: true, backdrop: 'static', keyboard: false});
                    }
                }
            });
        });

        $(document).on('click', 'input[name="check_expense"]', function () {
            if ($(this).is(":checked")) {
                $(document).find('[name=check_expense]').prop('checked', false);
                $(this).prop('checked', true);
                $('#filter span').hide();
                if ($(this).attr('edit') != '0')
                    $('#filter .edit').attr('href', $(this).attr('edit')).parent().show();
                $('#filter .view_btn').attr('href', $(this).attr('view')).parent().show();
                if ($(this).attr('pdf') != '0')
                    $('#filter .pdf_btn').attr('href', $(this).attr('pdf')).parent().show();
                if ($(this).attr('email') != '0')
                    $('#filter .composeMail').attr('data-id', $(this).attr('email')).parent().show();
                if ($(this).val() != '')
                    $('#filter .delete_button').attr('data-id', $(this).val()).show();
                if ($(this).attr('payment') != '0')
                    $('#filter .payment').attr('href', $(this).attr('payment')).parent().show();

                var reference_id = $(this).attr('reference_id');
                if(reference_id){
                    $('#filter .view_voucher').attr('href', $(this).attr('voucher_link')).parent().show();
                    $('#filter form').attr('action', $(this).attr('ledger_action')).parent().show();
                    $('#filter form input[name=reference_id]').val(reference_id);
                }

                $('#plus_btn').hide();
                $('#filter').show();
            } else {
                $('#plus_btn').show();
                $('#filter').hide();
            }
        });
        <?php 
        $expence_bill_success = $this->session->flashdata('expence_bill_success');
        $expence_bill_payment_success = $this->session->flashdata('payment_voucher_success');
        $expence_bill_error = $this->session->flashdata('expence_bill_error');
        ?>
        var alert_success = '<?= $expence_bill_success; ?>';
        var alert_payment_success = '<?= $expence_bill_payment_success; ?>';
        var alert_failure = '<?= $expence_bill_error; ?>';
        if(alert_payment_success != ''){
            alert_d.text = alert_payment_success;
            PNotify.success(alert_d);
        }else if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });
    function addToModel(id) {
// var id = data;
        document.getElementById("salesId").value = id;
        $.ajax({
            type: "GET",
            url: base_url + "follow_up/follow/" + id,
            success: function (data)
            {
// alert(data)
// $obj = JSON.parse(data);
                $("#follow_up_table").html(data);
            }
        });
    }
</script>
