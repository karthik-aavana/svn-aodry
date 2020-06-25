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
    #list_datatable tr td{
        white-space: nowrap;
    }

    #list_datatable th a span{
        float: none;
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
    <!-- Content Header (Page header) -->
    <section class="content-header">
    </section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Purchase Credit Note Reports</li>
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
                        <h3 class="box-title">Purchase Credit Note Reports</h3>
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
                    <div class="box-body">
                        <div class="table-responsive">
                        <div class="box-header" id="filters_applied">
                        </div>
                            <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover" >
                                <thead>
                                    <tr>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Invoice Date" data-type="invoice_date">Invoice Date <span class="fa fa-toggle-down mr-20"></span></a>
                                        </th>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Credit Note Number" data-type="invoice">Credit Note Number<span class="fa fa-toggle-down mr-20"></span></a>
                                        </th>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select supplier" data-type="supplier">Supplier<span class="fa fa-toggle-down mr-20"></span></a></th>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Purchase Reference Number" data-type="reference_number">Purchase  Reference Number <span class="fa fa-toggle-down mr-20"></span></a></th>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Nature of Supply" data-type="nature_of_supply">Nature of supply<span class="fa fa-toggle-down mr-20"></span></a>
                                        </th>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Billing Country" data-type="billing_country">Billing Country<span class="fa fa-toggle-down mr-20"></span></a>
                                        </th>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Place of Supply" data-type="place_of_supply">Place of Supply<span class="fa fa-toggle-down mr-20"></span></a>
                                        </th>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Type of Supply" data-type="Type_of_Supply">Type of Supply<span class="fa fa-toggle-down mr-20"></span></a>
                                        </th>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Supplier Debit Note Number" data-type="credit_note_number">Supplier Debit Note Number<span class="fa fa-toggle-down mr-20"></span></a>
                                        </th>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Supplier Debit Note Date" data-type="credit_note_date">Supplier Debit Note Date<span class="fa fa-toggle-down mr-20"></span></a>
                                        </th>

<!-- <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Billing Currency" data-type="billing_Currency">Billing Currency<span class="fa fa-toggle-down mr-20"></span></a>
</th> -->
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select GST Payable on reverse charge" data-type="gst_payable">Gst Payable On Reverse Charge<span class="fa fa-toggle-down mr-20"></span></a></th>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Enter Taxable Amount" data-type="taxable_amount">Taxable Amount<span class="fa fa-toggle-down mr-20"></span></a></th>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select CGST" data-type="cgst">CGST<span class="fa fa-toggle-down mr-20"></span></a>
                                        </th>
                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select SGST/UTGST" data-type="sgst">SGST/UTGST<span class="fa fa-toggle-down mr-20"></span></a>
                                        </th>
                                        <!-- <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select UTGST" data-type="utgst">UTGST<span class="fa fa-toggle-down mr-20"></span></a></th> -->

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select IGST" data-type="igst">IGST<span class="fa fa-toggle-down mr-20"></span></a>
                                        </th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Enter Purchase Credit Note Amount" data-type="credit_note_amount">Purchase Credit Note Amount<span class="fa fa-toggle-down mr-20"></span></a>
                                        </th>

