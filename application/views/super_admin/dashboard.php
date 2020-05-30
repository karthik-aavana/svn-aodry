<?php

$this->load->view('super_admin/layouts/header');

?>

<div class="content-wrapper add_margin" style="padding-bottom: 80px !important;">

    <section class="content-header">

        <h5>

            <ol class="breadcrumb">

                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>

            </ol>

        </h5> 

    </section>

    <section class="content">

        <div class="row">

            <div class="col-md-12">     

                <div class="box">              

                    <div class="box-header with-border">

                        <span class="box-title external-event bg-yellow" id="today" style="font-size: 14px;">Today</span>

                        <span class="box-title external-event" id="week" style="font-size: 14px;">This Week</span>

                        <span class="box-title external-event" id="month" style="font-size: 14px;">This Month</span>

                        <span class="box-title external-event" id="year" style="font-size: 14px;">This Year</span>

                        <span class="box-title external-event" id="all" style="font-size: 14px;">All Time</span>

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

                                        <span class="small-box-footer">New Items</span>

                                    </div>

                                </div>

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

                                        <span class="small-box-footer">Purchased Items</span>

                                    </div>

                                </div>

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

                                        <span class="small-box-footer">Sold Items</span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <hr />

                        <div class="row">

                            <div class="week">

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

                                        <span class="small-box-footer">New Items</span>

                                    </div>

                                </div>

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

                                        <span class="small-box-footer">Purchased Items</span>

                                    </div>

                                </div>

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

                                        <span class="small-box-footer">Sold Items</span>

                                    </div>

                                </div>

                                <div class="col-md-2 col-xs-5">

                                    <div class="small-box bg-light-blue">

                                        <div class="inner">

                                            <?php

                                            if (isset($weekPurchase[0]->value)) {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . $weekPurchase[0]->value . "</span>";

                                            } else {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . "0</span>";

                                            }

                                            ?>

                                        </div>

                                        <span class="small-box-footer">Purchased Amount</span>

                                    </div>

                                </div>

                                <div class="col-md-2 col-xs-5">

                                    <div class="small-box bg-red">

                                        <div class="inner">

                                            <?php

                                            if (isset($weekSales[0]->value)) {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . $weekSales[0]->value . "</span>";

                                            } else {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . "0<span class='h-font'>";

                                            }

                                            ?>

                                        </div>

                                        <span class="small-box-footer">Sales Amount</span>

                                    </div>

                                </div>

                            </div>

                            <!-- <div class="col-md-12">

                              <a href="<?php echo base_url('superadmin/admin/default_sql'); ?>"><i class="fa fa-dashboard"></i>Default Sql</a>                            

                            </div> -->

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

                                        <span class="small-box-footer">New Items</span>

                                    </div>

                                </div>

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

                                        <span class="small-box-footer">Purchased Items</span>

                                    </div>

                                </div>

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

                                        <span class="small-box-footer">Sold Items</span>

                                    </div>

                                </div>

                                <div class="col-md-2 col-xs-5">

                                    <div class="small-box bg-light-blue">

                                        <div class="inner">

                                            <?php

                                            if (isset($monthPurchase[0]->value)) {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . $monthPurchase[0]->value . "</span>";

                                            } else {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . "0<span class='h-font'>";

                                            }

                                            ?>

                                        </div>

                                        <span class="small-box-footer">Purchased Amount</span>

                                    </div>

                                </div>

                                <div class="col-md-2 col-xs-5">

                                    <div class="small-box bg-red">

                                        <div class="inner">

                                            <?php

                                            if (isset($monthSales[0]->value)) {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . $monthSales[0]->value . "</span>";

                                            } else {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . "0<span class='h-font'>";

                                            }

                                            ?>

                                        </div>

                                        <span class="small-box-footer">Sold Amount</span>

                                    </div>

                                </div>

                            </div>

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

                                        <span class="small-box-footer">New Items</span>

                                    </div>

                                </div>

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

                                        <span class="small-box-footer">Purchased Items</span>

                                    </div>

                                </div>

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

                                        <span class="small-box-footer">Sold Items</span>

                                    </div>

                                </div>

                                <div class="col-md-2 col-xs-5">

                                    <div class="small-box bg-light-blue">

                                        <div class="inner">

                                            <?php

                                            if (isset($yearPurchase[0]->value)) {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . $yearPurchase[0]->value . "</span>";

                                            } else {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . "0<span class='h-font'>";

                                            }

                                            ?>

                                        </div>

                                        <span class="small-box-footer">Purchased Amount</span>

                                    </div>

                                </div>

                                <div class="col-md-2 col-xs-5">

                                    <div class="small-box bg-red">

                                        <div class="inner">

                                            <?php

                                            if (isset($yearSales[0]->value)) {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . $yearSales[0]->value . "</span>";

                                            } else {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . "0<span class='h-font'>";

                                            }

                                            ?>

                                        </div>

                                        <span class="small-box-footer">Sold Amount</span>

                                    </div>

                                </div>

                            </div>

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

                                        <span class="small-box-footer">New Items</span>

                                    </div>

                                </div>

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

                                        <span class="small-box-footer">Purchased Items</span>

                                    </div>

                                </div>

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

                                        <span class="small-box-footer">Sold Items</span>

                                    </div>

                                </div>

                                <div class="col-md-2 col-xs-5">

                                    <div class="small-box bg-light-blue">

                                        <div class="inner">

                                            <?php

                                            if (isset($allPurchase[0]->value)) {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . $allPurchase[0]->value . "</span>";

                                            } else {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . "0<span class='h-font'>";

                                            }

                                            ?>

                                        </div>

                                        <span class="small-box-footer">Purchased Amount</span>

                                    </div>

                                </div>

                                <div class="col-md-2 col-xs-5">

                                    <div class="small-box bg-red">

                                        <div class="inner">

                                            <?php

                                            if (isset($allSales[0]->value)) {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . $allSales[0]->value . "</span>";

                                            } else {

                                                echo "<span class='h-font'>" . $currency[0]->currency_symbol . "0<span class='h-font'>";

                                            }

                                            ?>

                                        </div>

                                        <span class="small-box-footer">Sold Amount></span>

                                    </div>

                                </div>

                            </div>

                            <script>

                                $('.week').hide();

                                $('.month').hide();

                                $('.year').hide();

                                $('.all').hide();

                            </script>

                        </div>

                    </div>

                </div>



                <div class="box">

                    <div class="row">

                        <div class="col-md-6">

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

                        </div>

                        <div class="col-md-6">					

                            <div class="box-header with-border">

                                <h3 class="box-title">Yearly Sales</h3>							

                            </div>

                            <div class="box-body" style="overflow-y: auto;">

                                <div id="bar_chart"></div>

                            </div>

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

            url: base_url + 'auth/product_profit',

            success: function (data1) {

                var data = new google.visualization.DataTable();

                data.addColumn('string', 'Month');

                data.addColumn('number', 'Sales');

                data.addColumn('number', 'Purchase');

                var jsonData = $.parseJSON(data1);

                for (var i in jsonData) {

                    data.addRow([jsonData[i].month, parseInt(jsonData[i].sales), parseInt(jsonData[i].purchase)]);

                }

                var options = {

                    chart: {

                        title: 'Sales Perfomance',

                        subtitle: 'Sales of Company'

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