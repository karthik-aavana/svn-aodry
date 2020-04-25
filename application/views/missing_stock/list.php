<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header"></section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li>
                <a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                Product Stock
            </li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Missing Stock</h3>
                        <?php if(in_array($damaged_stock_module_id, $active_add) || in_array($product_module_id, $active_add)) {?>
                            <a class="btn btn-sm btn-info pull-right btn-flat" data-toggle="modal" data-target="#add_report_fixed" href="#">Report Found/Damged Stock</a>
                        <?php } ?>
                    </div>
                    <div class="well">
                        <div class="box-body">
                            <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                                <thead>
                                    <tr>
                                        <th> Products </th>
                                        <th> SKU </th>
                                        <th> Total Missing Quantity</th>
                                        <th> Total Damaged Quantity</th>
                                        <th> Total Found Quantity</th>
                                        <th> Remaining Quantity</th>
                                        <th> Reported Missing<br>Stock History</th>
                                        <th> Reported<br>Found/Damaged</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('missing_stock/view_product');
$this->load->view('missing_stock/add_fixed_product');
$this->load->view('missing_stock/edit_fixed_stock');
?>
<script>
    $(document).ready(function () {

        $('table tbody').find('input').addClass('disable_in');
        $(document).on('click', '.edit_cell', function () {
            var id = $(this).attr('data-id');
            console.log(id + "");
            $('table tbody').find('[data-id="' + id + '"]').addClass('dt-pkr-bdr');
        });
        $(document).on('click', '.save_cell', function () {
            var id = $(this).attr('data-id');
            $('table tbody').find('[data-id="' + id + '"]').removeClass('dt-pkr-bdr');
        });
    });
    var dTable = '';
    $(document).ready(function () {
        dTable = getAllMissingStock();
        <?php 
        $missing_stock_success = $this->session->flashdata('missing_stock_success');
        $missing_stock_error = $this->session->flashdata('missing_stock_error');
        ?>
        var alert_success = '<?= $missing_stock_success; ?>';
        var alert_failure = '<?= $missing_stock_error; ?>';
        if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });

    function getAllMissingStock(){
        var disable = true;
        <?php if(!in_array($damaged_stock_module_id, $active_view) && !in_array($product_module_id, $active_view)){ ?>
            disable = false;
        <?php } ?>
         var table =  $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": base_url + "missing_stock",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "product_name"},
                {"data": "product_sku"},
                {"data": "total_missing"},
                {"data": "total_damaged"},
                {"data": "total_fixed"},
                {"data": "total_remaining"},
                {"data": "history"},
                {"data": "action",'visible': disable}
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