<!-- <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Payment Status" data-type="payment_status">Payment To <span class="fa fa-toggle-down mr-20"></span></a></th> -->



                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>                  
                        <table id="addition_table" class="mt-15 mb-25">
                            <thead>
                                <tr>
                                    <th>Sum Of Taxable Amount</th>
                                    <th>Sum Of Taxable CGST Amount</th>
                                    <th>Sum Of Taxable SGST/UTGST Amount</th>
                                    <!-- <th>Sum Of Taxable UTGST Amount</th> -->
                                    <th>Sum Of Taxable IGST Amount</th>
                                    <th>Sum Of Invoice Amount:</th>
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
                    </div>
                </div>
            </div>
        </div>
    </section>
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
                                    <option value="">select</option>
                                    <option value="supplier">supplier</option>
                                    <option value="invoice">Invoice</option>
                                    <option value="invoice_date">Invoice Date</option>
                                    <option value="invoice_amount">Invoice Amount</option>
                                    <option value="nature_of_supply">Nature of Supply</option>
                                    <option value="taxable_amount">Taxable Value</option>
                                    <option value="cgst">cgst</option>
                                    <option value="sgst">sgst</option>
                                    <option value="igst">igst</option>
                                    <option value="utgst">UTGST</option>
                                    <option value="gst_payable">gst payable</option>
                                    <option value="credit_note_amount">Credit Note Amount</option>
                                    <option value="reference_number">reference number</option>
                                    <option value="place_of_supply">Place Of Supply</option>
                                    <option value="Type_of_Supply">Type Of Supply</option>
                                    <option value="credit_note_number">Credit Note Number</option>
                                    <option value="credit_note_date">Credit Note Date</option>
                                    <option value="billing_country">Billing Country</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="supplier_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_supplier_name" name="filter_supplier_name">
                                    <option value="">Select Supplier</option>
                                    <?php foreach ($suppliers as $key => $value) { ?>
                                        <option value="<?= $value->supplier_id ?>">
                                            <?= $value->supplier_name ?></option>
                                    <?php } ?>
                                </select>
                                <!--  <input type="text" name="filter_supplier_name" value="" class="form-control" id="filter_supplier_name" placeholder="supplier Name"> -->
                            </div>
                        </div>
                        <div class="col-md-12" id="invoice_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_invoice_number" name="filter_invoice_number">
                                    <option value="">Select Credit Note Number</option>
                                    <?php foreach ($invoices as $key => $value) { ?>
                                        <option value="<?= $value->purchase_credit_note_invoice_number ?>">
                                            <?= $value->purchase_credit_note_invoice_number ?></option>
                                    <?php } ?>
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
                        <!-- <div class="col-md-12" id="invoice_amount_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_invoice_amount" name="filter_invoice_amount">
                                    <option value="">Select Invoice Amount</option>
                        <?php foreach ($invoice_amount as $key => $value) { ?>
                                                        <option value="<?= $value->purchase_debit_note_invoice_amount ?>">
                            <?= $value->purchase_debit_note_invoice_amount ?></option>
                        <?php } ?>
                                </select>
                            </div>
                        </div> -->
                        <div class="col-md-12" id="reference_number_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_reference_number" name="filter_reference_number">
                                    <option value="">Select reference number</option>
                                    <?php foreach ($reference_number as $key => $value) { ?>
                                        <option value="<?= $value->purchase_invoice_number ?>">
                                            <?= $value->purchase_invoice_number ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="billing_country_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_billing_country" name="filter_billing_country">
                                    <option value="">Select Billing Country</option>
                                    <?php foreach ($billing_country as $key => $value) { ?>
                                        <option value="<?= $value->country_id ?>">
                                            <?= $value->country_name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="nature_supply_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_nature_supply" name="filter_nature_supply">
                                    <option value="">Select Nature of Supply</option>
                                    <?php foreach ($nature_of_supply as $key => $value) { ?>
                                        <option value="<?= $value->purchase_credit_note_nature_of_supply ?>">
                                            <?= $value->purchase_credit_note_nature_of_supply ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <!-- <div class="col-md-12" id="receivable_date_div">
                            <div class="form-group">
                                <input type="text" name="filter_receivable_date" value="" class="form-control datepicker" id="filter_receivable_date" placeholder="Receivable Date">
                            </div>
                        </div> -->
                        <!-- <div class="col-md-12" id="taxable_value_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_taxable_value" name="filter_taxable_value">
                                    <option value="">Select Taxable Value</option>
                        <?php foreach ($taxable_value as $key => $value) { ?>
                                                        <option value="<?= $value->purchase_debit_note_taxable_value ?>">
                            <?= $value->purchase_debit_note_taxable_value ?></option>
                        <?php } ?>
                                </select>
                            </div>
                        </div> -->
                        <div class="col-md-12" id="cgst_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_cgst" name="filter_cgst">
                                    <option value="">Select Tax Value</option>
                                    <?php foreach ($cgst as $key => $value) { ?>
                                        <option value="<?= $value->purchase_credit_note_cgst_amount ?>">
                                            <?= precise_amount($value->purchase_credit_note_cgst_amount, 2) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="sgst_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_sgst" name="filter_sgst">
                                    <option value="">Select Taxable Value</option>
                                    <?php foreach ($sgst as $key => $value) { ?>
                                        <option value="<?= $value->purchase_credit_note_sgst_amount ?>">
                                            <?= precise_amount($value->purchase_credit_note_sgst_amount, 2) ?></option>
                                    <?php } ?>
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
                                        <option value="<?= $value->purchase_credit_note_sgst_amount ?>"><?= precise_amount($value->purchase_credit_note_sgst_amount, 2) ?></option>
                                        <?php
                                    }
                                    ?> 
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="igst_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_igst" name="filter_igst">
                                    <option value="">Select Taxable Value</option>
                                    <?php foreach ($igst as $key => $value) { ?>
                                        <option value="<?= $value->purchase_credit_note_igst_amount ?>">
                                            <?= precise_amount($value->purchase_credit_note_igst_amount, 2) ?></option>
                                    <?php } ?>
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
                        <div class="col-md-12" id="place_of_supply_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_place_of_supply"  name="filter_place_of_supply">
                                    <option value="" >Select Place of supply</option>
                                    <?php
                                    foreach ($place_of_supply as $key => $value) {
                                        if (!empty($value->state_name)) {
                                            ?>
                                            <option value="<?= $value->purchase_credit_note_billing_state_id ?>"><?= $value->state_name ?></option>
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
                                    <option value="" >Select type of supply</option>
                                    <?php
                                    foreach ($type_of_supply as $key => $value) {
                                        ?>
                                        <option value="<?= $value->purchase_credit_note_type_of_supply ?>"><?= $value->purchase_credit_note_type_of_supply ?></option>
                                        <?php
                                    }
                                    ?>                                 
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="supplier_dn_number_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_supplier_cn_number"  name="filter_supplier_cn_number">
                                    <option value="" >Select Supplier Credit Note Number</option>
                                    <?php
                                    foreach ($credit_note_number as $key => $value) {
                                        if (!empty($value->purchase_supplier_credit_note_number)) {
                                            ?>
                                            <option value="<?= $value->purchase_supplier_credit_note_number ?>"><?= $value->purchase_supplier_credit_note_number ?></option>
                                            <?php
                                        }
                                    }
                                    ?>                                 
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="supplier_dn_from_div">
                            <div class="form-group">
                                <div class="input-group date">
                                    <input type="text" name="filter_supplier_cn_from" value="" class="form-control datepicker" id="filter_supplier_cn_from" placeholder="From Date">
                                    <div class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="supplier_dn_to_div">
                            <div class="form-group">
                                <div class="input-group date">
                                    <input type="text" name="filter_supplier_cn_to" value="" class="form-control datepicker" id="filter_supplier_cn_to" placeholder="End date">
                                    <div class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button name="filter_search" type="button" class="btn btn-primary" id="filter_search" value="filter" data-dismiss="modal">Apply</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <!-- <button type="reset" class="btn btn-danger" id="reset-all">Reset All</button> -->
                    <button type="button" class="btn btn-warning" id="reset-supplier">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-invoice">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-in-date">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-in-amt">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-rec-amt">Recived Amount</button>
                    <button type="button" class="btn btn-warning" id="reset-status">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-amt">Pending Amount</button>
                    <button type="button" class="btn btn-warning" id="reset-reference">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-nature">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-country">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-supply">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-t-supply">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-dn-number">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-dn-date">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-taxable-amount">Reset </button>
                    <!-- <button type="button" class="btn btn-warning" id="reset-taxable">Reset Taxable</button> -->
                    <button type="button" class="btn btn-warning" id="reset-cgst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-sgst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-igst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-gst-payable">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-invoice-amount">Reset </button>
                    <button type="button" class="btn btn-warning" id="reset-utgst">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="purchase_credit_report_select_columns" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true" data-backdrop="static">
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

                                <input type="checkbox" name="credit_note_number" id="credit_note_number"  value="1" checked> 
                                <label for="credit_note_number" >Credit Note Number</label><br>

                                <input type="checkbox"  name="supplier" id="supplier" value="2" checked>
                                <label for="supplier">Supplier</label><br>

                                <input type="checkbox" name="purchase_reference_number" id="purchase_reference_number" value="3" checked>
                                <label for="purchase_reference_number">Purchase  Reference Number</label><br>

                                <input type="checkbox" name="nature_of_supply" id="nature_of_supply" value="4" checked>
                                <label for="nature_of_supply">Nature of Supply</label><br>

                                <input type="checkbox" name="billing_country" id="billing_country" value="5" checked>
                                <label for="billing_country">Billing Country </label><br>

                                <input type="checkbox" name="place_of_supply" id="place_of_supply" value="6" checked>
                                <label for="place_of_supply">Place of Supply</label><br>

                                 <input type="checkbox" name="sales_type_of_supply" id="sales_type_of_supply" value="7" checked>
                                <label for="sales_type_of_supply">Type of Supply</label><br>

                             </div>
                            <div class="col-md-6">
                                <input type="checkbox" name="supplier_debit_note_number" id="supplier_debit_note_number" value="8" checked>
                                <label for="supplier_debit_note_number">Supplier Debit Note Number</label><br>

                                <input type="checkbox" name="supplier_debit_note_date" id="supplier_debit_note_date" value="9" checked>
                                <label for="supplier_debit_note_date">Supplier Debit Note Date</label><br>

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
                                <label for="grand_total">Purchase Credit Note Amount</label><br>
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
<?php
$this->load->view('layout/footer');
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
        });
        $("#reset-invoice").click(function () {
            $("#invoice_div .select2-selection__rendered").empty();
            $('#filter_invoice_number option:selected').prop("selected", false)
        });
        $("#reset-in-date").click(function () {
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
        });
        $("#reset-dn-date").click(function () {
            $('#filter_supplier_cn_from').val('');
            $('#filter_supplier_cn_to').val('');
        });

        $("#reset-reference").click(function () {
            $("#reference_number_div .select2-selection__rendered").empty();
            $('#filter_reference_number option:selected').prop("selected", false)
        });
        $("#reset-country").click(function () {
            $("#billing_country_div .select2-selection__rendered").empty();
            $('#filter_billing_country option:selected').prop("selected", false)
        });
        $("#reset-supply").click(function () {
            $("#place_of_supply_div .select2-selection__rendered").empty();
            $('#filter_place_of_supply option:selected').prop("selected", false)
        });
        $("#reset-t-supply").click(function () {
            $("#type_of_supply_div .select2-selection__rendered").empty();
            $('#filter_type_of_supply option:selected').prop("selected", false)
        });
        $("#reset-dn-number").click(function () {
            $("#supplier_dn_number_div .select2-selection__rendered").empty();
            $('#filter_supplier_cn_number option:selected').prop("selected", false)
        });
        $("#reset-taxable-amount").click(function () {
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
        });
        $("#reset-in-date").click(function () {
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
        });
        $("#reset-taxable-amount").click(function () {
            $('#filter_from_taxable_amount').val('');
            $('#filter_to_taxable_amount').val('');
        });
        $('#reset-in-amt').click(function () {
            $("#invoice_amount_div .select2-selection__rendered").empty();
            $('#filter_invoice_amount option:selected').prop("selected", false)
        });
        $('#reset-nature').click(function () {
            $("#nature_supply_div .select2-selection__rendered").empty();
            $('#filter_nature_supply option:selected').prop("selected", false)
        });
        $('#reset-rec-amt').click(function () {
            $("#received_amount_div .select2-selection__rendered").empty();
            $('#filter_received_amount option:selected').prop("selected", false)
        });
        $('#reset-status').click(function () {
            $("#payment_status_div .select2-selection__rendered").empty();
            $('#filter_payment_status option:selected').prop("selected", false)
        });
        $('#reset-gst-payable').click(function () {
            $("#gst_payable_div .select2-selection__rendered").empty();
            $('#filter_gst_payable option:selected').prop("selected", false)
        });
        $('#reset-cgst').click(function () {
            $("#filter_from_cgst_amount").val('');
            $('#filter_to_cgst_amount').val('');
        });
        $('#reset-sgst').click(function () {
            $("#filter_from_sgst_amount").val('');
            $('#filter_to_sgst_amount').val('');
        });
        $('#reset-igst').click(function () {
            $("#filter_from_igst_amount").val('');
            $('#filter_to_igst_amount').val('');
        });
        $("#reset-utgst").click(function () {
            $("#filter_from_utgst_amount").val('');
            $('#filter_to_utgst_amount').val('');
        });
        $('#reset-amt').click(function () {
            $("#pending_amount_div .select2-selection__rendered").empty();
            $('#filter_pending_amount option:selected').prop("selected", false)
        });
        $('#reset-invoice-amount').click(function () {
            $("#filter_from_invoice_amount").val('');
            $('#filter_to_invoice_amount').val('');
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
                $("#supplier_div").hide();
                $("#invoice_div").hide();
                $("#from_date_div").hide();
                $("#to_date_div").hide();
                $("#invoice_amount_div").hide();
                $("#purchase_reference_div").hide();
                $("#billing_country_div").hide();
                $("#nature_supply_div").hide();
                $("#receivable_date_div").hide();
                $("#taxable_value_div").hide();
                $("#reference_number_div").hide();
                $("#billing_country_div").hide();
                //$("#nature_supply_div").hide();
                $("#cgst_div").hide();
                $("#sgst_div").hide();
                $("#igst_div").hide();
                $("#gst_payable_div").hide();
                $("#place_of_supply_div").hide();
                $("#type_of_supply_div").hide();
                $("#supplier_dn_number_div").hide();
                $("#supplier_dn_from_div").hide();
                $("#supplier_dn_to_div").hide();
                $("#from_taxable_div").hide();
                $("#to_taxable_div").hide();
                $("#from_invoice_div").hide();
                $("#to_invoice_div").hide();
                $("#utgst_div").hide();
                $("#from_sgst_div ").hide();
                $("#to_sgst_div").hide();
                $("#from_cgst_div").hide();
                $("#to_cgst_div").hide();
                $("#from_utgst_div").hide();
                $("#to_utgst_div").hide();
                $("#from_igst_div").hide();
                $("#to_igst_div").hide();
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
            if (type == "credit_note_amount") {
                $("#from_invoice_div").show();
                $("#to_invoice_div").show();
                $('#reset-invoice-amount').show();
            } else {
                $("#from_invoice_div").hide();
                $("#to_invoice_div").hide();
                $('#reset-invoice-amount').hide();
            }
            if (type == "billing_country") {
                $("#billing_country_div").show();
                $("#reset-country").show();
            } else {
                $("#billing_country_div").hide();
                $("#reset-country").hide();
            }
            if (type == "nature_of_supply") {
                $("#nature_supply_div").show();
                $("#reset-nature_supply").show();
                $("#reset-nature").show();
            } else {
                $("#nature_supply_div").hide();
                $("#reset-nature_supply").hide();
                $("#reset-nature").hide();
            }
            if (type == "taxable_amount")
            {
                $("#from_taxable_div").show();
                $("#to_taxable_div").show();
                $("#reset-taxable-amount").show();
            } else
            {
                $("#from_taxable_div").hide();
                $("#to_taxable_div").hide();
                $("#reset-taxable-amount").hide();
            }
            if (type == "credit_note_date") {
                $("#supplier_dn_from_div").show();
                $("#supplier_dn_to_div").show();
                $("#reset-dn-date").show();
            } else {
                $("#supplier_dn_from_div").hide();
                $("#supplier_dn_to_div").hide();
                $("#reset-dn-date").hide();
            }
            if (type == "credit_note_number") {
                $("#supplier_dn_number_div").show();
                $("#reset-dn-number").show();
            } else {
                $("#supplier_dn_number_div").hide();
                $("#reset-dn-number").hide();
            }
            if (type == "Type_of_Supply") {
                $("#type_of_supply_div").show();
                $("#reset-t-supply").show();
            } else {
                $("#type_of_supply_div").hide();
                $("#reset-t-supply").hide();
            }
            if (type == "place_of_supply") {
                $("#place_of_supply_div").show();
                $("#reset-supply").show();
            } else {
                $("#place_of_supply_div").hide();
                $("#reset-supply").hide();
            }
            if (type == "reference_number") {
                $("#reference_number_div").show();
                $("#reset-reference").show();
            } else {
                $("#reference_number_div").hide();
                $("#reset-reference").hide();
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
            if (type == "supplier")
            {
                $("#reset-supplier").show();
            } else
            {
                $("#reset-supplier").hide();
            }
            if (type == "taxable_amount")
            {
                $("#from_taxable_div").show();
                $("#to_taxable_div").show();
            } else
            {
                $("#from_taxable_div").hide();
                $("#to_taxable_div").hide();
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
                $('#reset-sgst').hide();
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
                $('#reset-cgst').hide();
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
                $('#reset-igst').hide();
            }
            if (type == "gst_payable")
            {
                $("#gst_payable_div").show();
                $('#gst_payable-igst').show();
                $('#reset-gst-payable').show();
            } else
            {
                $("#gst_payable_div").hide();
                $('#reset-gst-payable').hide();
            }
        }).change();
        generateOrderTable();
        function resetall() {
            $('#filter_supplier_name option:selected').prop("selected", false)
            $('#filter_invoice_number option:selected').prop("selected", false)
            $('#filter_from_date option:selected').prop("selected", false)
            $('#filter_to_date option:selected').prop("selected", false)
            $('#filter_reference_number option:selected').prop("selected", false)
            $('#filter_billing_country option:selected').prop("selected", false)
            $('#filter_nature_supply option:selected').prop("selected", false)
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
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
            $('#filter_supplier_cn_from').val('');
            $('#filter_supplier_cn_to').val('');
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
            $('#filter_from_invoice_amount').val('');
            $('#filter_to_invoice_amount').val('');
            $('#filter_from_taxable_amount').val('');
            $('#filter_to_taxable_amount').val('');
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
            $('#list_datatable').dataTable().fnDestroy();
            generateOrderTable();
        }
        function generateOrderTable() {
             list_datatable = $('#list_datatable').DataTable({
                "processing": true,
                "serverSide": true,
                "scrollX": true,
                "iDisplayLength": 50,
                "lengthMenu": [ [10, 25, 50,100, -1], [10, 25, 50,100, "All"] ],
                "ajax": {
                    "url": base_url + "report/purchase_credit_note_report",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        'filter_supplier_name': $('#filter_supplier_name').val(),
                        'filter_invoice_number': $('#filter_invoice_number').val(),
                        'filter_from_date': $('#filter_from_date').val(),
                        'filter_to_date': $('#filter_to_date').val(),
                        //'filter_invoice_amount': $('#filter_invoice_amount').val(),
                        //'filter_purchase_reference': $('#filter_purchase_reference').val(),
                        'filter_nature_supply': $('#filter_nature_supply').val(),
                        'filter_billing_country': $('#filter_billing_country').val(),
                        //'filter_receivable_date': $('#filter_receivable_date').val(),
                        //'filter_taxable_value': $('#filter_taxable_value').val(),
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
                        'filter_gst_payable': $('#filter_gst_payable').val(),
                        //'filter_credit_note_amount': $('#filter_credit_note_amount').val(),
                        'filter_reference_number': $('#filter_reference_number').val(),
                        'filter_type_of_supply': $('#filter_type_of_supply').val(),
                        'filter_place_of_supply': $('#filter_place_of_supply').val(),
                        'filter_supplier_cn_number': $('#filter_supplier_cn_number').val(),
                        'filter_supplier_cn_to': $('#filter_supplier_cn_to').val(),
                        'filter_supplier_cn_from': $('#filter_supplier_cn_from').val(),
                        'filter_from_taxable_amount': $('#filter_from_taxable_amount').val(),
                        'filter_to_taxable_amount': $('#filter_to_taxable_amount').val(),
                        'filter_from_invoice_amount': $('#filter_from_invoice_amount').val(),
                        'filter_to_invoice_amount': $('#filter_to_invoice_amount').val()
                    },
                    "dataSrc": function (result) {
                        var tfoot = parseFloat(result.total_list_record[0].tot_purchase_cn_taxable_value).toFixed(2);
                        $('#total_list_record1').html(tfoot);
                        // tfoot = "";
                        tfoot = parseFloat(result.total_list_record[0].tot_purchase_cn_cgst_amount).toFixed(2);
                        $('#total_list_record2').html(tfoot);
                        tfoot = parseFloat(result.total_list_record[0].tot_purchase_cn_sgst_amount).toFixed(2);
                        $('#total_list_record3').html(tfoot);
                        tfoot = parseFloat(result.total_list_record[0].tot_purchase_cn_utgst_amount).toFixed(2);
                        $('#total_list_record4').html(tfoot);
                        tfoot = parseFloat(result.total_list_record[0].tot_purchase_cn_igst_amount).toFixed(2);
                        $('#total_list_record5').html(tfoot);
                        //$('#total_list_record2').html(tfoot);
                        tfoot = parseFloat(result.total_list_record[0].tot_purchase_cn_total_amount).toFixed(2);
                        $('#total_list_record6').html(tfoot);
                        return result.data;
                    }
                },
                "columns": [
                    {"data": "date"},
                    {"data": "invoice"},
                    {"data": "supplier"},
                    {"data": "purchase_invoice_number"},
                    {"data": "nature_of_supply"},
                    {"data": "billing_country"},
                    {"data": "place_of_supply"},
                    {"data": "type_of_supply"},
                    {"data": "debit_note_number"},
                    {"data": "debit_note_date"},
                    //{"data": "billing_currency"},
                    {"data": "credit_note_gst_payable"},
                    {"data": "taxable"},
                    {"data": "cgst"},
                    {"data": "sgst"},
                    /*{"data": "utgst"},*/
                    {"data": "igst"},
                    {"data": "grand_total"}
                    // {"data": "paid_amount"},
                    // {"data": "from_account"},
                    // {"data": "pending_amount"},
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
                        filename: 'Purchase Credit Note Report',
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
                                            text: 'Purchase Credit Note Report'
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
                            $('#purchase_credit_report_select_columns').modal('show');
                        }
                        
                    }
                    ],
                'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });
                anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
            $("#loader").hide();
            return true;
        }
        $('#filter_search').click(function () {
            appendFilter();
        });
        function appendFilter() {
            $("#loader").show();

            var filter_supplier_name = "";
            var filter_invoice_date = "";
            var filter_reference_number = "";
            var filter_nature_supply = "";
            var filter_billing_country = "";
            var filter_invoice_number = ""; 
            var filter_place_of_supply = "";
            var filter_type_of_supply = "";
            var filter_supplier_cn_number = "";
            var filter_supplier_cn = "";
            var filter_gst_payable = "";
            var filter_supplier_invoice = "";
            var filter_supplier_order = "";
            var filter_order_number = "";
            var filter_dilevery_number = "";
            var filter_taxable_amount = "";
            /*var filter_cgst = "";
            var filter_sgst = "";
            var filter_utgst = "";
            var filter_igst = "";*/
            var filter_invoice_amount = "";
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
            $("#filter_invoice_number option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_invoice_number += $this.text() + ", ";
                }
            });
            if (filter_invoice_number != '') {
                label += "<label id='lbl_invoice_number' class='label-style'><b> Credit Note Number : </b> " + filter_invoice_number.slice(0, -2) + '<i class="fa fa-times"></i></label>' + " ";
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
            $("#filter_nature_supply option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_nature_supply += $this.text() + ", ";
                }
            });
            if(filter_nature_supply != '') {
                label +="<label id='lbl_nature_supply' class='label-style'><b> Nature Of Supply : </b> " + filter_nature_supply.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_billing_country option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_billing_country += $this.text() + ", ";
                }
            });
            if(filter_billing_country != '') {
                label +="<label id='lbl_billing_country' class='label-style'><b> Billing Country : </b> " + filter_billing_country.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
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
            $("#filter_type_of_supply option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_type_of_supply += $this.text() + ", ";
                }
            });
            if(filter_type_of_supply != '') {
                label += "<label id='lbl_type_supply' class='label-style'><b> Type Of Supply : </b> " + filter_type_of_supply.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_supplier_cn_number option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_supplier_cn_number += $this.text() + ", ";
                }
            });
            if(filter_supplier_cn_number != '') {
                label +="<label id='lbl_supplier_cn_number' class='label-style'><b> Supplier Debit Note Number : </b> " + filter_supplier_cn_number.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            var filter_supplier_cn_from = $('#filter_supplier_cn_from').val();
            var filter_supplier_cn_to = $('#filter_supplier_cn_to').val();
            if (filter_supplier_cn_from != "" || filter_supplier_cn_to != "") {
                filter_supplier_cn = "<label id='lbl_supplier_cn_date' class='label-style'><b> Supplier Debit Note Date : </b> FROM " + filter_supplier_cn_from + " TO " + filter_supplier_cn_to + '<i class="fa fa-times"></i></label>';
            }
            if (filter_supplier_cn != '') {
                label += filter_supplier_cn + " ";
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
           /* $("#filter_cgst option:selected").each(function () {
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
                filter_invoice_amount = "<label id='lbl_invoice_Amount' class='label-style'><b> Purchase Credit Note Amount : </b> FROM " + filter_from_invoice + " TO " + filter_to_invoice + '<i class="fa fa-times"></i></label>';
            }
            if (filter_invoice_amount != '') {
                label += filter_invoice_amount + " ";
            }
            if (label != "") {
                $('#filters_applied').html(label);
            } else {
                $('#filters_applied').html('<label></label>');
            }
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable();
        }
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
            if(label1 == 'lbl_invoice_number'){
                $("#invoice_div .select2-selection__rendered").empty();
                $('#filter_invoice_number option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_supplier_name'){
                $("#supplier_div .select2-selection__rendered").empty();
                $('#filter_supplier_name option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_reference_name'){
                $("#reference_number_div .select2-selection__rendered").empty();
                $('#filter_reference_number option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_nature_supply'){
                $("#nature_supply_div .select2-selection__rendered").empty();
                $('#filter_nature_supply option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()               
                generateOrderTable();
            }
            if(label1 == 'lbl_billing_country'){
                $("#billing_country_div .select2-selection__rendered").empty();
                $('#filter_billing_country option:selected').prop("selected", false)
                $("#list_datatable").dataTable().fnDestroy()               
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
                $('#filter_type_of_supply option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            if(label1 == 'lbl_supplier_cn_number'){
                $("#supplier_cn_number_div .select2-selection__rendered").empty();
                $('#filter_supplier_cn_number option:selected').prop("selected", false)
                $("#list_datatable").dataTable().fnDestroy()               
                generateOrderTable();
            }
             if(label1 == 'lbl_supplier_cn_date'){
                $('#filter_supplier_cn_from').val('');
                $('#filter_supplier_cn_to').val('');
                $("#list_datatable").dataTable().fnDestroy()               
                generateOrderTable();
            }
            if(label1 == 'lbl_payable_on_reverse'){
                $("#gst_payable_div .select2-selection__rendered").empty();
                $('#filter_gst_payable option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy();
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
        });
    });
$(document).ready(function(){
    $('#purchase_credit_report_select_columns #select_column').click(function(){
        var checked_arr = [];
        var unchecked_arr = [];

        $.each($('#purchase_credit_report_select_columns input[type="checkbox"]:checked'),function(key,value){
            checked_arr.push(this.value);
        });

        $.each($('#purchase_credit_report_select_columns input[type="checkbox"]:not(:checked)'),function(key,value){
            unchecked_arr.push(this.value);
        });
        list_datatable.columns(checked_arr).visible(true);
        // console.log(checked_arr);

        list_datatable.columns(unchecked_arr).visible(false);
        // console.log(unchecked_arr);
        $('#purchase_credit_report_select_columns').modal('hide');
    });
});
</script>