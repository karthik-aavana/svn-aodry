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
                Customer
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <!-- <?php
             if(!@$bulk_error){
            if ($this->session->flashdata('email_send') == 'success') { ?>
                <div class="col-sm-12">
                    <div class="alert alert-danger">
                        <button class="close" data-dismiss="alert" type="button">×</button>
                        Email has been send with the attachment.
                        <div class="alerts-con"></div>
                    </div>
                </div>
            <?php }
            } ?> -->
            <div class="col-md-12">
                <div class="box">
                    <div id="plus_btn">
                        <div class="box-header with-border">
                            <!-- <?php if(@$bulk_success){?>
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert">x</button>
                                <strong>Success!</strong> <?=$bulk_success;?>
                            </div>
                        <?php } ?>
                        <?php if(@$bulk_error){?>
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">x</button>
                                <strong>Error!</strong> <?=$bulk_error;?>                                
                            </div>
                        <?php } ?> -->
                         <h3 class="box-title">Customer</h3>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('customer/add'); ?>">Add Customer</a>
                            <span><a href="<?php echo base_url("assets/excel/CustomerDemo.csv") ?>"  class="btn btn-sm btn-info pull-right" data-toggle="tooltip" data-placement="bottom" data-custom-class="tooltip-primary" data-original-title="Download Customer CSV Demo File" download><i class="fa fa-download"></i></a></span>

                            <span><a data-toggle="tooltip" data-placement="bottom" data-custom-class="tooltip-primary" class="btn btn-sm btn-info upload_customer_popup pull-right" data-original-title="Upload Customer" class=""><i class="fa fa-cloud-upload"></i></a> </span>
                            <span><a ref="excel" href="javascript:void(0);" id='download_customer_data' class="btn download_sheet  pull-right" data-placement="bottom" data-toggle="tooltip" data-original-title="Download XLS" download><i class="fa fa-file-excel-o" aria-hidden="true"></i></a></span>
                        </div>
                        <form method="post" action="<?php echo base_url() ?>Customer/exportCustomerReportExcel" id="export_form">
                                <input type="hidden" name="customer_name">
                        </form>
                    </div>
                    <div id="filter">
                        <div class="box-header with-border box-body filter_body">

                        </div>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th width="9px">#</th>
                                    <th>Customer Code</th>
                                    <th>Company/Individual Name</th>
                                    <th>Country</th>
                                    <th>State</th>
                                    <th>City</th>
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
$this->load->view('customer/customer_bulk_upload'); 
?>
<script>
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": base_url + "customer",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "customer_code"},
                {"data": "customer_name"},
                {"data": "country"},
                {"data": "state"},
                {"data": "city"}
            ],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });
             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
        <?php 
        $customer_success = $this->session->flashdata('customer_success');
        $customer_error = $this->session->flashdata('customer_error');
        $bulk_success_customer = $this->session->flashdata('bulk_success_customer');
        $bulk_error_customer = $this->session->flashdata('bulk_error_customer');
        $email_success = $this->session->flashdata('email_send_customer');
        ?>
        var alert_success = '<?= $customer_success; ?>';
        var alert_failure = '<?= $customer_error; ?>';
        var alert_bulk_success = '<?= $bulk_success_customer; ?>';
        var alert_bulk_failure = '<?= $bulk_error_customer; ?>';
        var email_success = '<?= $email_success; ?>';
        if(email_success == 'success'){
            alert_d.text = 'Email has been send with the attachment.';
            PNotify.error(alert_d);
        }else if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }else if(alert_bulk_success != ''){
            alert_d.text = alert_bulk_success;
            PNotify.success(alert_d);
        }else if(alert_bulk_failure != ''){
            alert_d.text = alert_bulk_failure;
            PNotify.error(alert_d);
        }       
        $(document).on('change', 'input[name="check_item"]', function() {       
            $("input[name='check_item']").not(this).prop('checked', false);  
        });
        $('.upload_customer_popup').click(function(){
            $('#upload_customer_doc').modal({show: true, backdrop: 'static', keyboard: false});
        }); 
        $('#download_customer_data').click(function(){
            $('#export_form').submit();
        })
        
    });
</script>