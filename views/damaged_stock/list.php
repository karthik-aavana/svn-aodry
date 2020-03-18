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
                Damaged Product Stock
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Damaged Stock</h3>
                        <a class="btn btn-sm btn-info pull-right btn-flat" data-toggle="modal" data-target="#add_report_fixed" href="#">Report Fixed/Missing Stock</a>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th>Products </th>
                                    <th>SKU </th>
                                    <th>Total Damaged Quantity</th>
                                    <th>Total Missing Quantity</th>
                                    <th>Total Fixed Quantity</th>
                                    <th>Remaining Quantity</th>
                                    <th width="18%">Reported Damaged<br>Stock History</th>
                                    <th width="18%">Reported<br>Fixed/Missing Stock</th>
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
$this->load->view('damaged_stock/view_product');
$this->load->view('damaged_stock/add_report_fixed');
$this->load->view('damaged_stock/edit_found_stock');
?>
<script>
     var dTable = '';
    $(document).ready(function () {
         dTable = getAllDamagedStock();
        <?php 
        $damaged_stock_success = $this->session->flashdata('damaged_stock_success');
        $damaged_stock_error = $this->session->flashdata('damaged_stock_error');
        ?>
        var alert_success = '<?= $damaged_stock_success; ?>';
        var alert_failure = '<?= $damaged_stock_error; ?>';
        if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });

    function getAllDamagedStock(){
       var table =  $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": base_url + "damaged_stock",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "product_name"},
                {"data": "product_sku"},
                {"data": "total_damaged"},
                {"data": "total_missing"},
                {"data": "total_fixed"},
                {"data": "total_remaining"},
                {"data": "history"},
                {"data": "action"}
            ],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });
             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
        return table;
    }
</script>