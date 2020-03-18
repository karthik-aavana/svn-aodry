<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$type           = user_limit();

$scount         = supplier_count();

$limit          = $type['coun'] + $scount['scount'];

$this->load->view('layout/header');

?>

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <h5>

        <ol class="breadcrumb">

            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>

            <li><a href="<?php echo base_url('pos_billing'); ?>">POS Billing</a></li>

            <li class="active">Add</li>

        </ol>

        </h5>

    </section>

    <!-- Main content -->

    <section class="content">

        <div class="row">

            <!-- right column -->

            <div class="col-md-12">

                <div class="box">

                    <div class="box-header with-border">

                        <h3 class="box-title">Add POS Billing</h3>

                        <a class="btn btn-sm btn-default pull-right"  href="<?php echo base_url('pos_billing'); ?>">Back </a>

                    </div>

                    <!-- /.box-header -->

                    <div class="box-body">

                        <div class="row">

                            <form role="form" id="form" method="post" action="<?php echo base_url('pos_billing/add_pos_billing'); ?>">

                                <div class="col-sm-12">

                                    <div class="well">

                                        <div class="row">

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="date">Date<span class="validation-color">*</span></label>

                                                    <?php

                                                    $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));

                                                    if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1]))

                                                    {

                                                    $date = $financial_year[0] . '-04-01';

                                                    }

                                                    else

                                                    {

                                                    $date = date('Y-m-d');

                                                    }

                                                    ?>

                                                    <div class="input-group date">
                                                        <input type="text" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?php echo $date; ?>" readonly>

                                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>

                                                    </div>

                                                    <span class="validation-color" id="err_date"></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="invoice_number">Invoice Number<span class="validation-color">*</span></label>

                                                    <?php

                                                    ?>

                                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?= $invoice_number ?>" <?php

                                                    if ($access_settings[0]->invoice_readonly == 'yes')

                                                    {

                                                    echo "readonly";

                                                    }

                                                    ?>>

                                                    <span class="validation-color" id="err_invoice_number"></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="customer_name">Customer Name</label>

                                                    <?php ?>

                                                    <input type="text" class="form-control" id="customer_name" name="customer_name">

                                                    <span class="validation-color" id="err_customer_name"></span>

                                                </div>

                                                <!-- <div class="form-group">

                                                    <label for="customer">Customer</label>

                                                    <?php

                                                    if (isset($other_modules_present['customer_module_id']) && $other_modules_present['customer_module_id'] != "")

                                                    {

                                                    ?>

                                                    <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#customer_modal" class="open_customer_modal pull-right">+ Add Customer</a>

                                                    <?php }

                                                    ?>

                                                    <select class="form-control select2" autofocus="on" id="customer" name="customer" style="width: 100%;">

                                                        <option value="">Select</option>

                                                        <?php

                                                        foreach ($customer as $row)

                                                        {

                                                        echo "<option value='$row->customer_id-$row->customer_country_id-$row->customer_state_id'>$row->customer_name</option>";

                                                        }

                                                        ?>

                                                    </select>

                                                    <span class="validation-color" id="err_customer"><?php echo form_error('customer'); ?></span>

                                                </div> -->

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="mobile">Mobile No.</label>

                                                    <?php ?>

                                                    <input type="text" class="form-control" id="mobile" name="mobile">

                                                    <span class="validation-color" id="err_mobile"></span>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="email">Email Id</label>

                                                    <?php ?>

                                                    <input type="text" class="form-control" id="email" name="email">

                                                    <span class="validation-color" id="err_email"></span>

                                                </div>

                                            </div>

                                        </div>

                                        

                                        <div class="row">

                                            <div class="col-sm-12">

                                                <span class="validation-color" id="err_product"></span>

                                            </div>

                                            <?php

                                            if ($access_settings[0]->item_access == 'product')

                                            {

                                            ?>

                                            <div class="col-sm-12 search_sales_code">

                                                <?php

                                                if (isset($other_modules_present['product_module_id']) && $other_modules_present['product_module_id'] != "")

                                                {

                                                ?>

                                                <a href="" data-toggle="modal" data-target="#product_modal" class="open_product_modal pull-left">+ Add Product</a>

                                                <?php } ?>

                                                <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product Code/Name" >

                                            </div>

                                            <?php

                                            }

                                            elseif ($access_settings[0]->item_access == 'service')

                                            {

                                            ?>

                                            <div class="col-sm-12 search_sales_code">

                                                <?php

                                                if (isset($other_modules_present['service_module_id']) && $other_modules_present['service_module_id'] != "")

                                                {

                                                ?>

                                                <a href="" data-toggle="modal" data-target="#service_modal" class="open_service_modal pull-left">+ Add Service</a>

                                                <?php } ?>

                                                <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product Code/Name" >

                                            </div>

                                            <?php

                                            }

                                            else

                                            {

                                            ?>

                                            <div class="col-sm-12 search_sales_code">

                                                <?php

                                                if (isset($other_modules_present['product_module_id']) && isset($other_modules_present['service_module_id']) && $other_modules_present['product_module_id'] != "" && $other_modules_present['service_module_id'] != "")

                                                {

                                                ?>

                                                <div class="input-group">

                                                    <div class="input-group-addon">

                                                        <a href="" data-toggle="modal" data-target="#item_modal" class="pull-left">+</a>

                                                        <?php } ?>

                                                    </div>

                                                    <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product/Service Code/Name" >

                                                </div>

                                            </div>

                                            <?php

                                            }

                                            ?>

                                            <div class="col-sm-8 mt-30">

                                                <span class="validation-color" id="err_sales_code"></span>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-sm-12">

                                                <div class="form-group">

                                                    <label>Inventory Items</label>

                                                    <table class="table table-striped table-bordered table-condensed table-hover sales_table table-responsive" name="sales_data" id="sales_data">

                                                        <thead>

                                                            <tr>

                                                                <th width="2%"><img src="<?php echo base_url(); ?>assets/images/bin1.png" /></th>

                                                                <th class="span2" width="15%">Product/Service Name</th>

                                                                <!-- <th class="span2" width="17%">Product/Service Description</th> -->

                                                                <th class="span2" width="6%">Quantity</th>

                                                                <th class="span2" >Rate</th>

                                                                <th class="span2" >Discount</th>

                                                                <th class="span2" >Taxable Value</th>

                                                                <th class="span2" width="10%">GST(%)</th>

                                                                <!-- <th class="span2" >CGST(%)</th>

                                                                <th class="span2" >SGST(%)</th> -->

                                                                <th class="span2" >Total</th>

                                                            </tr>

                                                        </thead>

                                                        <tbody id="sales_table_body">

                                                        </tbody>

                                                    </table>

                                                    <!-- Hidden Field -->

                                                    <!-- <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php echo $branch[0]->branch_country_id; ?>">

                                                    <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php echo $branch[0]->branch_state_id; ?>"> -->

                                                    <input type="hidden" class="form-control" id="product_module_id" name="product_module_id" value="<?php

                                                    if (isset($other_modules_present['product_module_id']))

                                                    {

                                                    echo $other_modules_present['product_module_id'];

                                                    }

                                                    ?>" readonly>

                                                    <input type="hidden" class="form-control" id="service_module_id" name="service_module_id" value="<?php

                                                    if (isset($other_modules_present['service_module_id']))

                                                    {

                                                    echo $other_modules_present['service_module_id'];

                                                    }

                                                    ?>" readonly>

                                                    <input type="hidden" class="form-control" id="customer_module_id" name="customer_module_id" value="<?php

                                                    if (isset($other_modules_present['customer_module_id']))

                                                    {

                                                    echo $other_modules_present['customer_module_id'];

                                                    }

                                                    ?>" readonly>

                                                    <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo $date; ?>" readonly>

                                                    <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" readonly>

                                                    <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>

                                                    <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $invoice_number ?>" readonly>

                                                    <input type="hidden" class="form-control" id="section_area" name="section_area" value="add_pos_billing" readonly>

                                                    <input type="hidden" name="total_sub_total" id="total_sub_total">

                                                    <input type="hidden" name="total_taxable_amount" id="total_taxable_amount">

                                                    <input type="hidden" name="total_igst_amount" id="total_igst_amount">

                                                    <input type="hidden" name="total_cgst_amount" id="total_cgst_amount">

                                                    <input type="hidden" name="total_sgst_amount" id="total_sgst_amount">

                                                    <input type="hidden" name="total_discount_amount" id="total_discount_amount">

                                                    <input type="hidden" name="total_tax_amount" id="total_tax_amount">

                                                    <input type="hidden" name="total_grand_total" id="total_grand_total">

                                                    <input type="hidden" name="table_data" id="table_data">

                                                    <input type="hidden" name="total_other_amount" id="total_other_amount">

                                                    <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive">

                                                        <tr>

                                                            <td align="right"><?php echo 'Subtotal'; ?> (+)</td>

                                                            <td align='right'><span id="totalSubTotal">0.00</span></td>

                                                        </tr>

                                                        <tr>

                                                            <td align="right"><?php echo 'Discount'; ?> (-)</td>

                                                            <td align='right'>

                                                                <span id="totalDiscountAmount">0.00</span>

                                                            </td>

                                                        </tr>

                                                        <tr>

                                                            <td align="right"><?php echo 'Tax Amount'; ?> (+)</td>

                                                            <td align='right'>

                                                                <span id="totalTaxAmount">0.00</span>

                                                            </td>

                                                        </tr>

                                                        <tr>

                                                            <td align="right"><?php echo 'Grand Total'; ?> (=)</td>

                                                            <td align='right'><span id="totalGrandTotal">0.00</span></td>

                                                        </tr>

                                                    </table>

                                                </div>

                                            </div>

                                            <div class="col-sm-12">

                                                <div class="box-footer">

                                                    <button type="submit" id="pos_billing_submit" name="sales_submit" class="btn btn-info">Add</button>

                                                    <span class="btn btn-default" id="sale_cancel" onclick="cancel('pos_billing')">Cancel</span>

                                                </div>

                                            </div>

                                        </div>

                                        <?php

                                        $notes_sub_module = 0;

                                        foreach ($access_sub_modules as $key => $value)

                                        {

                                        if (isset($notes_sub_module_id))

                                        {

                                        if ($notes_sub_module_id == $value->sub_module_id)

                                        {

                                        $notes_sub_module = 1;

                                        }

                                        }

                                        }

                                        if ($notes_sub_module == 1)

                                        {

                                        $this->load->view('sub_modules/notes_sub_module');

                                        }

                                        ?>

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

    // $this->load->view('customer/customer_modal');

    $this->load->view('product/product_modal');

    $this->load->view('service/service_modal');

    $this->load->view('layout/item_modal');

    $this->load->view('category/category_modal');

    $this->load->view('subcategory/subcategory_modal');

    $this->load->view('tax/tax_modal');

    $this->load->view('discount/discount_modal');

    $this->load->view('service/sac_modal');

    $this->load->view('product/hsn_modal');

    $this->load->view('product/product_inventory_modal');

    ?>

    <script type="text/javascript">

    var sales_data = new Array();

    var branch_state_list = <?php echo json_encode($state); ?>;

    var item_gst = new Array();

    var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";

    var common_settings_tax_split = "<?= $access_common_settings[0]->tax_split_equaly ?>";

    </script>

    <script src="<?php echo base_url('assets/js/pos_billing/') ?>pos_billing_basic_common.js"></script>

    <script src="<?php echo base_url('assets/js/pos_billing/') ?>pos_billing_basic.js"></script>