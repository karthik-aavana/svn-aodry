<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">   
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Service</li>
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
                    <div id="plus_btn">
                        <div class="box-header with-border">
                            <h3 class="box-title">Service</h3>
                            <?php
                            if (in_array($service_module_id, $active_add)) {
                                ?>
                                <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('service/add'); ?>">Add Service</a>
                            <?php } ?>
                            <span><a href="<?php echo base_url("assets/excel/service_bulk_demo.csv") ?>"  class="btn btn-sm btn-info pull-right" data-toggle="tooltip" data-placement="bottom" data-custom-class="tooltip-primary" data-original-title="Download Service CSV Demo File" download><i class="fa fa-download"></i></a></span>

                            <span><a data-toggle="tooltip" data-placement="bottom" data-custom-class="tooltip-primary" class="btn btn-sm btn-info upload_service_popup pull-right" data-original-title="Upload Service" class=""><i class="fa fa-cloud-upload"></i></a> </span>
                        </div>
                    </div>
                    <div id="filter">
                        <div class="box-header with-border box-body filter_body">
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>                 
                                	<th width="9px">#</th>                   
                                    <th>Service Code</th>
                                    <th>Service Name</th>
                                    <th>HSN/SAC Code</th>                                                                       
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
$this->load->view('service/service_bulk_upload'); 

?>
<script>
    var settings_tax_type = "<?= $access_settings[0]->tax_type ?>";
    $(document).ready(function () {
        var table = $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "service",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "service_code"},
                {"data": "service_name"},
                {"data": "hsn_code"},
                
            ],
            "columnDefs": [
                //{ "visible": false, "targets": [0]},
                {"orderable": false, "targets": [3]}
            ],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
        <?php 
        $service_success = $this->session->flashdata('service_success');
        $service_error = $this->session->flashdata('service_error');
        $bulk_success_service = $this->session->flashdata('bulk_success_service');
        $bulk_error_service = $this->session->flashdata('bulk_error_service');
        $email_success = $this->session->flashdata('email_send_service');
        ?>
        var alert_success = '<?= $service_success; ?>';
        var alert_failure = '<?= $service_error; ?>';
        var alert_bulk_success = '<?= $bulk_success_service; ?>';
        var alert_bulk_failure = '<?= $bulk_error_service; ?>';
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
        $('.upload_service_popup').click(function(){
            $('#upload_service_doc').modal({show: true, backdrop: 'static', keyboard: false});
        })
    });
</script>