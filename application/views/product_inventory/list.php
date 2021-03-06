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

            <li class="active">Product</li>

        </ol>

    </div>

    <!-- Main content -->

    <section class="content mt-50">

        <div class="row">

            <!-- right column -->

            <div class="col-md-12">

                <div class="box">

                    <div class="box-header with-border">

                        <h3 class="box-title">Product</h3>

                        <?php

                        if ($access_module_privilege->add_privilege == "yes")

                        {

                        ?>

                        <a class="btn btn-sm btn-info pull-right btn-flat" href="<?php echo base_url('product/product_add'); ?>">Add Product</a>

                        <a class="btn btn-sm btn-info pull-right btn-flat" href="<?php echo base_url('barcode/get_barcode'); ?>">Print Barcode</a>

                        <a class="btn btn-sm btn-info pull-right btn-flat" href="<?php echo base_url('varients/add'); ?>">Add Varients</a>

                        <?php } ?>

                    </div>

                    <!-- /.box-header -->

                    <div class="box-body">

                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >

                            <thead>

                                <tr>

                                    <!-- <th>Date</th> -->

                                    <th>Product Code</th>

                                    <th>HSN Code</th>

                                    <th>Product Name</th>

                                    <th> Tax Rate</th>

                                    <!-- <th style="min-width: 100px;text-align: center;">Category</th> -->

                                    <th>Unit Price</th>

                                    <!-- <th style="max-width: 50px;text-align: center;">Existing<br/>Stock</th> -->

                                    <!-- <th style="max-width: 50px;text-align: center;">Damaged<br/>Stock</th>  -->

                                    <th>Added date</th>

                                    <th>User</th>

                                    <th>Action <?php echo ''; ?></th>

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

<div id="editProduct" class="modal fade" role="dialog">

<div class="modal-dialog">

    <!-- Modal content-->

    <div class="modal-content">

        <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal">&times;</button>

            <h4 class="modal-title">Edit Product</h4>

        </div>

        <div class="modal-body" id="productForm">

            <form role="form" method="post">

                <div class="row">

                    <input type="hidden" name="p_id" id="p_id">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label for="product_code">Product Code <span class="validation-color">*</span></label>

                            <input type="text" class="form-control" tabindex="1" id="product_code" name="ajax_product_code" readonly="" value="">

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">

                            <label for="product_code">Product Name <span class="validation-color">*</span></label>

                            <input type="text" class="form-control" tabindex="1" id="product_name" name="ajax_product_code" value="">

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label for="product_code">Product Price <span class="validation-color">*</span></label>

                            <input type="text" class="form-control" tabindex="1" id="product_price" name="ajax_product_code" value="">

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">

                            <label for="product_hsn_sac_code">Product HSN Code <span class="validation-color">*</span></label>

                            <div class="input-group">

                                <div class="input-group-addon">

                                    <a href="" data-toggle="modal" data-target="#hsn_modal" class="pull-right"><span class="fa fa-eye"></span></a>

                                </div>

                                <input type="text" class="form-control" id="product_hsn_sac_code" name="product_hsn_sac_code" value="<?php echo set_value('product_hsn_sac_code'); ?>" tabindex="11">

                            </div>

                            <span class="validation-color" id="err_product_hsn_sac_code"><?php echo form_error('product_hsn_sac_code'); ?></span>

                        </div>

                    </div>

                </div>

                

                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label for="product_tax">Select Product Tax <span class="validation-color">*</span></label>

                            <div class="input-group">

                                <div class="input-group-addon">

                                    <a href="" data-toggle="modal" data-target="#tax_modal" data-name="product" class="pull-right new_tax">+</a></div>

                                    <select class="form-control select2" id="product_tax" name="product_tax" style="width: 100%;" tabindex="10">

                                        <option value="0">Select</option>

                                        <?php

                                        foreach ($tax as $row)

                                        {

                                        echo "<option value='$row->tax_id-$row->tax_value'" . set_select('tax', $row->tax_id) . ">$row->tax_name</option>";

                                        }

                                        ?>

                                    </select>

                                </div>

                                <span class="validation-color" id="err_product_tax"><?php echo form_error('product_tax'); ?></span>

                                <div id="p-tax">

                                    <input type="hidden" name="product_igst" id="product_igst" value="0.00" />

                                    <input type="hidden" name="product_cgst" id="product_cgst" value="0.00" />

                                    <input type="hidden" name="product_sgst" id="product_sgst" value="0.00" />

                                    <br>IGST : <span id="p_igst">0.00</span>

                                    <br>CGST : <span id="p_cgst">0.00</span>

                                    <br>SGST : <span id="p_sgst">0.00</span>

                                </div>

                            </div> 

                             

                        </div>

                    </div>

                </div>

            </form>

            <div class="modal-footer">

                <button type="submit" class="close" data-dismiss="modal" id='post_products' href="#">Edit Products</button>

                <button type="button" id='close_model' class="btn btn-info" data-dismiss="modal">Close</button>

            </div>

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
                "url": base_url + "product_inventory",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
                },
            "columns": [
                // { "data": "added_date" },
                {"data": "product_code"},
                {"data": "product_hsn_sac_code"},
                {"data": "product_name"},
                // { "data": "product_model_no" },
                // { "data": "product_color" },
                {"data": "product_igst"},
                // { "data": "category_name" },
                {"data": "product_price"},
                // { "data": "product_quantity" },
                // { "data": "product_damaged_quantity" },
                {"data": "added_date"},
                {"data": "added_user"},
                {"data": "action"}
            ],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
            });
