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
                        <h3 class="box-title">List Expense Bill</h3>
                        <?php
                        if ($access_module_privilege->add_privilege == "yes") {
                            ?>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('expense_bill/add'); ?>" >Add Expense Bill</a>
                        <?php } ?>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reference No</th>
                                    <th>Supplier</th>
                                    <th>Grand Total</th>
                                    <th><?php echo 'Converted Amount (' . $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ')'; ?></th>
                                    <th>Paid Amount</th>
                                    <th>User</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="myModal" class="modal fade" role="dialog">
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

                                    <input type="hidden" name="type" id="type" value="customer">

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



    <div id="myModal" class="modal fade" role="dialog">

        <div class="modal-dialog">



            <!-- Modal content-->

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

                                    <input type="hidden" name="type" id="type" value="customer">

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
</div>

<!-- /.content-wrapper -->

<?php
$this->load->view('layout/footer');

$this->load->view('general/delete_modal');

$this->load->view('recurrence/recurrence_invoice_modal');
?>
<script>
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "report/expense_bill_reports",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "date"},
                {"data": "invoice"},
                {"data": "payee"},
                // { "data": "expense_name" },
                {"data": "grand_total"},
                {"data": "currency_converted_amount"},
                {"data": "paid_amount"},
                {"data": "added_user"},
                {"data": "action"},
            ],
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
    });
    function addToModel(id) {
        document.getElementById("salesId").value = id;
        $.ajax({
            type: "GET",
            url: base_url + "follow_up/follow/" + id,
            success: function (data)
            {
                $("#follow_up_table").html(data);
            }
        });
    }
</script>