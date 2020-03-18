<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<style type="text/css">
    .cwhite{color: white!important;}
    #filter{
        display: block;
        float: right;
    }
    #filter .box-body{
        padding: 0;
        text-align: right;
    }
    .main_head{font-size: 17px;
    text-align: center;}
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active"><a href="<?php echo base_url('balance_sheet'); ?>">Balance Sheet</a></li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Balance Sheet</h3>
                        <!-- <a class="btn btn-sm btn-info pull-right btn-flat" href="<?php echo base_url()?>report/all_reports">Back</a> -->
                    </div>
                    <div class="box-body">                    
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <select class="form-control select2" id="select_financial_type">
                                    <option value="financial">Financial</option>
                                    <option value="calender">Calender</option>
                                </select>
                            </div>
                            <span id="finacial_year">
                                <div class="form-group col-sm-3">
                                    <select class="form-control select2" name="financial_years">
                                        <option value="">Select Financial Year*</option>
                                        <?php
                                        if ($financial_year) {
                                            foreach ($financial_year as $key => $value) {
                                                if ($value['from_date'] != '0000-00-00 00:00:00' && $value['from_date'] != '1970-01-01 01:00:00' && $value['to_date'] != '0000-00-00 00:00:00' && $value['to_date'] != '1970-01-01 01:00:00') {
                                                    $duration = date('m/Y', strtotime($value['from_date'])) . ' - ' . date('m/Y', strtotime($value['to_date']));
                                                    if ($duration != '01/1970 - 01/1970')
                                                        echo '<option value="' . $value['year_id'] . '" '.($financial_year_id == $value['year_id'] ? 'selected' : '').'>' . $duration . '</option>';
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </span>
                            <span id="select_date" class="filter" style="display: none;">
                                <div class="form-group col-sm-2">
                                    <div id="datepicker-popup1" class="input-group date">
                                        <input type="text" class="form-control datepicker" name="from_date" placeholder="From Date" autocomplete="off">
                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-2">
                                    <div id="datepicker-popup2" class="input-group date">
                                        <input type="text" class="form-control datepicker" name="to_date" placeholder="To Date" autocomplete="off">
                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    </div>
                                </div>
                            </span>
                            <div class="col-sm-3">
                                <button type="button" class="btn btn-primary tbl-btn search_reports" id="search_reports">Search
                                </button>
                                <button type="reset" class="btn btn-primary tbl-btn reset_filter" id="reset_filter"> Reset
                                </button>
                                <!-- <button type="button" class="btn sheet_type" id="verticle"> Verticle</button>
                                <button type="button" class="btn sheet_type" id="horizontal"> Horizontal</button> -->
                            </div>

                            <form method="post" action="<?php echo base_url() ?>Balance_sheet/downloadBSReport" id="download_form">
                                <input type="hidden" name="from_date">
                                <input type="hidden" name="to_date">
                                <input type="hidden" name="year_id">
                                <input type="hidden" name="type">
                            </form>
                            <div class="col-sm-2" id="filter">
                                <div class="box-header box-body filter_body">
                                    <div class="btn-group">
                                        <span><a ref="excel" href='javascript:void(0);' class="btn btn-app download_sheet" data-placement="bottom" data-toggle="tooltip" data-original-title="Download XLS" download><i class="fa fa-file-excel-o" aria-hidden="true"></i></a> </span>
                                        <span><a ref="csv" href="javascript:void(0);" class="btn btn-app download_sheet" data-placement="bottom" data-toggle="tooltip" data-original-title="Download CSV" download><i class="fa fa-file-excel-o" aria-hidden="true"></i></a> </span>
                                        <span><a ref="pdf" href="javascript:void(0);" class="btn btn-app download_sheet" data-placement="bottom" data-toggle="tooltip" data-original-title="Download PDF" download><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a> </span>
                                    </div>
                                </div>
                            </div>
                        </div>   
                         <div id="loader">
                           <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                       </div>                 
                       	<div class="row filter ledger_row mt-50" style="display: none;">
                            <div class="col-sm-12 verticle_sheet" style="display: none;">                               
                                <table id="balance_sheet" class="table table-bordered table-hover table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="col-sm-12 horizontal_sheet" style="display: none;">
                                <div class="col-sm-6">
                                    <table id="balance_sheetA" class="table table-bordered table-hover table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="col-sm-6">
                                    <table id="balance_sheetB" class="table table-bordered table-hover table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="col-sm-12 opening_bln">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</body>
<style type="text/css">
    .table tr td.padding_30 {
        padding-left: 30px !important;
    }
    .table tr td.padding_60 {
        padding-left: 60px !important;
    }
    .table tr td.padding_90 {
        padding-left: 90px !important;
    }
    .table tr td.f_blue {
        color: #3f51b5 !important;
    }
    .table tr td.b_bottom {
        text-decoration: underline;
    }
    .table tr td.text-left {
        text-align: left;
    }
    .btn-default{}
</style>
<?php $this->load->view('layout/footer'); ?>
<script type="text/javascript">
    getBSReport();
    $("#balance_sheet,#balance_sheetA,#balance_sheetB").dataTable({
        "ordering": false,
        "searching" :false,
        "paging":   false,
        "info":     false
    });
    var last_search_id = 0;
    $("#finacial_year").show();
    $(document).ready(function () {

        $('#reset_filter').click(function () {
            $('[name=financial_years],[name=from_date],[name=to_date]').val('').trigger('change');
            $('.ledger_row').hide();
        })

        $(document).on('click', '.search_reports,.get_vouchers', function () {
            getBSReport();
        })

        $('.download_sheet').click(function () {
            var year_id = from_date = to_date = 0;
            var select_financial = $('#select_financial_type').val();
            if (select_financial != '') {
                if (select_financial == 'financial') {
                    year_id = $(document).find('[name=financial_years]').val();
                    if (year_id == 0 || year_id == '') {
                        /*alert("Select financial year first!");*/
                        alert_d.text ='Select financial year first';
                         PNotify.error(alert_d);
                        return false;
                    }
                } else {
                    from_date = $('[name=from_date]').val();
                    to_date = $('[name=to_date]').val();
                    if (from_date == '' || to_date == '') {
                        alert_d.text ='Select Date duration first!';
                        PNotify.error(alert_d);
                        /*alert('select date duration first!');*/
                        return false;
                    }
                }
            }
            $('#download_form').find('[name=from_date]').val(from_date);
            $('#download_form').find('[name=to_date]').val(to_date);
            $('#download_form').find('[name=year_id]').val(year_id);
            $('#download_form').find('[name=type]').val($(this).attr('ref'));
            $('#download_form').removeAttr('target');
            if ($(this).attr('ref') == 'pdf') {
                $('#download_form').attr('target', '_blank');
            }
            $('#download_form').submit();
        })

        /*$('#submit_comp_info').on('click', function () {
            var firm_id = $('[name=filter_admin_comp]').val();
            var acc_id = $('[name=filter_acc_name]').val();
            var address = $('.company_address').val();
            $.ajax({
                url: '<?= base_url(); ?>Auth/updateCompanyAddress',
                type: 'post',
                dataType: 'json',
                data: {firm_id: firm_id, address: address, acc_id: acc_id},
                success: function (j) {
                    if (j) {
                        alert_d.text = 'Address updated successfully!';
                        PNotify.success(alert_d);
                        return false;
                    } else {
                        alert_d.text = 'Something went wrong!';
                        PNotify.error(alert_d);
                        return false;
                    }
                }
            })
        });*/

        $("#select_financial_type").change(function () {
            var select_item = $(this).val();
            if (select_item == "") {
                $("#select_date").hide();
                $("#finacial_year").hide();
            }
            if (select_item == "calender") {
                $("#select_date").show();
                $("#finacial_year").hide();
            }
            if (select_item == "financial") {
                $("#finacial_year").show();
                $("#select_date").hide();
            }
        });

        $('.sheet_type').click(function(){
            var typ = $(this).attr('id');
            $('.sheet_type').removeClass('active');
            $(this).addClass('active');
            getBSReport();
        })

        /*$(document).on('blur','[name=closing_stock],[name=opening_stock]',function(){
         var id = $(this).attr('data-id');
         var sum = $(this).val();
         
         var column = $(this).attr('name');
         if(sum == '')sum = 0;
         sum = parseFloat(sum.replace(',',''));
         var firm_id = $('[name=filter_admin_comp]').val();
         var acc_id = $(document).find('select[name=filter_acc_name]').val();
         if(firm_id != '' && acc_id != ''){
         $.ajax({
         url:'<?= base_url() ?>Profit_loss/updateStock',
         type:'post',
         dataType:'json',
         data:{id:id,sum:sum,column:column,firm_id:firm_id,acc_id:acc_id},
         success:function(j){
         alert_d.text = j.msg;
         if(j.flag){
         PNotify.success(alert_d);
         getBSReport();
         }else{
         PNotify.error(alert_d);
         }
         }
         })
         }else{
         alert_d.text = 'Select required option';
         PNotify.error(alert_d);
         $('.ledger_row').hide();
         }
         })*/

    })

    /*function getLedgersYears(){
     var firm_id = $('[name=filter_admin_comp]').val();
     var acc_id = $(document).find('select[name=filter_acc_name]').val();
     $('[name=financial_years]').html('');
     $('.ledger_row').hide();
     $('.main_row').show();
     $.ajax({
     url:'<?= base_url() ?>Reports/getLedgersYears',
     type:'post',
     dataType:'html',
     data:{acc_id:acc_id,firm_id:firm_id},
     success:function(j){
     $(document).find('[name=financial_years]').html(j);
<?php if (@$this->session->userdata('year_id')) { ?>
         $(document).find('[name=financial_years]').val(<?= $this->session->userdata('year_id'); ?>).trigger('change');
         setTimeout(function(){getBSReport()},500);
<?php } ?>
     }
     })
     }*/

    /*function getAccCompany(){
     var firm_id = $('[name=filter_admin_comp]').val();
     $('[name=filter_acc_name]').html('<option value="">Select Acc Company*</option>');
     if(firm_id != ''){
     $.ajax({
     url:'<?= base_url(); ?>Financial_year/findAccCompany',
     type:'post',
     dataType:'html',
     data:{firm_id:firm_id},
     success:function(j){
     $('[name=filter_acc_name]').html(j);
<?php if (@$this->session->userdata('acc_id')) { ?>
         $(document).find('[name=filter_acc_name]').val(<?= $this->session->userdata('acc_id'); ?>).trigger('change');
<?php } ?>
     },error:function(){
     
     }
     })
     }
     }*/

    function getBSReport() {
        var firm_id = $('[name=filter_admin_comp]').val();
        var acc_id = $(document).find('select[name=filter_acc_name]').val();
        var select_financial = $('#select_financial_type').val();
        var year_id = from_date = to_date = 0;
        $('.ledger_row').hide();
        if (select_financial != '') {
            if (select_financial == 'financial') {
                year_id = $(document).find('[name=financial_years]').val();

                if (year_id == 0 || year_id == '' || year_id == null) {
                    //alert("Select financial year first!");
                    alert_d.text = 'Select financial year first';
                     PNotify.error(alert_d);
                    /*alert('Select financial year first');*/
                    return false;
                }
            } else {
                from_date = $('[name=from_date]').val();
                to_date = $('[name=to_date]').val();
                if (from_date == '' || to_date == '' || from_date == '0' || to_date == '0' || from_date == null || to_date == '') {
                    alert_d.text = 'select date duration first!';
                    PNotify.error(alert_d);
                    /*alert('select date duration first');*/
                    return false;
                }
            }
        }
        $('.search_reports').attr('disabled', true);
        $('.loader').addClass('.hide-loader').show();
        $.ajax({
            url: '<?= base_url(); ?>Balance_sheet/getBSReport',
            type: 'post',
            dataType: 'json',
            data: {year_id: year_id, from_date: from_date, to_date: to_date},
             beforeSend: function(){
                   // Show image container
                   $("#loader").show();
                },
            success: function (json) {
                $('.loader').addClass('.hide-loader').hide();
                var sheet_type = $('.sheet_type.active').attr('id');
                if(sheet_type == 'horizontal'){
                    $('.horizontal_sheet').show();
                    $('.verticle_sheet').hide();
                    $('#balance_sheetA tbody').html(json.html_A);
                    $('#balance_sheetB tbody').html(json.html_B);
                    $('.opening_bln').html(json.opn_html);
                }else{
                    $('.horizontal_sheet').hide();
                    $('.verticle_sheet').show();
                    $('#balance_sheet tbody').html(json.html);
                }

                $('#peroid_duration').html(json.peroid);
                $('.company_name').text(json.company_name);
                $('.company_address').text(json.primary_address);
                $('.ledger_row').show();
                $('.search_reports').attr('disabled', false);
                $("#loader").hide();
            }, error: function () {
                $('.loader').addClass('.hide-loader').hide();
                $('.search_reports').attr('disabled', false);
            }
        });
    }
    anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});

</script>