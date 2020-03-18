<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Expense TDS Report</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <?php
            if ($this->session->flashdata('email_send') == 'success') {
                ?>
                <div class="col-sm-12">
                    <div class="alert alert-success">
                        <button class="close" data-dismiss="alert" type="button">×</button>
                        Email has been send with the attachment.
                        <div class="alerts-con"></div>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Pur & Exp TCS - TCS receivable</h3>
                        <a class="btn btn-sm btn-info pull-right" href="#" >Add</a>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>                               
                                 <tr>
                                    <th><a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#collector">Collector Name<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#tan_card">TAN<span class="fa fa-toggle-down mr-20"></span></a> </th>
                                    <th><a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#tcs_payable">TCS Payable u/s<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#rate_tax">Rate of Tax<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#taxable_value">Taxable Value<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#tcs_amount">TCS Amount<span class="fa fa-toggle-down mr-20"></span></a></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Sachin T</td>
                                    <td>DSWK02G4512</td>
                                    <td>891220.00</td>
                                    <td>18%</td>
                                    <td>890000.00</td>
                                    <td>5000.00</td>
                                </tr>
                                <tr>                                  
                                    <td>Aavana </td>
                                    <td>DSWK02G4512</td>
                                    <td>891220.00</td>
                                    <td>18%</td>
                                    <td>890000.00</td>
                                    <td>5000.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php 
$this->load->view("layout/footer");
$this->load->view("gst_reports/collector");
$this->load->view("gst_reports/tan_card");
$this->load->view("gst_reports/rate_tax");
$this->load->view("gst_reports/taxable_value");
$this->load->view("gst_reports/tcs_payable");
$this->load->view("gst_reports/tcs_amount");
?>
<script type="">
    $("#list_datatable").dataTable({
        "ordering": false
    });
</script>