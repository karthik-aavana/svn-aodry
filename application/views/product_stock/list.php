<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<style>
    /*#list_datatable tr td{
        white-space: nowrap;
        vertical-align: middle;
        text-align: right;
    }    
    
    #list_datatable tr th{
        white-space: nowrap;
    }
    
    #list_datatable tr td:first-child{
        text-align: left;
    }    
    
    #list_datatable tr td:nth-child(2), #list_datatable tr td:nth-child(10),
    #list_datatable tr td:nth-child(11){
        text-align: center !important;
    }*/
    
    input[name="reorder"]{
        width: 40px !important;
        margin-right: 5px;
    }
</style>
<div class="content-wrapper">
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
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Product Stock</h3>
                        <a class="btn btn-sm btn-info pull-right btn-flat" data-toggle="modal" data-target="#add_missing_product" href="#">Report Damaged/Missing Stock</a>
                    </div>                   
                    <div class="box-body">                       
                        <table id="list_datatable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Products </th>
                                    <th>SKU </th>
                                    <th>Purchase Reference </th>
                                    <th>Total Purchase Quantity</th>
                                    <th>In stock</th>
                                    <th>Price Per Unit</th>
                                    <th>Total Purchase Cost</th>
                                    <th>Reorder Point</th>
                                    <th>Total Damaged Stock</th>
                                    <th>Total Missing Stock</th>
                                    <th>Received from Damaged/Missing History</th>
                                    <th> Reported Damaged/Missing Stock</th>
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
    $this->load->view('product_stock/view_product');
    $this->load->view('product_stock/add_missing_product');
    $this->load->view('product_stock/damaged_stock');
    $this->load->view('product_stock/edit_reoder');
    $this->load->view('product_stock/view_reference');
    ?>
<script>
    var dTable = '';
    $(document).ready(function () {
        dTable = getAllProductStock();
        $('table tbody').find('input').addClass('disable_in');
        $(document).on('click', '.edit_cell', function () {
            var id = $(this).attr('data-id');
            $('table tbody').find('input[data-id="' + id + '"]').addClass('dt-pkr-bdr');
        });
        $(document).on('click', '.save_cell', function () {
            var id = $(this).attr('data-id');
            var reorder = $('#reorder_' + id).val();
//            console.log(id);
//            console.log($('#reorder_' + id).val());
            if (reorder != 0) {
                $.ajax({
                    url: base_url + 'Product_stock/update_reorder',
                    dataType: 'JSON',
                    method: 'POST',
                    data: {'product_id': id, 'reorder': reorder},
                    success: function (result) {
                        alert_d.text = "Reorder Point Updated";
                        PNotify.success(alert_d);
                    }
                });
            }
            $('table tbody').find('[data-id="' + id + '"]').removeClass('dt-pkr-bdr');
        });
        <?php 
        $product_stock_success = $this->session->flashdata('product_stock_success');
        $product_stock_error = $this->session->flashdata('product_stock_error');
        ?>
        var alert_success = '<?= $product_stock_success; ?>';
        var alert_failure = '<?= $product_stock_error; ?>';
        if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });

    function getAllProductStock(){
       var table = $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "ajax": {
                "url": base_url + "product_stock",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "product_name"},
                {"data": "product_sku"},
                {"data": "reference_number"},
                {"data": "qty"},
                {"data": "product_quantity"},
                {"data": "amt"},
                {"data": "purchase_cost"},
                {"data": "product_alert_quantity"},
                {"data": "product_damaged_quantity"},
                {"data": "product_missing_quantity"},
                {"data": "view"},
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