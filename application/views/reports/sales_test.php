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
   
    #display-dates,#display-customers,#display-invoice{
        color: #0a0a0a;
        margin-left: 28px;
        background: #fff;
        padding: 5px;}
    .select2-container {
        width: 100% !important;
    }    

    .table>thead:first-child>tr:first-child>th{
        padding-right: 0px !important;
    }

    table.dataTable thead .sorting_asc:after{
        display: none;
    }
    div.dataTables_wrapper div.dataTables_filter {
    float: right;
    }


</style>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('report/all_reports'); ?>">Report</a></li>
            <li class="active">Sales Report</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
             <div id="loader">
                <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
            </div>
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
                        <h3 class="box-title">Sales Report</h3>
                        <div class="pull-right double-btn">
                            <a class="btn btn-sm btn-info back_button" href1="<?php echo base_url() ?>report/all_reports">Back</a>
                            <button id="refresh_table" class="btn btn-sm btn-info">Refresh</button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                        <!-- <h5>There are <?php echo count($currency_converted_records); ?> records which currency has not converted yet. Here only converted records are displaying.</h5> -->
                            <!-- <h5>
                            <?php
                            if (count($currency_converted_records) > 0) {
                                echo "There are" . count($currency_converted_records) . "records which currency has not converted yet.";
                            }
                            ?>
                                Here only converted records are displaying.
                            </h5>  -->
                            <div class="box-header" id="filters_applied">
                            </div>                   
                            <table id="list_datatable" class="table table-bordered table-striped table-hover dataTable no-footer" >
                                <thead>
                                    <tr>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Invoice Date" data-type="invoice_date">Invoice Date <span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Invoice Number" data-type="invoice">Invoice Number<span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Customer" data-type="customer">Customer<span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select nature of supply" data-type="nature_of_supply">Nature of supply<span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Bill To Name" data-type="Bill_to_name">Bill-To Name<span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#"> Bill-to address</a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Ship To Name" data-type="Ship_to_name">Ship-to Name<span class="fa fa-toggle-down mr-20"></span></a></th>
 
                                        <th><a href="#">Ship-to address</a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Place of Supply" data-type="place_of_supply">Place of Supply <span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Type of Supply" data-type="type_of_supply">Type of Supply <span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select GST Payable on Reverse Charge" data-type="payable_on_reverse_charge">GST Payable on Reverse Charge <span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <!-- <th><a href="#">E-way Billing</a></th> -->
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Taxable Amount" data-type="taxable_amount">Taxable Amount <span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select CGST" data-type="cgst">CGST <span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select SGST/UTGST" data-type="sgst">SGST/UTGST <span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <!-- <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select UTGST" data-type="utgst">UTGST<span class="fa fa-toggle-down mr-20"></span></a></th> -->

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select IGST" data-type="igst">IGST <span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Invoice Amount" data-type="invoice_amount">Invoice Amount <span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Due Day" data-type="due_day">Due Days<span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <!-- <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Received Amount" data-type="received_amount">Received Amount <span class="fa fa-toggle-down mr-20"></span></a></th> -->
                                        <!-- <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Payment Status" data-type="payment_status">Payment<br>Status <span class="fa fa-toggle-down mr-20"></span></a></th>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Pending Amount" data-type="pending_amount">Pending Amount<span class="fa fa-toggle-down mr-20"></span></a></th> -->
                                        <!-- <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Receivable Date" data-type="receivable_date">Receivable Date<span class="fa fa-toggle-down mr-20"></span></a></th>
                                         -->                                    
                                    </tr>
                                </thead>
                                <tbody></tbody>                           
                            </table>
                        </div>
                        <table id="addition_table" class="mt-15 mb-25">                         
                            <thead>
                                <tr>
                                    <th>Sum Of Taxable Amount</th>
                                    <th>Sum Of CGST Amount</th>
                                    <th>Sum Of SGST/UTGST Amount</th>
                                    <!-- <th>Sum Of UTGST Amount</th> -->
                                    <th>Sum Of IGST Amount</th>
                                    <th>Sum Of Invoice Amount</th>
                                </tr>
                            </thead>                            
                            <tbody>
                                <tr>
                                    <td id="total_list_record1"></td>
                                    <td id="total_list_record2"></td>
                                    <td id="total_list_record3"></td>
                                    <!-- <td id="total_list_record4"></td> -->
                                    <td id="total_list_record5"></td>
                                    <td id="total_list_record6"></td>
                                </tr>
                            </tbody>
                        </table>

