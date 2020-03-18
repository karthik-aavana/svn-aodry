<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');

?>

<script type="text/javascript">

// function delete_id(id)

// {

//     if (confirm('Sure To Remove This Record ?'))

//     {

//         window.location.href = '<?php echo base_url('bank_account/delete/'); ?>' + id;

//     }

// }

</script>

<div class="content-wrapper">

    <section class="content-header">

        <h5>

            <ol class="breadcrumb">

                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> <!-- Dashboard -->Dashboard</a></li>

                <li class="active">Bank Reconciliation</li>

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

                        <h3 class="box-title">Bank Reconciliation</h3>

                        <a class="btn btn-sm btn-default pull-right" id="cancel" onclick="cancel('bank_reconciliation/view_vouchers')">View Vouchers</a>

                    </div>

                    <div class="box-body">

                        <div class="row">

                            <div class="col-md-12">

                                <form action="<?php echo base_url('bank_reconciliation/list_data'); ?>" role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8">

                                    <div class="well">

                                        <div class="row">

                                            <div class="col-sm-2">

                                                <div class="sales">

                                                    <div class="form-group">

                                                        <label for="Users">Users<span class="validation-color">*</span></label>

                                                        <select multiple="" class="form-control select2" id="user_id" name="user_id[]" style="width: 100%;">

                                                            <!-- <option value="">Select</option> -->

                                                            <?php

                                                            if (isset($post_data['user_id']))

                                                            {

                                                                $selected_users = $post_data['user_id'];

                                                            }

                                                            else

                                                            {

                                                                $selected_users = array();

                                                            }

                                                            foreach ($users as $key => $row)

                                                            {

                                                                ?>

                                                                <option value="<?= $row->id ?>" <?php if (in_array($row->id, $selected_users)) echo "selected = 'selected' "; ?> ><?php echo $row->first_name . ' ' . $row->last_name; ?></option>

                                                                <?php

                                                            }

                                                            ?>

                                                        </select>

                                                        <span class="validation-color" id="err_user_id"><?php echo form_error('Users'); ?></span>

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-sm-2">

                                                <div class="sales">

                                                    <div class="form-group">

                                                        <label for="Bank Account">Bank Account<span class="validation-color">*</span></label>

                                                        <select multiple="" class="form-control select2" id="bank_ledger_id" name="bank_ledger_id[]" style="width: 100%;">

                                                            <option value="">Select</option>

                                                            <?php

                                                            if (isset($post_data['bank_ledger_id']))

                                                            {

                                                                $selected_bank = $post_data['bank_ledger_id'];

                                                            }

                                                            else

                                                            {

                                                                $selected_bank = array();

                                                            }

                                                            foreach ($bank as $key => $row)

                                                            {

                                                                ?>

                                                                <option value="<?= $row->ledger_id ?>" <?php if (in_array($row->ledger_id, $selected_bank)) echo "selected = 'selected' "; ?> ><?= $row->ledger_title ?></option>

                                                                <?php

                                                            }

                                                            ?>

                                                        </select>

                                                        <span class="validation-color" id="err_bank_account"><?php echo form_error('Bank Account'); ?></span>

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-sm-4" id="search_month">

                                                <div class="form-group">

                                                    <label for="From">Month<span class="validation-color">*</span></label>

                                                    <select class="form-control select2" id="month" name="month">

                                                        <?php

                                                        $month = 0;

                                                        if (isset($post_data['to_date']) && $post_data['to_date'] != "")

                                                        {

                                                            $month = date('m', strtotime($post_data['to_date']));

                                                        }

                                                        ?>

                                                        <option value="">Select</option>

                                                        <option value="01" <?php if ($month == '01') echo "selected = 'selected' "; ?>>

                                                            January</option>

                                                        <option value="02" <?php if ($month == '02') echo "selected = 'selected' "; ?>>

                                                            February</option>

                                                        <option value="03" <?php if ($month == '03') echo "selected = 'selected' "; ?>>

                                                            March</option>

                                                        <option value="04" <?php if ($month == '04') echo "selected = 'selected' "; ?>>

                                                            April</option>

                                                        <option value="05" <?php if ($month == '05') echo "selected = 'selected' "; ?>>

                                                            May</option>

                                                        <option value="06" <?php if ($month == '06') echo "selected = 'selected' "; ?>>

                                                            June</option>

                                                        <option value="07" <?php if ($month == '07') echo "selected = 'selected' "; ?>>

                                                            July</option>

                                                        <option value="08" <?php if ($month == '08') echo "selected = 'selected' "; ?>>

                                                            August</option>

                                                        <option value="09" <?php if ($month == '09') echo "selected = 'selected' "; ?>>

                                                            September</option>

                                                        <option value="10" <?php if ($month == '10') echo "selected = 'selected' "; ?>>

                                                            October</option>

                                                        <option value="11" <?php if ($month == '11') echo "selected = 'selected' "; ?>>

                                                            November</option>

                                                        <option value="12" <?php if ($month == '12') echo "selected = 'selected' "; ?>>

                                                            December</option>

                                                    </select>

                                                    <span class="validation-color" id="err_month"><?php echo form_error('month'); ?></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-2" id="from">

                                                <div class="form-group">

                                                    <label for="From">From<span class="validation-color">*</span></label>

                                                    <div class='input-group date' id='datetimepicker6'>

                                                        <input class="form-control datepicker" id="from_date" name="from_date" value="<?php if (isset($post_data['from_date']) && $post_data['from_date'] != "") echo $post_data['from_date']; ?>">

                                                        <span class="input-group-addon">

                                                            <span class="glyphicon glyphicon-calendar"></span>

                                                        </span>

                                                    </div>

                                                    <span class="validation-color" id="err_from_date"><?php echo form_error('from_date'); ?></span>

                                                </div>

                                            </div>

                                            <div class="col-sm-2" id="to">

                                                <div class="form-group">

                                                    <label for="To">To<span class="validation-color">*</span></label>

                                                    <div class='input-group date' id='datetimepicker7'>

                                                        <input class="form-control datepicker" id="to_date" name="to_date" value="<?php if (isset($post_data['to_date']) && $post_data['to_date'] != "") echo $post_data['to_date']; ?>">

                                                        <span class="input-group-addon">

                                                            <span class="glyphicon glyphicon-calendar"></span>

                                                        </span>

                                                    </div>

                                                    <span class="validation-color" id="err_to_date"><?php echo form_error('to_date'); ?></span>

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="form-group mt-30">

                                                    <div class="form-check-label">

                                                        <input class="advance_search" type="checkbox" name="checkbox" value="advance_search" <?php

                                                        if ($this->session->userdata('advance_search') == 'true')

                                                        {

                                                            echo "checked";

                                                        }

                                                        ?>> Advance Search</div>

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="form-group">

                                                    <label></label>

                                                    <button type="submit" class="form-control btn btn-info" id="search_submit">Search</button>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </form>

                            </div>



                            <div class="box-body" id="rawdata">

                                <table id="log_datatable" class="table table-bordered table-striped table-hover table-responsive">

                                    <thead>

                                        <tr>

                                            <th>Voucher Date</th>

                                            <th>Voucher Number</th>

                                            <th>Ledgers</th>

                                            <!-- <th>Reference Number</th> -->

                                            <!-- <th>Amount</th> -->

                                            <th>Actual Amount</th>

                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody id="tbody_rawdata">

                                        <?php

                                        if (isset($receipt_voucher_data))

                                        {

                                            foreach ($receipt_voucher_data as $key => $value)

                                            {

                                                ?>

                                                <tr>

                                                    <td><?= $value->voucher_date ?></td>

                                                    <td><?= $value->voucher_number ?></td>

                                                    <td>Customer - <?= $value->customer_name ?></td>

                                                    <!-- <td><?= $value->reference_number ?></td> -->

                                                    <td><?= $value->currency_converted_amount ?></td>

                                                    <td><a href="" title="Move to categorized" data-voucher_id="<?= $value->receipt_id ?>" data-voucher_type="receipt_voucher" data-voucher_amount="<?= $value->currency_converted_amount ?>" data-toggle="modal" data-target="#categorized_voucher" class="categorized_voucher"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td>

                                                </tr>

                                                <?php

                                            }

                                        }

                                        ?>

                                        <?php

                                        if (isset($receipt_voucher_data1))

                                        {

                                            foreach ($receipt_voucher_data1 as $key => $value)

                                            {

                                                ?>

                                                <tr>

                                                    <td><?= $value->voucher_date ?></td>

                                                    <td><?= $value->voucher_number ?></td>

                                                    <td>Ledger - <?= $value->customer_name ?></td>

                                                    <!-- <td><?= $value->reference_number ?></td> -->

                                                    <td><?= $value->currency_converted_amount ?></td>

                                                    <td><a href="" title="Move to categorized" data-voucher_id="<?= $value->receipt_id ?>" data-voucher_type="receipt_voucher" data-voucher_amount="<?= $value->currency_converted_amount ?>" data-toggle="modal" data-target="#categorized_voucher" class="categorized_voucher"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td>

                                                </tr>

                                                <?php

                                            }

                                        }

                                        ?>

                                        <?php

                                        if (isset($advance_voucher_data))

                                        {

                                            foreach ($advance_voucher_data as $key => $value)

                                            {

                                                ?>

                                                <tr>

                                                    <td><?= $value->voucher_date ?></td>

                                                    <td><?= $value->voucher_number ?></td>

                                                    <td>Customer - <?= $value->customer_name ?></td>

                                                    <!-- <td><?= $value->reference_number ?></td> -->

                                                    <td><?= $value->currency_converted_amount ?></td>

                                                    <td><a href="" title="Move to categorized" data-voucher_id="<?= $value->advance_id ?>" data-voucher_type="advance_voucher" data-voucher_amount="<?= $value->currency_converted_amount ?>" data-toggle="modal" data-target="#categorized_voucher" class="categorized_voucher"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td>

                                                </tr>

                                                <?php

                                            }

                                        }

                                        ?>

                                        <?php

                                        if (isset($refund_voucher_data))

                                        {

                                            foreach ($refund_voucher_data as $key => $value)

                                            {

                                                ?>

                                                <tr>

                                                    <td><?= $value->voucher_date ?></td>

                                                    <td><?= $value->voucher_number ?></td>

                                                    <td>Customer - <?= $value->customer_name ?></td>

                                                    <!-- <td><?= $value->reference_number ?></td> -->

                                                    <td><?= $value->currency_converted_amount ?></td>

                                                    <td><a href="" title="Move to categorized" data-voucher_id="<?= $value->refund_id ?>" data-voucher_type="refund_voucher" data-voucher_amount="<?= $value->currency_converted_amount ?>" data-toggle="modal" data-target="#categorized_voucher" class="categorized_voucher"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td>

                                                </tr>

                                                <?php

                                            }

                                        }

                                        ?>

                                        <?php

                                        if (isset($payment_voucher_data))

                                        {

                                            foreach ($payment_voucher_data as $key => $value)

                                            {

                                                ?>

                                                <tr>

                                                    <td><?= $value->voucher_date ?></td>

                                                    <td><?= $value->voucher_number ?></td>

                                                    <td>Supplier - <?= $value->supplier_name ?></td>

                                                    <!-- <td><?= $value->reference_number ?></td> -->

                                                    <td><?= $value->currency_converted_amount ?></td>

                                                    <td><a href="" title="Move to categorized" data-voucher_id="<?= $value->payment_id ?>" data-voucher_type="payment_voucher" data-voucher_amount="<?= $value->currency_converted_amount ?>" data-toggle="modal" data-target="#categorized_voucher" class="categorized_voucher"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td>

                                                </tr>

                                                <?php

                                            }

                                        }

                                        ?>

                                        <?php

                                        if (isset($payment_voucher_data1))

                                        {

                                            foreach ($payment_voucher_data1 as $key => $value)

                                            {

                                                ?>

                                                <tr>

                                                    <td><?= $value->voucher_date ?></td>

                                                    <td><?= $value->voucher_number ?></td>

                                                    <td>Ledger - <?= $value->supplier_name ?></td>

                                                    <!-- <td><?= $value->reference_number ?></td> -->

                                                    <td><?= $value->currency_converted_amount ?></td>

                                                    <td><a href="" title="Move to categorized" data-voucher_id="<?= $value->payment_id ?>" data-voucher_type="payment_voucher" data-voucher_amount="<?= $value->currency_converted_amount ?>" data-toggle="modal" data-target="#categorized_voucher" class="categorized_voucher"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td>

                                                </tr>

                                                <?php

                                            }

                                        }

                                        ?>

                                        <?php

                                        if (isset($contra_voucher_data))

                                        {

                                            foreach ($contra_voucher_data as $key => $value)

                                            {

                                                ?>

                                                <tr>

                                                    <td><?= $value->voucher_date ?></td>

                                                    <td><?= $value->voucher_number ?></td>

                                                    <td>Ledger - <?= $value->ledger_title ?></td>

                                                    <!-- <td><?= $value->reference_number ?></td> -->

                                                    <td><?= $value->currency_converted_amount ?></td>

                                                    <td><a href="" title="Move to categorized" data-voucher_id="<?= $value->contra_voucher_id ?>" data-voucher_type="contra_voucher" data-voucher_amount="<?= $value->currency_converted_amount ?>" data-toggle="modal" data-target="#categorized_voucher" class="categorized_voucher"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td>

                                                </tr>

                                                <?php

                                            }

                                        }

                                        ?>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>

