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
            <li class="active">Manufacturing Process List</li>
        </ol>
    </div>


    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">

            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Manufacturing Process</h3>
                        <?php
                          if(in_array($module_id, $active_add))
                        {
                            ?>
                            <a class="btn btn-sm btn-info pull-right btn-flat" style="margin: 10px" href="<?php echo base_url('manufacturing/manufacturing_product'); ?>">Add Manufacturing Product</a>

                        <?php } ?>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" style="overflow-y: auto;margin-left: 9px;">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                 <!-- <th>Date</th> -->
                                    <th style="min-width: 100px;text-align: center;">Manufacturing Order Number</th>
                                    <th style="min-width: 80px;text-align: center;">Product name</th>
                                    <th style="min-width: 200px;text-align: center;"> Added User</th>
                                    <th style="max-width: 50px;text-align: center;"> Date</th>
                                    <th style="min-width: 150px;text-align: center;">Added Date</th>
                                    <th style="min-width: 250px;text-align: center;"><?php echo ''; ?></th>
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
<!-- model to edit -->

<div id="quantity_model" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Stock</h4>
            </div>
            <div class="modal-body" id="productForm">
                <form role="form" method="post" action = "<?= base_url('Manufacturing/add_stock') ?>">
                    <div class="row">
                        <input type="hidden" name="qp_id" id="qp_id">
                        <input type="hidden" name="url" id="url" value="<?= $this->uri->segment(3) ?>">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_code">Quantity <span class="validation-color">*</span></label>
                                <input type="text" class="form-control" tabindex="1" id="quantity_new" name="quantity_new"  value="">

                                <input type="hidden" class="form-control" tabindex="1" id="quantity_history" name="quantity_history"  value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_code">Reference Number</label>
                                <input type="text" class="form-control" tabindex="1" id="reference_number" name="reference_number"  value="">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_code">Date</label>
                                <input type="text" class="form-control datepicker" tabindex="1" id="user_date" name="user_date" readonly=""  value="<?= date('Y-m-d') ?>">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_code">Price</label>
                                <input type="text" class="form-control" tabindex="1" id="new_price" name="new_price"  value="">

                            </div>
                        </div>
                    </div>
                    <button type="submit" id="quantity_history_submit" class="btn btn-info">&nbsp;&nbsp;&nbsp;Update&nbsp;&nbsp;&nbsp;</button>
                </form>
                <div id="quantity_price">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>




<?php
$this->load->view('layout/footer');
$this->load->view('tax/tax_modal');
$this->load->view('general/delete_modal');
$this->load->view('product/hsn_modal');
$this->load->view('category/category_modal');
$this->load->view('subcategory/subcategory_modal');
$this->load->view('product/damaged_stock_modal');
$this->load->view('product/quantity_products_modal');
?>

<script>
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "/manufacturing/manufacturing_process_list",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "manufacturing_order_code"},
                {"data" : "varient_code"},
                {"data": "added_user"},

                {"data": "added_date"},
                {"data": "status"},
                {"data": "action"}
            ]
        });
    });

    function get_product_stock(id) {

        $("#qp_id").val(id)

        $.ajax({
            url: base_url + 'manufacturing/get_stocks/' + id,
            type: 'GET',
            success: function (data)
            {

                $("#quantity_price").html(data)
            }
        });
    }

    $(document).ready(function(){
        $('body').on('click','.staus_change',function(){
            var m_id = $(this).data("id");
            var status = $(this).text();
            var change_text = $(this);

            $.ajax({
            url: base_url + 'manufacturing/change_status',
            type: 'POST',
            data:{'m_id':m_id,
                    'status':status},
            success: function (data)
            {
                // alert(data)
                change_text.text(data);
                // $("#quantity_price").html(data)
            }
        });
        })
    })

</script>
<script src="<?php echo base_url('assets/js/product/') ?>product.js"></script>
