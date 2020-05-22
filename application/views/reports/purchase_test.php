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
    .label-style{
        background: #ddd;
        color: #333;
        padding: 2px 10px;
        font-size: 11px;
        border-radius: 10px;
    }
    .drop-down-supplier{
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
    
    #display-dates,#display-suppliers,#display-invoice{
        color: #0a0a0a;
        margin-left: 28px;
        background: #fff;
        padding: 5px;
    }
    .select2-container {
        width: 100% !important;
    }

    #list_datatable tr th a{
        color: #fff !important;
        float:left;
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
    <!-- Content Header (Page header) -->
    <section class="content-header">
    </section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('report/all_reports'); ?>">Report</a></li>
            <li class="active">Purchase Report</li>
        </ol>
    </div>
    <!-- Main content -->
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
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Purchase Report</h3>
                        <div class="pull-right double-btn">
                            <a class="btn btn-sm btn-info btn-flat back_button" href1="<?php echo base_url() ?>report/all_reports">Back</a>
                            <button id="refresh_table" class="btn btn-sm btn-info">Refresh</button>
                        </div>
         <!-- <select style="display: inline-block;float: right;width: 8%;margin-top: 10px" name="list_datatable_length" id="getRecordsDate" aria-controls="list_datatable" class="form-control input-sm">
          <option value="10">Select</option>
          <option value="25">Last 15 days(20)</option>
          <option value="50">Todays (5)</option>
          </select> -->
           <!-- <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('sales/recurrence_invoice_list'); ?>" title="Recurrence Invoice List">Recurrence Invoice List </a> -->
                    </div>
                    <!-- /.box-header  -->
                    <div class="box-body">
                        <!--  <h5><?php
                        if (count($currency_converted_records) > 0) {
                            ?>There are <?php echo count($currency_converted_records); ?> records which currency has not converted yet. <?php } ?>Here only converted records are displaying.</h5> -->
                        <div class="box-header" id="filters_applied">
                        </div>
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Invoice Date" data-type="invoice_date">Invoice Date <span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Supplier" data-type="supplier">Supplier<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Reference Number" data-type="reference_number">Reference Number<span class="fa fa-toggle-down mr-20"></span></a>
                                    </th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Nature of supply" data-type="nature_of_supply">Nature of supply<span class="fa fa-toggle-down mr-20"></span></a>
                                    </th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Invoice Number" data-type="invoice">Supplier Invoice Number<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Place of Supply" data-type="place_of_supply">Place of Supply<span class="fa fa-toggle-down mr-20"></span></a>
                                    </th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select GST Payable" data-type="gst_payable">GST Payable on Reverse Charge<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Invoice Date" data-type="supplier_invoice">Supplier Invoice date<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Purchase Order Date" data-type="order_date">Purchase Order Date<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Purchase Order Number" data-type="order_number">Purchase Order Number<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Delivery Challan Number" data-type="delivery_challan_number">Delivery Challan Number<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select E-way Billing" data-type="e_way_billing">E-way Billing<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Enter Taxable Amount" data-type="taxable_amount">Taxable Amount<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select CGST Amount" data-type="cgst">CGST Amount<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select SGST/UTGST Amount" data-type="sgst">SGST/UTGST Amount<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <!-- <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select UTGST" data-type="utgst">UTGST Amount<span class="fa fa-toggle-down mr-20"></span></a></th> -->
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select IGST Amount" data-type="igst">IGST Amount<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Invoice Amount" style="text-decoration: none;color: inherit !important;" data-type="invoice_amount">Invoice Amount <span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Payment Day" data-type="due_day">Payment Day<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <!-- <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Paid Amount" data-type="received_amount">Paid Amount <span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Payment Status" data-type="payment_status">Payment Status <span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Pending Amount" data-type="pending_amount">Pending Amount<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Receivable Date" data-type="receivable_date">Payable Date<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Due Day" data-type="due_day">Due Days</a></th>
                                    -->
                                </tr>
                            </thead>
                            <tbody></tbody>                          
                        </table>

                        <table id="addition_table" class="mt-15 mb-25">
                            <thead>
                                <tr>
                                    <th>Sum Of Taxable Amount</th>
                                    <th>Sum Of CGST Amount</th>
                                    <th>Sum Of SGST/UTGST Amount</th>
                                    <!-- <th>Sum Of UTGST Amount</th> -->
                                    <th>Sum Of IGST Amount</th>
                                    <th>Sum Of Invoice Amount</th> 
                                    <!-- <th>Total Grand Total Amount</th> -->    
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
                                   <!--  <td id="total_list_record6"></td>
                                    <td id="total_list_record7"></td>
                                    <td id="total_list_record8"></td>
                                    <td id="total_list_record9"></td>
                                    <td id="total_list_record10"></td> -->
                                </tr>
                            </tbody>
                        </table>

