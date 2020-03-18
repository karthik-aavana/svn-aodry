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
                <li><a href="<?php echo base_url('newsupdates'); ?>"><!-- news_updates -->News and Updates</a></li>
                <li><a href="<?php echo base_url('newsupdates/all_news'); ?>"> All News and Updates</a></li>
                <li class="active">View News and Updates</li>
            </ol>
        </h5>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-">

            </div>
            <div class="col-md-12" style="">
                <?php
                foreach ($news as $new)
                {
                    ?>
                    <div class="box box-solid" style="background: #cccccc2e">
                        <div class="box-body">
                            <div class="media">
                                <div class="media-body">
                                    <div class="clearfix">
                                        <h4 style="margin-top: 0"><?= $new->news_title; ?></h4>
                                        <p style="float: right;position: absolute;right: 10px;    bottom: 20px" class="label label-primary"><?= ucwords($new->type) ?></p>
                                        <p><?= str_replace('\r\n', '', $new->news_description) ?></p>
                                        <p style="margin-bottom: 0;float: right;">
                                            </i><?= ($new->news_added_date == date("Y-m-d") ? "Today" : $new->news_added_date ) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
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


