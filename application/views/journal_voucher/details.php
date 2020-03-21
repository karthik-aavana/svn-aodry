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
                <li class="active"><a href="<?php echo base_url('sales_voucher'); ?>">General Voucher</li></a>
                <li class="active">General Voucher Details</li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                       <h3 class="box-title">General Voucher Details</h3>
                        <span class="btn btn-sm btn-default pull-right back_button" id="cancel" style="margin: 1%">Back</span> 
                    </div>
                    <div class="box-body">
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
                                    $date = $data[0]->voucher_date;
                                    $c_date = date('d-m-Y', strtotime($date));
                                    echo $c_date;
                                    ?>" readonly>
                                </div>
                            </div>
                            <!-- <div class="col-sm-3">
                                <div class="sales">
                                    <?php $idd = $this->encryption_url->encode($data[0]->reference_id); ?>
                                    <div class="form-group">
                                        <label for="invoice">Invoice Number</label> <span><b><a href="<?php echo base_url("{$data[0]->reference_type}/view/") . $idd; ?>">(<i class="fa fa-eye"></i> View Invoice)</a></b></span>
                                        <input type="text" name="invoice" class="form-control" value="<?= $data[0]->reference_number; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="amount">Voucher Amount</label>
                                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?= precise_amount($data[0]->receipt_amount); ?>" readonly>
                                </div>
                            </div> -->
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
                                        <tr>
                                            <td colspan="3">
                                                <b> Transaction Purpose :- </b><?php echo $data[0]->purpose_option;?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <b> Voucher Type :- </b><?php echo $data[0]->voucher_type;?>
                                            </td>
                                        </tr>
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
                                        
                                        /*usort($credit_ary, 'order_by_amount');
                                        usort($debit_ary, 'order_by_amount');

                                        function order_by_amount($a, $b) {
                                            return $b->voucher_amount > $a->voucher_amount ? 1 : -1;
                                        }*/

                                        $data = array_merge($debit_ary,$credit_ary);
                                       
                                        foreach ($data as $row) {
                                           // $id = $row->accounts_sales_id;
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $row->ledger_name; ?></td>
                                                <?php
                                                if ($row->dr_amount != 0) {
                                                    $debit += $row->dr_amount;
                                                    ?>
                                                    <td>
                                                        <?php echo precise_amount($row->dr_amount); ?></td>
                                                    <td>
                                                        <?php echo "0.00"; ?>
                                                    </td>
                                                    <?php
                                                } else if ($row->cr_amount != 0) {
                                                    $credit += $row->cr_amount;
                                                    ?>
                                                    <td>
                                                        <?php echo "0.00"; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo precise_amount($row->cr_amount); ?></td>
                                                <?php } else { ?>
                                                    <td>
                                                        <?php echo "0.00"; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo "0.00"; ?>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="" style="height: 50px;">
                                            <td colspan="3"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td><strong><?= precise_amount($debit) ?></strong>
                                            </td>
                                            <td><strong><?= precise_amount($credit) ?></strong>
                                            </td>
                                        </tr>                                       
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>                  

<?php $this->load->view('layout/footer'); ?>