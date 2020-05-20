<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    $this->load->view('layout/header');
?>

<div class="content-wrapper">
    <section class="content-header"></section>

    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('product/varient_list'); ?>">Product</a></li>
            <li class="active">Generate Barcode</li>
        </ol>
    </div>

    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Generate Barcode</h3>
                        <a class="btn btn-sm btn-default pull-right back_button" href1="<?php echo base_url('barcode'); ?>">Back </a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post" action="<?php echo base_url('barcode/print_roll_barcode'); ?>" target="_blank">
                                <div class="col-sm-12">
                                    <div class="well">
                                        <div class="form-group">
                                            Select Purchase Number
                                            <select class="form-control select2" id="purchase_invoice" name="purchase_invoice" style="width: 100%;">
                                                <option value="">Select</option>
                                                <?php
                                                foreach ($purchase_invoice as $row) {
                                                    echo "<option value='$row->purchase_id'>$row->purchase_invoice_number ($row->purchase_grn_number)</option>";
                                                }
                                                ?>
                                            </select>
                                            <!-- <input id="add_item" class="form-control" type="text" name="add_item" placeholder="Enter Product Code/Name" > -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Product Items</label>
                                        <table class="table items table-striped table-bordered table-condensed table-hover sales_table table-responsive" name="sales_data" id="sales_data">
                                            <thead>
                                                <tr>
                                                    <th width="2%"><img src="<?php echo base_url(); ?>assets/images/bin1.png" /></th>
                                                    <th class="span2" width="15%">Article</th>
                                                    <th class="span2" width="25%">Product Name</th>
                                                    <th class="span2" width="25%">Barcode</th>
                                                    <th class="span2" width="15%">Quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sales_table_body"></tbody>
                                        </table>
                                        <input type="hidden" name="table_data" id="table_data">
                                    </div>
                                </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" id="print" name="print" value="print" class="btn btn-info" target="_blank">Get Barcode</button>
                            <span class="btn btn-default" id="sale_cancel" onclick="cancel('barcode')">Cancel</span>
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

    var sales_data = new Array();
    var mapping = {};
    <?php 
        if ($this->input->post('print')){
    ?>
        $(window).load(function () {
            $('html, body').animate({
                scrollTop: ($("#barcode-con").offset().top) - 15
            }, 1000);
        });
    <?php } ?>

    if (typeof count_data === 'undefined' || count_data === null){
        var count_data = 0;
    }

    var table_index = count_data;
   

    $('#purchase_invoice').on('change',function(){
        var purchase_id = $(this).val();
        $.ajax({
            url: base_url + 'barcode/get_purchase_details',
            type: 'POST',
            dataType:'json',
            data: { purchase_id: purchase_id},
            success: function (result) {
                $('#sales_table_body').html('');
                sales_data = new Array();
                table_index = 0;
                $.each(result,function(k,v){
                    add_row(v);
                    call_css();
                })
            }
        });
    })
    //delete rows

    $("#sales_table_body").on("click", "a.deleteRow", function (event){
        deleteRow($(this).closest("tr"));
       $(this).closest("tr").remove();
    });

    $("#sales_table_body").on("change blur", 'input[name^="item_quantity"]', function (event){
        calculateRow($(this).closest("tr"));
    });
    //function to add new row to datatable

    function add_row(data){
       
        var flag = 0;
        var item_id = data.product_id;
        var item_name = data.product_name;
        var item_barcode_symbology = data.product_barcode;
        var item_code = data.product_code;
        var item_quantity = data.purchase_item_quantity;
        var temp = {
            "item_id": item_id,
            'item_code' : item_code,
            "item_barcode_symbology": item_barcode_symbology,
        };

        table_index = +table_index;
        var data_item = {
            "item_id": item_id,
            "item_barcode_symbology": item_barcode_symbology,
            "item_code": item_code
        };

        var newRow = $("<tr id=" + table_index + ">");

        var cols = "";

        cols += "<td><a class='deleteRow'> <img src='" + base_url + "assets/images/bin_close.png' /> </a><input type='hidden' name='item_key_value'  value=" + table_index + "><input type='hidden' name='item_id' value=" + item_id + "><input type='hidden' name='item_barcode_symbology' value=" + item_barcode_symbology + "><input type='hidden' name='product_combination_id' value=" + data.product_combination_id + "><input type='hidden' name='varient_value_id' value=" + data.varient_value_id + "><input type='hidden' name='product_unit' value=" + data.product_unit + "><input type='hidden' name='item_code' value='" + item_code + "'><input type='hidden' name='product_mrp' value='" + data.product_mrp_price + "'><input type='hidden' name='mfg_date' value='" + data.mfg_date + "'><input type='hidden' name='brand_name' value='" + data.brand_name + "'><input type='hidden' name='category_name' value='" + data.category_name + "'><input type='hidden' name='item_name' value='" + item_name + "'></td>";
        cols += "<td>" +  item_code + "</td>";
        cols += "<td>" + item_name + "</td>";
        cols += "<td>" +  item_barcode_symbology + "</td>";

        cols += "<td>" + "<input type='text' class='form-control form-fixer text-center float_number' value='"+item_quantity+"' data-rule='quantity' name='item_quantity'>" + "</td>";

        cols += "</tr>";

        newRow.append(cols);
        $("#sales_table_body").append(newRow);
        table_index++;
        calculateRow(newRow);
    }

    function calculateRow(row){

        var key = +row.index();
        var table_index = +row.find('input[name^="item_key_value"]').val();
        var item_quantity = +row.find('input[name^="item_quantity"]').val();
        var item_id = +row.find('input[name^="item_id"]').val();
        var item_barcode_symbology = row.find('input[name^="item_barcode_symbology"]').val();
        var product_combination_id = row.find('input[name^="product_combination_id"]').val();
        var varient_value_id = row.find('input[name^="varient_value_id"]').val();
        var item_code = row.find('input[name^="item_code"]').val();
        var item_name = row.find('input[name^="item_name"]').val();

        if (sales_data[key] == null){
                var temp = {
                    "item_id": item_id,
                    "item_barcode_symbology": item_barcode_symbology,
                    'product_combination_id' :product_combination_id,
                    "varient_value_id" : varient_value_id,
                    "brand_name" : row.find('input[name=brand_name]').val(),
                    "category_name" : row.find('input[name=category_name]').val(),
                    'mfg_date' : row.find('input[name^="mfg_date"]').val(),
                    'mrp' : row.find('input[name^="product_mrp"]').val(),
                    'product_unit' : row.find('input[name^="product_unit"]').val(),
                    "item_code": item_code,
                    "item_name": item_name,
                };
            sales_data[key] = temp
        }

        sales_data[key].item_quantity = item_quantity;
        sales_data[key].item_key_value = table_index;
        var table_data = JSON.stringify(sales_data);
        $('#table_data').val(table_data);
    }

    function deleteRow(row){
        var table_index = +row.find('input[name^="item_key_value"]').val();     
         var sales_data_temp = new Array();         
        for (var i = 0; i < sales_data.length; i++){           
            if (sales_data[i] != null && sales_data[i].item_key_value == table_index){
                sales_data.splice(i, 1)               
            }
        }
        var table_data = JSON.stringify(sales_data);       
        $('#table_data').val(table_data)
    }

    $(document).ready(function (){
        $("#print").click(function (event){
            if(sales_data.length == 0 || sales_data.length === 'undefined'){
                alert_d.text ='Please Select Purchase';
                PNotify.error(alert_d);
                return false;
            }
            return true;
        });
    });
    

</script>

