<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<style type="text/css">
    #ledger_table tr td{
        vertical-align: middle;
        padding: 2px 6px !important;
        white-space: normal;
    }
    #ledger_table .form-control {
        border: 2px solid #012b72;
        outline: 0;
    }

#balance_table tr td{
    padding: 2px 6px !important;
}
    .disable_in {
        border: none;
        pointer-events: none !important;
        color: #212529 !important;
        border: none !important;
         -moz-appearance: none;
        -webkit-appearance: none;
    }
    .dt-pkr-bdr {
        border: 2px solid #012b72 !important;
    }
    .date_error {
        border: 1px solid red !important;
    }
</style>
<div class="content-wrapper">		
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li>
                <a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                Closing Balance
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Closing Balance</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <label>Closing Balance Upto*</label>
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group date">
                                    <input type="text" class="form-control datepicker" name="upto_date" placeholder="Date*" value="<?=($close_date != '' ? $close_date : '');?>">
                                    <div class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button type="button" class="btn btn-primary tbl-btn save_date">Submit</button>
                            </div>
                        </div>
                        <hr>
                         <div id="loader">
                           <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                       </div>
                            <table id="balance_table" class="table table-bordered table-hover" style="display: none;">
                                <thead>
                                    <tr>
                                        <th style="display: none;">ID</th>
                                        <th style="width: 25%">Ledgers</th>
                                        <th>Amount</th>
                                        <th>CR/DR</th>
                                        <th>Action</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>                        
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
?>
<script type="text/javascript">
    var ledger_table;
    $(document).ready(function(){

        <?php if($close_date != '' ){ ?>
            ledger_table = getOpeningBalance();
        <?php } ?>
       
        $(document).on('click','.edit_ledger',function(){
            var id = $(this).attr('data-id');
            var tr = $(this).closest('tr');
            $('#balance_table tr').removeClass('edit_mode');
            tr.addClass('edit_mode');
            $('#balance_table').find('input,select').addClass('disable_in');
            if(id > 0){
                $('#balance_table').find('[data-id='+id+']').removeClass('disable_in');
            }else{
                $('#balance_table').find('[ledger_id='+$(this).attr('ledger_id')+']').removeClass('disable_in');
            }
        });

        $(document).on('keydown', function(event) {  
       if (event.key == "Escape") {
            $('table tbody').find('tr td select, tr td input').addClass('disable_in');
           }
        });

        $(document).on('click','.save_date',function(){
            var date =$('input[name=upto_date]').val();
            if(date == ''){
                /*alert_d.text = 'Please select date first';
                PNotify.error(alert_d);*/
                alert_d.text ='Select date first!';
                PNotify.error(alert_d);
            }else{
                $.ajax({
                    url:'<?=base_url();?>Closing_balance/updateDefaultDate',
                    type:'post',
                    dataType:'json',
                    data:{date:date},
                    success:function(j){
                        /*alert_d.text = j.msg;*/
                        alert_d.text = j.msg;
                        PNotify.success(alert_d);
                        if(j.flag){
                            /*PNotify.success(alert_d);*/
                            if(ledger_table) ledger_table.destroy();
                            ledger_table = getOpeningBalance();
                        }else{
                            /*PNotify.error(alert_d);*/
                        }

                        /*getUptoDate();*/
                    },error:function(){
                    }
                })
            }
        })

        $(document).on('click','.sub_ledger',function(){
            var id = $(this).attr('data-id');
            var ledger_id = $(this).attr('ledger_id');
            var tr = $(this).closest('tr');     
            if(tr.hasClass('edit_mode')){
                var amount = tr.find('input[name=ledger_amount]').val();
                var amount_type = tr.find('select[name=amount_type]').val();
                /*if(amount == 0){
                    alert_d.text = 'Please Enter Amount!';
                    PNotify.error(alert_d);
                    return false;
                }*/
                $.ajax({
                    url:'<?=base_url();?>Closing_balance/updateDefaultBalance',
                    type:'post',
                    dataType:'html',
                    data:{id:id,ledger_id:ledger_id,amount_type:amount_type,amount:amount},
                    success:function(j){
                        //alert('Your Data Updated Successfully!');
                        alert_d.text = 'Updated Successfully';
                        PNotify.success(alert_d);
                        $('#balance_table tr').removeClass('edit_mode');
                        $('#balance_table').find('input,select').addClass('disable_in');
                        if(ledger_table) ledger_table.destroy();
                        ledger_table = getOpeningBalance();
                    },error:function(){
                    }
                })
            }
        });
    });
    
    function getOpeningBalance(){
        $('#balance_table').show();
        $('.report_row').show();
        var comp_table = $('#balance_table').DataTable({        
            'ajax' : {
                url:'<?=base_url();?>Closing_balance/getDefaultOpeningBalance',
                type:'post',
                data:{},
            },
            "processing": true,
            'paging': true,
            'searching': true,
            "bStateSave": true,
            'ordering':  true,
            'columns' : [
            {'data':'id'},
            {'data' : 'ledger_name'},
            {'data' : 'amount','sType':'dom-text'},
            {'data' : 'amount_type','sType':'dom-text'},
            {'data' : 'action'}, 
            {'data' : 'status'}], 
            'order' : [[0, 'desc']],
            "columnDefs": [
            { "visible": false, "targets": [0]},
            { "orderable": false, "targets": [4,5]}
            ],
            /*"dom" : '<"top"i>rt<"bottom"flp><"clear">',*/
            "fnDrawCallback": function(oSettings) {
                var rowCount = this.fnSettings().fnRecordsDisplay();
                if (rowCount <= 10) {
                    $('.dataTables_length, .dataTables_paginate').hide();
                } else {
                    $('.dataTables_length, .dataTables_paginate').show();
                }
            },
            'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
            }
        });
        anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
        return comp_table;
    }

    function blockLedger(ths) {
        var msg = "Are you sure want to Active Status..?";
        var status = 1;
        if (!ths.is(':checked')){
            msg = "Are you sure want to Inactive Status..?";
            status = 0;
        }
        if(confirm(msg)){
            var id = ths.attr('data-id');
            var ledger_id = $(this).attr('ledger_id');
            $.ajax({
                url:'<?=base_url()?>Closing_balance/updateDefaultStatus',
                type:'post',
                dataType:'json',
                data:{id:id,ledger_id:ledger_id,status:status},
                success:function(j){
                    if(j.flag){
                        alert_d.text = result.msg;
                        PNotify.success(alert_d);
                        if(ledger_table) ledger_table.destroy();
                        ledger_table = getOpeningBalance();
                    }  
                }
            })
        }else{
            return false;
        }   
    }

    /*function getUptoDate(){
        $('.report_row').hide();
        $.ajax({
            url:'<?=base_url();?>Closing_balance/getCompanyUptoDate',
            type:'post',
            dataType:'html',
            data:{},
            success:function(j){
                $('[name=upto_date]').val(j);
                $('.date_row').show();
                if(j != ''){
                    if(ledger_table) ledger_table.destroy();
                    ledger_table = getOpeningBalance();
                    $('.no_date_added').hide();
                }else{
                    $('.no_date_added').show();
                }
                $('.main_row').show();
            },error:function(){
            }
        })
    }*/
</script>