<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<style type="text/css">
    td span.details-control {
        background: url('<?= base_url() ?>assets/images/expond.png') no-repeat center center;
        width: 40px;
        display: block;
        height: 30px;
        cursor: pointer;
    }
    .disable_in {
        border: none;
        pointer-events: none;
        color: #212529 !important;
    }
</style>
<div class="content-wrapper">    
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Sales Voucher</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
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
                        <h3 class="box-title">Receipt Ledger</h3>
                    </div>
                    <div class="row filter-margin">
                        <div class="col-sm-2 pr-2">
                            <div class="input-group date">
                                <input type="text" class="form-control datepicker" name="invoice_from" placeholder="Invoice From Date*">
                                <div class="input-group-addon">
                                    <span class="fa fa-calendar"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2 pl-2 pr-2">
                            <div class="input-group date">
                                <input type="text" class="form-control datepicker" name="invoice_to" placeholder="Invoice To Date*">
                                <div class="input-group-addon">
                                    <span class="fa fa-calendar"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2 pl-2 pr-2">
                            <select class="form-control select2" name="invoice_ledger">
                                <option value="">Select Ledger*</option>
                                <?php
                                if (!empty($ledgers)) {
                                    foreach ($ledgers as $key => $value) {
                                        echo "<option value='{$value['ledger_id']}'>{$value['ledger_name']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-2 pl-2 pr-2">
                            <select class="form-control select2" name="invoice_cr">
                                <option value="">Select CR/DR*</option>
                                <option value="CR">CR</option>
                                <option value="DR">DR</option>
                            </select>
                        </div>
                        <div class="col-sm-2 pl-2">
                            <button type="button" class="btn btn-primary tbl-btn" id="search_voucher">
                                Search
                            </button>
                            <button type="reset" class="btn btn-primary tbl-btn" id="reset_filter">
                                Reset
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                         <div id="loader">
                            <h1 class="ml8">
                                <span class="letters-container"> 
                                    <span class="letters letters-left">
                                        <img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px">
                                    </span>
                                </span>
                                <span class="circle circle-white"></span>
                                <span class="circle circle-dark"></span>
                                <span class="circle circle-container">
                                    <span class="circle circle-dark-dashed"></span>
                                </span>
                            </h1>
                       </div>
                        <table id="voucher_list" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th width="12%">Voucher Number</th>
                                    <th width="12%">Invoice Date</th>
                                    <th width="12%">Invoice Number</th>
                                    <th>Narration</th>
                                    <th>Invoice Total</th>
                                    <th width="6%">Expand</th>
                                    <th>Ledger</th>
                                    <th>Amount</th>
                                    <th width="6%">CR/DR</th>
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
$this->load->view('general/delete_modal');
?>
<script type="text/javascript">
    $(document).ready(function () {
        GetAllVoucher();
        $('#voucher_list tbody').on('click', 'span.details-control', function () {
            var tr = $(this).closest('tr');
            if ($(this).hasClass('expand')) {
                $(this).removeClass('expand');
                tr.nextUntil('tr.main_tr').show().end().addClass('shown');
            } else {
                tr.nextUntil('tr.main_tr').not(tr.next('tr')).hide().end();
                tr.removeClass('shown');
                //tr.nextUntil('tr.odd').hide().end().removeClass('shown');
                $(this).addClass('expand');
            }
        });
        $(document).on('click', '#search_voucher', function () {
            GetAllVoucher();
        });
        $(document).on('click', '#reset_filter', function () {
            $('[name=invoice_from],[name=invoice_to],[name=invoice_ledger],[name=invoice_cr],[name=invoice_status]').val('').trigger('change');
            GetAllVoucher();
        });
        $(document).on('click', '.paginate_button a', function () {
            if ($(this).parent().hasClass('enable')) {
                var page = $(this).attr('page');
                GetAllVoucher(page);
            }
        });
        $(document).on('change', '#chnage_page', function () {
            var page = $(this).val();
            GetAllVoucher(page);
        });
    });
    function GetAllVoucher(page = 1) {
        var voucher_type = 'sales';
        var voucher_id = $('input[name=voucher_id]').val();
        $('.main_row').show();
        $(document).find('div.filter,div.table-responsive.voucher').show();
        var invoice_from = $('input[name=invoice_from]').val();
        var invoice_to = $('input[name=invoice_to]').val();
        var invoice_ledger = $('select[name=invoice_ledger]').val();
        var invoice_cr = $('select[name=invoice_cr]').val();
        var invoice_status = $('select[name=invoice_status]').val();
        $('#search_voucher-popup').modal('hide');
        $.ajax({
            url: '<?= base_url(); ?>Receipt_ledgers/GetAllVoucher',
            type: 'post',
            dataType: 'json',
            data: {page: page, invoice_to: invoice_to, invoice_from: invoice_from, invoice_ledger: invoice_ledger, invoice_cr: invoice_cr, invoice_status: invoice_status, voucher_type: voucher_type, voucher_id: voucher_id},
              beforeSend: function(){
                   // Show image container
                   $("#loader").show();
                },
            success: function (j) {
                $('#voucher_list tbody').html(j.tbody);
                $(document).find('ul.pagination').html(j.pagination);
                $('[name=invoice_from]').val(j.from_date);
                $('[name=invoice_to]').val(j.to_date);
                $('.pages_dropdown').html(j.pages_dropdown);
                 $("#loader").hide();
                /*SetDefaultClass();*/
                //GetLedgers();
            }
        });
    }
    anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});

</script>