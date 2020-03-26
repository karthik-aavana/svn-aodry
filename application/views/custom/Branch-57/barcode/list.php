<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">	
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a>
                </li>
                <li class="active"> Barcode</li>
            </ol></h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div id="plus_btn">
                        <div class="box-header with-border">
                            <h3 class="box-title">Barcode</h3>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('barcode/get_barcode_on_purchase'); ?>">Generate Barcode </a>
                        </div>
                    </div>
                    <div id="filter">
                        <div class="box-header with-border box-body filter_body">
                        </div>
                    </div>					
                    <div class="box-body">
                        <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Style</th>
                                    <th>Unit</th>
                                    <th>Selling Price</th>
                                    <th>Category</th>
                                    <th>SKU</th>
                                    <!--<th>Serial No</th>
                                    <th>Orientation</th>
                                    <th>Height</th>
                                    <th>Width</th> -->
                                    <th>Generate</th>
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
<?php  $this->load->view('layout/footer'); ?>
<script>
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "paging": true,
            "searching": true,
            "ordering": true,
            "ajax": {
                "url": base_url + "barcode",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            'language': {
            'loadingRecords': '&nbsp;',
            'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
            },
            "columns": [
                {"data": "product_name"},
                {"data": "style"},
                {"data": "unit"},
                {"data": "selling_price"},
                {"data": "category"},
                {"data": "sku"},
               /* {"data": "serial_no"},
                {"data": "orientation"},
                {"data": "height"},
                {"data": "width"}, */
                {"data": "action"},              
            ],"columnDefs": [{
                        "targets": "_all",
                        "orderable": false
                    }],
        });
        anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
    });
</script>
