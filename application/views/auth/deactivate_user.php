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
            <li class="active">Deactivate User</li>
        </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Deactivate User</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- <h1><?php echo lang('deactivate_heading'); ?></h1> -->
                                <p><?php echo sprintf(lang('deactivate_subheading'), $user->username); ?></p>
                                <?php
                                $user_id = $this->encryption_url->encode($user->id);
                                echo form_open("auth/deactivate/" . $user_id);
                                ?>
                                <p>
                                    <?php echo lang('deactivate_confirm_y_label', 'confirm'); ?>
                                    <input type="radio" name="confirm" value="yes" checked="checked" class="minimal" />
                                    <?php echo lang('deactivate_confirm_n_label', 'confirm'); ?>
                                    <input type="radio" name="confirm" value="no" class="minimal" />
                                </p>
                                <?php echo form_hidden($csrf); ?>
                                <?php echo form_hidden(array(
                                'id' => $user->id ));
                                ?>
                                <!-- <p><?php echo form_submit('submit', lang('deactivate_submit_btn')); ?></p> -->
                                <button type="submit" id="deactivate_submit_btn" name="deactivate_submit_btn" class="btn btn-info">Submit</button>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
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
// $this->load->view('general/modal/delete_modal');
?>