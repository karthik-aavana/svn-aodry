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

            <li class="active">Manufacturing Process</li>

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

                            <a class="btn btn-sm btn-info pull-right btn-flat" href="<?php echo base_url('manufacturing/manufacturing_process_list'); ?>">Back</a>



                        <?php } ?>

                        <div class="row">

                            <div class="col-md-3">

                                <label for="date">Batch Number</label>

                                <div class="input-group">

                                    <input type="text" class="form-control" readonly="" id="batch" name="invoice_date" value="<?= $manufacturing_process[0]->manufacturing_order_code?>">

                                 </div>

                                

                            </div>

                             <div class="col-md-3">

                                <label for="date">Product</label>

                                 <div class="input-group">

                                    <input type="text" class="form-control" readonly=""  name="invoice_date" value="<?= $manufacturing_process[0]->varient_code?>">

                                 </div>

                            </div>

                            <div class="col-md-3">

                                <label for="date">Status</label>

                                 <div class="input-group">

                                    <input type="text" class="form-control staus_change" data-id="<?= $manufacturing_process[0]->manufacturing_process_id?>" readonly="" id="product_name" name="invoice_date" value="<?= $manufacturing_process[0]->status?>">

                                 </div>

                            </div>

                            <div class="col-md-3">

                                <label for="date">Quantity</label>

                                 <div class="input-group">

                                    <input type="text" class="form-control" data-id="<?= $manufacturing_process[0]->manufacturing_process_id?>" readonly="" id="product_quantity" name="invoice_date" value="<?= $manufacturing_process[0]->quantity?>">

                                 </div>

                            </div>

                        </div>

                        <div class="row">

                         <div class="col-md-6">

                            <h4>Over Heads</h4><span data-target="#add_over_heads" data-toggle="modal" style="float: right;color: #3f51b5">Add more</span>

                         <table id="overHeads" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >

                            <thead>

                               <tr><th>Name</th><th>Quantity </th><th>Cost Per Unit</th><th>Total Cost</th></tr>



                            </thead>

                            <tbody id="over_head_list">

                                <?php 

                                foreach ($over_head as $key2)

                                 { ?>

                                <tr><td><?= $key2->over_head_name ?></td><td><?=$key2->quantity ?></td><td><?= $key2->price ?></td><td><?= $key2->cost ?></td></tr>



                                <?php 

                            }

                                ?>

                            </tbody>

                        </table>

                    </div>

                    <div class="col-md-6">

                        <h4>Raw Materials</h4><span data-target="#add_raw_materials" data-toggle="modal" style="float: right;color: #3f51b5">Add more</span>

                          <table id="rawMaterials" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >

                            <thead>

                               <tr><th>Name</th><th>Quantity Required</th><th>Rate</th><th>Instock</th></tr>



                            </thead>

                            <tbody id="raw_materials_total">

                                <?php 

                                foreach ($raw_materials as $key1)

                                 { ?>

                                <tr><td><?= $key1->r_name ?></td><td><?= $key1->quantity ?></td><td><?= $key1->price ?></td><td><?= $key1->stock ?></td></tr>



                                <?php 

                                     }

                                ?>

                            </tbody>

                        </table>

                    </div>

                    <div class="col-md-6">

                        <h4>Labour</h4><span data-target="#add_labour1" data-toggle="modal" style="float: right;color: #3f51b5">Add more</span>

                        <table id="rawMaterials" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >

                            <thead>

                               <tr><th>No of labour </th><th>total no hours Required</th><th>cost per hour</th><th>activity_name</th><th>Total</th></tr>



                            </thead>

                            <tbody id="labour_total">

                                <?php 

                                 foreach ($labour as $key)

            {

                echo "<tr><td>" . $key->no_of_labour . "</td><td>" . $key->total_no_hours . "</td><td>" . $key->cost_per_hour . "</td><td>" . $key->activity_name . "</td><td>" . $key->total_cost . "</td></tr>";

            }

                                ?>

                            </tbody>

                        </table>

                    </div>



                    <div class="col-md-6">

                        <h4>Scrap</h4><span data-target="#scrap_model" data-toggle="modal" style="float: right;color: #3f51b5">Add more</span>

                        <?php

                        if(($scrap!= NULL)){



                        ?>

                        <table id="rawMaterials" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >

                            <thead>

                               <tr><th>Type </th><th>Reason</th><th>Rate</th><th>Quantity</th><th>reference_no</th></tr>



                            </thead>

                            <tbody id="scrap">

                                <?php 

                                 foreach ($scrap as $key)

            {

                echo "<tr><td>" . $key->type . "</td><td>" . $key->reason . "</td><td>" . $key->price . "</td><td>" . $key->quantity . "</td><td>" . $key->reference_no . "</td></tr>";

            }

                                ?>

                            </tbody>

                        </table>



                        <?php

                    }

                        ?>

                    </div>

                    </div>

                    <div class="row">

                        <div class="col-md-8">



                            <p>Total Over heads cost: <b id="over_head_sum"><?= 

                            $over_head_sum[0]->price;

                            ?></b></p>

                            <p>Total Raw materials Cost: <b id="raw_material_sum"><?= 

                            $raw_material_sum[0]->price;

                            ?></b></p>

                            <p>Total Labour cost: <b id="labour_sum"><?= 

                            $labour_sum[0]->price;

                            ?></b></p>

                            <p>Total Scrap cost: <b id="scrap_sum"><?= 

                            $scrap_sum[0]->price;

                            ?></b></p>

                            <p>Total value of the Batch:<b id="total_value"></b></p>

                            <p>Cost per unit :<b id="per_unit_cost"></b></p>

                        </div>

                    </div>

                    </div>

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







