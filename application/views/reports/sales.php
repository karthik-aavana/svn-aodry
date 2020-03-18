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

    .mr-20{margin-left: 10px}



    .fa-times{

        padding-right: 10px

    }

    #display-dates,#display-customers,#display-invoice{

        color: #0a0a0a;

        margin-left: 28px;

        background: #fff;

        padding: 5px;}





    .select2-container {

        width: 100% !important;

    }









</style>

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

    </section>

    <div class="fixed-breadcrumb">

        <ol class="breadcrumb abs-ol">

            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>

            <li><a href="<?php echo base_url('report/all_reports'); ?>">Report</a></li>

            <li class="active">Sales Report</li>

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

                        <h3 class="box-title">Sales Report</h3>





         <!-- <select style="display: inline-block;float: right;width: 8%;margin-top: 10px" name="list_datatable_length" id="getRecordsDate" aria-controls="list_datatable" class="form-control input-sm">

          <option value="10">Select</option>

          <option value="25">Last 15 days(20)</option>

          <option value="50">Todays (5)</option>

          </select> -->



           <!-- <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('sales/recurrence_invoice_list'); ?>" title="Recurrence Invoice List">Recurrence Invoice List </a> -->



                    </div>



                    <!-- /.box-header  -->

                    <div class="box-body">

                        <!-- <h5>There are <?php echo count($currency_converted_records); ?> records which currency has not converted yet. Here only converted records are displaying.</h5> -->

                        <h5><?php if (count($currency_converted_records) > 0)

            {

                ?>There are <?php echo count($currency_converted_records); ?> records which currency has not converted yet. <?php } ?>Here only converted records are displaying.</h5>

                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >

                            <thead>

                                <tr>

                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Invoice Date" data-type="invoice_date">Invoice Date <span class="fa fa-toggle-down mr-20"></span></a></th>



           <!-- <th>Invoice</th> -->

                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Invoice Number" data-type="invoice">Invoice Number<span class="fa fa-toggle-down mr-20"></span></a></th>



                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Customer" data-type="customer">Customer<span class="fa fa-toggle-down mr-20"></span></a></th>



                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Customer Gst Number" data-type="customer_gstin_number">Customer GST<span class="fa fa-toggle-down mr-20"></span></a></th>



                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Customer Address" data-type="customer_address">Customer Address<span class="fa fa-toggle-down mr-20"></span></a></th>



                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Nature of Supply" data-type="nature_of_supply">Nature of Supply<span class="fa fa-toggle-down mr-20"></span></a></th>



                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Shipping Address" data-type="shipping_address">Shipping Address<span class="fa fa-toggle-down mr-20"></span></a></th>



                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Place of Supply" data-type="place_of_supply">Place of Supply<span class="fa fa-toggle-down mr-20"></span></a></th>



                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Type of Supply" data-type="type_of_supply">Type of Supply<span class="fa fa-toggle-down mr-20"></span></a></th>



                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select GST Payable" data-type="gst_payable">GST Payable<span class="fa fa-toggle-down mr-20"></span></a></th>



                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select E-Way Bill" data-type="e_way_bill">E-Way<span class="fa fa-toggle-down mr-20"></span></a></th>



                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Taxable Amount" data-type="taxable_amount">Taxable Amount<span class="fa fa-toggle-down mr-20"></span></a></th>



                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select IGST Amount" data-type="igst_amount">IGST Amount<span class="fa fa-toggle-down mr-20"></span></a></th>



                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select CGST Amount" data-type="cgst_amount">CGST Amount<span class="fa fa-toggle-down mr-20"></span></a></th>



                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select SGST Amount" data-type="sgst_amount">SGST Amount<span class="fa fa-toggle-down mr-20"></span></a></th>





                                    <th style="min-width: 100px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Invoice Amount" data-type="invoice_amount">Invoice Amount <span class="fa fa-toggle-down mr-20"></span></a></th>



                                </tr>

                            </thead>

                            <tbody></tbody>

                           <!--  <tfoot>

                                <tr>

                                    <td id="total_list_record1"></td>

                                    <td id="total_list_record2"></td>

                                    <td id="total_list_record3"></td>

                                    <td id="total_list_record4"></td>

                                    <td id="total_list_record5"></td>

                                    <td id="total_list_record6"></td>

                                    <td id="total_list_record7"></td>

                                    <td id="total_list_record8"></td>

                                    <td id="total_list_record9"></td>



                                </tr>



                            </tfoot> -->

                        </table>

            <!-- <b>Grand Total : <?= $total_sum[0]->sum_of_grand_total; ?></b><br>

            <b>Paid Amount : <?= $total_sum[0]->sales_paid_amount; ?></b><br>

            <b>Pending Amount Amount : <?= $total_sum[0]->pending_amount; ?></b><br>

            <b>Total Converted amount : <?= $coverted_total; ?></b> -->

                    </div>

                    <!-- /.box-body -->

                </div>

                <!-- /.box -->

            </div>

            <!--/.col (right) -->



        </div>

        <!-- /.row -->

    </section>



    <!-- /.content -->

