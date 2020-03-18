<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layout/header');

?>

<script type="text/javascript">

// function delete_id(id)

// {

//     if (confirm('Sure To Remove This Record ?'))

//     {

//         window.location.href = '<?php echo base_url('bank_account/delete/'); ?>' + id;

//     }

// }

</script>

<div class="content-wrapper">

    <section class="content-header">

        <h5>

            <ol class="breadcrumb">

                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>

                <li><a href="<?php echo base_url('bank_reconciliation'); ?>">Bank Reconciliation</a></li>

                <li class="active">View Vouchers</li>

            </ol>

        </h5>

    </section>

    <!-- Main content -->

    <section class="content">

        <div class="row">

            <!-- right column -->

            <div class="col-md-12">

                <div class="box">

                    <div class="box-header with-border">

                        <h3 class="box-title">View Vouchers</h3>

                        <span class="btn btn-sm btn-default pull-right" id="cancel" style="margin-left:1%" onclick="cancel('bank_reconciliation')">Back</span>

                    </div>

                    <div class="box-body">

                        <div class="row">

                            <div class="col-md-12">

                                <form action="<?php echo base_url('bank_reconciliation/view_vouchers'); ?>" role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8">

                                    <div class="well">

                                        <div class="row">

                                            <div class="col-sm-3">

                                                <div class="sales">

                                                    <div class="form-group">

                                                        <label for="Users">Users<span class="validation-color">*</span></label>

                                                        <select class="form-control select2" id="user_id" name="user_id" style="width: 100%;">

                                                            <!-- <option value="">Select</option> -->

                                                            <?php

                                                            foreach ($users as $key => $row)

                                                            {

                                                                ?>

                                                                <option value="<?= $row->id ?>" <?php if (isset($user_id) && $user_id == $row->id) echo "selected = 'selected' "; ?> ><?php echo $row->first_name . ' ' . $row->last_name; ?></option>

                                                                <?php

                                                            }

                                                            ?>

                                                        </select>

                                                        <span class="validation-color" id="err_user_id"><?php echo form_error('Users'); ?></span>

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-sm-3">

                                                <div class="sales">

                                                    <div class="form-group">

                                                        <label for="Categorized Type">Categorized Type<span class="validation-color">*</span></label>

                                                        <select class="form-control select2" id="categorized_type" name="categorized_type" style="width: 100%;">

                                                            <option value="unmatched" <?php if (isset($categorized_type) && $categorized_type == "unmatched") echo "selected = 'selected'"; ?>>Unmatched</option>

                                                            <option value="matched" <?php if (isset($categorized_type) && $categorized_type == "matched") echo "selected = 'selected'"; ?>>Matched</option>

                                                            <option value="all" <?php if (isset($categorized_type) && $categorized_type == "all") echo "selected = 'selected'"; ?>>All</option>



                                                        </select>

                                                        <span class="validation-color" id="err_bank_account"><?php echo form_error('Bank Account'); ?></span>

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="form-group">

                                                    <label></label>

                                                    <button type="submit" class="form-control btn btn-sm btn-info" id="search_submit">Search Voucher</button>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </form>

                            </div>



                            <?php

                            if (isset($categorized_type) && $categorized_type == "all")

                            {

                                ?>

                                <div class="box-body" id="rawdata">

                                    <div style='background:green;width:15px;height:15px;float:left;'>

                                    </div> Categorized Voucher<br>

                                    <div style='background:red;width:15px;height:15px;float:left;'>

                                    </div> Uncategorized Voucher<br>

                                    <table id="log_datatable" class="table table-bordered table-striped table-hover table-responsive">

                                        <thead>

                                            <tr>

                                                <th>Voucher Date</th>

                                                <th>Voucher Number</th>

                                                <th>Customer / Supplier</th>

                                                <!-- <th>Reference Number</th> -->

                                                <!-- <th>Amount</th> -->

                                                <th>Actual Amount</th>

                                                <th>Bank Statement Date</th>

                                                <th>Voucher Type</th>

                                            </tr>

                                        </thead>

                                        <tbody id="tbody_rawdata">

                                            <?php

                                            if (isset($receipt_voucher_data1))

                                            {

                                                foreach ($receipt_voucher_data1 as $key => $value)

                                                {

                                                    ?>

                                                    <tr <?php

                                                    if ($value->voucher_status == 1)

                                                    {

                                                        echo 'style="color: red"';

                                                    }

                                                    else if ($value->voucher_status == 2)

                                                    {

                                                        echo 'style="color: green"';

                                                    }

                                                    ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->customer_name ?></td>

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td><?= $value->date ?></td>

                                                        <?php

                                                        // if($value->voucher_status==1)

                                                        // {

                                                        //     echo '<td>Uncategorized</td>';

                                                        // }

                                                        // else if($value->voucher_status==2)

                                                        // {

                                                        echo '<td>Categorized</td>';

                                                        // }

                                                        ?>

                                                    </tr>

                                                    <?php

                                                }

                                            }

                                            ?>

                                            <?php

                                            if (isset($receipt_voucher_data1_1))

                                            {

                                                foreach ($receipt_voucher_data1_1 as $key => $value)

                                                {

                                                    ?>

                                                    <tr <?php

                                                    if ($value->voucher_status == 1)

                                                    {

                                                        echo 'style="color: red"';

                                                    }

                                                    else if ($value->voucher_status == 2)

                                                    {

                                                        echo 'style="color: green"';

                                                    }

                                                    ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->customer_name ?></td>

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td><?= $value->date ?></td>

                                                    <?php

                                                    echo '<td>Categorized</td>';

                                                    ?>

                                                    </tr>

                                                    <?php

                                                }

                                            }

                                            ?>

                                            <?php

                                            if (isset($advance_voucher_data1))

                                            {

                                                foreach ($advance_voucher_data1 as $key => $value)

                                                {

                                                    ?>

                                                    <tr <?php

                                                    if ($value->voucher_status == 1)

                                                    {

                                                        echo 'style="color: red"';

                                                    }

                                                    else if ($value->voucher_status == 2)

                                                    {

                                                        echo 'style="color: green"';

                                                    }

                                                    ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->customer_name ?></td>

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td><?= $value->date ?></td>

                                                        <?php

                                                        // if($value->voucher_status==1)

                                                        // {

                                                        //     echo '<td>Uncategorized</td>';

                                                        // }

                                                        // else if($value->voucher_status==2)

                                                        // {

                                                        echo '<td>Categorized</td>';

                                                        // }

                                                        ?>

                                                    </tr>

                                                    <?php

                                                }

                                            }

                                            ?>

                                            <?php

                                            if (isset($refund_voucher_data1))

                                            {

                                                foreach ($refund_voucher_data1 as $key => $value)

                                                {

                                                    ?>

                                                    <tr <?php

                                                        if ($value->voucher_status == 1)

                                                        {

                                                            echo 'style="color: red"';

                                                        }

                                                        else if ($value->voucher_status == 2)

                                                        {

                                                            echo 'style="color: green"';

                                                        }

                                                        ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->customer_name ?></td>

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td><?= $value->date ?></td>

                                                        <?php

                                                        // if($value->voucher_status==1)

                                                        // {

                                                        //     echo '<td>Uncategorized</td>';

                                                        // }

                                                        // else if($value->voucher_status==2)

                                                        // {

                                                        echo '<td>Categorized</td>';

                                                        // }

                                                        ?>

                                                    </tr>

                                                    <?php

                                                }

                                            }

                                            ?>

                                            <?php

                                            if (isset($payment_voucher_data1))

                                            {

                                                foreach ($payment_voucher_data1 as $key => $value)

                                                {

                                                    ?>

                                                    <tr <?php

                                            if ($value->voucher_status == 1)

                                            {

                                                echo 'style="color: red"';

                                            }

                                            else if ($value->voucher_status == 2)

                                            {

                                                echo 'style="color: green"';

                                            }

                                                    ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->supplier_name ?></td>

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td><?= $value->date ?></td>

                                                    <?php

                                                    // if($value->voucher_status==1)

                                                    // {

                                                    //     echo '<td>Uncategorized</td>';

                                                    // }

                                                    // else if($value->voucher_status==2)

                                                    // {

                                                    echo '<td>Categorized</td>';

                                                    // }

                                                    ?>

                                                    </tr>

                                                    <?php

                                                }

                                            }

                                            ?>

                                            <?php

                                            if (isset($payment_voucher_data1_1))

                                            {

                                                foreach ($payment_voucher_data1_1 as $key => $value)

                                                {

                                                    ?>

                                                    <tr <?php

                                                    if ($value->voucher_status == 1)

                                                    {

                                                        echo 'style="color: red"';

                                                    }

                                                    else if ($value->voucher_status == 2)

                                                    {

                                                        echo 'style="color: green"';

                                                    }

                                                    ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->supplier_name ?></td>

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td><?= $value->date ?></td>

                                                    <?php

                                                    echo '<td>Categorized</td>';

                                                    ?>

                                                    </tr>

                                                    <?php

                                                }

                                            }

                                            ?>

                                            <?php

                                                if (isset($contra_voucher_data1))

                                                {

                                                    foreach ($contra_voucher_data1 as $key => $value)

                                                    {

                                                        ?>

                                                    <tr <?php

                                                        if ($value->voucher_status == 1)

                                                        {

                                                            echo 'style="color: red"';

                                                        }

                                                        else if ($value->voucher_status == 2)

                                                        {

                                                            echo 'style="color: green"';

                                                        }

                                                        ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->ledger_title ?></td>

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td><?= $value->date ?></td>

                                                    <?php

                                                    // if($value->voucher_status==1)

                                                    // {

                                                    //     echo '<td>Uncategorized</td>';

                                                    // }

                                                    // else if($value->voucher_status==2)

                                                    // {

                                                    echo '<td>Categorized</td>';

                                                    // }

                                                    ?>

                                                    </tr>

                                                    <?php

                                                }

                                            }

                                            ?>

    <?php

    if (isset($receipt_voucher_data))

    {

        foreach ($receipt_voucher_data as $key => $value)

        {

            ?>

                                                    <tr <?php

                                                        if ($value->voucher_status == 1)

                                                        {

                                                            echo 'style="color: red"';

                                                        }

                                                        else if ($value->voucher_status == 2)

                                                        {

                                                            echo 'style="color: green"';

                                                        }

                                                        ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->customer_name ?></td>

                                                        <!-- <td><?= $value->reference_number ?></td> -->

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td> -- </td>

                                                        <!-- <td><a href="" title="Move to categorized" data-voucher_id="<?= $value->receipt_id ?>" data-voucher_type="receipt_voucher" data-voucher_amount="<?= $value->currency_converted_amount ?>" data-toggle="modal" data-target="#categorized_voucher" class="categorized_voucher"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td> -->

                                                    <?php

                                                    if ($value->voucher_status == 1)

                                                    {

                                                        echo '<td>Uncategorized</td>';

                                                    }

                                                    else if ($value->voucher_status == 2)

                                                    {

                                                        echo '<td>Categorized</td>';

                                                    }

                                                    ?>

                                                    </tr>

                                                    <?php

                                                    }

                                                }

                                                ?>

    <?php

    if (isset($receipt_voucher_data_1))

    {

        foreach ($receipt_voucher_data_1 as $key => $value)

        {

            ?>

                                                    <tr <?php

                                                        if ($value->voucher_status == 1)

                                                        {

                                                            echo 'style="color: red"';

                                                        }

                                                        else if ($value->voucher_status == 2)

                                                        {

                                                            echo 'style="color: green"';

                                                        }

                                                        ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->customer_name ?></td>

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td> -- </td>

                                                    <?php

                                                    if ($value->voucher_status == 1)

                                                    {

                                                        echo '<td>Uncategorized</td>';

                                                    }

                                                    else if ($value->voucher_status == 2)

                                                    {

                                                        echo '<td>Categorized</td>';

                                                    }

                                                    ?>

                                                    </tr>

            <?php

        }

    }

    ?>

    <?php

    if (isset($advance_voucher_data))

    {

        foreach ($advance_voucher_data as $key => $value)

        {

            ?>

                                                    <tr <?php

                                                        if ($value->voucher_status == 1)

                                                        {

                                                            echo 'style="color: red"';

                                                        }

                                                        else if ($value->voucher_status == 2)

                                                        {

                                                            echo 'style="color: green"';

                                                        }

                                                        ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->customer_name ?></td>

                                                        <!-- <td><?= $value->reference_number ?></td> -->

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td> -- </td>

                                                        <!-- <td><a href="" title="Move to categorized" data-voucher_id="<?= $value->advance_id ?>" data-voucher_type="advance_voucher" data-voucher_amount="<?= $value->currency_converted_amount ?>" data-toggle="modal" data-target="#categorized_voucher" class="categorized_voucher"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td> -->

                                                    <?php

                                                    if ($value->voucher_status == 1)

                                                    {

                                                        echo '<td>Uncategorized</td>';

                                                    }

                                                    else if ($value->voucher_status == 2)

                                                    {

                                                        echo '<td>Categorized</td>';

                                                    }

                                                    ?>

                                                    </tr>

            <?php

        }

    }

    ?>

                                                <?php

                                                if (isset($refund_voucher_data))

                                                {

                                                    foreach ($refund_voucher_data as $key => $value)

                                                    {

                                                        ?>

                                                    <tr <?php

                                                        if ($value->voucher_status == 1)

                                                        {

                                                            echo 'style="color: red"';

                                                        }

                                                        else if ($value->voucher_status == 2)

                                                        {

                                                            echo 'style="color: green"';

                                                        }

                                                        ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->customer_name ?></td>

                                                        <!-- <td><?= $value->reference_number ?></td> -->

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td> -- </td>

                                                        <!-- <td><a href="" title="Move to categorized" data-voucher_id="<?= $value->refund_id ?>" data-voucher_type="refund_voucher" data-voucher_amount="<?= $value->currency_converted_amount ?>" data-toggle="modal" data-target="#categorized_voucher" class="categorized_voucher"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td> -->

                                                    <?php

                                                    if ($value->voucher_status == 1)

                                                    {

                                                        echo '<td>Uncategorized</td>';

                                                    }

                                                    else if ($value->voucher_status == 2)

                                                    {

                                                        echo '<td>Categorized</td>';

                                                    }

                                                    ?>

                                                    </tr>

            <?php

        }

    }

    ?>

                                                <?php

                                                if (isset($payment_voucher_data))

                                                {

                                                    foreach ($payment_voucher_data as $key => $value)

                                                    {

                                                        ?>

                                                    <tr <?php

                                                        if ($value->voucher_status == 1)

                                                        {

                                                            echo 'style="color: red"';

                                                        }

                                                        else if ($value->voucher_status == 2)

                                                        {

                                                            echo 'style="color: green"';

                                                        }

                                                        ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->supplier_name ?></td>

                                                        <!-- <td><?= $value->reference_number ?></td> -->

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td> -- </td>

                                                        <!-- <td><a href="" title="Move to categorized" data-voucher_id="<?= $value->payment_id ?>" data-voucher_type="payment_voucher" data-voucher_amount="<?= $value->currency_converted_amount ?>" data-toggle="modal" data-target="#categorized_voucher" class="categorized_voucher"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td> -->

                                                    <?php

                                                    if ($value->voucher_status == 1)

                                                    {

                                                        echo '<td>Uncategorized</td>';

                                                    }

                                                    else if ($value->voucher_status == 2)

                                                    {

                                                        echo '<td>Categorized</td>';

                                                    }

                                                    ?>

                                                    </tr>

                                                        <?php

                                                    }

                                                }

                                                ?>

                                                <?php

                                                if (isset($payment_voucher_data_1))

                                                {

                                                    foreach ($payment_voucher_data_1 as $key => $value)

                                                    {

                                                        ?>

                                                    <tr <?php

                                                    if ($value->voucher_status == 1)

                                                    {

                                                        echo 'style="color: red"';

                                                    }

                                                    else if ($value->voucher_status == 2)

                                                    {

                                                        echo 'style="color: green"';

                                                    }

                                                    ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->supplier_name ?></td>

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td> -- </td>

                                                    <?php

                                                    if ($value->voucher_status == 1)

                                                    {

                                                        echo '<td>Uncategorized</td>';

                                                    }

                                                    else if ($value->voucher_status == 2)

                                                    {

                                                        echo '<td>Categorized</td>';

                                                    }

                                                    ?>

                                                    </tr>

                                                        <?php

                                                    }

                                                }

                                                ?>

                                                <?php

                                                if (isset($contra_voucher_data))

                                                {

                                                    foreach ($contra_voucher_data as $key => $value)

                                                    {

                                                        ?>

                                                    <tr <?php

                                                    if ($value->voucher_status == 1)

                                                    {

                                                        echo 'style="color: red"';

                                                    }

                                                    else if ($value->voucher_status == 2)

                                                    {

                                                        echo 'style="color: green"';

                                                    }

                                                    ?>>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->ledger_title ?></td>

                                                        <!-- <td><?= $value->reference_number ?></td> -->

                                                        <td><?= $value->currency_converted_amount ?></td>

                                                        <td> -- </td>

                                                        <!-- <td><a href="" title="Move to categorized" data-voucher_id="<?= $value->payment_id ?>" data-voucher_type="payment_voucher" data-voucher_amount="<?= $value->currency_converted_amount ?>" data-toggle="modal" data-target="#categorized_voucher" class="categorized_voucher"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td> -->

            <?php

            if ($value->voucher_status == 1)

            {

                echo '<td>Uncategorized</td>';

            }

            else if ($value->voucher_status == 2)

            {

                echo '<td>Categorized</td>';

            }

            ?>

                                                    </tr>

                                                    <?php

                                                }

                                            }

                                            ?>

                                        </tbody>

                                    </table>

                                </div>

    <?php

}

