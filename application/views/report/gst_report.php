<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<style type="text/css">    
    table tr th a {
    color: #fff;
    white-space: nowrap !important;
    }
    table.dataTable thead .sorting_asc:after{
     display: none;
    }
    .form-group {
        margin: 10px 5px;
    }
    .modal-footer button:nth-child(2) {
    display: inline-block;
    }
    .label-style{
        background: #ddd;
        color: #333;
        padding: 2px 10px;
        font-size: 11px;
        border-radius: 10px;
    }
    div.dataTables_wrapper div.dataTables_filter {
        float: right;
    }
</style>
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
            <div id="loader">
                <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
            </div>
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
                    <div class="box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Sales GST Report</h3>
                           <div class="pull-right double-btn">
                                <button id="refresh_table" class="btn btn-sm btn-info">Refresh</button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <div class="box-header" id="filters_applied">
                                </div>
                                <table id="report_table" class="table table-bordered table-striped table-hover dataTable no-footer">
                                    <thead>
                                        <tr>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#voucher_no_modal" style="text-decoration: none;color: inherit !important;">Voucher No  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#customer_name_modal" style="text-decoration: none;color: inherit !important;">Customer Name  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#gstin_modal" style="text-decoration: none;color: inherit !important;">GSTIN  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#invoice_no_modal" style="text-decoration: none;color: inherit !important;">Invoice Number  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#invoice_date_modal" style="text-decoration: none;color: inherit !important;">Invoice Date  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#invoice_value_modal" style="text-decoration: none;color: inherit !important;">Invoice Value  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#taxable_value_modal" style="text-decoration: none;color: inherit !important;">Taxable Value  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#hsn_modal" style="text-decoration: none;color: inherit !important;">HSN/SAC No  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#qty_modal" style="text-decoration: none;color: inherit !important;">Qty  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#uom_modal" style="text-decoration: none;color: inherit !important;">UOM  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#rate_modal" style="text-decoration: none;color: inherit !important;">Rate  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#place_supply_modal" style="text-decoration: none;color: inherit !important;">Place of Supply  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#rate_per_modal" style="text-decoration: none;color: inherit !important;">Rate(%)  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#cgst_modal" style="text-decoration: none;color: inherit !important;">CGST  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#sgst_modal" style="text-decoration: none;color: inherit !important;">SGST/UTGST <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#igst_modal" style="text-decoration: none;color: inherit !important;">IGST  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <!-- <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#ugst_modal" style="text-decoration: none;color: inherit !important;">UTGST  <span class="fa fa-toggle-down mr-20"></span></a></th> -->
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#rate_cess_modal" style="text-decoration: none;color: inherit !important;">RATE (%)  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                            <th><a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#cess_modal" style="text-decoration: none;color: inherit !important;">Cess  <span class="fa fa-toggle-down mr-20"></span></a></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                    <table id="addition_table" class="mt-15 mb-25">                         
                        <thead>
                            <tr>
                                <th>Sum Of Taxable Amount</th>
                                <th>Sum Of Invoice Amount</th>
                                <th>Sum Of SGST/UTGST Amount</th>
                                <!-- <th>Sum Of UTGST Amount</th> -->
                                <th>Sum Of CGST Amount</th>
                                <th>Sum Of IGST Amount</th>
                                <th>Sum Of CESS Amount</th>
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
                                <td id="total_list_record7"></td>
                            </tr>
                        </tbody>
                    </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="voucher_no_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select Voucher Number</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                           <select class="select2" multiple="multiple"  id="filter_voucher"  name="filter_voucher">
                            <option value="">Please Select Voucher Number</option>
                                <?php
                                    foreach ($voucher_number as $key => $value) {
                                        if($value->sales_invoice_number != ''){
                                    ?>
                                    <option value="<?= $value->sales_invoice_number ?>"><?= $value->sales_invoice_number ?></option>
                                    <?php
                                    }
                                    }
                                ?>                                
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-voucher" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="customer_name_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select Customer</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_customer"  name="filter_customer">
                                <option value="">Please Select Customer</option>
                                <?php
                                    foreach ($customers as $key => $value) {
                                    ?>
                                    <option value="<?= $value->customer_id ?>"><?= $value->customer_name ?></option>
                                    <?php
                                    }
                                    ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-customer" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="gstin_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select GSTIN</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_gstin"  name="filter_gstin">
                                <option value="">Please Select GSTIN</option>
                                <?php
                                    foreach ($customers_gstin as $key => $value) {
                                        if($value->customer_gstin_number != ''){
                                    ?>
                                    <option value="<?= $value->customer_gstin_number ?>"><?= $value->customer_gstin_number ?></option>
                                    <?php
                                    }
                                    }
                                    ?>  
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-gstin" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>
<div class="modal fade" id="invoice_no_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select Invoice Number</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_invoice_number"  name="filter_invoice_number">
                                <option value="">Please Select Invoice Number</option>
                                <?php
                                    foreach ($voucher_number as $key => $value) {
                                        if($value->sales_id != ''){
                                    ?>
                                    <option value="<?= $value->sales_id ?>"><?= $value->sales_invoice_number ?></option>
                                    <?php
                                    }
                                    }
                                ?> 
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-invoice_number" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="invoice_date_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select Invoice Date</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="input-group date">
                                <input type="text" name="from_date" class="form-control datepicker" id="from_date" placeholder="From Date" autocomplete="off">
                                <div class="input-group-addon"> <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="col-sm-12">
                        <div class="form-group">
                            <div class="input-group date">
                                <input type="text" name="to_date" class="form-control datepicker" id="to_date" placeholder="To Date" autocomplete="off">
                                <div class="input-group-addon"> <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-invoice_date" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="invoice_value_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Enter Invoice Value</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="from_invoice_amount" class="form-control" id="from_invoice_amount" placeholder="From" autocomplete="off" min="1">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="to_invoice_amount" class="form-control" id="to_invoice_amount" placeholder="To" autocomplete="off" min="1">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-invoice_value" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="taxable_value_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Enter Taxable Amount</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="from_taxable_amount" class="form-control" id="from_taxable_amount" placeholder="From" autocomplete="off" min="1">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="to_taxable_amount" class="form-control" id="to_taxable_amount" placeholder="To" autocomplete="off" min="1">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-taxable_value" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="hsn_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select HSN/SAC Number</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_hsn_number"  name="filter_hsn_number">
                                <option value="">Please Select HSN/SAC Number</option>
                                <?php
                                    foreach ($hsn_code as $key => $value) {
                                        if($value->hsn_sac_code != ''){
                                    ?>
                                    <option value="<?= $value->hsn_sac_code ?>"><?= $value->hsn_sac_code ?></option>
                                    <?php
                                    }
                                    }
                                ?> 
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-hsn_code" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="qty_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select Quantity</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_quantity"  name="filter_quantity">
                                <option value="">Please Select Quantity</option>
                                <?php
                                    foreach ($quantity as $key => $value) {
                                        if($value->sales_item_quantity != ''){
                                    ?>
                                    <option value="<?= $value->sales_item_quantity ?>"><?= round($value->sales_item_quantity,2) ?></option>
                                    <?php
                                    }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-quantity" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>
<div class="modal fade" id="uom_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select UOM</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_uom"  name="filter_uom">
                                <option value="">Please Select UOM</option>
                                 <?php
                                    foreach ($uom as $key => $value) {
                                        if($value->product_unit_id != ''){
                                    ?>
                                    <option value="<?= $value->product_unit_id ?>"><?= $value->uom ?></option>
                                    <?php
                                    }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-uom" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>
<div class="modal fade" id="rate_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Enter Rate</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="from_price_amount" class="form-control" id="from_price_amount" placeholder="From" autocomplete="off" min="1">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="to_price_amount" class="form-control" id="to_price_amount" placeholder="To" autocomplete="off" min="1">
                        </div>
                    </div>
                </div>
               <!-- <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_price"  name="filter_price">
                                <option value="">Please Select Rate</option>
                                <?php
                                    foreach ($unit_price as $key => $value) {
                                        if($value->sales_item_unit_price != ''){
                                           $price =  round($value->sales_item_unit_price,2);
                                    ?>
                                    <option value="<?= $value->sales_item_unit_price ?>"><?= $price ?></option>
                                    <?php
                                    }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-price" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="place_supply_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select Place of Supply</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_place_of_supply"  name="filter_place_of_supply">
                                <option value="">Please Select Place of Supply</option>
                                <?php
                                    foreach ($place_of_supply as $key => $value) {
                                        if($value->sales_billing_state_id != ''){
                                    ?>
                                    <option value="<?= $value->sales_billing_state_id ?>"><?= $value->state_name ?></option>
                                    <?php
                                    }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-supply" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
    </div>
</div>      
</div>
</div>

<div class="modal fade" id="rate_per_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select Rate</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_gst_rate"  name="filter_gst_rate">   
                            <option value="">Please Select Rate</option>                            
                                <?php
                                    foreach ($tax_percentage_gst as $key => $value) {
                                        if($value->sales_item_tax_percentage != ''){
                                           $tax_percentage =  (float)($value->sales_item_tax_percentage);
                                    ?>
                                    <option value="<?= $value->sales_item_tax_percentage ?>"><?= $tax_percentage.'%' ?></option>
                                    <?php
                                    }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-gst_percent" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="cgst_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Enter CGST Amount</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="from_cgst_amount" class="form-control" id="from_cgst_amount" placeholder="From" autocomplete="off" min="1">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="to_cgst_amount" class="form-control" id="to_cgst_amount" placeholder="To" autocomplete="off" min="1">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-cgst" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="sgst_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Enter SGST/UTGST Amount</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="from_sgst_amount" class="form-control" id="from_sgst_amount" placeholder="From" autocomplete="off" min="1">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="to_sgst_amount" class="form-control" id="to_sgst_amount" placeholder="To" autocomplete="off" min="1">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-sgst" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="igst_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Enter IGST Amount</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="from_igst_amount" class="form-control" id="from_igst_amount" placeholder="From" autocomplete="off" min="1">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="to_igst_amount" class="form-control" id="to_igst_amount" placeholder="To" autocomplete="off" min="1">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-igst" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="ugst_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Enter UTGST Amount</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="from_ugst_amount" class="form-control" id="from_ugst_amount" placeholder="From" autocomplete="off" min="1">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="to_ugst_amount" class="form-control" id="to_ugst_amount" placeholder="To" autocomplete="off" min="1">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-ugst" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>
<div class="modal fade" id="rate_cess_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select CESS Rate</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                             <select class="select2" multiple="multiple"  id="filter_cess_rate"  name="filter_cess_rate">
                                <option value="">Please Select CESS Rate</option>
                                 <?php
                                    foreach ($tax_percentage_cess as $key => $value) {
                                        if($value->sales_item_tax_cess_percentage != ''){
                                           $tax_percentage =  (float)($value->sales_item_tax_cess_percentage);
                                    ?>
                                    <option value="<?= $value->sales_item_tax_cess_percentage ?>"><?= $tax_percentage.'%' ?></option>
                                    <?php
                                    }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-cess_percent" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="cess_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Enter Cess Amount</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="from_cess_amount" class="form-control" id="from_cess_amount" placeholder="From" autocomplete="off" min="1">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                           <input type="number" name="to_cess_amount" class="form-control" id="to_cess_amount" placeholder="To" autocomplete="off" min="1">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-cess" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div> 
    </div>
</div>
<div class="modal fade" id="gst_report_select_columns" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true">
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

                                <input type="checkbox" name="voucher_number" id="voucher_number"  value="0" checked> 
                                <label for="voucher_number" >Voucher No</label><br>

                                <input type="checkbox"  name="customer_name" id="customer_name" value="1" checked>
                                <label for="customer_name">Customer Name</label><br>

                                <input type="checkbox" name="gstin" id="gstin" value="2" checked>
                                <label for="gstin">GSTIN</label><br>

                                <input type="checkbox" name="invoice_number" id="invoice_number" value="3" checked>
                                <label for="invoice_number">Invoice Number</label><br>

                                <input type="checkbox" name="invoice_date" id="invoice_date"  value="4" checked> 
                                <label for="invoice_date" >Invoice Date</label><br>

                                <input type="checkbox"  name="invoice_value" id="invoice_value" value="5" checked>
                                <label for="invoice_value">Invoice Value</label><br>

                                <input type="checkbox" name="taxable_value" id="taxable_value" value="6" checked>
                                <label for="taxable_value">Taxable Value</label><br>

                                <input type="checkbox" name="hsn_sac_num" id="hsn_sac_num" value="7" checked>
                                <label for="hsn_sac_num">HSN/SAC No</label><br>

                                <input type="checkbox" name="qty" id="qty" value="8" checked>
                                <label for="qty">Qty</label><br>

                            </div>
                            <div class="col-md-6">
                                <input type="checkbox" name="uom" id="uom" value="9" checked>
                                <label for="uom">UOM</label><br>

                                <input type="checkbox" name="rate" id="rate" value="10" checked>
                                <label for="rate">Rate</label><br>

                                <input type="checkbox" name="place_of_supply" id="place_of_supply" value="11" checked>
                                <label for="place_of_supply">Place of Supply</label><br>

                                <input type="checkbox" name="rate_percentage" id="rate_percentage" value="12" checked>
                                <label for="rate_percentage">Rate(%)</label><br>

                                <input type="checkbox" name="cgst" id="cgst" value="13" checked>
                                <label for="cgst">CGST</label><br>

                                <input type="checkbox" name="sgst_utgst" id="sgst_utgst" value="14" checked>
                                <label for="sgst_utgst">SGST/UTGST</label><br>

                                <input type="checkbox" name="igst" id="igst" value="15" checked>
                                <label for="igst">IGST</label><br>

                                <input type="checkbox" name="rate_%" id="rate_%" value="16" checked>
                                <label for="rate_%">Rate(%)</label><br>

                                <input type="checkbox" name="cess" id="cess" value="17" checked>
                                <label for="cess">Cess</label><br>


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
<?php $this->load->view('layout/footer'); ?>
<script src="<?php echo base_url('assets/js/') ?>datatable_variable.js"></script>
<script>
   var list_datatable;    
   $(document).ready(function () {

        generateOrderTable();

        $("#reset-voucher").click(function () {
            $("#voucher_no_modal .select2-selection__rendered").empty();
            $('#filter_voucher option:selected').prop("selected", false);
        })

        $("#reset-customer").click(function () {
            $("#customer_name_modal .select2-selection__rendered").empty();
            $('#filter_customer option:selected').prop("selected", false);
        })

        $("#reset-gstin").click(function () {
             $("#gstin_modal .select2-selection__rendered").empty();
            $('#filter_gstin option:selected').prop("selected", false);
        })

        $("#reset-invoice_number").click(function () {
            $("#invoice_no_modal .select2-selection__rendered").empty();
            $('#filter_invoice_number option:selected').prop("selected", false);   
        }) 

        $("#reset-invoice_date").click(function () {
            $('#from_date').val('');
            $('#to_date').val('');
        })

        $("#reset-invoice_value").click(function () {
            $('#from_invoice_amount').val('');
            $('#to_invoice_amount').val('');
        })

        $("#reset-taxable_value").click(function () {
            $('#from_taxable_amount').val('');
            $('#to_taxable_amount').val('');
        })

        $("#reset-hsn_code").click(function () {
             $("#hsn_modal .select2-selection__rendered").empty();
            $('#filter_hsn_number option:selected').prop("selected", false);
        })

        $("#reset-quantity").click(function () {
             $("#qty_modal .select2-selection__rendered").empty();
            $('#filter_quantity option:selected').prop("selected", false);
        })

        $("#reset-uom").click(function () {
             $("#uom_modal .select2-selection__rendered").empty();
            $('#filter_uom option:selected').prop("selected", false);
        })

        $("#reset-price").click(function () {
            $('#from_price_amount').val('');
            $('#to_price_amount').val('');
        })

        $("#reset-supply").click(function () {
            $("#place_supply_modal .select2-selection__rendered").empty();
            $('#filter_place_of_supply option:selected').prop("selected", false);
        })

        $("#reset-gst_percent").click(function () {
            $("#rate_per_modal .select2-selection__rendered").empty();
            $('#filter_gst_rate option:selected').prop("selected", false);
        })

        $("#reset-cgst").click(function () {
            $('#from_cgst_amount').val('');
            $('#to_cgst_amount').val('');
        })

        $("#reset-sgst").click(function () {
            $('#from_sgst_amount').val('');
            $('#to_sgst_amount').val('');
        })

        $("#reset-igst").click(function () {
            $('#from_igst_amount').val('');
            $('#to_igst_amount').val('');
        })

        $("#reset-ugst").click(function () {
            $('#from_ugst_amount').val('');
            $('#to_ugst_amount').val('');
        })
        
        $("#reset-cess_percent").click(function () {
            $("#rate_cess_modal .select2-selection__rendered").empty();
            $('#filter_cess_rate option:selected').prop("selected", false);
        })

        $("#reset-cess").click(function () {
            $('#from_cess_amount').val('');
            $('#to_cess_amount').val('');
        })
        $('#refresh_table').on('click', function(){
            $("#report_table").dataTable().fnDestroy()
            generateOrderTable();
        });
        function resetall() {  
        $('#filter_voucher option:selected').prop("selected", false);
        $('#filter_customer option:selected').prop("selected", false);
        $('#filter_gstin option:selected').prop("selected", false);
        $('#filter_invoice_number option:selected').prop("selected", false);    
        $('#from_date').val('');
        $('#to_date').val('');
        $('#from_invoice_amount').val('');
        $('#to_invoice_amount').val('');
        $('#from_taxable_amount').val('');
        $('#to_taxable_amount').val('');
        $('#filter_hsn_number option:selected').prop("selected", false);
        $('#filter_quantity option:selected').prop("selected", false);
        $('#filter_uom option:selected').prop("selected", false);
       // $('#filter_price option:selected').prop("selected", false);
        $('#from_price_amount').val('');
        $('#to_price_amount').val('');
        $('#filter_place_of_supply option:selected').prop("selected", false);
        $('#filter_gst_rate option:selected').prop("selected", false);
        $('#from_cgst_amount').val('');
        $('#to_cgst_amount').val('');
        $('#from_sgst_amount').val('');
        $('#to_sgst_amount').val('');
        $('#from_igst_amount').val('');
        $('#to_igst_amount').val('');
        $('#from_ugst_amount').val('');
        $('#to_ugst_amount').val('');
        $('#filter_cess_rate option:selected').prop("selected", false);
        $('#from_cess_amount').val('');
        $('#to_cess_amount').val('');
        $('.select2-selection__rendered').empty();
        appendFilter();
        $("#report_table").dataTable().fnDestroy()
            generateOrderTable()
    }

        function generateOrderTable() {
            list_datatable = $("#report_table").DataTable({
                "processing": true,
                "serverSide": true,
                "scrollX": true,
                "iDisplayLength": 50,
                "lengthMenu": [ [10, 25, 50,100, -1], [10, 25, 50,100, "All"] ],
                "ajax": {
                    "url": base_url + "report/gst_report",
                    "dataType": "json",
                    "type": "POST",
                    "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>', 'filter_voucher': $('#filter_voucher').val(),'filter_customer': $('#filter_customer').val(),'filter_gstin': $('#filter_gstin').val(), 'filter_invoice_number': $('#filter_invoice_number').val(), 'from_date': $('#from_date').val(), 'to_date': $('#to_date').val(), 'from_invoice_amount': $('#from_invoice_amount').val(), 'to_invoice_amount': $('#to_invoice_amount').val(), 'from_taxable_amount': $('#from_taxable_amount').val(), 'to_taxable_amount': $('#to_taxable_amount').val(), 'filter_hsn_number': $('#filter_hsn_number').val(),'filter_quantity': $('#filter_quantity').val(),'filter_uom': $('#filter_uom').val(), 'filter_from_price': $('#from_price_amount').val(), 'filter_to_price': $('#to_price_amount').val(), 'filter_place_of_supply': $('#filter_place_of_supply').val(), 'filter_gst_rate': $('#filter_gst_rate').val(), 'from_cgst_amount': $('#from_cgst_amount').val(), 'to_cgst_amount': $('#to_cgst_amount').val(), 'from_sgst_amount': $('#from_sgst_amount').val(), 'to_sgst_amount': $('#to_sgst_amount').val(), 'from_igst_amount': $('#from_igst_amount').val(),'to_igst_amount': $('#to_igst_amount').val(),'from_ugst_amount': $('#from_ugst_amount').val(), 'to_ugst_amount': $('#to_ugst_amount').val(), 'filter_cess_rate': $('#filter_cess_rate').val(), 'from_cess_amount': $('#from_cess_amount').val(), 'to_cess_amount': $('#to_cess_amount').val()},
                    "dataSrc": function (result) {
                            var tfoot = parseFloat(result.total_list_record[0].tot_taxable_value).toFixed(2);
                            $('#total_list_record1').html(tfoot);
                        
                            tfoot = parseFloat(result.total_list_record[0].invoice_value).toFixed(2);
                            $('#total_list_record2').html(tfoot);
                            
                            tfoot = parseFloat(result.total_list_record[0].sgst_amount).toFixed(2);
                            $('#total_list_record3').html(tfoot);
                        
                            tfoot = parseFloat(result.total_list_record[0].utgst_amount).toFixed(2);
                            $('#total_list_record4').html(tfoot);
                            
                            tfoot = parseFloat(result.total_list_record[0].cgst_amount).toFixed(2);
                            $('#total_list_record5').html(tfoot);
                        
                            tfoot = parseFloat(result.total_list_record[0].igst_amount).toFixed(2);
                            $('#total_list_record6').html(tfoot);

                             tfoot = parseFloat(result.total_list_record[0].cess_amount).toFixed(2);
                            $('#total_list_record7').html(tfoot);
                            
                        return result.data;
                    }
                },
                "columns": [
                    {"data": "voucher_number"},
                    {"data": "customer_name"},
                    {"data": "gstin"},
                    {"data": "invoice_number"},
                    {"data": "invoice_date"},
                    {"data": "invoice_value"},
                    {"data": "taxable_value"},
                    {"data": "hsn_code"},
                    {"data": "quantity"},
                    {"data": "uom"},
                    {"data": "rate"},
                    {"data": "place_of_supply"},
                    {"data": "tax_rate"},
                    {"data": "cgst_amount"},
                    {"data": "sgst_amount"},
                    {"data": "igst_amount"},
                    /*{"data": "utgst_amount"},*/
                    {"data": "cess_rate"},
                    {"data": "cess_amount"},
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
                        filename: 'Sales GST Report',
                        orientation: 'landscape', //portrait
                        pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                        exportOptions: {
                            columns: ':visible',
                            search: 'applied',
                            order: 'applied'
                        },
                        customize: function (doc) {
                            //Remove the title created by datatTables
                            doc.content.splice(0, 1);
                            //Create a date string that we use in the footer. Format is dd-mm-yyyy
                            var now = new Date();
                            var jsDate = now.getDate() + '-' + (now.getMonth() + 1) + '-' + now.getFullYear();
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
                                            text: 'Sales GST Report'
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
                                            alignment: 'left',
                                            text: ['Created on: ', {text: jsDate.toString()}]
                                        },
                                        {
                                            alignment: 'right',
                                            text: ['page ', {text: page.toString()}, ' of ', {text: pages.toString()}]
                                        }
                                    ],
                                    margin: 20
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
                            $('#gst_report_select_columns').modal('show');
                    }
                    
                    }],
                'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });
             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
            $("#loader").hide();
        }
        $('.filter_search').click(function () {
            appendFilter();
        });
        function appendFilter() { //button filter event click 
            $("#loader").show();

            var filter_voucher = "";
            var filter_customer = "";
            var filter_gstin = "";
            var filter_invoice_number = "";
            var filter_invoice_date = "";
            var filter_invoice_amount = "";
            var filter_taxable_amount = "";
            var filter_hsn_number = "";
            var filter_quantity = "";
            var filter_uom = "";
            var filter_price = "";
            var filter_place_of_supply = "";
            var filter_gst_rate = "";
            var filter_cgst = "";
            var filter_sgst = "";
            var filter_utgst = "";
            var filter_igst = "";
            var filter_cess_rate = "";
            var filter_cess_amount = "";

            var label = "";
            $("#filter_voucher option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_voucher += $this.text() + ", ";
                }
            });
            if (filter_voucher != '') {
                label += "<label id='lbl_voucher' class='label-style'><b> Voucher Number : </b> " + filter_voucher.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            } 
             $("#filter_customer option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_customer += $this.text() + ", ";
                }
            });
            if (filter_customer != '') {
                label += "<label id='lbl_customer_name' class='label-style'><b> Customer Name : </b> " + filter_customer.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            } 
            $("#filter_gstin option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_gstin += $this.text() + ", ";
                }
            });
            if (filter_gstin != '') {
                label += "<label id='lbl_gstin' class='label-style'><b> GSTIN : </b> " + filter_gstin.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_invoice_number option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_invoice_number += $this.text() + ", ";
                }
            });
            if (filter_invoice_number != '') {
                label += "<label id='lbl_invoice_number' class='label-style'><b> Invoice Number : </b> " + filter_invoice_number.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if (from_date != "" || to_date != "") {
                filter_invoice_date = "<label id='lbl_invoice_date' class='label-style'><b> Invoice Date : </b> FROM " + from_date + " TO " + to_date + '<i class="fa fa-times"></i></label>';
            }
            if (filter_invoice_date != '') {
                label += filter_invoice_date + " ";
            } 
            var from_invoice_amount = $('#from_invoice_amount').val();
            var to_invoice_amount = $('#to_invoice_amount').val();
            if (from_invoice_amount != "" || to_invoice_amount != "") {
                filter_invoice_amount = "<label id='lbl_invoice_amount' class='label-style'><b> Invoice Value : </b> FROM " + from_invoice_amount + " TO " + to_invoice_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_invoice_amount != '') {
                label += filter_invoice_amount + " ";
            }
            var from_taxable_amount = $('#from_taxable_amount').val();
            var to_taxable_amount = $('#to_taxable_amount').val();
            if (from_taxable_amount != "" || to_taxable_amount != "") {
                filter_taxable_amount = "<label id='lbl_taxable_amount' class='label-style'><b> Taxable Value : </b> FROM " + from_taxable_amount + " TO " + to_taxable_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_taxable_amount != '') {
                label += filter_taxable_amount + " ";
            }
            $("#filter_hsn_number option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_hsn_number += $this.text() + ", ";
                }
            });
            if (filter_hsn_number != '') {
                label += "<label id='lbl_hsn_number' class='label-style'><b> HSN/SAC Number : </b> " + filter_hsn_number.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_quantity option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_quantity += $this.text() + ", ";
                }
            });
            if (filter_quantity != '') {
                label += "<label id='lbl_quantity' class='label-style'><b> Quantity : </b> " + filter_quantity.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_uom option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_uom += $this.text() + ", ";
                }
            });
            if (filter_uom != '') {
                label += "<label id='lbl_uom' class='label-style'><b> UOM : </b> " + filter_uom.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            var from_price = $('#from_price_amount').val();
            var to_price = $('#to_price_amount').val();
            if (from_price != "" || to_price != "") {
                filter_price = "<label id='lbl_price' class='label-style'><b> RATE : </b> FROM " + from_price + " TO " + to_price + '<i class="fa fa-times"></i></label>';
            }
            if (filter_price != '') {
                label += filter_price + " ";
            }

           
            $("#filter_place_of_supply option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_place_of_supply += $this.text() + ", ";
                }
            });
            if (filter_place_of_supply != '') {
                label += "<label id='lbl_place_of_supply' class='label-style'><b> Place Of Supply : </b> " + filter_place_of_supply.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_gst_rate option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_gst_rate += $this.text() + ", ";
                }
            });
            if (filter_gst_rate != '') {
                label += "<label id='lbl_gst_rate' class='label-style'><b> GST Rate : </b> " + filter_gst_rate.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            var from_cgst_amount = $('#from_cgst_amount').val();
            var to_cgst_amount = $('#to_cgst_amount').val();
            if (from_cgst_amount != "" || to_cgst_amount != "") {
                filter_cgst = "<label id='lbl_cgst' class='label-style'><b> CGST : </b> FROM " + from_cgst_amount + " TO " + to_cgst_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_cgst != '') {
                label += filter_cgst + " ";
            }
            var from_sgst_amount = $('#from_sgst_amount').val();
            var to_sgst_amount = $('#to_sgst_amount').val();
            if (from_sgst_amount != "" || to_sgst_amount != "") {
                filter_sgst = "<label id='lbl_sgst' class='label-style'><b> SGST/UTGST : </b> FROM " + from_sgst_amount + " TO " + to_sgst_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_sgst != '') {
                label += filter_sgst + " ";
            }
            var from_ugst_amount = $('#from_ugst_amount').val();
            var to_ugst_amount = $('#to_ugst_amount').val();
            if (from_ugst_amount != "" || to_ugst_amount != "") {
                filter_utgst = "<label id='lbl_utgst' class='label-style'><b> UTGST : </b> FROM " + from_ugst_amount + " TO " + to_ugst_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_utgst != '') {
                label += filter_utgst + " ";
            }
            var from_igst_amount = $('#from_igst_amount').val();
            var to_igst_amount = $('#to_igst_amount').val();
            if (from_igst_amount != "" || to_igst_amount != "") {
                filter_igst = "<label id='lbl_igst' class='label-style'><b> IGST : </b> FROM " + from_igst_amount + " TO " + to_igst_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_igst != '') {
                label += filter_igst + " ";
            }
            $("#filter_cess_rate option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_cess_rate += $this.text() + ", ";
                }
            });
            if (filter_cess_rate != '') {
                label += "<label id='lbl_cess_rate' class='label-style'><b> CESS Rate : </b> " + filter_cess_rate.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            var from_cess_amount = $('#from_cess_amount').val();
            var to_cess_amount = $('#to_cess_amount').val();
            if (from_cess_amount != "" || to_cess_amount != "") {
                filter_cess_amount = "<label id='lbl_cess_amount' class='label-style'><b> Cess Amount : </b> FROM " + from_cess_amount + " TO " + to_cess_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_cess_amount != '') {
                label += filter_cess_amount + " ";
            }
            if (label != "") {
                $('#filters_applied').html(label);
            } else {
                $('#filters_applied').html('<label></label>');
            }       
            $("#report_table").dataTable().fnDestroy()
            generateOrderTable()
        };
    $(document).on('click',".fa-times",function(){
            $(this).parent('label').remove();
            var label1=$(this).parent('label').attr('id');
            console.log(label1);
            if(label1 == 'lbl_voucher'){
                $("#voucher_no_modal .select2-selection__rendered").empty();
                $('#filter_voucher option:selected').prop("selected", false);
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_customer_name'){
                $("#customer_name_modal .select2-selection__rendered").empty();
                $('#filter_customer option:selected').prop("selected", false);
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_gstin'){
                $("#gstin_modal .select2-selection__rendered").empty();
                $('#filter_gstin option:selected').prop("selected", false);
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_invoice_number'){
                $("#invoice_no_modal .select2-selection__rendered").empty();
                $('#filter_invoice_number option:selected').prop("selected", false);
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_invoice_date'){
                $('#from_date').val('');
                $('#to_date').val('');
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_invoice_amount'){
                $('#from_invoice_amount').val('');
                $('#to_invoice_amount').val('');
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_taxable_amount'){
                $('#from_taxable_amount').val('');
                $('#to_taxable_amount').val('');
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_taxable_amount'){
                $('#from_taxable_amount').val('');
                $('#to_taxable_amount').val('');
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_hsn_number'){
                $("#hsn_modal .select2-selection__rendered").empty();
                $('#filter_hsn_number option:selected').prop("selected", false);
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_quantity'){
                $("#qty_modal .select2-selection__rendered").empty();
                $('#filter_quantity option:selected').prop("selected", false);
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_uom'){
                $("#uom_modal .select2-selection__rendered").empty();
                $('#filter_uom option:selected').prop("selected", false);
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_price'){
               $('#from_price_amount').val('');
                $('#to_price_amount').val('');
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_place_of_supply'){
                $("#place_supply_modal .select2-selection__rendered").empty();
                $('#filter_place_of_supply option:selected').prop("selected", false);
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_gst_rate'){
                $("#rate_per_modal .select2-selection__rendered").empty();
                $('#filter_gst_rate option:selected').prop("selected", false);
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_cgst'){
                $('#from_cgst_amount').val('');
                $('#to_cgst_amount').val('');
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_sgst'){
                $('#from_sgst_amount').val('');
                $('#to_sgst_amount').val('');
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_utgst'){
                $('#from_ugst_amount').val('');
                $('#to_ugst_amount').val('');
                $("#report_table").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_igst'){
                $('#from_igst_amount').val('');
                $('#to_igst_amount').val('');
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_cess_rate'){
                $("#rate_cess_modal .select2-selection__rendered").empty();
                $('#filter_cess_rate option:selected').prop("selected", false);
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_cess_amount'){
                $('#from_cess_amount').val('');
                $('#to_cess_amount').val('');
                $("#report_table").dataTable().fnDestroy()
                generateOrderTable();
            }
        });
    });
$(document).ready(function(){
    $('#gst_report_select_columns #select_column').click(function(){
        var checked_arr = [];
        var unchecked_arr = [];

        $.each($('#gst_report_select_columns input[type="checkbox"]:checked'),function(key,value){
            checked_arr.push(this.value);
        });

        $.each($('#gst_report_select_columns input[type="checkbox"]:not(:checked)'),function(key,value){
            unchecked_arr.push(this.value);
        });
        
        list_datatable.columns(checked_arr).visible(true);

        list_datatable.columns(unchecked_arr).visible(false);
       
        $('#gst_report_select_columns ').modal('hide');
    });
});
</script>