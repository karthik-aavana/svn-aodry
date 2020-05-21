<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<style type="text/css">    
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
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>DashBoard</a></li>
                <li><a href="<?php echo base_url('report/all_reports'); ?>">Report</a></li> <li class="active">TDS Report</li>
            </ol>
        </h5>
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

                        <button class="close" data-dismiss="alert" type="button">×</button>

                        <?php echo $message; ?>

                        <div class="alerts-con"></div>

                    </div>

                </div>

                <?php
            }
            ?>

            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">TDS Receivable  Reports (Expense)</h3>
                        <div class="pull-right double-btn">
                            <button id="refresh_table" class="btn btn-sm btn-info">Refresh</button>
                        </div>    
                    </div>

                    <div class="box-body">
                        <div class="box-header" id="filters_applied">
                        </div>
                        <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th>
                                        <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#date_coll_modal">Date of Collection<span class="fa fa-toggle-down"></span></a>
                                    </th>
                                    <th>
                                        <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#deductee_name_modal">Deductee Name<span class="fa fa-toggle-down"></span></a>
                                    </th>
                                    <th>
                                        <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#tan_modal">TAN<span class="fa fa-toggle-down"></span></a>
                                    </th>
                                    <th>
                                        <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#tcs_receivable_modal">TDS receivable u/s<span class="fa fa-toggle-down"></span></a>
                                    </th>
                                    <th>
                                        <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#rate_tax_modal">Rate of Tax<span class="fa fa-toggle-down"></span></a>
                                    </th>
                                    <th>
                                        <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#taxable_value_modal">Taxable Value<span class="fa fa-toggle-down"></span></a>
                                    </th>
                                    <th>
                                        <a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#tcs_amt_modal">TDS Amount<span class="fa fa-toggle-down"></span></a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                        <table id="addition_table" class="mt-15 mb-25">                         
                            <thead>
                                <tr>
                                    <th>Sum Of Taxable Amount</th>
                                    <th>Sum Of TDS Amount</th>
                                </tr>
                            </thead>                            
                            <tbody>
                                <tr>
                                    <td id="total_list_record1"></td>
                                    <td id="total_list_record2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </section>

