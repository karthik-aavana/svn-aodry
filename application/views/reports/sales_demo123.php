<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>

<style type="text/css">
    .input-elements{color: #000}
    .drop-down-date{
        background: #ffffff;
        padding: 10px;
        position: absolute;
        top: 20%;
        left: 23%;
        z-index: 2;
        box-shadow: 4px -1px 6px 3px #ccc;
        /*display: none;  */
    }
    .drop-down-customer{
        background: #ffffff;
        padding: 10px;
        position: absolute;
        top: 20%;
        left: 1%;
        z-index: 2;
        box-shadow: 4px -1px 6px 3px #ccc;
        display: none;
    }
    .drop-down-invoice{
        background: #ffffff;
        padding: 10px;
        position: absolute;
        top: 25%;
        left: 15%;
        z-index: 2;
        box-shadow: 4px -1px 6px 3px #ccc;
        display: none;
    }


    .fa-times{
        padding-right: 10px
    }
    #display-dates,#display-customers,#display-invoice{
        color: #0a0a0a;
        margin-left: 28px;
        background: #fff;
        padding: 5px;}

</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    </section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Sales Reports</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <?php
            if ($this->session->flashdata('email_send') == 'success')
            {
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
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Sales Reports</h3>



         <!-- <select style="display: inline-block;float: right;width: 8%;margin-top: 10px" name="list_datatable_length" id="getRecordsDate" aria-controls="list_datatable" class="form-control input-sm">
          <option value="10">Select</option>
          <option value="25">Last 15 days(20)</option>
          <option value="50">Todays (5)</option>
          </select> -->

           <!-- <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('sales/recurrence_invoice_list'); ?>" title="Recurrence Invoice List">Recurrence Invoice List </a> -->

                    </div>
                    <div class="container">
                        <div id="search-options">

                        </div>
                    </div>

                    <!-- /.box-header  -->
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th style="min-width: 100px;text-align: center;">Customer <span id="display-customers" class="fa fa-angle-down" style="color: #000;margin-left: 25px"><span></th>
                                                <div class="drop-down-customer">
                                                    <select id="customer_id" name="customer_id" style="width: 200px">
                                                        <option> Select</option>

                                                        <?php
                                                        foreach ($customers as $customer)
                                                        {
                                                            # code...
                                                            ?>
                                                            <option><?= $customer->customer_name ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                               <!-- <th>Invoice</th> -->
                                                <th style="min-width: 120px;text-align: center;">Invoice Number <span id="display-invoice" class="fa fa-angle-down " style="color: #000;margin-left: 25px"></th>
                                                <div class="drop-down-invoice">
                                                    <select id="invoice_id" name="invoice_id" style="width: 200px">
                                                        <option> Select</option>
                                                        <?php
                                                        foreach ($invoices as $invoice)
                                                        {
                                                            # code...
                                                            ?>
                                                            <option><?= $invoice->sales_invoice_number ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <th style="min-width: 100px;text-align: center;"><?php echo 'Invoice Date'; ?><span id="display-dates" class="fa fa-angle-down " style="color: #000;margin-left: 25px"><span></th>
                                                            <div class="drop-down-date" style="display: none;">
                                                               <!-- <p>Default Date</p> -->
                                                                <input type="text" name="dates[]" style="background: #fff;"  id="column3_search">
                                                                <!-- <input type="text" name="dates" id="column3_search" di> -->
                                                                <p></p>
                                                            </div>
                                                            <th style="min-width: 100px;text-align: center;"><?php echo 'Invoice Amount'; ?></th>
                                                            <th style="min-width: 80px;text-align: center;"><?php echo 'Received Amount'; ?></th>
                                                            <th style="min-width: 100px;text-align: center;"><?php echo 'Status'; ?></th>
                                                            <th style="min-width: 100px;text-align: center;">Pending Amount</th>
                                                            <th style="min-width: 80px;text-align: center;">Receivable date</th>
                                                            <th style="min-width: 50px;text-align: center;"><?php echo 'Due'; ?></th>
                                                            <!-- <th style="min-width: 150px;text-align: center;"><?php echo ''; ?></th> -->
                                                            </tr>
                                                            </thead>
                                                            <?php
                                                            // print_r($posts);
                                                            foreach ($posts as $post)
                                                            {
                                                                print_r($posts);
                                                                ?>
                                                                <tbody>

             <!--  <tr>
                <td><?= $post->date ?></td>
                <td><?= $post->invoice ?></td>
                <td><?= $post->customer ?></td>
                <td><?= $post->grand_total ?></td>
                <td><?= $post->grand_total ?></td>
                <td><?= $post->grand_total ?></td>
                <td><?= $post->grand_total ?></td>
                <td><?= $post->grand_total ?></td>
                <td><?= $post->grand_total ?></td>
              </tr> -->

                                                                </tbody>
                                                                <?php
                                                            }
                                                            ?>
                                                            </table>





                                                            </div>
                                                            <!-- /.box-body -->
                                                            </div>
                                                            <!-- /.box -->
                                                            </div>
                                                            <!--/.col (right) -->
                                                            </div>
                                                            <!-- /.row -->
                                                            </section>

                                                            <div id="myModal" class="modal fade" role="dialog">
                                                                <div class="modal-dialog">

                                                                    <!-- Modal content-->
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                            <h4 class="modal-title">Update Date</h4>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form>
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

                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>

                                                            <!-- model ok  -->
                                                            <div id="myModal2" class="modal fade" role="dialog">
                                                                <div class="modal-dialog">

                                                                    <!-- Modal content-->
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


                                                            <!-- /.content -->
                                                            </div>
                                                            <!-- /.content-wrapper -->
                                                            <?php
                                                            $this->load->view('layout/footer');
// $this->load->view('sales/pay_now_modal');
                                                            $this->load->view('sales/pdf_type_modal');
// $this->load->view('advance_voucher/advance_voucher_modal');
                                                            $this->load->view('general/delete_modal');
                                                            $this->load->view('recurrence/recurrence_invoice_modal');
                                                            ?>

                                                            <script>
                                                                $(document).ready(function () {



                                                                    $('#list_datatable').DataTable({

                                                                    })




                                                                });










                                                            </script>