else

{

    ?>

                                <div class="box-body" id="rawdata">

                                    <table id="log_datatable" class="table table-bordered table-striped table-hover table-responsive">

                                        <thead>

                                            <tr>

                                                <th>Voucher Date</th>

                                                <th>Voucher Number</th>

                                                <th>Customer / Supplier</th>

                                                <!-- <th>Reference Number</th> -->

                                                <!-- <th>Amount</th> -->

                                                <th>Actual Amount</th>

                                            <?php if (isset($categorized_type) && $categorized_type == "matched")

                                            {

                                                ?> <th>Bank Statement Date</th> <th>Action</th> <?php } ?>

                                            </tr>

                                        </thead>

                                        <tbody id="tbody_rawdata">

                                            <?php

                                            if (isset($receipt_voucher_data))

                                            {

                                                foreach ($receipt_voucher_data as $key => $value)

                                                {

                                                    ?>

                                                    <tr>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->customer_name ?></td>

                                                        <!-- <td><?= $value->reference_number ?></td> -->

                                                        <td><?= $value->currency_converted_amount ?></td>



            <?php if (isset($categorized_type) && $categorized_type == "matched")

            {

                ?>

                                                            <td><?= $value->date ?></td>

                                                            <td>

                                                                <a href="" title="Remove Categorized" data-voucher_id="<?= $value->receipt_id ?>" data-voucher_type="receipt_voucher" data-toggle="modal" data-target="#remove_categorized" class="remove_categorized"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>

                                                            </td>

                                                    <?php } ?>

                                                    <!-- <a href="" title="Edit Ledger" data-voucher_id="<?= $value->receipt_id ?>" data-voucher_type="receipt_voucher" data-toggle="modal" data-target="#edit_ledger" class="edit_ledger"><i class="fa fa-pencil text-blue"></i></a> -->



                                                    </tr>

                                                    <?php

                                                }

                                            }

                                            ?>

    <?php

    if (isset($receipt_voucher_data_1))

    {

        foreach ($receipt_voucher_data_1 as $key => $value)

        {

            ?>

                                                    <tr>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->customer_name ?></td>

                                                        <td><?= $value->currency_converted_amount ?></td>



                                                        <?php if (isset($categorized_type) && $categorized_type == "matched")

                                                        {

                                                            ?>

                                                            <td><?= $value->date ?></td>

                                                            <td>

                                                                <a href="" title="Remove Categorized" data-voucher_id="<?= $value->receipt_id ?>" data-voucher_type="receipt_voucher" data-toggle="modal" data-target="#remove_categorized" class="remove_categorized"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>

                                                            </td>

                                                    <?php } ?>

                                                    </tr>

                                                    <?php

                                                }

                                            }

                                            ?>

                                            <?php

                                            if (isset($advance_voucher_data))

                                            {

                                                foreach ($advance_voucher_data as $key => $value)

                                                {

                                                    ?>

                                                    <tr>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->customer_name ?></td>

                                                        <!-- <td><?= $value->reference_number ?></td> -->

                                                        <td><?= $value->currency_converted_amount ?></td>



            <?php if (isset($categorized_type) && $categorized_type == "matched")

            {

                ?>

                                                            <td><?= $value->date ?></td>

                                                            <td>

                                                                <a href="" title="Remove Categorized" data-voucher_id="<?= $value->advance_id ?>" data-voucher_type="advance_voucher" data-toggle="modal" data-target="#remove_categorized" class="remove_categorized"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>

                                                            </td>

                                                    <?php } ?>

                                                    <!-- <a href="" title="Edit Ledger" data-voucher_id="<?= $value->receipt_id ?>" data-voucher_type="receipt_voucher" data-toggle="modal" data-target="#edit_ledger" class="edit_ledger"><i class="fa fa-pencil text-blue"></i></a> -->



                                                    </tr>

                                                    <?php

                                                }

                                            }

                                            ?>

                                            <?php

                                            if (isset($refund_voucher_data))

                                            {

                                                foreach ($refund_voucher_data as $key => $value)

                                                {

                                                    ?>

                                                    <tr>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->customer_name ?></td>

                                                        <!-- <td><?= $value->reference_number ?></td> -->

                                                        <td><?= $value->currency_converted_amount ?></td>



                                                        <?php if (isset($categorized_type) && $categorized_type == "matched")

                                                        {

                                                            ?>

                                                            <td><?= $value->date ?></td>

                                                            <td>

                                                                <a href="" title="Remove Categorized" data-voucher_id="<?= $value->refund_id ?>" data-voucher_type="refund_voucher" data-toggle="modal" data-target="#remove_categorized" class="remove_categorized"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>

                                                            </td>

                                                    <?php } ?>

                                                    <!-- <a href="" title="Edit Ledger" data-voucher_id="<?= $value->receipt_id ?>" data-voucher_type="receipt_voucher" data-toggle="modal" data-target="#edit_ledger" class="edit_ledger"><i class="fa fa-pencil text-blue"></i></a> -->



                                                    </tr>

                                                    <?php

                                                }

                                            }

                                            ?>

    <?php

    if (isset($payment_voucher_data))

    {

        foreach ($payment_voucher_data as $key => $value)

        {

            ?>

                                                    <tr>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->supplier_name ?></td>

                                                        <!-- <td><?= $value->reference_number ?></td> -->

                                                        <td><?= $value->currency_converted_amount ?></td>



                                                    <?php if (isset($categorized_type) && $categorized_type == "matched")

                                                    {

                                                        ?>

                                                            <td><?= $value->date ?></td>

                                                            <td>

                                                                <a href="" title="Remove Categorized" data-voucher_id="<?= $value->payment_id ?>" data-voucher_type="payment_voucher" data-toggle="modal" data-target="#remove_categorized" class="remove_categorized"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>

                                                            </td>

                                                    <?php } ?>

                                                    <!-- <a href="" title="Edit Ledger" data-voucher_id="<?= $value->receipt_id ?>" data-voucher_type="receipt_voucher" data-toggle="modal" data-target="#edit_ledger" class="edit_ledger"><i class="fa fa-pencil text-blue"></i></a> -->



                                                    </tr>

            <?php

        }

    }

    ?>

    <?php

    if (isset($payment_voucher_data_1))

    {

        foreach ($payment_voucher_data_1 as $key => $value)

        {

            ?>

                                                    <tr>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->supplier_name ?></td>

                                                        <td><?= $value->currency_converted_amount ?></td>



                                                    <?php if (isset($categorized_type) && $categorized_type == "matched")

                                                    {

                                                        ?>

                                                            <td><?= $value->date ?></td>

                                                            <td>

                                                                <a href="" title="Remove Categorized" data-voucher_id="<?= $value->payment_id ?>" data-voucher_type="payment_voucher" data-toggle="modal" data-target="#remove_categorized" class="remove_categorized"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>

                                                            </td>

            <?php } ?>

                                                    </tr>

                                    <?php

                                }

                            }

                            ?>

    <?php

    if (isset($contra_voucher_data))

    {

        foreach ($contra_voucher_data as $key => $value)

        {

            ?>

                                                    <tr>

                                                        <td><?= $value->voucher_date ?></td>

                                                        <td><?= $value->voucher_number ?></td>

                                                        <td><?= $value->ledger_title ?></td>

                                                        <!-- <td><?= $value->reference_number ?></td> -->

                                                        <td><?= $value->currency_converted_amount ?></td>



            <?php if (isset($categorized_type) && $categorized_type == "matched")

            {

                ?>

                                                            <td><?= $value->date ?></td>

                                                            <td>

                                                                <a href="" title="Remove Categorized" data-voucher_id="<?= $value->contra_voucher_id ?>" data-voucher_type="contra_voucher" data-toggle="modal" data-target="#remove_categorized" class="remove_categorized"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>

                                                            </td>

            <?php } ?>

                                                    <!-- <a href="" title="Edit Ledger" data-voucher_id="<?= $value->receipt_id ?>" data-voucher_type="receipt_voucher" data-toggle="modal" data-target="#edit_ledger" class="edit_ledger"><i class="fa fa-pencil text-blue"></i></a> -->



                                                    </tr>

            <?php

        }

    }

    ?>

                                        </tbody>

                                    </table>

                                </div>

                            </div>

<?php } ?>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>

