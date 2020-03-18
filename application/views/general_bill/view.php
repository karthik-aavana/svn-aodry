<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');

?>

<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">



    </section>

    <div class="fixed-breadcrumb">

        <ol class="breadcrumb abs-ol">

            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>

            <li><a href="<?php echo base_url('general_bill'); ?>">General Bill</a></li>

            <li class="active">View General Bill</li>

        </ol>

    </div>

    <!-- Main content -->

    <section class="content mt-50">

        <div class="row">

            <!-- right column -->

            <div class="col-md-12">

                <div class="box">

                    <div class="box-header with-border">

                        <h3 class="box-title">General Bill</h3>

                        <a class="btn btn-default pull-right" id="sale_cancel" onclick="cancel('general_bill')">Back</a>

                    </div>

                    <!-- /.box-header -->

                    <div class="box-body">

                        <div class="row">

                            <div class="col-md-12">

                                <div class="well">

                                    <div class="row">

                                        <div class="col-sm-3">

                                            <div class="form-group">

                                                <label for="date"> Date<span class="validation-color">*</span></label>

                                                <input type="text" class="form-control" id="invoice_date" name="invoice_date" value="<?= $general_bill_data[0]->general_bill_date ?>" readonly>

                                                <span class="validation-color" id="err_invoice_date"></span>

                                            </div>

                                        </div>

                                        <div class="col-sm-3">

                                            <div class="form-group">

                                                <label for="reference_no">Invoice Number <span class="validation-color">*</span></label>

                                                <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?= $general_bill_data[0]->general_bill_invoice_number ?>" readonly>

                                                <span class="validation-color" id="err_invoice_number"></span>

                                            </div>

                                        </div>

                                        <div class="col-sm-3">

                                            <div class="form-group">

                                                <label for="">Purpose of Transaction <span class="validation-color">*</span></label>

                                                <input type="text" name="purpose_of_transaction"  class="form-control" value="<?= $general_bill_data[0]->purpose_of_transaction ?>" readonly>

                                                <span class="validation-color" id="err_purpose_of_transaction"></span>

                                            </div>

                                        </div>

                                        <?php

                                        if (isset($general_bill_data[0]->type_of_transaction) && $general_bill_data[0]->type_of_transaction != "")

                                        {

                                            ?>

                                            <div class="col-sm-3" id="div_type_of_transaction">

                                                <div class="form-group">

                                                    <label for="">Type of Transaction <span class="validation-color">*</span></label>

                                                    <input type="text" name="type_of_transaction"  class="form-control" value="<?= $general_bill_data[0]->type_of_transaction ?>" readonly>

                                                    <span class="validation-color" id="err_type_of_transaction"></span>

                                                </div>

                                            </div>

                                        <?php } ?>

                                    </div>

                                    <div class="row">

                                        <div class="col-sm-3">

                                            <div class="form-group">

                                                <label for="currency_id">Billing Currency <span class="validation-color">*</span></label>

                                                <select class="form-control select2" id="currency_id" name="currency_id" disabled>

                                                    <?php

                                                    foreach ($currency as $key => $value)

                                                    {

                                                        if ($value->currency_id == $general_bill_data[0]->currency_id)

                                                        {

                                                            echo "<option value='" . $value->currency_id . "' selected>" . $value->currency_name . "</option>";

                                                        }

                                                    }

                                                    ?>

                                                </select>

                                                <span class="validation-color" id="err_currency_id"></span>

                                            </div>

                                        </div>

                                        <!--  <div class="col-sm-3">

                                          <div class="form-group">

                                            <label ></label>Amount<span class="validation-color">*</span>

                                            <input type="text" id="amount" name="amount" class="form-control" value="<?= $general_bill_data[0]->general_bill_grand_total ?>" readonly>

                                            <span class="validation-color" id="err_amount"></span>

                                          </div>

                                        </div>  -->

                                        <div class="col-sm-3" id="payment_mode">

                                            <div class="form-group">

                                                <label id="label_mode_payment"><?= $payment_mode ?></label><span class="validation-color">*</span>

                                                <input type="text" id="mode_of_payment" name="mode_of_payment" class="form-control" value="<?= $mode_of_payment[0]->ledger_title ?>" readonly>

                                                <span class="validation-color" id="err_mode_of_payment"></span>

                                            </div>

                                        </div>

                                        <div class="col-sm-3" id="div_hide">

                                            <div class="form-group">

                                                <label id="label_change"><?= $to_from_title ?></label><span class="validation-color">*</span>

                                                <?php $array_from_to = htmlspecialchars(json_encode($from_to)); ?>

                                                <input type="hidden" name="txt" id="txt" value="<?php echo $array_from_to ?>">

                                                <textarea class="form-control" id="from_to" name="from_to" rows="5" readonly></textarea>

                                                <span class="validation-color" id="err_from_to"></span>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="row" id="bank_details">

                                        <?php if (isset($general_bill_data[0]->bank_name) && $general_bill_data[0]->bank_name != null)

                                        {

                                            ?>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="bank_name">Bank Name</label>

                                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?= $general_bill_data[0]->bank_name ?>" readonly>

                                                    <span class="validation-color" id="err_bank_name"></span>

                                                </div>

                                            </div>

                                        <?php

                                        }

                                        if (isset($general_bill_data[0]->cheque_number) && $general_bill_data[0]->cheque_number != null)

                                        {

                                            ?>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="cheque_no">Reference Number</label>

                                                    <input type="text" class="form-control" id="cheque_number" name="cheque_number" value="<?= $general_bill_data[0]->cheque_number ?>" readonly>

                                                    <span class="validation-color" id="err_cheque_number"></span>

                                                </div>

                                            </div>

                                        <?php

                                        }

                                        if (isset($general_bill_data[0]->cheque_date) && $general_bill_data[0]->cheque_date != '0000-00-00')

                                        {

                                            ?>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="cheque_date">Cheque Date (If Applicable)</label>

                                                    <input type="text" class="form-control datepicker" id="cheque_date" name="cheque_date" value="<?= $general_bill_data[0]->cheque_date ?>" readonly>

                                                    <span class="validation-color" id="err_cheque_date"></span>

                                                </div>

                                            </div>

<?php } ?>

                                    </div>

                                </div>

                                <div class="well" id="purchase_sale">

                                    <div class="row">

