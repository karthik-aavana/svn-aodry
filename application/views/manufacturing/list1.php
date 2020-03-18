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
            <li class="active">Manufacturing Order List</li>
        </ol>
    </div>


    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">

            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Manufacturing Order</h3>
                        <?php
                          if(in_array($module_id, $active_add))
                        {
                            ?>
                            <a class="btn btn-sm btn-info pull-right btn-flat" style="margin: 10px" href="<?php echo base_url('manufacturing/manufacturing_product'); ?>">Add Manufacturing Product</a>

                        <?php } ?>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                 <!-- <th>Date</th> -->
                                    <th style="min-width: 100px;text-align: center;">Manufacturing Order Number</th>
                                    <th style="min-width: 80px;text-align: center;">Product name</th>
                                    <th style="min-width: 200px;text-align: center;"> Order Quantity</th>
                                    <th style="max-width: 50px;text-align: center;"> Stock</th>
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

<!-- model for raw material availability -->
<!-- Modal -->
<div id="raw_material_availability" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Raw Materials Availability</h4>
            </div>
            <div class="modal-body" id="appendRawmaterials">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<!-- labour Availability -->

<div id="labour_availability" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Labour Availability</h4>
            </div>
            <div class="modal-body" id="appendLabour">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<!-- Over Head Availability -->

<div id="over_head_availability" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Over Head Availability</h4>
            </div>
            <div class="modal-body" id="appendoverhead">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>


<!-- Make Availability -->

<div id="make" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Make</h3>
            </div>
            <div class="modal-body" id="appendMake">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<!-- stock movement -->
<div id="stock_movement" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Stock movement</h4>
            </div>
            <div class="modal-body" id="appendstockmovement">
              <form action="<?= base_url('manufacturing/move_stock_to');?>" method="post">
                <div class="row">
                  <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_code">Stock</label>
                    <select name="stock_to_move" class="form-control" id="stock_to_move">
                                  
                    </select>

                            </div>
                        </div>
                         <div class="col-md-6">
                      <div class="form-group">
                       <label for="product_code">Branch <span class="validation-color">*</span></label>
                    <select class="form-control" name="to_branch">
                      <?php
                      foreach ($move_to_branch as $key ) {
                        echo "<option value = '".$key->branch_id."'>".$key->branch_name."</option>";
                      }
                      ?>
                    </select>
                      </div>
                    </div>
                    <input type="hidden" name="stock_mo_id" id="stock_mo_id">
                  <div class="col-md-6">
                      <div class="form-group">
                        <label for="product_code">Reference Number</label>
                        <input type="text" class="form-control" tabindex="1" id="r_number" name="r_number"  value="">
                      </div>
                    </div>
                   <div class="col-md-6">
                      <div class="form-group">
                        <label for="product_code">Reason For Movement</label>
                        <textarea name="reason" style="width: 100%"></textarea>
                      </div>
                    </div>
                    <div class="col-md-4 col-md-offset-5">
                      <input type="submit" name="submit" value="Move"  class="btn btn-default">
                    </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<!-- Make Availability -->

<div id="stock_history" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">History</h3>
            </div>
            <div class="modal-body" id="appendHistory">
               <table id="list_datatable1" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                 <!-- <th>Date</th> -->
                                    <th style="min-width: auto;text-align: center;">added_date</th>
                                    <th style="min-width: auto;text-align: center;">reference_no name</th>
                                    <th style="min-width: auto;text-align: center;"> activity_name Quantity</th>
                                    <th style="max-width: auto;text-align: center;"> reason</th>
                                    <th style="max-width: auto;text-align: center;"> Quantity</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                "url": base_url + "/manufacturing/list1",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "manufacturing_order_code"},
                {"data": "varient_code"},
                {"data": "manufacturing_order_quantity"},
                {"data": "manufacturing_order_stock"},
                {"data": "added_date"},
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

    function getRawMaterials(id) {
        $.ajax({
            url: base_url + 'manufacturing/get_raw_materials/' + id,
            type: 'GET',
            success: function (data)
            {
                $("#appendRawmaterials").html(data)
            }
        });
    }

    function getLabour(id) {
        $.ajax({
            url: base_url + 'labour/get_labour/' + id,
            type: 'GET',
            success: function (data)
            {
                $("#appendLabour").html(data)
            }
        });

    }
    function getOverHead(id) {
        $.ajax({
            url: base_url + 'over_head/get_over_head/' + id,
            type: 'GET',
            success: function (data)
            {
                $("#appendoverhead").html(data)
            }
        });
    }
    function make(id) {
        $.ajax({
            url: base_url + 'manufacturing/make/' + id,
            type: 'GET',
            success: function (data)
            {
                $("#appendMake").html(data)
            }
        });
    }

    function stockMovement(id){
      $("#stock_mo_id").val(id);
       $.ajax({
            url: base_url + 'manufacturing/stock_value/' + id,
            type: 'GET',
            success: function (data)
            {
                
                var totalQuantity;
                for (var i = 1; i <= data; i++) {
                  totalQuantity += "<option value='" + i + "'>" + i + "</option>";
                }
                  $('#stock_to_move').html(totalQuantity)

            }
        });
    }

    function stockHistory(id){

       // $("#stock_mo_id").val(id);
       $.ajax({
            url: base_url + 'manufacturing/get_stock_history/' + id,
            type: 'GET',
            success: function (data)
            {

              var obj = JSON.parse(data);
              // alert(obj[1].quantity)
                var totalQuantity;
                totalQuantity ="<table class=''><tr><th>quantity</th><th>Reference No</th><th>branch_name</th></tr>"
                for (var i = 0; i < obj.length; i++) {
                  totalQuantity += "<tr><td>"+obj[i].quantity+"</td><td>"+obj[i].reference_no+"</td><td>"+obj[i].reason+"</td></tr>";
                }
                totalQuantity+= "</table>";
                  $("#appendHistory").html(totalQuantity);

            }
        });

    }

    function shl(id){
       $.fn.dataTable.ext.errMode = 'none';
       $('#list_datatable1').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "/manufacturing/get_stock_history_listing/" + id,
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "added_date"},
                {"data": "reference_no"},
                {"data": "activity_name"},
                {"data": "reason"},
                {"data": "quantity"},
                // {"data": "action"}
            ]
        });
    }


</script>
<script src="<?php echo base_url('assets/js/product/') ?>product.js"></script>
