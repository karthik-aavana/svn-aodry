<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    </section>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Missed Target</h3>
                        <?php
                        if (in_array($lead_module_id, $active_add))
                        {
                            ?>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('leads/add'); ?>" title="Add Lead">Add Lead</a>
                        <?php } ?>
                        <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('leads/index'); ?>" title="Main Page">Main Page</a>
                        <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('leads/todays_target'); ?>" title="Todays Targets">Today's Targets</a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
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
                                                <!-- <th style="min-width: 100px;text-align: center;">Product / Service</th> -->
                                                <th style="min-width: 100px;text-align: center;">Source</th>
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
                    "url": base_url + "leads/missed_targets",
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
                },
                "columns": [
                    {"data": "lead_date"},
                    {"data": "asr_no"},
                    {"data": "customer"},
                    {"data": "group"},
                    {"data": "stages"},
// { "data" : "product_service" },
                    {"data": "source"},
                    {"data": "business_type"},
                    {"data": "next_action_date"},
                    {"data": "priority"},
                    {"data": "added_user"},
                    {"data": "action"}
                ]
            });
        }
    });
</script>