<?php

$this->load->view('layout/footer');

$this->session->set_userdata('advance_search', 'false');

?>

<div id="categorized_voucher" class="modal fade" role="dialog" data-backdrop="static">

    <div class="modal-dialog modal-lg" style="width:80%;">

        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">x</button>

                <h4 class="modal-title" style="float: left;">Move to Categorized</h4>

            </div>

            <div class="modal-body">

                <div class="box-body" id="categorized_data">

                    <table id="log_datatable1" class="table table-bordered table-striped table-hover table-responsive">

                        <thead>

                            <tr>

                                <th>Date</th>

                                <th>Particulars or<br/> Narration</th>

                                <th>Cheque No or<br/> Reference No</th>

                                <th>Withdrawal (DR)</th>

                                <th>Deposit (CR)</th>

                                <th>Closing Balance</th>

                                <th>Action</th>

                            </tr>

                        </thead>

                        <tbody id="tbody_categorized"></tbody>

                    </table>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                <!-- <button id="categorized_submit" type="button" class="btn btn-primary" data-dismiss="modal">Categorized</button> -->

            </div>

        </div>

    </div>

</div>

<!-- <script src="<?php echo base_url(); ?>assets/dist/js/jquery.backstretch.min.js"></script>

<script src="<?php echo base_url(); ?>assets/dist/js/scripts.js"></script> -->

