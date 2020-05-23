<?phpdefined('BASEPATH') OR exit('No direct script access allowed');$this->load->view('layout/header');?><div class="content-wrapper">       <div class="fixed-breadcrumb">        <ol class="breadcrumb abs-ol">            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>            <li><a href="<?php echo base_url('sales_credit_note'); ?>">Sales Credit Note</a></li>        </ol>    </div>    <section class="content mt-50">        <div class="row">            <?php            if ($this->session->flashdata('email_send') == 'success') {                ?>                <div class="col-sm-12">                    <div class="alert alert-success">                        <button class="close" data-dismiss="alert" type="button">×</button>                        Email has been send with the attachment.                        <div class="alerts-con"></div>                    </div>                </div>                <?php            }            ?>            <div class="col-md-12">                <div class="box">                    <div id="plus_btn">                        <div class="box-header with-border">                            <h3 class="box-title">Sales Credit Note/Sales Return</h3>                            <?php                            if (in_array($sales_credit_note_module_id, $active_add)) {                                ?>                                <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('sales_credit_note/add'); ?>">Add Sales Credit Note</a>                            <?php } ?>                        </div>                    </div>                    <div id="filter">                        <div class="box-header with-border box-body filter_body"></div>                    </div>                    <div class="box-body">                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >                            <thead>                                <tr>                                    <th width="9px">#</th>                                    <th>Invoice Date</th>                                    <th>Invoice Number</th>                                    <th>Customer</th>                                    <th>Billing Currency</th>                                    <th><?php echo 'Grand Total'; ?></th>                                    <!-- <th> View </th> -->                                    <!-- <th><?php echo 'Converted Amount (' . $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ')'; ?></th>                                    <th><?php echo 'Invoice Status'; ?></th> -->                                </tr>                            </thead>                            <tbody></tbody>                        </table>                    </div>                </div>            </div>        </div>    </section></div><div id="e_way_bill_modal" class="modal fade" role="dialog">    <div class="modal-dialog">        <div class="modal-content">            <div class="modal-header">                <button type="button" class="close" data-dismiss="modal">                    &times;                </button>                <h4 class="modal-title">E Way Bill</h4>            </div>            <div class="modal-body">                <div id="loader">                        <h1 class="ml8">                            <span class="letters-container">                                <span class="letters letters-left">                                    <img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px">                                </span>                            </span>                            <span class="circle circle-white"></span>                            <span class="circle circle-dark"></span>                            <span class="circle circle-container">                                <span class="circle circle-dark-dashed"></span>                            </span></h1>                </div>                <div class="table-responsive">                    <table id="excess_table" class="table custom_datatable table-bordered table-striped table-hover" >                        <thead>                        <th>E Way Bill Date </th>                        <th>E Way Bill Number</th>                        </thead>                        <tbody>                            <td>                               <div class="input-group date">                                    <input type="text" class="form-control datepicker" id="sales_credit_note_e_way_bill_date" name="sales_credit_note_e_way_bill_date">                                    <div class="input-group-addon">                                        <i class="fa fa-calendar"></i>                                    </div>                                </div>                                     <span class="validation-color" id="err_date"></span>                            </td>                            <td>                               <div class="input-group">                                    <input type="text" class="form-control" size="30" name="sales_credit_note_e_way_bill_number" id="sales_credit_note_e_way_bill_number">                                </div>                                <span class="validation-color" id="err_number"></span>                             </td>                        </tbody>                    </table>                    <div class="modal-footer">                <button type="button" id="e_way_bill_submit" name="e_way_bill_submit" class="btn btn-info">                    Submit                </button>                <button type="button" class="btn btn-info" class="close" data-dismiss="modal">                    Cancel                </button>            </div>                </div>            </div>                   </div>    </div></div> <div id="myModal1" class="modal fade" role="dialog">    <div class="modal-dialog">        <div class="modal-content" id="old-model">            <div class="modal-header">                <button type="button" class="close" data-dismiss="modal">&times;</button>                <h4 class="modal-title">Update Date</h4>            </div>            <div class="modal-body">                <div class="row">                    <div class="col-sm-12">                        <div class="form-group">                            <label for="date">Date<span class="validation-color">*</span></label>                            <input type="hidden" id="salesId" name="salesId" value="">                            <input type="hidden" name="type" id="type" value="sales_credit_note">                            <input type="text" class="form-control datepicker" id="invoice_date" name="invoice_date" value="" readonly="">                            <span class="validation-color" id="err_date"></span>                        </div>                    </div>                    <div class="col-sm-12">                        <div class="form-group">                            <label for="date">Comments<span class="validation-color">*</span></label>                            <textarea class="form-control" id="comments" name="comments"></textarea><br>                            <div class="form-group text-center">                                <input type="submit" class="btn btn-info" id="post_notification_date" name="post_notification_date">                                <span class="validation-color" id="err_date"></span>                            </div>                        </div>                    </div>                                   <table id="follow_up_table" border="1" cellspacing ="5" class="custom_datatable table table-bordered table-striped table-hover table-responsive">                    </table>                </div>            </div>            <div class="modal-footer">                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>            </div>        </div>    </div></div><div id="myModal2" class="modal fade" role="dialog">    <div class="modal-dialog">        <div class="modal-content">            <div class="modal-header">                <button type="button" class="close" data-dismiss="modal">&times;</button>                <h4 class="modal-title">Status</h4>            </div>            <div class="modal-body">                <div class="alert alert-success">                    <strong>Success!</strong> Updated Follow Up date.                </div>            </div>            <div class="modal-footer">                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>            </div>        </div>    </div></div><?php$this->load->view('layout/footer');// $this->load->view('sales_credit_note/pay_now_modal');$this->load->view('sales/pdf_type_modal');$this->load->view('general/delete_modal');?><script>    $(document).ready(function () {        $('#list_datatable').DataTable({            "processing": true,            "serverSide": true,            "responsive": true,            "ajax": {                "url": base_url + "sales_credit_note",                "dataType": "json",                "type": "POST",                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}            },            "columns": [                {"data": "action"},                {"data": "date"},                {"data": "invoice"},                {"data": "customer"},                {"data": "billing_currency"},                {"data": "grand_total"},                /*{"data": "sales_cn_voucher_view"}*/            ],            "order": [[ 0, "desc" ]],            "columnDefs": [ { 'orderable': false, 'targets': [4] }],             'language': {                'loadingRecords': '&nbsp;',                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'                },            });             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});                         $(document).on('click', '.credit_note_e_way_bill', function () {                var sales_id = $(this).attr('data-id');                var credit_note_e_way_bill_date = $(this).attr('credit_note_e_way_bill_date');                var credit_note_e_way_bill_number = $(this).attr('credit_note_e_way_bill_number');                $('#e_way_bill_modal').modal('show');                $('#sales_credit_note_e_way_bill_date').val(credit_note_e_way_bill_date);                $('#sales_credit_note_e_way_bill_number').val(credit_note_e_way_bill_number);            });            $('#e_way_bill_submit').click(function(){                var sales_credit_note_id = $('.credit_note_e_way_bill').attr('data-id');                var sales_credit_note_e_way_bill_date = $('#sales_credit_note_e_way_bill_date').val();                var sales_credit_note_e_way_bill_number = $('#sales_credit_note_e_way_bill_number').val();                   if (sales_credit_note_e_way_bill_date == '' || sales_credit_note_e_way_bill_date == null) {                        $('#err_date').text('Please enter your E way bill date');                        return false;                    } else {                        $('#err_date').text('');                    }                    if (sales_credit_note_e_way_bill_number == '' || sales_credit_note_e_way_bill_number == null) {                        $('#err_number').text('Please enter your way bill number');                        return false;                    } else {                        $('#err_number').text('');                    }                    $.ajax({                        type:'POST',                        url: base_url + 'sales_credit_note/update_e_way_bill',                        dataType:'JSON',                        data: {                            'sales_credit_note_id' : sales_credit_note_id,                            'sales_credit_note_e_way_bill_date' : sales_credit_note_e_way_bill_date ,                             'sales_credit_note_e_way_bill_number' : sales_credit_note_e_way_bill_number                             },                        beforeSend: function(){                            // Show image container                            $("#e_way_bill_modal #loader").show();                        },                        success : function(data) {                            $("#e_way_bill_modal #loader").hide();                            $('#e_way_bill_modal').modal('hide');                            $(".credit_note_e_way_bill[data-id=" + sales_credit_note_id + "]").attr('credit_note_e_way_bill_date', sales_credit_note_e_way_bill_date);                            $(".credit_note_e_way_bill[data-id=" + sales_credit_note_id + "]").attr('credit_note_e_way_bill_number', sales_credit_note_e_way_bill_number);                            $('#sales_credit_note_e_way_bill_date').val('');                            $('#sales_credit_note_e_way_bill_number').val('');                            $(document).find('[name=check_item]').trigger('change');                        }                    });            });        <?php         $sales_cn_success = $this->session->flashdata('sales_cn_success');        $sales_cn_error = $this->session->flashdata('sales_cn_error');        ?>        var alert_success = '<?= $sales_cn_success; ?>';        var alert_failure = '<?= $sales_cn_error; ?>';        if(alert_success != ''){            alert_d.text = alert_success;            PNotify.success(alert_d);        }else if(alert_failure != ''){            alert_d.text = alert_failure;            PNotify.error(alert_d);        }    });</script>