<!-- <b>Grand Total : <?= $total_sum[0]->sum_of_grand_total; ?></b><br>
<b>Paid Amount : <?= $total_sum[0]->sales_paid_amount; ?></b><br>
<b>Pending Amount Amount : <?= $total_sum[0]->pending_amount; ?></b><br>
<b>Total Converted amount : <?= $coverted_total; ?></b> -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="distinct-modal" role="dialog">
    <div class="modal-dialog">        
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
                                    <option value="invoice_amount">Invoice Amount</option>
                                    <option value="received_amount">Received Amount</option>
                                    <option value="payment_status">Payment Status</option>
                                    <option value="pending_amount">Pending Amount</option>
                                    <option value="receivable_date">Receivable Date</option>
                                    <option value="due_day">Due Days</option>
                                    <option value="nature_of_supply">nature_of_supply</option>
                                    <option value="Bill_to_name">Bill-to Name</option>
                                    <option value="Ship_to_name">Ship-to Name</option>
                                    <option value="place_of_supply">Place of Supply</option>
                                    <option value="type_of_supply">Type of Supply</option>
                                    <option value="payable_on_reverse_charge">GST Payable on Reverse Charge</option>
                                    <option value="taxable_amount">Taxable Amount</option>
                                    <option value="cgst">CGST</option>
                                    <option value="sgst">SGST</option>
                                    <option value="utgst">UTGST</option>
                                    <option value="igst">IGST</option>
                                    <option value="due">Due Days</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="customer_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_customer_name"  name="filter_customer_name">
                                    <option value="" >Select Customer</option>
                                    <?php
                                    foreach ($customers as $key => $value) {
                                        ?>
                                        <option value="<?= $value->customer_id ?>"><?= $value->customer_name ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                 <!--  <input type="text" name="filter_customer_name" value="" class="form-control" id="filter_customer_name" placeholder="Customer Name"> -->
                            </div>
                        </div>
                        <div class="col-md-12" id="bill_to_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_billing_name"  name="filter_billing_name">
                                    <option value="" >Select Bill-To Name</option>
                                    <?php
                                    foreach ($billing_name as $key => $value) {
                                        ?>
                                        <option value="<?= $value->customer_id ?>"><?= $value->customer_name ?></option>
                                        <?php
                                    }
                                    ?> 
                                </select>
                                 <!--  <input type="text" name="filter_customer_name" value="" class="form-control" id="filter_customer_name" placeholder="Customer Name"> -->
                            </div>
                        </div>
                        <div class="col-md-12" id="ship_to_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_shipping_name"  name="filter_shipping_name">
                                    <option value="" >Select Ship-To Name</option>
                                    <?php
                                    foreach ($shipping_name as $key => $value) {
                                        ?>
                                        <option value="<?= $value->customer_id ?>"><?= $value->customer_name ?></option>
                                        <?php
                                    }
                                    ?> 
                                </select>
                                 <!--  <input type="text" name="filter_customer_name" value="" class="form-control" id="filter_customer_name" placeholder="Customer Name"> -->
                            </div>
                        </div>
                        <div class="col-md-12" id="nature_of_supply_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_nature_of_supply"  name="filter_nature_of_supply">
                                    <option value="" >Select Nature of Supply</option>
                                    <?php
                                    foreach ($nature_of_supply as $key => $value) {
                                        ?>
                                        <option value="<?= $value->sales_nature_of_supply ?>"><?= $value->sales_nature_of_supply ?></option>
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
                                    foreach ($invoices as $key => $value) {
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
                                <div class="input-group date">
                                    <input type="text" name="filter_from_date" value="" class="form-control datepicker" id="filter_from_date" placeholder="From Date">
                                    <div class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="to_date_div">
                            <div class="form-group">
                                 <div class="input-group date">
                                    <input type="text" name="filter_to_date" value="" class="form-control datepicker" id="filter_to_date" placeholder="End date">
                                <div class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12" id="from_taxable_div">
                            <div class="form-group">
                                <input type="number" name="taxable_from_number" value="" id="filter_from_amount" class="form-control" placeholder="From">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_taxable_div">
                            <div class="form-group">
                                <input type="number" name="taxable_to_number" value=""  id="filter_to_amount" class="form-control" placeholder="To">
                            </div>
                        </div>
                        <div class="col-md-12" id="from_sgst_div">
                            <div class="form-group">
                                <input type="number" name="sgst_from_number" value="" id="filter_from_sgst_amount" class="form-control" placeholder="From ">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_sgst_div">
                            <div class="form-group">
                                <input type="number" name="sgst_to_number" value=""  id="filter_to_sgst_amount" class="form-control" placeholder="To">
                            </div>
                        </div>
                        <div class="col-md-12" id="from_cgst_div">
                            <div class="form-group">
                                <input type="number" name="cgst_from_number" value="" id="filter_from_cgst_amount" class="form-control" placeholder="From ">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_cgst_div">
                            <div class="form-group">
                                <input type="number" name="cgst_to_number" value=""  id="filter_to_cgst_amount" class="form-control" placeholder="To">
                            </div>
                        </div>
                        <div class="col-md-12" id="from_utgst_div">
                            <div class="form-group">
                                <input type="number" name="utgst_from_number" value="" id="filter_from_utgst_amount" class="form-control" placeholder="From ">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_utgst_div">
                            <div class="form-group">
                                <input type="number" name="utgst_to_number" value=""  id="filter_to_utgst_amount" class="form-control" placeholder="To">
                            </div>
                        </div>
                        <div class="col-md-12" id="from_igst_div">
                            <div class="form-group">
                                <input type="number" name="igst_from_number" value="" id="filter_from_igst_amount" class="form-control" placeholder="From ">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_igst_div">
                            <div class="form-group">
                                <input type="number" name="igst_to_number" value=""  id="filter_to_igst_amount" class="form-control" placeholder="To">
                            </div>
                        </div>
                        <div class="col-md-12" id="from_invoice_div">
                            <div class="form-group">
                                <input type="number" name="invoice_from_number" value="" id="filter_from_invoice" class="form-control" placeholder="From ">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_invoice_div">
                            <div class="form-group">
                                <input type="number" name="taxable_to_number" value=""  id="filter_to_invoice" class="form-control" placeholder="To">
                            </div>
                        </div>
                        <div class="col-md-12" id="invoice_amount_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_invoice_amount"  name="filter_invoice_amount">
                                    <option value="" >Select Invoice Amount</option>
                                    <?php
                                    foreach ($invoice_amount as $key => $value) {
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
                                    foreach ($received_amount as $key => $value) {
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
                                    foreach ($pending_amount as $key => $value) {
                                        ?>
                                        <option value="<?= $value->sales_pending_amount ?>"><?= $value->sales_pending_amount ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="place_of_supply_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_place_of_supply"  name="filter_place_of_supply">
                                    <option value="" >Select Place of supply</option>
                                    <?php
                                    foreach ($place_of_supply as $key => $value) {
                                        ?>
                                        <option value="<?= $value->sales_billing_state_id ?>"><?= $value->state_name ?></option>
                                        <?php
                                    }
                                    ?>                                 
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="type_of_supply_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_type_of_supply"  name="filter_type_of_supply">
                                    <option value="" >Select Type of supply</option>
                                    <?php
                                    foreach ($type_of_supply as $key => $value) {
                                        ?>
                                        <option value="<?= $value->sales_type_of_supply ?>"><?= $value->sales_type_of_supply ?></option>
                                        <?php
                                    }
                                    ?> 
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="payable_on_reverse_charge_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_payable_on_reverse_charge"  name="filter_payable_on_reverse_charge">
                                    <option value="" >Select Payable on reverse charge</option>
                                    <?php
                                    foreach ($sales_gst_payable as $key => $value) {
                                        ?>
                                        <option value="<?= $value->sales_gst_payable ?>"><?= $value->sales_gst_payable ?></option>
                                        <?php
                                    }
                                    ?> 
                                </select>
                            </div>
                        </div>
                        <!-- <div class="col-md-12" id="taxable_amount_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_taxable_amount"  name="filter_taxable_amount">
                                    <option value="" >Select Taxable Amount</option>
                        <?php
                        foreach ($sales_taxable_value as $key => $value) {
                            ?>
                                                                                <option value="<?= $value->sales_taxable_value ?>"><?= $value->sales_taxable_value ?></option>
                            <?php
                        }
                        ?> 
                                </select>
                            </div>
                        </div> -->

                        <div class="col-md-12" id="cgst_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_cgst"  name="filter_cgst">
                                    <option value="" >Select CGST</option>
                                    <?php
                                    foreach ($sales_cgst_amount as $key => $value) {
                                        ?>
                                        <option value="<?= $value->sales_cgst_amount ?>"><?= precise_amount($value->sales_cgst_amount, 2) ?></option>
                                        <?php
                                    }
                                    ?> 
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12" id="sgst_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_sgst"  name="filter_sgst">
                                    <option value="" >Select SGST</option>
                                    <?php
                                    foreach ($sales_sgst_amount as $key => $value) {
                                        ?>
                                        <option value="<?= $value->sales_sgst_amount ?>"><?= precise_amount($value->sales_sgst_amount, 2) ?></option>
                                        <?php
                                    }
                                    ?> 
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="utgst_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_utgst"  name="filter_utgst">
                                    <option value="" >Select UTGST</option>
                                    <?php
                                    foreach ($sales_utgst_amount as $key => $value) {
                                        ?>
                                        <option value="<?= $value->sales_sgst_amount ?>"><?= precise_amount($value->sales_sgst_amount, 2) ?></option>
                                        <?php
                                    }
                                    ?> 
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12" id="igst_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_igst"  name="filter_igst">
                                    <option value="" >Select IGST</option>
                                    <?php
                                    foreach ($sales_igst_amount as $key => $value) {
                                        ?>
                                        <option value="<?= $value->sales_igst_amount ?>"><?= precise_amount($value->sales_igst_amount, 2) ?></option>
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
                        <div class="col-md-12" id="from_due_day_div">
                            <div class="form-group">
                                <input type="number" name="filter_from_due_day" value="" id="filter_from_due_day" class="form-control" placeholder="From ">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_due_day_div">
                            <div class="form-group">
                                <input type="number" name="filter_to_due_day" value=""  id="filter_to_due_day" class="form-control" placeholder="To">
                            </div>
                        </div>
                        <!-- <div class="col-md-12" id="due_day_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_due"  name="filter_due">
                                    <option value="" >Select Due Day</option>
                                    <?php
                                    foreach ($due_day as $key => $value) {
                                        ?>
                                        <option value="<?= $value->due_day ?>"><?= $value->due_day ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button name="filter_search" type="button" class="btn btn-primary" id="filter_search" value="filter" data-dismiss="modal">Apply</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <!-- <button type="reset" class="btn btn-danger" id="reset-all">Reset All</button> -->
                    <button type="button" class="btn btn-warning" id="reset-customer">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-invoice">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-in-date">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-in-amt">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-rec-amt">Received Amount</button>
                    <button type="button" class="btn btn-warning" id="reset-status">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-amt">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-nature">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-bill_to">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-ship_to">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-Place-supply">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-type-supply">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-gst-payable">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-taxable">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-sgst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-utgst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-igst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-cgst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-due">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="report_select_columns" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> 
                <h4 class="modal-title">Select Columns Name</h4>
            </div>
                <div class="modal-body">
                    <!-- <form  id="export_form" action="<?=base_url(); ?>Product/exportProductReportExcel" enctype="multipart/form-data" method="post" > -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                               <input type="checkbox" name="date" id="date"  value="0" checked> 
                                <label for="date" >Invoice Date</label><br>

                                <input type="checkbox"  name="invoice" id="invoice" value="1" checked>
                                <label for="invoice">Invoice Number</label><br>

                                <input type="checkbox" name="customer" id="customer" value="2" checked>
                                <label for="customer">Customer</label><br>

                              <input type="checkbox" name="nature_of_supply" id="nature_of_supply" value="3" checked>
                                <label for="nature_of_supply">Nature of Supply</label><br>

                                <input type="checkbox" name="bill_to_name" id="bill_to_name" value="4" checked>
                                <label for="bill_to_name">Bill-To Name</label><br>

                                <input type="checkbox" name="bill_to_address" id="bill_to_address" value="5" checked>
                                <label for="bill_to_address">Bill-To Address</label><br>

                                <input type="checkbox" name="ship_to_name" id="ship_to_name" value="6" checked>
                                <label for="ship_to_name">Ship-To Name</label><br>

                                <input type="checkbox" name="shipping_address" id="shipping_address" value="7" checked>
                                <label for="shipping_address">Ship-To Address</label><br>

                                <input type="checkbox" name="place_of_supply" id="place_of_supply" value="8" checked>
                                 <label for="place_of_supply">Place of Supply</label><br>
                            </div>
                            <div class="col-md-6">
                                <input type="checkbox" name="sales_type_of_supply" id="sales_type_of_supply" value="9" checked>
                                <label for="sales_type_of_supply">Type of Supply</label><br>

                                <input type="checkbox" name="sales_gst_payable" id="sales_gst_payable" value="10" checked>
                                <label for="sales_gst_payable">GST Payable On Reverse Charge</label><br>

                                <input type="checkbox" name="sales_taxable_value" id="sales_taxable_value" value="11" checked>
                                <label for="sales_taxable_value">Taxable Amount</label><br>

                                <input type="checkbox" name="sales_cgst_amount" id="sales_cgst_amount" value="12" checked>
                                <label for="sales_cgst_amount">CGST</label><br>

                                <input type="checkbox" name="sales_sgst_amount" id="sales_sgst_amount" value="13"  checked>
                                <label for="sales_sgst_amount">SGST/UTGST</label><br>

                                <input type="checkbox" name="sales_igst_amount" id="sales_igst_amount" value="14" checked>
                                <label for="sales_igst_amount">IGST</label><br>

                                <input type="checkbox" name="grand_total" id="grand_total" value="15" checked>
                                <label for="grand_total">Invoice Amount</label><br>

                                <input type="checkbox" name="due" id="due" value="16" checked>
                                <label for="due">Due Days</label><br>
                                
                            </div>
                        </div>
                    </div>
                </form>
                </div>
                <div class="modal-footer">
                    <button id='select_column' class="btn btn-primary" >Submit</button>
                    <button type="button" class="btn btn-primary tbl-btn" data-dismiss="modal">Cancel</button>
                </div>
        </div>
    </div>
</div>
<?php
$this->load->view('layout/footer');
// $this->load->view('sales/pay_now_modal');
$this->load->view('sales/pdf_type_modal');
// $this->load->view('advance_voucher/advance_voucher_modal');
$this->load->view('general/delete_modal');
$this->load->view('recurrence/recurrence_invoice_modal');
?>
<script src="<?php echo base_url('assets/js/') ?>datatable_variable.js"></script>
<script>
    var list_datatable;
    $(document).on('click', '#reset-all', function () {
        $('#filter_customer_name option:selected').prop("selected", false)
        $('#filter_invoice_number option:selected').prop("selected", false)
        $('#filter_from_date option:selected').prop("selected", false)
        $('#filter_to_date option:selected').prop("selected", false)
        $('#filter_payment_status option:selected').prop("selected", false)
        $('#filter_received_amount option:selected').prop("selected", false)
        $('#filter_invoice_amount option:selected').prop("selected", false)
        $('#filter_reference_number option:selected').prop("selected", false)
        $('#filter_nature_of_supply option:selected').prop("selected", false)
        $('#filter_billing_name option:selected').prop("selected", false)
        $('#filter_shipping_name option:selected').prop("selected", false)
        $('#filter_customer_billing_name option:selected').prop("selected", false)
        $('#filter_place_of_supply option:selected').prop("selected", false)
        $('#filter_type_of_supply option:selected').prop("selected", false)
        $('#filter_gst_payable option:selected').prop("selected", false)
        $('#filter_billing_currency option:selected').prop("selected", false)
        $('#filter_cgst option:selected').prop("selected", false)
        $('#filter_sgst option:selected').prop("selected", false)
        $('#filter_utgst option:selected').prop("selected", false)
        $('#filter_igst option:selected').prop("selected", false)
        $('.select2-selection__rendered').empty();
        //$('#filters_applied').html('<label></label>');
    });
     


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
        });
        $("#reset-cgst").click(function () {
            $("#filter_from_cgst_amount").val('');
            $('#filter_to_cgst_amount').val('');
        })
        $("#reset-sgst").click(function () {
            $("#filter_from_sgst_amount").val('');
            $('#filter_to_sgst_amount').val('');
        })
        $("#reset-utgst").click(function () {
            $("#filter_from_utgst_amount").val('');
            $('#filter_to_utgst_amount').val('');
        })
        $("#reset-igst").click(function () {
            $("#filter_from_igst_amount").val('');
            $('#filter_to_igst_amount').val('');
        })
        $("#reset-taxable").click(function () {
            $("#filter_from_amount").val('');
            $('#filter_to_amount').val('');
        });
        $("#reset-Place-supply").click(function () {
            $("#place_of_supply_div .select2-selection__rendered").empty();
            $('#filter_place_of_supply option:selected').prop("selected", false)
        });
        $("#reset-gst-payable").click(function () {
            $("#payable_on_reverse_charge_div .select2-selection__rendered").empty();
            $('#filter_payable_on_reverse_charge option:selected').prop("selected", false)
        });
        $("#reset-type-supply").click(function () {
            $("#type_of_supply_div .select2-selection__rendered").empty();
            $('#filter_type_of_supply option:selected').prop("selected", false)
        });
        $("#reset-bill_to").click(function () {
            $("#bill_to_div .select2-selection__rendered").empty();
            $('#filter_billing_name option:selected').prop("selected", false)
        });
        $("#reset-ship_to").click(function () {
            $("#ship_to_div .select2-selection__rendered").empty();
            $('#filter_shipping_name option:selected').prop("selected", false)
        });
        $("#reset-nature").click(function () {
            $("#nature_of_supply_div .select2-selection__rendered").empty();
            $('#filter_nature_of_supply option:selected').prop("selected", false)
        });
        $("#reset-invoice").click(function () {
            $("#invoice_div .select2-selection__rendered").empty();
            $('#filter_invoice_number option:selected').prop("selected", false)
        });
        $("#reset-in-date").click(function () {
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
        });
        $('#reset-in-amt').click(function () {
            $("#filter_from_invoice").val('');
            $('#filter_to_invoice').val('');
        });
        $('#reset-rec-amt').click(function () {
            $("#received_amount_div .select2-selection__rendered").empty();
            $('#filter_received_amount option:selected').prop("selected", false)
        });
        $('#reset-status').click(function () {
            $("#payment_status_div .select2-selection__rendered").empty();
            $('#filter_payment_status option:selected').prop("selected", false)
        });
        $('#reset-amt').click(function () {
            $("#pending_amount_div .select2-selection__rendered").empty();
            $('#filter_pending_amount option:selected').prop("selected", false)
        });
        $('#reset-due').click(function () {
            $("#filter_from_due_day").val('');
            $('#filter_to_due_day').val('');
        });

        $('#refresh_table').on('click', function(){
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable();
        });

        $("#modal-select_type").on("change", function (event)
        {
            var type = $('#modal-select_type').val();
            console.log(type);
            if (type == "")
            {
                $("#customer_div").hide();
                $("#invoice_div").hide();
                $("#nature_of_supply_div").hide();
                $("#from_date_div").hide();
                $("#to_date_div").hide();
                $("#invoice_amount_div").hide();
                $("#received_amount_div").hide();
                $("#payment_status_div").hide();
                $("#pending_amount_div").hide();
                $("#receivable_date_div").hide();
                $("#due_day_div").hide();
                $("#nature_supply_div").hide();
                $("#bill_to_div").hide();
                $("#ship_to_div").hide();
                $("#igst_div ").hide();
                $("#sgst_div").hide();
                $("#utgst_div").hide();
                $("#cgst_div").hide();
                $("#taxable_amount_div").hide();
                $("#payable_on_reverse_charge_div").hide();
                $("#place_of_supply_div").hide();
                $("#type_of_supply_div").hide();
                $("#from_taxable_div").hide();
                $("#to_taxable_div").hide();
                $("#filter_taxable_amount").hide();
                $("#from_due_day_div").hide();
                $('#to_due_day_div').hide();
                $("#from_sgst_div ").hide();
                $("#to_sgst_div").hide();
                $("#from_cgst_div").hide();
                $("#to_cgst_div").hide();
                $("#from_utgst_div").hide();
                $("#to_utgst_div").hide();
                $("#from_igst_div").hide();
                $("#to_igst_div").hide();
            }
            if (type == "due_day")
            {
                $("#from_due_day_div").show();
                $('#to_due_day_div').show();
                $('#reset-due').show();
            } else {
                $("#from_due_day_div").hide();
                $('#to_due_day_div').hide();
                $('#reset-due').hide();
            }

            if (type == "place_of_supply")
            {
                $("#place_of_supply_div").show();
                $("#reset-Place-supply").show();
            } else
            {
                $("#place_of_supply_div").hide();
                $("#reset-Place-supply").hide();
            }

            if (type == "type_of_supply")
            {
                $("#type_of_supply_div").show();
                $("#reset-type-supply").show();
            } else
            {
                $("#type_of_supply_div").hide();
                $("#reset-type-supply").hide();
            }

            if (type == "payable_on_reverse_charge")
            {
                $("#payable_on_reverse_charge_div").show();
                $("#reset-gst-payable").show();
            } else
            {
                $("#payable_on_reverse_charge_div").hide();
                $("#reset-gst-payable").hide();
            }

            if (type == "taxable_amount")
            {
                $("#from_taxable_div").show();
                $("#to_taxable_div").show();
                $("#reset-taxable").show();
            } else
            {
                $("#from_taxable_div").hide();
                $("#to_taxable_div").hide();
                $("#reset-taxable").hide();
            }

            if (type == "cgst")
            {
                $("#from_cgst_div").show();
                $("#to_cgst_div").show();
                $('#reset-cgst').show();
            } else
            {
                $("#from_cgst_div").hide();
                $("#to_cgst_div").hide();
                $("#cgst_div").hide();
                $("#reset-cgst").hide();
            }

            if (type == "sgst")
            {
                $("#from_sgst_div ").show();
                $("#to_sgst_div").show();
                $('#reset-sgst').show();
            } else
            {
                $("#from_sgst_div ").hide();
                $("#to_sgst_div").hide();
                $("#sgst_div").hide();
                $("#reset-sgst").hide();
            }

            if (type == "utgst")
            {
                $("#from_utgst_div").show();
                $("#to_utgst_div").show();
                $("#reset-utgst").show();
            } else
            {
                $("#from_utgst_div").hide();
                $("#to_utgst_div").hide();
                $("#utgst_div").hide();
                $("#reset-utgst").hide();
            }

            if (type == "igst")
            {
                $("#from_igst_div").show();
                $("#to_igst_div").show();
                $('#reset-igst').show();
            } else
            {
                $("#from_igst_div").hide();
                $("#to_igst_div").hide();
                $("#igst_div").hide();
                $("#reset-igst").hide();
            }

            if (type == "nature_of_supply")
            {
                $("#nature_of_supply_div").show();
                $("#reset-nature").show();
            } else
            {
                $("#nature_of_supply_div").hide();
                $("#reset-nature").hide();
            }

            if (type == "taxable_amount")
            {
                $("#taxable_amount_div").show();
            } else
            {
                $("#taxable_amount_div").hide();
            }

            if (type == "customer")
            {
                $("#customer_div").show();
                $("#reset-customer").show();
            } else
            {
                $("#customer_div").hide();
                $("#reset-customer").hide();
            }

            if (type == "Bill_to_name")
            {
                $("#bill_to_div").show();
                $("#reset-bill_to").show();
            } else
            {
                $("#bill_to_div").hide();
                $("#reset-bill_to").hide();
            }

            if (type == "Ship_to_name")
            {
                $("#ship_to_div").show();
                $("#reset-ship_to").show();
            } else
            {
                $("#ship_to_div").hide();
                $("#reset-ship_to").hide();
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
                $("#from_invoice_div").show();
                $("#to_invoice_div").show();
                $("#reset-in-amt").show();
            } else
            {
                $("#from_invoice_div").hide();
                $("#to_invoice_div").hide();
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

        }).change();
        generateOrderTable();
        function resetall() {
            $('#filter_customer_name option:selected').prop("selected", false)
            $('#filter_invoice_number option:selected').prop("selected", false)
            $('#filter_from_date option:selected').prop("selected", false)
            $('#filter_to_date option:selected').prop("selected", false)
            $('#filter_payment_status option:selected').prop("selected", false)
            $('#filter_received_amount option:selected').prop("selected", false)
            $('#filter_invoice_amount option:selected').prop("selected", false)
            $('#filter_reference_number option:selected').prop("selected", false)
            $('#filter_nature_of_supply option:selected').prop("selected", false)
            $('#filter_billing_name option:selected').prop("selected", false)
            $('#filter_shipping_name option:selected').prop("selected", false)
            $('#filter_customer_billing_name option:selected').prop("selected", false)
            $('#filter_place_of_supply option:selected').prop("selected", false)
            $('#filter_type_of_supply option:selected').prop("selected", false)
            $('#filter_gst_payable option:selected').prop("selected", false)
            $('#filter_billing_currency option:selected').prop("selected", false)
            $('#filter_cgst option:selected').prop("selected", false)
            $('#filter_sgst option:selected').prop("selected", false)
            $('#filter_utgst option:selected').prop("selected", false)
            $('#filter_igst option:selected').prop("selected", false)
            $('#filter_payable_on_reverse_charge option:selected').prop("selected", false)
            $("#filter_from_amount").val('');
            $('#filter_to_amount').val('');
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
            $("#filter_from_invoice").val('');
            $('#filter_to_invoice').val('');
            $("#filter_from_due_day").val('');
            $('#filter_to_due_day').val('');
            $("#filter_from_cgst_amount").val('');
            $("#filter_to_cgst_amount").val('');
            $("#filter_from_sgst_amount").val('');
            $("#filter_to_sgst_amount").val('');
            $("#filter_from_utgst_amount").val('');
            $("#filter_to_utgst_amount").val('');
            $("#filter_from_igst_amount").val('');
            $("#filter_to_igst_amount").val('');
            $('.select2-selection__rendered').empty();
            var filter_customer_name = "";
            var filter_invoice_number = "";
            var filter_invoice_amount = "";
            var filter_invoice_date = "";
            var filter_received_amount = "";
            var filter_payment_status = "";
            var filter_pending_amount = "";
            var filter_nature_of_supply = "";
            var filter_due_day = "";
            //if(label == undefined){
            label = '';
            //}
            //if(arr == undefined){
            // var arr = new Array();
            //}

            $("#filter_customer_name option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_customer_name += $this.text() + ", ";

                }
            });

            $("#filter_billing_name option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_billing_name += $this.text() + ", ";

                }
            });

            $("#filter_shipping_name option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_shipping_name += $this.text() + ", ";

                }
            });

            $("#filter_nature_of_supply option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_nature_of_supply += $this.text() + ", ";

                }
            });

            $("#filter_invoice_number option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_invoice_number += $this.text() + ", ";
                }
            });
            var filter_from_date = $('#filter_from_date').val();
            var filter_to_date = $('#filter_to_date').val();
            if (filter_from_date != "" || filter_to_date != "") {
                filter_invoice_date = "<b> INVOICE DATE </b> FROM " + filter_from_date + " TO " + filter_to_date;
            }

            $("#filter_invoice_amount option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_invoice_amount += $this.text() + ", ";
                }
            });

            $("#filter_received_amount option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_received_amount += $this.text() + ", ";
                }
            });
            $("#filter_payment_status option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_payment_status += $this.text() + ", ";
                }
            });
            $("#filter_pending_amount option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_pending_amount += $this.text() + ", ";
                }
            });
            var filter_from_due_day = $('#filter_from_due_day').val();
            var filter_to_due_day = $('#filter_to_due_day').val();
            if (filter_from_due_day != "" || filter_to_due_day != "") {
                filter_due_day = "<b> Due Days </b> FROM " + filter_from_due_day + " TO " + filter_to_due_day;
            }
            var filter_receivable_date = $('#filter_receivable_date').val();
            var filter_due = $('#filter_due').val();
            if (filter_customer_name != '') {
                label += "<b> CUSTOMER NAME : </b> " + filter_customer_name;
            }
            if (filter_invoice_number != '') {
                label += "<b> INVOICE NUMBER : </b> " + filter_invoice_number + " ";
            }

            if (filter_invoice_date != '') {
                label += filter_invoice_date + " ";
            }
            if (filter_invoice_amount != '') {
                label += "<b> INVOICE AMOUNT : </b> " + filter_invoice_amount + " ";
            }
            if (filter_received_amount != '') {
                label += "<b> RECEIVED AMOUNT : </b> " + filter_received_amount + " ";
            }
            if (filter_payment_status != '') {
                label += "<b> PAYMENT STATUS : </b> " + filter_payment_status + " ";
            }

            if (filter_pending_amount != '') {
                label += "<b> PENDING AMOUNT : </b> " + filter_pending_amount + " ";
            }

            if (filter_receivable_date != '') {
                label += "<b> RECEIVABLE DATE : </b> " + filter_receivable_date + " ";
            }

            /*if (filter_due != '') {
                label += "<b> DUE DAY  : </b> " + filter_due + " ";
            }*/
            // if (filter_due_day != '') {
            //     label += "<b> DUE DAY  : </b> " + filter_due_day + " ";
            // }
            //  arr.push(label);
            // console.log(arr);
            if (label != "") {
                $('#filters_applied').html('<label>Filters : ' + label + '</label>');
            } else {
                $('#filters_applied').html('<label></label>');
            }

            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable();
            //$('#filters_applied').html('<label></label>');
        }
        function generateOrderTable() {
            
            list_datatable = $('#list_datatable').DataTable({
                "processing": true,
                "serverSide": true,
                "scrollX": true,
                "iDisplayLength": 50,
                 "lengthMenu": [ [15, 25, 50,100, -1], [15, 25, 50,100, "All"] ],
                "ajax": {
                    "url": base_url + "report/sales_test",
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
                        'filter_due': $('#filter_due').val(),
                        'filter_nature_of_supply': $('#filter_nature_of_supply').val(),
                        'filter_billing_to': $('#filter_billing_name').val(),
                        'filter_shipping_to': $('#filter_shipping_name').val(),
                        'filter_place_of_supply': $('#filter_place_of_supply').val(),
                        'filter_type_of_supply': $('#filter_type_of_supply').val(),
                        'filter_payable_on_reverse_charge': $('#filter_payable_on_reverse_charge').val(),
                        //'filter_taxable_amount': $('#filter_taxable_amount').val(),
                        'filter_cgst_from': $('#filter_from_cgst_amount').val(),
                        'filter_cgst_to': $('#filter_to_cgst_amount').val(),
                        'filter_sgst_from': $("#filter_from_sgst_amount").val(),
                        'filter_sgst_to': $("#filter_to_sgst_amount").val(),
                        'filter_utgst_from': $('#filter_from_utgst_amount').val(),
                        'filter_utgst_to': $('#filter_to_utgst_amount').val(),
                        'filter_igst_from': $('#filter_from_igst_amount').val(),
                        'filter_igst_to': $('#filter_to_igst_amount').val(),
                        'filter_from_taxable_amount': $('#filter_from_amount').val(),
                        'filter_to_taxable_amount': $('#filter_to_amount').val(),
                        'filter_from_invoice_amount': $('#filter_from_invoice').val(),
                        'filter_to_invoice_amount': $('#filter_to_invoice').val(),
                        'filter_from_due_days' : $('#filter_from_due_day').val(),
                        'filter_to_due_days' : $('#filter_to_due_day').val()
                    },
                    "dataSrc": function (result) {
                        var tfoot = parseFloat(result.total_list_record[0].tot_sales_taxable_value).toFixed(2);
                        $('#total_list_record1').html(tfoot);
                        // tfoot = "";
                        tfoot = parseFloat(result.total_list_record[0].tot_sales_cgst_amount).toFixed(2);
                        $('#total_list_record2').html(tfoot);
                        tfoot = parseFloat(result.total_list_record[0].tot_sales_sgst_amount_new).toFixed(2);
                        $('#total_list_record3').html(tfoot);
                        tfoot = parseFloat(result.total_list_record[0].tot_sales_utgst_amount_new).toFixed(2);
                        $('#total_list_record4').html(tfoot);
                        tfoot = parseFloat(result.total_list_record[0].tot_sales_igst_amount).toFixed(2);
                        $('#total_list_record5').html(tfoot);
                        //$('#total_list_record2').html(tfoot);
                        tfoot = parseFloat(result.total_list_record[0].tot_sales_grand_total).toFixed(2);
                        $('#total_list_record6').html(tfoot);
                        // if (result.total_list_record[0].tot_credit_note_amount != 0)
                        // {
                        //     tfoot = parseFloat(result.total_list_record[0].tot_credit_note_amount).toFixed(2);
                        //     $('#total_list_record6').html(tfoot);
                        // }
                        // if (result.total_list_record[0].tot_debit_note_amount != 0)
                        // {
                        //     tfoot = parseFloat(result.total_list_record[0].tot_debit_note_amount).toFixed(2);
                        //     $('#total_list_record7').html(tfoot);
                        // }
                        // //$('#total_list_record5').html(tfoot);
                        // if (result.total_list_record[0].tot_grand_total != 0)
                        // {
                        //     tfoot = parseFloat(result.total_list_record[0].tot_grand_total).toFixed(2);
                        //     $('#total_list_record8').html(tfoot);
                        // }

                        // if (result.total_list_record[0].tot_sales_paid_amount != 0)
                        // {
                        //     tfoot = parseFloat(result.total_list_record[0].tot_sales_paid_amount).toFixed(2);
                        //     $('#total_list_record9').html(tfoot);
                        // }

                        // if (result.total_list_record[0].tot_sales_pending_amount != 0)
                        // {
                        //     tfoot = parseFloat(result.total_list_record[0].tot_sales_pending_amount).toFixed(2);
                        //     $('#total_list_record10').html(tfoot);
                        // }
                        return result.data;
                    }
                },
                "columns": [
                    {"data": "date"},
                    {"data": "invoice"},
                    {"data": "customer"},
                    {"data": "nature_of_supply"},
                    {"data": "bill_to_name"},
                    {"data": "bill_to_address"},
                    {'data': "ship_to_name"},
                    {'data': "shipping_address"},
                    {'data': "place_of_supply"},
                    {'data': "sales_type_of_supply"},
                    {'data': "sales_gst_payable"},
                    //{'data': "sales_e_way_bill_number"},
                    {'data': "sales_taxable_value"},
                    {'data': "sales_cgst_amount"},
                    {'data': "sales_sgst_amount"},
                    /*{'data': "sales_utgst_amount"},*/
                    {'data': "sales_igst_amount"},
                    {"data": "grand_total"},
                    //{"data": "payment_status"},
                    //{"data": "pending_amount"},
                    //{"data": "receivable_date"},
                    {"data": "due"}
                    // {"data": "action"}
                ],
                "columnDefs": [{
                        "targets": "_all",
                        "orderable": false
                    }],               
                dom: 'Bfrltip',
                buttons: [
                    {extend: 'csv',
                        exportOptions: {
                            columns: ':visible',
                            footer: true
                        }       
                    },
                    {extend: 'excel', 
                        exportOptions: {
                            columns: ':visible',
                            footer: true
                        }       
                    },
                    {extend: 'pdfHtml5',
                        text: 'PDF',
                        filename: 'Sales Report',
                        orientation: 'landscape', //portrait
                        pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                        exportOptions: {
                            columns: ':visible',
                            search: 'applied',
                            order: 'applied'
                        },
                        customize: function (doc) {
                            //Remove the title created by datatTables
                            //doc.content.splice(0, 5);
                            //Create a date string that we use in the footer. Format is dd-mm-yyyy
                            // var now = new Date();
                            // var jsDate = now.getDate() + '-' + (now.getMonth() + 1) + '-' + now.getFullYear();
                            // Logo converted to base64
                            // var logo = getBase64FromImageUrl('https://datatables.net/media/images/logo.png');
                            // The above call should work, but not when called from codepen.io
                            // So we use a online converter and paste the string in.
                            // Done on http://codebeautify.org/image-to-base64-converter
                            // It's a LONG string scroll down to see the rest of the code !!!
                               
                             // var logo= 'http://localhost/PhpProject1/aodry-v4-1/assets/images/logo.png';
                            // A documentation reference can be found at
                            // https://github.com/bpampuch/pdfmake#getting-started
                            // Set page margins [left,top,right,bottom] or [horizontal,vertical]
                            // or one number for equal spread
                            // It's important to create enough space at the top for a header !!!
                            doc.pageMargins = [20, 60, 20, 30];
                            // Set the font size fot the entire document
                            doc.defaultStyle.fontSize = 7;
                            // Set the fontsize for the table header
                            doc.styles.tableHeader.fontSize = 7;
                            // Create a header object with 3 columns
                            // Left side: Logo
                            // Middle: brandname
                            // Right side: A document title
                            doc['header'] = (function () {
                                return {
                                    columns: [
                                        {
                                            image: datatable_logo,
                                            width: 90
                                        },
                                        {
                                            alignment: 'left',
                                            italics: true,
                                            text: '',
                                            fontSize: 18,
                                            margin: [10, 0]
                                        },
                                        {
                                            alignment: 'right',
                                            fontSize: 14,
                                            text: 'Sales Report'
                                        }
                                    ],
                                    margin: 20
                                }
                            });
                            // Create a footer object with 2 columns
                            // Left side: report creation date
                            // Right side: current page and total pages
                            doc['footer'] = (function (page, pages) {
                                return {
                                   columns: [
                                        {
                                            alignment: 'center',
                                            fontSize:10,
                                            text: ['Copyright © 2019 Aavana Corporate Solutions Pvt Ltd. All Rights Reserved']
                                        },                                     
                                    ]
                                }
                            });
                            // Change dataTable layout (Table styling)
                            // To use predefined layouts uncomment the line below and comment the custom lines below
                            // doc.content[0].layout = 'lightHorizontalLines'; // noBorders , headerLineOnly
                            var objLayout = {};
                            objLayout['hLineWidth'] = function (i) {
                                return .5;
                            };
                            objLayout['vLineWidth'] = function (i) {
                                return .5;
                            };
                            objLayout['hLineColor'] = function (i) {
                                return '#aaa';
                            };
                            objLayout['vLineColor'] = function (i) {
                                return '#aaa';
                            };
                            objLayout['paddingLeft'] = function (i) {
                                return 4;
                            };
                            objLayout['paddingRight'] = function (i) {
                                return 4;
                            };
                            doc.content[0].layout = objLayout;
                            /*doc.watermark = {text: 'Aavana Corporate Solutions PVT LTD', color: '#999999', opacity: 0.2};*/
                        }
                    },
                    {
                        text: 'Reset All',
                        action: function (e, dt, node, config) {
                            resetall();
                        }

                    },
                    {
                        text: 'Select columns',
                        action: function (e, dt, node, config) {
                            $('#report_select_columns').modal('show');
                        }
                        
                    }
                    ],

             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });
            anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
            /*setTimeout(function(){ $("#loader").hide(); }, 500);*/
            $("#loader").hide();
            return true;
        }
        $('#filter_search').click(function () { //button filter event click
            //var filter_customer_name =  $('#filter_customer_name option:selected').text();
            $("#loader").show();

            var filter_customer_name = "";
            var filter_invoice_number = "";
            var filter_invoice_date = "";
            var filter_taxable_amount= "";
            var filter_invoice_amount= "";
            var filter_received_amount = "";
            var filter_payment_status = "";
            var filter_pending_amount = "";
            var filter_nature_of_supply = "";
            var filter_billing_name = "";
            var filter_shipping_name = "";
            var filter_place_of_supply = "";
            var filter_type_of_supply = "";
            var filter_payable_on_reverse_charge = "";
            var filter_sgst_data = "";
            var filter_cgst_data = "";
            var filter_utgst_data = "";
            var filter_igst_data = "";
            /*var filter_cgst = "";
            var filter_sgst = "";
            var filter_utgst = "";
            var filter_igst = "";*/
            var filter_due = "";

            //if(label == undefined){
            label = '';
            //}
            //if(arr == undefined){
            // var arr = new Array();
            //}
            $("#filter_customer_name option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_customer_name += $this.text() + ", ";
                }
            });
            $("#filter_billing_name option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_billing_name += $this.text() + ", ";
                }
            });
            $("#filter_shipping_name option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_shipping_name += $this.text() + ", ";
                }
            });
            $("#filter_nature_of_supply option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_nature_of_supply += $this.text() + ", ";
                }
            });
            $("#filter_place_of_supply option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_place_of_supply += $this.text() + ", ";
                }
            });
            $("#filter_type_of_supply option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_type_of_supply += $this.text() + ", ";
                }
            });
            $("#filter_payable_on_reverse_charge option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_payable_on_reverse_charge += $this.text() + ", ";
                }
            });
            /*$("#filter_cgst option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_cgst += $this.text() + ", ";
                }
            });
            $("#filter_sgst option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_sgst += $this.text() + ", ";
                }
            });
            $("#filter_utgst option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_utgst += $this.text() + ", ";
                }
            });
            $("#filter_igst option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_igst += $this.text() + ", ";
                }
            });*/
            $("#filter_invoice_number option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_invoice_number += $this.text() + ", ";
                }
            });
            var filter_from_date = $('#filter_from_date').val();
            var filter_to_date = $('#filter_to_date').val();
            if (filter_from_date != "" || filter_to_date != "") {
                filter_invoice_date = "<label id='lbl_invoice_date' class='label-style'><b> Invoice date : </b> FROM " + filter_from_date + " TO " + filter_to_date + '<i class="fa fa-times"></i></label>';
                // "<label class='label-style'><b> INVOICE NUMBER : </b> " + filter_invoice_number + '<i class="fa fa-times"></i></label>' + " ";
            }
            var filter_sgst_from = $("#filter_from_sgst_amount ").val();
            var filter_sgst_to = $("#filter_to_sgst_amount").val();
            if (filter_sgst_from != "" || filter_sgst_to != "") {
                filter_sgst_data = "<label id='lbl_sgst_amount' class='label-style'><b> SGST/UTGST : </b> FROM " + filter_sgst_from + " TO " + filter_sgst_to + '<i class="fa fa-times"></i></label>';
            }

            var filter_cgst_from = $("#filter_from_cgst_amount ").val();
            var filter_cgst_to = $("#filter_to_cgst_amount").val();
            if (filter_cgst_from != "" || filter_cgst_to != "") {
                filter_cgst_data = "<label id='lbl_cgst_amount' class='label-style'><b> CGST : </b> FROM " + filter_cgst_from + " TO " + filter_cgst_to + '<i class="fa fa-times"></i></label>';
            }

            var filter_utgst_from = $("#filter_from_utgst_amount ").val();
            var filter_utgst_to = $("#filter_to_utgst_amount").val();
            if (filter_utgst_from != "" || filter_utgst_to != "") {
                filter_utgst_data = "<label id='lbl_utgst_amount' class='label-style'><b> UTGST : </b> FROM " + filter_utgst_from + " TO " + filter_utgst_to + '<i class="fa fa-times"></i></label>';
            }

            var filter_igst_from = $("#filter_from_igst_amount ").val();
            var filter_igst_to = $("#filter_to_igst_amount").val();
            if (filter_igst_from != "" || filter_igst_to != "") {
                filter_igst_data = "<label id='lbl_igst_amount' class='label-style'><b> IGST : </b> FROM " + filter_igst_from + " TO " + filter_igst_to + '<i class="fa fa-times"></i></label>';
            }
            // $("#filter_invoice_amount1 option:selected").each(function () {
            //     var $this = $(this);
            //     if ($this.length) {
            //         filter_invoice_amount1 += $this.text() + ", ";
            //     }
            // });
            $("#filter_received_amount option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_received_amount += $this.text() + ", ";
                }
            });
            $("#filter_payment_status option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_payment_status += $this.text() + ", ";
                }
            });
            $("#filter_pending_amount option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_pending_amount += $this.text() + ", ";
                }
            });
            var filter_receivable_date = $('#filter_receivable_date').val();
            // var filter_due = $('#filter_due').val();
            // filter_invoice_number = filter_invoice_number.slice(0, -1);
            if (filter_invoice_number != '') {
                label += "<label id='lbl_invoice_number' class='label-style'><b> Invoice Number : </b> " + filter_invoice_number.slice(0, -2) + '<i class="fa fa-times"></i></label>' + " ";
            }
            if (filter_customer_name != '') {
                label += "<label id='lbl_customer_name' class='label-style'><b> Customer Name : </b> " + filter_customer_name.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            if(filter_nature_of_supply != '') {
                label +="<label id='lbl_nature_supply' class='label-style'><b> Nature Of Supply : </b> " + filter_nature_of_supply.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            if(filter_billing_name != '') {
                label += "<label id='lbl_billing_name' class='label-style'><b> Billing Name : </b> " + filter_billing_name.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            if(filter_shipping_name != '') {
                label += "<label id='lbl_shipping_name' class='label-style'><b> Shipping Name : </b> " + filter_shipping_name.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            if(filter_place_of_supply != '') {
                label += "<label id='lbl_place_supply' class='label-style'><b> Place Of Supply : </b> " + filter_place_of_supply.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            if(filter_type_of_supply != '') {
                label += "<label id='lbl_type_supply' class='label-style'><b> Type Of Supply : </b> " + filter_type_of_supply.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            if(filter_payable_on_reverse_charge != '') {
                label += "<label id='lbl_payable_on_reverse' class='label-style'><b> Payable On Reverse Charge : </b> " + filter_payable_on_reverse_charge.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            var filter_from_amount = $('#filter_from_amount').val();
            var filter_to_amount = $('#filter_to_amount').val();
            if (filter_from_amount != "" || filter_to_amount != "") {
                filter_taxable_amount = "<label id='lbl_taxable_amount' class='label-style'><b> Taxable Amount : </b> FROM " + filter_from_amount + " TO " + filter_to_amount + '<i class="fa fa-times"></i></label>';
            }
            /*if(filter_cgst != '') {
                label += "<label id='lbl_cgst' class='label-style'><b> CGST : </b> " + filter_cgst.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            if(filter_sgst != '') {
                label += "<label id='lbl_sgst' class='label-style'><b> SGST : </b> " + filter_sgst.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            if(filter_utgst != '') {
                label += "<label id='lbl_utgst' class='label-style'><b> UTGST : </b> " + filter_utgst.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            if(filter_igst != '') {
                label += "<label id='lbl_igst' class='label-style'><b> IGST : </b> " + filter_igst.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }*/
            var filter_from_invoice = $('#filter_from_invoice').val();
            var filter_to_invoice = $('#filter_to_invoice').val();
            if (filter_from_invoice != "" || filter_to_invoice != "") {
                filter_invoice_amount = "<label id='lbl_invoice_Amount' class='label-style'><b> Invoice Amount : </b> FROM " + filter_from_invoice + " TO " + filter_to_invoice + '<i class="fa fa-times"></i></label>';
            }
            var filter_from_due_day = $('#filter_from_due_day').val();
            var filter_to_due_day = $('#filter_to_due_day').val(); 
            if (filter_from_due_day != "" || filter_to_due_day != "") {
                filter_due = "<label id='lbl_due_day' class='label-style'><b> Due Days : </b> FROM " + filter_from_due_day + " TO " + filter_to_due_day + '<i class="fa fa-times"></i></label>';
            }
             
            if (filter_due != '') {
                label += filter_due + " ";
            }
            if (filter_invoice_date != '') {
                label += filter_invoice_date + " ";
            }
            if (filter_sgst_data != '') {
                label += filter_sgst_data + " ";
            }
            if (filter_cgst_data != '') {
                label += filter_cgst_data + " ";
            }
            if (filter_utgst_data != '') {
                label += filter_utgst_data + " ";
            }
            if (filter_igst_data != '') {
                label += filter_igst_data + " ";
            }
            if (filter_taxable_amount != '') {
                label += filter_taxable_amount + " ";
            }
            if (filter_invoice_amount != '') {
                label += filter_invoice_amount + " ";
            }
            // if (filter_invoice_amount != '') {
            //     label += "<b> INVOICE AMOUNT : </b> " + filter_invoice_amount1 + " ";
            // }
            if (filter_received_amount != '') {
                label += "<b> RECEIVED AMOUNT : </b> " + filter_received_amount + " ";
            }
            if (filter_payment_status != '') {
                label += "<b> PAYMENT STATUS : </b> " + filter_payment_status + " ";
            }
            if (filter_pending_amount != '') {
                label += "<b> PENDING AMOUNT : </b> " + filter_pending_amount + " ";
            }
            if (filter_receivable_date != '') {
                label += "<b> RECEIVABLE DATE : </b> " + filter_receivable_date + " ";
            }
            // var label_dynamic = '<label class="label-style">Filters : ' + label + ' <i class=fa fa-times"></i></label>';
            // var label_dynamic1 = '<label class="label-style">Filterrtgrfggrs : ' + label + ' <i class=fa fa-times"></i></label>';
            if (label != "") {
                $('#filters_applied').html(label);
            } else {
                $('#filters_applied').html('<label></label>');
            }
        
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable()
        });
        $(document).on("click", ".fa-times", function (){
            
            $(this).parent('label').remove();
            var label1=$(this).parent('label').attr('id');
            if( label1 == 'lbl_invoice_date'){
                $('#filter_from_date').val('');
                $('#filter_to_date').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_invoice_number'){
                $("#invoice_div .select2-selection__rendered").empty();
                $('#filter_invoice_number option:selected').prop("selected", false)
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();

            }
            if(label1 == 'lbl_customer_name'){
                $("#customer_div .select2-selection__rendered").empty();
                $('#filter_customer_name option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_nature_supply'){
                $("#nature_of_supply_div .select2-selection__rendered").empty();
                $('#filter_nature_of_supply option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_billing_name'){
                $("#bill_to_div .select2-selection__rendered").empty();
                $('#filter_billing_name option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_shipping_name'){
                $("#ship_to_div .select2-selection__rendered").empty();
                $('#filter_shipping_name option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_place_supply'){
                $("#place_of_supply_div .select2-selection__rendered").empty();
                $('#filter_place_of_supply option:selected').prop("selected", false)
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_type_supply'){
                $("#type_of_supply_div .select2-selection__rendered").empty();
                $('#filter_type_of_supply option:selected').prop("selected", false)
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_payable_on_reverse'){
                $("#payable_on_reverse_charge_div .select2-selection__rendered").empty();
                $('#filter_payable_on_reverse_charge option:selected').prop("selected", false)
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_taxable_amount'){
                $("#filter_from_amount").val('');
                $('#filter_to_amount').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_sgst_amount'){
                $("#filter_from_sgst_amount").val('');
                $('#filter_to_sgst_amount').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_cgst_amount'){
                $("#filter_from_cgst_amount").val('');
                $('#filter_to_cgst_amount').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_utgst_amount'){
                $("#filter_from_utgst_amount").val('');
                $('#filter_to_utgst_amount').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_igst_amount'){
                $("#filter_from_igst_amount").val('');
                $('#filter_to_igst_amount').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_invoice_Amount'){
                $("#filter_from_invoice").val('');
                $('#filter_to_invoice').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_due_day'){
                $("#filter_from_due_day").val('');
                $('#filter_to_due_day').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            
        });
    });
$(document).ready(function(){
    $('#select_column').click(function(){
        var checked_arr = [];
        var unchecked_arr = [];

        $.each($('#report_select_columns input[type="checkbox"]:checked'),function(key,value){
            checked_arr.push(this.value);
        });

        $.each($('#report_select_columns input[type="checkbox"]:not(:checked)'),function(key,value){
            unchecked_arr.push(this.value);
        });
        list_datatable.columns(checked_arr).visible(true);
        // console.log(checked_arr);

        list_datatable.columns(unchecked_arr).visible(false);
        // console.log(unchecked_arr);
        $('#report_select_columns').modal('hide');
    });
});
</script>