<script type="text/javascript">

    $(document).ready(function () {

        $(".categorized_voucher").click(function () {


            var voucher_id = $(this).data('voucher_id');

            var voucher_type = $(this).data('voucher_type');

            var voucher_amount = $(this).data('voucher_amount');

            var bank_ledger_id = $('#bank_ledger_id').val();

// var user_id         = $('#user_id').val();

            var from_date = $('#from_date').val();

            var to_date = $('#to_date').val();

            $.ajax({

                type: "POST",

                dataType: 'html',

                url: "<?php echo base_url('bank_reconciliation/get_bank_statement/') ?>",

                data:

                        {

                            voucher_id: voucher_id,

                            voucher_type: voucher_type,

                            voucher_amount: voucher_amount,

                            bank_ledger_id: bank_ledger_id,

// user_id         :   user_id,

                            from_date: from_date,

                            to_date: to_date

                        },

                success: function (data)

                {

                    $('#tbody_categorized').html('');

                    document.getElementById('tbody_categorized').innerHTML = data;

                    $('#log_datatable1').DataTable();

                }

            });

        });

        if ($(".advance_search").prop("checked") == true) {

            $('#to').show();

            $('#from').show();

            $('#search_month').hide();

        } else if ($(".advance_search").prop("checked") == false) {

            $('#to').hide();

            $('#from').hide();

            $('#search_month').show();

        }

        $(".advance_search").change(function () {

            if ($(this).prop("checked") == true) {

                $('#to').show();

                $('#from').show();

                $('#search_month').hide();

            } else if ($(this).prop("checked") == false) {

                $('#to').hide();

                $('#from').hide();

                $('#search_month').show();

            }

        });

        $("#search_submit").click(function () {

            var user_id = $('#user_id').val();

            var bank_ledger_id = $('#bank_ledger_id').val();

            var to_date = $('#to_date').val();

            var from_date = $('#from_date').val();

            var month = $('#month').val();

            if (user_id == "" || user_id == null) {

                $('#err_user_id').text("Please Select Users");

                return false;

            } else {

                $('#err_user_id').text('');

            }

            if (bank_ledger_id == "" || bank_ledger_id == null) {

                $('#err_bank_account').text("Please Select Bank Account");

                return false;

            } else {

                $('#err_bank_account').text('');

            }

            if ((from_date == "" || from_date == null) && $("#from").is(":visible")) {

                $('#err_from_date').text("Please Choose From Date");

                return false;

            } else {

                $('#err_from_date').text('');

            }

            if ((to_date == "" || to_date == null) && $("#to").is(":visible")) {

                $('#err_to_date').text("Please Choose To Date");

                return false;

            } else {

                $('#err_to_date').text('');

            }

            if ((month == "" || month == null) && $("#search_month").is(":visible")) {

                $('#err_month').text("Please Select Month");

                return false;

            } else {

                $('#err_month').text('');

            }

            if ($("#from").is(":visible"))

            {

                $('#month').val('');

            }

            if ($("#search_month").is(":visible"))

            {

                $('#from_date').val('');

                $('#to_date').val('');

            }

        });

    });

</script>

