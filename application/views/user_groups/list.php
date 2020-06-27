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
                User Groups
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div id="plus_btn">
                        <div class="box-header with-border">
                            <h3 class="box-title">User Group</h3>
                            <?php
                            if (in_array($groups_module_id, $active_add)) {
                                ?>
                                <a data-toggle="modal" data-target="#add_user_group_modal" class="add_user_group btn btn-sm btn-info pull-right">Add User Group</a>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="filter">
                        <div class="box-header with-border box-body filter_body">
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th width="10%">#</th>
                                    <th width="30%">Group Name</th>
                                    <th width="30%">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('user_groups/add_user_group_modal');
$this->load->view('user_groups/edit_user_group_modal');
$this->load->view('general/delete_modal');
?>
<script type="text/javascript">
    var dTable = '';
    $(document).ready(function () {
        dTable = getAllUserGroup();
        <?php 
        $group_user_success = $this->session->flashdata('group_user_success');
        $group_user_error = $this->session->flashdata('group_user_error');
        ?>
        var alert_success = '<?= $group_user_success; ?>';
        var alert_failure = '<?= $group_user_error; ?>';
        if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });
    function getAllUserGroup(){
        var table = $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "iDisplayLength": 15,
            "lengthMenu": [ [15, 25, 50,100, -1], [15, 25, 50,100, "All"] ],
            "ajax": {
                "url": base_url + "groups",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "group_name"},
                {"data": "description"}
            ]
        });
        return table;
    }
</script>
