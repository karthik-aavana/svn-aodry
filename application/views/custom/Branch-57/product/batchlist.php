<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content-header"></section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li>
                <a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                Product
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
            }?> -->
            <div class="col-md-12">
                <div class="box">
                     <div class="box-header with-border">
                        <!-- <h3 class="box-title">Journal Voucher</h3>  -->                       
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
                            <h3 class="box-title">Product Batch</h3>
                            <?php
                            if (in_array($product_module_id, $active_add)) {
                                ?>
                                <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('product/add'); ?>">Add Product</a>
                                <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('product/product_batch'); ?>" title='Create New Product Batch'>New Batch</a>
                            <?php }
                            ?>
                            
                        </div>
                    </div>
                    <div id="filter">
                        <div class="box-header with-border box-body filter_body">
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="list_datatable" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="2%">#</th>
                                        <th>Product Name</th>
                                        <th>Batch</th>
                                        <th>MRP</th>
                                        <th>Markdown Discount(%)</th>
                                        <th>Selling Price</th>
                                        <th>Marginal Discount(%)</th>
                                        <th>GST Output</th>
                                        <th>Basic Price</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
$this->load->view('general/delete_modal');
$this->load->view('product/damaged_stock_modal');
$this->load->view('product/quantity_products_modal');
$this->load->view('product/product_bulk_upload'); 
?>
<script>
    var settings_tax_type = "<?= $access_settings[0]->tax_type ?>";
    $(document).ready(function () {
        var table = $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": base_url + "product/product_batchList",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "product_name"},
                {"data": "product_batch"},
                {"data": "mrp"},
                {"data": "markdown_discount"},
                {"data": "selling_price"},
                {"data": "marginal_discount"},
                {"data": "gst_Output"},
                {"data": "basic_price"},
            ],

            "columnDefs": [ {
                /*"targets": 6,*/
                "orderable": false
                }]
        });
        <?php 
        $product_success = $this->session->flashdata('product_success');
        $product_error = $this->session->flashdata('product_error');
        $bulk_success_product = $this->session->flashdata('bulk_success_product');
        $bulk_error_product = $this->session->flashdata('bulk_error_product');
        $email_success = $this->session->flashdata('email_send_product');
        ?>
        var alert_success = '<?= $product_success; ?>';
        var alert_failure = '<?= $product_error; ?>';
        var alert_bulk_success = '<?= $bulk_success_product; ?>';
        var alert_bulk_failure = '<?= $bulk_error_product; ?>';
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
    });
    $('.upload_product_popup').click(function(){
        $('#upload_doc').modal({show: true, backdrop: 'static', keyboard: false});
    })
    $(document).on('click',".save_bulk",function(){
        var bulk_voucher = $('#upload_bulk_voucher [name=bulk_voucher]').val();
        if(bulk_voucher == ''){
            /*alert('Select file to upload!');*/
            alert_d.text = 'Select file to upload!';
            PNotify.error(alert_d);
            return false;
        }
    })
</script>