</div>
<div class="modal fade" id="date_coll_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Date of Collection</h4>
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
                <button type="button" class="btn btn-warning" id="reset-date">Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deductee_name_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select Deductee Name</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_customer"  name="filter_customer">
                                <option value="">Select Deductee Name</option>
                                <?php
                                foreach ($supplier as $key => $value) {
                                    ?>
                                    <option value="<?= $value->supplier_id ?>"><?= $value->supplier_name ?></option>
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
<div class="modal fade" id="tan_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select TAN</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_tan"  name="filter_tan">
                                <option value="">Select TAN</option>
                                <?php
                                foreach ($supplier as $key => $value) {
                                    if ($value->supplier_tan_number != '') {
                                        ?>
                                        <option value="<?= $value->supplier_tan_number ?>"><?= $value->supplier_tan_number ?></option>
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
                <button type="button" class="btn btn-warning" id="reset-tan" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>
<div class="modal fade" id="tcs_receivable_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select TDS Receivable u/s</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_tcs_section"  name="filter_tcs_section">
                                <option value="">Please Select TDS Receivable u/s</option>
                                <?php
                                foreach ($tds_section as $key => $value) {
                                    if ($value->section_name != '') {
                                        $section = 'TDS receivable u/s' . $value->section_name;
                                        ?>
                                        <option value="<?= $value->section_name ?>"><?= $section ?></option>
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
                <button type="button" class="btn btn-warning" id="reset-section" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>
<div class="modal fade" id="rate_tax_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Select Rate of Tax</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="select2" multiple="multiple"  id="filter_tcs_percentage"  name="filter_tcs_percentage">
                                <option value="">Please Select Rate of Tax</option>
                                <?php
                                foreach ($tcs_percentage as $key => $value) {
                                    if ($value->expense_bill_item_tds_percentage != '') {
                                        $percentage = (float)($value->expense_bill_item_tds_percentage);
                                        ?>
                                        <option value="<?= $value->expense_bill_item_tds_percentage ?>"><?= $percentage.'%' ?></option>
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
                <button type="button" class="btn btn-warning" id="reset-percentage" >Reset</button>
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
                <button type="button" class="btn btn-warning" id="reset-tcs-taxable" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="tcs_amt_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please Enter TDS Amount</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="number" name="from_tcs_amount" class="form-control" id="from_tcs_amount" placeholder="From" autocomplete="off" min="1">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="number" name="to_tcs_amount" class="form-control" id="to_tcs_amount" placeholder="To" autocomplete="off" min="1">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter_search" data-dismiss="modal">Apply</button>
                <button type="button" class="btn btn-warning" id="reset-tcs-amount" >Reset</button>
                <!-- <button type="button" class="btn btn-default filter_search_rest" >Reset All</button> -->
            </div>
        </div>      
    </div>
</div>
<div class="modal fade" id="tds_expense_report_select_columns" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true">
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

                                <input type="checkbox" name="sales_date" id="sales_date"  value="0" checked> 
                                <label for="sales_date" >Date of Collection</label><br>

                                <input type="checkbox"  name="deductee_name" id="deductee_name" value="1" checked>
                                <label for="deductee_name">Deductee Name</label><br>

                                <input type="checkbox" name="pan" id="pan" value="2" checked>
                                <label for="pan">TAN</label><br>

                                <input type="checkbox" name="tds_receivable" id="tds_receivable" value="3" checked>
                                <label for="tds_receivable">TDS receivable u/s</label><br>

                            </div>
                            <div class="col-md-6">
                                <input type="checkbox" name="rate_of_tax" id="rate_of_tax" value="4" checked>
                                <label for="rate_of_tax">Rate of Tax</label><br>

                                <input type="checkbox" name="taxable_value" id="taxable_value" value="5" checked>
                                <label for="taxable_value">Taxable Value</label><br>

                                <input type="checkbox" name="tds_amount" id="tds_amount" value="6" checked>
                                <label for="tds_amount">TDS Amount</label><br>

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
?>

<script src="<?php echo base_url('assets/js/') ?>datatable_variable.js"></script>
<script>
   var list_datatable;
    $(document).ready(function () {
        $("#reset-date").click(function () {
            $('#from_date').val('');
            $('#to_date').val('');
        });
        $("#reset-customer").click(function () {
            $("#deductee_name_modal .select2-selection__rendered").empty();
            $('#filter_customer option:selected').prop("selected", false);
        });
        $("#reset-pan").click(function () {
            $("#pan_modal .select2-selection__rendered").empty();
            $('#filter_pan option:selected').prop("selected", false);
        });
        $("#reset-section").click(function () {
            $("#tcs_receivable_modal .select2-selection__rendered").empty();
            $('#filter_tcs_section option:selected').prop("selected", false);
        });
        $("#reset-percentage").click(function () {
            $("#rate_tax_modal .select2-selection__rendered").empty();
            $('#filter_tcs_percentage option:selected').prop("selected", false);
        });
        $("#reset-tcs-taxable").click(function () {
            $('#from_taxable_amount').val('');
            $('#to_taxable_amount').val('');
        });
        $("#reset-tcs-amount").click(function () {
            $('#from_tcs_amount').val('');
            $('#to_tcs_amount').val('');
        });
        $("#reset-tan").click(function () {
            $("#tan_modal .select2-selection__rendered").empty();
            $('#filter_tan option:selected').prop("selected", false);
        });
        $('#refresh_table').on('click', function(){
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable();
        });
        generateOrderTable();
        function resetall() {
            $('#from_date').val('');
            $('#to_date').val('');
            $('#filter_customer option:selected').prop("selected", false);
            $('#filter_tan option:selected').prop("selected", false);
            $('#filter_tcs_section option:selected').prop("selected", false);
            $('#filter_tcs_percentage option:selected').prop("selected", false);
            $('#from_taxable_amount').val('');
            $('#to_taxable_amount').val('');
            $('#from_tcs_amount').val('');
            $('#to_tcs_amount').val('');
            $('.select2-selection__rendered').empty();
            appendFilter();
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable()
        }
        ;
        function generateOrderTable() {
            list_datatable = $("#list_datatable").DataTable({
                "processing": true,
                "serverSide": true,
                "iDisplayLength": 50,
                "lengthMenu": [ [10, 25, 50, 100], [10, 25, 50, 100] ],
                "ajax": {
                    "url": base_url + "report/tds_report_expense",
                    "dataType": "json",
                    "type": "POST",
                    "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>', 'filter_customer': $('#filter_customer').val(), 'filter_from_date': $('#from_date').val(), 'filter_to_date': $('#to_date').val(), 'filter_tan': $('#filter_tan').val(), 'filter_from_taxable_amount': $('#from_taxable_amount').val(), 'filter_to_taxable_amount': $('#to_taxable_amount').val(), 'filter_from_tcs_amount': $('#from_tcs_amount').val(), 'filter_to_tcs_amount': $('#to_tcs_amount').val(), 'filter_tcs_percentage': $('#filter_tcs_percentage').val(), 'filter_tcs_section': $('#filter_tcs_section').val()},
                    "dataSrc": function (result) {
                        var tfoot = parseFloat(result.total_list_record[0].tot_taxable_value).toFixed(2);
                        $('#total_list_record1').html(tfoot);

                        tfoot = parseFloat(result.total_list_record[0].tot_tds_amount).toFixed(2);
                        $('#total_list_record2').html(tfoot);
                        return result.data;
                    }
                },
                "columns": [
                    {"data": "sales_date"},
                    {"data": "customer"},
                    {"data": "pan_number"},
                    {"data": "payable_us"},
                    {"data": "rate_of_tax"},
                    {"data": "taxable_value"},
                    {"data": "amount"},
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
                        filename: 'TDS Receivable Reports(Expense)',
                        // orientation: 'landscape', //portrait
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
                                            text: 'TDS Receivable Reports(Expense)'
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
                            $('#tds_expense_report_select_columns').modal('show');
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

            var filter_invoice_date = "";
            var filter_customer = "";
            var filter_tan = "";
            var filter_tcs_section = "";
            var filter_tcs_percentage = "";
            var filter_taxable_amount = "";
            var filter_tcs_amount = "";

            var label = "";
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if (from_date != "" || to_date != "") {
                filter_invoice_date = "<label id='lbl_invoice_date' class='label-style'><b> Date Of Collection : </b> FROM " + from_date + " TO " + to_date + '<i class="fa fa-times"></i></label>';
            }
            if (filter_invoice_date != '') {
                label += filter_invoice_date + " ";
            }
            $("#filter_customer option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_customer += $this.text() + ", ";
                }
            });
            if (filter_customer != '') {
                label += "<label id='lbl_customer_name' class='label-style'><b> Deductee Name : </b> " + filter_customer.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_tan option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_tan += $this.text() + ", ";
                }
            });
            if (filter_tan != '') {
                label += "<label id='lbl_tan' class='label-style'><b> TAN : </b> " + filter_tan.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_tcs_section option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_tcs_section += $this.text() + ", ";
                }
            });
            if (filter_tcs_section != '') {
                label += "<label id='lbl_tcs_section' class='label-style'><b> TDS Receivable U/S : </b> " + filter_tcs_section.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            $("#filter_tcs_percentage option:selected").each(function(){
                var $this = $(this);
                if ($this.length) {
                    filter_tcs_percentage += $this.text() + ", ";
                }
            });
            if (filter_tcs_percentage != '') {
                label += "<label id='lbl_tcs_percentage' class='label-style'><b> Rate Of Tax : </b> " + filter_tcs_percentage.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }
            var from_taxable_amount = $('#from_taxable_amount').val();
            var to_taxable_amount = $('#to_taxable_amount').val();
            if (from_taxable_amount != "" || to_taxable_amount != "") {
                filter_taxable_amount = "<label id='lbl_taxable_amount' class='label-style'><b> Taxable Amount : </b> FROM " + from_taxable_amount + " TO " + to_taxable_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_taxable_amount != '') {
                label += filter_taxable_amount + " ";
            }
            var from_tcs_amount = $('#from_tcs_amount').val();
            var to_tcs_amount = $('#to_tcs_amount').val();
            if (from_tcs_amount != "" || to_tcs_amount != "") {
                filter_tcs_amount = "<label id='lbl_tcs_amount' class='label-style'><b> TDS Amount : </b> FROM " + from_tcs_amount + " TO " + to_tcs_amount + '<i class="fa fa-times"></i></label>';
            }
            if (filter_tcs_amount != '') {
                label += filter_tcs_amount + " ";
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
                $('#from_date').val('');
                $('#to_date').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_customer_name'){
                $("#deductee_name_modal .select2-selection__rendered").empty();
                $('#filter_customer option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_tan'){
                $("#tan_modal .select2-selection__rendered").empty();
                $('#filter_tan option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_tcs_section'){
                $("#tcs_receivable_modal .select2-selection__rendered").empty();
                $('#filter_tcs_section option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_tcs_percentage'){
                $("#rate_tax_modal .select2-selection__rendered").empty();
                $('#filter_tcs_percentage option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_taxable_amount'){
                $('#from_taxable_amount').val('');
                $('#to_taxable_amount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
            if(label1 == 'lbl_tcs_amount'){
                $('#from_tcs_amount').val('');
                $('#to_tcs_amount').val('');
                $("#list_datatable").dataTable().fnDestroy()
                generateOrderTable();
            }
        });
    });
$(document).ready(function(){
    $('#tds_expense_report_select_columns #select_column').click(function(){
        var checked_arr = [];
        var unchecked_arr = [];

        $.each($('#tds_expense_report_select_columns input[type="checkbox"]:checked'),function(key,value){
            checked_arr.push(this.value);
        });

        $.each($('#tds_expense_report_select_columns input[type="checkbox"]:not(:checked)'),function(key,value){
            unchecked_arr.push(this.value);
        });
        
        list_datatable.columns(checked_arr).visible(true);

        list_datatable.columns(unchecked_arr).visible(false);
       
        $('#tds_expense_report_select_columns ').modal('hide');
    });
});
</script>