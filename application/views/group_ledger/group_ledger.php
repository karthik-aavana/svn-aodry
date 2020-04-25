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
    /*#ledger_table .form-control, select2 {
        border: 2px solid #012b72;
        outline: 0;
    }*/
    .disable_in {
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
                Groups & Ledgers List
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Groups & Ledgers</h3>
                        <?php
                        if (in_array($group_ledgers_module_id, $active_add)) {
                        ?>
                        <a class="btn btn-sm btn-info pull-right add_groups" href="javascript:void(0);">Add Group/Subgroup/Ledger</a>
                        <?php } ?>
                    </div>
                    <div class="box-body">
                         <div id="loader">
                        <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                    </div>
                        <div class="">
                            <table id="ledger_table" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="display: none;width: 0%;">Id</th>
                                        <th>Main Group</th>
                                        <th>Sub Group-1</th>
                                        <th>Sub Group-2</th>
                                        <th width="25%">Ledger</th>
                                        <th>Report</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('group_ledger/add_group_ledger');
?>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script type="text/javascript">
    var edit_privilege = true;
    <?php if(in_array($group_ledgers_module_id, $active_edit)){ ?>
        edit_privilege = false;
    <?php } ?>
    GetCustomLedger();
    $(document).ready(function () {
        $(document).on('click', '.edit_grp', function () {
            if(edit_privilege){
                alert_d.text ="Access denied. Please Contact Admin";
                PNotify.error(alert_d);
                return false;
            }
            $('table tbody').find('input,select').addClass('disable_in');
            $('table tbody').find('tr').removeClass('edit_mode');
            $('table tbody').find('.select2 span').removeClass('select2-selection--single');
            var tr = $(this).closest('tr');            
            var id = $(this).attr('data-id');
            tr.addClass('edit_mode');
            tr.find('.select2').select2();
            tr.find('select[data-id=' + id + '],input[data-id=' + id + ']').removeClass('disable_in');
        });
        $(document).on('click', '.add_groups', function () {
            $('#add_groups').find('select').val('').prop('selected', true).trigger('change');
            $('#add_groups').find('input').val('');
            $('#add_groups').modal({show: true, backdrop: 'static', keyboard: false});
        });
        $(document).on('click', '.update_grp', function () {
            var tr = $(this).closest('tr');
            if (tr.hasClass('edit_mode')) {
                var ledger_id = $(this).attr('data-id');
                var is_edit = $(this).attr('is_edit');
                var ledger = tr.find('[name=ledger]').val();
                var branch_id = tr.find('[name=branch_id]').val();
                var firm_id = tr.find('[name=firm_id]').val();
                var main_grp_id = primary_sub_group = sec_sub_group = report_id = '';
                if (is_edit == '1') {
                    main_grp_id = tr.find('[name=main_grp_id]').val();
                    primary_sub_group = tr.find('[name=sub_group_name_1]').val();
                    sec_sub_group = tr.find('[name=sub_group_name_2]').val();
                    report_id = tr.find('[name=report_id]').val();
                    if (main_grp_id == '' || report_id == '') {
                        alert_d.text ='Please select required field!';                         
                         PNotify.error(alert_d);
                        //alert('Please select required field!');
                        return false;
                    }
                 //   console.log(primary_sub_group, sec_sub_group);
                    if (sec_sub_group != '' && primary_sub_group == '') {
                         alert_d.text ='Please add primary sub group first!';                         
                         PNotify.error(alert_d);	
                       // alert('Please add primary sub group first!');
                        return false;
                    }
                }
                $.ajax({
                    url: '<?= base_url(); ?>GroupLedgers/updateLedgerInfo',
                    type: 'post',
                    dataType: 'json',
                    data: {ledger_id: ledger_id, main_grp_id: main_grp_id, primary_sub_group: primary_sub_group, sec_sub_group: sec_sub_group, report_id: report_id, ledger: ledger, is_edit: is_edit, firm_id: firm_id, branch_id: branch_id},
                    success: function (j) {
                        
                        /*alert_d.text =j.msg;*/
                        if (j.flag) {
                            alert_d.text = j.msg ;
                            PNotify.success(alert_d);
                            /*PNotify.success(alert_d);	*/
                            tr.removeClass('edit_mode');
                            tr.find('select,input').addClass('disable_in');
                            tr.find('.select2 span').removeClass('select2-selection--single');
                            /*getFilterSubGroups();*/
                        } else {
                            alert_d.text = j.msg ;
                            PNotify.error(alert_d);
                            /*PNotify.error(alert_d);	*/
                        }
                    }
                });
            }
        });

        $(document).on('keydown', function(event) {  
           if (event.key == "Escape") {
                $('table tbody').find('tr td select, tr td input').addClass('disable_in');  
                $('table tbody').find('.select2 span').removeClass('select2-selection--single');             
               }
        });

        $(document).on('change', '#add_groups [name=main_grp_id]', function () {
            var main_grp_id = $(this).val();
            if (main_grp_id != '') {
                $.ajax({
                    url: '<?= base_url(); ?>GroupLedgers/getPrimaryGroups',
                    type: 'post',
                    dataType: 'json',
                    data: {main_grp_id: main_grp_id},
                    success: function (j) {
                        if (j.flag) {
                            var opt = '<option value="">Add (other)</option>';
                            $.each(j.data, function (k, v) {
                                opt += "<option value='" + v.primary_sub_group + "'>" + v.primary_sub_group + "</option>";
                            });
                            $('#add_groups #primary_sub_group_drop').html(opt);
                        } else {
                            $('#add_groups #primary_sub_group_drop').html('');
                        }
                    }
                });
            }
        });
        /*$(document).on('change', '#add_groups select[name=primary_sub_group]', function () {*/
        $("#add_groups [name=primary_sub_group]").on('input', function () {
            var main_grp_id = $('#add_groups [name=main_grp_id]').val();
            var primary_sub_group = $(this).val();
            if ($(this).val() == '') {
                $('#add_groups input[name=primary_sub_group],#add_groups input[name=sec_sub_group]').show();
            } else if (main_grp_id != '' && primary_sub_group != '') {
                $.ajax({
                    url: '<?= base_url(); ?>GroupLedgers/getSecondaryGroupsLedger',
                    type: 'post',
                    dataType: 'json',
                    data: {main_grp_id: main_grp_id, primary_sub_group: primary_sub_group},
                    success: function (j) {
                        if (j.flag) {
                            var opt = '<option value="">Add (other)</option>';
                            $.each(j.data, function (k, v) {
                                opt += "<option value='" + v.sub_group_name_2 + "'>" + v.sub_group_name_2 + "</option>";
                            });
                            $('#add_groups #sec_sub_group_drop').html(opt);
                        } else {
                            $('#add_groups #sec_sub_group_drop').html('');
                        }
                    }
                });
            }
        });

        $("#add_groups [name=primary_sub_group]").on('input', function () {
            var val = this.value;
            console.log(val);
            /*if($('#primary_sub_group_drop option').filter(function(){
                return this.value.toUpperCase() === val.toUpperCase();        
            }).length) {
                //send ajax request
            }*/
        });

        $(document).on('click', '#addNewGroup', function () {
            var main_grp_id = $('#add_groups [name=main_grp_id]').val();
            var report_id = $('#add_groups [name=report_id]').val();
            if (main_grp_id == '' || report_id == '') {
                alert_d.text ='Select required field!';        
                 PNotify.error(alert_d);	
                //alert('Select required field!');
                return false;
            }
            var primary_sub_group = $('#add_groups select[name=primary_sub_group]').val();
            if ($.trim(primary_sub_group) == '') {
                primary_sub_group = $('#add_groups input[name=primary_sub_group]').val();
            }
            var sec_sub_group = $('#add_groups select[name=sec_sub_group]').val();
            if ($.trim(sec_sub_group) == '') {
                sec_sub_group = $('#add_groups input[name=sec_sub_group]').val();
            }
            if (primary_sub_group == '' && sec_sub_group != '') {
               // alert('Sub group 1 is required!');
                alert_d.text ='Subgroup-1 is required!';
                 PNotify.error(alert_d);	
                return false;
            }
            var ledger = $('#add_groups [name=ledger_name]').val();
            if (ledger == '') {
               // alert('Please enter ledger name!');
                alert_d.text ='Please enter ledger name!';         
                 PNotify.error(alert_d);	
                return false;
            }
            $.ajax({
                url: '<?= base_url(); ?>GroupLedgers/addNewLedgerGroup',
                type: 'post',
                dataType: 'json',
                data: {main_grp_id: main_grp_id, report_id: report_id, primary_sub_group: primary_sub_group, sec_sub_group: sec_sub_group, ledger: ledger},
                beforeSend: function(){
                     // Show image container
                    $("#loader").show();
                },
                
                success: function (j) {
                   // alert(j.msg);
                    /*alert_d.text =j.msg;*/
                    alert_d.text = j.msg;   
                    if (j.flag) {
                        $("#loader").hide();      
                        PNotify.success(alert_d);
                        $('#add_groups').modal('hide');
                        var pg = $(document).find('.paginate_button.active a').attr('page');
                        ledgerTable.destroy();
                        ledgerTable = GetCustomLedger();
                    } else {
                        $("#loader").hide();        
                        PNotify.error(alert_d);
                        /*PNotify.error(alert_d);*/
                    }
                }
            });
            anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
        });
    });
    function GetCustomLedger() {
        $(document).find('div.filter,div.table-responsive.ledger').hide();
        var filter_main_group = $('[name=filter_main_group]').val();
        var filter_sub_group_1 = $('[name=filter_sub_group_1]').val();
        var filter_sub_group_2 = $('[name=filter_sub_group_2]').val();
        var filter_report = $('[name=filter_report]').val();
        var filter_group_status = $('[name=filter_group_status]').val();
        var filter_ledger = $('[name=filter_ledger]').val();
        $(document).find('div.filter,div.table-responsive.ledger').show();
        var comp_table = $('#ledger_table').DataTable({
            'ajax': {
                url: '<?= base_url(); ?>GroupLedgers/GetCustomLedger',
                type: 'post',
                data: {filter_main_group: filter_main_group, filter_sub_group_1: filter_sub_group_1, filter_sub_group_2: filter_sub_group_2, filter_report: filter_report, filter_group_status: filter_group_status, filter_ledger: filter_ledger},
            },
            "processing": true,
            'paging': true,
            'searching': true,
            "bStateSave": true,
            'ordering': true,
            'columns': [
                {'data': 'ledger_id'},
                {'data': 'main_group_name'},
                {'data': 'sub_group_1', "sType": "mystring"},
                {'data': 'sub_group_2', "sType": "mystring"},
                {'data': 'ledger', "sType": "mystring"},
                {'data': 'report_name', "sType": "mystring"},
                {'data': 'status'},
                {'data': 'action'}],
            'order': [[0, 'desc']],
            "columnDefs": [
                {"visible": false, "targets": [0]},
                {"orderable": false, "targets": [6, 7]}
            ],
            "fnDrawCallback": function (oSettings) {
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
                },
            });
             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});

        return comp_table;
    }
    function blockGroup(ths) {
        if(edit_privilege){
            alert_d.text ="Access denied. Please Contact Admin";
            PNotify.error(alert_d);
            return false;
        }
        var msg = "Are you sure want to Active Status?";
        var status = 1;
        if (!ths.is(':checked')) {
            status = 0;
            var msg = "Are you sure want to Inactive Status?";
        }
        if (confirm(msg)) {
            var sub_grp_id = ths.attr('data-id');
            $.ajax({
                url: '<?= base_url() ?>GroupLedgers/updateLedgerStatus',
                type: 'post',
                dataType: 'json',
                data: {sub_grp_id: sub_grp_id, status: status},
                success: function (j) {
                    alert_d.text = j.msg;
                    PNotify.success(alert_d);
                    /*alert_d.text =j.msg;                   
                     PNotify.success(alert_d);*/
                }
            });
        } else {
            return false;
        }
    }
</script>