<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');

?>

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <h5>

        <ol class="breadcrumb">

            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>

            <li><a href="<?php echo base_url('pos_billing'); ?>">POS Billing</a></li>

            <li class="active">Edit POS Billing</li>

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

                        <h3 class="box-title">Edit POS Billing</h3>

                        <a class="btn btn-sm btn-default pull-right" href="<?php echo base_url('pos_billing'); ?>">Back </a>

                    </div>

                    <!-- /.box-header -->

                    <div class="box-body">

                        <div class="row">

                            <form role="form" id="form" method="post" action="<?php echo base_url('pos_billing/edit_pos_billing'); ?>">

                                <?php foreach ($data as $row)

                                {

                                ?>

                                <div class="col-sm-12">

                                    <div class="well">

                                        <div class="row">

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="date">Date<span class="validation-color">*</span></label>

                                                    <div class="input-group date">

                                                        <input type="text" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?php echo $row->pos_billing_date; ?>" readonly>

                                                        <input type="hidden" id="date_old" name="date_old" value="<?php echo $row->pos_billing_date; ?>">

                                                        <input type="hidden" id="added_date" name="added_date" value="<?php echo $row->added_date; ?>">

                                                        <input type="hidden" id="added_user_id" name="added_user_id" value="<?php echo $row->added_user_id; ?>">

                                                        <input type="hidden" name="pos_billing_id" value="<?php echo $row->pos_billing_id; ?>">

                                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>

                                                    </div>

                                                    <span class="validation-color" id="err_date"></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="invoice_number">Invoice Number<span class="validation-color">*</span></label>

                                                    <?php ?>

                                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo $row->pos_billing_invoice_number ?>" <?php

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

                                                    <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo $row->pos_billing_party_type ?>">

                                                    <span class="validation-color" id="err_customer_name"></span>

                                                </div>

                                                <!-- <div class="form-group">

                                                    <label for="customer">Customer</label>

                                                    <select class="form-control select2" autofocus="on" id="customer" name="customer" style="width: 100%;">

                                                        <option value="">Select</option>

                                                        <?php

                                                        foreach ($customer as $key)

                                                        {

                                                        ?>

                                                        <option value='<?php echo $key->customer_id ?>'  <?php

                                                            if ($key->customer_id == $row->pos_billing_party_id)

                                                            {

                                                            echo "selected";

                                                            }

                                                        ?>><?php echo $key->customer_name ?></option>

                                                        <?php

                                                        }

                                                        ?>

                                                    </select>

                                                    <span class="validation-color" id="err_customer"><?php echo form_error('customer'); ?></span>

                                                </div> -->

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="mobile">Mobile No</label>

                                                    <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo $row->pos_billing_mobile ?>">

                                                    <span class="validation-color" id="err_mobile"></span>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="email">Email Id</label>

                                                    <input type="text" class="form-control" id="email" name="email" value="<?php echo $row->pos_billing_email ?>">

                                                    <span class="validation-color" id="err_email"></span>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                               

                                    <div class="well">

                                        <div class="row">

                                            <div class="col-sm-8">

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

                                                    </div>

                                                    <?php } ?>

                                                    <input id="input_sales_code" class="form-control" type="text" name="input_sales_code" placeholder="Enter Product/Service Code/Name" >

                                                </div>

                                            </div>

                                            <?php

                                            }

                                            ?>

                                            <div class="col-sm-8">

                                                <span class="validation-color" id="err_sales_code"></span>

                                            </div>

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

                                                    <?php

                                                    $i   = 0;

                                                    $tot = 0;

                                                    foreach ($items as $key)

                                                    {

                                                    ?>

                                                    <tr id="<?= $i ?>">

                                                        <td>

                                                            <a class='deleteRow'> <img src='<?php echo base_url(); ?>assets/images/bin_close.png' /> </a>

                                                            <input type='hidden' name='item_key_value' value="<?php echo $i ?>">

                                                            <input type='hidden' name='item_id' value="<?php echo $key->item_id ?>">

                                                            <input type='hidden' name='item_type' value="<?php echo $key->item_type ?>">

                                                            <?php if ($key->item_type == 'product' || $key->item_type == 'product_inventory')

                                                            {

                                                            ?>

                                                            <input type='hidden' name='item_code' value='<?php echo $key->product_code ?>'>

                                                            <?php

                                                            }

                                                            else

                                                            {

                                                            ?>

                                                            <input type='hidden' name='item_code' value='<?php echo $key->service_code ?>'>

                                                            <?php } ?>

                                                            <?php if ($key->item_type == 'product' || $key->item_type == 'product_inventory')

                                                            {

                                                            ?>

                                                            <td><?php echo $key->product_name; ?><br>HSN/SAC: <?php echo $key->product_hsn_sac_code; ?></td>

                                                            <?php

                                                            }

                                                            else

                                                            {

                                                            ?>

                                                            <td><?php echo $key->service_name; ?><br>HSN/SAC: <?php echo $key->service_hsn_sac_code; ?></td>

                                                            <?php } ?>

                                                            <td style="display: none;"><input type='text' name='item_description' class='form-control form-fixer' value="<?php echo $key->pos_billing_item_description ?>"></td>

                                                            <td><input type="number" step='0.01' class="form-control form-fixer text-center" value="<?php echo $key->pos_billing_item_quantity ?>" data-rule="quantity" name='item_quantity' min="1" >

                                                            </td>

                                                            <td>

                                                                <input type='number' step='0.01' class='form-control form-fixer text-right' name='item_price' value='<?php echo $key->pos_billing_item_unit_price ?>'>

                                                                <span id='item_sub_total_lbl_<?= $i ?>' class='pull-right' style='color:red;'><?php echo $key->pos_billing_item_unit_price; ?></span>

                                                                <input type='hidden' class='form-control' style='' value='<?php echo $key->pos_billing_item_sub_total ?>' name='item_sub_total' readonly>

                                                            </td>

                                                            <td align="right">

                                                                <div class="form-group">

                                                                    <select class="form-control" name="item_discount" style="width: 100%;">

                                                                        <option value="">Select</option>

                                                                        <?php

                                                                        $item_discount_value = 0;

                                                                        foreach ($discount as $dis)

                                                                        {

                                                                        if ($key->pos_billing_item_discount_id == $dis->discount_id)

                                                                        {

                                                                        $item_discount_value = $dis->discount_value;

                                                                        ?>

                                                                        <option value='<?php echo $dis->discount_id . '-' . $dis->discount_value ?>' selected="selected"><?php echo $dis->discount_value . '%'; ?></option>

                                                                        <?php

                                                                        }

                                                                        else

                                                                        {

                                                                        ?>

                                                                        <option value='<?php echo $dis->discount_id . '-' . $dis->discount_value ?>'>

                                                                        <?php echo $dis->discount_value . '%'; ?></option>

                                                                        <?php

                                                                        }

                                                                        }

                                                                        ?>

                                                                    </select>

                                                                </div>

                                                                <input type="hidden" name="item_discount_amount" value="<?php echo $key->pos_billing_item_discount_amount; ?>">

                                                                <input type="hidden" name="item_discount_value" value="<?php echo $item_discount_value; ?>">

                                                            </td>

                                                            <td align="right">

                                                                <input type='hidden' name='item_taxable_value' value="<?= $key->pos_billing_item_taxable_value ?>">

                                                                <span id="item_taxable_value_<?= $i ?>"><?= $key->pos_billing_item_taxable_value ?></span>

                                                            </td>

                                                            <td>

                                                                <?php

                                                                $item_tax_percentage                             = $key->pos_billing_item_igst_percentage + $key->pos_billing_item_cgst_percentage + $key->pos_billing_item_sgst_percentage;

                                                                $item_tax_amount                                 = $key->pos_billing_item_igst_amount + $key->pos_billing_item_cgst_amount + $key->pos_billing_item_sgst_amount;

                                                                ?>

                                                                <input type='hidden' name='item_tax_percentage' value="<?= $item_tax_percentage ?>">

                                                                <input type='hidden' name='item_tax_amount' value="<?= $item_tax_amount ?>">

                                                                <input type="hidden" step="0.01" name="item_igst" class="form-control form-fixer text-center" max="100" min="0" value="<?= $key->pos_billing_item_igst_percentage ?>" readonly>

                                                                <input type="text" name="item_igst" class="form-control form-fixer text-center" value="<?php echo ($key->pos_billing_item_cgst_percentage + $key->pos_billing_item_sgst_percentage) ?>" readonly>

                                                                <input type="hidden" name="item_igst_amount" class="form-control form-fixer" value="<?php echo $key->pos_billing_item_igst_amount; ?>" >

                                                                <span style="display: none;" id="item_igst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red;"><?php echo $key->pos_billing_item_igst_amount; ?></span>

                                                            </td>

                                                            <td style="display: none;">

                                                                <input type="number" step="0.01" name="item_cgst" class="form-control form-fixer text-center" max="100" min="0" value="<?= $key->pos_billing_item_cgst_percentage ?>" <?php if ($igst_tax != 0) echo "readonly"; ?>>

                                                                <input type="hidden" name="item_cgst_amount" class="form-control form-fixer" value="<?php echo $key->pos_billing_item_cgst_amount; ?>">

                                                                <span id="item_cgst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red;">

                                                                <?php echo $key->pos_billing_item_cgst_amount; ?></span>

                                                            </td>

                                                            <td style="display: none;">

                                                                <input type="number" step="0.01" name="item_sgst" class="form-control form-fixer text-center" max="100" min="0" value="<?= $key->pos_billing_item_sgst_percentage ?>" <?php if ($igst_tax != 0) echo "readonly"; ?>>

                                                                <input type="hidden" name="item_sgst_amount" class="form-control form-fixer text-center" value="<?php echo $key->pos_billing_item_sgst_amount; ?>">

                                                                <span id="item_sgst_amount_lbl_<?= $i ?>" class="pull-right" style="color:red;"><?php echo $key->pos_billing_item_sgst_amount; ?></span>

                                                            </td>

                                                            <td align="right">

                                                                <input type="text" class="form-control form-fixer text-right" name="item_grand_total" value=" <?php echo $key->pos_billing_item_grand_total ?>">

                                                            </td>

                                                            <?php

                                                            $temp                                            = array(

                                                            "item_id"   => $key->item_id,

                                                            "item_type" => $key->item_type,

                                                            "item_igst" => $key->pos_billing_item_igst_percentage,

                                                            "item_cgst" => $key->pos_billing_item_cgst_percentage,

                                                            "item_sgst" => $key->pos_billing_item_sgst_percentage

                                                            );

                                                            $item_gst[$key->item_id . '-' . $key->item_type] = $temp;

                                                            ?>

                                                        </tr>

                                                        <?php

                                                        $sales_data[$i]['item_id']                       = $key->item_id;

                                                        $sales_data[$i]['item_type']                     = $key->item_type;

                                                        if ($key->item_type == 'product' || $key->item_type == 'product_inventory')

                                                        {

                                                        $sales_data[$i]['item_code'] = $key->product_code;

                                                        }

                                                        else

                                                        {

                                                        $sales_data[$i]['item_code'] = $key->service_code;

                                                        }

                                                        $sales_data[$i]['item_price']       = $key->pos_billing_item_unit_price;

                                                        $sales_data[$i]['item_key_value']   = $i;

                                                        $sales_data[$i]['item_sub_total']   = $key->pos_billing_item_sub_total;

                                                        $sales_data[$i]['item_description'] = $key->pos_billing_item_description;

                                                        $sales_data[$i]['item_quantity']    = $key->pos_billing_item_quantity;

                                                        $sales_data[$i]['item_discount_amount'] = $key->pos_billing_item_discount_amount;

                                                        $sales_data[$i]['item_discount_value']  = $item_discount_value;

                                                        $sales_data[$i]['item_discount']        = $key->pos_billing_item_discount_id;

                                                        $sales_data[$i]['item_igst']            = $key->pos_billing_item_igst_percentage;

                                                        $sales_data[$i]['item_igst_amount']     = $key->pos_billing_item_igst_amount;

                                                        $sales_data[$i]['item_cgst']            = $key->pos_billing_item_cgst_percentage;

                                                        $sales_data[$i]['item_cgst_amount']     = $key->pos_billing_item_cgst_amount;

                                                        $sales_data[$i]['item_sgst']            = $key->pos_billing_item_sgst_percentage;

                                                        $sales_data[$i]['item_sgst_amount']     = $key->pos_billing_item_sgst_amount;

                                                        $sales_data[$i]['item_tax_percentage']  = $item_tax_percentage;

                                                        $sales_data[$i]['item_tax_amount']      = $item_tax_amount;

                                                        $sales_data[$i]['item_taxable_value']   = $key->pos_billing_item_taxable_value;

                                                        $sales_data[$i]['item_grand_total']     = $key->pos_billing_item_grand_total;

                                                        //array_push($product_data,$product);

                                                        $i++;

                                                        }

                                                        $sales = htmlspecialchars(json_encode($sales_data));

                                                        ?>

                                                    </tbody>

                                                </table>

                                                <!-- Hidden Field -->

                                                <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php echo $branch[0]->branch_country_id; ?>">

                                                <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php echo $branch[0]->branch_state_id; ?>">

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

                                                <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo date("Y-m-d"); ?>" readonly>

                                                <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" readonly>

                                                <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>

                                                <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $row->pos_billing_invoice_number ?>" readonly>

                                                <input type="hidden" class="form-control" id="section_area" name="section_area" value="edit_pos_billing" readonly>

                                                <input type="hidden" name="total_sub_total" id="total_sub_total" value="<?= $row->pos_billing_sub_total; ?>">

                                                <input type="hidden" name="total_taxable_amount" id="total_taxable_amount" value="<?= $row->pos_billing_taxable_value; ?>">

                                                <input type="hidden" name="total_discount_amount" id="total_discount_amount" value="<?= $row->pos_billing_discount_value; ?>">

                                                <input type="hidden" name="total_tax_amount" id="total_tax_amount" value="<?= $row->pos_billing_tax_amount; ?>">

                                                <input type="hidden" name="total_igst_amount" id="total_igst_amount" value="<?= $row->pos_billing_igst_amount; ?>">

                                                <input type="hidden" name="total_cgst_amount" id="total_cgst_amount" value="<?= $row->pos_billing_cgst_amount; ?>">

                                                <input type="hidden" name="total_sgst_amount" id="total_sgst_amount" value="<?= $row->pos_billing_sgst_amount; ?>">

                                                <input type="hidden" name="total_grand_total" id="total_grand_total" value="<?= $row->pos_billing_grand_total; ?>">

                                                <input type="hidden" name="table_data" id="table_data" value="<?php echo $sales; ?>">

                                                <input type="hidden" name="total_other_amount" id="total_other_amount" value="0">

                                                <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive">

                                                    <tr>

                                                        <td align="right">Total Value (+)</td>

                                                        <td align='right'><span id="totalSubTotal"><?php echo $row->pos_billing_sub_total; ?></span></td>

                                                    </tr>

                                                    <tr>

                                                        <td align="right">Total Discount (-)</td>

                                                        <td align='right'><span id="totalDiscountAmount"><?php echo $row->pos_billing_discount_value; ?></span>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td align="right">Total Tax (+)</td>

                                                    <td align='right'><span id="totalTaxAmount"><?php echo $row->pos_billing_tax_amount; ?></span>

                                                </td>

                                            </tr>

                                            <tr>

                                                <td align="right">Grand Total (=)</td>

                                                <td align='right'><span id="totalGrandTotal"><?php echo $row->pos_billing_grand_total; ?></span></td>

                                            </tr>

                                        </table>

                                    </div>

                                </div>

                                <div class="col-sm-12">

                                    <div class="box-footer">

                                        <button type="submit" id="sales_submit" name="sales_submit" class="btn btn-info">Update</button>

                                        <span class="btn btn-default" id="sale_cancel" onclick="cancel('pos_billing')">Cancel</span>

                                    </div>

                                </div>

                            </div>

                                <?php } ?>

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

?>

<script type="text/javascript">

var sales_data = new Array();

var sales_data = <?php echo json_encode($sales_data); ?>;

var branch_state_list = <?php echo json_encode($state); ?>;

var item_gst = <?php echo json_encode($item_gst); ?>;

var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";

var common_settings_tax_split = "<?= $access_common_settings[0]->tax_split_equaly ?>";

</script>

<script src="<?php echo base_url('assets/js/pos_billing/') ?>pos_billing_basic_common.js"></script>

<script src="<?php echo base_url('assets/js/pos_billing/') ?>pos_billing_basic.js"></script>