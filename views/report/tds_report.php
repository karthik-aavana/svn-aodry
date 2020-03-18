<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
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
                        <h3 class="box-title">
                            TDS Reports
                        </h3>    
                    </div>
                    <div class="box-body">
                        <div class="row" >
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="input-group date">
                                        <input type="text" name="from_date" value="" class="form-control datepicker" id="from_date" placeholder="From Date" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="input-group date">
                                        <input type="text" name="to_date" value="" class="form-control datepicker" id="to_date" placeholder="To date" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button type="submit" class="btn btn-info btn-flat"  id="tds" name="submit">Get Report</button>
                            </div>
                        </div>
                        <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Basic Amount</th>
                                    <th>TDS(%)</th>
                                    <th>TDS Amount</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>   
                        <table id="addition_table" class="mb-25">
                            <thead>
                                <tr>
                                    <th>Total Basic Amount</th>
                                    <th>Total TDS Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="total_basic_amt" ></td>                                   
                                    <td id="total_tds_amt"></td>
                                </tr>
                            </tbody>
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
<script>
    $(document).ready(function () {
        generateOrderTable();
        function generateOrderTable() {
            $('#list_datatable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": base_url + "report/tds_report",
                    "dataType": "json",
                    "type": "POST",
                    "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        'from_date': $('#from_date').val(),
                        'to_date': $('#to_date').val(),
                    },
                    "dataSrc": function (result)
                    {
                        if (result.recordsTotal == '0')
                        {
                            var tfoot = '<b>' + 0 + '</b><br>';
                            $('#total_basic_amt').html(tfoot);
                            tfoot = '<b>' + 0 + '</b><br>';
                            $('#total_tds_amt').html(tfoot);
                            return result.data;
                        } else
                        {
                            var tfoot = '<b>' + parseFloat(result.total_list_record[0].tot_sub_total_value).toFixed(2) + '</b><br>';
                            $('#total_basic_amt').html(tfoot);
                            tfoot = '<b>' + parseFloat(result.total_list_record[0].tot_tds_amount).toFixed(2) + '</b><br>';
                            $('#total_tds_amt').html(tfoot);
                            return result.data;
                        }
                    }
                },
                "columns": [
                    {"data": "expense_bill_date"},
                    {"data": "expense_bill_item_description"},
                    {"data": "expense_bill_item_sub_total"},
                    {"data": "expense_bill_item_tds_percentage"},
                    {"data": "expense_bill_item_tds_amount"}
                ]
            });
        }
        $('#tds').click(function () { //button filter event click
            $("#list_datatable").dataTable().fnDestroy()
            generateOrderTable()
        });
    });
</script>