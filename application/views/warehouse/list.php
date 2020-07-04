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
                            <h3 class="box-title">Warehouse </h3>
                            <?php
                            if (in_array($warehouse_module_id, $active_add)) {
                            ?>
                                <a class="btn btn-sm btn-info pull-right" data-toggle="modal" data-target="#add_warehouse">Add Warehouse</a>
                            <?php } ?>
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
                                    <th>Warehouse Name</th>
                                    <th width="20%">Country</th>
                                    <th width="20%">State</th>
                                    <th width="20%">City</th>
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
$this->load->view('warehouse/add_modal');
$this->load->view('warehouse/edit_modal');
?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "iDisplayLength": 15,
            "lengthMenu": [ [15, 25, 50,100, -1], [15, 25, 50,100, "All"] ],
            "ajax": {
                "url": base_url + "warehouse",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "warehouse_name"},
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
        $warehouse_success = $this->session->flashdata('warehouse_success');
        $warehouse_error = $this->session->flashdata('warehouse_error');
        ?>
        var alert_success = '<?= $warehouse_success; ?>';
        var alert_failure = '<?= $warehouse_error; ?>';
        if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });
</script>