</div>

<!-- /.content-wrapper -->



<!-- Modals starts -->

<!-- Modal -->

<div class="modal fade" id="distinct-modal" role="dialog">

    <div class="modal-dialog">



        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title"><span id="modal-title"></span></h4>

            </div>

            <form id="form-filter" class="">

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-12 hide">

                            <div class="form-group">

                                <select id="modal-select_type"  name="modal-select_type">

                                    <option value="">select</option>

                                    <option value="customer">Customer</option>

                                    <option value="invoice">Invoice</option>

                                    <option value="invoice_date">Invoice Date</option>

                                    <option value="customer_gstin_number">Customer GST</option>

                                    <option value="customer_address">Customer Address</option>

                                    <option value="nature_of_supply">Nature of Supply</option>

                                    <option value="shipping_address">Shipping Address</option>

                                    <option value="place_of_supply">Place of Supply</option>

                                    <option value="type_of_supply">Type of Supply</option>

                                    <option value="gst_payable">GST Payable</option>

                                    <option value="e_way_bill">E-Way Bill</option>

                                    <option value="taxable_amount">Taxable Amount</option>

                                    <option value="igst_amount">IGST Amount</option>

                                    <option value="cgst_amount">CGST Amount</option>

                                    <option value="sgst_amount">SGST Amount</option>

                                    <option value="invoice_amount">Invoice Amount</option>



                                </select>

                            </div>

                        </div>



                        <div class="col-md-12" id="customer_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_customer_name"  name="filter_customer_name">

                                    <option value="" >Select Customer</option>

                                    <?php

                                    foreach ($customers as $key => $value)

                                    {

                                        ?>

                                        <option value="<?= $value->customer_id ?>"><?= $value->customer_name ?></option>

                                        <?php

                                    }

                                    ?>



                                </select>

                                 <!--  <input type="text" name="filter_customer_name" value="" class="form-control" id="filter_customer_name" placeholder="Customer Name"> -->

                            </div>

                        </div>

                        <div class="col-md-12" id="invoice_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_invoice_number"  name="filter_invoice_number">

                                    <option value="" >Select Invoice Number</option>

                                    <?php

                                    foreach ($invoices as $key => $value)

                                    {

                                        ?>

                                        <option value="<?= $value->sales_id ?>"><?= $value->sales_invoice_number ?></option>

                                        <?php

                                    }

                                    ?>



                                </select>



                            </div>

                        </div>

                        <div class="col-md-12" id="from_date_div">

                            <div class="form-group">

                                <input type="text" name="filter_from_date" value="" class="form-control datepicker" id="filter_from_date" placeholder="From Date">

                            </div>

                        </div>

                        <div class="col-md-12" id="to_date_div">

                            <div class="form-group">

                                <input type="text" name="filter_to_date" value="" class="form-control datepicker" id="filter_to_date" placeholder="End date">

                            </div>

                        </div>



                        <div class="col-md-12" id="customer_gstin_number_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_customer_gstin_number"  name="filter_customer_gstin_number">

                                    <option value="" >Select Customer GST Number</option>

                                    <option value="unregistered">Not Registerd</option>

                                    <?php

                                    foreach ($customers as $key => $value)

                                    {

                                        if ($value->customer_gstin_number != "")

                                        {

                                            ?>

                                            <option value="<?= $value->customer_gstin_number ?>"><?= $value->customer_gstin_number ?></option>

                                            <?php

                                        }

                                    }

                                    ?>



                                </select>





                            </div>

                        </div>



                        <div class="col-md-12" id="customer_address_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_customer_address"  name="filter_customer_address">

                                    <option value="" >Select Customer Address</option>



                                    <?php

                                    foreach ($customers as $key => $value)

                                    {

                                        if ($value->customer_address != "")

                                        {

                                            ?>

                                            <option value="<?= $value->customer_address ?>"><?= $value->customer_address ?></option>

                                            <?php

                                        }

                                    }

                                    ?>



                                </select>





                            </div>

                        </div>



                        <div class="col-md-12" id="nature_of_supply_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_nature_of_supply"  name="filter_nature_of_supply">

                                    <option value="" >Select Nature of Supply</option>



                                    <?php

                                    foreach ($sales as $key => $value)

                                    {

                                        if ($value->sales_nature_of_supply != "")

                                        {

                                            ?>

                                            <option value="<?= $value->sales_nature_of_supply ?>"><?= $value->sales_nature_of_supply ?></option>

                                            <?php

                                        }

                                    }

                                    ?>



                                </select>





                            </div>

                        </div>



                        <div class="col-md-12" id="shipping_address_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_shipping_address"  name="filter_shipping_address">

                                    <option value="" >Select Shipping Address</option>



                                    <?php

                                    foreach ($sales as $key => $value)

                                    {

                                        if ($value->shipping_address_name != "")

                                        {

                                            ?>

                                            <option value="<?= $value->shipping_address_name ?>"><?= $value->shipping_address_name ?></option>

                                            <?php

                                        }

                                    }

                                    ?>



                                </select>





                            </div>

                        </div>



                        <div class="col-md-12" id="place_of_supply_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_place_of_supply"  name="filter_place_of_supply">

                                    <option value="" >Select Place of Supply</option>



                                    <?php

                                    foreach ($sales as $key => $value)

                                    {

                                        if ($value->place_of_supply != "")

                                        {

                                            ?>

                                            <option value="<?= $value->place_of_supply ?>"><?= $value->place_of_supply ?></option>

                                            <?php

                                        }

                                    }

                                    ?>



                                </select>





                            </div>

                        </div>



                        <div class="col-md-12" id="type_of_supply_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_type_of_supply"  name="filter_type_of_supply">

                                    <option value="" >Select Place of Supply</option>



                                    <?php

                                    foreach ($sales as $key => $value)

                                    {

                                        if ($value->sales_type_of_supply != "")

                                        {

                                            ?>

                                            <option value="<?= $value->sales_type_of_supply ?>"><?= $value->sales_type_of_supply ?></option>

                                            <?php

                                        }

                                    }

                                    ?>



                                </select>



                            </div>

                        </div>



                        <div class="col-md-12" id="gst_payable_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_gst_payable"  name="filter_gst_payable">

                                    <option value="" >Select Place of Supply</option>



                                    <?php

                                    foreach ($sales as $key => $value)

                                    {

                                        if ($value->sales_gst_payable != "")

                                        {

                                            ?>

                                            <option value="<?= $value->sales_gst_payable ?>"><?= $value->sales_gst_payable ?></option>

                                            <?php

                                        }

                                    }

                                    ?>



                                </select>



                            </div>

                        </div>



                        <div class="col-md-12" id="e_way_bill_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_e_way_bill"  name="filter_e_way_bill">

                                    <option value="" >Select Place of Supply</option>



                                    <?php

                                    foreach ($sales as $key => $value)

                                    {

                                        if ($value->sales_e_way_bill != "")

                                        {

                                            ?>

                                            <option value="<?= $value->sales_e_way_bill ?>"><?= $value->sales_gst_payable ?></option>

                                            <?php

                                        }

                                    }

                                    ?>



                                </select>



                            </div>

                        </div>



                        <div class="col-md-12" id="invoice_amount_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_invoice_amount"  name="filter_invoice_amount">

                                    <option value="" >Select Invoice Amount</option>

                                    <?php

                                    foreach ($invoice_amount as $key => $value)

                                    {

                                        ?>

                                        <option value="<?= $value->sales_invoice_amount ?>"><?= $value->sales_invoice_amount ?></option>

                                        <?php

                                    }

                                    ?>



                                </select>





                            </div>

                        </div>

                        <div class="col-md-12" id="received_amount_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_received_amount"  name="filter_received_amount">

                                    <option value="" >Select Received Amount</option>

                                    <?php

                                    foreach ($received_amount as $key => $value)

                                    {

                                        ?>

                                        <option value="<?= $value->sales_paid_amount ?>"><?= $value->sales_paid_amount ?></option>

                                        <?php

                                    }

                                    ?>



                                </select>



                            </div>

                        </div>



                        <div class="col-md-12" id="payment_status_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_payment_status"  name="filter_payment_status">

                                    <option value="" >Select Payment Status</option>



                                    <option value="pending">Pending</option>

                                    <option value="partial">Partial</option>

                                    <option value="completed">Completed</option>

                                </select>

                            </div>

                        </div>

                        <div class="col-md-12" id="pending_amount_div">

                            <div class="form-group">

                                <select class="select2" multiple="multiple"  id="filter_pending_amount"  name="filter_pending_amount">

                                    <option value="" >Select Pending Amount</option>

                                    <?php

                                    foreach ($pending_amount as $key => $value)

                                    {

                                        ?>

                                        <option value="<?= $value->sales_pending_amount ?>"><?= $value->sales_pending_amount ?></option>

                                        <?php

                                    }

                                    ?>



                                </select>



                            </div>

                        </div>

                        <div class="col-md-12" id="receivable_date_div">

                            <div class="form-group">

                                <input type="text" name="filter_receivable_date" value="" class="form-control datepicker" id="filter_receivable_date" placeholder="Receivable Date">

                            </div>

                        </div>



                        <div class="col-md-12" id="due_day_div">

                            <div class="form-group">



                                <select class="select2" multiple="multiple"  id="filter_due"  name="filter_due">

                                    <option value="" >Select Due Day</option>

                                    <?php

                                    foreach ($due_day as $key => $value)

                                    {

                                        ?>

                                        <option value="<?= $value->due_day ?>"><?= $value->due_day ?></option>

                                        <?php

                                    }

                                    ?>



                                </select>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button name="filter_search" type="button" class="btn btn-primary" id="filter_search" value="filter" data-dismiss="modal">Apply</button>

                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    <button type="reset" class="btn btn-danger" id="reset-all">Reset All</button>

                    <button type="button" class="btn btn-warning" id="reset-customer">Reset Customer</button>

                    <button type="button" class="btn btn-warning" id="reset-invoice">Reset Invoice Number</button>

                    <button type="button" class="btn btn-warning" id="reset-in-date">Reset Invoice Date</button>

                    <button type="button" class="btn btn-warning" id="reset-in-amt">Reset Amount</button>

                    <button type="button" class="btn btn-warning" id="reset-rec-amt">Recived Amount</button>

                    <button type="button" class="btn btn-warning" id="reset-status">Reset Payment Status</button>

                    <button type="button" class="btn btn-warning" id="reset-amt">Reset Pending Amount</button>

                </div>

            </form>

        </div>



    </div>

