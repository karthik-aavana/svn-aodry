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
                        <h3 class="box-title">Sales TDS Report - TDS receivab</h3>
                        <a class="btn btn-sm btn-info pull-right" href="#" >Add Expense Bill</a>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th><a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#collector">Deductor Name<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#collector">TAN<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#collector">TDS receivable u/s<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#collector">Rate of Tax<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#collector">Taxable Value<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#collector">TDS Amount<span class="fa fa-toggle-down mr-20"></span></a></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>17-08-2019</td>                                   
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
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Date</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="date">Date<span class="validation-color">*</span></label>
                            <input type="hidden" id="salesId" name="salesId" value="">
                            <input type="hidden" name="type" id="type" value="customer">
                            <input type="text" style="background: #fff;" class="form-control datepicker" id="invoice_date" name="invoice_date" readonly="">
                            <span class="validation-color" id="err_date"></span>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="date">Comments<span class="validation-color">*</span></label>

                            <textarea class="form-control" id="comments" name="comments"></textarea><br>
                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-info" id="post_notification_date" name="post_notification_date">
                                <span class="validation-color" id="err_date"></span>
                            </div>
                        </div>
                    </div>
                    </form>
                    <table id="follow_up_table" border="1" cellspacing ="5" class="custom_datatable table table-bordered table-striped table-hover table-responsive">
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="myModal2" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Status</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <strong>Success!</strong> Updated Follow Up date.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Date</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="date">Date<span class="validation-color">*</span></label>
                            <input type="hidden" id="salesId" name="salesId" value="">
                            <input type="hidden" name="type" id="type" value="customer">
                            <input type="text" style="background: #fff;" class="form-control datepicker" id="invoice_date" name="invoice_date" readonly="">
                            <span class="validation-color" id="err_date"></span>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="date">Comments<span class="validation-color">*</span></label>
                            <textarea class="form-control" id="comments" name="comments"></textarea><br>
                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-info" id="post_notification_date" name="post_notification_date">
                                <span class="validation-color" id="err_date"></span>
                            </div>
                        </div>
                    </div>
                    <table id="follow_up_table" border="1" cellspacing ="5" class="custom_datatable table table-bordered table-striped table-hover table-responsive">
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
    <div id="myModal2" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Status</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <strong>Success!</strong> Updated Follow Up date.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php 
$this->load->view("layout/footer");
$this->load->view("gst_reports/date_deduction");
$this->load->view("gst_reports/deductee_name");
$this->load->view("gst_reports/pan_card");
$this->load->view("gst_reports/rate_tax");
$this->load->view("gst_reports/taxable_value");
$this->load->view("gst_reports/tds_payable");
$this->load->view("gst_reports/tds_amount");
?>
<script type="">
    $("#list_datatable").dataTable({
        "ordering": false
    });
</script>