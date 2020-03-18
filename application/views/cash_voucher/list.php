<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    </section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Cash Voucher</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <?php
            if ($this->session->flashdata('email_send') == 'success')
            {
                ?>
                <div class="col-sm-12">
                    <div class="alert alert-success">
                        <button class="close" data-dismiss="alert" type="button">×</button>
                        Email has been send with the attachment.
                        <div class="alerts-con"></div>
                    </div>
                </div>
                <?php
            }
            ?>
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cash Voucher</h3>
                        <div id="filter" style="display: block; float: right">
                            <div class="box-header box-body filter_body">
                                <div class="btn-group">             
                                    <span><a data-toggle="tooltip" data-placement="bottom" class="btn btn-app edit_voucher" data-custom-class="tooltip-primary" data-original-title="Edit Cash Voucher" style="display: none;"><i class="fa fa-pencil"></i></a></span>
                                    <span><a data-toggle="tooltip" data-placement="bottom" class="btn btn-app delete_voucher" data-custom-class="tooltip-primary" data-original-title="Delete Cash Voucher" style="display: none;"><i class="fa fa-trash-o"></i></a></span>                                      
                                    <span><a data-toggle="tooltip" data-placement="bottom" class="btn btn-app add_voucher" data-value = "cash" data-custom-class="tooltip-primary" data-original-title="Add Cash Voucher"><i class="fa fa-plus"></i></a></span>
                                    <span><a href="<?php echo base_url("assets/excel/voucher_with_ledgers_demo.csv") ?>"  class="btn btn-app" data-toggle="tooltip" data-placement="bottom" data-custom-class="tooltip-primary" data-original-title="Download Cash Voucher with ledgers CSV demo file" download><i class="fa fa-download"></i></a></span>
                                    <span><a data-toggle="tooltip" data-placement="bottom" data-custom-class="tooltip-primary" class="btn btn-app upload_voucher_popup" data-original-title="Upload Cash Voucher" class=""><i class="fa fa-cloud-upload"></i></a> </span>
                                   <!--  <span><a href="<?= base_url(); ?>upload_voucher" data-toggle="tooltip" data-placement="bottom" class="btn btn-app" data-custom-class="tooltip-primary" data-original-title="Upload Bulk Voucher"><i class="fa fa-cloud-upload"></i></a> </span> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                         <div id="loader">
                           <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                       </div>
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th width="9px">#</th>
                                    <th>Voucher Date</th>
                                    <th>Voucher Number</th>
                                    <th>Invoice Number</th>
                                    <!-- <th><?php echo 'Grand Total'; ?></th> -->
                                    <!-- <th>From Account</th>
                                    <th>To Account</th> -->
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php
$this->load->view('layout/footer');
$this->load->view('general/add_voucher.php');
$this->load->view('general/upload_popup.php'); 
// $this->load->view('general/delete_modal');
?>
<script>
    var getVoucher;
    $(document).ready(function () {
        getVoucher = GetAllVoucher();
    })

    function GetAllVoucher(){

        var tbl = $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "cash_voucher",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "voucher_date"},
                {"data": "voucher_number"},
                {"data": "invoice_number"},
                /*{"data": "grand_total","className" : "text-right"}*/
                /*{"data": "from_account"},
                {"data": "to_account"},*/
            ],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
        $('.edit_voucher').hide();
        $('.delete_voucher').hide();
        return tbl;
    }
</script>
<script src="<?php echo base_url('assets/js/vouchers/'); ?>common_voucher.js"></script>