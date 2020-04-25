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
                    <div id="plus_btn">
                        <div class="box-header with-border">
                            <h3 class="box-title">Purchase</h3>
                            <?php
                            if (in_array($purchase_module_id, $active_add)) {
                            ?>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('purchase/add'); ?>">Add Purchase </a>
                            <?php } ?>
                        </div>
                    </div>                                     
                    <div id="filter">
                        <div class="box-header with-border box-body filter_body">
                        </div>
                    </div>                    
                    <div class="box-body">
                        <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th> # </th>
                                    <th width="8.5%">Invoice Date</th>
                                    <th> Customer & Invoice Number</th>
                                    <th> Invoice Total</th>
                                    <th> Total Payable</th>
                                    <th> Net Payable</th>
                                    <th> Paid Amount</th>
                                    <th width="14%"> Total Pending Amount</th>
                                    <th> Payment status</th>
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
    <!--<div id="myModal1" class="modal fade" role="dialog">
        <div class="modal-dialog">
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
                                    <input type="hidden" name="type" id="type" value="purchase">
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
    <div id="myModal2" class="modal fade" role="dialog">
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
    </div>   -->
<?php
$this->load->view('layout/footer');
// $this->load->view('purchase/pay_now_modal');
// $this->load->view('purchase/pdf_type_modal');
$this->load->view('general/delete_modal');
?>
<script>
    $(document).ready(function () {
        $('body').on('change', 'input[type="checkbox"][name="check_item"]', function () {
            var i = 0;
            $.each($("input[name='check_item']:checked"), function () {
                i++;
            });
            if (i == 1)
            {
                var row = $("input[name='check_item']:checked").closest("tr");
                var action_button = row.find('.action_button').html();
                $('#plus_btn').hide();
                $('.filter_body').html(action_button);
                $('#filter').show();
            } else
            {
                $('#plus_btn').show();
                $('#filter').hide();
                $('.filter_body').html('');
            }
        });
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": base_url + "purchase",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "date"},
                {"data": "supplier"},
                {"data": "grand_total", "className" : "text-right"},
                /*{"data" : "converted_grand_total"} ,*/
                {"data": "total_receivable", "className" : "text-right"},
                {"data": "net_receivable", "className" : "text-right"},
                {"data": "received_amount", "className" : "text-right"},
                {"data": "pending_amount", "className" : "text-right"},
                {"data": "payment_status"},
                /*{"data": "purchase_voucher_view"},*/
                        // {"data": "added_user"},                
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
                    // $('#myModal2').trigger();
                    var obj = JSON.parse(data);
                    if (obj.status = 'success') {
                        $("#myModal2").modal({show: true, backdrop: 'static', keyboard: false});
                    }
                }
            })
        })
        <?php 
        $purchase_success = $this->session->flashdata('purchase_success');
        $purchase_payment_voucher_success = $this->session->flashdata('payment_voucher_purchase_success');
        $purchase_order_success = $this->session->flashdata('purchase_order_success');
        $purchase_error = $this->session->flashdata('purchase_error');
        ?>
        var alert_success = '<?= $purchase_success; ?>';
        var alert_payment_success = '<?= $purchase_payment_voucher_success; ?>';
        var alert_order_success = '<?= $purchase_order_success; ?>';
        var alert_failure = '<?= $purchase_error; ?>';
        if(alert_order_success != ''){
            alert_d.text = alert_order_success;
            PNotify.success(alert_d);
        }else if(alert_payment_success != ''){
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