<?php

$this->load->view('layout/footer');

$this->session->set_userdata('advance_search', 'false');

?>

<div id="remove_categorized" class="modal fade" role="dialog" data-backdrop="static">

    <div class="modal-dialog">

        <!-- Modal content-->

        <div class="modal-content">

            <form action="<?php echo base_url('bank_reconciliation/remove_categorized'); ?>" role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8">

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal">x</button>

                    <h4 class="modal-title" style="float: left;">Remove Categorization</h4>

                </div>

                <div class="modal-body">

                    <div class="form-group">

                        <label>Are you sure! You want to uncategorized this statement ?</label>

                        <input type="hidden" id="voucher_id" name="voucher_id" value="">

                        <input type="hidden" id="voucher_type" name="voucher_type" value="">

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>

                    <button id="remove_submit" type="submit" class="btn btn-primary">Yes</button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- <div id="edit_ledger" class="modal fade" role="dialog" data-backdrop="static">

    <div class="modal-dialog">

        <div class="modal-content">

            <form action="<?php echo base_url('bank_reconciliation/edit_ledger'); ?>" role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8">

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal">x</button>

                    <h4 class="modal-title" style="float: left;">Edit Ledger</h4>

                </div>

                <div class="modal-body">

                    <div class="form-group">

                        <select class="form-control select2" id="ledger" name="ledger">

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <input type="hidden" id="voucher_id" name="voucher_id" value="">

                    <input type="hidden" id="voucher_type" name="voucher_type" value="">

                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>

                    <button id="ledger_submit" type="submit" class="btn btn-primary">Update</button>

                </div>

            </form>

        </div>

    </div>

