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

div.dataTables_wrapper div.dataTables_filter {
    float: right;
}

    .select2-container {
        width: 100% !important;
    }
</style>
<div class="content-wrapper">  
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('report/all_reports'); ?>">Report</a></li>
            <li class="active">Refund Voucher Report</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <div id="loader">
                <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
            </div>
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
                        <h3 class="box-title">Refund Voucher Report</h3>
                        <div class="pull-right double-btn">
                            <a class="btn btn-sm btn-info btn-flat back_button" href1="<?php echo base_url()?>report/all_reports">Back</a>
                            <button id="refresh_table" class="btn btn-sm btn-info">Refresh</button>
                        </div>
                    </div>
                    <div class="box-body">
                        <!-- <h5><?php
                            if (count($currency_converted_records) > 0)
                            {
                                ?>There are <?php echo count($currency_converted_records); ?> records which currency has not converted yet. <?php } ?>Here only converted records are displaying.</h5> -->
                        <div class="table-responsive">
                        <div class="box-header" id="filters_applied">
                        </div>
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Voucher Date" data-type="invoice_date">Voucher Date <span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Voucher Number" data-type="invoice">Voucher Number<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Customer" data-type="customer">Customer<span class="fa fa-toggle-down mr-20"></span></a></th>
                                   <!-- <th>Invoice</th> -->
                                   <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Advance Reference Number" data-type="payment_status">Advance Reference Number <span class="fa fa-toggle-down mr-20"></span></a></th>

                                   <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Refund Amount" data-type="refund_amount">Refund Amount <span class="fa fa-toggle-down mr-20"></span></a></th>

                                   <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Billing Country" data-type="billing_country">Billing Country<span class="fa fa-toggle-down mr-20"></span></a></th>

                                   <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Place of Supply" data-type="place_of_supply">Place of Supply<span class="fa fa-toggle-down mr-20"></span></a>
                                    </th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Select Billing Currency" data-type="billing_Currency">Billing Currency<span class="fa fa-toggle-down mr-20"></span></a>
                                    </th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select payment Mode" data-type="payment_mode">Payment Mode<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Enter Taxable Amount" data-type="taxable_value">Taxable Amount<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Enter CGST" data-type="cgst">CGST<span class="fa fa-toggle-down mr-20"></span></a>
                                    </th>
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Enter SGST/UTGST" data-type="sgst">SGST/UTGST<span class="fa fa-toggle-down mr-20"></span></a>
                                    </th>
                                    <!-- <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter UTGST" data-type="utgst">UTGST<span class="fa fa-toggle-down mr-20"></span></a></th> -->
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal" data-toggle="modal" data-target="#distinct-modal" style="text-decoration: none;color: inherit !important;" data-title="Please Enter IGST" data-type="igst">IGST<span class="fa fa-toggle-down mr-20"></span></a>
                                    </th>

                                   <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Refund Voucher Amount" data-type="receipt_amount">Refund Voucher amount<span class="fa fa-toggle-down mr-20"></span></a></th>
                                                          
                                    <!-- <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Receipt Amount" data-type="received_amount">Amount<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    
                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select From Account" data-type="pending_amount">From Account<span class="fa fa-toggle-down mr-20"></span></a></th> -->
                                    <!-- <th style="min-width: 50px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Receivable Date" data-type="receivable_date">Tax amount<span class="fa fa-toggle-down mr-20"></span></a></th>
                                     <th style="min-width: 80px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select CGST" data-type="taxable_value">CGST<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th style="min-width: 80px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select SGST" data-type="sgst">SGST<span class="fa fa-toggle-down mr-20"></span></a></th>
                                    <th style="min-width: 80px;text-align: center;"><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select IGST" data-type="igst">IGST<span class="fa fa-toggle-down mr-20"></span></a></th> -->
                                </tr>
                            </thead>
                            <tbody></tbody>                            
                        </table> 
                        </div>                       
                        <table id="addition_table" class="mt-15 mb-25">
                            <thead>
                                <tr>
                                    <th>Total Taxable Amount</th>
                                    <th>Total CGST Amount</th>
                                    <th>Total SGST/UTGST Amount</th>
                                    <!-- <th>Total UTGST Amount</th> -->
                                    <th>Total IGST Amount</th>                                
                                    <th>Total Voucher Amount</th>
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

