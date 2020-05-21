<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <!-- <?php
            if ($this->session->flashdata('email_send') == 'success') {
                ?>
                <div class="col-sm-12">
                    <div class="alert alert-success">
                        <button class="close" data-dismiss="alert" type="button">
                            ×
                        </button>
                        Email has been send with the attachment.
                        <div class="alerts-con"></div>
                    </div>
                </div>
            <?php } ?> -->
            <div class="col-md-12">
                <div class="box">
                <div class="box-header with-border">
                    <div id="plus_btn">                       
                            <h3 class="box-title">Sales</h3>
                            <?php
                            if (in_array($sales_module_id, $active_add)) {
                            ?>
                            <a class="btn btn-sm btn-default pull-right" href="<?php echo base_url('sales/add'); ?>">Add Sales </a>
                            <?php } ?>
                        </div>
                        <div id="filter">
                            <div class="box-header box-body filter_body"></div>
                        </div>
                    </div>                    
                    <div class="box-body">
                        <table id="list_datatable" class="table custom_datatable table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th  width="1%"> # </th>
                                    <th width="9%">Invoice Date</th>
                                    <th> Customer & Invoice Number</th>
                                    <th> Invoice Total</th>
                                    <th width="5%"> Conversion Rate</th>
                                    <th> Total Receivable</th>
                                    <th> Net Receivable</th>
                                    <th> Received Amount</th>
                                    <th width="12%"> Total Pending/ Excess Amount</th>
                                    <th> Payment status</th>
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
<!-- <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Update Date</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="date">Date<span class="validation-color">*</span></label>
                                <input type="hidden" id="salesId" name="salesId" value="">
                                <input type="hidden" name="type" id="type" value="sales">
                                <input type="text" style="background: #fff;" class="form-control datepicker" id="follow_up_date" name="follow_up_date">
                                <span class="validation-color" id="err_date"></span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="date">Comments<span class="validation-color">*</span></label>
                                <textarea class="form-control" id="comments" name="comments"></textarea>
                                <br>                                <div class="form-group text-center">
                                    <input type="submit" class="btn btn-info" id="post_notification_date" name="post_notification_date">
                                    <span class="validation-color" id="err_date"></span>
                                </div>
                            </div>
                        </div>
                </form>
                <table id="follow_up_table" border="1" cellspacing ="5" class="custom_datatable table table-bordered table-striped table-hover table-responsive"></table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
</div>

<div id="FollowUpModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Status</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <strong>Success!</strong> Updated Follow Up date.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div> -->

<div id="advance_voucher_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Advance view in sales invoice</h4>
            </div>
            <div class="modal-body">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="advance_voucher_table" class="table custom_datatable table-bordered table-striped table-hover" >
                                <thead>
                                <th>Customer </th>
                                <th>Advance Voucher</th>
                                <th>Reference Invoice</th>
                                <th>Amount</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>           
        </div>
    </div>
</div>

<div id="excess_history_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Excess History of sales invoice</h4>
            </div>
            <div class="modal-body">

                <div class="table-responsive">
                    <table id="excess_table" class="table custom_datatable table-bordered table-striped table-hover" >
                        <thead>
                        <th>Voucher Number </th>
                        <th>Voucher date</th>
                        <th>Amount</th>
                        <th>Reference Type</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>           
        </div>
    </div>
</div>

<div id="e_way_bill_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">E Way Bill</h4>
            </div>
            <div class="modal-body">
                <div id="loader">
                        <h1 class="ml8">
                            <span class="letters-container">
                                <span class="letters letters-left">
                                    <img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px">
                                </span>
                            </span>
                            <span class="circle circle-white"></span>
                            <span class="circle circle-dark"></span>
                            <span class="circle circle-container">
                                <span class="circle circle-dark-dashed"></span>
                            </span></h1>
                    </div>
                <div class="table-responsive">
                    <table id="excess_table" class="table custom_datatable table-bordered table-striped table-hover" >
                        <thead>
                        <th>E Way Bill Date </th>
                        <th>E Way Bill Number</th>
                        </thead>
                        <tbody>
                            <td>
                               <div class="input-group date">
                                    <input type="text" class="form-control datepicker" id="sales_e_way_bill_date" name="sales_e_way_bill_date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div> 
                                    <span class="validation-color" id="err_date"></span>
                            </td>
                            <td>
                               <div class="input-group">
                                    <input type="text" class="form-control" size="30" name="sales_e_way_bill_number" id="sales_e_way_bill_number">
                                </div>
                                <span class="validation-color" id="err_number"></span> 
                            </td>
                        </tbody>
                    </table>
                    <div class="modal-footer">
                <button type="button" id="e_way_bill_submit" data-id="sales_id" name="e_way_bill_submit" class="btn btn-info">
                    Submit
                </button>
                <button type="button" class="btn btn-info" class="close" data-dismiss="modal">
                    Cancel
                </button>
            </div>
                </div>
            </div>           
        </div>
    </div>
