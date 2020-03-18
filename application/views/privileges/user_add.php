<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?php echo base_url('auth'); ?>"> User</a></li>
                <li class="active">Add Privileges</li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Privileges</h3>
                        <a class="btn btn-sm btn-default pull-right" onclick="cancel('auth')">Back</a>
                    </div>
                    <form role="form" id="form" enctype="multipart/form-data" method="post" action="<?php echo base_url('privilege/update_user_data'); ?>">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <input type="hidden" name="user_id" value="<?= $user_id ?>">
                                        <label for="type_of_supply">Module Name <span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="module_id" name="module_id">
                                            <option value="">Select</option>
                                            <?php
                                            foreach ($modules as $module) {
                                                ?>
                                                <option value='<?php echo $module->module_id ?>'>
                                                    <?php echo $module->module_name; ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="validation-color" id="err_module_id"></span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mt-30">
                                        <label>Add</label>
                                        <input type="checkbox" checked="" id="add"  name="add">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mt-30">
                                        <label>View</label>
                                        <input type="checkbox" id="view"  name="view">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mt-30">
                                        <label>Edit</label>
                                        <input type="checkbox" id="edit"  name="edit">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mt-30">
                                        <label>Delete</label>
                                        <input type="checkbox" id="delete" name="delete">
                                    </div>
                                </div>
                                <div class="col-md-1 mt-15">
                                    <button type="submit" id="model_submit" class="btn btn-info">Add</button>
                                </div>                                
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="index" class="table table-bordered table-striped table-hover table-responsive">
                                        <thead>
                                            <?php
                                            // $branch_id =  $this->uri->segment(5);
                                            $user_id = $this->uri->segment(3);
                                            $user_id = $this->encryption_url->decode($user_id);
                                            ?>
                                        <input type="hidden" name="user_id" id="user_id" value="<?= $user_id ?>">
                                        <!-- <p style="display: none;" id="branch_id"><?= $branch_id ?></p> -->
                                        <p style="display: none;" ><?= $user_id ?></p>
                                        <tr>
                                            <th>Module</th>
                                            <th>Add</th>
                                            <th>Edit</th>
                                            <th>view</th>
                                            <th>Delete</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($privilege_active_modules as $row) {
                                                $id = $row->accessibility_id;
                                                $p_id = $this->encryption_url->encode($id);
                                                ?>
                                                <tr id="row<?= $id ?>">
                                                    <td><?= $row->module_name ?></td>
                                                    <td><?= $row->add_privilege ?></td>
                                                    <td><?= $row->edit_privilege ?></td>
                                                    <td><?= $row->view_privilege ?></td>
                                                    <td><?= $row->delete_privilege ?></td>
                                                    <td>
                                                        <a data-toggle="modal" data-target="#update_privillege" data-id="<?= $id ?>" title="Update Privilege" class="privilege"><i class="fa fa-pencil"></i></a> | 
                                                        <a data-toggle="modal" data-target="#delete_modal" data-id="<?= $id ?>" title="Delete" class="delete_record"><span class="fa fa-trash-o"></span></a>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('privileges/update_privillege_modal');
?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#model_submit").click(function (e) {
            var user_id = $("#user_id").text();
            var module_id = $("#module_id").val();
            var add = document.getElementById('add').checked ? 'yes' : 'no';
            var view = document.getElementById('view').checked ? 'yes' : 'no';
            var edit = document.getElementById('edit').checked ? 'yes' : 'no';
            var del = document.getElementById('delete').checked ? 'yes' : 'no';
            if (module_id == "")
            {
                $("#err_module_id").html('please select a module');
                return false;
            } else
            {
                $("#err_module_id").html('');
            }
        });
        <?php 
        $privilege_success = $this->session->flashdata('privilege_success');
        $module_add = $this->session->flashdata('module_add');
        $privilege_error = $this->session->flashdata('privilege_error');
        ?>
        var alert_success = '<?= $privilege_success; ?>';
        var alert_module_success = '<?= $module_add; ?>';
        var alert_failure = '<?= $privilege_error; ?>';
        if(alert_module_success != ''){
            alert_d.text = alert_module_success;
            PNotify.success(alert_d);
        }else if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });
</script>
<script type="text/javascript">
    $(document).on("click", ".delete_record", function () {
        var id = $(this).data('id');
        var r = confirm("Are you sure?");
        if (r == true) {
            $.ajax({
                url: base_url + 'superadmin/modules/privilege_delete/' + id,
                type: "GET",
                success: function (result) {
                    location.reload();
                }
            });
        }
    });
    <?php 
        $module_delete_success = $this->session->flashdata('module_delete_success');
    ?>
    var alert_success = '<?= $module_delete_success; ?>';
    if(alert_success != ''){
        alert_d.text = alert_success;
        PNotify.success(alert_d);
    }
</script>

