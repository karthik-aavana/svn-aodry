<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li>
                <a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                Sub Department
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div id="plus_btn">
                        <div class="box-header with-border">
                            <h3 class="box-title">Sub Department</h3>
                            <?php
                            if (in_array($subdepartment_module_id, $active_add)) {
                                ?>
                                <a data-toggle="modal" data-target="#add_subdepartment_modal" class="add_subdepartment btn btn-sm btn-info pull-right" href="">Add Subdepartment</a>
                            <?php }
                            ?>
                        </div>
                    </div>
                    <div id="filter">
                        <div class="box-header with-border box-body filter_body">
                        </div>
                    </div>
                    <div class="box-body" style="overflow-y: auto;">
                        <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th width="9px">#</th>
                                    <th>Sub Department Code</th>
                                    <th>Sub Department Name</th>
                                    <th>Department Name</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('general/delete_modal');
$this->load->view('subdepartment/add_subdepartment_modal');
$this->load->view('subdepartment/edit_subdepartment_modal');
?>
<script>
    var dTable = '';
    $(document).ready(function () {
        dTable = getAllSubdepartment();
        <?php 
        $subdepartment_success = $this->session->flashdata('subdepartment_success');
        $subdepartment_error = $this->session->flashdata('subdepartment_error');
        ?>
        var alert_success = '<?= $subdepartment_success; ?>';
        var alert_failure = '<?= $subdepartment_error; ?>';
        if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });
    $('body').on('change', 'input[type="checkbox"][name="check_item"]', function () {
        var i = 0;
        $.each($("input[name='check_item']:checked"), function () {
            i++;
        });
        if (i == 1)
        {
            var row = $("input[name='check_item']:checked").closest("tr");
            var action_button = row.find('.action_button').html();

            $('#plus_btn').hide();
            $('.filter_body').html(action_button);
            $('#filter').show();
        } else
        {
            $('#plus_btn').show();
            $('#filter').hide();
            $('.filter_body').html('');
        }
    });

    function getAllSubdepartment(){
        var table = $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": base_url + "subdepartment",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "sub_department_code"},
                {"data": "sub_department_name"},
                {"data": "department_name"}
            ]
        });
        return table;
    }
</script>