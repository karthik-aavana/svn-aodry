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

                        <h3 class="box-title">Raw Material</h3>

                        <?php

                        if (in_array($product_module_id, $active_add)) {

                            ?>

                            <a class="btn btn-sm btn-info pull-right btn-flat" href="<?php echo base_url('product/add'); ?>">Add Product</a>

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

                                    <th style="min-width: 100px;text-align: center;">Raw Material Code</th>

                                    <th style="min-width: 80px;text-align: center;">HSN Code</th>

                                    <th style="min-width: 200px;text-align: center;">Raw MaterialName</th>

                                    <th style="max-width: 50px;text-align: center;"> Tax Rate</th>

                                    <th style="min-width: 150px;text-align: center;">Unit Price</th>

                                    <th style="min-width: 100px;text-align: center;">Added date</th>

                                    <th style="min-width: 100px;text-align: center;">User</th>

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

<div id="edit_raw_material" class="modal fade" role="dialog">

    <div class="modal-dialog">

        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Edit Raw Material</h4>

            </div>

            <div class="modal-body" id="productForm">

                <form role="form" method="post">

                    <div class="row">

                        <input type="hidden" name="p_id" id="p_id">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label for="product_code">Raw Material Code <span class="validation-color">*</span></label>

                                <input type="text" class="form-control" tabindex="1" id="product_code" name="ajax_product_code" readonly="" value="">

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label for="product_code">Raw Material Name <span class="validation-color">*</span></label>

                                <input type="text" class="form-control" tabindex="1" id="product_name" name="ajax_product_code" value="">

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label for="product_code">Raw Material Price <span class="validation-color">*</span></label>

                                <input type="text" class="form-control" tabindex="1" id="product_price" name="ajax_product_code" value="">

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label for="product_hsn_sac_code">Raw Material HSN Code <span class="validation-color">*</span></label> <a href="" data-toggle="modal" data-target="#hsn_modal" class="pull-right"><span>Raw Material HSN Lookup</span></a>

                                <input type="text" class="form-control" id="product_hsn_sac_code" name="product_hsn_sac_code" value="<?php echo set_value('product_hsn_sac_code'); ?>" tabindex="11">

                                <span class="validation-color" id="err_product_hsn_sac_code"><?php echo form_error('product_hsn_sac_code'); ?></span>

                            </div>



                        </div>



                    </div>

            </div>

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">

                        <label for="product_tax">Select Raw Material Tax <span class="validation-color">*</span></label><a href="" data-toggle="modal" data-target="#tax_modal" data-name="product" class="pull-right new_tax">+ Add Tax</a>

                        <select class="form-control select2" id="product_tax" name="product_tax" style="width: 100%;" tabindex="10">

                            <option value="0">Select</option>

                            <?php

                            foreach ($tax as $row)

                            {

                                echo "<option value='$row->tax_id-$row->tax_value'" . set_select('tax', $row->tax_id) . ">$row->tax_name</option>";

                            }

                            ?>

                        </select>

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

                <div class="col-md-6">



                </div>



            </div>

            <div class="row">

                <div class="col-md-5">

                </div>

                <a class="btn btn-sm btn-info   btn-flat" data-dismiss="modal"  id='post_products' href="#">Edit Products</a>

            </div>



            </form>

            <div class="modal-footer">

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

                "url": base_url + "/Manufacturing",

                "dataType": "json",

                "type": "POST",

                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}

            },

            "columns": [

                {"data": "raw_material_code"},

                {"data": "raw_material_hsn_sac_code"},

                {"data": "raw_material_name"},

                {"data": "raw_material_igst"},

                {"data": "raw_material_price"},

                {"data": "added_date"},

                {"data": "raw_material_user"},



                {"data": "action"}

            ]

        });

    });

    function get_product(id) {

        var productId = id;

        $.ajax({

            type: "GET",

            url: base_url + "manufacturing/get_product_id/" + productId,

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

            url: base_url + "product/ajax_edit_product/",

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

                if (data == 'sucess') {

                    $("#editProduct").modal('hide');

                }

            }

        })

    })

</script>

<script src="<?php echo base_url('assets/js/product/') ?>product.js"></script>

