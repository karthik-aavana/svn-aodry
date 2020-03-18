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
            <li class="active">Edit General Bill</li>
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
                        <span class="btn btn-default pull-right" id="sale_cancel" onclick="cancel('general_bill')">Back</span>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('general_bill/edit_general_bill'); ?>">
                            <?php foreach ($data as $row)
                            {
                                ?>
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="well">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="date"> Date<span class="validation-color">*</span></label>
                                                        <input type="text" style="background: #fff;" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?php echo $row->general_bill_date; ?>" readonly>

                                                        <input type="hidden" id="date_old" name="date_old" value="<?php echo $row->general_bill_date; ?>">



                                                        <input type="hidden" id="general_bill_id" name="general_bill_id" value="<?php echo $row->general_bill_id; ?>">


                                                        <span class="validation-color" id="err_invoice_date"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">

                                                    <div class="form-group">
                                                        <label for="reference_no">Invoice Number <span class="validation-color">*</span></label>
                                                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo $row->general_bill_invoice_number ?>"  <?php
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

                                                            <?php
                                                            foreach ($purpose as $key => $value)
                                                            {
                                                                ?>
                                                                <option value="<?= $value ?>" <?php if ($value == $row->purpose_of_transaction) echo "selected='selected'"; ?> ><?= $value ?></option>
        <?php
    }
    ?>

                                                        </select>
                                                        <span class="validation-color" id="err_purpose_of_transaction"></span>
                                                    </div>

                                                </div>
                                                <div class="col-sm-3" id="div_type_of_transaction">

                                                    <div class="form-group">
                                                        <label for="">Type of Transaction <span class="validation-color">*</span></label>
                                                        <select class="form-control select2" id="type_of_transaction" name="type_of_transaction" style="width: 100%;">
                                                            <option value="">Select</option>

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
                                                <div class="col-sm-3">

                                                    <div class="form-group">
                                                        <label ></label>Amount<span class="validation-color">*</span>
                                                        <input type="text" id="amount" name="amount" class="form-control">
                                                        <span class="validation-color" id="err_amount"></span>
                                                    </div>

                                                </div>
                                                <div class="col-sm-3">

                                                    <div class="form-group">
                                                        <label for="">Mode of Payment<span class="validation-color">*</span></label>

                                                        <select class="form-control select2" id="mode_of_payment" name="mode_of_payment" style="width: 100%;">

                                                        </select>
                                                        <span class="validation-color" id="err_mode_of_payment"></span>
                                                    </div>

                                                </div>


                                                <div class="col-sm-3" id="div_hide" hidden="true">

                                                    <div class="form-group">
                                                        <label id="label_change"></label><span class="validation-color">*</span>
                                                        <a href="" class="pull-right create_modal" id="create_new" data-toggle="modal" >Create New</a>

                                                        <select class="form-control select2" id="from_to" name="from_to" style="width: 100%;">

                                                        </select>
                                                        <span class="validation-color" id="err_from_to"></span>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>
                                        <div class="well">
                                            <div class="row">

                                                <div class="col-sm-3">

                                                    <div class="form-group">
                                                        <label for="account_number">From Account<span class="validation-color">*</span></label>
                                                        <input type="hidden" name="from_acc_id" id="from_acc_id">
                                                        <input type="text" class="form-control" id="from_acc" name="from_acc" value="" readonly="true">
                                                        <span class="validation-color" id="err_from_acc"><?php echo form_error('from_acc'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="balance">To Account<span class="validation-color">*</span></label>
                                                        <input type="hidden" name="to_acc_id" id="to_acc_id">
                                                        <input type="text" class="form-control" id="to_acc" name="to_acc" value="<?php echo set_value('balance'); ?>"  readonly="true">
                                                        <span class="validation-color" id="err_to_acc"><?php echo form_error('to_acc'); ?></span>
                                                    </div>

                                                </div>


                                            </div>

                                        </div>
<?php } ?>
                                    <!-- Hidden Field-->

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

                                    <!-- Hidden Field-->
                                    <div class="col-sm-12">
                                        <div class="box-footer">
                                            <button type="submit" id="general_bill_submit" class="btn btn-info">Add</button>
                                            <span class="btn btn-default" id="receipt_cancel" onclick="cancel('general_bill')">Cancel</span>
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
                                    </form>
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


                $("#purpose_of_transaction").change(function (event)
                {
                    $('#type_of_transaction').empty();
                    $("#div_hide").hide();
                    $("#from_acc").val("");
                    $("#to_acc").val("");
                    $('#from_to').empty();
                    $('#mode_of_payment').empty();

                    var purpose_of_transaction = $('#purpose_of_transaction').val();
                    if (purpose_of_transaction == null || purpose_of_transaction == "")
                    {
                        $('#err_purpose_of_transaction').text("Please Select Purpose of Transaction.");
                        return false;
                    } else
                    {
                        $('#err_purpose_of_transaction').text("");
                    }
                    if (purpose_of_transaction != "Indirect Income")
                    {

                        $("#div_type_of_transaction").show();
                    } else
                    {
                        $("#div_type_of_transaction").hide();
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
                        $('#mode_of_payment').append('<option value="">Select</option>');
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
                        var type_of_transaction_arr = new Array("Loan Borrowed", "Loan Paid", "Instalment or EMI Paid");


                    } else if (purpose_of_transaction == "Advance")
                    {
                        var type_of_transaction_arr = new Array("Advance Taken", "Advance Given", "Payment of Advance Taken", "Receipt of Advance Given");
                    } else if (purpose_of_transaction == "Bank to Bank")
                    {
                        var type_of_transaction_arr = new Array("Amount Deposited to Bank", "Amount Transfer From Bank");
                    } else if (purpose_of_transaction == "Capital")
                    {
                        var type_of_transaction_arr = new Array("Capital Invested", "Additional Capital Invested", "Capital Withdrawn");
                    } else if (purpose_of_transaction == "Indirect Income")
                    {
                        var type_of_transaction_arr = new Array();
                        $("#label_change").text("Type of Income");
                        $("#div_hide").show();

                        ledger_data(function (ledgers)
                        {

                            $('#from_to').append('<option value="">Select</option>');
                            for (i = 0; i < ledgers.length; i++)
                            {

                                $('#from_to').append('<option value="' + ledgers[i].ledger_id + '">' + ledgers[i].ledger_title + '</option>');
                            }

                        });

                    }


                    $('#type_of_transaction').append('<option value="">Select</option>');
                    for (i = 0; i < type_of_transaction_arr.length; i++)
                    {


                        $('#type_of_transaction').append('<option value="' + type_of_transaction_arr[i] + '">' + type_of_transaction_arr[i] + '</option>');
                    }




                }).change();

                $("#type_of_transaction").change(function (event)
                {
                    $("#from_acc").val("");
                    $("#to_acc").val("");
                    $('#from_to').empty();

                    var type_of_transaction = $('#type_of_transaction').val();

                    if (type_of_transaction == null || type_of_transaction == "")
                    {
                        $('#err_type_of_transaction').text("Please Select Type of Transaction.");
                        return false;
                    } else
                    {
                        $('#err_type_of_transaction').text("");
                    }


                    $("#div_hide").show();


                    if (type_of_transaction == "cash deposit in bank" || type_of_transaction == "cash withdrawal from bank")
                    {
                        if (type_of_transaction == "cash deposit in bank")
                        {
                            $("#label_change").text("Name the deposited bank");
                        } else if (type_of_transaction == "cash withdrawal from bank")
                        {
                            $("#label_change").text("Name the withdrawal bank");
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
                        } else if (type_of_transaction == "cash payment")
                        {

                            $("#label_change").text("Paid To");
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
                        } else if (type_of_transaction == "Deposit Withdraw")
                        {
                            $("#label_change").text("Deposited Withdraw");
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
                        if (type_of_transaction == "Fixed Asset Purchase")
                        {
                            $("#label_change").text("Purchase Of ");
                        } else if (type_of_transaction == "Fixed Asset Sold or Disposed")
                        {
                            $("#label_change").text("Sale/Disposal");
                        }

                        ledger_data(function (ledgers)
                        {

                            $('#from_to').append('<option value="">Select</option>');
                            for (i = 0; i < ledgers.length; i++)
                            {

                                $('#from_to').append('<option value="' + ledgers[i].ledger_id + '">' + ledgers[i].ledger_title + '</option>');
                            }

                        });

                    } else if (type_of_transaction == "Tax Receivable" || type_of_transaction == "Tax Payable")
                    {
                        if (type_of_transaction == "Tax Receivable")
                        {
                            $("#label_change").text("Taxes Received as");
                        } else if (type_of_transaction == "Tax Payable")
                        {
                            $("#label_change").text("Payment of");
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
                        } else if (type_of_transaction == "Investment Withdraw / Sold / Redeem / Mature")
                        {
                            $("#label_change").text("Investment withdrawn/sold/redeem/mature");
                        }

                        ledger_data(function (ledgers)
                        {

                            $('#from_to').append('<option value="">Select</option>');
                            for (i = 0; i < ledgers.length; i++)
                            {

                                $('#from_to').append('<option value="' + ledgers[i].ledger_id + '">' + ledgers[i].ledger_title + '</option>');
                            }

                        });

                    } else if (type_of_transaction == "Loan Borrowed" || type_of_transaction == "Loan Paid" || type_of_transaction == "Instalment or EMI Paid")
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

                        if (type_of_transaction == "Loan Borrowed")
                        {
                            $("#label_change").text("From whom");
                        } else if (type_of_transaction == "Loan Paid" || type_of_transaction == "Instalment or EMI Paid")
                        {
                            $("#label_change").text("For whom");
                        }
                    } else if (type_of_transaction == "Advance Taken" || type_of_transaction == "Advance Given" || type_of_transaction == "Payment of Advance Taken" || type_of_transaction == "Receipt of Advance Given")
                    {

                        if (type_of_transaction == "Advance Taken")
                        {
                            $("#label_change").text("Advance Taken From");
                        } else if (type_of_transaction == "Advance Given")
                        {
                            $("#label_change").text("Advance Given To");
                        } else if (type_of_transaction == "Payment of Advance Taken")
                        {
                            $("#label_change").text("Advance Refund To");
                        } else if (type_of_transaction == "Receipt of Advance Given")
                        {
                            $("#label_change").text("Advance Repaid By");
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
                        } else if (type_of_transaction == "Capital Withdrawn")
                        {
                            $("#label_change").text("To Whom");
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
                    } else if (type_of_transaction == "Amount Deposited to Bank" || type_of_transaction == "Amount Transfer From Bank")
                    {
                        $("#label_change").text("Amount Transfer From Bank");
                        bank(function (bank_accounts)
                        {
                            $('#from_to').append('<option value="">Select</option>');
                            for (i = 0; i < bank_accounts.length; i++)
                            {

                                $('#from_to').append('<option value="' + bank_accounts[i].ledger_id + '">' + bank_accounts[i].ledger_title + '</option>');
                            }


                        });

                    }

                }).change();



                $("#from_to").change(function (event)
                {
                    var from_to = $("option:selected", this).text();
                    var from_to_id = $('#from_to').val();

                    $("#from_to_value").val(from_to);
                    var type_of_transaction = $('#type_of_transaction').val();
                    var mode_of_payment = $("option:selected", '#mode_of_payment').text();
                    var purpose_of_transaction = $('#purpose_of_transaction').val();

                    if (from_to_id == null || from_to_id == "")
                    {
                        $('#err_from_to').text("Please Select");
                        return false;
                    } else
                    {
                        $('#err_from_to').text("");
                    }
                    if (type_of_transaction == "cash deposit in bank" || type_of_transaction == "cash payment" || type_of_transaction == "Deposit Made" || type_of_transaction == "Fixed Asset Purchase" || type_of_transaction == "Tax Payable" || type_of_transaction == "Investment Made" || type_of_transaction == "Payment of Advance Taken" || type_of_transaction == "Advance Given" || type_of_transaction == "Amount Deposited to Bank" || type_of_transaction == "Amount Transfer From Bank" || type_of_transaction == "Capital Withdrawn" || type_of_transaction == "Loan Paid" || type_of_transaction == "Instalment or EMI Paid")
                    {

                        if (from_to != "Select")
                        {
                            $("#from_acc").val(from_to);
                            $("#from_acc_id").val(from_to_id);
                            //$("#to_acc").val(mode_of_payment);
                        } else
                        {
                            $("#from_acc").val("");
                            $("#from_acc_id").val("");
                        }
                    } else if (type_of_transaction == "cash withdrawal from bank" || type_of_transaction == "cash receipt" || type_of_transaction == "Deposit Withdraw" || type_of_transaction == "Fixed Asset Sold or Disposed" || type_of_transaction == "Tax Receivable" || purpose_of_transaction == "Indirect Income" || type_of_transaction == "Investment Withdraw / Sold / Redeem / Mature" || type_of_transaction == "Advance Taken" || type_of_transaction == "Receipt of Advance Given" || type_of_transaction == "Capital Invested" || type_of_transaction == "Additional Capital Invested" || type_of_transaction == "Loan Borrowed")
                    {
                        if (from_to != "Select")
                        {
                            $("#to_acc").val(from_to);
                            $("#to_acc_id").val(from_to_id);
                            //$("#from_acc").val(mode_of_payment);
                        } else
                        {
                            $("#to_acc").val("");
                            $("#to_acc_id").val("");
                        }
                    }


                }).change();

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

                    $("#mode_of_payment_text").val(mode_of_payment);//to store text value of mode of payment

                    if (type_of_transaction == "cash deposit in bank" || type_of_transaction == "cash payment" || type_of_transaction == "Deposit Made" || type_of_transaction == "Fixed Asset Purchase" || type_of_transaction == "Tax Payable" || type_of_transaction == "Investment Made" || type_of_transaction == "Advance Given" || type_of_transaction == "Payment of Advance Taken" || type_of_transaction == "Amount Deposited to Bank" || type_of_transaction == "Amount Transfer From Bank" || type_of_transaction == "Capital Withdrawn" || type_of_transaction == "Loan Paid" || type_of_transaction == "Instalment or EMI Paid")
                    {
                        if (mode_of_payment != "Select")
                        {
                            // $("#from_acc").val(from_to);
                            $("#to_acc").val(mode_of_payment);

                            $("#to_acc_id").val(mode_of_payment_id);
                        } else
                        {
                            $("#to_acc").val("");
                            $("#to_acc_id").val("");
                        }
                    } else if (type_of_transaction == "cash withdrawal from bank" || type_of_transaction == "cash receipt" || type_of_transaction == "Deposit Withdraw" || type_of_transaction == "Fixed Asset Sold or Disposed" || type_of_transaction == "Tax Receivable" || purpose_of_transaction == "Indirect Income" || type_of_transaction == "Investment Withdraw / Sold / Redeem / Mature" || type_of_transaction == "Advance Taken" || type_of_transaction == "Receipt of Advance Given" || type_of_transaction == "Capital Invested" || type_of_transaction == "Additional Capital Invested" || type_of_transaction == "Loan Borrowed")
                    {
                        if (mode_of_payment != "Select")
                        {
                            // $("#to_acc").val(from_to);
                            $("#from_acc").val(mode_of_payment);
                            $("#from_acc_id").val(mode_of_payment_id);
                        } else
                        {
                            $("#from_acc").val("");
                            $("#from_acc_id").val("");
                        }
                    }

                }).change();

                $("#amount").on("blur", function (event)
                {

                    var amount = $('#amount').val();
                    if (amount != "")
                    {
                        if (!amount.match(price_regex)) {
                            $('#err_amount').text(" Please Enter Valid Amount.");
                            return false;
                        } else {
                            $("#err_amount").text("");
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
                    } else if (type_of_transaction == "Amount Deposited to Bank" | type_of_transaction == "Amount Transfer From Bank")
                    {
                        $("#open_bank_modal").click();
                    } else if (type_of_transaction == "Advance Given" || type_of_transaction == "Receipt of Advance Given")
                    {
                        $("#open_supplier_modal").click();
                    } else if (type_of_transaction == "Loan Borrowed" || type_of_transaction == "Loan Paid" || type_of_transaction == "Instalment or EMI Paid")
                    {
                        $("#open_item_select_modal").click();
                    } else if (type_of_transaction == "Capital Invested" || type_of_transaction == "Additional Capital Invested" || type_of_transaction == "Capital Withdrawn")
                    {
                        $("#open_director_org_modal").click();
                    } else
                    {
                        $("#open_create_new_modal").click();
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
                    if (purpose_of_transaction != "Indirect Income")
                    {
                        if (type_of_transaction == null || type_of_transaction == "") {
                            $("#err_type_of_transaction").text("Please Select Type of Transaction.");
                            return false;
                        } else {
                            $("#err_type_of_transaction").text("");
                        }
                    }

                    if (amount == null || amount == "") {
                        $("#err_amount").text("Please Enter Amount.");
                        return false;
                    } else {
                        $("#err_type_of_transaction").text("");
                    }

                    if (!amount.match(price_regex)) {
                        $('#err_amount').text("Please Enter Valid Amount.");
                        return false;
                    } else {
                        $("#err_amount").text("");
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

            </script>