<!-- <p><b>Sum of Grand Total : </b><?= $sum[0]->grand_total ?></p>
<p><b>Sum of Taxable : </b><?= $sum[0]->taxable_value ?></p>
<p><b>Sum of Igst : </b><?= $sum[0]->igst_sum ?></p>
<p><b>Sum of cgst : </b><?= $sum[0]->cgst_sum ?></p>
<p><b>Sum of sgst : </b><?= $sum[0]->sgst_sum ?></p> -->
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
                                    <option value="invoice">Voucher Number</option>
                                    <option value="invoice_date">Voucher Date</option>
                                    <option value="invoice_amount">Invoice Amount</option>
                                    <option value="received_amount">Received Amount</option>
                                    <option value="payment_status">Advance Reference Number</option>
                                    <option value="pending_amount">Pending Amount</option>
                                    <option value="receivable_date">Receivable Date</option>
                                    <option value="due_day">Due Day</option>
                                    <option value="utgst">UTGST</option>                           
                                    <option value="billing_country">Billing Country</option>
                                    <option value="place_of_supply">Place Of Supply</option>
                                    <option value="billing_Currency">Billing Currency</option>
                                    <option value="taxable_value">Taxable Value</option>
                                    <option value="cgst">cgst</option>
                                    <option value="sgst">sgst</option>
                                    <option value="igst">igst</option>
                                    <option value="receipt_amount">Refund Voucher amount</option>
                                    <option value="refund_amount"> Refund Amount</option>
                                     <option value="payment_mode"> Payment Mode</option>
                                    
                                    <!-- <option value="gst_payable">gst payable</option> -->
                                </select>
                            </div>
                        </div>
                        
                         <!-- Voucher DAte-->
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
                        <!-- Voucher Number-->
                        <div class="col-md-12" id="invoice_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_invoice_number"  name="filter_invoice_number">
                                    <option value="" >Select Voucher Number</option>
                                    <?php
                                    foreach ($invoices as $key => $value)
                                    {
                                        ?>
                                        <option value="<?= $value->refund_id ?>"><?= $value->voucher_number ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- Advance Customer-->
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
                                
                            </div>
                        </div>

                          <!-- Advance Reference Number-->

                        <div class="col-md-12" id="payment_status_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_payment_status"  name="filter_payment_status">
                                    <option value="" >Select Advance Reference Number</option>
                                    <?php
                                    foreach ($reference_number as $key => $value)
                                    {
                                        ?>
                                        <option value="<?= $value->reference_id ?>"><?= $value->reference_number ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- Refund Amount-->
                        <div class="col-md-12" id="refund_amount_div">
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="from_refund_amount" class="form-control" id="from_refund_amount" placeholder="From Refund Amount" autocomplete="off" min="1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="to_refund_amount" class="form-control" id="to_refund_amount" placeholder="To Refund Amount" autocomplete="off" min="1">
                                </div>
                            </div>                               
                        </div>

                        <!-- Billing Country-->
                        <div class="col-md-12" id="billing_country_div">
                            <select class="select2" multiple="multiple"  id="filter_billing_country"  name="filter_billing_country">
                                    <option value="" >Select  Billing Country</option>
                                    <?php
                                    foreach ($country as $key => $value)
                                    {
                                        ?>
                                        <option value="<?= $value->country_id ?>"><?= $value->country_name ?></option>
                                        <?php
                                    }
                                    ?>
                            </select>
                        </div>
                        <!-- Place of Supply-->
                        <div class="col-md-12" id="place_of_supply_div">
                            <select class="select2" multiple="multiple"  id="filter_place_of_supply"  name="filter_place_of_supply">
                                    <option value="" >Select  Place of Supply</option>                                    
                                    <?php
                                    foreach ($place_of_supply as $key => $value){
                                        ?>
                                        <option value="<?= $value->billing_state_id ?>"><?= $value->state_name ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                        </div>
                        <!-- Billing Currency-->
                        <div class="col-md-12" id="billing_Currency_div">
                            <select class="select2" multiple="multiple"  id="filter_billing_currency"  name="filter_billing_currency">
                                    <option value="" >Select Billing Currency</option>
                                    
                                    <?php
                                    foreach ($refund_currency as $key => $value)
                                    {
                                        ?>
                                        <option value="<?= $value->currency_id ?>"><?= $value->currency_name ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                        </div>
                        <!-- Payment Mode-->
                        <div class="col-md-12" id="payment_mode_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_payment_mode"  name="filter_payment_mode">
                                    <option value="" >Select Payment Mode</option>
                                    <?php
                                    foreach ($to_account as $key => $value)
                                    {
                                        ?>
                                        <option value="<?= $value->to_account ?>"><?= $value->to_account ?></option>
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
                                        <option value="<?= $value->voucher_sgst_amount ?>"><?= precise_amount($value->voucher_sgst_amount, 2) ?></option>
                                        <?php
                                    }
                                    ?> 
                                </select>
                            </div>
                        </div>
                        <!--  Taxable Amount-->
                        <div class="col-md-12" id="taxable_value_div">                           
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
                        <!--  CGST Amount-->
                        <div class="col-md-12" id="cgst_value_div">
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
                        <!--  SGST Amount-->
                        <div class="col-md-12" id="sgst_value_div">
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
                         <!--  IGST Amount-->
                        <div class="col-md-12" id="igst_value_div">
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
                        <div class="col-md-12" id="from_utgst_div">
                            <div class="form-group">
                                <input type="number" name="utgst_from_number" value="" id="filter_from_utgst_amount" class="form-control" placeholder="From UTGST Amount">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_utgst_div">
                            <div class="form-group">
                                <input type="number" name="utgst_to_number" value=""  id="filter_to_utgst_amount" class="form-control" placeholder="To UTGST Amount">
                            </div>
                        </div>
                        <!-- Refund Voucher Amount-->
                        <div class="col-md-12" id="received_amount_div">
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="from_reciept_amount" class="form-control" id="from_reciept_amount" placeholder="From" autocomplete="off" min="1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="to_reciept_amount" class="form-control" id="to_reciept_amount" placeholder="To" autocomplete="off" min="1">
                                </div>
                            </div>                               
                        </div>
                        <div class="col-md-12" id="pending_amount_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_pending_amount"  name="filter_pending_amount">
                                    <option value="" >Select From account</option>
                                    <?php
                                    // print_r($to_account);
                                    foreach ($from_account as $key => $value)
                                    {
                                        ?>
                                        <option value="<?= $value->from_account ?>"><?= $value->from_account ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="receivable_date_div">
                            <!--<div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_due"  name="filter_due">
                                    <option value="" >Select Taxable Value</option>
                                    <?php
                                    foreach ($taxable as $key => $value)
                                    {
                                        ?>
                                        <option value="<?= $value->voucher_tax_amount ?>"><?= $value->voucher_tax_amount ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div> -->
                            
                        </div>
                        <div class="col-md-12" id="invoice_amount_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_invoice_amount"  name="filter_invoice_amount">
                                    <option value="" >Select Country</option>
                                    <?php
                                    foreach ($country as $key => $value)
                                    {
                                        ?>
                                        <option value="<?= $value->country_id ?>"><?= $value->country_name ?></option>
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
                    <!-- <button type="reset" class="btn btn-danger" id="reset-all">Reset All</button> -->
                    <button type="button" class="btn btn-warning" id="reset-customer">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-invoice">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-in-date">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-in-amt">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-rec-amt">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-ref-amt">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-status">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-mode">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-utgst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-amt">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-taxable">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-cgst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-sgst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-igst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-country">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-supply">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-currency">Reset</button>
                    <!-- <button type="button" class="btn btn-warning" id="reset-gst-payable">Reset gst payable</button> -->
                </div>
            </form>
        </div>
    </div>
</div>
</div>


<!-- modals ends -->
<div class="modal fade" id="refund_voucher_report_select_columns" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true">
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
                                <label for="date" >Voucher Date</label><br>

                                <input type="checkbox"  name="voucher_number" id="voucher_number" value="1" checked>
                                <label for="voucher_number">Voucher Number</label><br>

                                <input type="checkbox" name="customer" id="customer" value="2" checked>
                                <label for="customer">Customer</label><br>

                                <input type="checkbox" name="advance_voucher_number" id="advance_voucher_number" value="3" checked>
                                <label for="advance_voucher_number">Advance Reference Number</label><br>

                                <input type="checkbox" name="refund_amount" id="refund_amount" value="4" checked>
                                <label for="refund_amount">Refund Amount</label><br>

                                <input type="checkbox" name="billing_country" id="billing_country" value="5" checked>
                                <label for="billing_country">Billing Country </label><br>

                                <input type="checkbox" name="place_of_supply" id="place_of_supply" value="6" checked>
                                <label for="place_of_supply">Place of Supply</label><br>

                            </div>
                            <div class="col-md-6">
                                <input type="checkbox" name="billing_currency" id="billing_currency" value="7" checked>
                                <label for="billing_currency">Billing Currency</label><br>

                                <input type="checkbox" name="payment_mode" id="payment_mode" value="8" checked>
                                <label for="payment_mode">Payment Mode</label><br>

                                <input type="checkbox" name="sales_taxable_value" id="sales_taxable_value" value="9" checked>
                                <label for="sales_taxable_value">Taxable Amount</label><br>

                                <input type="checkbox" name="sales_cgst_amount" id="sales_cgst_amount" value="10" checked>
                                <label for="sales_cgst_amount">CGST</label><br>

                                <input type="checkbox" name="sales_sgst_amount" id="sales_sgst_amount" value="11"  checked>
                                <label for="sales_sgst_amount">SGST/UTGST</label><br>

                                <input type="checkbox" name="sales_igst_amount" id="sales_igst_amount" value="12" checked>
                                <label for="sales_igst_amount">IGST</label><br>

                                <input type="checkbox" name="grand_total" id="grand_total" value="13" checked>
                                <label for="grand_total">Refund Voucher amount</label><br>
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
        $("#reset-utgst").click(function () {
            $("#filter_from_utgst_amount").val('');
            $('#filter_to_utgst_amount').val('');
        })
        $("#reset-invoice").click(function () {
            $("#invoice_div .select2-selection__rendered").empty();
            $('#filter_invoice_number option:selected').prop("selected", false)
        })

        $("#reset-supply").click(function () {
            $("#place_of_supply_div .select2-selection__rendered").empty();
            $('#filter_place_of_supply option:selected').prop("selected", false)
        })

        
        $("#reset-in-date").click(function () {
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
        })

        $("#reset-ref-amt").click(function () {
            $('#from_refund_amount').val('');
            $('#to_refund_amount').val('');
        })

        $("#reset-taxable").click(function () {
            $('#from_taxable_amount').val('');
            $('#to_taxable_amount').val('');
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

        $("#reset-rec-amt").click(function () {
            $('#from_reciept_amount').val('');
            $('#to_reciept_amount').val('');
        })
        

        
        $('#reset-in-amt').click(function () {
            $("#invoice_amount_div .select2-selection__rendered").empty();
            $('#filter_invoice_amount option:selected').prop("selected", false)
        })
        $('#reset-rec-amt').click(function () {
            $("#received_amount_div .select2-selection__rendered").empty();
            $('#filter_received_amount option:selected').prop("selected", false)
        })

        $('#reset-country').click(function () {
            $("#billing_country_div .select2-selection__rendered").empty();
            $('#filter_billing_country option:selected').prop("selected", false)
        })

        $('#reset-currency').click(function () {
            $("#billing_Currency_div .select2-selection__rendered").empty();
            $('#filter_billing_currency option:selected').prop("selected", false)
        })


         $('#reset-ref-amt').click(function () {
            $("#received_amount_div .select2-selection__rendered").empty();
            $('#filter_received_amount option:selected').prop("selected", false)
        })

         

        $('#reset-mode').click(function () {
            $("#payment_mode_div .select2-selection__rendered").empty();
            $('#filter_payment_mode option:selected').prop("selected", false)
        })

       

        $('#reset-status').click(function () {
            $("#payment_status_div .select2-selection__rendered").empty();
            $('#filter_payment_status option:selected').prop("selected", false)
        })
        $('#reset-amt').click(function () {
            $("#pending_amount_div .select2-selection__rendered").empty();
            $('#filter_pending_amount option:selected').prop("selected", false)
        });
        $('#refresh_table').on('click', function(){
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable();
        });
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
                $("#refund_amount_div").hide();
                $("#payment_status_div").hide();
                $("#pending_amount_div").hide();
                $("#receivable_date_div").hide();
                $("#payment_mode_div").hide();
                $("#billing_country_div").hide();
                $("#place_of_supply_div").hide();                
                $("#billing_Currency_div").hide();
                $('#taxable_value_div').hide();
                $("#cgst_value_div").hide();
                $("#utgst_div").hide();
                $("#from_utgst_div").hide();
                $("#to_utgst_div").hide();
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

            if (type == "receipt_amount")
            {
                $("#received_amount_div").show();
                $("#reset-rec-amt").show();
            } else
            {
                $("#received_amount_div").hide();
                $("#reset-rec-amt").hide();
            }

            if (type == "refund_amount")
            {
                $("#refund_amount_div").show();
                $("#reset-ref-amt").show();
            } else
            {
                $("#refund_amount_div").hide();
                $("#reset-ref-amt").hide();
            }

            
            if (type == "payment_mode")
            {
                $("#payment_mode_div").show();
                $("#reset-mode").show();
            } else
            {
                $("#payment_mode_div").hide();
                $("#reset-mode").hide();
            }
            
            if (type == "billing_country")
            {
                $("#billing_country_div").show();
                $("#reset-country").show();
            } else
            {
                $("#billing_country_div").hide();
                $("#reset-country").hide();
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

            if (type == "billing_Currency")
            {
                $("#billing_Currency_div").show();
                $("#reset-currency").show();
            } else
            {
                 $("#billing_Currency_div").hide();
                $("#reset-currency").hide();
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

            

            if (type == "customer")
            {
                $("#reset-customer").show();
            } else
            {
                $("#reset-customer").hide();
            }

            

            if (type == "customer")
            {
                $("#reset-customer").show();
            } else
            {
                $("#reset-customer").hide();
            }
            if (type == "taxable_value")
            {
                $("#taxable_value_div").show();
                $('#reset-taxable').show();
            } else
            {
                $("#taxable_value_div").hide();
                $('#reset-taxable').hide();
            }

            
            if (type == "cgst"){
                $("#cgst_value_div").show();
                $('#reset-cgst').show();
            } else
            {
                $("#cgst_value_div").hide();
                $('#reset-cgst').hide();
            }
            if (type == "sgst")
            {
                $("#sgst_value_div").show();
                $('#reset-sgst').show();
            } else
            {
                $("#sgst_value_div").hide();
                $('#reset-sgst').hide();
            }
            if (type == "igst")
            {
                $("#igst_value_div").show();
                $('#reset-igst').show();
            } else
            {
                $("#igst_value_div").hide();
                $('#reset-igst').hide();
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
        $('#filter_billing_country option:selected').prop("selected", false)
        $('#filter_place_of_supply option:selected').prop("selected", false)
        $('#filter_billing_currency option:selected').prop("selected", false)
        $('#filter_utgst option:selected').prop("selected", false)
        $('.select2-selection__rendered').empty();
        $('#filter_payment_mode option:selected').prop("selected", false)
        $('#filter_from_date').val('');
        $('#filter_to_date').val('');
        $('#from_refund_amount').val('');
        $('#to_refund_amount').val('');
        $('#from_taxable_amount').val('');
        $('#to_taxable_amount').val('');
        $('#from_cgst_amount').val('');
        $('#to_cgst_amount').val('');
        $('#from_sgst_amount').val('');
        $('#to_sgst_amount').val('');
        $('#from_igst_amount').val('');
        $('#to_igst_amount').val('');
        $('#from_reciept_amount').val('');
        $('#to_reciept_amount').val('');
        $("#filter_from_utgst_amount").val('');
        $("#filter_to_utgst_amount").val('');
        $('.select2-selection__rendered').empty();
        appendFilter();
         $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable();

    }
        function generateOrderTable() {
            list_datatable = $('#list_datatable').DataTable({
                "processing": true,
                "serverSide": true,
                "scrollX":true,
                "iDisplayLength": 50,
                "lengthMenu": [ [10, 25, 50, 100], [10, 25, 50, 100] ],
                "ajax": {
                    "url": base_url + "report/refund_voucher_report",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        'filter_from_date': $('#filter_from_date').val(),
                        'filter_to_date': $('#filter_to_date').val(),                        
                        'filter_invoice_number': $('#filter_invoice_number').val(),
                        'filter_customer_name': $('#filter_customer_name').val(),
                        'filter_payment_status': $('#filter_payment_status').val(),
                        'filter_from_refund_amount': $('#from_refund_amount').val(),
                        'filter_to_refund_amount': $('#to_refund_amount').val(),
                        'filter_billing_country': $('#filter_billing_country').val(),
                        'filter_billing_currency': $('#filter_billing_currency').val(),
                        'filter_place_of_supply': $('#filter_place_of_supply').val(),
                        'filter_payment_mode': $('#filter_payment_mode').val(),
                        'filter_invoice_amount': $('#filter_invoice_amount').val(),
                        'filter_received_amount': $('#filter_received_amount').val(),
                        'filter_pending_amount': $('#filter_pending_amount').val(),
                        'filter_receivable_date': $('#filter_receivable_date').val(),
                        'filter_from_taxable_amount': $('#from_taxable_amount').val(),
                        'filter_to_taxable_amount': $('#to_taxable_amount').val(),
                        'filter_from_cgst_amount': $('#from_cgst_amount ').val(),                       
                        'filter_to_cgst_amount': $('#to_cgst_amount').val(),
                        'filter_from_sgst_amount': $('#from_sgst_amount').val(),
                        'filter_to_sgst_amount': $('#to_sgst_amount').val(),
                        'filter_from_igst_amount': $('#from_igst_amount').val(),
                        'filter_to_igst_amount': $('#to_igst_amount').val(),
                        'filter_utgst_from': $('#filter_from_utgst_amount').val(),
                        'filter_utgst_to': $('#filter_to_utgst_amount').val(),
                        'filter_from_reciept_amount': $('#from_reciept_amount').val(),
                        'filter_to_reciept_amount': $('#to_reciept_amount').val(),
                        'filter_due': $('#filter_due').val(),
                        'filter_tax': $('#filter_tax').val(),
                        'filter_sgst': $('#filter_sgst').val(),
                        'filter_igst': $('#filter_igst').val(),
                        'filter_utgst': $('#filter_utgst').val()
                    },
                    "dataSrc"
                        : function(result) {
                            var tfoot = parseFloat(result.total_list_record[0].tot_voucher_sub_total).toFixed(2);
                            $('#total_list_record1').html(tfoot);

                            var tfoot = parseFloat(result.total_list_record[0].tot_voucher_igst_amount).toFixed(2);
                            $('#total_list_record5').html(tfoot);

                            var tfoot = parseFloat(result.total_list_record[0].tot_voucher_cgst_amount).toFixed(2);
                            $('#total_list_record2').html(tfoot);

                            var tfoot = parseFloat(result.total_list_record[0].tot_voucher_sgst_amount).toFixed(2);
                            $('#total_list_record3').html(tfoot);

                            var tfoot = parseFloat(result.total_list_record[0].tot_voucher_utgst_amount).toFixed(2);
                            $('#total_list_record4').html(tfoot);

                            var tfoot = parseFloat(result.total_list_record[0].tot_receipt_amount).toFixed(2);
                            $('#total_list_record6').html(tfoot);
                            return result.data;
                        }
                        },
                "columns": [
                    {"data": "date"},
                    {"data": "invoice"},
                    {"data": "customer"},
                    {"data": "reference_number"},
                    {"data": "refund_amount"},
                    {"data": "billing_country"},
                    {"data": "place_of_supply"},
                    {"data": "billing_currency"},
                    {"data": "payment_mode"},
                    {"data": "taxable_value"},
                    {"data": "cgst_amount"},
                    {"data": "sgst_amount"},
                    /*{"data": "utgst"},*/
                    {"data": "igst_amount"},
                    {"data": "receipt_amount"}
                    
                    //{"data": "from_account"}
                    // { "data": "to_account" }
                    // { "data": "voucher_tax_amount" },
                    // { "data": "cgst_amount" },
                    // { "data": "sgst_amount" },
                    // { "data": "igst_amount" },
                    // { "data": "due" },

                    // { "data": "action" }
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
                        filename: 'Refund Voucher Report',
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
                                            text: 'Refund Voucher Report'
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
                            $('#refund_voucher_report_select_columns').modal('show');
                        }
                        
                    }]
            });
            $("#loader").hide();
            return true;
        }
        $('#filter_search').click(function () {
            appendFilter();
        });

        function appendFilter() { //button filter event click

            $("#loader").show();
            var filter_invoice_date = "";
            var filter_invoice_number = "";
            var filter_customer_name = "";
            var filter_payment_status = "";
            var filter_refund_amount = "";
            var filter_billing_country = "";
            var filter_place_of_supply = "";
            var filter_billing_currency = "";
            var filter_payment_mode = "";
            var filter_taxable_amount = "";
            var filter_cgst = "";
            var filter_sgst = "";
           /* var filter_utgst = "";*/
            var filter_igst = "";
            var filter_invoice_amount = "";
            var filter_utgst_data = "";

            var label = "";
            var filter_from_date = $('#filter_from_date').val();
            var filter_to_date = $('#filter_to_date').val();
            if (filter_from_date != "" || filter_to_date != "") {
                filter_invoice_date = "<label id='lbl_invoice_date' class='label-style'><b> Voucher Date : </b> FROM " + filter_from_date + " TO " + filter_to_date + '<i class="fa fa-times"></i></label>';
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
                label += "<label id='lbl_invoice_number' class='label-style'><b> Voucher Number : </b> " + filter_invoice_number.slice(0, -2) + '<i class="fa fa-times"></i></label>' + " ";
            }
            $("#filter_customer_name option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_customer_name += $this.text() + ", ";
                }
            });
            if (filter_customer_name != '') {
                label += "<label id='lbl_customer_name' class='label-style'><b> Customer Name : </b> " + filter_customer_name.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_payment_status option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_payment_status += $this.text() + ", ";
                }
            });
            if (filter_payment_status != '') {
                label += "<label id='lbl_reference_number' class='label-style'><b> Advance Reference Number : </b> " + filter_payment_status.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            var from_refund_amount = $('#from_refund_amount').val();
            var to_refund_amount = $('#to_refund_amount').val();
            if (from_refund_amount != "" || to_refund_amount != "") {
                filter_refund_amount = "<label id='lbl_refund_amount' class='label-style'><b> Refund Amount : </b> FROM " + from_refund_amount + " TO " + to_refund_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_refund_amount != '') {
                label += filter_refund_amount + " ";
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
            $("#filter_billing_currency option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_billing_currency += $this.text() + ", ";
                }
            }); 
            if(filter_billing_currency != '') {
                label += "<label id='lbl_billing_currency' class='label-style'><b> Billing Currency : </b> " + filter_billing_currency.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_payment_mode option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_payment_mode += $this.text() + ", ";
                }
            }); 
            if(filter_payment_mode != '') {
                label += "<label id='lbl_payment_mode' class='label-style'><b> Payment Mode : </b> " + filter_payment_mode.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            var from_taxable_amount = $('#from_taxable_amount').val();
            var to_taxable_amount = $('#to_taxable_amount').val();
            if (from_taxable_amount != "" || to_taxable_amount != "") {
                filter_taxable_amount = "<label id='lbl_taxable_amount' class='label-style'><b> Taxable Amount : </b> FROM " + from_taxable_amount + " TO " + to_taxable_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_taxable_amount != '') {
                label += filter_taxable_amount + " ";
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
            /*$("#filter_utgst option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_utgst += $this.text() + ", ";
                }
            });
            if(filter_utgst != '') {
                label += "<label id='lbl_utgst' class='label-style'><b> UTGST : </b> " + filter_utgst.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }*/
            var filter_utgst_from = $("#filter_from_utgst_amount ").val();
            var filter_utgst_to = $("#filter_to_utgst_amount").val();
            if (filter_utgst_from != "" || filter_utgst_to != "") {
                filter_utgst_data = "<label id='lbl_utgst_amount' class='label-style'><b> UTGST : </b> FROM " + filter_utgst_from + " TO " + filter_utgst_to + '<i class="fa fa-times"></i></label>';
            }
            if (filter_utgst_data != '') {
                label += filter_utgst_data + " ";
            }
            var from_igst_amount = $('#from_igst_amount').val();
            var to_igst_amount = $('#to_igst_amount').val();
            if (from_igst_amount != "" || to_igst_amount != "") {
                filter_igst = "<label id='lbl_igst' class='label-style'><b> IGST : </b> FROM " + from_igst_amount + " TO " + to_igst_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_igst != '') {
                label += filter_igst + " ";
            }
            var from_reciept_amount = $('#from_reciept_amount').val();
            var to_reciept_amount = $('#to_reciept_amount').val();
            if (from_reciept_amount != "" || to_reciept_amount != "") {
                filter_invoice_amount = "<label id='lbl_invoice_Amount' class='label-style'><b> Refund Voucher Amount : </b> FROM " + from_reciept_amount + " TO " + to_reciept_amount + '<i class="fa fa-times"></i></label>';
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
        };
        $(document).on('click',".fa-times",function(){
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
            if(label1 == 'lbl_customer_name'){
                $("#customer_div .select2-selection__rendered").empty();
                $('#filter_customer_name option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_reference_number'){
                $("#payment_status_div .select2-selection__rendered").empty();
                $('#filter_payment_status option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_refund_amount'){
                $('#from_refund_amount').val('');
                $('#to_refund_amount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_billing_country'){
                $("#billing_country_div .select2-selection__rendered").empty();
                $('#filter_billing_country option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_place_supply'){
                $("#place_of_supply_div .select2-selection__rendered").empty();
                $('#filter_place_of_supply option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_billing_currency'){
                $("#billing_currency_div .select2-selection__rendered").empty();
                $('#filter_billing_currency option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_payment_mode'){
                $("#payment_mode_div .select2-selection__rendered").empty();
                $('#filter_payment_mode option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_taxable_amount'){
                $('#from_taxable_amount').val('');
                $('#to_taxable_amount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_cgst'){
                $('#from_cgst_amount').val('');
                $('#to_cgst_amount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_sgst'){
                $('#from_sgst_amount').val('');
                $('#to_sgst_amount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_utgst_amount'){
                $("#filter_from_utgst_amount").val('');
                $('#filter_to_utgst_amount').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            /*if(label1 == 'lbl_utgst'){
                $("#utgst_div .select2-selection__rendered").empty();
                $('#filter_utgst option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }*/
            if(label1 == 'lbl_igst'){
                $('#from_igst_amount').val('');
                $('#to_igst_amount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_invoice_Amount'){
                $('#from_reciept_amount').val('');
                $('#to_reciept_amount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
        });
        // $('#btn-reset').click(function(){ //button reset event click
        //     $('#form-filter')[0].reset();
        //     table.ajax.reload();  //just reload table
        // });

    });
$(document).ready(function(){
    $('#refund_voucher_report_select_columns #select_column').click(function(){
        var checked_arr = [];
        var unchecked_arr = [];

        $.each($('#refund_voucher_report_select_columns input[type="checkbox"]:checked'),function(key,value){
            checked_arr.push(this.value);
        });

        $.each($('#refund_voucher_report_select_columns input[type="checkbox"]:not(:checked)'),function(key,value){
            unchecked_arr.push(this.value);
        });
        list_datatable.columns(checked_arr).visible(true);
        // console.log(list_datatable);

        list_datatable.columns(unchecked_arr).visible(false);
        // console.log(unchecked_arr);
        $('#refund_voucher_report_select_columns ').modal('hide');
    });
});

</script>

