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
                <li class="active"><a href="<?php echo base_url('receipt_voucher'); ?>">Receipt Voucher</a></li></ol>
            <li class="active">Receipt Voucher Details</li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Receipt Voucher Details</h3>
                        <span class="btn btn-sm btn-default pull-right back_button" id="cancel" style="margin: 1%">Back</span>
                    </div>
                    <div class="box-body">                       
                        <form role="form" id="form" method="post">                                
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="reference_no">Voucher Number</label>
                                        <input type="text" class="form-control" id="reference_no" name="reference_no" value="<?= $voucher_number; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="date">Voucher Date</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php
                                        $date = $voucher_date;
                                        $c_date = date('d-m-Y', strtotime($date));
                                        echo $c_date;
                                        ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="receipt">
                                        
                                        <div class="form-group">
                                            <label for="invoice">Invoice Number</label> 
                                            <input type="text" name="invoice" class="form-control" value="<?= implode(',', $reference_number); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="amount">Voucher Amount</label>
                                        <input type="text" class="form-control" id="amount" name="amount" value="<?= precise_amount($voucher_amount); ?>" readonly>
                                    </div>
                                </div>
                            </div>                                  
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
                                            $debit = 0;
                                            $credit = 0;
                                            $credit_ary = $debit_ary = array();
                                            foreach ($data as $key => $value) {
                                                if ($value->dr_amount != 0) {
                                                    array_push($debit_ary, $value);
                                                }else{
                                                    array_push($credit_ary, $value);
                                                }
                                            }
                                            
                                            usort($credit_ary, 'order_by_amount');
                                            usort($debit_ary, 'order_by_amount');

                                            function order_by_amount($a, $b) {
                                                return $b->voucher_amount > $a->voucher_amount ? 1 : -1;
                                            }

                                            $data = array_merge($debit_ary,$credit_ary);

                                            foreach ($data as $row) {
                                                $id = $row->accounts_receipt_id;
                                                ?>
                                                <tr>
                                                    <td><?php echo $row->ledger_name; ?></td>
                                                    <?php
                                                    if ($row->dr_amount != 0) {
                                                        $debit += $row->voucher_amount;
                                                        ?>
                                                        <td><?php echo precise_amount($row->voucher_amount); ?></td>
                                                        <td><?php echo "0.00"; ?></td>
                                                        <?php
                                                    } else if ($row->cr_amount != 0) {
                                                        $credit += $row->voucher_amount;
                                                        ?>
                                                        <td><?php echo "0.00"; ?></td>
                                                        <td><?php echo precise_amount($row->voucher_amount); ?></td>
                                                    <?php } else { ?>
                                                        <td><?php echo "0.00"; ?></td>
                                                        <td><?php echo "0.00"; ?></td>
                                                    <?php } ?>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="" style="height: 50px;">
                                                <td colspan="3"></td>
                                            </tr>
                                            <tr>
                                                <td ></td>
                                                <td ><strong><?= precise_amount($debit) ?></strong></td>
                                                <td ><strong><?= precise_amount($credit) ?></strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </form>
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
