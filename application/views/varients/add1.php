<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');
?>

<style type="text/css">
    .add-more>.fa,.removeDiv>.fa{font-size: 20px;  }
    .add-more,.removeDiv{color: #0177a9 !important;cursor: pointer;}
    .add-more{margin-top: 25px}
    .mt-25{margin-top: 25px}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
                <li><a href="<?php echo base_url('varients'); ?>">Varients</a></li>
                <li class="active">Add Varients</li>
            </ol>
        </h5>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Varients</h3>
                        <span class="btn btn-default pull-right" id="sale_cancel" onclick="cancel('refund_voucher')">Back</span>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post" action="<?php echo base_url('Varients/add_varients'); ?>">
                                <div class="col-md-12">
                                    <div class="well">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="date">Varient Key<span class="validation-color">*</span></label>
                                                    <input type="text" class="form-control" id="varient_key" name="varient_key[]" value="" placeholder="ex : Color,Size etc">
                                                    <span class="validation-color" id="err_voucher_date"></span>

                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="date">Varient Value<span class="validation-color">*</span></label>
                                                    <input type="text" class="form-control" id="varient_value" multiple="" name="varient_value[]" value="" placeholder="red,blue">
                                                    <span class="validation-color" id="err_voucher_date"></span>

                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <p class="add-more" id="add_more"><i class="fa fa-files-o" aria-hidden="true"> </i> Add More</p>
                                            </div>


                                        </div>
                                        <div id="newRows">

                                        </div>


                                    </div>
                                    <div class="col-sm-12">
                                        <div class="box-footer">
                                            <button type="submit" id="sales_submit" name="submit" value="add" class="btn btn-info">Add</button>

                                            <span class="btn btn-default" id="sale_cancel" onclick="cancel('sales')">Cancel</span>
                                        </div>
                                    </div>
                                </div>

                        </div>
                    </div>




                </div>
                </form>
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
?>

<script type="text/javascript">
    $(document).ready(function () {
        $('#add_more').click(function () {


            var addVarients = "<div class='row mt-25'><div class='col-sm-3'><input name='varient_key[]' type = 'text' class='form-control'></div><div class='col-sm-3'><input name='varient_value[]' type = 'text' class='form-control'></div><div class='col-sm-3'><p class='removeDiv'><i class='fa fa-trash' aria-hidden='true'></i> Remove</p></div><br></div>";

            $('#newRows').append(addVarients);

        })

        $('body').on('click', '.removeDiv', function () {
            $(this).closest('.row').remove();
        })
    })
</script>

