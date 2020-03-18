<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> <!-- Dashboard --> Dashboard</a></li>
                <li><a href="<?php echo base_url('newsupdates'); ?>"><!-- news_updates -->News and Updates</a></li>
                <li class="active"><!-- Add news_updates --> Edit News and Updates</li>
            </ol>
        </h5>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><!-- Add news_updates -->Edit News and Updates</h3>
                    </div>
                    <?php
                    foreach ($get_list as $news_list)
                    {
                        // print_r($key);
                        $selected       = $news_list->type;
                        $selected_users = explode(",", $news_list->news_display_id);
                        // foreach ($users as $user) {
                        //  foreach ($selected_users as  $value) {
                        //    if($user->id == $value){
                        //      echo $value->id;
                        //    }
                        //  }
                        // }
                        ?>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form role="form" id="form" method="post" action="<?php echo base_url('newsupdates/edit_newsupdates'); ?>">
                                <div class="well">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="news_updates_name"><!-- news_updates Name -->Title <span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" value="<?= $news_list->news_title ?>" id="title" name="title">
                                                <span class="validation-color" id="err_title"><?php echo form_error('title'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="news_updates_name">Type<span class="validation-color">*</span></label>
                                                <select class="form-control select2" id="type" name="type" style="width: 100%;">
                                                    <option value="">Select</option>
                                                    <option <?= ($selected == 'news') ? "selected" : "" ?> value='news'>News</option>
                                                    <option value='update' <?= ($selected == 'update') ? "selected" : "" ?>>Update</option>
                                                </select>
                                                <span class="validation-color" id="err_type"><?php echo form_error('type'); ?></span>
                                            </div>
                                        </div>
                                        <input type="hidden" name="news_id" value="<?= $id ?>">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="news_updates_name">Users<span class="validation-color">*</span></label>
                                                <select multiple="multiple" class="form-control select2" id="usersSelect" name="usersSelect[]" style="width: 100%;">
                                                    <?php
                                                    foreach ($users as $key)
                                                    {
                                                        ?>
                                                        <option value="<?= $key->id ?>" <?php
                                                        if (in_array($key->id, $selected_users))
                                                        {
                                                            echo "selected";
                                                        }
                                                        ?> ><?= $key->username ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                </select>
                                                <span class="validation-color" id="err_usersSelect"><?php echo form_error('usersSelect'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="box-footer mt-15">
                                                <button class="btn btn-default" id="selectAll" type="button">Select All</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="news_updates_name">Description<span class="validation-color">*</span></label>
                                                <textarea class="ckeditor" id="description" name="description"> <?= str_replace('\r\n', '', $news_list->news_description) ?> </textarea>
                                                <span class="validation-color" id="err_description"><?php echo form_error('description'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="box-footer">
                                                <button type="submit" id="news_updates_submit" class="btn btn-default">Update</button>
                                                <button class="btn btn-default" id="cancel" onclick="cancel('newsupdates')"><!-- Cancel -->Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                            </form>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <!-- /.box-body -->
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
?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#selectAll").click(function () {
            $("#usersSelect option").prop('selected', true);
        })
    })
</script>
<script src="<?php echo base_url('assets/js/news_updates/') ?>news_updates.js"></script>
