<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">	
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li>
                <a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                Receipt Voucher
            </li>
        </ol>
    </div>
    <section class="content mt-50">
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
                    <div id="plus_btn">
                        <div class="box-header with-border">
                            <h3 class="box-title">Receipt Voucher</h3>
                            <?php
                            if (in_array($receipt_voucher_module_id, $active_add)) {
                                ?>
                               <!--  <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('advance_voucher/add'); ?>">Add Advance voucher </a> -->
                                <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('receipt_voucher/add'); ?>">Add Receipt Voucher </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="filter">
                        <div class="box-header with-border box-body filter_body">
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover" >
                            <thead>
                                <tr>
                                    <th width="3%">#</th>
                                    <th width="16%">Voucher Date</th>    
                                    <th width="16%">Voucher Number</th>
                                    <th>Customer</th>
                                    <th>Reference Number</th>
                                    <th>Receipt Amount</th>
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
<?php
$this->load->view('layout/footer');
$this->load->view('general/delete_modal');
$this->load->view('receipt_voucher/compose_mail');
?>
<script>
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "receipt_voucher",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "voucher_date"},
                {"data": "voucher_number"},
                {"data": "customer"},
                {"data": "reference_number"},
                {"data": "amount", "className" : "text-right"},
                        /*{"data": "currency_converted_amount"},*/
                        /*{"data": "from_account"},
                         {"data": "added_user"},*/
            ],
            "order": [[ 0, "desc" ]],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
        <?php 
        $receipt_voucher_success = $this->session->flashdata('receipt_voucher_success');
        $receipt_voucher_error = $this->session->flashdata('receipt_voucher_error');
        ?>
        var alert_success = '<?= $receipt_voucher_success; ?>';
        var alert_failure = '<?= $receipt_voucher_error; ?>';
        if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });
</script>