</div> -->

<!-- <script src="<?php echo base_url(); ?>assets/dist/js/jquery.backstretch.min.js"></script>

<script src="<?php echo base_url(); ?>assets/dist/js/scripts.js"></script> -->

<?php if (isset($categorized_type) && $categorized_type == "matched")

{

    ?>

    <script type="text/javascript">

        $(document).ready(function () {

            $("#log_datatable").dataTable().fnDestroy();

            $('#log_datatable').dataTable({

                // "aLengthMenu": [

                //     [50, 100, 150, -1],

                //     [50, 100, 150, "All"]

                // ],

                lengthMenu: [

                    [10, 50, 100, -1],

                    ['10 rows', '50 rows', '100 rows', 'Show all']

                ],

                dom: 'Bfrtip',

                buttons: [

                    {extend: 'csv',

                        exportOptions: {

                            columns: 'th:not(:last-child)'

                        }

                    },

                    {extend: 'excel',

                        exportOptions: {

                            columns: 'th:not(:last-child)'

                        }

                    },

                    {extend: 'pdf',

                        exportOptions: {

                            columns: 'th:not(:last-child)'

                        }

                    }

                    // 'csv', 'excel', 'pdf'

                ]

            });

        });

    </script>

<?php

}

else

{

    ?>

    <script type="text/javascript">

        $(document).ready(function () {

            $("#log_datatable").dataTable().fnDestroy();

            $('#log_datatable').dataTable({

                // "aLengthMenu": [

                //     [50, 100, 150, -1],

                //     [50, 100, 150, "All"]

                // ],

                lengthMenu: [

                    [10, 50, 100, -1],

                    ['10 rows', '50 rows', '100 rows', 'Show all']

                ],

                dom: 'Blfrtip',

                buttons: [

                    'csv', 'excel', 'pdf'

                ]

            });

        });

    </script>

<?php } ?>