<!-- <b>Grand Total : <?= $total_sum[0]->sum_of_grand_total; ?></b><br>
<b>Paid Amount : <?= $total_sum[0]->paid_amount; ?></b><br>
<b>Pending Amount  : <?= $total_sum[0]->pending_amount; ?></b><br>
<b>Total Converted Amount  : <?= $converted_sum; ?></b> -->
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
                                    <option value="">Select</option>
                                    <option value="supplier">Supplier</option>
                                    <option value="invoice">Invoice</option>
                                    <option value="invoice_date">Invoice Date</option>
                                    <option value="invoice_amount">Invoice Amount</option>
                                    <option value="received_amount">Paid Amount</option>
                                    <option value="payment_status">Payment Status</option>
                                    <option value="pending_amount">Pending Amount</option>
                                    <option value="receivable_date">Receivable Date</option>
                                    <option value="due_day">Due Day</option>
                                    <option value="nature_of_supply">Nature Of Supply</option>
                                    <option value="place_of_supply">Place Of Supply</option>
                                    <option value="gst_payable">Gst Payable</option>
                                    <option value="supplier_invoice">Supplier Invoice Date</option>
                                    <option value="order_date">Purchase Order Date</option>
                                    <option value="order_number">Purchase Order Number</option>
                                    <option value="delivery_challan_number">Delivery Challan Number</option>
                                    <option value="reference_number">Reference Number</option>
                                    <option value="e_way_billing">E-way Billing</option>
                                    <option value="taxable_amount">Taxable Amount</option>
                                    <option value="taxable_value">Taxable Value</option>
                                    <option value="sgst">sgst</option>
                                    <option value="igst">igst</option>
                                    <option value="cgst">cgst</option>
                                    <option value="utgst">UTGST</option>
                                    <option value="invoice_amount">Invoice Amount</option>
                                    <option value="due">Due Days</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="supplier_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_supplier_name"  name="filter_supplier_name">
                                    <option value="" >Select Supplier</option>
                                    <?php
                                    foreach ($suppliers as $key => $value) {
                                        ?>
                                        <option value="<?= $value->supplier_id ?>"><?= $value->supplier_name ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="invoice_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_invoice_number"  name="filter_invoice_number">
                                    <option value="" >Select Invoice Number</option>
                                    <?php
                                    foreach ($supplier_invoice as $key => $value) {
                                        if (!empty($value->purchase_supplier_invoice_number)) {
                                            ?>
                                            <option value="<?= $value->purchase_supplier_invoice_number ?>"><?= $value->purchase_supplier_invoice_number ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="reference_number_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_reference_number" name="filter_reference_number">
                                    <option value="">Select Reference Number</option>
                                    <?php
                                    foreach ($invoices as $key => $value) {
                                        ?>
                                        <option value="<?= $value->purchase_invoice_number ?>"><?= $value->purchase_invoice_number ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="nature_of_supply_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_nature_of_supply" name="filter_nature_of_supply">
                                    <option value="">Select Nature of supply</option>
                                    <?php foreach ($nature_of_supply as $key => $value) { ?>
                                        <option value="<?= $value->purchase_nature_of_supply ?>">
                                            <?= $value->purchase_nature_of_supply ?></option>
                                    <?php } ?> 
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="place_of_supply_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_place_of_supply"  name="filter_place_of_supply">
                                    <option value="" >Select Place of supply</option>
                                    <?php
                                    foreach ($place_of_supply as $key => $value) {
                                        if (!empty($value->state_name)) {
                                            ?>
                                            <option value="<?= $value->purchase_billing_state_id ?>"><?= $value->state_name ?></option>
                                            <?php
                                        }
                                    }
                                    ?>                                 
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="gst_payable_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_gst_payable" name="filter_gst_payable">
                                    <option value="">Select Gst Payable</option>
                                    <option value="no">No</option>
                                    <option value="yes">yes</option>
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
                        <div class="col-md-12" id="supplier_invoice_from_div">
                            <div class="form-group">
                                <div class="input-group date">
                                    <input type="text" name="filter_supplier_invoice_from" value="" class="form-control datepicker" id="filter_supplier_invoice_from" placeholder="From Date">
                                    <div class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="supplier_invoice_to_div">
                            <div class="form-group">
                                <div class="input-group date">
                                    <input type="text" name="filter_supplier_invoice_to" value="" class="form-control datepicker" id="filter_supplier_invoice_to" placeholder="End date">
                                    <div class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="order_from_div">
                            <div class="form-group">
                                <div class="input-group date">
                                    <input type="text" name="filter_order_from" value="" class="form-control datepicker" id="filter_order_from" placeholder="From Date">
                                    <div class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="order_to_div">
                            <div class="form-group">
                               <div class="input-group date">
                                     <input type="text" name="filter_order_to" value="" class="form-control datepicker" id="filter_order_to" placeholder="End date">
                                    <div class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="order_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_order_number" name="filter_order_number">
                                    <option value="">Select Purchase Order Number</option>
                                    <?php
                                    foreach ($order_number as $key => $value) {
                                        if (!empty($value->purchase_order_number)) {
                                            ?>
                                            <option value="<?= $value->purchase_order_number ?>">
                                                <?= $value->purchase_order_number ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="dilevery_number_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_dilevery_number" name="filter_dilevery_number">
                                    <option value="">Select Delivery Challan Number</option>
                                    <?php
                                    foreach ($delivery_challen_number as $key => $value) {
                                        if (!empty($value->purchase_delivery_challan_number)) {
                                            ?>
                                            <option value="<?= $value->purchase_delivery_challan_number ?>">
                                                <?= $value->purchase_delivery_challan_number ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12" id="e_way_billing_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_e_way_billing" name="filter_e_way_billing">
                                    <option value="">Select E-way Billing</option>
                                    <?php
                                    foreach ($purchase_e_way as $key => $value) {
                                        if (!empty($value->purchase_e_way_bill_number)) {
                                            ?>
                                            <option value="<?= $value->purchase_e_way_bill_number ?>">
                                                <?= $value->purchase_e_way_bill_number ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="cgst_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_cgst"  name="filter_cgst">
                                    <option value="" >Select CGST</option>
                                    <?php
                                    foreach ($cgst as $key => $value) {
                                        ?>
                                        <option value="<?= $value->purchase_cgst_amount ?>"><?= precise_amount($value->purchase_cgst_amount, 2) ?></option>
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
                                    foreach ($sgst as $key => $value) {
                                        ?>
                                        <option value="<?= $value->purchase_sgst_amount ?>"><?= precise_amount($value->purchase_sgst_amount, 2) ?></option>
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
                                    foreach ($utgst as $key => $value) {
                                        ?>
                                        <option value="<?= $value->purchase_sgst_amount ?>"><?= precise_amount($value->purchase_sgst_amount, 2) ?></option>
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
                                    foreach ($igst as $key => $value) {
                                        ?>
                                        <option value="<?= $value->purchase_igst_amount ?>"><?= precise_amount($value->purchase_igst_amount, 2) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="from_taxable_div">
                            <div class="form-group">
                                <input type="number" name="filter_from_taxable_amount" value="" id="filter_from_taxable_amount" class="form-control" placeholder="From ">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_taxable_div">
                            <div class="form-group">
                                <input type="number" name="filter_to_taxable_amount" value=""  id="filter_to_taxable_amount" class="form-control" placeholder="To">
                            </div>
                        </div>

                        <div class="col-md-12" id="from_invoice_div">
                            <div class="form-group">
                                <input type="number" name="filter_from_invoice_amount" value="" id="filter_from_invoice_amount" class="form-control" placeholder="From ">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_invoice_div">
                            <div class="form-group">
                                <input type="number" name="filter_to_invoice_amount" value=""  id="filter_to_invoice_amount" class="form-control" placeholder="To">
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
                    <button type="button" class="btn btn-warning" id="reset-supplier">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-invoice">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-in-date">Reset</button>
                    <!-- <button type="button" class="btn btn-warning" id="reset-in-amt">Reset Amount</button> -->
                    <button type="button" class="btn btn-warning" id="reset-challen-number">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-order-num">Reset </button>
                    <button type="button" class="btn btn-warning" id="reset-nature">Reset</button>
                    <!-- <button type="button" class="btn btn-warning" id="reset-country">Reset Country</button> -->
                    <button type="button" class="btn btn-warning" id="reset-supply">Reset</button>
                    <!-- <button type="button" class="btn btn-warning" id="reset-t-supply">Reset Supply Type</button> -->
                    <button type="button" class="btn btn-warning" id="reset-pur-date">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-order-date">Reset</button>
                    <!-- <button type="button" class="btn btn-warning" id="reset-taxable-amount">Reset Amount</button> -->
                    <button type="button" class="btn btn-warning" id="reset-cgst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-sgst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-igst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-gst-payable">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-invoice-amount">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-invoice-pur-amount">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-reference">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-utgst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-eway">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-due">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<div class="modal fade" id="purchase_report_select_columns" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true">
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

                                <input type="checkbox"  name="supplier" id="supplier" value="1" checked>
                                <label for="supplier">Supplier</label><br>

                                <input type="checkbox" name="reference" id="reference" value="2" checked>
                                <label for="reference">Reference Number</label><br>

                                <input type="checkbox" name="nature_of_supply" id="nature_of_supply" value="3" checked>
                                <label for="nature_of_supply">Nature of Supply</label><br>

                                <input type="checkbox" name="supplier_invoice_number" id="supplier_invoice_number" value="4" checked>
                                <label for="supplier_invoice_number">Supplier Invoice Number</label><br>

                                <input type="checkbox" name="place_of_supply" id="place_of_supply" value="5" checked>
                                <label for="place_of_supply">Place of Supply</label><br>

                                <input type="checkbox" name="purchase_gst_payable" id="purchase_gst_payable" value="6" checked>
                                <label for="purchase_gst_payable">GST Payable on Reverse Charge</label><br>

                                <input type="checkbox" name="supplier_invoice_date" id="supplier_invoice_date" value="7" checked>
                                <label for="supplier_invoice_date">Supplier Invoice date</label><br>

                                <input type="checkbox" name="purchase_order_date" id="purchase_order_date" value="8" checked>
                                <label for="purchase_order_date">Purchase Order Date</label><br>

                                <input type="checkbox" name="purchase_order_number" id="purchase_order_number" value="9" checked>
                                <label for="purchase_order_number">Purchase Order Number</label><br>
                            </div>
                            <div class="col-md-6">
                               <input type="checkbox" name="delivery_challan_number" id="delivery_challan_number" value="10" checked>
                                 <label for="delivery_challan_number">Delivery Challan Number</label><br>
                           
                                <input type="checkbox" name="e_way_billing" id="e_way_billing" value="11" checked>
                                <label for="e_way_billing">E-way Billing</label><br>

                                <input type="checkbox" name="taxable_amount" id="taxable_amount" value="12" checked>
                                <label for="taxable_amount">Taxable Amount</label><br>

                                <input type="checkbox" name="cgst_amount" id="cgst_amount" value="13" checked>
                                <label for="cgst_amount">CGST Amount</label><br>

                                <input type="checkbox" name="sgst_amount" id="sgst_amount" value="14" checked>
                                <label for="sgst_amount">SGST/UTGST Amount</label><br>

                                <input type="checkbox" name="igst_amount" id="igst_amount" value="15" checked>
                                <label for="igst_amount">IGST Amount</label><br>

                                <input type="checkbox" name="invoice_amount" id="invoice_amount" value="16"  checked>
                                <label for="invoice_amount">Invoice Amount</label><br>

                                <input type="checkbox" name="payment_day" id="payment_day" value="17" checked>
                                <label for="payment_day">Payment Day</label><br>
<!-- 
                                <input type="checkbox" name="due" value="16" checked>
                                <label for="product">Due Days</label><br> -->
                                
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
<script>
 
</script>
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
        $("#reset-supplier").click(function () {
            $("#supplier_div .select2-selection__rendered").empty();
            $('#filter_supplier_name option:selected').prop("selected", false)
        })
        $("#reset-invoice-pur-amount").click(function () {
            $('#filter_from_invoice_amount').val('');
            $('#filter_to_invoice_amount').val('');
        })
        $("#reset-invoice-amount").click(function () {
            $('#filter_from_taxable_amount').val('');
            $('#filter_to_taxable_amount').val('');
        })
        $('#reset-cgst').click(function () {
            $("#filter_from_cgst_amount").val('');
            $('#filter_to_cgst_amount').val('');
        });
        $('#reset-sgst').click(function () {
            $("#filter_from_sgst_amount").val('');
            $('#filter_to_sgst_amount').val('');
        });
        $("#reset-utgst").click(function () {
            $("#filter_from_utgst_amount").val('');
            $('#filter_to_utgst_amount').val('');
        })
        $('#reset-igst').click(function () {
            $("#filter_from_igst_amount").val('');
            $('#filter_to_igst_amount').val('');
        });
        $("#reset-order-date").click(function () {
            $('#filter_order_from').val('');
            $('#filter_order_to').val('');
        })
        $("#reset-pur-date").click(function () {
            $('#filter_supplier_invoice_from').val('');
            $('#filter_supplier_invoice_to').val('');
        })
        $('#reset-eway').click(function () {
            $("#e_way_billing_div .select2-selection__rendered").empty();
            $('#filter_e_way_billing option:selected').prop("selected", false)
        })
        $('#reset-challen-number').click(function () {
            $("#dilevery_number_div .select2-selection__rendered").empty();
            $('#filter_dilevery_number option:selected').prop("selected", false)
        })
        $('#reset-order-num').click(function () {
            $("#order_div .select2-selection__rendered").empty();
            $('#filter_order_number option:selected').prop("selected", false)
        })
        $('#reset-gst-payable').click(function () {
            $("#gst_payable_div .select2-selection__rendered").empty();
            $('#filter_gst_payable option:selected').prop("selected", false)
        })
        $('#reset-supply').click(function () {
            $("#place_of_supply_div .select2-selection__rendered").empty();
            $('#filter_place_of_supply option:selected').prop("selected", false)
        })
        $('#reset-reference').click(function () {
            $("#reference_number_div .select2-selection__rendered").empty();
            $('#filter_reference_number option:selected').prop("selected", false)
        })
        $('#reset-nature').click(function () {
            $("#nature_of_supply_div .select2-selection__rendered").empty();
            $('#filter_nature_of_supply option:selected').prop("selected", false)
        })
        $("#reset-invoice").click(function () {
            $("#invoice_div .select2-selection__rendered").empty();
            $('#filter_invoice_number option:selected').prop("selected", false)
        })
        $("#reset-in-date").click(function () {
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
        })
        $("#reset-due").click(function () {
            $('#filter_from_due_day').val('');
            $('#filter_to_due_day').val('');
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
                $("#supplier_div").hide();
                $("#invoice_div").hide();
                $("#from_date_div").hide();
                $("#to_date_div").hide();
                $("#invoice_amount_div").hide();
                $("#received_amount_div").hide();
                $("#payment_status_div").hide();
                $("#pending_amount_div").hide();
                $("#receivable_date_div").hide();
                $("#due_day_div").hide();
                $("#nature_of_supply_div").hide();
                $("#place_of_supply_div").hide();
                $("#supplier_invoice_from_div").hide();
                $("#supplier_invoice_to_div").hide();
                $("#order_from_div").hide();
                $("#order_to_div").hide();
                $("#order_div").hide();
                $("#dilevery_number_div").hide();
                $("#e_way_billing_div").hide();
                $("#order_div").hide();
                $("#sgst_value_div").hide();
                $("#from_taxable_div").hide();
                $("#to_taxable_div").hide();
                $("#from_invoice_div").hide();
                $("#to_invoice_div").hide();
                $("#igst_div").hide();
                $("#cgst_div").hide();
                $("#sgst_div").hide();
                $("#utgst_div").hide();
                $("#gst_payable_div").hide();
                $("#reference_number").hide();
                $('#to_due_day_div').hide();
                $('#from_due_day_div').hide();
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
            } else
            {
                $("#from_due_day_div").hide();
                $('#to_due_day_div').hide();
                $('#reset-due').hide();
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
            if (type == "cgst")
            {
                $("#from_cgst_div").show();
                $("#to_cgst_div").show();
                $("#reset-cgst").show();
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
                $("#reset-sgst").show();
            } else
            {
                $("#from_sgst_div ").hide();
                $("#to_sgst_div").hide();
                $("#sgst_div").hide();
                $("#reset-sgst").hide();
            }
            if (type == "igst")
            {
                $("#from_igst_div").show();
                $("#to_igst_div").show();
                $("#reset-igst").show();
            } else
            {
                $("#from_igst_div").hide();
                $("#to_igst_div").hide();
                $("#igst_div").hide();
                $("#reset-igst").hide();
            }
            if (type == "invoice_amount")
            {
                $("#from_invoice_div").show();
                $("#to_invoice_div").show();
                $("#reset-invoice-pur-amount").show();
            } else
            {
                $("#from_invoice_div").hide();
                $("#to_invoice_div").hide();
                $("#reset-invoice-pur-amount").hide();
            }
            if (type == "taxable_amount")
            {
                $("#from_taxable_div").show();
                $("#to_taxable_div").show();
                $("#reset-invoice-amount").show();
            } else
            {
                $("#from_taxable_div").hide();
                $("#to_taxable_div").hide();
                $("#reset-invoice-amount").hide();
            }
            if (type == "e_way_billing")
            {
                $("#e_way_billing_div").show();
                $("#reset-eway").show();
            } else
            {
                $("#e_way_billing_div").hide();
                $("#reset-eway").hide();
            }
            if (type == "delivery_challan_number")
            {
                $("#dilevery_number_div").show();
                $("#reset-challen-number").show();
            } else
            {
                $("#dilevery_number_div").hide();
                $("#reset-challen-number").hide();
            }
            if (type == "order_number")
            {
                $("#order_div").show();
                $("#reset-order-num").show();
            } else
            {
                $("#order_div").hide();
                $("#reset-order-num").hide();
            }
            if (type == "order_date")
            {
                $("#order_from_div").show();
                $("#order_to_div").show();
                $("#reset-order-date").show();
            } else
            {
                $("#order_from_div").hide();
                $("#order_to_div").hide();
                $("#reset-order-date").hide();
            }
            if (type == "supplier_invoice")
            {
                $("#supplier_invoice_from_div").show();
                $("#supplier_invoice_to_div").show();
                $("#reset-pur-date").show();
            } else
            {
                $("#supplier_invoice_from_div").hide();
                $("#supplier_invoice_to_div").hide();
                $("#reset-pur-date").hide();
            }
            if (type == "place_of_supply")
            {
                $("#place_of_supply_div").show();
                $("#reset-supply").show();
            } else
            {
                $("#place_of_supply_div").hide();
                $("#reset-supply").hide();
            }
            if (type == "reference_number")
            {
                $("#reference_number_div").show();
                $("#reset-reference").show();
            } else
            {
                $("#reference_number_div").hide();
                $("#reset-reference").hide();
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
            if (type == "gst_payable")
            {
                $("#gst_payable_div").show();
                $("#reset-gst-payable").show();
            } else
            {
                $("#gst_payable_div").hide();
                $("#reset-gst-payable").hide();
            }
            if (type == "supplier")
            {
                $("#supplier_div").show();
            } else
            {
                $("#supplier_div").hide();
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
            /*if (type == "invoice_amount")
             {
             $("#invoice_amount_div").show();
             $("#reset-in-amt").show();
             } else
             {
             $("#invoice_amount_div").hide();
             $("#reset-in-amt").hide();
             }*/
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
            // if (type == "due_day")
            // {
            //     $("#due_day_div").show();
            // } else
            // {
            //     $("#due_day_div").hide();
            // }
            if (type == "supplier")
            {
                $("#reset-supplier").show();
                $("#reset-supplier").show();
            } else
            {
                $("#reset-supplier").hide();
                $("#reset-supplier").hide();
            }
        }).change();
        generateOrderTable();
        function resetall() {
            $('#filter_supplier_name option:selected').prop("selected", false)
            $('#filter_invoice_number option:selected').prop("selected", false)
            $('#filter_from_date option:selected').prop("selected", false)
            $('#filter_to_date option:selected').prop("selected", false)
            $('#filter_payment_status option:selected').prop("selected", false)
            $('#filter_received_amount option:selected').prop("selected", false)
            $('#filter_invoice_amount option:selected').prop("selected", false)
            $('#filter_reference_number option:selected').prop("selected", false)
            $('#filter_billing_country option:selected').prop("selected", false)
            $('#filter_nature_supply option:selected').prop("selected", false)
            $('#filter_order_number option:selected').prop("selected", false)
            $('#filter_nature_of_supply option:selected').prop("selected", false);
            $('#filter_cgst option:selected').prop("selected", false)
            $('#filter_sgst option:selected').prop("selected", false)
            $('#filter_igst option:selected').prop("selected", false)
            $('#filter_utgst option:selected').prop("selected", false)
            $('#filter_gst_payable option:selected').prop("selected", false)
            $('#filter_place_of_supply option:selected').prop("selected", false)
            $('#filter_type_of_supply option:selected').prop("selected", false)
            $('#filter_supplier_cn_number option:selected').prop("selected", false)
            $('#filter_supplier_cn_from option:selected').prop("selected", false)
            $('#filter_supplier_cn_to option:selected').prop("selected", false)
            $('#filter_from_taxable_amount option:selected').prop("selected", false)
            $('#filter_to_taxable_amount option:selected').prop("selected", false)
            $('#filter_from_invoice_amount option:selected').prop("selected", false)
            $('#filter_to_invoice_amount option:selected').prop("selected", false)
            $('#filter_from_invoice_amount').val('');
            $('#filter_to_invoice_amount').val('');
            $('#filter_from_taxable_amount').val('');
            $('#filter_to_taxable_amount').val('');
            $('#filter_order_from').val('');
            $('#filter_order_to').val('');
            $('#filter_supplier_invoice_from').val('');
            $('#filter_supplier_invoice_to').val('');
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
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
            appendFilter();
            $("#list_datatable").dataTable().fnDestroy();
            generateOrderTable();
        }
        function generateOrderTable() {
           list_datatable =  $('#list_datatable').DataTable({
                "processing": true,
                "serverSide": true,
                "scrollX": true,
                "iDisplayLength": 50,
                "lengthMenu": [ [10, 25, 50,100, -1], [10, 25, 50,100, "All"] ],
                "ajax": {
                    "url": base_url + "report/purchase_test",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        'filter_supplier_name': $('#filter_supplier_name').val(),
                        'filter_invoice_number': $('#filter_invoice_number').val(),
                        'filter_from_date': $('#filter_from_date').val(),
                        'filter_to_date': $('#filter_to_date').val(),
                        'filter_invoice_amount': $('#filter_invoice_amount').val(),
                        'filter_received_amount': $('#filter_received_amount').val(),
                        'filter_pending_amount': $('#filter_pending_amount').val(),
                        'filter_payment_status': $('#filter_payment_status').val(),
                        'filter_receivable_date': $('#filter_receivable_date').val(),
                        'filter_nature_of_supply': $('#filter_nature_of_supply').val(),
                        'filter_reference_number': $('#filter_reference_number').val(),
                        'filter_place_of_supply': $('#filter_place_of_supply').val(),
                        'filter_gst_payable': $('#filter_gst_payable').val(),
                        'filter_order_number': $('#filter_order_number').val(),
                        'filter_dilevery_number': $('#filter_dilevery_number').val(),
                        'filter_e_way_billing': $('#filter_e_way_billing').val(),
                        /*'filter_cgst': $('#filter_cgst').val(),
                        'filter_sgst': $('#filter_sgst').val(),
                        'filter_igst': $('#filter_igst').val(),
                        'filter_utgst': $('#filter_utgst').val(),*/
                        'filter_cgst_from': $('#filter_from_cgst_amount').val(),
                        'filter_cgst_to': $('#filter_to_cgst_amount').val(),
                        'filter_sgst_from': $("#filter_from_sgst_amount ").val(),
                        'filter_sgst_to': $("#filter_to_sgst_amount").val(),
                        'filter_utgst_from': $('#filter_from_utgst_amount').val(),
                        'filter_utgst_to': $('#filter_to_utgst_amount').val(),
                        'filter_igst_from': $('#filter_from_igst_amount').val(),
                        'filter_igst_to': $('#filter_to_igst_amount').val(),
                        'filter_from_taxable_amount': $('#filter_from_taxable_amount').val(),
                        'filter_to_taxable_amount': $('#filter_to_taxable_amount').val(),
                        'filter_from_invoice_amount': $('#filter_from_invoice_amount').val(),
                        'filter_to_invoice_amount': $('#filter_to_invoice_amount').val(),
                        'filter_supplier_invoice_from': $('#filter_supplier_invoice_from').val(),
                        'filter_supplier_invoice_to': $('#filter_supplier_invoice_to').val(),
                        'filter_order_from': $('#filter_order_from').val(),
                        'filter_order_to': $('#filter_order_to').val(),
                        'filter_from_due_days' : $('#filter_from_due_day').val(),
                        'filter_to_due_days' : $('#filter_to_due_day').val()
                    },
                    "dataSrc"
                            : function (result) {
                                var tfoot = parseFloat(result.total_list_record[0].tot_purchase_taxable_value).toFixed(2);
                                $('#total_list_record1').html(tfoot);
                                var tfoot = parseFloat(result.total_list_record[0].tot_purchase_cgst_amount).toFixed(2);
                                $('#total_list_record2').html(tfoot);
                                var tfoot = parseFloat(result.total_list_record[0].tot_purchase_sgst_amount).toFixed(2);
                                $('#total_list_record3').html(tfoot);
                                var tfoot = parseFloat(result.total_list_record[0].tot_purchase_utgst_amount).toFixed(2);
                                $('#total_list_record4').html(tfoot);
                                var tfoot = parseFloat(result.total_list_record[0].tot_purchase_igst_amount).toFixed(2);
                                $('#total_list_record5').html(tfoot);
                                var tfoot = parseFloat(result.total_list_record[0].tot_purchase_grand_total).toFixed(2);
                                $('#total_list_record6').html(tfoot);
                                /*var tfoot = parseFloat(result.total_list_record[0].tot_credit_note_amount).toFixed(2);
                                 $('#total_list_record6').html(tfoot);
                                 var tfoot = parseFloat(result.total_list_record[0].tot_debit_note_amount).toFixed(2);
                                 $('#total_list_record7').html(tfoot);
                                 var tfoot = parseFloat(result.total_list_record[0].tot_grand_total).toFixed(2);
                                 $('#total_list_record8').html(tfoot);
                                 var tfoot = parseFloat(result.total_list_record[0].tot_purchase_paid_amount).toFixed(2);
                                 $('#total_list_record9').html(tfoot);
                                 var tfoot = parseFloat(result.total_list_record[0].tot_purchase_pending_amount).toFixed(2);
                                 $('#total_list_record10').html(tfoot);*/
                                return result.data;
                            }
                },
                "columns": [
                    {"data": "date"},
                    {"data": "supplier"},
                    {"data": "invoice"},
                    {"data": "nature_of_supply"},
                    {"data": "reference_number"},
                    {"data": "place_of_supply"},
                    {"data": "purchase_gst_payable"},
                    {"data": "purchase_supplier_date"},
                    {"data": "purchase_order_date"},
                    {"data": "purchase_order_number"},
                    {"data": "delivery_challan_number"},
                    {"data": "e_way_bill_number"},
                    {"data": "purchase_taxable_value"},
                    {"data": "purchase_cgst_amount"},
                    {"data": "purchase_sgst_amount"},
                    /*{"data": "utgst"},*/
                    {"data": "purchase_igst_amount"},
                    {"data": "grand_total"},  
                    {"data": "due"}                  
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
                        filename: 'Purchase Report',
                        orientation: 'landscape', //portrait
                        pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                        exportOptions: {
                            columns: ':visible',
                            search: 'applied',
                            order: 'applied'
                        },
                        customize: function (doc) {
                            //Remove the title created by datatTables
                            //doc.content.splice(0, 1);
                            //Create a date string that we use in the footer. Format is dd-mm-yyyy
                            //var now = new Date();
                            //var jsDate = now.getDate() + '-' + (now.getMonth() + 1) + '-' + now.getFullYear();
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
                                            text: 'Purchase Report'
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
                            $('#purchase_report_select_columns').modal('show');
                        }
                        
                    }
                    ],
                'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });
            anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
            
             $(".buttons-pdf").addClass("Purchase");
             $("#loader").hide();
            return true;
        }
        $('#filter_search').click(function () {
            appendFilter();
        });
        function appendFilter() { //button filter event click
            $("#loader").show();

            var filter_supplier_name = "";
            var filter_invoice_date = "";
            var filter_reference_number = "";
            var filter_nature_of_supply = "";
            var filter_invoice_number = ""; 
            var filter_place_of_supply = "";
            var filter_gst_payable = "";
            var filter_supplier_invoice = "";
            var filter_supplier_order = "";
            var filter_order_number = "";
            var filter_dilevery_number = "";
            var filter_e_way_billing = "";
            var filter_taxable_amount = "";
            var filter_cgst = "";
            var filter_sgst = "";
            var filter_utgst = "";
            var filter_igst = "";
            var filter_invoice_amount = "";
            var filter_due = "";
            var filter_sgst_data = "";
            var filter_cgst_data = "";
            var filter_utgst_data = "";
            var filter_igst_data = "";
            
            var label = "";

            var filter_from_date = $('#filter_from_date').val();
            var filter_to_date = $('#filter_to_date').val();
            if (filter_from_date != "" || filter_to_date != "") {
                filter_invoice_date = "<label id='lbl_invoice_date' class='label-style'><b> Invoice date : </b> FROM " + filter_from_date + " TO " + filter_to_date + '<i class="fa fa-times"></i></label>';
            }
            if (filter_invoice_date != '') {
                label += filter_invoice_date + " ";
            }
            $("#filter_supplier_name option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_supplier_name += $this.text() + ", ";
                }
            });
            if (filter_supplier_name != '') {
                label += "<label id='lbl_supplier_name' class='label-style'><b> Supplier Name : </b> " + filter_supplier_name.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_reference_number option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_reference_number += $this.text() + ", ";
                }
            });
            if (filter_reference_number != '') {
                label += "<label id='lbl_reference_name' class='label-style'><b> Purchase Reference Number : </b> " + filter_reference_number.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_nature_of_supply option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_nature_of_supply += $this.text() + ", ";
                }
            });
            if(filter_nature_of_supply != '') {
                label +="<label id='lbl_nature_supply' class='label-style'><b> Nature Of Supply : </b> " + filter_nature_of_supply.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_invoice_number option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_invoice_number += $this.text() + ", ";
                }
            });
            if (filter_invoice_number != '') {
                label += "<label id='lbl_invoice_number' class='label-style'><b> Supplier Invoice Number : </b> " + filter_invoice_number.slice(0, -2) + '<i class="fa fa-times"></i></label>' + " ";
            }
            $("#filter_place_of_supply option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_place_of_supply += $this.text() + ", ";
                }
            });
            if(filter_place_of_supply != '') {
                label += "<label id='lbl_place_supply' class='label-style'><b> Place Of Supply : </b> " + filter_place_of_supply.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_gst_payable option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_gst_payable += $this.text() + ", ";
                }
            });
            if(filter_gst_payable != '') {
                label += "<label id='lbl_payable_on_reverse' class='label-style'><b> Payable On Reverse Charge : </b> " + filter_gst_payable.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            var filter_supplier_invoice_from = $('#filter_supplier_invoice_from').val();
            var filter_supplier_invoice_to = $('#filter_supplier_invoice_to').val();
            if (filter_supplier_invoice_from != "" || filter_supplier_invoice_to != "") {
                filter_supplier_invoice = "<label id='lbl_supplier_invoice_date' class='label-style'><b> Supplier Invoice Date : </b> FROM " + filter_supplier_invoice_from + " TO " + filter_supplier_invoice_to + '<i class="fa fa-times"></i></label>';
            }
            if (filter_supplier_invoice != '') {
                label += filter_supplier_invoice + " ";
            }
            var filter_order_from = $('#filter_order_from').val();
            var filter_order_to = $('#filter_order_to').val();
            if (filter_order_from != "" || filter_order_to != "") {
                filter_supplier_order = "<label id='lbl_supplier_order' class='label-style'><b> Purchase Order Date : </b> FROM " + filter_order_from + " TO " + filter_order_to + '<i class="fa fa-times"></i></label>';
            }
            if (filter_supplier_order != '') {
                label += filter_supplier_order + " ";
            }
            $("#filter_order_number option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_order_number += $this.text() + ", ";
                }
            });
            if(filter_order_number != '') {
                label +="<label id='lbl_order_number' class='label-style'><b> Purchase Order Number : </b> " + filter_order_number.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_dilevery_number option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_dilevery_number += $this.text() + ", ";
                }
            });
            if(filter_dilevery_number != '') {
                label +="<label id='lbl_dilevery_number' class='label-style'><b> Delivery Challan Number : </b> " + filter_dilevery_number.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_e_way_billing option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_e_way_billing += $this.text() + ", ";
                }
            });
            if(filter_e_way_billing != '') {
                label +="<label id='lbl_e_way_billing' class='label-style'><b> E-Way Billing : </b> " + filter_e_way_billing.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            var filter_from_amount = $('#filter_from_taxable_amount').val();
            var filter_to_amount = $('#filter_to_taxable_amount').val();
            if (filter_from_amount != "" || filter_to_amount != "") {
                filter_taxable_amount = "<label id='lbl_taxable_amount' class='label-style'><b> Taxable Amount : </b> FROM " + filter_from_amount + " TO " + filter_to_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_taxable_amount != '') {
                label += filter_taxable_amount + " ";
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
            /*$("#filter_cgst option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_cgst += $this.text() + ", ";
                }
            });
            if(filter_cgst != '') {
                label += "<label id='lbl_cgst' class='label-style'><b> CGST : </b> " + filter_cgst.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_sgst option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_sgst += $this.text() + ", ";
                }
            });
            if(filter_sgst != '') {
                label += "<label id='lbl_sgst' class='label-style'><b> SGST : </b> " + filter_sgst.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_utgst option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_utgst += $this.text() + ", ";
                }
            });
            if(filter_utgst != '') {
                label += "<label id='lbl_utgst' class='label-style'><b> UTGST : </b> " + filter_utgst.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_igst option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_igst += $this.text() + ", ";
                }
            });
            if(filter_igst != '') {
                label += "<label id='lbl_igst' class='label-style'><b> IGST : </b> " + filter_igst.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }*/
            var filter_from_invoice = $('#filter_from_invoice_amount').val();
            var filter_to_invoice = $('#filter_to_invoice_amount').val();
            if (filter_from_invoice != "" || filter_to_invoice != "") {
                filter_invoice_amount = "<label id='lbl_invoice_Amount' class='label-style'><b> Purchase Invoice Amount : </b> FROM " + filter_from_invoice + " TO " + filter_to_invoice + '<i class="fa fa-times"></i></label>';
            }
            if (filter_invoice_amount != '') {
                label += filter_invoice_amount + " ";
            }
            var filter_from_due_day = $('#filter_from_due_day').val();
            var filter_to_due_day = $('#filter_to_due_day').val();
            if (filter_from_due_day != "" || filter_to_due_day != "") {
                filter_due = "<label id='lbl_due' class='label-style'><b> Taxable Amount : </b> FROM " + filter_from_due_day + " TO " + filter_to_due_day + '<i class="fa fa-times"></i></label>';
            }
            if (filter_due != '') {
                label += filter_due + " ";
            }
            /*$("#filter_billing_name option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_billing_name += $this.text() + ", ";
                }
            });
            if(filter_billing_name != '') {
                label +="<label id='lbl_billing_country' class='label-style'><b> Billing Country : </b> " + filter_billing_name + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_customer_billing_name option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_customer_billing_name += $this.text() + ", ";
                }
            });
            if(filter_customer_billing_name != '') {
                label += "<label id='lbl_billing_name' class='label-style'><b> Billing Name : </b> " + filter_customer_billing_name + '<i class="fa fa-times"></i></label>'+" ";
            }
            */

            /*$("#filter_place_of_supply option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_place_of_supply += $this.text() + ", ";
                }
            });
            if(filter_place_of_supply != '') {
                label += "<label id='lbl_place_supply' class='label-style'><b> Place Of Supply : </b> " + filter_place_of_supply + '<i class="fa fa-times"></i></label>'+" ";
            }*/
            /*$("#filter_type_of_supply option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_type_of_supply += $this.text() + ", ";
                }
            });
            if(filter_type_of_supply != '') {
                label += "<label id='lbl_type_supply' class='label-style'><b> Type Of Supply : </b> " + filter_type_of_supply + '<i class="fa fa-times"></i></label>'+" ";
            }*/
            /*$("#filter_billing_currency option:selected").each(function() {
                var $this = $(this);
                if($this.length){
                    filter_billing_currency += $this.text() + ", ";
                }
            });
            if(filter_billing_currency != '') {
                label += "<label id='lbl_bill_currency' class='label-style'><b> Billing Currency : </b> " + filter_billing_currency + '<i class="fa fa-times"></i></label>'+" ";
            }*/
            
            if (label != "") {
                $('#filters_applied').html(label);
            } else {
                $('#filters_applied').html('<label></label>');
            }
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable();
        };
        $(document).on("click",".fa-times",function(){
            $(this).parent('label').remove();
            var label1=$(this).parent('label').attr('id');
            console.log(label1);
            if(label1 == 'lbl_invoice_date'){
                $('#filter_from_date').val('');
                $('#filter_to_date').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_reference_name'){
                $("#reference_number_div .select2-selection__rendered").empty();
                $('#filter_reference_number option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_supplier_name'){
                $("#supplier_div .select2-selection__rendered").empty();
                $('#filter_supplier_name option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_nature_supply'){
                $("#nature_of_supply_div .select2-selection__rendered").empty();
                $('#filter_nature_of_supply option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()               
                generateOrderTable();
            }
            if(label1 == 'lbl_invoice_number'){
                $("#invoice_div .select2-selection__rendered").empty();
                $('#filter_invoice_number option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_place_supply'){
                $("#place_of_supply_div .select2-selection__rendered").empty();
                $('#filter_place_of_supply option:selected').prop("selected", false)
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_payable_on_reverse'){
                $("#gst_payable_div .select2-selection__rendered").empty();
                $('#filter_gst_payable option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_supplier_invoice_date'){
                $('#filter_supplier_invoice_from').val('');
                $('#filter_supplier_invoice_to').val('');
                $("#list_datatable").dataTable().fnDestroy()               
                generateOrderTable();
            }
            if(label1 == 'lbl_supplier_order'){
                $('#filter_order_from').val('');
                $('#filter_order_to').val('');
                $("#list_datatable").dataTable().fnDestroy()               
                generateOrderTable();
            }
            if(label1 == 'lbl_order_number'){
                $("#order_div .select2-selection__rendered").empty();
                $('#filter_order_number option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()               
                generateOrderTable();
            }
            if(label1 == 'lbl_dilevery_number'){
                $("#dilevery_number_div .select2-selection__rendered").empty();
                $('#filter_dilevery_number option:selected').prop("selected", false)
                $("#list_datatable").dataTable().fnDestroy()               
                generateOrderTable();
            }
            if(label1 == 'lbl_e_way_billing'){
                $("#e_way_billing_div .select2-selection__rendered").empty();
                $('#filter_e_way_billing option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()               
                generateOrderTable();
            }          
            if(label1 == 'lbl_taxable_amount'){
                $("#filter_from_taxable_amount").val('');
                $('#filter_to_taxable_amount').val('');
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
            /*if(label1 == 'lbl_cgst'){
                $("#cgst_div .select2-selection__rendered").empty();
                $('#filter_cgst option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_sgst'){
                $("#sgst_div .select2-selection__rendered").empty();
                $('#filter_sgst option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_utgst'){
                $("#utgst_div .select2-selection__rendered").empty();
                $('#filter_utgst option:selected').prop("selected", false)
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_igst'){
                $("#igst_div .select2-selection__rendered").empty();
                $('#filter_igst option:selected').prop("selected", false)
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }*/
            if(label1 == 'lbl_invoice_Amount'){
                $("#filter_from_invoice_amount").val('');
                $('#filter_to_invoice_amount').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_due'){
                $("#filter_from_due_day").val('');
                $('#filter_to_due_day').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
        });
    });
$(document).ready(function(){
    $('#purchase_report_select_columns #select_column').click(function(){
        var checked_arr = [];
        var unchecked_arr = [];

        $.each($('#purchase_report_select_columns input[type="checkbox"]:checked'),function(key,value){
            checked_arr.push(this.value);
        });

        $.each($('#purchase_report_select_columns input[type="checkbox"]:not(:checked)'),function(key,value){
            unchecked_arr.push(this.value);
        });
        list_datatable.columns(checked_arr).visible(true);
        // console.log(checked_arr);

        list_datatable.columns(unchecked_arr).visible(false);
        // console.log(unchecked_arr);
        $('#purchase_report_select_columns').modal('hide');
    });
});
</script>
