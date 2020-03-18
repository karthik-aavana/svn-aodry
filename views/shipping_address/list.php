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
                Contact Person
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div id="plus_btn">
                        <div class="box-header with-border">
                            <h3 class="box-title">Customer / Vendor Shipping Address </h3>
                            <a class="btn btn-sm btn-info pull-right" data-toggle="modal" data-target="#add_shipping">Add Customer / Vendor Shipping Address</a>
                        </div>
                    </div>
                    <div id="filter">
                        <div class="box-header with-border box-body filter_body"></div>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="9px">#</th>
                                    <th>Customer / Vendor</th>
                                    <th width="20%">Contact Person Name</th>
                                    <th width="20%">Shipping Code</th>
                                    <th>Customer/Vendor Shipping Address</th>
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
$this->load->view('shipping_address/add_modal');
$this->load->view('shipping_address/edit_modal');
?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": base_url + "shipping_address",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "customer_name"},
                {"data": "contact_person"},
                {"data": "shipping_code"},
                {"data": "address"}
                //{"data": "department"},
                //{"data": "email"},
                //{"data": "contact_number"}                
            ],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
        <?php 
        $shipping_address_success = $this->session->flashdata('shipping_address_success');
        $shipping_address_error = $this->session->flashdata('shipping_address_error');
        ?>
        var alert_success = '<?= $shipping_address_success; ?>';
        var alert_failure = '<?= $shipping_address_error; ?>';
        if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });
</script>
