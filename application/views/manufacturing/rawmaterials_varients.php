<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');

?>

<style type="text/css">

    #add_more{margin-top: 25px}

    .table-scroll{width: 100%;overflow: scroll;display: none;}

</style>



<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">



    </section>



    <div class="fixed-breadcrumb">

        <ol class="breadcrumb abs-ol">

            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>

            <li><a href="<?php echo base_url('product/varient_list'); ?>">Product</a></li>

            <li class="active">Raw Material Varient List</li>



        </ol>

    </div>





    <!-- Main content -->

    <section class="content mt-50">

        <div class="row">



            <!-- right column -->

            <div class="col-md-12">

                <div class="box">

                    <div class="box-body">

                        <div class="table-scroll">



                            <table class="gen_table table table-bordered table-striped table-hover table-responsive no-footer dtr-inline collapsed">

                                <thead>

                                    <tr id="genarate_table">



                                    </tr>

                                </thead>

                                <tbody>

                                    <tr id="generate_table_data">



                                    </tr>

                                </tbody>

                            </table>

                        </div>

                        <form method="post" action="<?= base_url('product/update_product_varients') ?>">

                            <input type="hidden" name="table_data" id="table_data" >

                            <input type="hidden" name="key_value" id="key_value" >

                            <input type="hidden" name="product_varient_value_id" value="<?= $id ?>">

                            <!-- <input type="hidden" name="product_varient_key_id" value=""> -->

                            <button type="submit" class="btn btn-sm btn-info" id="product_modal_submit" style="display: none"> Submit</button>

                        </form>

                    </div>

                </div>

            </div>

        </div>



        <div class="row">



            <!-- right column -->

            <div class="col-md-12">

                <div class="box">

                    <div class="box-header with-border">

                        <h3 class="box-title">Raw Material Varient</h3>



                        <p id="getRowsValue"></p>

                        <?php

                        if ($access_module_privilege->add_privilege == "yes")

                        {

                            ?>

                            <a class="btn btn-sm btn-info pull-right btn-flat" href="<?php echo base_url('product/add'); ?>">Add Product</a>

                            <a class="btn btn-sm btn-info pull-right btn-flat" href="<?php echo base_url('barcode/get_barcode'); ?>">Print Barcode</a>

                            <a data-toggle="modal"  data-target="#keyValue" class="btn btn-sm btn-info pull-right btn-flat" href="#">Add varients</a>

                        <?php } ?>

                    </div>

                    <!-- /.box-header -->

                    <div class="box-body">

                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >

                            <thead>

                                <tr>

                                 <!-- <th>Date</th> -->

                                    <th style="min-width: 100px;text-align: center;">Raw Material Name</th>

                                    <th style="min-width: 80px;text-align: center;">Varient Name</th>

                                    <th style="min-width: 200px;text-align: center;">Varient Code</th>

                                    <th style="min-width: 100px;text-align: center;">Reordering Point</th>



                                    <th style="max-width: 100px;text-align: center;"> Purchase Price</th>

                                    <th style="max-width: 100px;text-align: center;"> Selling Price</th>

                                    <th style="min-width: 100px;text-align: center;">Quantity</th>

                                    <th style="min-width: 100px;text-align: center;">Varient Unit</th>

                                    <th style="min-width: 100px;text-align: center;">Damage Stock</th>



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



