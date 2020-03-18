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
                <li><a href="<?php echo base_url('bank_statement'); ?>">View Bank Statement</a></li>
                <li class="active">Get Bank Statement
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
                        <h3 class="box-title">Get Bank Statement</h3>
                        <!-- <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('bank_statement/add'); ?>">Add Bank Statement</a> -->
                        <!-- <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('bank_online'); ?>">Online Banking</a>          -->
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="<?php echo base_url('bank_statement/add_bank_statement'); ?>"  role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                                    <div class="well">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="sales">
                                                    <div class="form-group">
                                                        <label for="Bank Account">Bank Account<span class="validation-color">*</span></label>
                                                        <a href="" data-toggle="modal" data-target="#bank_account_modal" class="pull-right">+ Add Account</a>
                                                        <input type="hidden" id="paying_by1" name="paying_by1">
                                                        <select class="form-control select2" id="bank_ledger_id" name="bank_ledger_id">
                                                            <option value="">Select</option>
                                                            <?php
                                                            foreach ($bank as $key => $row)
                                                            {
                                                                ?>
                                                                <option value="<?= $row->ledger_id ?>" <?php if (isset($bank_ledger) && $bank_ledger == $row->ledger_id) echo "selected = 'selected' "; ?> ><?= $row->ledger_title ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                        <span class="validation-color" id="err_bank_account"><?php echo form_error('Bank Account'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4" id="search_month">
                                                <div class="form-group">
                                                    <label for="From">Month<span class="validation-color">*</span></label>
                                                    <select class="form-control select2" id="month" name="month">
                                                        <option value="">Select</option>
                                                        <option value="1" <?php if (isset($file_month) && $file_month == '1') echo "selected = 'selected' "; ?>>
                                                            Jan</option>
                                                        <option value="2" <?php if (isset($file_month) && $file_month == '2') echo "selected = 'selected' "; ?>>
                                                            Feb</option>
                                                        <option value="3" <?php if (isset($file_month) && $file_month == '3') echo "selected = 'selected' "; ?>>
                                                            March</option>
                                                        <option value="4" <?php if (isset($file_month) && $file_month == '4') echo "selected = 'selected' "; ?>>
                                                            April</option>
                                                        <option value="5" <?php if (isset($file_month) && $file_month == '5') echo "selected = 'selected' "; ?>>
                                                            May</option>
                                                        <option value="6" <?php if (isset($file_month) && $file_month == '6') echo "selected = 'selected' "; ?>>
                                                            June</option>
                                                        <option value="7" <?php if (isset($file_month) && $file_month == '7') echo "selected = 'selected' "; ?>>
                                                            July</option>
                                                        <option value="8" <?php if (isset($file_month) && $file_month == '8') echo "selected = 'selected' "; ?>>
                                                            Aug</option>
                                                        <option value="9" <?php if (isset($file_month) && $file_month == '9') echo "selected = 'selected' "; ?>>
                                                            Sep</option>
                                                        <option value="10" <?php if (isset($file_month) && $file_month == '10') echo "selected = 'selected' "; ?>>
                                                            Oct</option>
                                                        <option value="11" <?php if (isset($file_month) && $file_month == '11') echo "selected = 'selected' "; ?>>
                                                            Nov</option>
                                                        <option value="12" <?php if (isset($file_month) && $file_month == '12') echo "selected = 'selected' "; ?>>
                                                            Dec</option>
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
                                                <div class="form-group">
                                                    <label></label><br>
                                                    <label class="form-check-label"><input class="advance_search" type="checkbox" name="checkbox" value="advance_search" <?php
                                                        if ($this->session->userdata('advance_search') == 'true')
                                                        {
                                                            echo "checked";
                                                        }
                                                        ?>> Advance Search</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="file">File<span class="validation-color">*</span></label>
                                                    <input class="form-control" id="uploadfilename" name="uploadfilename" type="file">
                                                    <span class="validation-color" id="err_uploadfilename"><?php echo form_error('uploadfilename'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label></label>
                                                    <input type="submit" class="form-control btn btn-sm btn-info pull-right" id="search_submit" value="Get Bank Statement">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="modal_action" name="modal_action" value="<?php
               if (isset($modal))
               {
                   echo $modal;
               }
               ?>">
        <input type="hidden" id="mismatch_action" name="mismatch_action" value="<?php
        if (isset($mismatch_modal))
        {
            echo $mismatch_modal;
        }
        ?>">
        <input type="hidden" id="minstat_action" name="minstat_action" value="<?php
        if (isset($minimum_stat))
        {
            echo $minimum_stat;
        }
        ?>">
        <input type="button" data-toggle="modal" data-target="#password_protected" id="modal_open" name="modal_open" value="">
        <input type="button" data-toggle="modal" data-target="#mismatch_account" id="mismatch_acc" name="mismatch_acc" value="">
        <input type="button" data-toggle="modal" data-target="#minimum_statement" id="minimum_stat" name="minimum_stat" value="">
    </section>
</div>

<div id="password_protected" class="modal fade" role="dialog" data-backdrop="static" >
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <form class="password-protected" action="<?= base_url() ?>bank_statement/password_protected" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">x</button>
                    <h4 class="modal-title" style="float: left;">Password Protected</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="pwd">Password:</label>
                        <input type="password" class="form-control pwd"  placeholder="Enter password" name="pwd" required>
                    </div>
                    <input type="hidden" name="fileid" value="<?= $fileid; ?>">
                    <input type="hidden" name="bank_ledger" value="<?= $bank_ledger; ?>">
                    <input type="hidden" name="file_month" value="<?= $file_month; ?>">

                    <p class="" style="color:red;"><?= isset($errormsg) ? $errormsg : ""; ?></p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"> Submit</button>
                </div>
            </form>
        </div>

    </div>
</div>

<div id="mismatch_account" class="modal fade" role="dialog" data-backdrop="static" >
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title" style="float: left;">Mismatch Accounts</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="pwd">The Account you have selected is not matching with the bank statement.</label>
                    <label for="pwd">Try again with the correct details.</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-submit" data-dismiss="modal">OK</button>
            </div>
        </div>

    </div>
</div>

<div id="minimum_statement" class="modal fade" role="dialog" data-backdrop="static" >
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title" style="float: left;">Minimum Statement</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="pwd">The Bank statement should contain atleast five statement.</label>
                    <label for="pwd">This statement can't be upload.</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-submit" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var bank_ajax_bank_statement = 'yes';
</script>
<?php
$this->load->view('layout/footer');
// $this->load->view('bank_statement/categorized-popup');
// $this->load->view('bank_statement/split-popup');
// $this->load->view('bank_statement/cat_service');
$this->load->view('bank_account/bank_modal');
?>
<script type="text/javascript">var base_url = "<?php echo base_url(); ?>";</script>

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

        $('#modal_open').hide();
        $('#mismatch_acc').hide();
        $('#minimum_stat').hide();

        var password_prot = $('#modal_action').val();
        if (password_prot == 'open')
        {
            $('#modal_open').click();
        }

        var mismatch_acc = $('#mismatch_action').val();
        if (mismatch_acc == 'open')
        {
            $('#mismatch_acc').click();
        }

        var minstat_action = $('#minstat_action').val();
        if (minstat_action == 'open')
        {
            $('#minimum_stat').click();
        }

        $("#search_submit").click(function () {
            var bank_ledger_id = $('#bank_ledger_id').val();
            // var to_date = $('#to_date').val();
            var month = $('#month').val();
            var uploadfilename = $("#uploadfilename").val();

            if (bank_ledger_id == "" || bank_ledger_id == null) {
                $('#err_bank_account').text("Please Select Bank Account");
                return false;
            } else {
                $('#err_bank_account').text('');
            }

            // if (month == "" || month == null) {
            //     $('#err_month').text("Please Select Month");
            //     return false;
            // } else {
            //     $('#err_month').text('');
            // }

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

            if (uploadfilename == "" || uploadfilename == null) {
                $('#err_uploadfilename').text("Please Select File");
                return false;
            } else {
                $('#err_uploadfilename').text('');
            }
            if (uploadfilename)
            {
                var extension = uploadfilename.split('.').pop().toUpperCase();
                if (uploadfilename.length < 1)
                {
                    // $('#err_uploadfilename').text("Invalid extension " + extension);
                    return false;
                } else if (extension != "XLS" && extension != "XLSX" && extension != "CSV")
                {
                    uploadfilename_ok = 0;
                    $('#err_uploadfilename').text("Invalid extension " + extension);
                    return false;
                } else
                {
                    uploadfilename_ok = 1;
                }
            }
        });
    });
</script>
