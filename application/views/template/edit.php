<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function in_array_results($needle, $haystack, $strict = false)
{
    foreach ($haystack as $item)
    {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_results($needle, $item, $strict)))
        {
            return true;
        }
    }

    return false;
}

$b = $this->session->userdata('type');

if (!in_array_results('admin', $b) || in_array_results('manager', $b))
{
    redirect('auth');
}
$this->load->view('layout/header');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>
                        <!-- Dashboard -->
                        <?php echo $this->lang->line('header_dashboard'); ?></a></li>
                <li><a href="<?php echo base_url('template'); ?>">
                        Template</a>
                </li>
                <li class="active">Edit Template
                    <!-- <?php echo $this->lang->line('add_customer_label'); ?> -->
                </li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Edit Template
                            <!-- <?php echo $this->lang->line('add_customer_header'); ?> -->
                        </h3>
                    </div>
                    <!-- /.box-header -->
                    <?php echo $this->session->flashdata('success'); ?>
                    <!-- <?php echo validation_errors(); ?> -->
                    <?php
                    if (isset($error))
                    {
                        echo $error;
                    }
                    ?>
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post" action="<?php echo base_url('template/update_template'); ?>">
                                <div class="panel-body">

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="hash_tag">Hash Tag (#)<span style="color:red;">*</span></label>
                                            <input type="hidden" name="id" value="<?php echo $template[0]->id; ?>">
                                            <input type="text" class="form-control" id="hash_tag" placeholder="#tag" name="hash_tag" value="<?php echo $template[0]->hash_tag; ?>" readonly>
                                            <span class="validation-color" id="err_hash_tag"><?php echo form_error('title'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="title">Title<span style="color:red;">*</span></label>
                                            <input type="text" class="form-control" placeholder="Title" id="title" name="title" value="<?php echo $template[0]->title; ?>">
                                            <span class="validation-color" id="err_title"><?php echo form_error('title'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="content">Content<span style="color:red;">*</span></label>
                                            <textarea class="form-control" id="content" name="content"><?php echo $template[0]->content; ?></textarea>
                                            <span class="validation-color" id="err_content"><?php echo form_error('content'); ?></span>
                                        </div>
                                    </div>

                                    <div class="panel-body pull-right">
                                        <p>
                                            <button class="btn btn-info btn-flat" id="add_template" type="submit">Update</button>
                                            <a href="<?php echo base_url('template'); ?>" class="btn btn-default btn-flat" type="button">Cancel</a>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('ledger/add_group_modal.php');
?>

<script>
    $(document).ready(function () {
        $("#add_template").click(function (event) {
            // var name_regex = /^[a-zA-Z\s]+$/;
            var hash_tag = $('#hash_tag').val();
            var title = $('#title').val();
            var content = $('#content').val();

            if (hash_tag == null || hash_tag == "")
            {
                $("#err_hash_tag").text("Please Enter Hash tag");
                return false;
            } else
            {
                $('#err_hash_tag').text("");
            }

            if (title == null || title == "")
            {
                $("#err_title").text("Please Enter Title");
                return false;
            } else
            {
                $('#err_title').text("");
            }

            if (content == null || content == "")
            {
                $("#err_content").text("Please Enter Content");
                return false;
            } else
            {
                $("#err_content").text("");
            }
        });
        // $("#hash_tag").on("blur keypress", function (event) {

        //     $('#hash_tag').val('#'+$(this).val());
        // });
    });
</script>