<div id="productVarient" class="modal fade" role="dialog">

    <div class="modal-dialog">



        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Edit Varient</h4>

            </div>

            <div class="modal-body" id="varientForm">

                <form role="form" method="post">

                    <div class="row">

                        <input type="hidden" name="p_id" id="p_id">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label for="product_code">Varient Name <span class="validation-color">*</span></label>

                                <input type="text" class="form-control" tabindex="1" id="varient_name" name="ajax_product_code" value="">

                            </div>

                        </div>



                        <div class="col-md-6">

                            <div class="form-group">

                                <label for="product_code">Varient Code <span class="validation-color">*</span></label>

                                <input type="text" class="form-control" tabindex="1" id="varient_code" name="ajax_product_code" value="">

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label for="product_code">Purchase Price <span class="validation-color">*</span></label>

                                <input type="text" class="form-control" tabindex="1" id="purchase_price" name="ajax_product_code" value="">

                            </div>

                        </div>



                        <div class="col-md-6">

                            <div class="form-group">

                                <label for="product_code">Selling Price <span class="validation-color">*</span></label>

                                <input type="text" class="form-control" tabindex="1" id="selling_price" name="ajax_product_code" value="">

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <!-- <div class="col-md-6">

                          <div class="form-group">

                        <label for="product_code">Quantity <span class="validation-color">*</span></label>

                        <input type="text" class="form-control" tabindex="1" id="quantity" name="ajax_product_code" value="" >

                        </div>

                        </div> -->



                        <div class="col-md-6">

                            <div class="form-group">

                                <label for="product_code">Varient Unit <span class="validation-color">*</span></label>

                                <select class="form-control select2" id="varient_unit" name="varient_unit" style="width: 100%;" tabindex="4">

                                    <option value="">Select</option>

                                    <?php

                                    foreach ($uqc as $value)

                                    {

                                        echo "<option value='$value->uom - $value->description'" . set_select('brand', $value->id) . ">$value->uom - $value->description</option>";

                                    }

                                    ?>

                                </select>

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-5">

                        </div>

                        <a class="btn btn-sm btn-info  btn-flat" data-dismiss="modal"  id='post_varients' href="#">Edit Varients</a>

                    </div>



                </form>

            </div>

            <div class="modal-footer">

                <button type="button" id='firstClose' class="btn btn-default" data-dismiss="modal">Close</button>

            </div>

        </div>



    </div>

</div>



<!-- varient key value -->



<div id="keyValue" class="modal fade" role="dialog">

    <div class="modal-dialog">

        <?php

        $array_varients = htmlspecialchars(json_encode($varients_key));

        ?>



        <input type="hidden" name="varients_array" id="varients_array" value="<?php echo $array_varients; ?>">



        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Varient Key Value</h4>

            </div>

            <div class="modal-body">

                <div class="well get_num">

                    <div class="row">

                        <div class="col-sm-3">

                            <div class="form-group">

                                <label for="date">Varient Key<span class="validation-color">*</span></label>

                                <select class="form-control select2 key_varient" id="varient_key_1" name="varient_key[]" style="width: 100%;">

                                    <option value="">Select</option>

                                    <?php

                                    foreach ($varients_key as $row)

                                    {

                                        echo "<option value='$row->varients_id'" . set_select('category', $row->varients_id) . ">$row->varient_key</option>";

                                    }

                                    ?>

                                </select>

                            </div>

                        </div>

                        <div class="col-sm-3">

                            <div class="form-group">

                                <label for="date">Varient Value<span class="validation-color">*</span></label>

                                <select multiple="" class="form-control select2 value_varient" id="varient_value_1" name="varient_value[]" style="width: 100%;">



                                </select>

                                <span class="validation-color" id="err_voucher_date"></span>



                            </div>

                        </div>

                        <div class="col-sm-3">

                            <p class="add-more" id="add_more"><i class="fa fa-files-o" aria-hidden="true"> </i> Add More</p>

                        </div>

                    </div>

                    <div id="newRows">



                    </div>

                    <input type="radio" id="getRows" name="combination" data-dismiss="modal" value="manual" onclick="get_dynamic_table()" />&nbsp<label  fclass="form-group">Manual</label>



                </div>



            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>

            </div>

        </div>



    </div>

</div>





<!-- </div> -->



<div id="myModal2" class="modal fade" role="dialog">

    <div class="modal-dialog">



        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Status</h4>

            </div>

            <div class="modal-body">

                <div class="alert alert-success">

                    <strong>Success!</strong> Updated .

                </div>



            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>

            </div>

        </div>



    </div>

</div>





<div id="quantity_model" class="modal fade" role="dialog">

    <div class="modal-dialog">



        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Add Stock</h4>

            </div>

            <div class="modal-body" id="productForm">

                <form role="form" method="post" action = "<?= base_url('Manufacturing/update_quantity') ?>">

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

                    <button type="submit" id="quantity_history_submit" class="btn btn-info">Update</button>

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





<div id="stock_management" class="modal fade" role="dialog">

    <div class="modal-dialog">



        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Add Stock</h4>

            </div>

            <div class="modal-body" id="productForm">

                <form role="form" method="post" action = "<?= base_url('manufacturing/move_to_damage') ?>">

                    <div class="row">

                        <input type="hidden" name="stock_p_id" id="stock_p_id">

                        <input type="hidden" name="stock_url" id="stock_url" value="<?= $this->uri->segment(3) ?>">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label for="product_code">Stock <span class="validation-color">*</span></label>

                                <select id="damaged_stock" name="damaged_stock"></select>



                            </div>

                        </div>





                    </div>

                    <button type="submit" id="stock_update_btn" class="btn btn-info">Update</button>

                </form>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>

            </div>

        </div>



    </div>