<!-- model for raw material availability -->

<!-- Modal -->



<!-- labour Availability -->





<!-- Over Head Availability -->







<!-- Make Availability -->



<!-- stock movement -->





<!-- Make Availability -->



<div id="add_over_heads" class="modal fade" role="dialog">

    <div class="modal-dialog">



        <!-- Modal content-->

        <div class="modal-content" style="width: 900px">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h3 class="modal-title">Add Over Head</h3>

            </div>

            <div class="modal-body" id="">

               <div class="box-body">

                                <table class="table overHeadTable">

                                    <thead>

                                        <tr>

                                            <th>Over Head name<a data-toggle="modal" data-target="#add_over_head_modal"  class="pull-middle"> + Add</a></th>

                                            <th>Quantity</th>

                                            <th>Cost per unit</th>

                                            <th>Total</th>

                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <tr  class="clone_more_o_h">

                                            <td><select class="over-head-change oh_cost">

                                                    <option>Select</option>

                                                    <?php

                                                    foreach ($over_head_select as $oh)

                                                    {

                                                        echo "<option value='" . $oh->over_head_id . "'>" . $oh->over_head_name . "</option>";

                                                    }

                                                    ?>

                                                </select></td>

                                            

                                            <td><input class="overHeadquantity oh_cost" type="text" name=""></td>

                                            <td><input class="overHeadCost oh_cost" type="text" name=""></td>

                                            <td><input class="overHeadtotal oh_cost" type="text" name=""></td>

                                            <td><p class="clone-o-h">+ Clone</p><p class="remove-p-r">- Remove</p></td>

                                        </tr>

                                    </tbody>

                                </table>

                                <button type="button" >Over Head</button>

                                <form action="<?= base_url('manufacturing/add_over_heads_to_process')?>" method="post">

                                    <input type="hidden" name="product_id" id="product_id" value="<?= $manufacturing_process[0]->product_inventory_varients_id?>">

                                <input type="hidden" name="o_h_quantity" id="o_h_quantity">

                                <input type="hidden" name="process_id" value="<?= $process_id?>">

                                <input style="float: right;" type="submit" id="create_json"  name="submit" value="submit">

                            </form>

                            </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>

        </div>



    </div>

</div>



