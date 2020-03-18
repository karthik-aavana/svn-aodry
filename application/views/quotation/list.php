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
                    <button class="close" data-dismiss="alert" type="button">×</button>
                    Email has been send with the attachment.
                    <div class="alerts-con"></div>
                </div>
            </div>
            <?php } ?> -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div id="plus_btn">
                            <h3 class="box-title">Quotation</h3>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('quotation/add'); ?>">Add Quotation</a>
                        </div>
                        <div id="filter">
                            <div class="box-header box-body filter_body"></div>
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="3%">#</th>
                                    <th>Invoice Date</th>
                                    <th>Customer & Invoice Number</th>
                                    <th>Grand Total</th>
                                    <!-- <th><?php echo 'Converted Amount (' . $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ')'; ?></th> -->
                                    <th>Status</th>
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
/* $this->load->view('recurrence/recurrence_invoice_modal'); */
$this->load->view('sales/pdf_type_modal');
$this->load->view('quotation/compose_mail');
?>
<script>
$(document).ready(function() {
    $('#list_datatable').DataTable({
        "processing": true,
        "serverSide": true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
        },
        "ajax": {
            "url": base_url + "quotation",
            "dataType": "json",
            "type": "POST",
            "data": { '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>' }
        },
        "columns": [
            { "data": "action" },
            { "data": "date" },
            { "data": "customer" },
            { "data": "grand_total", "className" : "text-right"},
            //{"data" : "converted_grand_total"} ,
            { "data": "payment_status" }
        ],
        "order": [[ 0, "desc" ]],
         "columnDefs": [ { 'orderable': false, 'targets': [4] }],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});

    anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});

    <?php 
        $quotation_success = $this->session->flashdata('quotation_success');
        $quotation_error = $this->session->flashdata('quotation_error');
    ?>
    var alert_success = '<?= $quotation_success; ?>';
    var alert_failure = '<?= $quotation_error; ?>';
    if(alert_success != ''){
        alert_d.text = alert_success;
        PNotify.success(alert_d);
    }else if(alert_failure != ''){
        alert_d.text = alert_failure;
        PNotify.error(alert_d);
    }    
});
</script>