</div>





<!-- function to update damaged stock -->



<div id="damages_quantity" class="modal fade" role="dialog">

    <div class="modal-dialog">



        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Move to Stock</h4>

            </div>

            <div class="modal-body" id="productForm">

                <form role="form" method="post" action = "<?= base_url('product/move_to_stock') ?>">

                    <div class="row">

                        <input type="hidden" name="move_to_id" id="move_to_id">

                        <input type="hidden" name="move_to_url" id="move_to_url" value="<?= $this->uri->segment(3) ?>">





                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label for="product_code">Stock</label>

                                <select id="move_to_stock" name="move_to_stock"></select>



                            </div>

                        </div>

                    </div>

                    <button type="submit" id="move_to_stock_submit" class="btn btn-info">Update</button>

                </form>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>

            </div>

        </div>



    </div>

</div>









<?php

$this->load->view('layout/footer');

$this->load->view('general/delete_modal');

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

                "url": base_url + "manufacturing/get_manufacturing_varient/<?= $id ?>",

                "dataType": "json",

                "type": "POST",

                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}

            },

            "columns": [

                {"data": "product_code"},

                {"data": "varient_name"},

                {"data": "product_name"},

                {"data": "reordering_point"},

                {"data": "purchase_price"},

                {"data": "selling_price"},

                {"data": "quantity"},

                {"data": "varient_unit"},

                {"data": "damaged_stock"},

                {"data": "action"}

            ]

        });

    });





    function getVarients(id) {





        var productId = id;



        $.ajax({

            type: "GET",

            url: base_url + "manufacturing/get_varient_values/" + productId,

            success: function (data)

            {



                // $("#varientForm").html(data)

                var obj = JSON.parse(data);

                // alert(obj.id)



                $("#varient_name").val(obj.name)

                $("#varient_code").val(obj.code)

                $("#purchase_price").val(obj.purchase_price)

                $("#selling_price").val(obj.selling_price)

                $('#quantity').val(obj.quantity)

                $('#p_id').val(obj.id)

                // $("#varient_unit").val(obj.varient_unit)

                $("#varient_unit").append('<option selected value="' + obj.varient_unit + '">' + obj.varient_unit + '</option>');



                // $('#varient_unit option:contains('+obj.varient_unit+')').prop('selected',true);

                // $("#varient_unit").text(obj.varient_unit)

                $('.select2-varient_unit-container').text(obj.varient_unit);





            }

        });

    }



    function get_key_val(id) {



        var productId = id;
        $.ajax({

            type: "GET",

            url: base_url + "product/get_varient_key_values/" + productId,

            success: function (data)

            {

                $("#varintkey_value").empty();



                $("#getkeyValue").html(data)

                // var myArray = JSON.parse(data);



                // var count =myArray.length;



                // for(var i =0;i< count;i++){



                //   $("#varintkey_value").append('<lable>'+myArray[i].varient_key+'</lable><br><input type ="text" value="'+myArray[i].varients_value+'"><br>')



                // }



            }

        });

    }



    $("#post_varients").click(function (e) {





        var p_id = $("#p_id").val()

        var varient_name = $("#varient_name").val()

        var varient_code = $("#varient_code").val()

        var purchase_price = $("#purchase_price").val()

        var selling_price = $("#selling_price").val()

        // var quantity=$("#quantity").val()

        var varient_unit = $("#varient_unit").val()



        $.ajax({

            type: "POST",

            url: base_url + "manufacturing/edit_product_vaarient/",

            data: {'id': p_id,

                'varient_name': varient_name,

                'varient_code': varient_code,

                'purchase_price': purchase_price,

                'selling_price': selling_price,

                // 'quantity':quantity,

                'varient_unit': varient_unit

            },

            success: function (data) {

                if (data == 'success') {

                    $("#firstClose").trigger("click");

                    // $('.modal-backdrop').remove()

                    $("#myModal2").modal('show');

                    // $('.modal-backdrop fade in')

                    var close_tr = $("#" + p_id).closest('tr');

                    close_tr.each(function (e) {

                        x = $(this).children();

                        x.eq(1).text(varient_name);

                        x.eq(2).text(varient_code);

                        x.eq(3).text(purchase_price);

                        x.eq(4).text(selling_price);

                        x.eq(5).text(quantity);

                        x.eq(6).text(varient_unit);

                    })

                }

            }

        })

        e.preventDefault();

    })







    varients_array = JSON.parse($('#varients_array').val());

    var x = 1;

    var in_array_data = new Array();



    $('#add_more').click(function ()

    {



        if ($('.key_varient').val() != "")

        {



            x++;

            var addVarients = "<div class='row mt-25'><div class='col-sm-3'> <select class='form-control select2 key_varient' id='varient_key_" + x + "' name='varient_key[]' style='width: 100%;'><option value=''>Select</option></select></div><div class='col-sm-3'><select multiple='' class='form-control select2 value_varient' id='varient_value_" + x + "' name='varient_value[]' style='width: 100%;'></select></div><div class='col-sm-3'><p class='removeDiv'><i class='fa fa-trash' aria-hidden='true'></i> Remove</p></div><br></div>";



            $('#newRows').append(addVarients);



            for (var i = 0; i < varients_array.length; i++)

            {



                $('#varient_key_' + x).append('<option value="' + varients_array[i].varients_id + '">' + varients_array[i].varient_key + '</option>');

            }



            $('.key_varient').select2();

            $('.value_varient').select2();



            call_css();



        }

    })





    $('body').on('click', '.removeDiv', function () {

        $(this).closest('.row').remove();

        x--;

    });





    $(document).on('change', '.key_varient', function (event)

    {



        // alert(($('.get_num select').length)/2);

        var id = $(this).attr('id');

        var id_index = id.split("_");//get the x value

        var value = $(this).val();



        $.ajax({

            url: base_url + 'varients/get_varient_value',

            dataType: 'JSON',

            method: 'POST',

            data: {

                'varient_id': value



            },

            success: function (result)

            {

                var data = result;

                $('#varient_value_' + id_index[2]).html('');

                for (i = 0; i < data.length; i++)

                {

                    $('#varient_value_' + id_index[2]).append('<option value="' + data[i].varients_value_id + '">' + data[i].varients_value + '</option>');

                }

            }

        });



    });













    function get_dynamic_table() {



        $('#genarate_table').empty();

        $("#generate_table_data").empty();

        var numberOfColoumns = ($('.get_num select').length) / 2;

        $("#getRowsValue").text("+ Add More")

        $("#product_modal_submit").show();

        $('.table-scroll').show()



        for (t = 1; t <= numberOfColoumns; t++) {



            var rows = "<th>" + $('#varient_key_' + t + ' option:selected').text() + "</th>";

            var rowsText = $("select#varient_value_" + t + " option:selected").map(function () {

                return '<option value=' + $(this).val() + ' >' + $(this).text() + '</option>';

            }).get();

            // var rowsVal = $("select#varient_value_"+t+" option:selected").map(function() {return '<option>'+$(this).val()+'</option>';}).get();



            var cols = "<td><select name='var_val[]' class='select2'>" + rowsText + "</select></td>";



            var remove = "<td>remove</td>";



            $('#genarate_table').append(rows);

            $("#generate_table_data").append(cols);



        }

        $('#genarate_table:last').append("<td class='removeTd'>Varient Code</td>");

        $('#genarate_table:last').append("<td class='removeTd'>Varient Name</td>");

        $('#genarate_table:last').append("<td class='removeTd'>Purchase price</td>");

        $('#genarate_table:last').append("<td class='removeTd'>Selling Price</td>");

        $('#genarate_table:last').append("<td class='removeTd'>Unit</td>");

        $('#genarate_table:last').append("<td class='removeTd'>Quantity</td>");

        $('#genarate_table:last').append("<td class='removeTd'>Action</td>");









        $('#generate_table_data:last').append("<td class='input_purchase'><input class='diff_varient' type='text'></td>");

        $('#generate_table_data:last').append("<td class='input_purchase'><input  class='diff_varient' type='text'></td>");

        $('#generate_table_data:last').append("<td class='input_purchase'><input class='diff_varient' type='text'></td>");

        $('#generate_table_data:last').append("<td class='input_purchase'><input class='diff_varient' type='text'></td>");



        $('#generate_table_data:last').append("<td class='input_purchase'><select class='diff_varient'><option value=''>select</option> <?php

foreach ($uqc as $value)

{

    echo "<option value='$value->uom - $value->description'" . set_select('brand', $value->id) . ">$value->uom - $value->description</option>";

}

?></select></td>");

        $('#generate_table_data:last').append("<td class='input_purchase'><input class='diff_varient' type='text'></td>");

        $('#generate_table_data:last').append("<td class='removeTd'><i class='fa fa-trash'></i>remove</td>");

    }



    // row cloan

    $(document).on('click', '#getRowsValue', function () {

        $('#generate_table_data:last').clone().insertAfter("#generate_table_data:last");

    })



    $(document).on('click', '.removeTd', function () {

        $(this).parent('tr').empty();

    })

    //row clone end



    function json_string() {

        var values = $("input[name='var_val[]']")

                .map(function () {

                    return $(this).val();

                }).get();



    }

    $("#product_modal_submit").click(function () {

        var json = html2json();

        var json1 = get_key_value();

        // alert(json);

        // alert(json1);



        $("#table_data").val(json);

        $("#key_value").val(json1);

        $("#product_modal_submit").submit();



    });



    function html2json() {

        var json = '{';

        var otArr = [];



        var rowCount = $('#gen_table tbody tr').length;





        // var i = 1;

        var tbl2 = $('.gen_table tbody tr').each(function (e) {

            x = $(this).children();

            var itArr = [];

            var t = 0;

            var l = 0;

            var count = 0;

            var column = $('.gen_table tbody tr').length;



            $(this).find(".diff_varient").each(function () {





                varient_array = ['varient_code', 'varient_name', 'purchase_price', 'selling_price', 'varient_unit', 'quantity'];



                itArr.push('"' + varient_array[l] + '"' + ':' + '"' + this.value + '"');

                l++;

            });



            otArr.push('"' + e + '": {' + itArr.join(',') + '}');

        })



        json += otArr.join(",") + '}'



        return json;

    }



    function get_key_value() {



        var json = '{';

        var otArr = [];



        var rowCount = $('#gen_table tbody tr').length;





        // var i = 1;

        var tbl2 = $('.gen_table tbody tr').each(function (e) {

            x = $(this).children();

            var itArr = [];

            var t = 0;

            var l = 0;

            var count = 0;

            x.each(function () {

                count++;





                if ($(this).find(".select2 option:selected").text() != "") {







                    t++;



                    var r = $('#varient_key_' + t + ' option:selected').val();

                    itArr.push('"' + r + '"' + ':' + '"' + $(this).find('option:selected').val() + '"');







                }

                // else if(!$(this).find('td>.input_purchase').val() ){

                //    l++;

                //     varient_array = ['','varient Code','varient name','purchase price', 'selling price',''];



                //     itArr.push('"'+varient_array[l-1]+ '"' +':'+'"' + $(this).find('input[type=text]').val()+'"' );

                // }





            });

            otArr.push('"' + e + '": {' + itArr.join(',') + '}');

        })

        json += otArr.join(",") + '}'



        return json;

    }



    function get_quantity(id) {

        $.ajax({

            url: base_url + 'manufacturing/get_quantity_values/' + id,

            method: 'GET',

            success: function (result)

            {

                $("#qp_id").val(id)



                $("#quantity_price").html(result)





            }

        });



    }



    function stock(id) {



        $.ajax({

            url: base_url + 'manufacturing/get_stock/' + id,

            method: 'GET',

            success: function (result)

            {



                $('#stock_p_id').val(id);



                var res = JSON.parse(result);



                // alert(res.quantity)

                var totalQuantity = "<option>select</option>"

                for (var i = 1; i <= res.quantity; i++) {

                    totalQuantity += "<option value='" + i + "'>" + i + "</option>";

                    // console.log(i)

                }

                $('#damaged_stock').html(totalQuantity)



            }

        });





    }



    function get_damaged_quantity(id) {



        $.ajax({

            url: base_url + 'manufacturing/get_damaged_products/' + id,

            method: 'GET',

            success: function (result)

            {



                $('#move_to_id').val(id);



                var res = JSON.parse(result);



                var totalQuantity = "<option>select</option>"

                for (var i = 1; i <= res.damaged_stock; i++) {

                    totalQuantity += "<option value='" + i + "'>" + i + "</option>";

                }

                $('#move_to_stock').html(totalQuantity)



            }

        });



    }



</script>

