<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$type = user_limit();
$scount = supplier_count();
$limit = $type['coun'] + $scount['scount'];
// print_r($type);
$this->load->view('layout/header');
?>
<div class="content-wrapper">    
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>
                    <!-- Dashboard -->
                    Dashboard</a></li>
            <li class="active">
                Users
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div id="plus_btn">
                            <h3 class="box-title">Users</h3>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('auth/create_user'); ?>">Add User</a>
                        </div>
                        <div id="filter">
                            <div class="box-header box-body filter_body">
                                <div class="btn-group">
                                    <span><a href="javascript:void(0);" class="btn btn-app edit" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit User" > <i class="fa fa-pencil"></i> </a></span>
                                    <span><a href="javascript:void(0);" class="btn btn-app update" data-toggle="tooltip" data-placement="bottom" data-original-title="Update Privilege" > <i class="fa fa-user"></i> </a></span>
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="box-body">
                        <table id="index" class="table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th>
                                        #
                                    </th>
                                    <th>
                                        First Name
                                    </th>
                                    <th>
                                        Last Name
                                    </th>                                  
                                    <th>
                                        Email
                                    </th>
                                   <!-- <th>
                                        Groups
                                    </th> -->
                                    <th>
                                        Status
                                    </th>                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($users as $user) {
                                    $user_id = $this->encryption_url->encode($user->id);
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="check_user" class="form-check-input" value="<?= $user_id; ?>">
                                            <input type="hidden" name="edit" value="<?php echo base_url('auth/edit_user/'); ?><?php echo $user_id; ?>">
                                            <input type="hidden" name="update" value="<?php echo base_url('privilege/update_user/'); ?><?php echo $user_id; ?>">
                                            <!-- <a href="<?php echo base_url('auth/edit_user/'); ?><?php echo $user_id; ?>" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a>
                                            <a href="<?php echo base_url('privilege/update_user/'); ?><?php echo $user_id; ?>" title="Update Privilege" class="btn btn-xs btn-warning"><span class="glyphicon glyphicon-edit"></span></a>  -->
                                        </td>
                                        <td><?php echo $user->first_name; ?></td>
                                        <td><?php echo $user->last_name; ?></td>                                   
                                        <td><?php echo $user->email ?></td>
                                       <!-- <td><?php foreach ($user->groups as $group): ?>
                                                <?php echo htmlspecialchars($group->name, ENT_QUOTES, 'UTF-8'); ?><br />
                                            <?php endforeach ?>
                                        </td> -->
                                        <td><?php echo ($user->active) ? anchor("auth/deactivate/" . $user_id, lang('index_active_link')) : anchor("auth/activate/" . $user_id, lang('index_inactive_link')); ?></td>                                        
                                    </tr>
                                    <?php
                                }
                                ?>
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
$this->load->view('general/modal/delete_modal');
?>
<script type="text/javascript">
    $(document).on('click', 'input[name="check_user"]', function () {
        if ($(this).is(":checked")) {
            $(document).find('[name=check_user]').prop('checked', false);
            $(this).prop('checked', true);
            $('#filter .edit').attr('href', $(this).parent().find('[name=edit]').val());
            $('#filter .update').attr('href', $(this).parent().find('[name=update]').val());
            $('#plus_btn').hide();
            $('#filter').show();
        } else {
            $('#plus_btn').show();
            $('#filter').hide();
        }
    });
    $(document).ready(function () {
        <?php if($new_user_success = $this->session->flashdata('new_user_success')){?>
        alert_d.text = '<?= $new_user_success; ?>';
        PNotify.success(alert_d);
        <?php } ?>
    });
</script>
