<?php
// echo $test;
if (!$this->session->userdata('email')) {
    redirect('auth/login');
}
function checkEmpty($table) {
    if ($table <= 0) {
        return '<span class="badge bg-default glyphicon glyphicon-ok"></span>';
    } else {
        return '<span class="badge glyphicon glyphicon-ok" style="background-color:#1fcf07"></span>';
    }
}
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>
                        <?php echo 'Dashboards'; ?></a></li>
                <li>Reports</li>
            </ol>
        </h5>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <span class="box-title external-event bg-yellow" id="today" style="font-size: 14px;"><?php echo'Today'; ?></span>
                        <span class="box-title external-event" id="week" style="font-size: 14px;"><?php echo 'This week'; ?></span>
                        <span class="box-title external-event" id="month" style="font-size: 14px;"><?php echo 'This Month'; ?></span>
                        <span class="box-title external-event" id="year" style="font-size: 14px;"><?php echo 'This Year'; ?></span>
                        <span class="box-title external-event" id="all" style="font-size: 14px;"><?php echo 'All Time'; ?></span>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" type="button" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                            <button class="btn btn-box-tool" type="button" data-widget="remove">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12 today">
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-aqua">
                                        <div class="inner">
                                            <?php
                                            if (isset($todayProduct[0]->item)) {
                                                echo "<span class='h-font'>" . $todayProduct[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0</span>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'New Items'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-green">
                                        <div class="inner">
                                            <?php
                                            if (isset($todayPurchase[0]->item)) {
                                                echo "<span class='h-font'>" . $todayPurchase[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0</span>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Purchased Item'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-yellow">
                                        <div class="inner">
                                            <?php
                                            if (isset($todaySales[0]->item)) {
                                                echo "<span class='h-font'>" . $todaySales[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0</span>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Sold Items'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-light-blue">
                                        <div class="inner">
                                            <?php
                                            if (isset($todayPurchase[0]->value)) {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . $todayPurchase[0]->value . "</span>";
                                            } else {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . "0</span>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Purchased Value'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-red">
                                        <div class="inner">
                                            <?php
                                            if (isset($todaySales[0]->value)) {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . $todaySales[0]->value . "</span>";
                                            } else {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . "0</span>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Sales Value'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                            </div><!-- /.col 12 -->
                            <div class="col-md-12 week">
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-aqua">
                                        <div class="inner">
                                            <?php
                                            if (isset($weekProduct[0]->item)) {
                                                echo "<span class='h-font'>" . $weekProduct[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0</span>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'New Items'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-green">
                                        <div class="inner">
                                            <?php
                                            if (isset($weekPurchase[0]->item)) {
                                                echo "<span class='h-font'>" . $weekPurchase[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0</span>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Purchased Item'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-yellow">
                                        <div class="inner">
                                            <?php
                                            if (isset($weekSales[0]->item)) {
                                                echo "<span class='h-font'>" . $weekSales[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0</span>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Sold Items'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-light-blue">
                                        <div class="inner">
                                            <?php
                                            if (isset($weekPurchase[0]->value)) {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . $weekPurchase[0]->value . "</span>";
                                            } else {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . "0</span>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Purchased Value'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-red">
                                        <div class="inner">
                                            <?php
                                            if (isset($weekSales[0]->value)) {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . $weekSales[0]->value . "</span>";
                                            } else {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . "0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Sales Value'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                            </div><!-- /.col 12 -->
                            <div class="col-md-12 month">
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-aqua">
                                        <div class="inner">
                                            <?php
                                            if (isset($monthProduct[0]->item)) {
                                                echo "<span class='h-font'>" . $monthProduct[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'New Items'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-green">
                                        <div class="inner">
                                            <?php
                                            if (isset($monthPurchase[0]->item)) {
                                                echo "<span class='h-font'>" . $monthPurchase[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Purchased Item'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-yellow">
                                        <div class="inner">
                                            <?php
                                            if (isset($monthSales[0]->item)) {
                                                echo "<span class='h-font'>" . $monthSales[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Sold Items'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-light-blue">
                                        <div class="inner">
                                            <?php
                                            if (isset($monthPurchase[0]->value)) {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . $monthPurchase[0]->value . "</span>";
                                            } else {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . "0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Purchased Value'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-red">
                                        <div class="inner">
                                            <?php
                                            if (isset($monthSales[0]->value)) {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . $monthSales[0]->value . "</span>";
                                            } else {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . "0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Sales Value'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                            </div><!-- /.col 12 -->
                            <div class="col-md-12 year">
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-aqua">
                                        <div class="inner">
                                            <?php
                                            if (isset($yearProduct[0]->item)) {
                                                echo "<span class='h-font'>" . $yearProduct[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'New Items'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-green">
                                        <div class="inner">
                                            <?php
                                            if (isset($yearPurchase[0]->item)) {
                                                echo "<span class='h-font'>" . $yearPurchase[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Purchased Item'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-yellow">
                                        <div class="inner">
                                            <?php
                                            if (isset($yearSales[0]->item)) {
                                                echo "<span class='h-font'>" . $yearSales[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Sold Items'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-light-blue">
                                        <div class="inner">
                                            <?php
                                            if (isset($yearPurchase[0]->value)) {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . $yearPurchase[0]->value . "</span>";
                                            } else {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . "0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Purchased Value'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-red">
                                        <div class="inner">
                                            <?php
                                            if (isset($yearSales[0]->value)) {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . $yearSales[0]->value . "</span>";
                                            } else {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . "0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Sales Value'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                            </div><!-- /.col 12 -->
                            <div class="col-md-12 all">
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-aqua">
                                        <div class="inner">
                                            <?php
                                            if (isset($allProduct[0]->item)) {
                                                echo "<span class='h-font'>" . $allProduct[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'New Items'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-green">
                                        <div class="inner">
                                            <?php
                                            if (isset($allPurchase[0]->item)) {
                                                echo "<span class='h-font'>" . $allPurchase[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Purchased Item'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-yellow">
                                        <div class="inner">
                                            <?php
                                            if (isset($allSales[0]->item)) {
                                                echo "<span class='h-font'>" . $allSales[0]->item . "</span>";
                                            } else {
                                                echo "<span class='h-font'>0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Sold Items'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-light-blue">
                                        <div class="inner">
                                            <?php
                                            if (isset($allPurchase[0]->value)) {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . $allPurchase[0]->value . "</span>";
                                            } else {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . "0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Purchased Value'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                                <div class="col-md-2 col-xs-5">
                                    <div class="small-box bg-red">
                                        <div class="inner">
                                            <?php
                                            if (isset($allSales[0]->value)) {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . $allSales[0]->value . "</span>";
                                            } else {
                                                echo "<span class='h-font'>" . $this->session->userdata('symbol') . "0<span class='h-font'>";
                                            }
                                            ?>
                                        </div>
                                        <span class="small-box-footer"><?php echo 'Sales Value'; ?></span>
                                    </div>
                                </div><!-- /.col -->
                            </div><!-- /.col 12 -->
                            <script>
                                $('.week').hide();
                                $('.month').hide();
                                $('.year').hide();
                                $('.all').hide();
                            </script>
                        </div><!-- /.row -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- Application buttons -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-body">
                                    <a class="btn btn-app" href="<?php echo base_url('purchase_order'); ?>">
                                        <i class="fa fa-square-o text-red"></i> Purchase Order
                                    </a>
                                    <a class="btn btn-app" href="<?php echo base_url('purchase'); ?>">
                                        <i class="fa fa-square-o text-aqua"></i> Purchase
                                    </a>
                                    <a class="btn btn-app" href="<?php echo base_url('quotation'); ?>">
                                        <i class="fa fa-star text-yellow"></i> Quotation
                                    </a>
                                    <a class="btn btn-app" href="<?php echo base_url('sales'); ?>">
                                        <i class="fa fa-shopping-cart text-green"></i> Sales
                                    </a>
                                    <a class="btn btn-app" href="<?php echo base_url('expense_bill'); ?>">
                                        <i class="fa fa-file-o text-suffron"></i> Expense Bill
                                    </a>
                                    <a class="btn btn-app" href="<?php echo base_url('sales/list_credit_note'); ?>">
                                        <i class="fa fa-shopping-cart text-green"></i> Credit Note
                                    </a>
                                    <a class="btn btn-app" href="<?php echo base_url('sales/list_debit_note'); ?>">
                                        <i class="fa fa-shopping-cart text-aqua"></i> Debit Note
                                    </a>
                                    <a class="btn btn-app" href="<?php echo base_url('purchase_return'); ?>">
                                        <i class="fa fa-square-o text-yellow"></i> Purchase Return
                                    </a>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                        <!-- /.box -->
                        <!-- Application buttons -->
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-body">
                                    <a class="btn btn-app" href="<?php echo base_url('receipt_voucher/advance_voucher_list'); ?>">
                                        <i class="fa fa-th-large text-maroon"></i>Advance Voucher
                                    </a>
                                    <a class="btn btn-app" href="<?php echo base_url('receipt_voucher'); ?>">
                                        <i class="fa fa-th-large text-green"></i>Receipt Voucher
                                    </a>
                                    <a class="btn btn-app" href="<?php echo base_url('payment_voucher'); ?>">
                                        <i class="fa fa-th-large text-black"></i>Payment Voucher
                                    </a>
                                    <a class="btn btn-app" href="<?php echo base_url('receipt_voucher/list_refund_voucher'); ?>">
                                        <i class="fa fa-th-large text-red"></i>Refund Voucher
                                    </a>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="col-md-6">
                        <!-- /.row -->
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?php echo $this->lang->line('dashboard_yearly_sales'); ?></h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-box-tool" type="button" data-widget="collapse">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                        <button class="btn btn-box-tool" type="button" data-widget="remove">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div><!-- /box-tools pull-right -->
                                </div>
                                <div class="box-body" style="overflow-y: auto;">
                                    <div id="bar_chart"></div>
                                </div><!-- box-->
                            </div><!-- box -->
                        </div><!-- /.col 7-->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<?php
$this->load->view('layout/footer');
?>
<script>
    $(document).ready(function () {
        var color = "bg-yellow"
        $('#today').click(function () {
            $('#today').addClass(color);
            $('#week').removeClass(color);
            $('#month').removeClass(color);
            $('#year').removeClass(color);
            $('#all').removeClass(color);
            $('.today').show();
            $('.week').hide();
            $('.month').hide();
            $('.year').hide();
            $('.all').hide();
        });
        $('#week').click(function () {
            $('#week').addClass(color);
            $('#today').removeClass(color);
            $('#month').removeClass(color);
            $('#year').removeClass(color);
            $('#all').removeClass(color);
            $('.today').hide();
            $('.week').show();
            $('.month').hide();
            $('.year').hide();
            $('.all').hide();
        });
        $('#month').click(function () {
            $('#month').addClass(color);
            $('#week').removeClass(color);
            $('#today').removeClass(color);
            $('#year').removeClass(color);
            $('#all').removeClass(color);
            $('.today').hide();
            $('.week').hide();
            $('.month').show();
            $('.year').hide();
            $('.all').hide();
        });
        $('#year').click(function () {
            $('#year').addClass(color);
            $('#week').removeClass(color);
            $('#month').removeClass(color);
            $('#today').removeClass(color);
            $('#all').removeClass(color);
            $('.today').hide();
            $('.week').hide();
            $('.month').hide();
            $('.year').show();
            $('.all').hide();
        });
        $('#all').click(function () {
            $('#weallek').addClass(color);
            $('#week').removeClass(color);
            $('#month').removeClass(color);
            $('#year').removeClass(color);
            $('#today').removeClass(color);
            $('.today').hide();
            $('.week').hide();
            $('.month').hide();
            $('.year').hide();
            $('.all').show();
        });
    });
</script>
<script type="text/javascript">
    google.charts.load('current', {'packages': ['bar']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url('auth/product_profit') ?>',
            success: function (data1) {
                var data = new google.visualization.DataTable();
                data.addColumn('string', '<?php echo $this->lang->line('dashboard_month'); ?>');
                data.addColumn('number', '<?php echo $this->lang->line('header_sales'); ?>');
                data.addColumn('number', '<?php echo $this->lang->line('header_purchase'); ?>');
                var jsonData = $.parseJSON(data1);
                for (var i in jsonData) {
                    data.addRow([jsonData[i].month, parseInt(jsonData[i].sales), parseInt(jsonData[i].purchase)]);
                }
                var options = {
                    chart: {
                        title: '<?php echo $this->lang->line('dashboard_sales_performance'); ?>',
                        subtitle: '<?php echo $this->lang->line('dashboard_sales_of_company'); ?>'
                    },
                    width: 600,
                    height: 350,
                    axes: {
                        x: {
                            0: {side: 'bottom'}
                        },
                        y: {
                            0: {side: 'left'}
                        }
                    }
                };
                var chart = new google.charts.Bar(document.getElementById('bar_chart'));
                chart.draw(data, options);
            }
        });
    }
</script>