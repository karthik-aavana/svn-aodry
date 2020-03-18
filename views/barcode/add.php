<?php

defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('barcode'); ?>">Barcode</a></li>
            <li class="active">Add </li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add</h3>
                        <a class="btn btn-sm btn-default pull-right back_button" href1="<?php echo base_url('barcode'); ?>">Back</a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post" action="<?php echo base_url('barcode/barcode_gen'); ?>">
                                <div class="col-sm-12">

                                    <div class="well">

                                        <div class="form-group">

                                            Add Product

                                            <input id="add_item" class="form-control" type="text" name="add_item" placeholder="Enter Product Code/Name" >

                                        </div>

                                    </div>

                                </div>

                                <!--

                                <div class="col-sm-12">

                                  <div class="well">

                                    <div class="row">

                                      <div class="col-sm-8">

                                        <span class="validation-color" id="err_product"></span>

                                      </div>





                                      <div class="col-sm-12 search_sales_code">



                                        <a href="" data-toggle="modal" data-target="#item_modal" class="pull-left">+ Add Item</a>



                                        <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product/Service Code/Name" >

                                      </div>







                                      <div class="col-sm-8">

                                        <span class="validation-color" id="err_sales_code"></span>

                                      </div>

                                    </div>

                                  </div>

                                </div> -->

                                <div class="col-sm-12">

                                    <div class="form-group">

                                        <label>Inventory Items</label>

                                        <table class="table items table-striped table-bordered table-condensed table-hover sales_table table-responsive" name="sales_data" id="sales_data">

                                            <thead>

                                                <tr>

                                                    <th width="1%"><i class="fa fa-trash-o"></i></th>

                                                    <th class="span2" width="1%">Product Name</th>

                                                    <th class="span2" width="1%">Quantity</th>

                                                </tr>

                                            </thead>

                                            <tbody id="sales_table_body">

                                            </tbody>

                                        </table>

                                        <input type="hidden" name="table_data" id="table_data">

                                    </div>

                                </div>

                                <div class="col-sm-12">

                                    <div class="well">

                                        <div class="form-group">

                                            <label>Style</label>

                                            <?php

                                            $opts = array(

                                                    '' => 'Select',

                                                    40 => '40_per_sheet',

                                                    30 => '30_per_sheet',

                                                    24 => '24_per_sheet',

                                                    20 => '20_per_sheet',

                                                    18 => '18_per_sheet',

                                                    14 => '14_per_sheet',

                                                    12 => '12_per_sheet',

                                                    10 => '10_per_sheet',

                                                    50 => 'continuous_feed' );

                                            ?>

<?= form_dropdown('style', $opts, set_value('style', 24), 'class="form-control select2 tip" id="style" required="required"'); ?>

                                            <div class="row cf-con" style="margin-top: 10px; display: none;">

                                                <div class="col-xs-4">

                                                    <div class="form-group">

                                                        <div class="input-group">

<?= form_input('cf_width', '', 'class="form-control" id="cf_width"'); ?>

                                                            <span class="input-group-addon"></span>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                            <span class="help-block"></span>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-sm-12">

                                    <div class="box-footer">

                                        <button type="submit" id="sales_submit" name="submit" value="add" class="btn btn-info">Add</button>

                                        <button type="submit" id="sales_pay_now" name="submit" value="pay_now" class="btn btn-success"> Receive Now </button>

                                        <span class="btn btn-default" id="sale_cancel" onclick="cancel('sales')">Cancel</span>

                                    </div>

                                </div>





                            </form>

                        </div>

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

?>

<script type="text/javascript">

    var mapping = {};



    if (typeof count_data === 'undefined' || count_data === null)

    {

        var count_data = 0;

    }

    var table_index = count_data;

