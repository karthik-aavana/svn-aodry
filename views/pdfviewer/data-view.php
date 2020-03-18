<?php if (isset($dataarr) && isset($meta_keys))
{
    ?>

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
                            <form action="<?php echo base_url('bank_statement/categorized_bank_statement'); ?>"  role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                                <?php if ($dataarr && !isset($contain_files))
                                {
                                    ?>
                                    <button type="submit" id="categorized" name="categorized" class="btn btn-sm btn-info pull-right">Categorized</button>
                                <?php } ?>
    <?php if (isset($contain_files))
    {
        ?>
                                    <!-- <a href="<?php echo base_url('bank_statement/delete_file_data/') . $file_id . '/' . $contain_files; ?>" title="Delete" class="btn btn-danger pull-right">Delete</a> -->
                                    <a href="" data-target="#delete_file_data" data-toggle="modal" title="Delete" class="btn btn-danger pull-right">Delete</a>
                                        <?php } ?>
                                <table id="log_datatable1" class="table table-bordered table-striped table-hover table-responsive">
                                    <thead>
                                        <!-- <?php if ($dataarr && !$contain_files)
                                        {
                                            ?>
                                                <tr>
                                            <?php
                                            $i            = 1;
                                            $date_display = 0;
                                            $chq_display  = 0;

                                            foreach ($meta_keys as $key)
                                            {
                                                if (preg_match('/[Cc][Hh][Ee]?[Qq]/', trim($key)))
                                                {
                                                    $chq_display = 1;
                                                }
                                            }
                                            foreach ($meta_keys as $key)
                                            {
                                                if (preg_match('/[dD]ate/', trim($key)))
                                                {
                                                    $date_display = 1;
                                                }
                                                if ($date_display != 0)
                                                {
                                                    ?>
                                                                    <td>
                                                                        <select class="form-control" data-id="<?php echo $i; ?>" id="bank<?php echo $i; ?>" name="bank<?php echo $i; ?>">
                                                                            <option value="">Select</option>
                                                                            <option id="date" value="date">Date</option>
                                                                            <option id="description" value="description">Particular/Narration</option>
                <?php if ($chq_display != 0)
                {
                    ?>
                                                                                    <option id="reference_no" value="reference_no">Cheque No</option>
                <?php } ?>
                                                                            <option id="debit" value="debit">Withdrawal(DR)</option>
                                                                            <option id="credit" value="credit">Deposit(CR)</option>
                                                                            <option id="closing_balance" value="closing_balance">Closing Balance</option>
                                                                            <option id="dr/cr" value="dr/cr">DR/CR</option>
                                                                            <option id="amount" value="amount">Amount</option>
                                                                        </select>
                <?php $i++; ?>
                                                                    </td>
                                                <?php }
                                            }
                                            ?>

                                                    <input type="hidden" id="ivalue" name="ivalue" value="<?php echo $i; ?>">

                                                </tr>
                                    <?php } ?> -->
                                        <tr>
                                    <input type="hidden" id="file_id" name="file_id" value="<?php echo $file_id; ?>">
                                    <!-- <?php
                                    $status       = 0;
                                    $count        = 0;
                                    $date_display = 0;
                                    foreach ($meta_keys as $key)
                                    {

                                        if (preg_match('/[dD]ate/', trim($key)))
                                        {
                                            $date_display = 1;
                                        }
                                        if ($date_display != 0)
                                        {
                                            if ($status == 0)
                                            {
                                                echo '<th><strong>Date</strong></th>';

                                                $data[$count] = $key;
                                                $count++;
                                                $status       = 1;
                                            }
                                            else
                                            {
                                                ?>

                                                                <th>
                                                                    <strong>
                                                <?php
                                                echo $key;
                                                ?>
                                                                    </strong>
                                                                </th>
                                                <?php
                                                $data[$count] = $key;
                                                $count++;
                                            }
                                        }
                                    }
                                    ?> -->
                                    <?php
                                    foreach ($meta_keys as $key)
                                    {
                                        if ($key == 'reference_no')
                                        {
                                            ?>
                                            <th><strong><?php echo strtoupper('cheque no'); ?></strong></th>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <th><strong><?php echo strtoupper($key); ?></strong></th>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </tr>
                                    </thead>
                                    <tbody  id="tbody_rawdata">
                                        <?php
                                        foreach ($dataarr as $row)
                                        {
                                            echo "<tr>";
                                            foreach ($row as $val)
                                            {
                                                // if(preg_match('/[0-9]{0,2}\.[0-9]{0,2}\.[0-9]{2,4}/',trim($val),$matches))
                                                // {
                                                //     $val=$matches[0];
                                                // }
                                                // elseif(preg_match('/[0-9]{0,2}\-[0-9]{0,2}\-[0-9]{2,4}/',trim($val),$matches))
                                                // {
                                                //     $val=$matches[0];
                                                // }
                                                // elseif(preg_match('/[0-9]{0,2}\/[0-9]{0,2}\/[0-9]{2,4}/',trim($val),$matches))
                                                // {
                                                //     $val=$matches[0];
                                                // }
                                                // elseif(preg_match('/[0-9]{0,2}\s[0-9]{0,2}\s[0-9]{2,4}/',trim($val),$matches))
                                                // {
                                                //     $val=$matches[0];
                                                // }
                                                // elseif(preg_match('/[0-9]{0,2}[\.\-\/\s]([Jj][Aa][Nn]|[Ff][Ee][Bb]|[Mm][Aa][Rr]|[Aa][Pp][Rr]|[Mm][Aa][Yy]|[Jj][Uu][Nn]|[Jj][Uu][Ll]|[Aa][Uu][Gg]|[Ss][Ee][Pp]|[Oo][Cc][Tt]|[Nn][Oo][Vv]|[Dd][Ee][Cc])[\.\-\/\s][0-9]{2,4}/',trim($val),$matches))
                                                // {
                                                //     $val=$matches[0];
                                                // }
                                                // elseif(preg_match('/[0-9]{0,2}[\.\-\/\s]([Jj][Aa][Nn][Uu][Aa][Rr][Yy]|[Ff][Ee][Bb][Rr][Uu][Aa][Rr][Yy]|[Mm][Aa][Rr][Cc][Hh]|[Aa][Pp][Rr][Ii][Ll]|[Mm][Aa][Yy]|[Jj][Uu][Nn][Ee]|[Jj][Uu][Ll][Yy]|[Aa][Uu][Gg][Uu][Ss][Tt]|[Ss][Ee][Pp][Tt][Ee][Mm][Bb][Ee][Rr]|[Oo][Cc][Tt][Oo][Bb][Ee][Rr]|[Nn][Oo][Vv][Ee][Mm][Bb][Ee][Rr]|[Dd][Ee][Cc][Ee][Mm][Bb][Ee][Rr])[\.\-\/\s][0-9]{2,4}/',trim($val),$matches))
                                                // {
                                                //     $val=$matches[0];
                                                // }
                                                echo "<td>" . str_replace(array(
                                                        "\r\n",
                                                        "\\r\\n",
                                                        "\\n",
                                                        "\n" ), " <br>", trim($val)) . "</td>";
                                                // echo "<td>".trim($val)."</td>";
                                            }
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
}
$json = json_encode($dataarr);
$this->session->set_userdata('categorized_content', $json);

$json2 = json_encode($meta_keys);
$this->session->set_userdata('content_select', $json2);
?>

<div id="delete_file_data" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title pull-left">Delete Statement</h4>
                <button type="button" class="close pull-right" data-dismiss="modal">x</button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label>Are you sure !</label>
                    <label>You want to delete this statement ?</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                <a href="<?php echo base_url('bank_statement/delete_file_data/') . $file_id . '/' . $contain_files; ?>" title="Delete" class="btn btn-primary pull-right">Yes</a>
            </div>
        </div>

    </div>
</div>

<?php
$this->load->view('layout/footer');
?>
