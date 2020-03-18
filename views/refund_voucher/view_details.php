<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<script type="text/javascript">
    function delete_id(id)
    {
        if (confirm('Sure To Remove This Record ?'))
        {
            window.location.href = '<?php echo base_url('ledger/delete/'); ?>' + id;
        }
    }
</script>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active"><a href="<?php echo base_url('refund_voucher'); ?>">Refund Voucher</li></a>
                <li class="active">Refund Voucher Details</li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">View Refund Voucher Details</h3>
                        <span class="btn btn-sm btn-default pull-right" id="cancel" style="margin: 1%" onclick="cancel('refund_voucher')">Back</span>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post">
                                <div class="col-md-12">
                                    <div class="well">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Voucher Number</label>
                                                    <input type="text" class="form-control" id="reference_no" name="reference_no" value="<?= $data[0]->voucher_number; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="date">Voucher Date</label>
                                                    <input type="text" class="form-control" id="date" name="date" value="<?php
                                                    $date   = $data[0]->voucher_date;
                                                    $c_date = date('d-m-Y', strtotime($date));
                                                    echo $c_date;
                                                    ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="refund">
                                                    <?php
                                                    $idd    = $this->encryption_url->encode($data[0]->reference_id);
                                                    ?>
                                                    <div class="form-group">
                                                        <label for="invoice">Advance Voucher No</label> <!-- <span><b><a href="<?php echo base_url('general_bill/view/') . $idd; ?>">(<i class="fa fa-eye"></i> View Invoice)</a></b></span> -->
                                                        <input type="text" name="invoice" class="form-control" value="<?= $data[0]->reference_number; ?>" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="amount">Voucher Amount</label>
                                                    <input type="text" class="form-control" id="amount" name="amount" value="<?= $data[0]->currency_converted_amount; ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="well">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table id="log_datatableV" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Description</th>
                                                            <th>Debit</th>
                                                            <th>Credit</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $debit  = 0;
                                                        $credit = 0;
                                                        foreach ($data as $row)
                                                        {
                                                            $id = $row->accounts_refund_id;
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $row->from_name; ?></td>
                                                                <?php
                                                                if ($row->dr_amount != 0.00)
                                                                {
                                                                    ?>
                                                                    <td><?php echo $row->converted_voucher_amount; ?></td>
                                                                    <td><?php echo "0.00"; ?></td>
                                                                <?php
                                                                }
                                                                else if ($row->cr_amount != 0.00)
                                                                {
                                                                    ?>
                                                                    <td><?php echo "0.00"; ?></td>
                                                                    <td><?php echo $row->converted_voucher_amount; ?></td>
                                                                <?php
                                                                }
                                                                else
                                                                {
                                                                    ?>
                                                                    <td><?php echo "0.00"; ?></td>
                                                                    <td><?php echo "0.00"; ?></td>
                                                            <?php } ?>
                                                            </tr>
                                                            <?php
                                                            if ($row->from_name != "Discount" && $row->from_name != "DISCOUNT")
                                                            {
                                                                if ($row->dr_amount != 0.00)
                                                                {
                                                                    $debit = bcadd($debit, $row->converted_voucher_amount, 2);
                                                                }
                                                            }
                                                            else
                                                            {
                                                                if ($row->dr_amount != 0.00)
                                                                {
                                                                    $debit = bcsub($debit, $row->converted_voucher_amount, 2);
                                                                }
                                                            }
                                                            if ($row->from_name != "Discount" && $row->from_name != "DISCOUNT")
                                                            {
                                                                if ($row->cr_amount != 0.00)
                                                                {
                                                                    $credit = bcadd($credit, $row->converted_voucher_amount, 2);
                                                                }
                                                            }
                                                            else
                                                            {
                                                                if ($row->cr_amount != 0.00)
                                                                {
                                                                    $credit = bcsub($credit, $row->converted_voucher_amount, 2);
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </tbody>
                                                    <tfoot>

                                                        <tr class="" style="height: 50px;">
                                                            <td colspan="3"></td>
                                                        </tr>

                                                        <tr>
                                                            <td ></td>
                                                            <td ><strong><?= $debit ?></strong></td>
                                                            <td ><strong><?= $credit ?></strong></td>
                                                        </tr>

                                                        <tr class="" style="height: 50px;">
                                                            <td colspan="3"></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                            </form>
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
        $("#log_datatable1").dataTable().fnDestroy();
        $('#log_datatable1').dataTable({
            "aaSorting": [],
        });
    });
</script>