<div id="add_raw_materials" class="modal fade" role="dialog">

    <div class="modal-dialog">



        <!-- Modal content-->

        <div class="modal-content" style="width: 900px">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h3 class="modal-title">Add Raw Materials</h3>

            </div>

            <div class="modal-body" id="">

               <div class="box-body">

                                <table class="table raw_materials">

                                    <thead>

                                        <tr>

                                            <th>Raw Material</th>

                                            <th>Quantity</th>

                                            <th>Cost</th>

                                            <th>Total</th>

                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <tr  class="clone_more_raw_materials">

                                            <td><select class="raw_materials_select cost_p">

                                                    <option>Select</option>

                                                    <?php

                                                    foreach ($raw_materials_select  as $rm)

                                                    {

                                                        echo "<option value='" . $rm->product_inventory_varients_id . "'>" . $rm->varient_code . "</option>";

                                                    }

                                                    ?>

                                                </select></td>

                                           

                                            <td><input class="p_quantity cost_p" type="text" name="product_invent"></td>

                                            <td><input class="p_cost cost_p" type="text" name=""></td>

                                            <td><input class="p_total_cost cost_p" type="text" name="p_total_cost"></td>

                                            <td><p class="clone">+ Clone</p><p class="remove-p-r">- Remove</p></td>

                                        </tr>

                                    </tbody>

                                </table>

                                <div class="col-sm-12">

                                    <div class="box-footer">

                                        <form action="<?= base_url('manufacturing/add_raw_materials_to_process')?>" method="post">

                                        

                                        <input type="hidden" name="process_id" value="<?= $process_id?>">

                                        <input type="text" name="r_values" id="r_values">

                                        <input type="submit" name="submit" value="submit" id="add_labour">

                                        <!-- <a data-toggle="tab" href="#menu2" ><button type="submit"  class="btn btn-info">Add Labour</button></a> -->

                                        </form>

                                    </div>

                                </div>

                            </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>

        </div>



    </div>

</div>





<div id="add_labour1" class="modal fade" role="dialog">

    <div class="modal-dialog">



        <!-- Modal content-->

        <div class="modal-content" style="width: 900px">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h3 class="modal-title">Add Raw Materials</h3>

            </div>

            <div class="modal-body" id="">

               <div class="box-body">

                                <table class="labour_values">

                                    <thead>

                                        <tr>

                                            <th>Labour Activity <a data-toggle="modal" data-target="#add_labour_modal"  class="pull-middle"> + Add</a></th>

                                            <th>No Of Labours</th>

                                            <th>Cost Per Hour</th>

                                            <th>Total Hours</th>

                                            <th>Total Cost</th>

                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <tr  class="clone_more_labour">

                                            <td><select class="labour_change l_cost">

                                                    <option>Select</option>

                                                    <?php

                                                    foreach ($labour1 as $lab)

                                                    {

                                                        echo "<option value ='" . $lab->labour_id . "'>" . $lab->activity_name . "</option>";

                                                    }

                                                    ?>

                                                </select></td>

                                            

                                            <td><input class="labour_quantity l_cost" type="text" name="labour_quantity"></td>

                                            <td><input class="labour_price l_cost" type="text" name=""></td>

                                            <td><input class="total_hours l_cost" type="text" name=""></td>

                                            <td><input class="total_labour_cost l_cost" type="text" name=""></td>

                                            <td><p class="clone-labour">+ Clone</p><p class="remove-p-r">- Remove</p></td>

                                        </tr>

                                    </tbody>

                                </table>

                                <div class="col-sm-12">

                                    <div class="box-footer">



                                         <form action="<?= base_url('manufacturing/add_labour_to_process')?>" method="post">

                                        

                                        <input type="hidden" name="process_id" value="<?= $process_id?>">

                                        <input type="hidden" name="q_values" id="q_values">

                                        <!-- <input type="text" name="labourIds" id="labourIds"> -->

                                        <input type="submit" name="submit" value="submit" id="add_over_head">

                                        <!-- <a data-toggle="tab" href="#menu2" ><button type="submit"  class="btn btn-info">Add Labour</button></a> -->

                                        </form>

                                        <a data-toggle="tab" href="#menu2" id="add_labour"><button type="button"  class="btn btn-info">Add Labour</button></a>

                                    </div>

                                </div>

                            </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>

        </div>



    </div>

</div>



