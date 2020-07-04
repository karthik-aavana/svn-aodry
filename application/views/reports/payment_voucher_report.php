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
    .label-style{
        background: #ddd;
        color: #333;
        padding: 2px 10px;
        font-size: 11px;
        border-radius: 10px;
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
            <li class="active">Payment Voucher Report</li>
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
                        <h3 class="box-title">Payment Voucher Report</h3>
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

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Supplier" data-type="supplier">Supplier<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Voucher Type" data-type="voucher_type">Voucher Type<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <!-- <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Billing Currency" data-type="billing_currency">Billing Currency<span class="fa fa-toggle-down mr-20"></span></a></th> -->

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Reference Number" data-type="reference_number">Reference Number<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Paid Amount" data-type="paid_amount">Paid Amount<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Total Paid Amount" data-type="total_paid_amount">Total paid Amount<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Exchange Gain/Loss" data-type="gain_loss">Exchange Gain/Loss
                                    <span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Discount" data-type="discount">Discount<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Other Charges" data-type="other_charges">Other Charges<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Round Off" data-type="round_off">Round Off<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Pending Amount" data-type="pending_amount">Pending Amount<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Received Amount" data-type="total_received_amount">Total Received Amount<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Invoice Amount" data-type="invoice_amount">Invoice Amount<span class="fa fa-toggle-down mr-20"></span></a></th>

                                    <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select Payment Mode" data-type="payment_status">Payment Mode<span class="fa fa-toggle-down mr-20"></span></a></th>
                                </tr>
                            </thead>
                            <tbody></tbody>                            
                        </table>
                    </div>
                        <table id="addition_table" class="mt-15 mb-25">
                            <thead>
                                <tr>
                                    <th>Total Paid Amount</th>
                                    <th>Total of Total Paid Amount</th>
                                    <th>Total Gain/Loss Amount</th>
                                    <th>Total Discount Amount</th>
                                    <th>Total Other Charges Amount</th>
                                    <th>Total Round-Off Amount</th>
                                    <th>Total Pending Amount</th>
                                    <th>Total Received Amount</th>   
                                    <th>Total Invoice Amount</th>   
                                </tr>
                            </thead>    
                        <tbody>
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
                                    <option value="supplier">supplier</option>
                                    <option value="invoice">Invoice</option>
                                    <option value="invoice_date">Invoice Date</option>
                                    <option value="invoice_amount">Invoice Amount</option>
                                    <option value="reference_number">Reference Number</option>
                                    <option value="payment_status">Payment Status</option>
                                    <option value="pending_amount">Pending Amount</option>
                                    <option value="receivable_date">Receivable Date</option>
                                    <option value="billing_currency">Billing Currency</option>
                                    <option value="due_day">Due Day</option>
                                    <option value="voucher_type">voucher type</option>
                                    <option value="paid_amount">Paid Amount</option>
                                    <option value="total_paid_amount">Total Paid Amount</option>
                                    <option value="gain_loss">Exchange Gain/Loss</option>
                                    <option value="discount">Discount</option>
                                    <option value="other_charges">Other Charges</option>
                                    <option value="round_off">Round Off</option>
                                    <option value="total_received_amount">Round Off</option>
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
                        <div class="col-md-12" id="supplier_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_supplier_name"  name="filter_supplier_name">
                                    <option value="" >Select Supplier</option>
                                    <option value="0">Others</option>
                                    <?php
                                    foreach ($supplier as $key => $value)
                                    {
                                        ?>
                                        <option value="<?= $value->supplier_id ?>"><?= $value->supplier_name ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                 <!--  <input type="text" name="filter_customer_name" value="" class="form-control" id="filter_customer_name" placeholder="supplier Name"> -->
                            </div>
                        </div>
                        <div class="col-md-12" id="invoice_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_invoice_number"  name="filter_invoice_number">
                                    <option value="" >Select Voucher Number</option>
                                    <?php
                                    foreach ($invoices as $key => $value)
                                    {
                                        ?>
                                        <option value="<?= $value->payment_id ?>"><?= $value->voucher_number ?></option>
                                        <?php
                                    }
                                    ?>

                                </select>

                            </div>
                        </div>
                        <div class="col-md-12" id="reference_number_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple" id="filter_reference_number"  name="filter_reference_number">
                                    <option value="" >Select Reference Number</option>
                                    <?php
                                    foreach ($invoices_ref as $key => $value){
                                    ?>
                                        <option value="<?= $value->purchase_invoice_number ?>"><?= $value->purchase_invoice_number ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="billing_currency_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_billing_currency"  name="filter_billing_currency">
                                    <option value="" >Select Billing Currency</option>
                                    <?php
                                    foreach ($billing_currency as $key => $value){
                                    ?>
                                        <option value="<?= $value->currency_id ?>"><?= $value->currency_name ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12" id="received_amount_div">
                             <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="from_received_amount" class="form-control" id="from_received_amount" placeholder="From Amount" autocomplete="off" min="1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="to_received_amount" class="form-control" id="to_received_amount" placeholder="To Amount" autocomplete="off" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" id="invoice_amount_div">
                             <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="from_invoice_amount" class="form-control" id="from_invoice_amount" placeholder="From Invoice Amount" autocomplete="off" min="1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="to_invoice_amount" class="form-control" id="to_invoice_amount" placeholder="To Invoice Amount" autocomplete="off" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="payment_status_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_payment_status"  name="filter_payment_status">
                                    <option value="" >Select Payment Mode</option>
                                    <?php
                                    foreach ($from_account as $key => $value){
                                        if($value->from_account != ''){
                                        ?>
                                        <option value="<?= $value->from_account ?>"><?= $value->from_account ?></option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="paid_amount_div">
                             <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="from_paid_amount" class="form-control" id="from_paid_amount" placeholder="From Paid Amount" autocomplete="off" min="1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="to_paid_amount" class="form-control" id="to_paid_amount" placeholder="To Paid Amount" autocomplete="off" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" id="total_paid_amount_div">
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="from_total_paid_amount" class="form-control" id="from_total_paid_amount" placeholder="From Total Paid Amount" autocomplete="off" min="1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="to_total_paid_amount" class="form-control" id="to_total_paid_amount" placeholder="To Total Paid Amount" autocomplete="off" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" id="gain_loss_div">
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="from_gain_loss" class="form-control" id="from_gain_loss" placeholder="From Gain Loss" autocomplete="off" min="1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="to_gain_loss" class="form-control" id="to_gain_loss" placeholder="To Gain Loss" autocomplete="off" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="discount_div">
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="from_discount" class="form-control" id="from_discount" placeholder="From Discount" autocomplete="off" min="1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="to_discount" class="form-control" id="to_discount" placeholder="To Discount" autocomplete="off" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" id="other_charges_div">
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="from_other_charges" class="form-control" id="from_other_charges" placeholder="From Other Charges" autocomplete="off" min="1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="to_other_charges" class="form-control" id="to_other_charges" placeholder="To Other Charges" autocomplete="off" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" id="round_off_div">
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="from_round_off" class="form-control" id="from_round_off" placeholder="From Round Off" autocomplete="off" min="1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="to_round_off" class="form-control" id="to_round_off" placeholder="To Round Off" autocomplete="off" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="pending_amount_div">
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="from_pending_amount" class="form-control" id="from_pending_amount" placeholder="From Pending Amount" autocomplete="off" min="1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                   <input type="number" name="to_pending_amount" class="form-control" id="to_pending_amount" placeholder="To Pending Amount" autocomplete="off" min="1">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12" id="receivable_date_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_total_paid_amount"  name="filter_total_paid_amount">
                                    <option value="" >Select Total Paid Amount</option>
                                    <?php
                                    foreach ($total_paid_amount as $key => $value)
                                    {
                                        ?>
                                        <option value="<?= $value->invoice_paid_amount ?>"><?= $value->invoice_paid_amount ?></option>
                                        <?php
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="voucher_type_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_voucher_type"  name="filter_voucher_type">
                                    <option value="" >Select Voucher Type</option>
                                    <?php
                                    $type = '';
                                    foreach ($voucher_type as $key => $value)
                                    {
                                        if($value->reference_type == "purchase"){
                                            $type = 'Purchase Bill';
                                        }elseif($value->reference_type == "expense"){
                                            $type = 'Expense Bill';
                                        } else {
                                            $type = 'Excess';
                                        }
                                        ?>
                                        <option value="<?= $value->reference_type ?>"><?= $type ?></option>
                                        <?php
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="due_day_div">
                            <div class="form-group">
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
                 <button type="button" class="btn btn-warning" id="reset-voucher_type">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-ref-number">Reset</button>
                     <button type="button" class="btn btn-warning" id="reset-ref-billing">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-rec-amt">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-status">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-ref-paid_amount">Reset </button>
                    <button type="button" class="btn btn-warning" id="reset-ref-total_paid_amount">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-ref-gain_loss">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-ref-discount">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-ref-other_charges">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-ref-round_off">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-received-amount">Reset</button>
                      <button type="button" class="btn btn-warning" id="reset-amt">Reset</button>
                </div>
            </form>
        </div>

    </div>
</div>

</div>


<!-- modals ends -->
<div class="modal fade" id="payment_voucher_report_select_columns" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true" data-backdrop="static">
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

                                <input type="checkbox" name="supplier" id="supplier" value="2" checked>
                                <label for="supplier">Suppiler</label><br>

                                <input type="checkbox" name="voucher_type" id="voucher_type" value="3" checked>
                                <label for="voucher_type">Voucher Type</label><br>

                               <input type="checkbox" name="reference_number" id="reference" value="4" checked>
                                <label for="reference">Refrence number</label><br>

                                <input type="checkbox" name="paid_amount" id="paid_amount" value="5" checked>
                                <label for="paid_amount">Paid Amount </label><br>

                                <input type="checkbox" name="total_paid_amount" id="total_paid_amount" value="6" checked>
                                <label for="total_paid_amount">Total paid Amount</label><br>

                                <input type="checkbox" name="exchange_gain_loss" id="exchange_gain_loss" value="7" checked>
                                <label for="exchange_gain_loss">Exchange Gain/Loss</label><br>

                            </div>
                            <div class="col-md-6">
                                <input type="checkbox" name="discount" id="discount" value="8" checked>
                                <label for="discount">Discount</label><br>

                                <input type="checkbox" name="other_charges" id="other_charges" value="9" checked>
                                <label for="other_charges">Other Charges</label><br>

                                <input type="checkbox" name="round_off" id="round_off" value="10" checked>
                                <label for="round_off">Round Off</label><br>

                                <input type="checkbox" name="pending_amount" id="pending_amount" value="11"  checked>
                                <label for="pending_amount">Pending Amount</label><br>

                                <input type="checkbox" name="total_received_amount" id="total_received_amount" value="12" checked>
                                <label for="total_received_amount">Total Received Amount</label><br>

                                <input type="checkbox" name="invoice_amount" id="invoice_amount" value="13" checked>
                                <label for="invoice_amount">Invoice Amount</label><br>

                                <input type="checkbox" name="payment_mode" id="payment_mode" value="14" checked>
                                <label for="payment_mode">Payment Mode</label><br>
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

        $("#reset-supplier").click(function () {
            $("#supplier_div .select2-selection__rendered").empty();
            $('#filter_supplier_name option:selected').prop("selected", false)
        })
        $("#reset-invoice").click(function () {
            $("#invoice_div .select2-selection__rendered").empty();
            $('#filter_invoice_number option:selected').prop("selected", false)
        })
        $("#reset-in-date").click(function () {
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
        })
        $("#reset-received-amount").click(function () {
            $('#from_received_amount').val('');
            $('#to_received_amount').val('');
        })
        // $('#reset-in-amt').click(function(){
        //   $("#reference_number .select2-selection__rendered").empty();
        // $('#filter_reference_number option:selected').prop("selected", false)
        // })
        $('#reset-ref-number').click(function () {
            $("#reference_number_div .select2-selection__rendered").empty();
            $('#filter_reference_number option:selected').prop("selected", false)
        })
        $('#reset-rec-amt').click(function () {
            $('#from_invoice_amount').val('');
            $('#to_invoice_amount').val('');
        })
        $('#reset-status').click(function () {
            $("#payment_status_div .select2-selection__rendered").empty();
            $('#filter_payment_status option:selected').prop("selected", false)
        })

        $('#reset-voucher_type').click(function () {
            $("#voucher_type_div .select2-selection__rendered").empty();
            $('#filter_voucher_type option:selected').prop("selected", false)
        })

        
        $('#reset-amt').click(function () {
            $('#from_pending_amount').val('');
            $('#to_pending_amount').val('');
        })

        $('#reset-ref-paid_amount').click(function () {
            $('#from_paid_amount').val('');
            $('#to_paid_amount').val('');

        })

        $('#reset-ref-total_paid_amount').click(function () {
            $('#from_total_paid_amount').val('');
            $('#to_total_paid_amount').val('');
        })

        $('#reset-ref-gain_loss').click(function () {
            $('#from_gain_loss').val('');
            $('#to_gain_loss').val('');
        })

        $('#reset-ref-discount').click(function () {
            $('#from_discount').val('');
            $('#to_discount').val('');
        })

        $('#reset-ref-other_charges').click(function () {
            $('#from_other_charges').val('');
            $('#to_other_charges').val('');
        })

        $('#reset-ref-round_off').click(function () {
            $('#from_round_off').val('');
            $('#to_round_off').val('');
        })
        $('#refresh_table').on('click', function(){
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable();
        });

        $("#modal-select_type").on("change", function (event){
            var type = $('#modal-select_type').val();
            console.log(type);
            if (type == ""){

                $("#supplier_div").hide();
                $("#invoice_div").hide();
                $("#from_date_div").hide();
                $("#to_date_div").hide();
                $("#reference_number").hide();
                $("#invoice_amount_div").hide();
                $("#payment_status_div").hide();
                $("#pending_amount_div").hide();
                $("#receivable_date_div").hide();
                $("#due_day_div").hide();
                $("#voucher_type_div").hide();
                $("#reference_number_div").hide();
                $("#billing_currency_div").hide();
                $("#paid_amount_div").hide();
                $("#total_paid_amount_div").hide();
                $("#gain_loss_div").hide();
                $("#discount_div").hide();
                $("#other_charges_div").hide();
                $("#round_off_div,#received_amount_div").hide();
            }

                if (type == "supplier"){
                    $("#supplier_div").show();
                }else{
                    $("#supplier_div").hide();
                }

                if (type == "invoice"){
                    $("#invoice_div").show();
                    $('#reset-invoice').show();
                }else{
                    $("#invoice_div").hide();
                    $('#reset-invoice').hide();
                }

                if (type == "reference_number"){
                    $("#reference_number_div").show();
                    $("#reset-ref-number").show();
                }else{
                    $("#reference_number_div").hide();
                    $("#reset-ref-number").hide();
                }

                if (type == "billing_currency"){
                    $("#billing_currency_div").show();
                    $("#reset-ref-billing").show();
                }else{
                    $("#billing_currency_div").hide();
                    $("#reset-ref-billing").hide();
                }


                if (type == "paid_amount"){
                    $("#paid_amount_div").show();
                    $("#reset-ref-paid_amount").show();
                }else{
                    $("#paid_amount_div").hide();               
                    $("#reset-ref-paid_amount").hide();
                }
            
                if (type == "total_paid_amount"){
                    $("#total_paid_amount_div").show();
                    $("#reset-ref-total_paid_amount").show();
                }else{
                    $("#total_paid_amount_div").hide();
                    $("#reset-ref-total_paid_amount").hide();
                }
                if (type == "gain_loss"){
                    $("#gain_loss_div").show();
                    $("#reset-ref-gain_loss").show();
                }else{
                    $("#gain_loss_div").hide();
                    $("#reset-ref-gain_loss").hide();
                }
                if (type == "discount"){
                    $("#discount_div").show();
                    $("#reset-ref-discount").show();
                }else{
                    $("#discount_div").hide();
                    $("#reset-ref-discount").hide();
                }
                if (type == "other_charges"){
                    $("#other_charges_div").show();
                    $("#reset-ref-other_charges").show();
                }else{
                    $("#other_charges_div").hide();
                    $("#reset-ref-other_charges").hide();
                }
                if (type == "round_off"){
                    $("#round_off_div").show();
                    $("#reset-ref-round_off").show();
                }else{
                    $("#round_off_div").hide();
                    $("#reset-ref-round_off").hide();
                }
                if (type == "invoice_date"){
                    $("#from_date_div").show();
                    $("#to_date_div").show();
                    $("#reset-in-date").show();
                }else{
                    $("#from_date_div").hide();
                    $("#to_date_div").hide();
                    $("#reset-in-date").hide();
                }
                if (type == "invoice_amount"){
                    $("#invoice_amount_div").show();
                    $("#reset-rec-amt").show();
                } else{
                    $("#invoice_amount_div").hide();
                    $("#reset-rec-amt").hide();
                }
                if (type == "payment_status"){
                    $("#payment_status_div").show();
                    $("#reset-status").show();
                }else{
                    $("#payment_status_div").hide();
                    $("#reset-status").hide();
                }
                if (type == "pending_amount"){
                    $("#pending_amount_div").show();
                    $("#reset-amt").show();

                } else{
                    $("#pending_amount_div").hide();
                    $("#reset-amt").hide();
                }
                if (type == "receivable_date"){
                    $("#receivable_date_div").show();
                } else{
                    $("#receivable_date_div").hide();
                }
                if (type == "due_day"){
                    $("#due_day_div").show();
                } else{
                    $("#due_day_div").hide();
                }
                if (type == "supplier"){
                    $("#reset-supplier").show();
                } else{
                    $("#reset-supplier").hide();
                }

                if (type == "voucher_type"){
                    $("#voucher_type_div").show();
                    $("#reset-voucher_type").show();
                } else{
                    $("#voucher_type_div").hide();
                    $("#reset-voucher_type").hide();
                }
                if (type == "total_received_amount"){
                    $("#received_amount_div").show();
                    $('#reset-received-amount').show();
                }else{
                    $("#received_amount_div").hide();
                    $('#reset-received-amount').hide();
                }

        }).change();

        generateOrderTable();
        function resetall() {
        $('#filter_supplier_name option:selected').prop("selected", false)
        $('#filter_invoice_number option:selected').prop("selected", false)
        $('#filter_from_date option:selected').prop("selected", false)
        $('#filter_to_date option:selected').prop("selected", false)
        $('#filter_payment_status option:selected').prop("selected", false)
        $('#filter_invoice_amount option:selected').prop("selected", false)
        $('#filter_reference_number option:selected').prop("selected", false)
        $('#filter_voucher_type option:selected').prop("selected", false)
        $('.select2-selection__rendered').empty();
        $('#filter_from_date').val('');
        $('#filter_to_date').val('');
        $('#from_round_off').val('');
        $('#to_round_off').val('');
        $('#from_paid_amount').val('');
        $('#to_paid_amount').val('');
        $('#from_total_paid_amount').val('');
        $('#to_total_paid_amount').val('');
        $('#from_gain_loss').val('');
        $('#to_gain_loss').val('');
        $('#from_discount').val('');
        $('#to_discount').val('');
        $('#from_other_charges').val('');
        $('#to_other_charges').val('');
        $('#from_pending_amount').val('');
        $('#to_pending_amount').val('');
        $('#from_invoice_amount').val('');
        $('#to_invoice_amount').val('');
        $('#from_received_amount').val('');
        $('#to_received_amount').val('');
        $('.select2-selection__rendered').empty();
        appendFilter();
        $("#list_datatable").dataTable().fnDestroy()
        generateOrderTable();
    }
        function generateOrderTable() {
            list_datatable = $('#list_datatable').DataTable({
                "processing": true,
                "serverSide": true, 
                "scrollX": true,
                "iDisplayLength": 50,
                 "lengthMenu": [ [15, 25, 50, 100, -1], [15, 25, 50, 100, "All"] ],
                "ajax": {
                    "url": base_url + "report/payment_voucher_report",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        'filter_supplier_name': $('#filter_supplier_name').val(),
                        'filter_invoice_number': $('#filter_invoice_number').val(),
                        'filter_from_date': $('#filter_from_date').val(),
                        'filter_to_date': $('#filter_to_date').val(),
                        'filter_reference_number': $('#filter_reference_number').val(),
                        'filter_invoice_amount': $('#filter_invoice_amount').val(),
                        'filter_payment_status': $('#filter_payment_status').val(),
                        'filter_paid_amount': $('#filter_paid_amount').val(),
                        'filter_payment_status': $('#filter_payment_status').val(),
                        'filter_receivable_date': $('#filter_receivable_date').val(),
                        'filter_total_paid_amount': $('#filter_total_paid_amount').val(),
                        'filter_voucher_type': $('#filter_voucher_type').val(),
                        'filter_billing_currency': $('#filter_billing_currency').val(),
                        'from_paid_amount': $('#from_paid_amount').val(),
                        'to_paid_amount': $('#to_paid_amount').val(),
                        'from_total_paid_amount': $('#from_total_paid_amount').val(),
                        'to_total_paid_amount': $('#to_total_paid_amount').val(),
                        'from_gain_loss': $('#from_gain_loss').val(),
                        'to_gain_loss': $('#to_gain_loss').val(),
                        'from_discount': $('#from_discount').val(),
                        'to_discount': $('#to_discount').val(),
                        'from_other_charges': $('#from_other_charges').val(),
                        'to_other_charges': $('#to_other_charges').val(),
                        'from_round_off': $('#from_round_off').val(),
                        'to_round_off': $('#to_round_off').val(),
                        'from_pending_amount': $('#from_pending_amount').val(),
                        'to_pending_amount': $('#to_pending_amount').val(),
                        'from_invoice_amount': $('#from_invoice_amount').val(),
                        'to_invoice_amount': $('#to_invoice_amount').val(),
                        'from_received_amount': $('#from_received_amount').val(),
                        'to_received_amount': $('#to_received_amount').val()
                    },
                    "dataSrc": function (result) {
                        var tfoot = parseFloat(result.total_list_record[0].tot_paid_amount).toFixed(2) ;
                        $('#total_list_record1').html(tfoot);
                        var tfoot = parseFloat(result.total_list_record[0].tot_of_total_paid_amount).toFixed(2) ;
                        $('#total_list_record2').html(tfoot);
                        var tfoot = result.total_list_record[0].tot_gain_loss_amount;
                        var tfoot1 = result.total_list_record[0].tot_minus_gain_loss_amount;
                        if(tfoot == null){
                            tfoot=0;
                        }
                        if(tfoot1 == null){
                            tfoot1=0;
                        }
                        var l = parseFloat(tfoot)+parseFloat(tfoot1);
                        $('#total_list_record3').html(l.toFixed(2));
                        var tfoot = parseFloat(result.total_list_record[0].tot_discount_amount).toFixed(2) ;
                        $('#total_list_record4').html(tfoot);
                        var tfoot = parseFloat(result.total_list_record[0].tot_other_charger_amount).toFixed(2) ;
                        $('#total_list_record5').html(tfoot);
                        var tfoot = result.total_list_record[0].tot_round_off_amount;
                        var tfoot1 = result.total_list_record[0].tot_round_off_minus_amount;
                        if(tfoot == null){
                            tfoot=0;
                        }
                        if(tfoot1 == null){
                            tfoot1=0;
                        }
                        var l = parseFloat(tfoot)+parseFloat(tfoot1);
                        $('#total_list_record6').html(l.toFixed(2));
                        var tfoot = parseFloat(result.total_list_record[0].tot_pending_amount).toFixed(2) ;
                        $('#total_list_record7').html(tfoot);
                        var tfoot = parseFloat(result.total_list_record[0].tot_received_amount).toFixed(2) ;
                        $('#total_list_record8').html(tfoot);
                        var tfoot = parseFloat(result.total_list_record[0].tot_invoice_amount).toFixed(2) ;
                        $('#total_list_record9').html(tfoot);
                        return result.data;
                    }
                },
                "columns": [
                    {"data": "date"},
                    {"data": "invoice"},
                    {"data": "supplier"},
                    {"data": "voucher_type"},
                    //{"data": "currency_name"},
                    {"data": "reference_number"},
                    {"data": "paid_amount"},
                    {"data": "total_paid_amount"},
                    {"data": "exchange_gain_loss"},
                    {"data": "discount"},
                    {"data": "other_charges"},
                    {"data": "round_off"},
                    {"data": "Invoice_pending"},
                    {"data": "Invoice_total_paid"},
                    {"data": "invoice_total"},
                    {"data": "payment_mode"}
                            // { "data": "paid_amount" },
                            // { "data": "reference_number" },
                            // { "data": "total_paid_amount" },
                            //  { "data": "voucher_type" },

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
                        filename: 'Payment Voucher Report',
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
                                            text: 'Payment Voucher Report'
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
                           /* doc.watermark = {text: 'Aavana Corporate Solutions PVT LTD', color: '#999999', opacity: 0.2};*/
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
                            $('#payment_voucher_report_select_columns').modal('show');
                        }
                        
                    }
                    ],
                'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },

            });
            anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
            // setTimeout(function(){ $("#loader").hide(); }, 4000);
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
            var filter_supplier_name = "";
            var filter_voucher_type = "";
            var filter_reference_number = "";
            var filter_paid_amount = "";
            var filter_total_paid_amount = "";
            var filter_gain_loss = "";
            var filter_discount = "";
            var filter_other_charges = "";
            var filter_round_off = "";
            var filter_pending_amount = "";
            var filter_received_amount = "";
            var filter_invoice_received_amount = "";
            var filter_payment_status = "";

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
            $("#filter_supplier_name option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_supplier_name += $this.text() + ", ";
                }
            });
            if (filter_supplier_name != '') {
                label += "<label id='lbl_supplier_name' class='label-style'><b> Supplier Name : </b> " + filter_supplier_name.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            /*$("#filter_invoice_amount option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_invoice_amount += $this.text() + ", ";
                }
            });
            if (filter_invoice_amount != '') {
                label += "<label id='lbl_reference_number' class='label-style'><b>Reference Number : </b> " + filter_invoice_amount + '<i class="fa fa-times"></i></label>'+" ";
            }*/
            $("#filter_voucher_type option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_voucher_type += $this.text() + ", ";
                }
            });
            if (filter_voucher_type != '') {
                label += "<label id='lbl_voucher_type' class='label-style'><b>Voucher Type : </b> " + filter_voucher_type.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_reference_number option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_reference_number += $this.text() + ", ";
                }
            });
            if (filter_reference_number != '') {
                label += "<label id='lbl_reference_number' class='label-style'><b>Reference Number : </b> " + filter_reference_number.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            var from_paid_amount = $('#from_paid_amount').val();
            var to_paid_amount = $('#to_paid_amount').val();
            if (from_paid_amount != "" || to_paid_amount != "") {
                filter_paid_amount = "<label id='lbl_paid_amount' class='label-style'><b> Paid Amount : </b> FROM " + from_paid_amount + " TO " + to_paid_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_paid_amount != '') {
                label += filter_paid_amount + " ";
            }
            var from_total_paid_amount = $('#from_total_paid_amount').val();
            var to_total_paid_amount = $('#to_total_paid_amount').val();
            if (from_total_paid_amount != "" || to_total_paid_amount != "") {
                filter_total_paid_amount = "<label id='lbl_total_paid_amount' class='label-style'><b> Total Paid Amount : </b> FROM " + from_total_paid_amount + " TO " + to_total_paid_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_total_paid_amount != '') {
                label += filter_total_paid_amount + " ";
            }
            var from_gain_loss = $('#from_gain_loss').val();
            var to_gain_loss = $('#to_gain_loss').val();
            if (from_gain_loss != "" || to_gain_loss != "") {
                filter_gain_loss = "<label id='lbl_gain_loss' class='label-style'><b> Exchange Gain/Loss : </b> FROM " + from_gain_loss + " TO " + to_gain_loss + '<i class="fa fa-times"></i></label>';
            }
            if (filter_gain_loss != '') {
                label += filter_gain_loss + " ";
            }
            var from_discount = $('#from_discount').val();
            var to_discount = $('#to_discount').val();
            if (from_discount != "" || to_discount != "") {
                filter_discount = "<label id='lbl_discount' class='label-style'><b> Discount : </b> FROM " + from_discount + " TO " + to_discount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_discount != '') {
                label += filter_discount + " ";
            }
            var from_other_charges = $('#from_other_charges').val();
            var to_other_charges = $('#to_other_charges').val();
            if (from_other_charges != "" || to_other_charges != "") {
                filter_other_charges = "<label id='lbl_other_charges' class='label-style'><b> Other Charges : </b> FROM " + from_other_charges + " TO " + to_other_charges + '<i class="fa fa-times"></i></label>';
            }
            if (filter_other_charges != '') {
                label += filter_other_charges + " ";
            }
            var from_round_off = $('#from_round_off').val();
            var to_round_off = $('#to_round_off').val();
            if (from_round_off != "" || to_round_off != "") {
                filter_round_off = "<label id='lbl_round_off' class='label-style'><b> Round Off : </b> FROM " + from_round_off + " TO " + to_round_off + '<i class="fa fa-times"></i></label>';
            }
            if (filter_round_off != '') {
                label += filter_round_off + " ";
            }
            var from_pending_amount = $('#from_pending_amount').val();
            var to_pending_amount = $('#to_pending_amount').val();
            if (from_pending_amount != "" || to_pending_amount != "") {
                filter_pending_amount = "<label id='lbl_pending_amount' class='label-style'><b> Pending Amount : </b> FROM " + from_pending_amount + " TO " + to_pending_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_pending_amount != '') {
                label += filter_pending_amount + " ";
            }
            var from_received_amount = $('#from_received_amount').val();
            var to_received_amount = $('#to_received_amount').val();
            if (from_received_amount != "" || to_received_amount != "") {
                filter_received_amount = "<label id='lbl_received_amount' class='label-style'><b> Total Received Amount : </b> FROM " + from_received_amount + " TO " + to_received_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_received_amount != '') {
                label += filter_received_amount + " ";
            }
            var from_invoice_amount = $('#from_invoice_amount').val();
            var to_invoice_amount = $('#to_invoice_amount').val();
            if (from_invoice_amount != "" || to_invoice_amount != "") {
                filter_invoice_received_amount = "<label id='lbl_invoice_received_amount' class='label-style'><b> Invoice Amount : </b> FROM " + from_invoice_amount + " TO " + to_invoice_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_invoice_received_amount != '') {
                label += filter_invoice_received_amount + " ";
            }
            $("#filter_payment_status option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_payment_status += $this.text() + ", ";
                }
            });
            if(filter_payment_status != '') {
                label += "<label id='lbl_payment_status' class='label-style'><b> Payment Mode : </b> " + filter_payment_status.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }

            
            if (label != "") {
                $('#filters_applied').html(label);
            } else {
                $('#filters_applied').html('<label></label>');
            }
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable();
        }
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
            if(label1 == 'lbl_supplier_name'){
                $("#supplier_div .select2-selection__rendered").empty();
                $('#filter_supplier_name option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_voucher_type'){
                $("#voucher_type_div .select2-selection__rendered").empty();
                $('#filter_voucher_type option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_reference_number'){
                $("#reference_number_div .select2-selection__rendered").empty();
                $('#filter_reference_number option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_paid_amount'){
                $('#from_paid_amount').val('');
                $('#to_paid_amount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_total_paid_amount'){
                $('#from_total_paid_amount').val('');
                $('#to_total_paid_amount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_gain_loss'){
                $('#from_gain_loss').val('');
                $('#to_gain_loss').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_discount'){
                $('#from_discount').val('');
                $('#to_discount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_other_charges'){
                $('#from_other_charges').val('');
                $('#to_other_charges').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_round_off'){
                $('#from_round_off').val('');
                $('#to_round_off').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_pending_amount'){
                $('#from_pending_amount').val('');
                $('#to_pending_amount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_received_amount'){
                $('#from_received_amount').val('');
                $('#to_received_amount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_invoice_received_amount'){
                $('#from_invoice_amount').val('');
                $('#to_invoice_amount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_payment_status'){
                $("#payment_status_div .select2-selection__rendered").empty();
                $('#filter_payment_status option:selected').prop("selected", false);
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
    $('#payment_voucher_report_select_columns #select_column').click(function(){
        var checked_arr = [];
        var unchecked_arr = [];

        $.each($('#payment_voucher_report_select_columns input[type="checkbox"]:checked'),function(key,value){
            checked_arr.push(this.value);
        });

        $.each($('#payment_voucher_report_select_columns input[type="checkbox"]:not(:checked)'),function(key,value){
            unchecked_arr.push(this.value);
        });
        list_datatable.columns(checked_arr).visible(true);
        // console.log(checked_arr);

        list_datatable.columns(unchecked_arr).visible(false);
        // console.log(unchecked_arr);
        $('#payment_voucher_report_select_columns').modal('hide');
    });
});
</script>

