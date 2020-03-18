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
                <li class="active">View Bank Statement
                </li>
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
                        <h3 class="box-title">View Bank Statement</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="<?php echo base_url('bank_statement/view_list_data'); ?>"  role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                                    <div class="well">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="sales">
                                                    <div class="form-group">
                                                        <label for="Bank Account">Bank Account<span class="validation-color">*</span></label>
                                                        <select class="form-control select2" id="bank_ledger_id" name="bank_ledger_id" style="width: 100%;">
                                                            <option value="all" <?php if (isset($bank_ledger_id) && $bank_ledger_id == 'all') echo "selected = 'selected' "; ?> >All</option>
                                                            <?php
                                                            foreach ($bank as $key => $row)
                                                            {
                                                                ?>
                                                                <option value="<?= $row->ledger_id ?>" <?php if (isset($bank_ledger_id) && $bank_ledger_id == $row->ledger_id) echo "selected = 'selected' "; ?> ><?= $row->ledger_title ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                        <span class="validation-color" id="err_bank_account"><?php echo form_error('Bank Account'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 pull-right mt-30">
                                                <div class="form-group">
                                                    <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('bank_statement/get_bank_statement'); ?>" title="Get Bank Statement">Get Bank Statement</a>
                                                </div>
                                            </div>
                                            <!-- <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="From">From<span class="validation-color">*</span></label>
                                                    <input class="form-control datepicker" id="from_date" name="from_date" value="<?php if (isset($post_data['from_date']) && $post_data['from_date'] != "") echo $post_data['from_date']; ?>">
                                                    <span class="validation-color" id="err_from_date"><?php echo form_error('from_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="To">To<span class="validation-color">*</span></label>
                                                    <input class="form-control datepicker" id="to_date" name="to_date" value="<?php if (isset($post_data['to_date']) && $post_data['to_date'] != "") echo $post_data['to_date']; ?>">
                                                    <span class="validation-color" id="err_to_date"><?php echo form_error('to_date'); ?></span>
                                                </div>
                                            </div> -->
                                            <!-- <div class="col-sm-2">
                                                <label for="To"><span class="validation-color"></span></label>
                                                <button type="submit" class="btn btn-sm btn-info pull-right" id="search_submit">Search Bank Statement</button>
                                            </div> -->
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="box-body">
                                <table id="log_datatable1" class="table table-bordered table-striped table-hover table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Bank Account</th>
                                            <th>Month</th>
                                            <th width="16%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_rawdata">
                                        <?php
                                        foreach ($file_details as $row)
                                        {
                                            if ($row->categorized_status == 0)
                                            {
                                                $color = 'red';
                                            }
                                            else
                                            {
                                                $color = 'green';
                                            }
                                            ?>
                                            <tr style="color: <?php echo $color; ?>;">
                                                <td><?php
                                                    // echo date("Y-m-d",$row->created_on);
                                                    echo date('d-m-Y H:i:s', strtotime($row->added_date));
                                                    ?></td>
                                                <td><?php echo $row->ledger_title; ?></td>
                                                <td><?php
                                                    $timestamp = mktime(0, 0, 0, $row->month, 1, 2011);
                                                    echo date("F", $timestamp);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($color == 'red')
                                                    {
                                                        ?>
                                                        <a title="History" class="statement_history btn btn-xs btn-info" data-file_id="<?php echo $row->file_id; ?>" data-target="#view_history" data-toggle="modal"><span class="fa fa-book"></span></a>
                                                    <?php } ?>
                                                    <a title="View" class="btn btn-xs btn-warning" href="<?php echo base_url(); ?>bank_statement/showdata/<?php echo $row->file_id; ?>"><span class="fa fa-eye">  </span></a>
                                                    <a title="Delete" class="delete_statement btn btn-xs btn-danger " href="" data-file_id="<?php echo $row->file_id; ?>" data-target="#delete_file_details" data-toggle="modal"><span class="fa fa-trash"></span></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <div style='background:green;width:15px;height:15px;float:left;'></div> Categorized Bank Statement.<br>
                                <div style='background:red;width:15px;height:15px;float:left;'></div> Uncategorized Bank Statement.<br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div id="view_history" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title" style="float: left;">Uploading Statement History</h4>
            </div>
            <div class="modal-body">
                <!-- <div class="form-group">
                    <label>How many split you want for <op id="t_type"></op> Amount : <op id="amount"></op></label>
                    <div class="form-group">
                        <select class="form-control" id="split_select" name="split_select">
                            <option value="">Select</option>
                <?php
                for ($i = 2; $i <= 10; $i++)
                {
                    ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php } ?>
                        </select>

                    </div>
                </div> -->
                <form id="history_form" method="POST">
                    <div id="history_text">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="delete_file_data" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title pull-left">Delete Statement</h4>
                <button type="button" class="close pull-right" data-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Are you sure!</label>
                    <label>You want to delete this statement ?</label>
                </div>
            </div>
            <div class="modal-footer" id="delete_file">
            </div>
        </div>
    </div>
</div>
<div id="delete_file_details" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title pull-left">Delete Statement</h4>
                <button type="button" class="close pull-right" data-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>This action will delete the entire statement history related to this statement.</label>
                    <label>Are you Sure! You want to delete this statement ?</label>
                </div>
            </div>
            <div class="modal-footer" id="delete_all_files">
            </div>
        </div>
    </div>
</div>
<div id="deletion_success" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title pull-left">Deletion Success</h4>
                <button type="button" class="close pull-right" data-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>The Statement has been Deleted Successfully.</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type='button' class='btn btn-default' data-dismiss='modal'>OK</button>
            </div>
        </div>
    </div>
</div>
<?php
if ($this->session->userdata('delete_statement') == 'success')
{
    ?>
    <input type="hidden" id="delete_stat" name="delete_stat" value="<?php echo $this->session->userdata('delete_statement'); ?>">
    <input type="button" data-toggle="modal" data-target="#deletion_success" id="del_stat" name="del_stat" value="">
    <?php
    $this->session->unset_userdata('delete_statement');
}
?>
<?php
$this->load->view('layout/footer');
$this->load->view('general/delete_modal');
?>
<script src="<?php echo base_url(); ?>assets/dist/js/jquery.backstretch.min.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/scripts.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#del_stat').hide();
        var delete_stat = $('#delete_stat').val();
        if (delete_stat == 'success')
        {
            $('#del_stat').click();
        }
    });
    $(".delete_statement").click(function () {
        var file_id = $(this).data('file_id');
        var delete_link = "<?php echo base_url('bank_statement/delete_file_details/'); ?>" + file_id;
        document.getElementById('delete_all_files').innerHTML = "<button type='button' class='btn btn-default' data-dismiss='modal'>No</button><a href=" + delete_link + " title='Delete' class='btn btn-primary pull-right'>Yes</a>";
    });
    $(function () {
        $(".statement_history").click(function () {
            var file_id = $(this).data('file_id');
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('bank_statement/statement_history/') ?>",
                data: {file_id: file_id},
                success: function (data) {
                    document.getElementById('history_text').innerHTML = data;
                    $(".file_delete").click(function () {
                        var file_id = $(this).data('file_id');
                        var contain_files = $(this).data('contain_files');
                        var delete_link = "<?php echo base_url('bank_statement/delete_file_data/'); ?>" + file_id + "/" + contain_files;
                        document.getElementById('delete_file').innerHTML = "<button type='button' class='btn btn-default' data-dismiss='modal'>No</button><a href=" + delete_link + " title='Delete' class='btn btn-primary pull-right'>Yes</a>";
                    });
                }
            });
        });
    });
    $(function () {
        $("#bank_ledger_id").change(function () {
            var bank_ledger_id = $('#bank_ledger_id').val();
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('bank_statement/view_list_data/') ?>",
                data: {bank_ledger_id: bank_ledger_id},
                success: function (data) {
                    document.getElementById('tbody_rawdata').innerHTML = data;
                }
            });
        });
    });
    $(document).ready(function () {
        $("#search_submit").click(function () {
            var bank_ledger_id = $('#bank_ledger_id').val();
            var to_date = $('#to_date').val();
            var from_date = $('#from_date').val();
            if (bank_ledger_id == "" || bank_ledger_id == null) {
                $('#err_bank_account').text("Please Select Bank Account");
                return false;
            } else {
                $('#err_bank_account').text('');
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
    });
</script>

