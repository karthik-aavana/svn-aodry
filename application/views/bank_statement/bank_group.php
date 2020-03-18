<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<script type="text/javascript">
    function delete_id(id)
    {
        if (confirm('Sure To Remove This Record ?'))
        {
            window.location.href = '<?php echo base_url('bank_account/delete/'); ?>' + id;
        }
    }
</script>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> <!-- Dashboard -->Dashboard</a></li>
                <li class="active">Bank to Group
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
                        <h3 class="box-title">Bank to Group</h3>
                        <a class="pull-right">
                            <label class="form-check-label">
                                <input class="advance_search" type="checkbox" name="checkbox" value="advance_search" <?php
                                if ($this->session->userdata('advance_search') == 'true')
                                {
                                    echo "checked";
                                }
                                ?>> Advance Search</label>
                        </a>
                        <!--                        <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('bank_statement/add'); ?>">Add Bank Statement</a>
                        <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('bank_online'); ?>">Online Banking</a>         -->
                    </div>
                    <div class="box-body">
                        <div class="well">
                            <form action="<?php echo base_url('bank_statement/list_data'); ?>"  role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                                <div class="row">
                                    <div class="col-sm-3">
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
                                    <div class="col-sm-3" id="search_month">
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
                                    <div class="col-sm-3" id="from">
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
                                    <div class="col-sm-3" id="to">
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label></label>
                                            <button type="submit" class="form-control btn btn-info" id="search_submit">Get Statement</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs">
                                        <li class="nav active"><a class="btn btn-default" data-toggle="tab" href="#rawdata">Raw Data</a></li>
                                        <li class="nav"><a class="btn btn-default" data-toggle="tab" href="#categorized">Categorized</a></li>
                                        <li class="nav"><a class="btn btn-default" data-toggle="tab" href="#unknow_data">Suspense Data</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div id="myTabContent" class="tab-content mt-30">
                                <div class="tab-pane fade in active" id="rawdata">
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
                                        <tbody id="tbody_rawdata">
                                            <?php
                                            if (isset($data))
                                            {
                                                echo $data;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="categorized">
                                    <table id="log_datatable2" class="table table-bordered table-striped table-hover table-responsive">
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
                                        <tbody id="tbody_categorized">
<?php
if (isset($data2))
{
    echo $data2;
}
?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="unknow_data">
                                    <table id="log_datatable3" class="table table-bordered table-striped table-hover table-responsive">
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
                                        <tbody id="tbody_suspense">
<?php
if (isset($data3))
{
    echo $data3;
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
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('bank_statement/categorized-popup');
$this->load->view('bank_statement/split-popup');
$this->load->view('bank_statement/cat_service');
$this->session->set_userdata('advance_search', 'false');
?>
<script src="<?php echo base_url(); ?>assets/dist/js/jquery.backstretch.min.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/scripts.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
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
            var bank_ledger_id = $('#bank_ledger_id').val();
            var to_date = $('#to_date').val();
            var from_date = $('#from_date').val();
            var month = $('#month').val();
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
<script type="text/javascript">
    $(document).ready(function () {
        $("#log_datatable1").dataTable().fnDestroy();
        $('#log_datatable1').dataTable({
            "aaSorting": [],
            // Your other options here...
        });
        $("#log_datatable2").dataTable().fnDestroy();
        $('#log_datatable2').dataTable({
            "aaSorting": [],
            // Your other options here...
        });
        $("#log_datatable3").dataTable().fnDestroy();
        $('#log_datatable3').dataTable({
            "aaSorting": [],
            // Your other options here...
        });
        // $("#bank_ledger_id").change(function () {
        //     $.ajax({
        //         type:"POST",
        //         dataType: 'json',
        //         url:"<?php echo base_url('bank_statement/list_data3/') ?>",
        //         data:{bank_ledger_id:$("#bank_ledger_id").val()},
        //         success: function(data) {
        //             document.getElementById('tbody_rawdata').innerHTML=data[0];
        //             document.getElementById('tbody_categorized').innerHTML=data[1];
        //             document.getElementById('tbody_suspense').innerHTML=data[2];
        //         }
        //     });
        // });
    });
</script>
<!-- <script type="text/javascript">
$(function () {
$('#datetimepicker7, #datetimepicker6').datetimepicker({
showTodayButton: true,
useCurrent: false,
format: 'YYYY-MM-DD',
maxDate: new Date()
});
$("#datetimepicker6").on("dp.change", function (e) {
$('#datetimepicker7').data("DateTimePicker").minDate(e.date);
});
$("#datetimepicker7").on("dp.change", function (e) {
$('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
});
});
</script> -->