<script type="text/javascript">

    $(document).ready(function () {

        $("#search_submit").click(function () {

            var user_id = $('#user_id').val();

            var categorized_type = $('#categorized_type').val();

            if (user_id == "" || user_id == null) {

                $('#err_user_id').text("Please Select Users");

                return false;

            } else {

                $('#err_user_id').text('');

            }

            if (categorized_type == "" || categorized_type == null) {

                $('#err_bank_account').text("Please Select Categorized Type");

                return false;

            } else {

                $('#err_bank_account').text('');

            }

        });

        $(".remove_categorized").click(function () {

            document.getElementById("voucher_id").value = $(this).data('voucher_id');

            document.getElementById("voucher_type").value = $(this).data('voucher_type');

        });

// $(".edit_ledger").click(function(){

//     $.ajax({

//         type:"POST",

//         dataType: 'json',

//         url:"<?php echo base_url('bank_reconciliation/get_ledger/') ?>",

//         data:

//         {

//             voucher_id   : $(this).data('voucher_id'),

//             voucher_type : $(this).data('voucher_type')

//         },

//         success: function(data)

//         {

//         }

//     });

// });

// $('#remove_submit').click(function(event){

//     $.ajax({

//         type:"POST",

//         dataType: 'json',

//         url:"<?php echo base_url('bank_reconciliation/remove_categorized/') ?>",

//         data:

//         {

//             voucher_id   : $("#voucher_id").val(),

//             voucher_type : $("#voucher_type").val()

//         },

//         success: function(data) {

//         }

//     });

// });

    });

</script>

