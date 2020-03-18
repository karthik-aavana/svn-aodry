<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');

?>

<div class="content-wrapper">

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

                    <div class="box-header with-border">

                        <h3 class="box-title">Sales GST Report</h3>

                    </div>

                    <div class="box-body">

                        <div class="row">

                            <div class="col-md-3 col-sm-6 col-xs-12">

                                <a href="<?php echo base_url('sales_gst_report/expense_tds_report') ?>">

                                    <div class="info-box bg-aqua">

                                        <span class="info-box-icon"><i class="fa fa-bookmark-o"></i></span>

                                        <div class="info-box-content">

                                            <span class="info-box-text">Expense TDS Report-TDS payable</span>

                                            <span class="info-box-number">41,410</span>                                      

                                        </div>                                    

                                    </div>

                                </a>                               

                            </div>                            

                            <div class="col-md-3 col-sm-6 col-xs-12">

                                <a href="<?php echo base_url('sales_gst_report/sales_tcs_report') ?>">

                                    <div class="info-box bg-green">

                                        <span class="info-box-icon"><i class="fa fa-thumbs-o-up"></i></span>

                                        <div class="info-box-content">

                                            <span class="info-box-text">Sales TCS Report-TCS Payable</span>

                                            <span class="info-box-number">41,410</span>                                       

                                        </div>                                   

                                    </div> 

                                </a>

                            </div>                            

                            <div class="col-md-3 col-sm-6 col-xs-12">

                                <a href="<?php echo base_url('sales_gst_report/sales_tds_report') ?>">

                                    <div class="info-box bg-yellow">

                                        <span class="info-box-icon"><i class="fa fa-book"></i></span>

                                        <div class="info-box-content">

                                            <span class="info-box-text">Sales TDS Report - TDS receivab</span>

                                            <span class="info-box-number">41,410</span>                                       

                                        </div>                                   

                                    </div> 

                                </a>

                            </div>                            

                            <div class="col-md-3 col-sm-6 col-xs-12">

                                <a href="<?php echo base_url('sales_gst_report/purchase_expense_tcs_report') ?>">

                                    <div class="info-box bg-red">

                                        <span class="info-box-icon"><i class="fa fa-comments-o"></i></span>

                                        <div class="info-box-content">

                                            <span class="info-box-text">Pur & Exp TCS - TCS receivab</span>

                                            <span class="info-box-number">41,410</span>                                       

                                        </div>

                                    </div>

                                </a>

                            </div>

                        </div>  

                    </div>

                </div>

                <hr>

                <div class="box box-primary">

                    <div class="box-header with-border">

                        <h3 class="box-title">GST Reports</h3>

                    </div>

                    <div class="box-body">

                        <div class="table-responsive">

                            <table id="report_table" class="table table-bordered table-striped table-hover dataTable no-footer">

                                <thead>

                                    <tr>

                                        <th>Voucher No</th>

                                        <th>Customer Name</th>

                                        <th>GSTIN</th>

                                        <th>Invoice Number</th>

                                        <th>Invoice Date</th>

                                        <th>Invoice Value</th>

                                        <th>Taxable Value</th>

                                        <th>HSN/SAC No</th>

                                        <th>Quantity</th>

                                        <th>UOM</th>

                                        <th>Rate</th>

                                        <th>Place of Supply</th>

                                        <th>Rate</th>

                                        <th>CGST</th>

                                        <th>SGST</th>

                                        <th>IGST</th>

                                        <th>Cess</th>

                                    </tr>

                                </thead>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>

<?php $this->load->view('layout/footer'); ?>

<script type="text/javascript">

    $(document).ready(function () {



        $("#report_table").dataTable({

             "ordering": false, 

             "scrollX": true

        });



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