</div>



</div>





<!-- modals ends -->

<?php

$this->load->view('layout/footer');

// $this->load->view('sales/pay_now_modal');

$this->load->view('sales/pdf_type_modal');

// $this->load->view('advance_voucher/advance_voucher_modal');

$this->load->view('general/delete_modal');

$this->load->view('recurrence/recurrence_invoice_modal');

?>



<script>

    $(document).on('click', '#reset-all', function () {

        // alert()



        $('#filter_customer_name option:selected').prop("selected", false)

        $('#filter_invoice_number option:selected').prop("selected", false)

        $('#filter_from_date option:selected').prop("selected", false)

        $('#filter_to_date option:selected').prop("selected", false)

        $('#filter_payment_status option:selected').prop("selected", false)

        $('#filter_received_amount option:selected').prop("selected", false)

        $('#filter_invoice_amount option:selected').prop("selected", false)

        $('.select2-selection__rendered').empty();



    })



    $(document).on("click", ".open_distinct_modal", function ()

    {

        var title = $(this).data("title");

        var type = $(this).data("type");

        $(".modal-header #modal-title").html(title);

        $(".modal-body #modal-select_type").val(type);

        $('#modal-select_type').change();





    });





    var table;

    $(document).ready(function () {



        $("#reset-customer").click(function () {

            $("#customer_div .select2-selection__rendered").empty();

            $('#filter_customer_name option:selected').prop("selected", false)

        })

        $("#reset-invoice").click(function () {

            $("#invoice_div .select2-selection__rendered").empty();

            $('#filter_invoice_number option:selected').prop("selected", false)

        })

        $("#reset-in-date").click(function () {

            $('#filter_from_date').val('');

            $('#filter_to_date').val('');

        })

        $('#reset-in-amt').click(function () {

            $("#invoice_amount_div .select2-selection__rendered").empty();

            $('#filter_invoice_amount option:selected').prop("selected", false)

        })

        $('#reset-rec-amt').click(function () {

            $("#received_amount_div .select2-selection__rendered").empty();

            $('#filter_received_amount option:selected').prop("selected", false)

        })

        $('#reset-status').click(function () {

            $("#payment_status_div .select2-selection__rendered").empty();

            $('#filter_payment_status option:selected').prop("selected", false)

        })

        $('#reset-amt').click(function () {

            $("#pending_amount_div .select2-selection__rendered").empty();

            $('#filter_pending_amount option:selected').prop("selected", false)

        })





        $("#modal-select_type").on("change", function (event)

        {

            var type = $('#modal-select_type').val();





            if (type == "")

            {



                $("#customer_div").hide();

                $("#invoice_div").hide();

                $("#from_date_div").hide();

                $("#to_date_div").hide();

                $("#invoice_amount_div").hide();

                $("#received_amount_div").hide();

                $("#payment_status_div").hide();

                $("#pending_amount_div").hide();

                $("#receivable_date_div").hide();

                $("#due_day_div").hide();



            }

            if (type == "customer")

            {



                $("#customer_div").show();





            } else

            {

                $("#customer_div").hide();

            }

            if (type == "invoice")

            {

                $("#invoice_div").show();

                $('#reset-invoice').show();

            } else

            {

                $("#invoice_div").hide();

                $('#reset-invoice').hide();

            }

            if (type == "invoice_amount")

            {

                $("#invoice_amount_div").show();

                $("#reset-in-amt").show();

            } else

            {

                $("#invoice_amount_div").hide();

                $("#reset-in-amt").hide();

            }



            if (type == "invoice_date")

            {

                $("#from_date_div").show();

                $("#to_date_div").show();

                $("#reset-in-date").show();

            } else

            {

                $("#from_date_div").hide();

                $("#to_date_div").hide();

                $("#reset-in-date").hide();

            }



            if (type == "received_amount")

            {

                $("#received_amount_div").show();

                $("#reset-rec-amt").show();

            } else

            {

                $("#received_amount_div").hide();

                $("#reset-rec-amt").hide();

            }



            if (type == "payment_status")

            {

                $("#payment_status_div").show();

                $("#reset-status").show();

            } else

            {

                $("#payment_status_div").hide();

                $("#reset-status").hide();

            }



            if (type == "pending_amount")

            {

                $("#pending_amount_div").show();

                $("#reset-amt").show();



            } else

            {

                $("#pending_amount_div").hide();

                $("#reset-amt").hide();

            }



            if (type == "receivable_date")

            {

                $("#receivable_date_div").show();

            } else

            {

                $("#receivable_date_div").hide();

            }



            if (type == "due_day")

            {

                $("#due_day_div").show();

            } else

            {

                $("#due_day_div").hide();

            }



            if (type == "customer")

            {

                $("#reset-customer").show();

            } else

            {

                $("#reset-customer").hide();

            }



        }).change();



        generateOrderTable();

        function generateOrderTable() {



            $('#list_datatable').DataTable({

                "processing": true,

                "serverSide": true,

                "responsive": true,

                "ajax": {

                    "url": base_url + "report/sales",

                    "dataType": "json",

                    "type": "POST",

                    "data": {

                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',

                        'filter_customer_name': $('#filter_customer_name').val(),

                        'filter_invoice_number': $('#filter_invoice_number').val(),

                        'filter_from_date': $('#filter_from_date').val(),

                        'filter_to_date': $('#filter_to_date').val(),

                        'filter_invoice_amount': $('#filter_invoice_amount').val(),

                        'filter_received_amount': $('#filter_received_amount').val(),

                        'filter_pending_amount': $('#filter_pending_amount').val(),

                        'filter_payment_status': $('#filter_payment_status').val(),

                        'filter_receivable_date': $('#filter_receivable_date').val(),

                        'filter_due': $('#filter_due').val()

                    },

                    "dataSrc": function (result) {

                        if (result.total_list_record[0].tot_sales_taxable_value != 0)

                        {

                            var tfoot = 'Taxable Amount: <br>\r\n<b>' + parseFloat(result.total_list_record[0].tot_sales_taxable_value).toFixed(2) + '</b><br>\n\r\n';

                            $('#total_list_record1').html(tfoot);

                        }

                        tfoot = "";

                        if (result.total_list_record[0].tot_sales_igst_amount != 0)

                        {

                            tfoot += 'IGST Amount: <br>\r\n<b>' + parseFloat(result.total_list_record[0].tot_sales_igst_amount).toFixed(2) + '</b>, <br>\n\r\n';



                        }

                        if (result.total_list_record[0].tot_sales_cgst_amount != 0)

                        {

                            tfoot += 'CGST Amount: <br>\r\n<b>' + parseFloat(result.total_list_record[0].tot_sales_cgst_amount).toFixed(2) + '</b>, <br>\n\r\n';



                        }

                        if (result.total_list_record[0].tot_sales_sgst_amount != 0)

                        {

                            tfoot += 'SGST Amount: <br>\r\n<b>' + parseFloat(result.total_list_record[0].tot_sales_sgst_amount).toFixed(2) + '</b><br>\n\r\n';



                        }

                        $('#total_list_record2').html(tfoot);





                        tfoot = "";

                        if (result.total_list_record[0].tot_sales_grand_total != 0)

                        {

                            tfoot += 'Invoice Amount: <br>\r\n<b>' + parseFloat(result.total_list_record[0].tot_sales_grand_total).toFixed(2) + '</b><br>\n\r\n';





                        }



                        if (result.total_list_record[0].tot_credit_note_amount != 0)

                        {

                            tfoot += 'Credit Note Amount: <br>\r\n<b>' + parseFloat(result.total_list_record[0].tot_credit_note_amount).toFixed(2) + '</b>, <br>\n\r\n';



                        }

                        if (result.total_list_record[0].tot_debit_note_amount != 0)

                        {

                            tfoot += 'Debit Note Amount: <br>\r\n<b>' + parseFloat(result.total_list_record[0].tot_debit_note_amount).toFixed(2) + '</b><br>\n\r\n';



                        }

                        $('#total_list_record3').html(tfoot);



                        if (result.total_list_record[0].tot_grand_total != 0)

                        {

                            tfoot = 'Grand Total Amount: <br>\r\n<b>' + parseFloat(result.total_list_record[0].tot_grand_total).toFixed(2) + '</b><br>';

                            $('#total_list_record4').html(tfoot);



                        }



                        tfoot = ""

                        if (result.total_list_record[0].tot_sales_paid_amount != 0)

                        {

                            tfoot += 'Received Amount: <br>\r\n<b>' + parseFloat(result.total_list_record[0].tot_sales_paid_amount).toFixed(2) + '</b><br>\n\r\n';





                        }

                        if (result.total_list_record[0].tot_sales_pending_amount != 0)

                        {

                            tfoot += 'Pending Amount: <br>\r\n<b>' + parseFloat(result.total_list_record[0].tot_sales_pending_amount).toFixed(2) + '</b><br>';



                        }

                        $('#total_list_record5').html(tfoot);





                        return result.data;

                    }

                },

                "columns": [

                    {"data": "date"},

                    {"data": "invoice"},

                    {"data": "customer"},

                    {"data": "customer_gstin_number"},

                    {"data": "customer_address"},

                    {"data": "nature_of_supply"},

                    {"data": "shipping_address"},

                    {"data": "place_of_supply"},

                    {"data": "type_of_supply"},

                    {"data": "gst_payable"},

                    {"data": "e_way_bill"},

                    {"data": "taxable_amount"},

                    {"data": "igst_amount"},

                    {"data": "cgst_amount"},

                    {"data": "sgst_amount"},

                    {"data": "invoice_amount"},

                ],



                "columnDefs": [{

                        "targets": "_all",

                        "orderable": false

                    }],



                lengthMenu: [

                    [10, 50, 100, 100000],

                    ['10 rows', '50 rows', '100 rows', 'Show all']

                ],

                dom: 'Blfrtip',

                // buttons: [

                //     'csv', 'excel', 'pdf'

                // ]

                buttons: [

                    {extend: 'csv', footer: true},

                    {extend: 'excel', footer: true},

                    {extend: 'pdf', footer: true, orientation: 'landscape', exportOptions: {stripNewlines: false}}

                ]





            });





            return true;



        }





        $('#filter_search').click(function () { //button filter event click



            $("#list_datatable").dataTable().fnDestroy()

            generateOrderTable()



        });

        // $('#btn-reset').click(function(){ //button reset event click

        //     $('#form-filter')[0].reset();

        //     table.ajax.reload();  //just reload table

        // });



    });





</script>



<!-- <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>

<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>

<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>

<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script> -->



