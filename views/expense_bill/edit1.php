<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
                <li><a href="<?php echo base_url('expense_bill'); ?>">Expense Bill</a></li>
                <li class="active">Edit Expense Bill</li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Expense Bill</h3>
                        <span class="btn btn-sm btn-default pull-right" id="cancel" onclick="cancel('expense_bill')">Back</span>
                    </div>

                    <form role="form" id="form" method="post" action="<?php echo base_url('expense_bill/edit_expense_bill'); ?>">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="well">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="date">Date<span class="validation-color">*</span></label>
                                                    <!-- <div class="input-group"> -->
                                                    <input type="text" style="background: #fff;" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?php echo $data[0]->expense_bill_date; ?>" readonly>

    <!-- <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
</div> -->
                                                    <span class="validation-color" id="err_invoice_date"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Invoice Number<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?= $data[0]->expense_bill_invoice_number ?>" <?php
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
                                                    <label for="customer">Supplier<span class="validation-color">*</span></label>
                                                    <!--   <a href="" data-toggle="modal" data-target="#supplier_modal" class="pull-right">+ Add Supplier</a> -->
                                                    <select class="form-control select2" autofocus="on" id="supplier" name="supplier" style="width: 100%;">
                                                        <option selected value="<?= $data[0]->expense_bill_payee_id ?>"><?= $data[0]->supplier_name ?></option>
                                                    </select>
                                                    <span class="validation-color" id="err_supplier"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="expense_type">Expense<span class="validation-color">*</span></label>
                                                    <!--  <a href="" data-toggle="modal" data-target="#group_model" class="pull-right">+ Add Expense</a> -->
                                                    <select style="color: black" class="form-control select2" id="expense_type" name="expense_type">
                                                        <option selected value="<?= $data[0]->expense_id ?>"><?= $data[0]->expense_title ?></option>

                                                    </select>
                                                    <span class="validation-color" id="err_expense_type"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="invoice">Reference Number</label>
                                                    <input type="text" class="form-control" id="reference_number" name="reference_number" value="<?= $data[0]->expense_bill_reference_number ?>">

                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="currency_id">Billing Currency <span class="validation-color">*</span></label>
                                                    <select class="form-control select2" id="currency_id" name="currency_id">
                                                        <?php
                                                        foreach ($currency as $key => $value)
                                                        {
                                                            if ($value->currency_id == $data[0]->currency_id)
                                                            {
                                                                echo "<option value='" . $value->currency_id . "' selected>" . $value->currency_name . "</option>";
                                                            }
                                                            else
                                                            {
                                                                echo "<option value='" . $value->currency_id . "'>" . $value->currency_name . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <span class="validation-color" id="err_currency_id"></span>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="paying_by">Description</label>
                                                    <textarea type="text" class="form-control" id="description" name="description" value="<?= $data[0]->expense_bill_description ?>"></textarea>
                                                </div>
                                            </div>
                                        </div> -->

                                    </div>

                                </div>

                                <!-- hidden files -->
                                <input type="hidden" class="form-control" id="section_area" name="section_area" value="expense_bill_edit" readonly>
                                <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo $data[0]->expense_bill_date ?>" readonly>
                                <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" readonly>
                                <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>
                                <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $data[0]->expense_bill_invoice_number ?>" readonly>

                                <input type="hidden" name="expense_bill_id" id="expense_bill_id" value="<?= $data[0]->expense_bill_id ?>">

                                <!-- hidden files -->

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <table class="table items table-striped table-bordered table-condensed table-hover expense_table" name="expense_data" id="expense_data">
                                            <thead>
                                                <tr>
                                                    <th class="span2" width="20%">Item Description</th>
                                                    <th class="span2" width="12%">Amount</th>
                                                    <th class="span2" width="10%">TDS/TCS(%)</th>
                                                    <th class="span2" width="12%">Net Amount</th>
                                                    <th class="span2" width="10%">IGST(%)</th>
                                                    <th class="span2" width="10%">CGST(%)</th>
                                                    <th class="span2" width="10%">SGST(%)</th>
                                                    <th class="span2" width="16%">Grand Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="expense_table_body">
                                            <td><input class='form-control form-fixer' type="text" value="<?= $data[0]->expense_bill_description ?>" name="description" id="description"></td>
                                            <td><input class='form-control form-fixer float_number' value="<?= $data[0]->expense_bill_sub_total ?>" type=" float_number" name="amount"   id="amount"></td>
                                            <td><input class='form-control form-fixer float_number' type="text" name="tds" value="<?= $data[0]->expense_bill_tds_percentage ?>"  id="tds">

                                                <span id="tds_lbl" class="pull-right" style="color:red;"></span>
                                            </td>
                                            <td><input class='form-control form-fixer float_number' value="<?= $data[0]->expense_bill_net_amount ?>" type=" float_number" name="net_amount"   id="net_amount"></td>
                                            <td>
                                                <!-- <input class='form-control form-fixer float_number' value="<?= $data[0]->expense_bill_igst_percentage ?>" type="text" name="igst" id="igst" <?php
                                                if ($data[0]->expense_bill_igst_percentage < 1 && $data[0]->expense_bill_cgst_percentage > 0)
                                                {
                                                    echo "readonly";
                                                }
                                                ?>> -->

                                                <input class='form-control form-fixer float_number' value="<?php
                                                       if ($data[0]->expense_bill_igst_percentage > 0)
                                                       {
                                                           echo $data[0]->expense_bill_igst_percentage;
                                                       }
                                                       ?>" type="text" name="igst" id="igst" <?php
                                                       if ($data[0]->expense_bill_cgst_percentage > 0.00 || $data[0]->expense_bill_sgst_percentage > 0.00)
                                                       {
                                                           echo "readonly";
                                                       }
                                                       ?>>

                                                <span id="igst_tax_lbl" class="pull-right" style="color:red;"></span>
                                            </td>

                                            <td>
                                                <!-- <input class='form-control form-fixer float_number' value="<?= $data[0]->expense_bill_cgst_percentage ?>" type="text" name="cgst" id="cgst" <?php
                                                       if ($data[0]->expense_bill_igst_percentage > 0 && $data[0]->expense_bill_cgst_percentage < 1)
                                                       {
                                                           echo "readonly";
                                                       }
                                                       ?>> -->

                                                <input class='form-control form-fixer float_number' value="<?php
                                                       if ($data[0]->expense_bill_cgst_percentage > 0)
                                                       {
                                                           echo $data[0]->expense_bill_cgst_percentage;
                                                       }
                                                       ?>" type="text" name="cgst" id="cgst" <?php
                                                if ($data[0]->expense_bill_igst_percentage > 0.00)
                                                {
                                                    echo "readonly";
                                                }
                                                       ?>>

                                                <span id="cgst_tax_lbl" class="pull-right" style="color:red;"></span>
                                            </td>
                                            <td>
                                                <!-- <input class='form-control form-fixer float_number' value="<?= $data[0]->expense_bill_sgst_percentage ?>" type="text" name="sgst" id="sgst" <?php
                                                       if ($data[0]->expense_bill_igst_percentage > 0 && $data[0]->expense_bill_sgst_percentage < 1)
                                                       {
                                                           echo "readonly";
                                                       }
                                                       ?>> -->

                                                <input class='form-control form-fixer float_number' value="<?php
                                                       if ($data[0]->expense_bill_sgst_percentage > 0)
                                                       {
                                                           echo $data[0]->expense_bill_sgst_percentage;
                                                       }
                                                       ?>" type="text" name="sgst" id="sgst" <?php
                                                       if ($data[0]->expense_bill_igst_percentage > 0.00)
                                                       {
                                                           echo "readonly";
                                                       }
                                                       ?>>


                                                <span id="sgst_tax_lbl" class="pull-right" style="color:red;"></span>
                                            </td>
                                            <td><input class='form-control form-fixer float_number' value="<?= $data[0]->expense_bill_grand_total ?>" type="text" name="grand_total" id="grand_total">
                                            </td>

                                            <input class='form-control form-fixer float_number' value="<?= $data[0]->expense_bill_tds_amount ?>" type="hidden" name="tds_amount" id="tds_amount">
                                            <input class='form-control form-fixer float_number' value="<?= $data[0]->expense_bill_igst_amount ?>"  type="hidden" name="igst_amount" id="igst_amount">
                                            <input class='form-control form-fixer float_number' value="<?= $data[0]->expense_bill_cgst_amount ?>"  type="hidden" name="cgst_amount" id="cgst_amount">
                                            <input class='form-control form-fixer float_number' type="hidden" name="sgst_amount" value="<?= $data[0]->expense_bill_sgst_amount ?>" id="sgst_amount">
                                            <input class='form-control form-fixer' type="hidden" name="total_tax_percentage" id="total_tax_percentage" value="<?= $data[0]->expense_bill_tax_percentage ?>">
                                            <input class='form-control form-fixer float_number' value="<?= $data[0]->expense_bill_tax_amount ?>" type="hidden" name="total_tax_amount" id="total_tax_amount">

                                            </tbody>
                                        </table>
                                        <table id="table-total" class="table table-striped table-bordered table-condensed table-hover">
                                            <tr>
                                                <td align="right">Total Value</td>
                                                <td align='right'><span id="totalValue"><?= $data[0]->expense_bill_sub_total ?></span></td>
                                            </tr>

                                            <tr>
                                                <td align="right">Total Tax</td>
                                                <td align='right'>
                                                    <span id="totalTax"><?= $data[0]->expense_bill_tax_amount ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Total TDS</td>
                                                <td align='right'>
                                                    <span id="totalTds"><?= $data[0]->expense_bill_tds_amount ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Total </td>
                                                <td align='right'><span id="grandTotal"><?= $data[0]->expense_bill_grand_total ?></span></td>
                                            </tr>
                                        </table>
                                        <span id="err_grand_total" class="validation-color pull-right" style="padding-left: 30px;"></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="box-footer">
                                        <button type="submit" id="expense_bill_submit" class="btn btn-info">Update</button>
                                        <span class="btn btn-default" id="expense_bill_cancel" onclick="cancel('expense_bill')">Cancel</span>
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

                        </div>
                    </form>
                </div>
                <!-- /.box-body -->
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
$this->load->view('expense_bill/expense_modal');
$this->load->view('expense_bill/payee_modal');
?>
<script src="<?php echo base_url('assets/js/expense/') ?>expense_bill.js"></script>


<script type="text/javascript">
                                            var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
                                            var common_settings_tax_split = "<?= $access_common_settings[0]->tax_split_equaly ?>";
</script>
