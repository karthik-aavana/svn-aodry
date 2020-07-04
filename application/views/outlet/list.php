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
                            <h3 class="box-title">outlet</h3>
                            <?php
                            if (in_array($outlet_module_id, $active_add)) {
                            ?>
                            <a class="btn btn-sm btn-default pull-right" href="<?php echo base_url('outlet/add'); ?>">Add outlet </a>
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
                                    <th>Invoice Number</th>
                                    <th>Branch</th>
                                    <th> Invoice Total</th>
                                    <th> Transfer status</th>
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
                                    <input type="text" class="form-control datepicker" id="outlet_e_way_bill_date" name="outlet_e_way_bill_date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div> 
                                    <span class="validation-color" id="err_date"></span>
                            </td>
                            <td>
                               <div class="input-group">
                                    <input type="text" class="form-control" size="30" name="outlet_e_way_bill_number" id="outlet_e_way_bill_number">
                                </div>
                                <span class="validation-color" id="err_number"></span> 
                            </td>
                        </tbody>
                    </table>
                    <div class="modal-footer">
                <button type="button" id="e_way_bill_submit" data-id="outlet_id" name="e_way_bill_submit" class="btn btn-info">
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

<?php
$this->load->view('layout/footer');
/*$this->load->view('outlet/pdf_type_modal');*/
$this->load->view('general/delete_modal');
$this->load->view('outlet/compose_mail');
?>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script>
    $(document).ready(function () {
        var outletTable = GetAlloutlet();

        function GetAlloutlet() {
            var outlet_table = $('#list_datatable').DataTable({
                "processing": true,
                "serverSide": true,
                "iDisplayLength": 15,
                "lengthMenu": [ [15, 25, 50,100, -1], [15, 25, 50,100, "All"] ],
                "responsive": false,
                "ajax": {
                    "url": base_url + "outlet",
                    "dataType": "json",
                    "type": "POST",
                    "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
                },
                "columns": [
                    {"data": "action"},
                    {"data": "date"},
                    {"data": "invoice" },
                    {"data": "branch_name"},
                    {"data": "grand_total"},
                    {"data": "payment_status"},
                ],  
                "order": [[ 0, "desc" ]],              
                "columnDefs": [ { 'orderable': false, 'targets': [4,5] }],
                 'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                    },
                });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
            $(document).find('[name=check_item]').prop('checked', false);
            return outlet_table;
        }

        $("#post_notification_date").click(function (e) {
            e.preventDefault();
            var outlet_id = $('#outletId').val();
            var outlet_type = $('#type').val();
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
                data: {'outlet_id': outlet_id, 'outlet_type': outlet_type, 'update_date': update_date, 'comments': comments},
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
        $(document).on('click', '.e_way_bill', function () {
            var outlet_id = $(this).attr('data-id');
            var e_way_bill_date = $(this).attr('e_way_bill_date');
            var e_way_bill_number = $(this).attr('e_way_bill_number');
            $('#e_way_bill_modal').modal('show');
            $('#outlet_e_way_bill_date').val(e_way_bill_date);
            $('#outlet_e_way_bill_number').val(e_way_bill_number);
            console.log(outlet_e_way_bill_date);
        });

        $('#e_way_bill_submit').click(function(){

            var outlet_id = $('.e_way_bill').attr('data-id');
            var outlet_e_way_bill_date = $('#outlet_e_way_bill_date').val();
            var outlet_e_way_bill_number = $('#outlet_e_way_bill_number').val();


               if (outlet_e_way_bill_date == '' || outlet_e_way_bill_number == null) {
                    $('#err_date').text('Please enter your E way bill date');
                    return false;
                } else {
                    $('#err_date').text('');
                }

                if (outlet_e_way_bill_number == '' || outlet_e_way_bill_number == null) {
                    $('#err_number').text('Please enter your way bill number');
                    return false;
                } else {
                    $('#err_number').text('');
                }
                $.ajax({
                    type:'POST',
                    url: base_url + 'outlet/update_e_way_bill',
                    dataType:'JSON',
                    data: {
                        'outlet_id' : outlet_id,
                        'outlet_e_way_bill_date' : outlet_e_way_bill_date , 
                        'outlet_e_way_bill_number' : outlet_e_way_bill_number     
                    },
                    beforeSend: function(){
                        // Show image container
                        $("#e_way_bill_modal #loader").show();
                    },
                    success : function(data) {
                        $("#e_way_bill_modal #loader").hide();
                        $('#e_way_bill_modal').modal('hide');
                        $('.e_way_bill').attr('e_way_bill_date', outlet_e_way_bill_date);
                        $('.e_way_bill').attr('e_way_bill_number', outlet_e_way_bill_number);
                        $('#outlet_e_way_bill_date').val('');
                        $('#outlet_e_way_bill_number').val('');
                        $(document).find('[name=check_item]').trigger('change');
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
        document.getElementById("outletId").value = id;
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
        $outlet_success = $this->session->flashdata('outlet_success');
        $outlet_receipt_voucher_success = $this->session->flashdata('receipt_voucher_outlet');
        $outlet_error = $this->session->flashdata('outlet_error');
    ?>
    var alert_success = '<?= $outlet_success; ?>';
    var alert_receipt_success = '<?= $outlet_receipt_voucher_success; ?>';
    var alert_failure = '<?= $outlet_error; ?>';
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