</div>


<div id="excess_amount_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Excess Amount</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Purpose<span class="validation-color">*</span></label>
                            <select class="form-control" id="voucher_type" name="voucher_type">
                                <option value="">Select Purpose</option>
                                <option value="advance">Advance</option>
                                <option value="income">Income</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label>Date<span class="validation-color">*</span></label>
                        <div class="input-group date">
                            <input type="text" class="form-control datepicker" id="voucher_date" name="voucher_date" value="">
                            <div class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Voucher Number<span class="validation-color">*</span></label>
                            <input type="text" class="form-control" name="voucher_number" id="voucher_number" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Amount<span class="validation-color">*</span></label>
                            <input type="text" class="form-control" value="" name="voucher_amount" id="voucher_amount" />
                            <input type="hidden" name="sales_id" id="sales_id" value="0">
                            <input type="hidden" name="excess_id" id="excess_id" value="0">
                            <input type="hidden" name="customer_id" id="customer_id" value="0">
                            <input type="hidden" name="advance_voucher_number" id="advance_voucher_number" value="0">
                            <input type="hidden" name="general_voucher_number" id="general_voucher_number" value="0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" id="add_advance">
                    Add
                </button>
                <button type="button" class="btn btn-info" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<?php
$this->load->view('layout/footer');
// $this->load->view('sales/pay_now_modal');
$this->load->view('sales/pdf_type_modal');
// $this->load->view('advance_voucher/advance_voucher_modal');
$this->load->view('general/delete_modal');
$this->load->view('recurrence/recurrence_invoice_modal');
$this->load->view('sales/compose_mail');
?>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script>
    $(document).ready(function () {
        var salesTable = GetAllSales();

        function GetAllSales() {
            var sales_table = $('#list_datatable').DataTable({
                "processing": true,
                "serverSide": true,
                "responsive": false,
                "ajax": {
                    "url": base_url + "sales",
                    "dataType": "json",
                    "type": "POST",
                    "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
                },
                "columns": [
                    {"data": "action"},
                    {"data": "date"},
                    /*{ "data": "invoice" },*/
                    {"data": "customer"},
                    {"data": "grand_total"},
                    {"data": "billing_currency"},
                    /*{"data" : "converted_grand_total"} ,*/
                    {"data": "total_receivable"},
                    {"data": "net_receivable", "className" : "text-right"},
                    {"data": "received_amount", "className" : "text-right"},
                    {"data": "pending_amount", "className" : "text-right"},
                    {"data": "payment_status"},
                    /*{"data": "sales_voucher_view"}*/
                ],  
                "order": [[ 0, "desc" ]],              
                "columnDefs": [ { 'orderable': false, 'targets': [4,5,6,7,8,9] }],
                 'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                    },
                });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
            $(document).find('[name=check_item]').prop('checked', false);
            return sales_table;
        }

        $('#add_advance').click(function () {
            $(this).attr('disabled', true);
            var voucher_type = $('#excess_amount_modal').find('#voucher_type').val();
            var voucher_date = $('#excess_amount_modal').find('#voucher_date').val();
            var voucher_amount = $('#excess_amount_modal').find('#voucher_amount').val();
            var excess_id = $('#excess_amount_modal').find('#excess_id').val();
            var customer_id = $('#excess_amount_modal').find('#customer_id').val();
            var sales_id = $('#excess_amount_modal').find('#sales_id').val();
            var voucher_number = $('#excess_amount_modal').find('#voucher_number').val();
            var advance_voucher_number = $('#excess_amount_modal').find('#advance_voucher_number').val();
            var general_voucher_number = $('#excess_amount_modal').find('#general_voucher_number').val();

            $.ajax({
                url: base_url + "Advance_voucher/AdvanceFromSales",
                type: "POST",
                data: {'customer_id': customer_id, sales_id: sales_id, voucher_type: voucher_type, voucher_date: voucher_date, voucher_amount: voucher_amount, excess_id: excess_id, voucher_number: voucher_number, advance_voucher_number: advance_voucher_number, general_voucher_number: general_voucher_number},
                success: function (data) {
                    alert_d.text ='successfully submited!';
                    PNotify.success(alert_d);     
                    /*alert('successfully submited!');*/
                    $('#excess_amount_modal').modal('hide');
                    $('#add_advance').attr('disabled', false);
                    salesTable.destroy();
                    salesTable = GetAllSales();
                }
            });
        });

        $("#post_notification_date").click(function (e) {
            e.preventDefault();
            var sales_id = $('#salesId').val();
            var sales_type = $('#type').val();
            var update_date = $('#follow_up_date').val();
            var comments = $('#comments').val();
            if (update_date == null || update_date == "")
            {
                $('#err_date').text('please Select Date');
                return false;
            }
            $.ajax({
                url: base_url + "follow_up/follow_up",
                type: "POST",
                data: {'sales_id': sales_id, 'sales_type': sales_type, 'update_date': update_date, 'comments': comments},
                success: function (data) {
                    var obj = JSON.parse(data);
                    if (obj.status = 'success')
                    {
                        $("#FollowUpModal").modal({backdrop: 'static', keyboard: false},'show');
                    }
                }
            });
        });

        $('#excess_amount_modal #voucher_type').change(function () {
            var val = $(this).val();
            if (val == 'advance') {
                $('#excess_amount_modal').find('#voucher_number').val($('#advance_voucher_number').val());
            } else {
                $('#excess_amount_modal').find('#voucher_number').val($('#general_voucher_number').val());
            }
        });

        $(document).on('click', '.get_excess_amount', function () {
            var sales_id = $(this).attr('data-id');
            var c_id = $(this).parent().find('[name=customer_id]').val();
            var amnt = $(this).find('[name=excess_amount]').val();
            var excess_ids = $(this).find('[name=excess_ids]').val();
            $.ajax({
                url: base_url + "Sales/getAdvanceNumber",
                type: "POST",
                dataType: 'json',
                data: {'sales_id': sales_id},
                success: function (data) {
                    $('#excess_amount_modal').find('#voucher_type').val('advance');
                    $('#excess_amount_modal').find('#voucher_date').val(data.date).attr('disabled', true);
                    $('#excess_amount_modal').find('#voucher_amount').val(amnt).attr('disabled', true);
                    $('#excess_amount_modal').find('#excess_id').val(excess_ids).attr('disabled', true);
                    /*$('#excess_amount_modal').find('#excess_id').val(data.excess_id).attr('disabled', true);*/
                    $('#excess_amount_modal').find('#customer_id').val(c_id).attr('disabled', true);
                    $('#excess_amount_modal').find('#voucher_number').val(data.advance_voucher_number).attr('disabled', true);
                    $('#excess_amount_modal').find('#advance_voucher_number').val(data.advance_voucher_number).attr('disabled', true);
                    $('#excess_amount_modal').find('#general_voucher_number').val(data.general_voucher_number).attr('disabled', true);
                    $('#excess_amount_modal').find('#sales_id').val(sales_id);
                    $('#excess_amount_modal').modal({backdrop: 'static', keyboard: false},'show');
                }
            })
        })

        $(document).on('click', '.get_advance', function () {
            var c_id = $(this).parent().find('[name=customer_id]').val();
            var sales_id = $(this).parent().find('[name=sales_id]').val();
            $.ajax({
                url: base_url + "advance_voucher/getAllAdvanceDetail",
                type: "POST",
                dataType: 'json',
                data: {'sales_id': sales_id, c_id: c_id},
                success: function (data) {
                    var tr = '';
                    if (data.length) {
                        $(data).each(function (k, v) {
                            tr += "<tr>\n\
                              <td>" + v.customer_name + "</td>\n\
                              <td>" + v.voucher_number + "</td>\n\
                              <td>" + v.reference_number + "</td>\n\
                              <td><i class='fa fa-rupee'></i> " + v.adjusted_amount + "</td>\n\
                          </tr>";
                        });
                    }
                    if (tr == '') {
                        tr += "<tr><td colspan='4'> No records found!</td></tr>";
                    }
                    $('#advance_voucher_table tbody').html(tr);
                    $('#advance_voucher_modal').modal({backdrop: 'static', keyboard: false},'show');
                }
            });
        });
        $(document).on('click', '.e_way_bill', function () {
                var sales_id = $(this).attr('data-id');
                var e_way_bill_date = $(this).attr('e_way_bill_date');
                var e_way_bill_number = $(this).attr('e_way_bill_number');
                $('#e_way_bill_modal').modal('show');
                $('#sales_e_way_bill_date').val(e_way_bill_date);
                $('#sales_e_way_bill_number').val(e_way_bill_number);
                console.log(sales_e_way_bill_date);
        });

            $('#e_way_bill_submit').click(function(){

                var sales_id = $('.e_way_bill').attr('data-id');
                var sales_e_way_bill_date = $('#sales_e_way_bill_date').val();
                var sales_e_way_bill_number = $('#sales_e_way_bill_number').val();


                   if (sales_e_way_bill_date == '' || sales_e_way_bill_number == null) {
                        $('#err_date').text('Please enter your E way bill date');
                        return false;
                    } else {
                        $('#err_date').text('');
                    }

                    if (sales_e_way_bill_number == '' || sales_e_way_bill_number == null) {
                        $('#err_number').text('Please enter your way bill number');
                        return false;
                    } else {
                        $('#err_number').text('');
                    }
                    $.ajax({
                        type:'POST',
                        url: base_url + 'sales/update_e_way_bill',
                        dataType:'JSON',
                        data: {
                            'sales_id' : sales_id,
                            'sales_e_way_bill_date' : sales_e_way_bill_date , 
                            'sales_e_way_bill_number' : sales_e_way_bill_number     
                        },
                        beforeSend: function(){
                            // Show image container
                            $("#e_way_bill_modal #loader").show();
                        },
                        success : function(data) {
                            $("#e_way_bill_modal #loader").hide();
                            $('#e_way_bill_modal').modal('hide');
                            $('.e_way_bill').attr('e_way_bill_date', sales_e_way_bill_date);
                            $('.e_way_bill').attr('e_way_bill_number', sales_e_way_bill_number);
                            $('#sales_e_way_bill_date').val('');
                            $('#sales_e_way_bill_number').val('');
                            $(document).find('[name=check_item]').trigger('change');
                        }

                    });
            });


        $(document).on('click', '.get_excess', function () {
            var c_id = $(this).parent().find('[name=customer_id]').val();
            var sales_id = $(this).attr('data-id');
            $.ajax({
                url: base_url + "sales/getAllExcessDetail",
                type: "POST",
                dataType: 'json',
                data: {'sales_id': sales_id, c_id: c_id},
                success: function (data) {
                    var tr = '';
                    if (data.length) {
                        $(data).each(function (k, v) {
                            reference_type = 'Advance';
                            if (v.reference_type == 'general_voucher')
                                reference_type = 'Income';
                            tr += "<tr>\n\
                              <td>" + v.voucher_number + "</td>\n\
                              <td>" + v.voucher_date + "</td>\n\
                              <td><i class='fa fa-rupee'></i> " + v.excess_amount + "</td>\n\
                              <td>" + reference_type + "</td>\n\
                          </tr>";//<td>" + v.reference_number + "</td>\n\
                        });
                    }
                    if (tr == '') {
                        tr += "<tr><td colspan='4'> No records found!</td></tr>";
                    }
                    $('#excess_table tbody').html(tr);
                    $('#excess_history_modal').modal({backdrop: 'static', keyboard: false},'show');
                }
            });
        });
    });

    $('body').on('change', 'input[type="checkbox"][name="check_item"]', function () {
        var i = 0;
        $.each($("input[name='check_item']:checked"), function () {
            i++;
        });
        if (i == 1) {
            var row = $("input[name='check_item']:checked").closest("tr");
            var action_button = row.find('.action_button').html();

            $('#plus_btn').hide();
            $('.filter_body').html(action_button);
            $('#filter').show();
        } else {
            $('#plus_btn').show();
            $('#filter').hide();
            $('.filter_body').html('');
        }
    });
    function addToModel(id) {
        document.getElementById("salesId").value = id;
        $.ajax({
            type: "GET",
            url: base_url + "follow_up/follow/" + id,
            success: function (data) {
                // alert(data)
                // $obj = JSON.parse(data);
                $("#follow_up_table").html(data);
                $('#FollowUpModal').modal({backdrop: 'static', keyboard: false},'show');
                /*$('#myModal').modal('show');*/
            }
        });
    }
    <?php 
        $sales_success = $this->session->flashdata('sales_success');
        $sales_receipt_voucher_success = $this->session->flashdata('receipt_voucher_sales');
        $sales_error = $this->session->flashdata('sales_error');
    ?>
    var alert_success = '<?= $sales_success; ?>';
    var alert_receipt_success = '<?= $sales_receipt_voucher_success; ?>';
    var alert_failure = '<?= $sales_error; ?>';
    if(alert_receipt_success != ''){
        alert_d.text = alert_receipt_success;
        PNotify.success(alert_d);
    }else if(alert_success != ''){
        alert_d.text = alert_success;
        PNotify.success(alert_d);
    }else if(alert_failure != ''){
        alert_d.text = alert_failure;
        PNotify.error(alert_d);
    }
    
</script>