<?php if (isset($general_bill_data[0]->party_id) && $general_bill_data[0]->party_id != null)

{

    ?>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label id="sup_cust"><?= $party ?></label><span class="validation-color">*</span>



                                                    <select class="form-control select2" autofocus="on" id="party" name="party" style="width: 100%;" disabled>

                                                        <option value="">Select</option>

                                                        <?php

                                                        foreach ($ledger_data as $key)

                                                        {

                                                            ?>

                                                            <option value='<?php echo $key->country_id ?>' <?php

                                                                    if ($key->ledger_id == $general_bill_data[0]->party_id)

                                                                    {

                                                                        echo "selected";

                                                                    }

                                                                    ?>>

                                                            <?php echo $key->ledger_title; ?>

                                                            </option>

        <?php

    }

    ?>

                                                    </select>

                                                    <span class="validation-color" id="err_party"></span>

                                                </div>

                                            </div>

<?php } if (isset($general_bill_data[0]->nature_of_supply) && $general_bill_data[0]->nature_of_supply != null)

{

    ?>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="nature_of_supply">Nature of Supply <span class="validation-color">*</span></label>



                                                    <input type="text" class="form-control" id="nature_of_supply" name="nature_of_supply" value="<?= $general_bill_data[0]->nature_of_supply ?>" readonly>



                                                    <span class="validation-color" id="err_nature_supply"><?php echo form_error('nature_of_supply'); ?></span>

                                                </div>

                                            </div>

<?php } ?>

                                                    <?php if (isset($general_bill_data[0]->billing_country_id) && $general_bill_data[0]->billing_country_id != null)

                                                    {

                                                        ?>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="type_of_supply">Billing Country <span class="validation-color">*</span></label>

                                                    <select class="form-control select2" id="billing_country" name="billing_country" style="width: 100%;" disabled>

                                                        <?php

                                                        foreach ($country as $key)

                                                        {

                                                            ?>

                                                            <option value='<?php echo $key->country_id ?>' <?php

                                                                if ($key->country_id == $general_bill_data[0]->billing_country_id)

                                                                {

                                                                    echo "selected";

                                                                }

                                                                ?>>

        <?php echo $key->country_name; ?>

                                                            </option>

                                                <?php

                                            }

                                            ?>

                                                    </select>

                                                    <span class="validation-color" id="err_billing_country"><?php echo form_error('type_of_supply'); ?></span>

                                                </div>

                                            </div>

                                                    <?php } ?>

                                                    <?php if (isset($general_bill_data[0]->billing_state_id) && $general_bill_data[0]->billing_state_id != null)

                                                    {

                                                        ?>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="type_of_supply">Place of Supply <span class="validation-color">*</span></label>

                                                    <select class="form-control select2" id="billing_state" name="billing_state" style="width: 100%;" disabled>

                                                        <?php

                                                        if ($general_bill_data[0]->billing_country_id == $branch[0]->branch_country_id)

                                                        {

                                                            ?>

                                                            <?php

                                                            foreach ($state as $key)

                                                            {

                                                                ?>

                                                                <option value='<?php echo $key->state_id ?>' <?php

                                                                if ($key->state_id == $general_bill_data[0]->billing_state_id)

                                                                {

                                                                    echo "selected";

                                                                }

                                                                ?>>

                                                                <?php echo $key->state_name; ?>

                                                                </option>

                                                                <?php

                                                            }

                                                            ?>

    <?php

    }

    else

    {

        ?>

                                                            <option value="0">Out of Country</option>

                                            <?php } ?>

                                                    </select>

                                                    <span class="validation-color" id="err_billing_state"><?php echo form_error('type_of_supply'); ?></span>

                                                </div>

                                            </div>

                                        </div>

<?php } ?>

                                    <div class="row">

