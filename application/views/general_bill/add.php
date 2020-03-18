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

            <li class="active">Add General Bill</li>

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

                        <form role="form" id="form" method="post" action="<?php echo base_url('general_bill/add_general_bill'); ?>">

                            <div class="well">

                                <div class="row">

                                    <div class="col-md-12">

                                        <div class="row">

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="date"> Date<span class="validation-color">*</span></label>

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

                                                    <input type="text" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?php echo $date; ?>">

                                                    <span class="validation-color" id="err_invoice_date"></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="reference_no">Invoice Number <span class="validation-color">*</span></label>

                                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?= $invoice_number ?>"  <?php

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

                                                    <label for="">Purpose of Transaction <span class="validation-color">*</span></label>

                                                    <select class="form-control select2" id="purpose_of_transaction" name="purpose_of_transaction" style="width: 100%;">

                                                        <option value="">Select</option>

                                                        <option value="Cash">Cash</option>

                                                        <option value="Deposit">Deposit</option>

                                                        <option value="Fixed Assets">Fixed Assets</option>

                                                        <option value="Duties & Taxes">Duties & Taxes</option>

                                                        <option value="Indirect Income">Indirect Income</option>

                                                        <option value="Investment">Investment</option>

                                                        <option value="Loan">Loan</option>

                                                        <option value="Advance">Advance</option>

                                                        <option value="Bank to Bank">Bank to Bank</option>

                                                        <option value="Capital">Capital</option>

                                                    </select>

                                                    <span class="validation-color" id="err_purpose_of_transaction"></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3" id="div_type_of_transaction">



                                                <div class="form-group">

                                                    <label for="">Type of Transaction <span class="validation-color">*</span></label>

                                                    <select class="form-control select2" id="type_of_transaction" name="type_of_transaction" style="width: 100%;">

                                                        <option value="Select">Select</option>



                                                    </select>

                                                    <span class="validation-color" id="err_type_of_transaction"></span>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="currency_id">Billing Currency <span class="validation-color">*</span></label>

                                                    <select class="form-control select2" id="currency_id" name="currency_id">

                                                        <?php

                                                        foreach ($currency as $key => $value)

                                                        {

                                                            if ($value->currency_id == $this->session->userdata('SESS_DEFAULT_CURRENCY'))

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

                                            <!--    <div class="col-sm-3">



                                              <div class="form-group">

                                                <label ></label>Amount<span class="validation-color">*</span>

                                                <input type="text" id="amount" name="amount" class="form-control" readonly>

                                                <span class="validation-color" id="err_amount"></span>

                                              </div>

                                            </div>

                                            -->



                                            <div class="col-sm-3" id="div_hide" hidden="true">

                                                <div class="form-group">

                                                    <label id="label_change"></label><span class="validation-color">*</span>

                                                    <div class="input-group">

                                                        <div class="input-group-addon">

                                                            <a href="" class="pull-right create_modal" id="create_new" data-toggle="modal" >+</a>

                                                        </div>

                                                        <select class="form-control select2" id="from_to" name="from_to[]" style="width: 100%;" multiple="multiple">

                                                        </select>

                                                    </div>

                                                    <span class="validation-color" id="err_from_to"></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3" id="payment_mode" style="display: none;">

                                                <div class="form-group">

                                                    <label id="label_mode_payment"></label><span class="validation-color">*</span>

                                                    <select class="form-control select2" id="mode_of_payment" name="mode_of_payment" style="width: 100%;">

                                                    </select>

                                                    <span class="validation-color" id="err_mode_of_payment"></span>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="row" id="bank_details" style="display: none;">

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="bank_name">Bank Name</label>

                                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="">

                                                    <span class="validation-color" id="err_bank_name"></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="cheque_no">Reference Number</label>

                                                    <input type="text" class="form-control" id="cheque_number" name="cheque_number" value="">

                                                    <span class="validation-color" id="err_cheque_number"></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="form-group">

                                                    <label for="cheque_date">Cheque Date (If Applicable)</label>

                                                    <input type="text" class="form-control datepicker" id="cheque_date" name="cheque_date" value="">

                                                    <span class="validation-color" id="err_cheque_date"></span>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="well" id="purchase_sale" style="display: none;">

                                <div class="row">

                                    <div class="col-sm-3">

                                        <div class="form-group">

                                            <label id="sup_cust"></label><span class="validation-color">*</span>

                                            <select class="form-control select2" autofocus="on" id="party" name="party" style="width: 100%;">

                                            </select>

                                            <span class="validation-color" id="err_party"></span>

                                        </div>

                                    </div>

                                    <div class="col-sm-3">

                                        <div class="form-group">

                                            <label for="nature_of_supply">Nature of Supply <span class="validation-color">*</span></label>

<?php if ($access_settings[0]->item_access == 'product')

{

    ?>

                                                <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="product" readonly>

                                                <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Goods" readonly>

<?php

}

elseif ($access_settings[0]->item_access == 'service')

{

    ?>

                                                <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="service" readonly>

                                                <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Service" readonly>

                                            <?php

                                            }

                                            else

                                            {

                                                ?>

                                                <input type="hidden" class="form-control" id="nature_of_supply" name="nature_of_supply" value="both" readonly>

                                                <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Product/Service" readonly>

<?php } ?>

                                            <span class="validation-color" id="err_nature_supply"><?php echo form_error('nature_of_supply'); ?></span>

                                        </div>

                                    </div>

                                    <div class="col-sm-3">

                                        <div class="form-group">

                                            <label for="type_of_supply">Billing Country <span class="validation-color">*</span></label>

                                            <select class="form-control select2" id="billing_country" name="billing_country" style="width: 100%;">

                                                <option value="">Select</option>

                                                <?php

                                                foreach ($country as $key)

                                                {

                                                    ?>

                                                    <option value='<?php echo $key->country_id ?>' <?php

                                                    if ($key->country_id == $branch[0]->branch_country_id)

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

                                    <div class="col-sm-3">

                                        <div class="form-group">

                                            <label for="type_of_supply">Place of Supply <span class="validation-color">*</span></label>

                                            <select class="form-control select2" id="billing_state" name="billing_state" style="width: 100%;">

                                                <option value="">Select</option>

                                                <?php

                                                foreach ($state as $key)

                                                {

                                                    ?>

                                                    <option value='<?php echo $key->state_id ?>' <?php

                                                    if ($key->state_id == $branch[0]->branch_state_id)

                                                    {

                                                        echo "selected";

                                                    }

                                                    ?>>

    <?php echo $key->state_name; ?>

                                                    </option>

    <?php

}

?>

                                            </select>

                                            <span class="validation-color" id="err_billing_state"><?php echo form_error('type_of_supply'); ?></span>

                                        </div>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-sm-3">

                                        <div class="form-group">

                                            <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>

                                            <select class="form-control select2" id="type_of_supply" name="type_of_supply" style="width: 100%;">

                                                <option value="regular">Regular</option>

                                            </select>

                                            <span class="validation-color" id="err_type_supply"><?php echo form_error('type_of_supply'); ?></span>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="well">

                                <div class="row">

                                    <div class="col-sm-12">

                                        <table class="table items table-striped table-bordered table-condensed table-hover ledger_table" name="ledger_data" id="ledger_data">

                                            <thead id="ledger_table_header"> </thead>

                                            <tbody id="ledger_table_body"></tbody>

                                        </table>

                                        <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive">

                                            <tr>

                                                <td align="right"><?php echo 'Subtotal'; ?> (+)</td>

                                                <td align='right'><span id="totalSubTotal">0.00</span></td>

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

                            </div>

<?php

$array_ledger_data = htmlspecialchars(json_encode($ledger_data));

$state_array       = htmlspecialchars(json_encode($state));

?>

                            <input type="hidden" name="branch_country_id" id="branch_country_id" value="<?php echo $branch[0]->branch_country_id; ?>">

                            <input type="hidden" name="branch_state_id" id="branch_state_id" value="<?php echo $branch[0]->branch_state_id; ?>">

                            <input type="hidden" name="state_array" id="state_array" value="<?php echo $state_array; ?>">

                            <input type="hidden" name="multiple_field" id="multiple_field">

                            <input type="hidden" name="common_settings_tax_split" id="common_settings_tax_split" value="<?= $access_common_settings[0]->tax_split_equaly ?>">

                            <input type="hidden" name="total_amount" id="total_amount">

                            <input type="hidden" name="total_taxable_amount" id="total_taxable_amount">

                            <input type="hidden" name="total_igst_amount" id="total_igst_amount">

                            <input type="hidden" name="total_cgst_amount" id="total_cgst_amount">

                            <input type="hidden" name="total_sgst_amount" id="total_sgst_amount">

                            <input type="hidden" name="total_tax_amount" id="total_tax_amount">

                            <input type="hidden" name="total_grand_total" id="total_grand_total">

                            <input type="hidden" name="table_data" id="table_data">

                            <input type="hidden" class="form-control" id="invoice_date_old" name="invoice_date_old" value="<?php echo $date; ?>" readonly>

                            <input type="hidden" class="form-control" id="invoice_number_old" name="invoice_number_old" value="<?= $invoice_number ?>" readonly>

                            <input type="hidden" class="form-control" id="module_id" name="module_id" value="<?= $module_id ?>" readonly>

                            <input type="hidden" class="form-control" id="privilege" name="privilege" value="<?= $privilege ?>" readonly>



                            <input type="hidden" name="mode_of_payment_text" id="mode_of_payment_text">

                            <input type="hidden" name="from_to_value" id="from_to_value">

                            <input type="hidden" name="from_to_type" id="from_to_type" value="ledger">

                            <button id="open_create_new_modal" type="button" class="btn btn-default" data-dismiss="modal" data-toggle="modal" data-target="#create_new_modal" style="visibility:hidden;"></button>

                            <button id="open_director_org_modal" type="button" class="btn btn-default" data-dismiss="modal" data-toggle="modal" data-target="#add_director_org_modal" style="visibility:hidden;"></button>

                            <button id="open_customer_modal" type="button" class="btn btn-default" data-dismiss="modal" data-toggle="modal" data-target="#customer_modal" style="visibility:hidden;"></button>

                            <button id="open_supplier_modal" type="button" class="btn btn-default" data-dismiss="modal" data-toggle="modal" data-target="#supplier_modal_general_bill" style="visibility:hidden;"></button>

                            <button id="open_bank_modal" type="button" class="btn btn-default" data-dismiss="modal" data-toggle="modal" data-target="#bank_account_modal" style="visibility:hidden;"></button>

                            <button id="open_item_select_modal" type="button" class="btn btn-default" data-dismiss="modal" data-toggle="modal" data-target="#item_type_select_modal" style="visibility:hidden;"></button>

                            <input type="hidden" id="split_value" name="split_value">

                            <!-- Hidden Field-->

                            <div class="row">

                                <div class="col-sm-12">

                                    <div class="box-footer">

                                        <button type="submit" id="general_bill_submit" class="btn btn-info">Add</button>

                                        <span class="btn btn-default" id="receipt_cancel" onclick="cancel('general_bill')">Cancel</span>

                                    </div>

                                </div>

                            </div>

                            <?php

                            $notes_sub_module  = 0;

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

                        </form>

                    </div>

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

$this->load->view('bank_account/bank_modal');

$this->load->view('general_bill/create_new_modal');

$this->load->view('customer/customer_modal');

$this->load->view('general_bill/item_type_modal');

$this->load->view('general_bill/supplier_modal_general_bill');

$this->load->view('general_bill/add_director_org_modal');

?>

<script type="text/javascript">

//  $('#hide').hide();

    var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";

     var common_settings_amount_precision = "<?= $access_common_settings[0]->amount_precision ?>";

    var settings_tax_percentage = "<?= $access_common_settings[0]->tax_split_percentage ?>";

    var settings_tax_type = "<?= $access_settings[0]->tax_type ?>";

    var settings_discount_visible = "<?= $access_settings[0]->discount_visible ?>";

    var settings_description_visible = "<?= $access_settings[0]->description_visible ?>";

    var settings_tds_visible = "<?= $access_settings[0]->tds_visible ?>";

    var settings_item_editable = "<?= $access_settings[0]->item_editable ?>";

    var branch_state_list = <?php echo json_encode($state_array); ?>;

</script>

<script type="text/javascript">

//date change

    $('#invoice_date').on('changeDate', function ()

    {

        var selected = $(this).val();

        var module_id = $("#module_id").val();

        var date_old = $("#invoice_date_old").val();

        var privilege = $("#privilege").val();

        var old_res = date_old.split("-");

        var new_res = selected.split("-");

        if (old_res[1] != new_res[1])

        {

            $.ajax(

                    {

                        url: base_url + 'general/generate_date_reference',

                        type: 'POST',

                        data:

                                {

                                    date: selected,

                                    privilege: privilege,

                                    module_id: module_id

                                },

                        success: function (data)

                        {

                            var parsedJson = $.parseJSON(data);

                            var type = parsedJson.reference_no;

                            $('#invoice_number').val(type)

                        }

                    })

        } else

        {

            var old_reference = $('#voucher_number_old').val();

            $('#voucher_number').val(old_reference)

        }

    });

    var mode_of_payment_arr_key = new Array();

    var mode_of_payment_arr_value = new Array();

    var re = new Array();

    $("#from_acc").val("");

    $("#div_type_of_transaction").hide();

    $('#label_mode_payment').text("Mode of Payment");

    var branch_country_id = $('#branch_country_id').val();

    var branch_state_id = $('#branch_state_id').val();

    var branch_state_list = JSON.parse($('#state_array').val());

    var x = 1;

    var table_index = 1;

    var from_to_id = [];

    var selected;

    var ledger_data_arr = new Array();

    var flag = 3;

    var idArray = new Array();

    var not_in_select = new Array();

    var obj = {}, matched = [], unmatched = [];

    var del = 1;

    var status = 0;

    var flag2 = 0;

    var multiple_field;

    var interset_ledger_txt;

    var interset_ledger_id;

    $("#purpose_of_transaction").change(function (event)

    {

        $('#type_of_transaction').empty();

        $("#div_hide").hide();

        $("#from_acc").val("");

        $("#to_acc").val("");

        $('#from_to').empty();

        $('#payment_mode').hide();

        $('#bank_details').hide();

// $('#payment_mode').hide();

        $("#ledger_data > tbody").empty();

        $('#ledger_table_header').empty();

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        if (purpose_of_transaction == null || purpose_of_transaction == "")

        {

            $('#err_purpose_of_transaction').text("Please Select Purpose of Transaction.");

            return false;

        } else

        {

            $('#err_purpose_of_transaction').text("");

        }

        if (purpose_of_transaction != "Indirect Income" && purpose_of_transaction != "Bank to Bank")

        {

            $("#div_type_of_transaction").show();

            $('#label_mode_payment').text("Mode of Payment");

        } else

        {

            $("#div_type_of_transaction").hide();

            $('#label_mode_payment').text("Amount Transfer From Bank");

        }

        if (purpose_of_transaction != "Fixed Assets")

        {

            $('#purchase_sale').hide();

//$('#payment_mode').show();

        }

        if (purpose_of_transaction == "Cash")

        {

            var type_of_transaction_arr = new Array("cash deposit in bank", "cash withdrawal from bank", "cash receipt", "cash payment");

        } else if (purpose_of_transaction == "Deposit")

        {

            var type_of_transaction_arr = new Array("Deposit Made", "Deposit Withdraw");

        } else if (purpose_of_transaction == "Fixed Assets")

        {

            var type_of_transaction_arr = new Array("Fixed Asset Purchase", "Fixed Asset Sold or Disposed");

        } else if (purpose_of_transaction == "Duties & Taxes")

        {

            var type_of_transaction_arr = new Array("Tax Receivable", "Tax Payable");

        } else if (purpose_of_transaction == "Investment")

        {

            var type_of_transaction_arr = new Array("Investment Made", "Investment Withdraw / Sold / Redeem / Mature");

        } else if (purpose_of_transaction == "Loan")

        {

            var type_of_transaction_arr = new Array("Loan Borrowed", "Loan Repaid To Lender", "Instalment or EMI Paid To Lender", "Loan Given", "Loan Repaid By Borrower", "Instalment or EMI Repaid By Borrower");

        } else if (purpose_of_transaction == "Advance")

        {

            var type_of_transaction_arr = new Array("Advance Taken", "Advance Given", "Advance Tax Paid", "Payment of Advance Taken", "Receipt of Advance Given");

        } else if (purpose_of_transaction == "Capital")

        {

            var type_of_transaction_arr = new Array("Capital Invested", "Additional Capital Invested", "Capital Withdrawn");

        } else if (purpose_of_transaction == "Indirect Income")

        {

            var type_of_transaction_arr = new Array();

            multiple_field = "to";

            $("#label_change").text("Type of Income");

            $("#div_hide").show();

            $("#payment_mode").show();

            $('#from_to').empty();

            $('#mode_of_payment').empty();

            $("#label_mode_payment").text("Mode of Payment");

            ledger_data(function (ledgers)

            {

                $('#from_to').append('<option value="">Select</option>');

                for (i = 0; i < ledgers.length; i++)

                {

                    $('#from_to').append('<option value="' + ledgers[i].ledger_id + '">' + ledgers[i].ledger_title + '</option>');

                }

            });

            $('#mode_of_payment').append('<option value="select">Select</option>');

            ledger_cash_bank_other(function (datas)

            {

                for (i = 0; i < datas.length; i++)

                {

                    $('#mode_of_payment').append('<option value="' + datas[i].ledger_id + '">' + datas[i].ledger_title + '</option>');

                }

            });

//get bank accounts

            bank(function (bank_accounts)

            {

                for (i = 0; i < bank_accounts.length; i++)

                {

                    $('#mode_of_payment').append('<option value="' + bank_accounts[i].ledger_id + '">' + bank_accounts[i].ledger_title + '</option>');

                }

            });

        } else if (purpose_of_transaction == "Bank to Bank")

        {

            var type_of_transaction_arr = new Array();

            multiple_field = "from";

            $("#label_change").text("Amount Transfer To Bank");

            $("#div_hide").show();

            $('#payment_mode').show();

            $('#from_to').empty();

            $('#mode_of_payment').empty();

            bank(function (bank_accounts)

            {

                $('#from_to').append('<option value="">Select</option>');

                for (i = 0; i < bank_accounts.length; i++)

                {

                    $('#from_to').append('<option value="' + bank_accounts[i].ledger_id + '">' + bank_accounts[i].ledger_title + '</option>');

                }

                $('#mode_of_payment').append('<option value="">Select</option>');

                for (i = 0; i < bank_accounts.length; i++)

                {

                    $('#mode_of_payment').append('<option value="' + bank_accounts[i].ledger_id + '">' + bank_accounts[i].ledger_title + '</option>');

                }

            });

        }

        $('#type_of_transaction').append('<option value="">Select</option>');

        for (i = 0; i < type_of_transaction_arr.length; i++)

        {

            $('#type_of_transaction').append('<option value="' + type_of_transaction_arr[i] + '">' + type_of_transaction_arr[i] + '</option>');

        }

    });

    $("#type_of_transaction").change(function (event)

    {

        $("#from_acc").val("");

        $("#ledger_name_1").val("");

        $("#ledger_name_2").val("");

        $("#ledger_name_1_id").val("");

        $("#ledger_name_2_id").val("");

        $("#to_acc").val("");

        $('#from_to').empty();

        $('#mode_of_payment').empty();

        $('#payment_mode').show();

        $("#ledger_data > tbody").empty();

        var type_of_transaction = $('#type_of_transaction').val();

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        if (type_of_transaction == null || type_of_transaction == "")

        {

            $('#err_type_of_transaction').text("Please Select Type of Transaction.");

            return false;

        } else

        {

            $('#err_type_of_transaction').text("");

        }

        if (purpose_of_transaction == "Cash")

        {

            $('#mode_of_payment').empty();

            $('#mode_of_payment').append('<option value="">Select</option>');

            ledger_cash_bank_other(function (datas)

            {

                for (i = 0; i < datas.length; i++)

                {

                    $('#mode_of_payment').append('<option value="' + datas[i].ledger_id + '">' + datas[i].ledger_title + '</option>');

                }

            });

        } else

        {

//get cash, bank ,other payment mode

            $('#mode_of_payment').empty();

            $('#mode_of_payment').append('<option value="select">Select</option>');

            ledger_cash_bank_other(function (datas)

            {

                for (i = 0; i < datas.length; i++)

                {

                    $('#mode_of_payment').append('<option value="' + datas[i].ledger_id + '">' + datas[i].ledger_title + '</option>');

                }

            });

//get bank accounts

            bank(function (bank_accounts)

            {

                for (i = 0; i < bank_accounts.length; i++)

                {

                    $('#mode_of_payment').append('<option value="' + bank_accounts[i].ledger_id + '">' + bank_accounts[i].ledger_title + '</option>');

                }

            });

        }

        $("#div_hide").show();

        if (type_of_transaction == "cash deposit in bank" || type_of_transaction == "cash withdrawal from bank")

        {

            if (type_of_transaction == "cash deposit in bank")

            {

                $("#label_change").text("Name the deposited bank");

                multiple_field = "from";

            } else if (type_of_transaction == "cash withdrawal from bank")

            {

                $("#label_change").text("Name the withdrawal bank");

                multiple_field = "to";

            }

            bank(function (bank_accounts)

            {

                $('#from_to').append('<option value="">Select</option>');

                for (i = 0; i < bank_accounts.length; i++)

                {

                    $('#from_to').append('<option value="' + bank_accounts[i].ledger_id + '">' + bank_accounts[i].ledger_title + '</option>');

                }

            });

        } else if (type_of_transaction == "cash receipt" || type_of_transaction == "cash payment")

        {

            if (type_of_transaction == "cash receipt")

            {

                $("#label_change").text("Received From");

                multiple_field = "to";

            } else if (type_of_transaction == "cash payment")

            {

                $("#label_change").text("Paid To");

                multiple_field = "from";

            }

            ledger_data(function (ledgers)

            {

                var len = new Array;

                for (k = 0; k < ledgers.length; k++)

                {

                    len[k] = ledgers[k].length; //get length of each inside array

                }

                $('#from_to').append('<option value="">Select</option>');

                for (i = 0; i < ledgers.length; i++)

                {

                    for (j = 0; j < len[i]; j++)

                    {

                        $('#from_to').append('<option value="' + ledgers[i][j].ledger_id + '">' + ledgers[i][j].ledger_title + '</option>');

                    }

                }

            });

        } else if (type_of_transaction == "Deposit Made" || type_of_transaction == "Deposit Withdraw")

        {

            if (type_of_transaction == "Deposit Made")

            {

                $("#label_change").text("Deposited In");

                multiple_field = "from";

            } else if (type_of_transaction == "Deposit Withdraw")

            {

                $("#label_change").text("Deposited Withdraw");

                multiple_field = "to";

            }

            ledger_data(function (ledgers)

            {

                $('#from_to').append('<option value="">Select</option>');

                for (i = 0; i < ledgers.length; i++)

                {

                    $('#from_to').append('<option value="' + ledgers[i].ledger_id + '">' + ledgers[i].ledger_title + '</option>');

                }

            });

        } else if (type_of_transaction == "Fixed Asset Purchase" || type_of_transaction == "Fixed Asset Sold or Disposed")

        {

            $('#purchase_sale').show();

//$('#payment_mode').hide();

            if (type_of_transaction == "Fixed Asset Purchase")

            {

                $("#label_change").text("Purchase Of ");

                $("#sup_cust").text("Supplier ");

                multiple_field = "from";

            } else if (type_of_transaction == "Fixed Asset Sold or Disposed")

            {

                $("#label_change").text("Sale/Disposal");

                $("#sup_cust").text("Customer ");

                multiple_field = "to";

            }

            ledger_data(function (ledgers)

            {

                $('#from_to').append('<option value="">Select</option>');

                for (i = 0; i < ledgers['data'].length; i++)

                {

                    $('#from_to').append('<option value="' + ledgers['data'][i].ledger_id + '">' + ledgers['data'][i].ledger_title + '</option>');

                }

                $('#party').empty();

                $('#party').append('<option value="">Select</option>');

                for (i = 0; i < ledgers['party'].length; i++)

                {

                    $('#party').append('<option value="' + ledgers['party'][i].ledger_id + '-' + ledgers['party'][i].country_id + '-' + ledgers['party'][i].state_id + '">' + ledgers['party'][i].ledger_title + '</option>');

                }

            });

        } else if (type_of_transaction == "Tax Receivable" || type_of_transaction == "Tax Payable")

        {

            if (type_of_transaction == "Tax Receivable")

            {

                $("#label_change").text("Taxes Received as");

                multiple_field = "to";

            } else if (type_of_transaction == "Tax Payable")

            {

                $("#label_change").text("Payment of");

                multiple_field = "from";

            }

            ledger_data(function (ledgers)

            {

                $('#from_to').append('<option value="">Select</option>');

                for (i = 0; i < ledgers.length; i++)

                {

                    $('#from_to').append('<option value="' + ledgers[i].ledger_id + '">' + ledgers[i].ledger_title + '</option>');

                }

            });

        } else if (type_of_transaction == "Investment Made" || type_of_transaction == "Investment Withdraw / Sold / Redeem / Mature")

        {

            if (type_of_transaction == "Investment Made")

            {

                $("#label_change").text("Investment in");

                multiple_field = "from";

            } else if (type_of_transaction == "Investment Withdraw / Sold / Redeem / Mature")

            {

                $("#label_change").text("Investment withdrawn/sold/redeem/mature");

                multiple_field = "to";

            }

            ledger_data(function (ledgers)

            {

                $('#from_to').append('<option value="">Select</option>');

                for (i = 0; i < ledgers.length; i++)

                {

                    $('#from_to').append('<option value="' + ledgers[i].ledger_id + '">' + ledgers[i].ledger_title + '</option>');

                }

            });

        } else if (type_of_transaction == "Loan Borrowed" || type_of_transaction == "Loan Repaid To Lender" || type_of_transaction == "Instalment or EMI Paid To Lender" || type_of_transaction == "Loan Given" || type_of_transaction == "Loan Repaid By Borrower" || type_of_transaction == "Instalment or EMI Repaid By Borrower")

        {

            $('#from_to').empty();

            $('#from_to').append('<option value="">Select</option>');

            bank(function (bank_accounts)

            {

                for (i = 0; i < bank_accounts.length; i++)

                {

                    $('#from_to').append('<option value="' + bank_accounts[i].ledger_id + '">' + bank_accounts[i].ledger_title + '</option>');

                }

            });

            ledger_data(function (ledgers)

            {

                for (i = 0; i < ledgers.length; i++)

                {

                    $('#from_to').append('<option value="' + ledgers[i].ledger_id + '">' + ledgers[i].ledger_title + '</option>');

                }

            });

            if (type_of_transaction == "Loan Borrowed" || type_of_transaction == "Loan Repaid By Borrower" || type_of_transaction == "Instalment or EMI Repaid By Borrower")

            {

                $("#label_change").text("From whom");

                multiple_field = "to";

            } else if (type_of_transaction == "Loan Repaid To Lender" || type_of_transaction == "Instalment or EMI Paid To Lender" || type_of_transaction == "Loan Given")

            {

                $("#label_change").text("For whom");

                multiple_field = "from";

            }

        } else if (type_of_transaction == "Advance Taken" || type_of_transaction == "Advance Given" || type_of_transaction == "Payment of Advance Taken" || type_of_transaction == "Receipt of Advance Given" || type_of_transaction == "Advance Tax Paid")

        {

            if (type_of_transaction == "Advance Taken")

            {

                $("#label_change").text("Advance Taken From");

                multiple_field = "to";

            } else if (type_of_transaction == "Advance Given")

            {

                $("#label_change").text("Advance Given To");

                multiple_field = "from";

            } else if (type_of_transaction == "Payment of Advance Taken")

            {

                $("#label_change").text("Advance Refund To");

                multiple_field = "from";

            } else if (type_of_transaction == "Receipt of Advance Given")

            {

                $("#label_change").text("Advance Repaid By");

                multiple_field = "to";

            } else if (type_of_transaction == "Advance Tax Paid")

            {

                $("#label_change").text("Advance Tax Paid To");

                multiple_field = "from";

            }

            ledger_data(function (ledgers)

            {

                var len = new Array;

                for (k = 0; k < ledgers.length; k++)

                {

                    len[k] = ledgers[k].length; //get length of each inside array

                }

                $('#from_to').empty();

                $('#from_to').append('<option value="">Select</option>');

                for (i = 0; i < ledgers.length; i++)

                {

                    for (j = 0; j < len[i]; j++)

                    {

                        $('#from_to').append('<option value="' + ledgers[i][j].ledger_id + '">' + ledgers[i][j].ledger_title + '</option>');

                    }

                }

            });

        } else if (type_of_transaction == "Capital Invested" || type_of_transaction == "Additional Capital Invested" || type_of_transaction == "Capital Withdrawn")

        {

            if (type_of_transaction == "Capital Invested" || type_of_transaction == "Additional Capital Invested")

            {

                $("#label_change").text("From Whom");

                multiple_field = "to";

            } else if (type_of_transaction == "Capital Withdrawn")

            {

                $("#label_change").text("To Whom");

                multiple_field = "from";

            }

            ledger_data(function (ledgers)

            {

                var len = new Array;

                for (k = 0; k < ledgers.length; k++)

                {

                    len[k] = ledgers[k].length; //get length of each inside array

                }

                $('#from_to').empty();

                $('#from_to').append('<option value="">Select</option>');

                for (i = 0; i < ledgers.length; i++)

                {

                    for (j = 0; j < len[i]; j++)

                    {

                        $('#from_to').append('<option value="' + ledgers[i][j].ledger_id + '">' + ledgers[i][j].ledger_title + '</option>');

                    }

                }

            });

        }

    });

    $("#from_to").change(function (event)

    {

        var type_of_transaction = $('#type_of_transaction').val();

        var mode_of_payment = $("option:selected", '#mode_of_payment').text();

        var party = $("option:selected", '#party').text();

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        $('#multiple_field').val(multiple_field);

        var latestSelected;

        var currentOptionsValue = $(this).val();

        idArray = [];

        matched = [];

        unmatched = [];

        not_in_select = [];

        $('#split_value').val(currentOptionsValue);

        if (selected)

        {

            latestSelected = currentOptionsValue.filter(function (element) {

                return selected.indexOf(element) < 0;

            });

        }

        selected = $(this).val();

        if (latestSelected == "" || latestSelected == undefined)

        {

            from_to_id = selected;

        } else

        {

            from_to_id = latestSelected;

        }

        var split_val = $('#split_value').val().split(',');

        var table_length = $('table.ledger_table>tbody>tr').length;

        var table_header_length = $('table.ledger_table>thead>tr').length;

        if (table_length > 0)

        {

            if (multiple_field == "from")

            {

                $("#ledger_table_body tr").find('input[name="from_acc_id"]').each(function ()

                {

                    var row = $(this).closest("tr");

                    var idd = row.find('input[name="from_acc_id"]').val();

                    idArray.push(idd);

                });

            } else if (multiple_field == "to")

            {

                $("#ledger_table_body tr").find('input[name="to_acc_id"]').each(function ()

                {

                    var row = $(this).closest("tr");

                    var idd = row.find('input[name="to_acc_id"]').val();

                    idArray.push(idd);

                });

            }

            for (var i = 0; i < split_val.length; i++)

            {

                flag = 1;

                for (var j = 0; j < idArray.length; j++)

                {

                    if (idArray[j] == split_val[i])

                    {

                        flag = 0;

                    }

                }

                if (flag == 0)

                {

                    matched.push(split_val[i]);

                } else if (flag == 1)

                {

                    unmatched.push(split_val[i]);

                }

            }

            for (var i = 0; i < idArray.length; i++)

            {

                status = 0;

                for (var j = 0; j < split_val.length; j++)

                {

                    if (idArray[i] == split_val[j])

                    {

                        status = 0;

                        break;

                    } else

                    {

                        status = 1;

                    }

                }

                if (status == 1)

                {

                    not_in_select.push(idArray[i]);

                }

            }

//delete row from table

            if (not_in_select.length > 0)

            {

                $('#ledger_table_body tr').each(function () {

                    var row = $(this).closest("tr");

                    if (+row.find('input[name^="from_acc_id"]').val() == not_in_select)

                    {

                        deleteRow(row);

                        row.remove();

                    }

                })

            }

        } else

        {

//get interest leger tiltle and id

            if (type_of_transaction == "Loan Repaid By Borrower" || type_of_transaction == "Instalment or EMI Repaid By Borrower" || type_of_transaction == "Loan Repaid To Lender" || type_of_transaction == "Instalment or EMI Paid To Lender")

            {

                ledger_data_loan(function (ledgers_interest)

                {

                    interset_ledger_txt = ledgers_interest['data'][0].ledger_title;

                    interset_ledger_id = ledgers_interest['data'][0].ledger_id;

                    $('#ledger_table_body').append('<tr><td><input class="form-control form-fixer" type="hidden" name="intrst_ledger_id" id="intrst_ledger_id"><input class="form-control form-fixer from_acc" type="text" name="from_acc_intrst" id="from_acc_intrst" readonly></td><td><input class="form-control form-fixer to_acc" type="text" name="to_acc_intrst" id="to_acc_intrst" readonly></td><td><input class="form-control form-fixer" type="text" name="amount_intrst" id="amount_intrst"></td></tr>');

                    if (type_of_transaction == "Loan Repaid To Lender" || type_of_transaction == "Instalment or EMI Paid To Lender")

                    {

                        $('#from_acc_intrst').val(interset_ledger_txt);

                        $('#intrst_ledger_id').val(interset_ledger_id);

                    } else if (type_of_transaction == "Loan Repaid By Borrower" || type_of_transaction == "Instalment or EMI Repaid By Borrower")

                    {

                        $('#to_acc_intrst').val(interset_ledger_txt);

                        $('#intrst_ledger_id').val(interset_ledger_id);

                    }

                });

            }

        }//table length condition

        if (purpose_of_transaction == "Fixed Assets")

        {

            var th = '<tr><th class="span2" width="20%">From</th><th class="span2" width="20%">To</th><th class="span2" width="15%">Taxable</th><th class="span2" width="10%">IGST</th><th class="span2" width="10%">CGST</th><th class="span2" width="10%">SGST</th><th class="span2" width="15%">Amount</th></tr>';

            var tax_row = '<td><input class="form-control form-feixr float_number" type="text" name="item_taxable" id="item_taxable_' + table_index + '"></td><td><input type="hidden" name="item_tax_amount"><input class="form-control form-feixr float_number" type="text" name="item_igst" id="item_igst_' + table_index + '"><input type="hidden" name="item_igst_amount" class="form-control form-fixer"><span id="item_igst_amount_lbl_' + table_index + '" class="pull-right" style="color:red;""></span></td><td><input class="form-control form-feixr float_number" type="text" name="item_cgst" id="item_cgst_' + table_index + '"><input type="hidden" name="item_cgst_amount" class="form-control form-fixer"><span id="item_cgst_amount_lbl_' + table_index + '" class="pull-right" style="color:red;""></span></td><td><input class="form-control form-feixr float_number" type="text" name="item_sgst" id="item_sgst_' + table_index + '"><input type="hidden" name="item_sgst_amount" class="form-control form-fixer"><span id="item_sgst_amount_lbl_' + table_index + '" class="pull-right" style="color:red;""></span></td>';

        } else

        {

            var th = '<tr><th class="span2" width="30%">From</th><th class="span2" width="30%">To</th><th class="span2" width="40%">Amount</th></tr>';

        }

        if (type_of_transaction == "cash deposit in bank" || type_of_transaction == "cash payment" || type_of_transaction == "Deposit Made" || type_of_transaction == "Fixed Asset Purchase" || type_of_transaction == "Tax Payable" || type_of_transaction == "Investment Made" || type_of_transaction == "Payment of Advance Taken" || type_of_transaction == "Advance Given" || type_of_transaction == "Advance Tax Paid" || purpose_of_transaction == "Bank to Bank" || type_of_transaction == "Capital Withdrawn" || type_of_transaction == "Loan Repaid To Lender" || type_of_transaction == "Instalment or EMI Paid To Lender" || type_of_transaction == "Loan Given")

        {

//mutiple

            if (table_length > 0)

            {

                for (var k = 0; k < unmatched.length; k++)

                {

                    if ($("#from_to").val() != "")

                    {

                        from_to = $("#from_to option[value=" + unmatched[k] + "]").text();

                        var tr = '<tr id="' + table_index + '"><td><input type="hidden" name="item_key_value"  value="' + table_index + '"><input class="form-control form-fixer" type="text" name="from_acc" id="from_acc_' + table_index + '" value="' + from_to + '" readonly><input class="form-control form-fixer item" type="hidden" name="from_acc_id" id="from_acc_id' + table_index + '" value="' + unmatched[k] + '"></td>' +

                                '<td><input class="form-control form-fixer to_acc" type="text" name="to_acc" id="to_acc_' + table_index + '" readonly><input class="form-control form-fixer to_acc_id" type="hidden" name="to_acc_id" id="to_acc_id_' + table_index + '"></td>';

                        if (purpose_of_transaction == "Fixed Assets")

                        {

                            tr += tax_row;

                        }

                        tr += '<td><input class="form-control form-feixr float_number" type="text" name="amount" id="amount_' + table_index + '">';

// tr += '<a href="#"  class="remove_field pull-right" value="'+from_to+'" id="'+from_to+'">(-)Remove</a></td></tr>';

                        if (table_header_length == 0)

                        {

                            $('#ledger_table_header').append(th);

                        }

                        $('#ledger_table_body').append(tr); //add input box

                        if (purpose_of_transaction == "Fixed Assets")

                        {

                            change_gst();

                        }

                        call_css();

                        x++;

                        table_index++;

//  calculateGrandTotal();

                    }

                }

                if (mode_of_payment != "" && mode_of_payment != "Select" && purpose_of_transaction != "Fixed Assets")

                {

                    $(".to_acc").val(mode_of_payment);

                } else if (mode_of_payment != "" && mode_of_payment != "Select" && purpose_of_transaction == "Fixed Assets")

                {

                    $(".to_acc").val(party);

                }

            } else

            {

                if ($("#from_to").val() != "")

                {

                    from_to = $("#from_to option[value=" + from_to_id + "]").text();

                    var tr = '<tr id="' + table_index + '"><td><input type="hidden" name="item_key_value"  value="' + table_index + '"><input class="form-control form-fixer" type="text" name="from_acc" id="from_acc_' + table_index + '" value="' + from_to + '" readonly><input class="form-control form-fixer item" type="hidden" name="from_acc_id" id="from_acc_id' + table_index + '" value="' + from_to_id + '"></td>' +

                            '<td><input class="form-control form-fixer to_acc" type="text" name="to_acc" id="to_acc_' + table_index + '" readonly><input class="form-control form-fixer to_acc_id" type="hidden" name="to_acc_id" id="to_acc_id_' + table_index + '"></td>';

                    if (purpose_of_transaction == "Fixed Assets")

                    {

                        tr += tax_row;

                    }

                    tr += '<td><input class="form-control form-feixr float_number" type="text" name="amount" id="amount_' + table_index + '">';

// tr += '<a href="#"  class="remove_field pull-right" value="'+from_to+'" id="'+from_to+'">(-)Remove</a></td></tr>';

                    if (table_header_length == 0)

                    {

                        $('#ledger_table_header').append(th);

                    }

                    $('#ledger_table_body').append(tr); //add input box

                    call_css();

                    x++;

                    table_index++;

//  calculateGrandTotal();

                }

                if (mode_of_payment != "" && mode_of_payment != "Select" && purpose_of_transaction != "Fixed Assets")

                {

                    $(".to_acc").val(mode_of_payment);

                } else if (mode_of_payment != "" && mode_of_payment != "Select" && purpose_of_transaction == "Fixed Assets")

                {

                    $(".to_acc").val(party);

                }

            }

//multiple ends

        } else if (type_of_transaction == "cash withdrawal from bank" || type_of_transaction == "cash receipt" || type_of_transaction == "Deposit Withdraw" || type_of_transaction == "Fixed Asset Sold or Disposed" || type_of_transaction == "Tax Receivable" || purpose_of_transaction == "Indirect Income" || type_of_transaction == "Investment Withdraw / Sold / Redeem / Mature" || type_of_transaction == "Advance Taken" || type_of_transaction == "Receipt of Advance Given" || type_of_transaction == "Capital Invested" || type_of_transaction == "Additional Capital Invested" || type_of_transaction == "Loan Borrowed" || type_of_transaction == "Loan Repaid By Borrower" || type_of_transaction == "Instalment or EMI Repaid By Borrower")

        {

            if (table_length > 0)

            {

// $('#ledger_table_body').empty();

                for (var k = 0; k < unmatched.length; k++)

                {

                    if ($("#from_to").val() != "")

                    {

                        from_to = $("#from_to option[value=" + unmatched[k] + "]").text();

                        var tr = '<tr id="' + table_index + '"><td><input type="hidden" name="item_key_value"  value="' + table_index + '"><input class="form-control form-fixer from_acc" type="text" name="from_acc" id="from_acc_' + table_index + '" readonly><input class="form-control form-fixer from_acc_id" type="hidden" name="from_acc_id" id="from_acc_id' + table_index + '"></td>' +

                                '<td><input class="form-control form-fixer to_acc" type="text" name="to_acc" id="to_acc_' + table_index + '" value="' + from_to + '" readonly><input class="form-control form-fixer to_acc_id" type="hidden" name="to_acc_id" id="to_acc_id_' + table_index + '" value="' + unmatched[k] + '"></td>';

                        if (purpose_of_transaction == "Fixed Assets")

                        {

                            tr += tax_row;

                        }

                        tr += '<td><input class="form-control form-feixr float_number" type="text" name="amount" id="amount_' + table_index + '">';

// tr += '<a href="#"  class="remove_field pull-right" value="'+from_to+'" id="'+from_to+'">(-)Remove</a></td></tr>';

                        if (table_header_length == 0)

                        {

                            $('#ledger_table_header').append(th);

                        }

                        $('#ledger_table_body').append(tr); //add input box

                        call_css();

                        x++;

                        table_index++;

                    }    //  calculateGrandTotal();

                }

                if (mode_of_payment != "" && mode_of_payment != "Select" && purpose_of_transaction != "Fixed Assets")

                {

                    $(".from_acc").val(mode_of_payment);

                } else if (mode_of_payment != "" && mode_of_payment != "Select" && purpose_of_transaction == "Fixed Assets")

                {

                    $(".from_acc").val(party);

                }

            } else

            {

                from_to = $("#from_to option[value=" + from_to_id + "]").text();

                var tr = '<tr id="' + table_index + '"><td><input type="hidden" name="item_key_value"  value="' + table_index + '"><input class="form-control form-fixer from_acc" type="text" name="from_acc" id="from_acc_' + table_index + '" readonly><input class="form-control form-fixer from_acc_id" type="hidden" name="from_acc_id" id="from_acc_id' + table_index + '"></td>' +

                        '<td><input class="form-control form-fixer to_acc" type="text" name="to_acc" id="to_acc_' + table_index + '" value="' + from_to + '" readonly><input class="form-control form-fixer to_acc_id" type="hidden" name="to_acc_id" id="to_acc_id_' + table_index + '" value="' + from_to_id + '"></td>';

                if (purpose_of_transaction == "Fixed Assets")

                {

                    tr += tax_row;

                }

                tr += '<td><input class="form-control form-feixr float_number" type="text" name="amount" id="amount_' + table_index + '">';

// tr += '<a href="#"  class="remove_field pull-right" value="'+from_to+'" id="'+from_to+'">(-)Remove</a></td></tr>';

                if (table_header_length == 0)

                {

                    $('#ledger_table_header').append(th);

                }

                $('#ledger_table_body').append(tr); //add input box

                calculateGrandTotal();

                call_css();

                x++;

                table_index++;

            }

            if (mode_of_payment != "" && mode_of_payment != "Select" && purpose_of_transaction != "Fixed Assets")

            {

                $(".from_acc").val(mode_of_payment);

            } else if (mode_of_payment != "" && mode_of_payment != "Select" && purpose_of_transaction == "Fixed Assets")

            {

                $(".from_acc").val(party);

            }

        }

    });

    $("#mode_of_payment").change(function (event)

    {

        var from_to = $("option:selected", '#from_to').text();

        var from_to_id = $('#from_to').val();

        var type_of_transaction = $('#type_of_transaction').val();

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        var mode_of_payment = $("option:selected", '#mode_of_payment').text();

        var mode_of_payment_id = $('#mode_of_payment').val();

        if (mode_of_payment_id == null || mode_of_payment_id == "")

        {

            $('#err_mode_of_payment').text("Please Select Mode of Payemnt.");

            return false;

        } else

        {

            $('#err_mode_of_payment').text("");

        }

        if (mode_of_payment == "BANK")

        {

            $('#bank_details').show();

        } else

        {

            $('#bank_details').hide();

        }

        $("#mode_of_payment_text").val(mode_of_payment);//to store text value of mode of payment

        if (type_of_transaction == "cash deposit in bank" || type_of_transaction == "cash payment" || type_of_transaction == "Deposit Made" || type_of_transaction == "Tax Payable" || type_of_transaction == "Investment Made" || type_of_transaction == "Advance Given" || type_of_transaction == "Advance Tax Paid" || type_of_transaction == "Payment of Advance Taken" || purpose_of_transaction == "Bank to Bank" || type_of_transaction == "Capital Withdrawn" || type_of_transaction == "Loan Repaid To Lender" || type_of_transaction == "Instalment or EMI Paid To Lender" || type_of_transaction == "Loan Given")

        {

            if (mode_of_payment != "Select")

            {

// $("#from_acc").val(from_to);

                $(".to_acc").val(mode_of_payment);

                $(".to_acc_id").val(mode_of_payment_id);

            } else

            {

                $(".to_acc").val("");

                $(".to_acc_id").val("");

            }

        } else if (type_of_transaction == "cash withdrawal from bank" || type_of_transaction == "cash receipt" || type_of_transaction == "Deposit Withdraw" || type_of_transaction == "Tax Receivable" || purpose_of_transaction == "Indirect Income" || type_of_transaction == "Investment Withdraw / Sold / Redeem / Mature" || type_of_transaction == "Advance Taken" || type_of_transaction == "Receipt of Advance Given" || type_of_transaction == "Capital Invested" || type_of_transaction == "Additional Capital Invested" || type_of_transaction == "Loan Borrowed" || type_of_transaction == "Loan Repaid By Borrower" || type_of_transaction == "Instalment or EMI Repaid By Borrower")

        {

            if (mode_of_payment != "Select")

            {

// $("#to_acc").val(from_to);

                $(".from_acc").val(mode_of_payment);

                $(".from_acc_id").val(mode_of_payment_id);

            } else

            {

                $(".from_acc").val("");

                $(".from_acc_id").val("");

            }

        }

    });

    $("#party").change(function (event)

    {

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        var type_of_transaction = $('#type_of_transaction').val();

        var part_id = $('#party').val();

        var party = $("option:selected", '#party').text();

        if (type_of_transaction == "Fixed Asset Purchase")

        {

            $(".to_acc").val(party);

            $(".to_acc_id").val(part_id);

        } else if (type_of_transaction == "Fixed Asset Sold or Disposed")

        {

            $(".from_acc").val(party);

            $(".from_acc_id").val(part_id);

        }

//billing country

        var table_data = $('#table_data').val();

        var party = $('#party').val();

        var party_split = party.split('-');

        var party_id = party_split[0];

        var party_country_id = party_split[1];

        var party_state_id = party_split[2];

        $("#billing_country").select2().val(party_country_id).trigger('change.select2');

        $("#billing_state").select2().val(party_state_id).trigger('change.select2');

        var billing_country_id = $('#billing_country').val();

        if (billing_country_id != branch_country_id)

        {

            $('#billing_country').change();

            change_gst();

        } else

        {

            if (flag2 == 0)

            {

                $('#billing_country').change();

                $("#billing_state").select2().val(party_state_id).trigger('change.select2');

                change_gst();

            } else

            {

                if (table_data != "")

                {

                    change_gst();

                }

            }

        }

    });

    $(".create_modal").click(function ()

    {

        var type_of_transaction = $('#type_of_transaction').val();

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        if (type_of_transaction == "cash deposit in bank" || type_of_transaction == "cash withdrawal from bank")

        {

            $("#open_bank_modal").click();

        } else if (type_of_transaction == "cash receipt" || type_of_transaction == "cash payment")

        {

            $("#open_item_select_modal").click();

        } else if (type_of_transaction == "Advance Taken" || type_of_transaction == "Payment of Advance Taken")

        {

            $("#open_customer_modal").click();

        } else if (type_of_transaction == "Advance Given" || type_of_transaction == "Receipt of Advance Given")

        {

            $("#open_supplier_modal").click();

        } else if (type_of_transaction == "Loan Borrowed" || type_of_transaction == "Loan Repaid To Lender" || type_of_transaction == "Instalment or EMI Paid To Lender" || type_of_transaction == "Loan Given" || type_of_transaction == "Loan Repaid By Borrower" || type_of_transaction == "Instalment or EMI Repaid By Borrower")

        {

            $("#open_item_select_modal").click();

        } else if (type_of_transaction == "Capital Invested" || type_of_transaction == "Additional Capital Invested" || type_of_transaction == "Capital Withdrawn")

        {

            $("#open_director_org_modal").click();

        } else if (purpose_of_transaction == "Bank to Bank")

        {

            $("#open_bank_modal").click();

        } else

        {

            $("#open_create_new_modal").click();

        }

    });

//billing country changes

    $("#billing_country").change(function (event)

    {

        var billing_country = $('#billing_country').val();

        var branch_country_id = $('#branch_country_id').val();

        if (branch_country_id != billing_country)

        {

            $('#billing_state').html('');

            $('#billing_state').append('<option value="0">Out of Country</option>');

            $('#type_of_supply').html('');

            $('#type_of_supply').append('<option value="import">Import</option>');

        } else

        {

            $('#billing_state').html('');

            $('#billing_state').append('<option value="">Select</option>');

            for (var i = 0; i < branch_state_list.length; i++)

            {

                $('#billing_state').append('<option value="' + branch_state_list[i].state_id + '">' + branch_state_list[i].state_name + '</option>');

            }

            $('#type_of_supply').html('');

            $('#type_of_supply').append('<option value="regular">Regular</option>');

        }

        $('#type_of_supply').change();

    });

//billing state changes

    $("#billing_state").change(function (event)

    {

        var table_data = $('#table_data').val();

        var billing_state_id = $('#billing_state').val();

        if (table_data != "")

        {

            change_gst();

        }

    });

    $("#type_of_supply").change(function (event)

    {

        if ($('#type_of_supply').val() == 'import')

        {

            $('input[name="gstPayable"][value="no"]').attr('checked', false);

            $('input[name="gstPayable"][value="yes"]').attr('checked', true);

        } else

        {

            $('input[name="gstPayable"][value="yes"]').attr('checked', false);

            $('input[name="gstPayable"][value="no"]').attr('checked', true);

        }

    });

    $("#general_bill_submit").click(function (event)

    {

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        var type_of_transaction = $('#type_of_transaction').val();

        var amount = $('#amount').val();

        var mode_of_payment = $('#mode_of_payment').val();

        var from_to = $('#from_to').val();

        if (purpose_of_transaction == null || purpose_of_transaction == "") {

            $("#err_purpose_of_transaction").text("Please Select Purpose of Transaction.");

            return false;

        } else {

            $("#err_purpose_of_transaction").text("");

        }

        if (purpose_of_transaction != "Indirect Income" && purpose_of_transaction != "Bank to Bank")

        {

            if (type_of_transaction == null || type_of_transaction == "") {

                $("#err_type_of_transaction").text("Please Select Type of Transaction.");

                return false;

            } else {

                $("#err_type_of_transaction").text("");

            }

        }

        if (mode_of_payment == null || mode_of_payment == "") {

            $("#err_mode_of_payment").text("Please Select Mode of Payment.");

            return false;

        } else {

            $("#err_mode_of_payment").text("");

        }

        if (from_to == null || from_to == "") {

            $("#err_from_to").text("Please Select.");

            return false;

        } else {

            $("#err_from_to").text("");

        }

    });

    function bank(callback)

    {

        $.ajax({

            url: base_url + 'bank_account/get_bank_account',

            dataType: 'JSON',

            method: 'POST',

            data: {

            },

            success: function (result)

            {

                callback(result);

            }

        });

    }

    function ledger_data(callback2)

    {

        var type_of_transaction = $('#type_of_transaction').val();

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        $.ajax({

            url: base_url + 'ledger/get_all_ledgers',

            dataType: 'JSON',

            method: 'POST',

            data: {

                "type": type_of_transaction,

                "purpose": purpose_of_transaction

            },

            success: function (result)

            {

                callback2(result);

            }

        });

    }

    function ledger_cash_bank_other(callback3)

    {

        var type_of_transaction = $('#type_of_transaction').val();

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        $.ajax({

            url: base_url + 'ledger/get_cash_bank_other',

            dataType: 'JSON',

            method: 'POST',

            data: {

                "type": type_of_transaction,

                "purpose": purpose_of_transaction

            },

            success: function (result)

            {

                callback3(result);

            }

        });

    }

    function ledger_data_loan(callback4)

    {

        var type_of_transaction = $('#type_of_transaction').val();

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        $.ajax({

            url: base_url + 'ledger/get_interest_ledgers',

            dataType: 'JSON',

            method: 'POST',

            data: {

                "type": type_of_transaction,

                "purpose": purpose_of_transaction

            },

            success: function (result)

            {

                callback4(result);

            }

        });

    }

</script>

<script type="text/javascript">

    var common_settings_tax_split = $('#common_settings_tax_split').val();

    $('#ledger_table_body').on("click", ".remove_field", function (e) { //user click on remove text

        e.preventDefault();

        deleteRow($(this).closest("tr"));

        $(this).closest("tr").remove();

        calculateGrandTotal();

    })

    $("#ledger_table_body").on("change", 'input[name^="item_taxable"]', function (event)

    {

        row = $(this).closest("tr");

        calculateRow($(this).closest("tr"));

        calculateDiscountTax($(this).closest("tr"));

        calculateGrandTotal();

    });

//reverse calculations

    $("#ledger_table_body").on("change", 'input[name^="amount"]', function (event)

    {

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        if (purpose_of_transaction == "Fixed Assets")

        {

            row = $(this).closest("tr");

            var item_grand_total = +row.find('input[name^="amount"]').val();

            var item_igst = +row.find('input[name^="item_igst"]').val();

            var item_cgst = +row.find('input[name^="item_cgst"]').val();

            var item_sgst = +row.find('input[name^="item_sgst"]').val();

            var tax = item_igst + item_cgst + item_sgst;

            var item_discount = 0;

            var item_taxable_value = (+item_grand_total * 100) / (100 + +tax);

            var item_sub_total = (+item_taxable_value * 100) / (100 - +item_discount);

            var item_price = item_sub_total;

            row.find('input[name^="item_taxable"]').val(item_price.toFixed(2));

            if (common_settings_round_off == 'no')

            {

                row.find('input[name^="item_grand_total"]').val(item_grand_total.toFixed(2));

            }

        }

        calculateRow($(this).closest("tr"));

        calculateDiscountTax($(this).closest("tr"));

        calculateGrandTotal();

    });

    $("#ledger_table_body").on("change", 'input[name^="item_igst"],input[name^="item_cgst"],input[name^="item_sgst"]', function (event)

    {

        row = $(this).closest("tr");

        calculateRow($(this).closest("tr"));

        calculateDiscountTax($(this).closest("tr"));

        calculateGrandTotal();

    });

//split tax equally

    if (common_settings_tax_split == "yes")

    {

        $("#ledger_table_body").on("change", 'input[name^="item_cgst"]', function (event)

        {

            var row = $(this).closest("tr");

            var cgst = +row.find('input[name^="item_cgst"]').val();

            var sgst = +row.find('input[name^="item_sgst"]').val();

            row.find('input[name^="item_sgst"]').val(cgst);

            if (cgst != sgst)

            {

                row.find('input[name^="item_sgst"]').change();

            }

        });

        $("#ledger_table_body").on("change", 'input[name^="item_sgst"]', function (event)

        {

            var row = $(this).closest("tr");

            var sgst = +row.find('input[name^="item_sgst"]').val();

            var cgst = +row.find('input[name^="item_cgst"]').val();

            row.find('input[name^="item_cgst"]').val(sgst);

            if (cgst != sgst)

            {

                row.find('input[name^="item_cgst"]').change();

            }

        });

    }

    function calculateRow(row)

    {

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        var amount = +row.find('input[name^="amount"]').val();

//row.find('input[name^="total_amount"]').val((dr_amt).toFixed(2));

        var table_index = +row.find('input[name^="item_key_value"]').val();

        var key = +row.index();

        var from_id = +row.find('input[name^="from_acc_id"]').val();

        var to_id = +row.find('input[name^="to_acc_id"]').val();

        if (purpose_of_transaction == "Fixed Assets")

        {

            if (multiple_field == "from")

            {

                var splitted_val = row.find('input[name^="to_acc_id"]').val().split('-');

                to_id = splitted_val[0];

            } else if (multiple_field == "to")

            {

                var splitted_val = row.find('input[name^="from_acc_id"]').val().split('-');

                from_id = splitted_val[0];

            }

        }

        var from_id_txt = row.find('input[name^="from_acc"]').val();

        var to_id_txt = row.find('input[name^="to_acc"]').val();

        if (ledger_data_arr[key] == null)

        {

            var temp = {

                "item_key_value": table_index,

                "from_id": from_id,

                "from_id_txt": from_id_txt,

                "to_id": to_id,

                "to_id_txt": to_id_txt,

            };

            ledger_data_arr[key] = temp

        }

        ledger_data_arr[key].item_key_value = table_index;

        ledger_data_arr[key].from_id = from_id;

        ledger_data_arr[key].from_id_txt = from_id_txt;

        ledger_data_arr[key].to_id = to_id;

        ledger_data_arr[key].to_id_txt = to_id_txt;

        if (purpose_of_transaction == "Fixed Assets")

        {

            var item_igst = +row.find('input[name^="item_igst"]').val();

            var item_cgst = +row.find('input[name^="item_cgst"]').val();

            var item_sgst = +row.find('input[name^="item_sgst"]').val();

            var item_taxable_value = +row.find('input[name^="item_taxable"]').val();

            var item_igst_amount = item_taxable_value * item_igst / 100;

            var item_cgst_amount = item_taxable_value * item_cgst / 100;

            var item_sgst_amount = item_taxable_value * item_sgst / 100;

            var item_tax_amount = item_igst_amount + item_cgst_amount + item_sgst_amount;

            var item_grand_total = (item_taxable_value + item_tax_amount).toFixed(2);

            ledger_data_arr[key].item_igst = item_igst;

            ledger_data_arr[key].item_cgst = item_cgst;

            ledger_data_arr[key].item_sgst = item_sgst;

            ledger_data_arr[key].item_igst_amount = item_igst_amount.toFixed(2);

            ledger_data_arr[key].item_cgst_amount = item_cgst_amount.toFixed(2);

            ledger_data_arr[key].item_sgst_amount = item_sgst_amount.toFixed(2);

            ledger_data_arr[key].item_tax_amount = item_tax_amount.toFixed(2);

            ledger_data_arr[key].item_taxable_value = item_taxable_value.toFixed(2);

            ledger_data_arr[key].item_grand_total = global_round_off(item_grand_total);

        } else

        {

            ledger_data_arr[key].item_grand_total = amount;

        }

        var table_data = JSON.stringify(ledger_data_arr);

        $('#table_data').val(table_data);

    }

// Delete function

    function deleteRow(row)

    {

        var table_index = +row.find('input[name^="item_key_value"]').val();

        for (var i = 0; i < ledger_data_arr.length; i++)

        {

            if (ledger_data_arr[i] != null && ledger_data_arr[i].item_key_value == table_index)

            {

                ledger_data_arr.splice(i, 1)

            }

        }

        var table_data = JSON.stringify(ledger_data_arr);

        $('#table_data').val(table_data)

    }

    function change_gst()

    {

        if (flag2 == 1)

        {

            $("#ledger_table_body").find('input[name^="item_igst"]').each(function ()

            {

                var row = $(this).closest('tr');

                if ($('#type_of_supply').val() == "export_without_payment")

                {

                    row.find('input[name^="item_igst"]').val(0);

                    row.find('input[name^="item_igst"]').attr("readonly", true);

                } else

                {

                    var item_id = +row.find('input[name^="item_id"]').val();

                    row.find('input[name^="item_igst"]').attr("readonly", false);

                }

                row.find('input[name^="item_cgst"]').val(0);

                row.find('input[name^="item_cgst"]').attr("readonly", true);

                row.find('input[name^="item_sgst"]').val(0);

                row.find('input[name^="item_sgst"]').attr("readonly", true);

                $(this).change();

            });

        } else if (flag2 == 2)

        {

            $("#ledger_table_body").find('input[name^="item_igst"]').each(function ()

            {

                var row = $(this).closest('tr');

                row.find('input[name^="item_igst"]').attr("readonly", true);

                row.find('input[name^="item_cgst"]').attr("readonly", false);

                row.find('input[name^="item_sgst"]').attr("readonly", false);

                $(this).change();

            });

        } else

        {

            var billing_state_id = $('#billing_state').val();

            $("#ledger_table_body").find('input[name^="item_igst"]').each(function ()

            {

                var row = $(this).closest('tr');

                var item_id = +row.find('input[name^="item_id"]').val();

                var item_type = row.find('input[name^="item_type"]').val();

                if (branch_state_id == billing_state_id)

                {

                    row.find('input[name^="item_igst"]').val(0);

                    row.find('input[name^="item_igst"]').attr("readonly", true);

                    row.find('input[name^="item_cgst"]').attr("readonly", false);

                    row.find('input[name^="item_sgst"]').attr("readonly", false);

                } else

                {

                    row.find('input[name^="item_igst"]').attr("readonly", false);

                    row.find('input[name^="item_cgst"]').val(0);

                    row.find('input[name^="item_cgst"]').attr("readonly", true);

                    row.find('input[name^="item_sgst"]').val(0);

                    row.find('input[name^="item_sgst"]').attr("readonly", true);

                }

                $(this).change();

            });

        }

        flag2 = 0;

    }

    function calculateDiscountTax(row, data = 0)

    {

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        var type_of_transaction = $('#type_of_transaction').val();

        var table_index = +row.find('input[name^="item_key_value"]').val();

        if (purpose_of_transaction == "Fixed Assets")

        {

            var item_igst = +row.find('input[name^="item_igst"]').val();

            var item_cgst = +row.find('input[name^="item_cgst"]').val();

            var item_sgst = +row.find('input[name^="item_sgst"]').val();

            var item_taxable_value = +row.find('input[name^="item_taxable"]').val();

            var item_igst_amount = item_taxable_value * item_igst / 100;

            var item_cgst_amount = item_taxable_value * item_cgst / 100;

            var item_sgst_amount = item_taxable_value * item_sgst / 100;

            var item_tax_amount = item_igst_amount + item_cgst_amount + item_sgst_amount;

            var item_grand_total = (item_taxable_value + item_tax_amount).toFixed(2);

            row.find('input[name^="amount"]').val(global_round_off(item_grand_total));

            row.find('input[name^="item_igst_amount"]').val(item_igst_amount.toFixed(2));

            row.find('#item_igst_amount_lbl_' + table_index).text(item_igst_amount.toFixed(2));

            row.find('input[name^="item_cgst_amount"]').val(item_cgst_amount.toFixed(2));

            row.find('#item_cgst_amount_lbl_' + table_index).text(item_cgst_amount.toFixed(2));

            row.find('input[name^="item_sgst_amount"]').val(item_sgst_amount.toFixed(2));

            row.find('#item_sgst_amount_lbl_' + table_index).text(item_sgst_amount.toFixed(2));

            row.find('input[name^="item_tax_amount"]').val(item_tax_amount.toFixed(2));

            var key = +row.index();

            ledger_data_arr[key].item_key_value = +table_index;

            ledger_data_arr[key].item_igst = item_igst;

            ledger_data_arr[key].item_igst_amount = item_igst_amount.toFixed(2);

            ledger_data_arr[key].item_cgst = item_cgst;

            ledger_data_arr[key].item_cgst_amount = item_cgst_amount.toFixed(2);

            ledger_data_arr[key].item_sgst = item_sgst;

            ledger_data_arr[key].item_sgst_amount = item_sgst_amount.toFixed(2);

            ledger_data_arr[key].item_tax_amount = item_tax_amount.toFixed(2);

            ledger_data_arr[key].item_taxable_value = item_taxable_value.toFixed(2);

            ledger_data_arr[key].item_grand_total = global_round_off(item_grand_total);

        } else

        {

            var amount = +row.find('input[name^="amount"]').val();

            var key = +row.index();

            ledger_data_arr[key].item_key_value = +table_index;

            ledger_data_arr[key].item_grand_total = global_round_off(amount);

        }

        var table_data = JSON.stringify(ledger_data_arr);

        $('#table_data').val(table_data)

    }

    function calculateGrandTotal()

    {

        var purpose_of_transaction = $('#purpose_of_transaction').val();

        var sub_total = 0;

        var tax = 0;

        var igst = 0;

        var cgst = 0;

        var sgst = 0;

        var taxable = 0;

        var grand_total = 0;

//    $("#ledger_table_body").find('input[name^="item_taxable_value"]').each(function ()

// {

//    taxable += +$(this).val()

// });

        if (purpose_of_transaction == "Fixed Assets")

        {

            $("#ledger_table_body").find('input[name^="item_taxable"]').each(function ()

            {

                sub_total += +$(this).val()

            });

            $("#ledger_table_body").find('input[name^="item_tax_amount"]').each(function ()

            {

                tax += +$(this).val()

            });

            $("#ledger_table_body").find('input[name^="item_igst_amount"]').each(function ()

            {

                igst += +$(this).val()

            });

            $("#ledger_table_body").find('input[name^="item_cgst_amount"]').each(function ()

            {

                cgst += +$(this).val()

            });

            $("#ledger_table_body").find('input[name^="item_sgst_amount"]').each(function ()

            {

                sgst += +$(this).val()

            });

        } else

        {

// $('#tax_val').hide();

        }

        $("#ledger_table_body").find('input[name^="amount"]').each(function ()

        {

            grand_total += +$(this).val()

        });

        var final_grand_total = grand_total;

        $('#total_taxable_amount').val(sub_total.toFixed(2));

        $('#total_igst_amount').val(igst.toFixed(2));

        $('#total_cgst_amount').val(cgst.toFixed(2));

        $('#total_sgst_amount').val(sgst.toFixed(2));

        $('#total_grand_total').val(round_off(final_grand_total));

        $('#total_tax_amount').val(tax.toFixed(2));

        $('#totalSubTotal').text(sub_total.toFixed(2));

        $('#totalTaxAmount').text(tax.toFixed(2));

        $('#totalGrandTotal').text(round_off(final_grand_total));

    }

</script>

