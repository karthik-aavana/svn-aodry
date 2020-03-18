<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// $p = array('admin', 'sales_person', 'manager');

// if (!(in_array($this->session->userdata('type'), $p))) {

//     redirect('auth');

// }

$this->load->view('layout/header');

?>

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <h5>

            <ol class="breadcrumb">

                <li>

                    <a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>DashBoard</a>

                </li>

                <li class="active">

                    Report

                </li>

            </ol></h5>

    </section>

    <section class="content">

        <div class="row">

            <?php

            if ($message = $this->session->flashdata('message')) {

                ?>

                <div class="col-sm-12">

                    <div class="alert alert-success">

                        <button class="close" data-dismiss="alert" type="button">

                            ×

                        </button>

                        <?php echo $message; ?>

                        <div class="alerts-con"></div>

                    </div>

                </div>

            <?php } ?>

            <div class="col-md-12">

                <div class="box">

                    <form target="" id="advance_receipts" name="advance_receipts" method="post" action="<?php echo base_url('report/report_download'); ?>">

                        <div class="box-header with-border">

                            <h3 class="box-title">Sales GST Report</h3>

                        </div>

                        <div class="box-body">

                            <div class="row">

                                <div class="col-sm-3">

                                    <div class="form-group">

                                        <div class="input-group date">

                                            <input type="text" name="from_date" value="" class="form-control datepicker" id="from_date" placeholder="From Date" autocomplete="off">

                                            <div class="input-group-addon">

                                                <i class="fa fa-calendar"></i>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-sm-3">

                                    <div class="form-group">

                                        <div class="input-group date">

                                            <input type="text" name="to_date" value="" class="form-control datepicker" id="to_date" placeholder="To date" autocomplete="off">

                                            <div class="input-group-addon">

                                                <i class="fa fa-calendar"></i>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>							

                            <div class="row">

                                <div class="col-sm-12">

                                    <input id="report_month"  name="report_month" type="hidden">

                                    <button type="submit" class="btn btn-info btn-sm" align="right" id="adv_xl" name="submit">

                                        Advance Receipts XL

                                    </button>

                                    <button type="submit" class="btn btn-info btn-sm" align="right" id="b2b_xl" name="submit">

                                        B2B Receipts XL

                                    </button>

                                    <button type="submit" class="btn btn-info btn-sm" align="right" id="b2cl_xl" name="submit">

                                        B2CL Receipts XL

                                    </button>

                                    <button type="submit" class="btn btn-info btn-sm" align="right" id="b2cs_xl" name="submit">

                                        B2CS Receipts XL

                                    </button>

                                    <button type="submit" class="btn btn-info btn-sm" align="right" id="cdnr_xl" name="submit">

                                        CDNR Receipts XL

                                    </button>

                                    <button type="submit" class="btn btn-info btn-sm" align="right" id="cdnur_xl" name="submit">

                                        CDNUR Receipts XL

                                    </button>

                                    <button type="submit" class="btn btn-info btn-sm" align="right" id="hsn_xl" name="submit">

                                        HSN Receipts XL

                                    </button>

                                </div>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </section>

</div>

<?php $this->load->view('layout/footer'); ?>

<script type="text/javascript">

    $(document).ready(function () {

        $('#pdf').click(function () {

            $('form').attr('target', '_blank');

        });

        $('#csv').click(function () {

            $('form').attr('target', '_blank');

        });

        $('#print').click(function () {

            $('form').attr('target', '_blank');

        });

        $('#submit').click(function () {

            $('form').attr('target', '');

        });

    });

    $("#hide1").click(function () {

        $(".hide1").toggle();

    });

</script>