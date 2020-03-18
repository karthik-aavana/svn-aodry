<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> <!-- Dashboard -->Dashboard</a></li>
                <li><a href="<?php echo base_url('bank_statement'); ?>">View Bank Statement</a>
                </li>
                <li class="active">Bank Statement
                </li>
            </ol>
        </h5>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">

                    <div class="box-body">

                        <table id="log_datatable1" class="table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th width="10%"><strong>Date</strong></th>
                                    <th width="50%"><strong>Description</strong></th>
                                    <th width="10%"><strong>Cheque/Reference No.</strong></th>
                                    <th width="10%"><strong>Debit</strong></th>
                                    <th width="10%"><strong>Credit</strong></th>
                                    <th width="10%"><strong>Closing Balance</strong></th>
                                </tr>
                            </thead>
                            <tbody  id="tbody_rawdata">
                                <?php foreach ($data as $row)
                                {
                                    ?>
                                    <tr>
                                        <td><?php echo DateTime::createFromFormat('Y-m-d', $row->date)->format('d-m-Y'); ?></td>
                                        <td><?php
                                            echo str_replace(array(
                                                    "\r\n",
                                                    "\\r\\n",
                                                    "\\\\n",
                                                    "\\n",
                                                    "\n" ), " <br>", $row->description);
                                            ?></td>
                                        <td><?php echo $row->reference_no; ?></td>
                                        <td><?php echo $row->debit; ?></td>
                                        <td><?php echo $row->credit; ?></td>
                                        <td><?php echo $row->closing_balance; ?></td>
                                    </tr>
<?php } ?>
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
