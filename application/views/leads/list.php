<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    </section>
    </script>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Leads</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Leads</h3>
                        <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('leads/list_business_type'); ?>" title="Business Type List">Business Type List</a>
                        <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('leads/list_source'); ?>" title="Source List">Source List</a>
                        <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('leads/list_stages'); ?>" title="Stages List">Stages List</a>
                        <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('leads/list_group'); ?>" title="Group List">Group List</a>
                        <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('leads/todays_target'); ?>" title="Todays Target">Today's Target</a>
                        <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('leads/missed_targets'); ?>" title="Missed Target">Missed Target</a>
                        <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('report/leads_report'); ?>" title="Leads Reports"> Report </a>
                        <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('report/logs_report'); ?>" title="Leads Log"> Log  </a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="well">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="asr_no">
                                        <div class="form-group">
                                            <label for="asr_no">Lead No</label>
                                            <input type="text"  class="form-control" id="asr_no" name="asr_no">
                                            <!-- <span class="validation-color" id="err_asr_no"><?php echo form_error('asr_no'); ?></span> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="customer">
                                        <div class="form-group">
                                            <label for="customer">Customer</label>
                                            <select class="form-control select2" id="customer" name="customer" style="width: 100%;">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="group">
                                        <div class="form-group">
                                            <label for="group">Group</label>
                                            <select class="form-control select2" id="group" name="group" style="width: 100%;">
                                                <option value="">Select</option>
                                                <!-- <option value="Subscriber">Subscriber</option>
                                                <option value="Lead">Lead</option>
                                                <option value="Opportunities">Opportunities</option>
                                                <option value="Not Interested">Not Interested</option> -->
                                            </select>
                                            <!-- <span class="validation-color" id="err_group"><?php echo form_error('group'); ?></span> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="stages">
                                        <div class="form-group">
                                            <label for="stages">Stages</label>
                                            <select class="form-control select2" id="stages" name="stages" style="width: 100%;">
                                                <option value="">Select</option>
                                            </select>
                                            <!-- <span class="validation-color" id="err_stages"><?php echo form_error('stages'); ?></span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label for="lead_date">Lead Date</label>
                                    <div class="form-group">
                                        <div class='input-group'>
                                            <input class="form-control" id="lead_from" name="lead_from" value="<?php if (isset($post_data['lead_from']) && $post_data['lead_from'] != "") echo $post_data['lead_from']; ?>" placeholder="From">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <!-- <span class="validation-color" id="err_lead_from"><?php echo form_error('lead_from'); ?></span> -->
                                    </div>
                                    <div class="form-group">
                                        <div class='input-group'>
                                            <input class="form-control" id="lead_to" name="lead_to" value="<?php if (isset($post_data['lead_to']) && $post_data['lead_to'] != "") echo $post_data['lead_to']; ?>" placeholder="To">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <!-- <span class="validation-color" id="err_lead_to"><?php echo form_error('lead_to'); ?></span> -->
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label for="next_action_date">Next Action Date</label>
                                    <div class="form-group">
                                        <div class='input-group date'>
                                            <input class="form-control datepicker" id="next_action_from" name="next_action_from" value="<?php if (isset($post_data['next_action_from']) && $post_data['next_action_from'] != "") echo $post_data['next_action_from']; ?>" placeholder="From">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <span class="validation-color" id="err_next_action_from"><?php echo form_error('next_action_from'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <div class='input-group date'>
                                            <input class="form-control datepicker" id="next_action_to" name="next_action_to" value="<?php if (isset($post_data['next_action_to']) && $post_data['next_action_to'] != "") echo $post_data['next_action_to']; ?>" placeholder="To">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <span class="validation-color" id="err_next_action_to"><?php echo form_error('next_action_to'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-2 pull-right">
                                    <div class="form-group">
                                        <label></label>
                                        <button type="submit" class="form-control btn btn-info" id="search_leads">Search Leads</button><br><br><br>
                                        <?php
                                        if (in_array($lead_module_id, $active_add))
                                        { ?>
                                            <a class="btn btn-info pull-right" href="<?php echo base_url('leads/add'); ?>" title="Add Lead">Add Lead</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="well">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                                        <thead>
                                            <tr>
                                                <th style="min-width: 100px;text-align: center;">Lead Date</th>
                                                <th style="min-width: 60px;text-align: center;">Lead No</th>
                                                <th style="min-width: 200px;text-align: center;">Customers</th>
                                                <th style="min-width: 100px;text-align: center;">Group</th>
                                                <th style="min-width: 150px;text-align: center;">Stages</th>
                                                <th style="min-width: 100px;text-align: center;">Source</th>
                                                <!-- <th style="min-width: 100px;text-align: center;">Product/Service</th> -->
                                                <th style="min-width: 100px;text-align: center;">Business Type</th>
                                                <th style="min-width: 90px;text-align: center;">Next Action Date</th>
                                                <th style="min-width: 90px;text-align: center;">Priority</th>
                                                <th style="min-width: 100px;text-align: center;">User</th>
                                                <th style="min-width: 250px;text-align: center;"><?php echo ''; ?></th>  
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
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
$this->load->view('general/delete_modal');
$this->load->view('leads/change_stages');
?>
<script>
    $(document).ready(function () {
        generateOrderTable();
        function generateOrderTable() {
            $('#list_datatable').DataTable({
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "ajax": {
                    "url": base_url + "leads",
                    "dataType": "json",
                    "type": "POST",
                    "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        'asr_no': $('#asr_no').val(),
                        'customer': $('#customer').val(),
                        'group': $('#group').val(),
                        'stages': $('#stages').val(),
                        'lead_from': $('#lead_from').val(),
                        'lead_to': $('#lead_to').val(),
                        'next_action_from': $('#next_action_from').val(),
                        'next_action_to': $('#next_action_to').val()
                    },
                    "dataSrc": function (result)
                    {
                        if (result.customer_data && $('#customer').val() == "")
                        {
                            $('#customer').html('<option value="">Select</option>');
                            var option = '';
                            for (i = 0; i < result.customer_data.length; i++)
                            {
                                option += '<option value="' + result.customer_data[i].customer_id + '">' + result.customer_data[i].customer_code + ' - ' + result.customer_data[i].customer_name + '</option>';
                            }
                            $('#customer').append(option);
                        }
                        if (result.group_data && $('#group').val() == "")
                        {
                            $('#group').html('<option value="">Select</option>');
                            var option = '';
                            for (i = 0; i < result.group_data.length; i++)
                            {
                                option += '<option value="' + result.group_data[i].lead_group_id + '">' + result.group_data[i].lead_group_name + '</option>';
                            }
                            $('#group').append(option);
                        }
                        return result.data;
                    },
                },
                "columns": [
                    {"data": "lead_date"},
                    {"data": "asr_no"},
                    {"data": "customer"},
                    {"data": "group"},
                    {"data": "stages"},
                    {"data": "source"},
                    // {"data" : "product_service" },
                    {"data": "business_type"},
                    {"data": "next_action_date"},
                    {"data": "priority"},
                    {"data": "added_user"},
                    {"data": "action"}
                ]
            });
        }
        $('#search_leads').click(function () { //button filter event click
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable()
        });
        $("#group").change(function (event)
        {
            var group = $('#group').val();
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: base_url + 'leads/get_stages',
                data:
                    {
                        group: group
                    },
                success: function (data)
                {
                    $('#stages').html('<option value="">Select</option>');
                    var option = '';
                    for (i = 0; i < data.length; i++)
                    {
                        option += '<option value="' + data[i].lead_stages_id + '">' + data[i].lead_stages_name + '</option>';
                    }
                    $('#stages').append(option);
                }
            });
        });
        $("#lead_from").datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: false,
            orientation: "auto",
            todayBtn: false,
        }).on('changeDate', function (selected) {
            var minDate = new Date(selected.date.valueOf());
            $('#lead_to').datepicker('setStartDate', minDate);
        });
        $("#lead_to").datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: false,
            orientation: "auto",
            todayBtn: false,
        }).on('changeDate', function (selected) {
            var maxDate = new Date(selected.date.valueOf());
            $('#lead_from').datepicker('setEndDate', maxDate);
        });
        $("#next_action_from").datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: false,
            orientation: "auto",
            todayBtn: false,
        }).on('changeDate', function (selected) {
            var minDate = new Date(selected.date.valueOf());
            $('#next_action_to').datepicker('setStartDate', minDate);
        });
        $("#next_action_to").datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: false,
            orientation: "auto",
            todayBtn: false,
        }).on('changeDate', function (selected) {
            var maxDate = new Date(selected.date.valueOf());
            $('#next_action_from').datepicker('setEndDate', maxDate);
        });
    });
</script>
