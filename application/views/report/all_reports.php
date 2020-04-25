<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// $p = array('admin', 'sales_person', 'manager');
// if (!(in_array($this->session->userdata('type'), $p))) {
//     redirect('auth');
// }
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> DashBoard</a></li>
                <li class="active">Report</li>
            </ol>
        </h5>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!--            <?php
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
            ?>            -->
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Register</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <?php 
                            if(in_array($sales_report_module_id, $active_view)){ ?>
                            <div class="col-lg-4 col-xs-6">                
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3>Invoices : <strong><?= $sales[0]->total_count ?></strong></h3>
                                        <h3>Total Value : <strong><i class="fa fa-rupee"></i><?= precise_amount($sales[0]->total_invoice, 2) ?></strong></h3>                                       
                                    </div>
                                    <div class="icon">

                                        <span class="lnr lnr-chart-bars"></span>

<!--                                        <i class="fa fa-shopping-bag" aria-hidden="true"></i>-->
                                      <!--   <img src="<?php echo base_url('assets/images/reports/sales.svg'); ?>" > -->                                    </div>
                                    <a href="<?= base_url('report/sales_test') ?>" class="small-box-footer">Sales Register <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <?php } 
                            if(in_array($sales_debit_note_report_module_id, $active_view)){?>
                            <div class="col-lg-4 col-xs-6">                
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3>Debit Notes : <strong><?= $sales_dn[0]->debit_count ?></strong></h3>
                                        <h3>Total Value : <strong><i class="fa fa-rupee"></i><?= precise_amount($sales_dn[0]->sales_debit_note_grand_total, 2) ?></strong></h3>                                       
                                    </div>
                                    <div class="icon">
                                        <span class="lnr lnr-chart-bars"></span>
                                        <!-- <img src="<?php echo base_url('assets/images/reports/sales.svg'); ?>" > -->
                                    </div>
                                    <a href="<?= base_url('report/debit_note_report') ?>" class="small-box-footer">Sales Debit Note Register <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <?php } 
                            if(in_array($sales_credit_note_report_module_id, $active_view)){?>
                            <div class="col-lg-4 col-xs-6">                
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3>Credit Notes : <strong><?= $sales_cn[0]->credit_count ?></strong></h3>
                                        <h3>Total Value : <strong><i class="fa fa-rupee"></i><?= precise_amount($sales_cn[0]->sales_credit_note_grand_total, 2) ?></strong></h3>                                       
                                    </div>
                                    <div class="icon">
                                        <span class="lnr lnr-chart-bars"></span>
                                        <!-- <img src="<?php echo base_url('assets/images/reports/sales.svg'); ?>" > -->
                                    </div>
                                    <a href="<?= base_url('report/credit_note_report') ?>" class="small-box-footer">Sales Credit Note Register <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <?php } 
                            if(in_array($purchase_report_module_id, $active_view)){?>
                            <div class="col-lg-4 col-xs-6">                
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3>Invoices : <strong><?= $purchase[0]->purchase_count ?></strong></h3>
                                        <h3>Total Value : <strong><i class="fa fa-rupee"></i><?= precise_amount($purchase[0]->purchase_grand_total, 2) ?></strong></h3>                                       
                                    </div>
                                    <div class="icon">
                                        <span class="lnr lnr-pie-chart"></span>
                                       <!--  <img src="<?php echo base_url('assets/images/reports/purchase.svg'); ?>" > -->
                                    </div>
                                    <a href="<?= base_url('report/purchase_test') ?>" class="small-box-footer">Purchase Register <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <?php } 
                            if(in_array($purchase_debit_note_report_module_id, $active_view)){?>  
                            <div class="col-lg-4 col-xs-6">                
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3>Debit Notes : <strong><?= $purchase_dn[0]->purchase_dn_count ?></strong></h3>
                                        <h3>Total Value : <strong><i class="fa fa-rupee"></i><?= precise_amount($purchase_dn[0]->purchase_debit_note_grand_total, 2) ?></strong></h3>                                       
                                    </div>
                                    <div class="icon">
                                       <span class="lnr lnr-pie-chart"></span>
                                    </div>
                                    <a href="<?= base_url('report/purchase_debit_note_report') ?>" class="small-box-footer">Purchase Debit Note Register <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <?php } 
                            if(in_array($purchase_credit_note_report_module_id, $active_view)){?> 
                            <div class="col-lg-4 col-xs-6">                
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3>Credit Notes : <strong><?= $purchase_cn[0]->purchase_cn_count ?></strong></h3>
                                        <h3>Total Value : <strong><i class="fa fa-rupee"></i><?= precise_amount($purchase_cn[0]->purchase_credit_note_grand_total, 2) ?></strong></h3>                                       
                                    </div>
                                    <div class="icon">
                                        <span class="lnr lnr-pie-chart"></span>
                                    </div>
                                    <a href="<?= base_url('report/purchase_credit_note_report') ?>" class="small-box-footer">Purchase Credit Note Register <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div> 
                            <?php } 
                            if(in_array($expense_bill_report_module_id, $active_view)){ ?>
                            <div class="col-lg-4 col-xs-6">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3>Bills : <strong><?= $expence_bill[0]->expences_count ?></strong></h3>
                                        <h3>Total Value : <strong><i class="fa fa-rupee"></i><?= precise_Amount($expence_bill[0]->expense_bill_grand_total, 2) ?></strong></h3>                                       
                                    </div>
                                    <div class="icon">
                                        <img src="<?php echo base_url('assets/images/reports/bills.svg'); ?>" >
                                    </div>
                                    <a href="<?= base_url('report/expense_bill_report') ?>" class="small-box-footer">Expense Bill Register <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <?php } 
                            if(in_array($advance_voucher_report_module_id, $active_view)){ ?>
                            <div class="col-lg-4 col-xs-6">                
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3>Vouchers : <strong><?= $advance_voucher[0]->advance_count ?></strong></h3>
                                        <h3>Total Value : <strong><i class="fa fa-rupee"></i><?= precise_amount($advance_voucher[0]->receipt_amount, 2) ?></strong></h3>                                       
                                    </div>
                                    <div class="icon">
                                        <img src="<?php echo base_url('assets/images/reports/advance_voucher.svg'); ?>" >
                                    </div>
                                    <a href="<?= base_url('report/advance_voucher_report') ?>" class="small-box-footer">Advance Voucher Register <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <?php } 
                            if(in_array($refund_voucher_report_module_id, $active_view)){ ?>
                            <div class="col-lg-4 col-xs-6">                
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3>Vouchers : <strong><?= $refund_voucher[0]->refund_count_voucher ?></strong></h3>
                                        <h3>Total Value : <strong><i class="fa fa-rupee"></i><?= precise_amount($refund_voucher[0]->receipt_amount, 2) ?></strong></h3>                                       
                                    </div>
                                    <div class="icon">
                                        <img src="<?php echo base_url('assets/images/reports/refund_voucher.svg'); ?>" >
                                    </div>
                                    <a href="<?= base_url('report/refund_voucher_report') ?>" class="small-box-footer">Refund Voucher Register <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <?php } 
                            if(in_array($receipt_voucher_report_module_id, $active_view)){ ?>
                            <div class="col-lg-4 col-xs-6">                
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3>Vouchers : <strong><?= $receipt_voucher[0]->receipt_voucher_count ?></strong></h3>
                                        <h3>Total Value : <strong><i class="fa fa-rupee"></i><?= precise_amount($receipt_voucher[0]->total_receipt_voucher_amount, 2) ?></strong></h3>                                       
                                    </div>
                                    <div class="icon">
                                        <img src="<?php echo base_url('assets/images/reports/voucher.svg'); ?>" >
                                    </div>
                                    <a href="<?= base_url('report/receipt_voucher_report') ?>" class="small-box-footer">Receipt Voucher Register <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <?php } 
                            if(in_array($payment_voucher_report_module_id, $active_view)){ ?>
                            <div class="col-lg-4 col-xs-6">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3>Vouchers : <strong><?= $payment_voucher[0]->payment_voucher_count ?></strong></h3>
                                        <h3>Total Value : <strong><i class="fa fa-rupee"></i><?= precise_Amount($payment_voucher[0]->payment_voucher_paid_amount, 2) ?></strong></h3>                                       
                                    </div>
                                    <div class="icon">
                                        <img src="<?php echo base_url('assets/images/reports/voucher.svg'); ?>" >
                                    </div>
                                    <a href="<?= base_url('report/payment_voucher_report') ?>" class="small-box-footer">Payment Voucher Register <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <?php } ?>
                            <!-- <div class="col-lg-4 col-xs-6">                
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3>44</h3>
                                        <p>Leads Report</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-users" aria-hidden="true"></i>
                                    </div>
                                    <a href="<?= base_url('report/leads_report') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>   -->          
                        </div>
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
    $(document).ready(function () {
        $('#month').on('change', function ()
        {
            var month_selected = $('#month').val();
            $('#report_month').val(month_selected);
            if (month_selected == null || month_selected == "") {
                $("#err_date").text('Select  a  month');
                return false;
            } else {
                $("#err_date").text("");
            }
        });
        //   $('#adv_xl').on('click', function()
        //  {
        //    var month_selected = $('#month').val();
        // $('#report_month').val(month_selected);
        //  });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function () {
            $('form').attr('target', '_blank');
        });
        $('#csv').click(function () {
            $('form').attr('target', '_blank');
        });
        $('#print').click(function () {
            $('form').attr('target', '_blank');
        });
        $('#submit').click(function () {
            $('form').attr('target', '');
        });
    });
    $("#hide1").click(function () {
        $(".hide1").toggle();
    });
</script>