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
                                    <th>Article</th>
                                    <th>Product name</th>
                                    <th>Category</th>
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
<?php
$branch_id = $this->session->userdata('SESS_BRANCH_ID');
?>
<div id="barcode_popup" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form role="form" id="form" method="post" action="<?php echo base_url('barcode/print_roll_barcode'); ?>" target="_blank">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Barcode</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <label for="quantity">Quantity
                                <span class="validation-color">*</span>
                            </label>
                            <input type="number" required="true" class="form-control" id="quantity" name="quantity" value="<?php echo set_value('quantity'); ?>">
                            <input type="hidden" name="item_id" id="item_id">
                            <input type="hidden" name="single_product" value="1">
                            <input type="hidden" name="barcode_id" id="barcode_id">
                            <span class="validation-color" id="err_quantity"><?php echo form_error('quantity'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="print_barcode" class="btn btn-primary">Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div> 
        </form>
    </div>
</div>

<?php  $this->load->view('layout/footer'); ?>
<script>
    $(document).ready(function () {
        $(document).on('click','.quantity_popup',function(){
            var id = $(this).attr('item_id');
            var barId = $(this).attr('barId');
            $('#barcode_popup').find('[name=barcode_id]').val(barId);
            $('#barcode_popup').find('[name=item_id]').val(id);
            $('#barcode_popup').modal('show');
        })

        $('#print_barcode').on('click',function(){
            $('#barcode_popup').modal('hide');
        })

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
                {"data": "item_code"},
                {"data": "product_name"},
                {"data": "category"},
                {"data": "action_popup"},              
            ],"columnDefs": [{
                        "targets": "_all",
                        "orderable": false
                    }],
        });
        anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
    });
</script>
