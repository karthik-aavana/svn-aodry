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
            <li class="active">HSN Report</li>
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
                        <h3 class="box-title">HSN Report</h3>
                        <div class="pull-right double-btn">
                            <a class="btn btn-sm btn-info btn-flat back_button" href1="<?php echo base_url() ?>report/all_reports">Back</a>
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

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select HSN Number" data-type="hsn">HSN<span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#">Description</a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select UQC" data-type="uqc">UQC<span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Select total Quantity" data-type="total_quantity">Total Quantity<span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter total Value" data-type="total_value">Total Value<span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Taxable Value" data-type="taxable_value">Taxable Value<span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter IGST Amount" data-type="igst">Integrated Tax Amount<span class="fa fa-toggle-down mr-20"></span></a></th>

                                       <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter CGST Amount" data-type="cgst">Central Tax Amount<span class="fa fa-toggle-down mr-20"></span></a></th>


                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter SGST Amount" data-type="sgst">State/UT Tax Amount<span class="fa fa-toggle-down mr-20"></span></a></th>

                                        <th><a href="#" data-backdrop="static" data-keyboard="false" class="open_distinct_modal"  data-toggle="modal" data-target="#distinct-modal" data-title="Please Enter Cess Amount" data-type="cess_amount">Cess Amount<span class="fa fa-toggle-down mr-20"></span></a></th>
                              
                                    </tr>
                                </thead>
                                <tbody></tbody>                           
                            </table>
                        </div>
                         <table id="addition_table" class="mt-15 mb-25">                         
                            <thead>
                                <tr>
                                    <th>Sum Of Total Value</th>
                                    <th>Sum Of Taxable Value</th>
                                    <th>Sum Of CGST Amount</th>
                                    <th>Sum Of SGST Amount</th>
                                    <th>Sum Of IGST Amount</th>
                                    <th>Sum Of Cess Amount</th>
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
                                    <option value="hsn">HSN</option>
                                    <option value="uqc">UQC</option>
                                    <option value="total_quantity">Total Quantity</option>
                                    <option value="total_value">Total Value</option>
                                    <option value="taxable_value">Taxable Value</option>
                                    <option value="cgst">Central Tax Amount</option>
                                    <option value="sgst">State/UT Tax Amount</option>
                                    <option value="igst">Integrated Tax Amount</option>
                                    <option value="cess_amount">Cess Amount</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="hsn_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_hsn"  name="filter_hsn">
                                    <option value="">Please Select HSN Number</option>
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
                        <div class="col-md-12" id="uqc_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_uqc"  name="filter_uqc">
                                    <option value="">Please Select UQC</option>
                                     <?php
                                        foreach ($uom as $key => $value) { 
                                            if($value->unit_id != ''){
                                        ?>
                                        <option value="<?= $value->unit_id ?>"><?= $value->T_uom ?></option>
                                        <?php
                                        }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="quantity_div">
                            <div class="form-group">
                                <select class="select2" multiple="multiple"  id="filter_quantity"  name="filter_quantity">
                                    <option value="">Please Select Total Quantity</option>
                                    <?php
                                    $qty = array(); 
                                        foreach ($quantity as $key => $value) {
                                            if($value->sales_item_quantity != ''){
                                                if(!in_array($value->sales_item_quantity, $qty)){
                                                    ?>
                                                    <option value="<?= $value->sales_item_quantity ?>"><?= round($value->sales_item_quantity,2) ?></option>
                                                    <?php

                                                }
                                             $qty[$value->sales_item_quantity] = $value->sales_item_quantity;
                                             }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="from_total_amount_div">
                            <div class="form-group">
                               <input type="number" name="filter_from_total_amount" class="form-control" id="filter_from_total_amount" placeholder="From Total Value" autocomplete="off" min="1">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_total_amount_div">
                            <div class="form-group">
                               <input type="number" name="filter_to_total_amount" class="form-control" id="filter_to_total_amount" placeholder="To Total Value" autocomplete="off" min="1">
                            </div>
                        </div>
                        <div class="col-md-12" id="from_taxable_amount_div">
                            <div class="form-group">
                               <input type="number" name="filter_from_taxable_amount" class="form-control" id="filter_from_taxable_amount" placeholder="From Taxable Value" autocomplete="off" min="1">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_taxable_amount_div">
                            <div class="form-group">
                               <input type="number" name="filter_to_taxable_amount" class="form-control" id="filter_to_taxable_amount" placeholder="To Taxable Value" autocomplete="off" min="1">
                            </div>
                        </div>
                        <div class="col-md-12" id="from_igst_amount_div">
                            <div class="form-group">
                               <input type="number" name="filter_from_igst_amount" class="form-control" id="filter_from_igst_amount" placeholder="From IGST Amount" autocomplete="off" min="1">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_igst_amount_div">
                            <div class="form-group">
                               <input type="number" name="filter_to_igst_amount" class="form-control" id="filter_to_igst_amount" placeholder="To IGST Amount" autocomplete="off" min="1">
                            </div>
                        </div>
                        <div class="col-md-12" id="from_sgst_amount_div">
                            <div class="form-group">
                               <input type="number" name="filter_from_sgst_amount" class="form-control" id="filter_from_sgst_amount" placeholder="From SGST Amount" autocomplete="off" min="1">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_sgst_amount_div">
                            <div class="form-group">
                               <input type="number" name="filter_to_sgst_amount" class="form-control" id="filter_to_sgst_amount" placeholder="To SGST Amount" autocomplete="off" min="1">
                            </div>
                        </div>
                        <div class="col-md-12" id="from_cgst_amount_div">
                            <div class="form-group">
                               <input type="number" name="filter_from_cgst_amount" class="form-control" id="filter_from_cgst_amount" placeholder="From CGST Amount" autocomplete="off" min="1">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_cgst_amount_div">
                            <div class="form-group">
                               <input type="number" name="filter_to_cgst_amount" class="form-control" id="filter_to_cgst_amount" placeholder="To CGST Amount" autocomplete="off" min="1">
                            </div>
                        </div>
                        <div class="col-md-12" id="from_cess_amount_div">
                            <div class="form-group">
                               <input type="number" name="filter_from_cess_amount" class="form-control" id="filter_from_cess_amount" placeholder="From Cess Amount" autocomplete="off" min="1">
                            </div>
                        </div>
                        <div class="col-md-12" id="to_cess_amount_div">
                            <div class="form-group">
                               <input type="number" name="filter_to_cess_amount" class="form-control" id="filter_to_cess_amount" placeholder="To Cess Amount" autocomplete="off" min="1">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button name="filter_search" type="button" class="btn btn-primary" id="filter_search" value="filter" data-dismiss="modal">Apply</button>
                    <button type="button" class="btn btn-warning" id="reset-hsn">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-uqc">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-total_quantity">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-total_value">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-taxable_value">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-cgst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-sgst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-igst">Reset</button>
                    <button type="button" class="btn btn-warning" id="reset-cess_amount">Reset</button>
                   
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="hsn_report_select_columns" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true" data-backdrop="static">
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

                                <input type="checkbox" name="hsn" id="hsn"  value="0" checked> 
                                <label for="hsn" >HSN</label><br>

                                <input type="checkbox"  name="description" id="description" value="1" checked>
                                <label for="description">Description</label><br>

                                <input type="checkbox" name="uqc" id="uqc" value="2" checked>
                                <label for="uqc">UQC</label><br>

                                <input type="checkbox" name="total_quantity" id="total_quantity" value="3" checked>
                                <label for="total_quantity">Total Quantity</label><br>

                                <input type="checkbox" name="total_value" id="total_value"  value="4" checked> 
                                <label for="total_value" >Total Value</label><br>

                            </div>
                            <div class="col-md-6">
                                <input type="checkbox"  name="taxable_value" id="taxable_value" value="5" checked>
                                <label for="taxable_value">Taxable Value</label><br>

                                <input type="checkbox" name="integrated_tax_amount" id="integrated_tax_amount" value="6" checked>
                                <label for="integrated_tax_amount">Integrated Tax Amount</label><br>

                                <input type="checkbox" name="central_tax_amount" id="central_tax_amount" value="7" checked>
                                <label for="central_tax_amount">Central Tax Amount</label><br>

                                <input type="checkbox" name="state_tax_amount" id="state_tax_amount" value="8" checked>
                                <label for="state_tax_amount">State/UT Tax Amount</label><br>

                                <input type="checkbox" name="cess_amount" id="cess_amount" value="9" checked>
                                <label for="cess_amount">Cess Amount</label><br>

                            </div>
                        </div>
                    </div>
                </form>
                </div>
                <div class="modal-footer">
                    <button id='select_column' class="btn btn-primary" >Submit</button>
                 <!--    <button type="button" class="btn btn-primary tbl-btn" data-dismiss="modal">Cancel</button> -->
                </div>
        </div>
    </div>
</div>
<?php
$this->load->view('layout/footer');
// $this->load->view('sales/pay_now_modal');

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
        $("#reset-hsn").click(function () {
            $("#hsn_div .select2-selection__rendered").empty();
            $('#filter_hsn option:selected').prop("selected", false)
        });
        $("#reset-uqc").click(function () {
            $("#uqc_div .select2-selection__rendered").empty();
            $('#filter_uqc option:selected').prop("selected", false)
        });
       
        $("#reset-total_quantity").click(function () {
            $("#quantity_div .select2-selection__rendered").empty();
            $('#filter_quantity option:selected').prop("selected", false)
        });
        $("#reset-total_value").click(function () {
            $("#filter_from_total_amount").val('');
            $('#filter_to_total_amount').val('');
        });
        $("#reset-taxable_value").click(function () {
            $("#filter_from_taxable_amount").val('');
            $('#filter_to_taxable_amount').val('');
        });
        $("#reset-cgst").click(function () {
            $("#filter_from_cgst_amount").val('');
            $('#filter_to_cgst_amount').val('');
        })
        $("#reset-sgst").click(function () {
            $("#filter_from_sgst_amount").val('');
            $('#filter_to_sgst_amount').val('');
        })
        $("#reset-igst").click(function () {
            $("#filter_from_igst_amount").val('');
            $('#filter_to_igst_amount').val('');
        })
        $("#reset-cess_amount").click(function () {
            $("#filter_from_cess_amount").val('');
            $('#filter_to_cess_amount').val('');
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
                $("#hsn_div").hide();
                $("#uqc_div").hide();
                $("#quantity_div").hide();
                $("#from_total_amount_div").hide();
                $("#to_total_amount_div").hide();
                $("#from_taxable_amount_div").hide();
                $("#to_taxable_amount_div").hide();
                $("#from_igst_amount_div").hide();
                $("#to_igst_amount_div").hide();
                $("#from_sgst_amount_div").hide();
                $("#to_sgst_amount_div").hide();
                $("#from_cgst_amount_div").hide();
                $("#to_cgst_amount_div").hide();
                $("#from_cess_amount_div").hide();
                $("#to_cess_amount_div").hide();
                
            }
            if (type == "hsn")
            {
                $("#hsn_div").show();
                $('#reset-hsn').show();
            } else {
                $("#hsn_div").hide();
                $('#reset-hsn').hide();
            }

            if (type == "uqc")
            {
                $("#uqc_div").show();
                $("#reset-uqc").show();
            } else
            {
                $("#uqc_div").hide();
                $("#reset-uqc").hide();
            }

            if (type == "total_quantity")
            {
                $("#quantity_div").show();
                $("#reset-total_quantity").show();
            } else
            {
                $("#quantity_div").hide();
                $("#reset-total_quantity").hide();
            }

            if (type == "total_value")
            {
                $("#from_total_amount_div").show();
                $("#to_total_amount_div").show();
                $("#reset-total_value").show();
            } else
            {
                $("#from_total_amount_div").hide();
                $("#to_total_amount_div").hide();
                $("#reset-total_value").hide();
            }

            if (type == "taxable_value")
            {
                $("#from_taxable_amount_div").show();
                $("#to_taxable_amount_div").show();
                $("#reset-taxable_value").show();
            } else
            {
                $("#from_taxable_amount_div").hide();
                $("#to_taxable_amount_div").hide();
                $("#reset-taxable_value").hide();
            }

            if (type == "igst")
            {
                $("#from_igst_amount_div").show();
                $("#to_igst_amount_div").show();
                $("#reset-igst").show();
            } else
            {
                $("#from_igst_amount_div").hide();
                $("#to_igst_amount_div").hide();
                $("#reset-igst").hide();
            }

            if (type == "cgst")
            {
               $("#from_cgst_amount_div").show();
                $("#to_cgst_amount_div").show();
                $("#reset-cgst").show();
            } else
            {
                $("#from_cgst_amount_div").hide();
                $("#to_cgst_amount_div").hide();
                $("#reset-cgst").hide();
            }

            if (type == "sgst")
            {
                $("#from_sgst_amount_div").show();
                $("#to_sgst_amount_div").show();
                $("#reset-sgst").show();
            } else
            {
                $("#from_sgst_amount_div").hide();
                $("#to_sgst_amount_div").hide();
                $("#reset-sgst").hide();
            }

            
            if (type == "cess_amount")
            {
                $("#from_cess_amount_div").show();
                $("#to_cess_amount_div").show();
                $("#reset-cess_amount").show();
            } else
            {
                $("#from_cess_amount_div").hide();
                $("#to_cess_amount_div").hide();
                $("#reset-cess_amount").hide();
            }
        

        }).change();
        generateOrderTable();
        function resetall() {
            $('#filter_hsn option:selected').prop("selected", false)
            $('#filter_uqc option:selected').prop("selected", false)
            $('#filter_quantity option:selected').prop("selected", false)
            $('#filter_from_total_amount').val('');
            $('#filter_to_total_amount').val('');
            $('#filter_from_taxable_amount').val('');
            $('#filter_to_taxable_amount').val('');
            $('#filter_from_igst_amount').val('');
            $('#filter_to_igst_amount').val('');
            $('#filter_from_sgst_amount').val('');
            $('#filter_to_sgst_amount').val('');
            $('#filter_from_cgst_amount').val('');
            $('#filter_to_cgst_amount').val('');
            $('#filter_from_cess_amount').val('');
            $('#filter_to_cess_amount').val('');
            

            var filter_hsn = "";
            var filter_uqc = "";
            var filter_quantity = "";
            var filter_from_total_amount = $('#filter_from_total_amount').val();
            var filter_to_total_amount = $('#filter_to_total_amount').val();
            var filter_amount="";
            var filter_from_taxable_amount = $('#filter_from_taxable_amount').val();
            var filter_to_taxable_amount = $('#filter_to_taxable_amount').val();
            var filter_taxable_amount ="";
            var filter_from_igst_amount = $('#filter_from_igst_amount').val();
            var filter_to_igst_amount = $('#filter_to_igst_amount').val();
            var filter_igst = "";
            var filter_from_sgst_amount = $('#filter_from_sgst_amount').val();
            var filter_to_sgst_amount = $('#filter_to_sgst_amount').val();
            var filter_sgst ="";
            var filter_from_cgst_amount = $('#filter_from_cgst_amount').val();
            var filter_to_cgst_amount = $('#filter_to_cgst_amount').val();
            var filter_cgst ="";
            var filter_from_cess_amount = $('#filter_from_cess_amount').val();
            var filter_to_cess_amount = $('#filter_to_cess_amount').val();
            var filter_cess="";

            //if(label == undefined){
            label = '';
            //}
            //if(arr == undefined){
            // var arr = new Array();
            //}
            $("#filter_hsn option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_hsn += $this.text() + ", ";
                }
            });
            if (filter_hsn != '') {
                label += "<label id='lbl_hsn' class='label-style'><b> HSN Number : </b> " + filter_hsn.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }


            $("#filter_uqc option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_uqc += $this.text() + ", ";

                }
            });
            if (filter_uqc != '') {
                label += "<label id='lbl_uqc' class='label-style'><b> UQC : </b> " + filter_uqc.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }


            $("#filter_quantity option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_quantity += $this.text() + ", ";

                }
            });
            if (filter_quantity != '') {
                label += "<label id='lbl_quantity' class='label-style'><b> Total Quantity : </b> " + filter_quantity.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }

            

            
            if (filter_from_total_amount != "" || filter_to_total_amount != "") {
                filter_amount = "<label id='lbl_filter_amount' class='label-style'><b> Total Value : </b> FROM " + filter_from_total_amount + " TO " + filter_to_total_amount + '<i class="fa fa-times"></i></label>';
            }
            
            if (filter_from_taxable_amount != "" || filter_to_taxable_amount != "") {
                filter_taxable_amount = "<label id='lbl_filter_taxable_amount' class='label-style'><b> Total Taxable Value : </b> FROM " + filter_from_taxable_amount + " TO " + filter_to_taxable_amount + '<i class="fa fa-times"></i></label>';
            }

            if (filter_from_igst_amount != "" || filter_to_igst_amount != "") {
                filter_igst = "<label id='lbl_filter_igst' class='label-style'><b> Total IGST Amount : </b> FROM " + filter_from_igst_amount + " TO " + filter_to_igst_amount + '<i class="fa fa-times"></i></label>';
            }
            
            if (filter_from_sgst_amount != "" || filter_to_sgst_amount != "") {
                filter_sgst = "<label id='lbl_filter_sgst' class='label-style'><b> Total SGST Amount : </b> FROM " + filter_from_sgst_amount + " TO " + filter_to_sgst_amount + '<i class="fa fa-times"></i></label>';
            }
            
            if (filter_from_cgst_amount != "" || filter_to_cgst_amount != "") {
                filter_cgst = "<label id='lbl_filter_cgst' class='label-style'><b> Total CGST Amount : </b> FROM " + filter_from_cgst_amount + " TO " + filter_to_cgst_amount + '<i class="fa fa-times"></i></label>';
            }

            if (filter_from_cess_amount != "" || filter_to_cess_amount != "") {
                filter_cess = "<label id='lbl_filter_cess' class='label-style'><b> Total Cess Amount : </b> FROM " + filter_from_cess_amount + " TO " + filter_to_cess_amount + '<i class="fa fa-times"></i></label>';
            }


            if (filter_amount != '') {
                label += filter_amount + " ";
            }
            
            if (filter_taxable_amount != '') {
                label += filter_taxable_amount + " ";
            }
            if (filter_igst != '') {
                label += filter_igst + " ";
            }
            if (filter_sgst != '') {
                label += filter_sgst + " ";
            }
            if (filter_cgst != '') {
                label += filter_cgst + " ";
            }
            if (filter_cess != '') {
                label += filter_cess + " ";
            }


           
            if (label != "") {
                $('#filters_applied').html(label);
            } else {
                $('#filters_applied').html('<label></label>');
            }
            
            $('.select2-selection__rendered').empty();
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable();

        }
        function generateOrderTable() {           
            list_datatable = $('#list_datatable').DataTable({
                "processing": true,
                "serverSide": true,
                "scrollX": true,
                "iDisplayLength": 50,
                "lengthMenu": [ [10, 25, 50, 100], [10, 25, 50, 100] ],
                "ajax": {
                    "url": base_url + "report/hsn_report",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        'filter_hsn': $('#filter_hsn').val(),
                        'filter_uqc': $('#filter_uqc').val(),
                        'filter_quantity': $('#filter_quantity').val(),
                        'filter_from_total_amount': $('#filter_from_total_amount').val(),
                        'filter_to_total_amount': $('#filter_to_total_amount').val(),
                        'filter_from_taxable_amount': $('#filter_from_taxable_amount').val(),
                        'filter_to_taxable_amount': $('#filter_to_taxable_amount').val(),
                        'filter_from_igst_amount': $('#filter_from_igst_amount').val(),
                        'filter_to_igst_amount': $('#filter_to_igst_amount').val(),
                        'filter_from_sgst_amount': $('#filter_from_sgst_amount').val(),
                        'filter_to_sgst_amount': $('#filter_to_sgst_amount').val(),
                        'filter_from_cgst_amount': $('#filter_from_cgst_amount').val(),
                        'filter_to_cgst_amount': $('#filter_to_cgst_amount').val(),
                        'filter_from_cess_amount': $('#filter_from_cess_amount').val(),
                        'filter_to_cess_amount': $('#filter_to_cess_amount').val()
                      
                    },
                    "dataSrc": function (result) {
                        var tfoot = parseFloat(result.total_list_record[0].invoice_value).toFixed(2);
                        $('#total_list_record1').html(tfoot);
                        // tfoot = "";
                        tfoot = parseFloat(result.total_list_record[0].tot_taxable_value).toFixed(2);
                        $('#total_list_record2').html(tfoot);

                        tfoot = parseFloat(result.total_list_record[0].cgst_amount).toFixed(2);
                        $('#total_list_record3').html(tfoot);

                        tfoot = parseFloat(result.total_list_record[0].sgst_amount).toFixed(2);
                        $('#total_list_record4').html(tfoot);

                        
                        tfoot = parseFloat(result.total_list_record[0].igst_amount).toFixed(2);
                        $('#total_list_record5').html(tfoot);

                        tfoot = parseFloat(result.total_list_record[0].cess_amount).toFixed(2);
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
                    {"data": "hsn"},
                    {"data": "description"},
                    {"data": "uqc"},
                    {"data": "total_quantity"},
                    {"data": "total_value"},
                    {"data": "taxable_value"},
                    {'data': "integrated_tax_amount"},
                    {'data': "central_tax_amount"},
                    {'data': "state_tax_amount"},
                    {'data': "cess_amount"}
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
                        filename: 'HSN Report',
                        orientation: 'portrait', //portrait
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
                                            text: 'HSN Report'
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
                            $('#hsn_report_select_columns').modal('show');
                    }
                    
                    }],
                'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });
             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
            $("#loader").hide();
            return true;
        }
        $('#filter_search').click(function () { //button filter event click
            //var filter_customer_name =  $('#filter_customer_name option:selected').text();

            $("#loader").show();
            var filter_hsn = "";
            var filter_uqc = "";
            var filter_quantity = "";
            var filter_from_total_amount = $('#filter_from_total_amount').val();
            var filter_to_total_amount = $('#filter_to_total_amount').val();
            var filter_amount="";
            var filter_from_taxable_amount = $('#filter_from_taxable_amount').val();
            var filter_to_taxable_amount = $('#filter_to_taxable_amount').val();
            var filter_taxable_amount ="";
            var filter_from_igst_amount = $('#filter_from_igst_amount').val();
            var filter_to_igst_amount = $('#filter_to_igst_amount').val();
            var filter_igst = "";
            var filter_from_sgst_amount = $('#filter_from_sgst_amount').val();
            var filter_to_sgst_amount = $('#filter_to_sgst_amount').val();
            var filter_sgst ="";
            var filter_from_cgst_amount = $('#filter_from_cgst_amount').val();
            var filter_to_cgst_amount = $('#filter_to_cgst_amount').val();
            var filter_cgst ="";
            var filter_from_cess_amount = $('#filter_from_cess_amount').val();
            var filter_to_cess_amount = $('#filter_to_cess_amount').val();
            var filter_cess="";

            //if(label == undefined){
            label = '';
            //}
            //if(arr == undefined){
            // var arr = new Array();
            //}
            $("#filter_hsn option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_hsn += $this.text() + ", ";
                }
            });
            if (filter_hsn != '') {
                label += "<label id='lbl_hsn' class='label-style'><b> HSN Number : </b> " + filter_hsn.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }


            $("#filter_uqc option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_uqc += $this.text() + ", ";

                }
            });
            if (filter_uqc != '') {
                label += "<label id='lbl_uqc' class='label-style'><b> UQC : </b> " + filter_uqc.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }


            $("#filter_quantity option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    filter_quantity += $this.text() + ", ";

                }
            });
            if (filter_quantity != '') {
                label += "<label id='lbl_quantity' class='label-style'><b> Total Quantity : </b> " + filter_quantity.slice(0, -2) + '<i class="fa fa-times"></i></label>'+" ";
            }

            

            
            if (filter_from_total_amount != "" || filter_to_total_amount != "") {
                filter_amount = "<label id='lbl_filter_amount' class='label-style'><b> Total Value : </b> FROM " + filter_from_total_amount + " TO " + filter_to_total_amount + '<i class="fa fa-times"></i></label>';
            }
            
            if (filter_from_taxable_amount != "" || filter_to_taxable_amount != "") {
                filter_taxable_amount = "<label id='lbl_filter_taxable_amount' class='label-style'><b> Total Taxable Value : </b> FROM " + filter_from_taxable_amount + " TO " + filter_to_taxable_amount + '<i class="fa fa-times"></i></label>';
            }

            if (filter_from_igst_amount != "" || filter_to_igst_amount != "") {
                filter_igst = "<label id='lbl_filter_igst' class='label-style'><b> Total IGST Amount : </b> FROM " + filter_from_igst_amount + " TO " + filter_to_igst_amount + '<i class="fa fa-times"></i></label>';
            }
            
            if (filter_from_sgst_amount != "" || filter_to_sgst_amount != "") {
                filter_sgst = "<label id='lbl_filter_sgst' class='label-style'><b> Total SGST Amount : </b> FROM " + filter_from_sgst_amount + " TO " + filter_to_sgst_amount + '<i class="fa fa-times"></i></label>';
            }
            
            if (filter_from_cgst_amount != "" || filter_to_cgst_amount != "") {
                filter_cgst = "<label id='lbl_filter_cgst' class='label-style'><b> Total CGST Amount : </b> FROM " + filter_from_cgst_amount + " TO " + filter_to_cgst_amount + '<i class="fa fa-times"></i></label>';
            }

            if (filter_from_cess_amount != "" || filter_to_cess_amount != "") {
                filter_cess = "<label id='lbl_filter_cess' class='label-style'><b> Total Cess Amount : </b> FROM " + filter_from_cess_amount + " TO " + filter_to_cess_amount + '<i class="fa fa-times"></i></label>';
            }


            if (filter_amount != '') {
                label += filter_amount + " ";
            }
            
            if (filter_taxable_amount != '') {
                label += filter_taxable_amount + " ";
            }
            if (filter_igst != '') {
                label += filter_igst + " ";
            }
            if (filter_sgst != '') {
                label += filter_sgst + " ";
            }
            if (filter_cgst != '') {
                label += filter_cgst + " ";
            }
            if (filter_cess != '') {
                label += filter_cess + " ";
            }


           
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
            console.log(label1);
            if(label1 == 'lbl_hsn'){
                $("#hsn_div .select2-selection__rendered").empty();
                $('#filter_hsn option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }

            if(label1 == 'lbl_uqc'){
                $("#uqc_div .select2-selection__rendered").empty();
                $('#filter_uqc option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }

            if(label1 == 'lbl_quantity'){
                $("#quantity_div .select2-selection__rendered").empty();
                $('#filter_quantity option:selected').prop("selected", false);
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }

            if( label1 == 'lbl_filter_amount'){
                $('#filter_from_total_amount').val('');
                $('#filter_to_total_amount').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }

            if( label1 == 'lbl_filter_taxable_amount'){
                $('#filter_from_taxable_amount').val('');
                $('#filter_to_taxable_amount').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            
            if( label1 == 'lbl_filter_igst'){
                $('#filter_from_igst_amount').val('');
                $('#filter_to_igst_amount').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            
            if( label1 == 'lbl_filter_cgst'){
                $('#filter_from_cgst_amount').val('');
                $('#filter_to_cgst_amount').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }

            if( label1 == 'lbl_filter_sgst'){
                $('#filter_from_sgst_amount').val('');
                $('#filter_to_sgst_amount').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }

            if( label1 == 'lbl_filter_cess'){
                $('#filter_from_cess_amount').val('');
                $('#filter_to_cess_amount').val('');
                $("#list_datatable").dataTable().fnDestroy();
                generateOrderTable();
            }
            
        });
    });
$(document).ready(function(){
    $('#hsn_report_select_columns #select_column').click(function(){
        var checked_arr = [];
        var unchecked_arr = [];

        $.each($('#hsn_report_select_columns input[type="checkbox"]:checked'),function(key,value){
            checked_arr.push(this.value);
        });

        $.each($('#hsn_report_select_columns input[type="checkbox"]:not(:checked)'),function(key,value){
            unchecked_arr.push(this.value);
        });
        
        list_datatable.columns(checked_arr).visible(true);

        list_datatable.columns(unchecked_arr).visible(false);
       
        $('#hsn_report_select_columns').modal('hide');
    });
});
</script>