function get_product(id) {

var productId = id;

$.ajax({

type: "GET",

url: base_url + "product_inventory/get_product_id/" + productId,

success: function (data) {

var products = JSON.parse(data);

$("#p_id").val(products.id);

$("#product_code").val(products.product_code);

$("#product_name").val(products.product_name);

$("#product_price").val(products.price);

// $("#product_category").val(products.);

$("#product_hsn_sac_code").val(products.hsn);

$("#product_tax").val(products.tax);

$("#product_igst").val(products.igst);

$("#product_cgst").val(products.cgst);

$("#product_sgst").val(products.sgst);

$("#p_igst").text(products.igst)

$("#p_cgst").text(products.cgst)

$("#p_sgst").text(products.sgst)

$("#select2-product_tax-container").text(products.tax_value)

$("#product_tax option").each(function () {

if ($(this).val() == products.tax) {

$(this).attr("selected", "selected");

}

});

$("#varient_unit").append('<option selected value="' + products.tax + '">' + obj.varient_unit + '</option>');

}

})

}

$("#post_products").click(function () {

// alert()

var id = $("#p_id").val();

var product_code = $("#product_code").val();

var product_name = $("#product_name").val();

var product_hsn_sac_code = $("#product_hsn_sac_code").val();

var product_price = $("#product_price").val();

var product_tax_id = $("#product_tax").val();

var product_igst = $("#product_igst").val();

var product_cgst = $("#product_cgst").val();

var product_sgst = $("#product_sgst").val();

$.ajax({

type: 'POST',

url: base_url + "product_inventory/ajax_edit_product/",

data: {

'id': id,

'product_code': product_code,

'product_name': product_name,

'product_hsn_sac_code': product_hsn_sac_code,

'product_price': product_price,

'product_tax': product_tax_id,

'product_igst': product_igst,

'product_cgst': product_cgst,

'product_sgst': product_sgst,

},

success: function (data) {

// $("#close_model").trigger('click');

if (data == 'sucess') 

{

   // $("#editProduct").modal('hide');

    setTimeout(function () {

                                location.reload();

                            });

}

}

})

})

</script>

<script src="<?php echo base_url('assets/js/product/') ?>product.js"></script>