<?php if (isset($general_bill_data[0]->type_of_supply) && $general_bill_data[0]->type_of_supply != null)

{

    ?>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>

                                                    <select class="form-control select2" id="type_of_supply" name="type_of_supply" style="width: 100%;" disabled>

                                                        <option value="regular"><?= $general_bill_data[0]->type_of_supply ?></option>



                                                    </select>

                                                    <span class="validation-color" id="err_type_supply"><?php echo form_error('type_of_supply'); ?></span>

                                                </div>

                                            </div>

                                                        <?php } ?>

                                    </div>

                                </div>

                                <div class="well">

                                    <div class="row">

                                        <div class="col-sm-8 col-sm-offset-2">

                                            <table class="table items table-striped table-bordered table-condensed table-hover ledger_table" >

                                                <thead>

                                                    <tr>

                                                        <?php

                                                        if ($general_bill_data[0]->purpose_of_transaction == "Fixed Assets")

                                                        {

                                                            ?>

                                                            <th class="span2" width="25%" >From</th>

                                                            <th class="span2" width="25%">To</th>

                                                            <th class="span2" width="10%">Taxable</th>

                                                            <th class="span2" width="10%">IGST</th>

                                                            <th class="span2" width="10%">CGST</th>

                                                            <th class="span2" width="10%">SGST</th>

                                                    <?php

                                                    }

                                                    else

                                                    {

                                                        ?>

                                                            <th class="span2" width="40%" >From</th>

                                                            <th class="span2" width="30%">To</th>

                                                        <?php } ?>

                                                        <th class="span2" width="30%">Amount</th>



                                                    </tr>

                                                </thead>

                                                <tbody id="ledger_table_body">

<?php foreach ($general_bill_data as $key)

{

    ?>



                                                        <tr>





                                                            <?php

                                                            foreach ($ledger_data as $ledg)

                                                            {

                                                                if ($key->from_id == $ledg->ledger_id)

                                                                {

                                                                    ?>

                                                                    <td>

                                                                        <input class='form-control form-fixer' type="text" name="from_id" value="<?= $ledg->ledger_title ?>"  readonly>

                                                                    </td>

                                                                <?php }

                                                            }

                                                            ?>



    <?php

    foreach ($ledger_data as $ledg)

    {

        if ($key->to_id == $ledg->ledger_id)

        {

            ?>

                                                                    <td>

                                                                        <input class='form-control form-fixer' type="text" name="to_id" value="<?= $ledg->ledger_title ?>" readonly></td>

        <?php }

    }

    ?>

    <?php

    if ($general_bill_data[0]->purpose_of_transaction == "Fixed Assets")

    {

        ?>

                                                                <td><input class='form-control form-fixer float_number' type="text" name="amount" value="<?= $key->item_taxable_value ?>" readonly>  </td>

                                                                <td><input class='form-control form-fixer float_number' type="text" name="amount" value="<?= $key->item_igst_percentage ?>" readonly><span><?= $key->item_igst_amount ?></span>  </td>

                                                                <td><input class='form-control form-fixer float_number' type="text" name="amount" value="<?= $key->item_cgst_percentage ?>" readonly><span><?= $key->item_cgst_amount ?></span>  </td>

                                                                <td><input class='form-control form-fixer float_number' type="text" name="amount" value="<?= $key->item_sgst_percentage ?>" readonly> <span><?= $key->item_sgst_amount ?></span> </td>

    <?php } ?>

                                                            <td><input class='form-control form-fixer float_number' type="text" name="amount" value="<?= $key->item_grand_total ?>" readonly>  </td>



                                                        </tr>

<?php } ?>







                                                </tbody>

                                            </table>

                                            <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive">



                                                <tr>

                                                    <td align="right"><?php echo 'Subtotal'; ?> (+)</td>

                                                    <td align='right'><span id="totalSubTotal"><?= $general_bill_data[0]->general_bill_taxable_amount ?></span></td>

                                                </tr>



                                                <tr>

                                                    <td align="right"><?php echo 'Tax Amount'; ?> (+)</td>

                                                    <td align='right'>

                                                        <span id="totalTaxAmount"><?= $general_bill_data[0]->general_bill_tax_amount ?></span>

                                                    </td>

                                                </tr>





                                                <tr>

                                                    <td align="right"><?php echo 'Grand Total'; ?> (=)</td>

                                                    <td align='right'><span id="totalGrandTotal"><?= $general_bill_data[0]->general_bill_grand_total ?></span></td>

                                                </tr>

                                            </table>



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

?>

                <script type="text/javascript">

                    $(document).ready(function () {

                        var json = $('#txt').val();

                        var obj = JSON.parse($('#txt').val());

                        var new_arr = new Array();

                        for (var i = 0; i < obj.length; i++)

                        {

                            new_arr.push(obj[i][0].ledger_title)

                            //alert(obj[i][0].ledger_title);

                            //$('#from_to').val(obj[i][0].ledger_title);

                        }

                        $('#from_to').val(new_arr.toString());

                        // alert(new_arr.toString());



                    });



                </script>

