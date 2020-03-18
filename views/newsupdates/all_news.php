<?php
$this->load->view('layout/header');
?>
<style type="text/css">
    .info-box-content {
        padding: 25px 10px;
    }
</style>
<div class="content-wrapper" style="padding-bottom: 80px !important;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?php echo base_url('newsupdates'); ?>"></i> News and Updates</a></li>
                <li class="active">All News and Updates</li>
            </ol>
        </h5>

    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-">

            </div>
            <div class="col-md-9" style="">
                <?php
                foreach ($news as $new)
                {

                    $users = explode(",", $new->news_display_id);

                    if (in_array($this->session->userdata('SESS_USER_ID'), $users))
                    {

                        $news_description = str_replace('\r\n', '', $new->news_description);
                        $id               = $this->encryption_url->encode($new->news_id);
                        ?>
                        <a href="<?= base_url('newsupdates/update_new/' . $id) ?>"> <div class="box box-solid" style="background: #cccccc2e">
                                <div class="box-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <div class="clearfix">
                                                <h4 style="margin-top: 0;color: #000"><?= substr($new->news_title, 0, 12) . ' ....'; ?></h4>
                                                <p style="float: right;position: absolute;right: 10px" class="label label-primary"><?= ucwords($new->type) ?></p>
                                                <p><?= substr($news_description, 0, 100) . ' .....' ?></p>
                                                <p style="margin-bottom: 0;float: right;">
                                                    </i><?= ($new->news_added_date == date("Y-m-d") ? "Today" : $new->news_added_date ) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div></a>
                        <?php
                    }
                }
                ?>

            </div>
        </div>

    </section>
    <!-- /.content -->
</div>

<?php
$this->load->view('layout/footer');
?>