<div id="scrap_model" class="modal fade" role="dialog">

    <div class="modal-dialog">



        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h3 class="modal-title">Scrap</h3>

            </div>

            <div class="modal-body" id="">

               <div class="box-body">

                <div class="row">

                    <form method="post" action="<?= base_url('manufacturing/add_scrap')?>">

                    <div class="col-md-6">

                                <label for="date">Type</label>

                                <div class="input-group">

                                    <select class="form-control" id="scrap_type" name="scrap_type" >

                                        <option value="Over Heads">Over Heads</option>

                                        <option value="Raw Materials">Raw Materials</option>

                                        <option value="Labour">Labour</option>

                                    </select>

                                    

                                 </div>

                                

                    </div>

                </div>

                <div class="row">

                     <div class="col-md-6">

                                <label for="date">Reason</label>

                                <div class="input-group">

                                    <input type="text" class="form-control" id="scrap_reason" name="scrap_reason" value="">

                                 </div>

                                

                            </div>

                     <div class="col-md-6">

                                <label for="date">Price</label>

                                <div class="input-group">

                                    <input type="text" class="form-control" id="scrap_price" name="scrap_price">

                                 </div>

                                

                            </div>

                            <div class="col-md-6">

                                <label for="date">Quantity</label>

                                <div class="input-group">

                                    <input type="text" class="form-control" id="scrap_quantity" name="scrap_quantity" value="">

                                 </div>

                                

                            </div>

                            <div class="col-md-6">

                                <label for="date">Reference Number</label>

                                <div class="input-group">

                                    <input type="text" class="form-control" id="scrap_reference_no" name="scrap_reference_no" value="">

                                 </div>

                                

                            </div>

                            <input type="hidden" name="process_id" value="<?= $process_id?>">

                </div>

                <div class="row">

                            <div class="col-md-4 col-md-offset-4">

                                <input type="submit" class="btn btn-default" id="submit" name="submit" value="submit" >

                            </div>

                            </form>

                </div>            

                </div>

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

    $("#create_json").click(function (e) {
        var oh_values = overhead_values();

// alert(oh_values)

        $("#o_h_quantity").val(oh_values)

        var oh_id = get_selected_over_head_id();

// alert(oh_id);

        $("#o_h_ids").val(oh_id)

    })

    $(document).on('change', '.over-head-change', function () {

        var id = $(this).val();

        var targetQuantity = $(this).closest($('tr')).find($('.overHeadquantity'));

        var targetPrice = $(this).closest($('tr')).find($('.overHeadCost'));

        var overHeadtotal = $(this).closest($('tr')).find($('.overHeadtotal'));

        $.ajax({

            url: base_url + "over_head/get_price",

            type: 'POST',

            data: {'id': id},

            success: function (result) {

                res = JSON.parse(result)

// alert(res[0].quantity)

                var labourQuantity = res[0].quantity;

                var labourtotal = res[0].over_head_cost_per_unit;

                targetQuantity.val(labourQuantity);

                targetPrice.val(labourtotal);

                overHeadtotal.val(labourQuantity * labourtotal)

            }

        })

    })

    $(document).on('click', '.clone-o-h', function () {

        $('.clone_more_o_h:last').clone().insertAfter(".clone_more_o_h:last");

    })

    $(document).on('click', '.remove-p-r', function () {

        $(this).closest($('tr')).remove();

    })

    $(document).on('keyup', '.p_quantity', function () {

        var targetQuantity = $(this).closest($('tr')).find($('.p_quantity'));

        var targetPrice = $(this).closest($('tr')).find($('.p_cost'));

        var totalCost = $(this).closest($('tr')).find($('.p_total_cost'));

        var total = targetPrice.val() * $(this).val();

        totalCost.val(total)

    })

    $(document).on('keyup', '.labour_quantity,.labour_price, .total_hours', function () {

        var labour_quantity = $(this).closest($('tr')).find($('.labour_quantity'));

        var labour_price = $(this).closest($('tr')).find($('.labour_price'));

        var total_hours = $(this).closest($('tr')).find($('.total_hours'));

        var total_labour_cost = $(this).closest($('tr')).find($('.total_labour_cost'));

        total_labour_cost.val(labour_price.val() * total_hours.val() * labour_quantity.val())

    })

    $(document).on('keyup', '.p_cost', function () {

        var targetQuantity = $(this).closest($('tr')).find($('.p_quantity'));

        var targetPrice = $(this).closest($('tr')).find($('.p_cost'));

        var totalCost = $(this).closest($('tr')).find($('.p_total_cost'));

        var total = targetQuantity.val() * $(this).val();

        totalCost.val(total)

    })

    $(document).on('keyup', '.overHeadquantity', function () {

        var overHeadCost = $(this).closest($('tr')).find($('.overHeadCost'));

        var overHeadtotal = $(this).closest($('tr')).find($('.overHeadtotal'));

        var total = overHeadCost.val() * $(this).val();

        overHeadtotal.val(total)

    })

    $(document).on('keyup', '.overHeadCost', function () {

        var overHeadquantity = $(this).closest($('tr')).find($('.overHeadquantity'));

        var overHeadtotal = $(this).closest($('tr')).find($('.overHeadtotal'));

        var total = overHeadquantity.val() * $(this).val();

        overHeadtotal.val(total)

    })



        $(document).on('change', '.raw_materials_select', function () {

        var id = $(this).val();

        var targetQuantity = $(this).closest($('tr')).find($('.p_quantity'));

        var targetPrice = $(this).closest($('tr')).find($('.p_cost'));

        var totalCost = $(this).closest($('tr')).find($('.p_total_cost'))

        $.ajax({

            url: base_url + "manufacturing/get_raw_materials_price",

            type: 'POST',

            data: {'id': id},

            success: function (result) {

                res = JSON.parse(result)

                var labourQuantity = res[0].purchase_price;

                var labourtotal = res[0].quantity;

                var total = parseInt(labourQuantity) * parseInt(labourtotal);

                targetQuantity.val(labourQuantity);

                targetPrice.val(labourtotal);

                totalCost.val(total)

            }

        })

    })

        $(document).on('click', '.clone', function () {

        $('.clone_more_raw_materials:last').clone().insertAfter(".clone_more_raw_materials:last");

    })

    $(document).on('click', '.remove-p-r', function () {    

        $(this).closest($('tr')).remove();

    })

    $(document).on('keyup', '.p_quantity', function () {

        var targetQuantity = $(this).closest($('tr')).find($('.p_quantity'));

        var targetPrice = $(this).closest($('tr')).find($('.p_cost'));

        var totalCost = $(this).closest($('tr')).find($('.p_total_cost'));

        var total = targetPrice.val() * $(this).val();

        totalCost.val(total)

    })

    $(document).on('change', '.labour_change', function () {

        var id = $(this).val();

        var targetQuantity = $(this).closest($('tr')).find($('.labour_quantity'));

        var targetPrice = $(this).closest($('tr')).find($('.labour_price'));

        var totalHours = $(this).closest($('tr')).find($('.total_hours'));

        var total_labour_cost = $(this).closest($('tr')).find($('.total_labour_cost'));

        $.ajax({

            url: base_url + "labour/get_labour_price",

            type: 'POST',

            data: {'id': id},

            success: function (result) {

                res = JSON.parse(result)

                var labourQuantity = res[0].no_of_labour;

                var labourtotal = res[0].cost_per_hour;

                var totalHr = res[0].total_no_hours;

                targetQuantity.val(labourQuantity);

                targetPrice.val(labourtotal);

                totalHours.val(totalHr);

                total_labour_cost.val(labourQuantity * labourtotal * totalHr);

            }

        })

    })

    // row cloan and remove for raw materials

    $(document).on('click', '.clone-labour', function () {

        $('.clone_more_labour:last').clone().insertAfter(".clone_more_labour:last");

    })

    $(document).on('click', '.remove-labour', function () {

        $(this).closest($('tr')).remove();

    })

    $(document).on('keyup', '.labour_quantity,.labour_price, .total_hours', function () {

        var labour_quantity = $(this).closest($('tr')).find($('.labour_quantity'));

        var labour_price = $(this).closest($('tr')).find($('.labour_price'));

        var total_hours = $(this).closest($('tr')).find($('.total_hours'));

        var total_labour_cost = $(this).closest($('tr')).find($('.total_labour_cost'));

        total_labour_cost.val(labour_price.val() * total_hours.val() * labour_quantity.val())

    })

    $("#create_json").click(function (e) {

        var oh_values = overhead_values();

// alert(oh_values)

        $("#o_h_quantity").val(oh_values)

        var oh_id = get_selected_over_head_id();

// alert(oh_id);

        $("#o_h_ids").val(oh_id)

    })

     function overhead_values() {

        var json = '{';

        var otArr = [];

        var tbl2 = $('.overHeadTable tbody tr').each(function (e) {

            x = $(this).children();

            var itArr = [];

            var t = 0;

            var l = 0;

            var count = 0;

            var column = $('.gen_table tbody tr').length;

            $(this).find(".oh_cost").each(function () {

                varient_array = ['over_head_id', 'quantity', 'price', 'cost'];

                itArr.push('"' + varient_array[l] + '"' + ':' + '"' + this.value + '"');

                l++;

            });

            otArr.push('"' + e + '": {' + itArr.join(',') + '}');

        })

        json += otArr.join(",") + '}'

        return json;

    }



        $("#add_labour").click(function () {

        var val = raw_materials();

        $("#r_values").val(val)



    })



 function raw_materials() {

        var json = '{';

        var otArr = [];

        var rowCount = $('#gen_table tbody tr').length;

// var i = 1;

        var tbl2 = $('.raw_materials tbody tr').each(function (e) {

            x = $(this).children();

            var itArr = [];

            var t = 0;

            var l = 0;

            var count = 0;

            var column = $('.gen_table tbody tr').length;

            $(this).find(".cost_p").each(function () {

                varient_array = ['raw_material_varient_id', 'quantity', 'price', 'cost'];

                itArr.push('"' + varient_array[l] + '"' + ':' + '"' + this.value + '"');

                l++;

            });

            otArr.push('"' + e + '": {' + itArr.join(',') + '}');

        })

        json += otArr.join(",") + '}'

        return json;

    }



    function test() {

        var json = '{';

        var otArr = [];

        var rowCount = $('#gen_table tbody tr').length;

// var i = 1;

        var tbl2 = $('.labour_values tbody tr').each(function (e) {

            x = $(this).children();

            var itArr = [];

            var t = 0;

            var l = 0;

            var count = 0;

            var column = $('.gen_table tbody tr').length;

            $(this).find(".l_cost").each(function () {

                varient_array = ['labour_id', 'no_of_labour', 'cost_per_hour', 'total_no_hours', 'total_cost'];

                itArr.push('"' + varient_array[l] + '"' + ':' + '"' + this.value + '"');

                l++;

            });

            otArr.push('"' + e + '": {' + itArr.join(',') + '}');

        })

        json += otArr.join(",") + '}'

        return json;

    }



    $("#add_over_head").click(function () {

        var json = test();

        $("#q_values").val(json);

// alert(json)

        var labourJson = get_selected_labour_id();

// alert(labourJson)

        $("#labourIds").val(labourJson)

    })



    $(document).ready(function(){

        $('body').on('click','.staus_change',function(){

            var m_id = $(this).data("id");

            var status = $(this).val();

            var change_text = $(this);



            $.ajax({

            url: base_url + 'manufacturing/change_status',

            type: 'POST',

            data:{'m_id':m_id,

                    'status':status},

            success: function (data)

            {

                // alert(data)

                change_text.val(data);

                // $("#quantity_price").html(data)

            }

        });

        })

    })



    var over_head_total = "";

    var raw_materials_total = Array();

    var labour_total = "";

    var scrap = "";



    $("#over_head_list tr").each(function(){

        over_head_total = $(this).find('td:nth(3)').text();

    })



     $("#raw_materials_total tr").each(function(){

        raw_materials_total.push(parseInt($(this).find('td:nth(3)').text())) ;

    })

     $("#labour_total tr").each(function(){

        labour_total = $(this).find('td:nth(4)').text();

    })

     $("#scrap tr").each(function(){

        scrap = $(this).find('td:nth(2)').text();

    })

     // alert(raw_materials_total)

     var over_head_sum = parseInt($("#over_head_sum").text());

    var raw_material_sum = parseInt($("#raw_material_sum").text());

    var labour_sum = parseInt($("#labour_sum").text());

    var scrap_sum = parseInt($("#scrap_sum").text());

     var total =  over_head_sum+  raw_material_sum+  labour_sum+  scrap_sum;

       // alert(total)

       var quantity = $("#product_quantity").val();

       var avgCost = total/quantity;    

       $("#total_value").text(total)

       $("#per_unit_cost").text(avgCost)





</script>

<script src="<?php echo base_url('assets/js/product/') ?>product.js"></script>