// get items to table starts

    $('#add_item').autoComplete(

            {

                minChars: 1,

                cache: false,

                source: function (term, suggest)

                {

                    term = term.toLowerCase();

                    $.ajax(

                            {

                                url: base_url + "sales/get_sales_suggestions/" + term,

                                type: "GET",

                                dataType: "json",

                                success: function (data)

                                {



                                    var suggestions = [];

                                    for (var i = 0; i < data.length; ++i) {

                                        suggestions.push(data[i].item_code + ' ' + data[i].item_name);

                                        mapping[data[i].item_code] = data[i].item_id + '-' + data[i].item_type;

                                    }

                                    suggest(suggestions);

                                }

                            })

                },

                onSelect: function (event, ui)

                {

                    var code = ui.split(' ');

                    $.ajax(

                            {

                                url: base_url + 'sales/get_table_items/' + mapping[code[0]],

                                type: "GET",

                                dataType: "JSON",

                                success: function (data)

                                {

                                    add_row(data);

                                    call_css();

                                    $('#add_item').val('')

                                }

                            })

                }

            });

//delete rows

    $("#sales_table_body").on("click", "a.deleteRow", function (event)

    {

        deleteRow($(this).closest("tr"));

        $(this).closest("tr").remove();



    });



//function to add new row to datatable

    function add_row(data)

    {

        var flag = 0;



        var item_id = data.item_id;

        var item_type = data.item_type;

        var branch_id = data.branch_id;

        if (item_type == 'service')

        {

            var item_code = data[0].service_code;

            var item_name = data[0].service_name;

            var item_hsn_sac_code = data[0].service_hsn_sac_code;



        } else

        {

            var item_code = data[0].product_code;

            var item_name = data[0].product_name;

            var item_hsn_sac_code = data[0].product_hsn_sac_code;





        }

        var temp = {

            "item_id": item_id,

            "item_type": item_type,



        };



        table_index = +table_index;

        var data_item = {

            "item_id": item_id,

            "item_type": item_type,

            "item_code": item_code,

            "item_name": item_name,

            "item_hsn_sac_code": item_hsn_sac_code,

            "item_key_value": table_index

        };

        var newRow = $("<tr id=" + table_index + ">");

        var cols = "";

        cols += "<td><a class='deleteRow'> <img src='" + base_url + "assets/images/bin_close.png' /> </a><input type='hidden' name='item_key_value'  value=" + table_index + "><input type='hidden' name='item_id' value=" + item_id + "><input type='hidden' name='item_type' value=" + item_type + "><input type='hidden' name='item_code' value='" + item_code + "'></td>";

        if (item_type == 'product')

            cols += "<td>" + item_name + "<br>HSN:" + item_hsn_sac_code + "</td>";

        else

            cols += "<td>" + item_name + "<br>SAC:" + item_hsn_sac_code + "</td>";



        cols += "<td>" + "<input type='text' class='form-control form-fixer text-center float_number' value='1' data-rule='quantity' name='item_quantity'>" + "</td>";





        cols += "</tr>";



        newRow.append(cols);

        $("#sales_table_body").append(newRow);

        table_index++;

        calculateRow(newRow);



    }

    function calculateRow(row)

    {

        var key = +row.index();

        var table_index = +row.find('input[name^="item_key_value"]').val();

        var item_quantity = +row.find('input[name^="item_quantity"]').val();

        var item_id = +row.find('input[name^="item_id"]').val();

        var item_type = row.find('input[name^="item_type"]').val();

        var item_code = row.find('input[name^="item_code"]').val();

        if (sales_data[key] == null)

        {

            var temp = {

                "item_id": item_id,

                "item_type": item_type,

                "item_code": item_code,

                "item_key_value": table_index,

            };

            sales_data[key] = temp

        }

        sales_data[key].item_quantity = item_quantity;

        sales_data[key].item_key_value = table_index;

        var table_data = JSON.stringify(sales_data);



        $('#table_data').val(table_data);

    }

    function deleteRow(row)

    {

        var table_index = +row.find('input[name^="item_key_value"]').val();

        for (var i = 0; i < sales_data.length; i++)

        {

            if (sales_data[i] != null && sales_data[i].item_key_value == table_index)

            {

                sales_data.splice(i, 1)

            }

        }



        var table_data = JSON.stringify(sales_data);

        $('#table_data').val(table_data)

    }

</script>

