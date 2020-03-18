<div class="col-sm-12" hidden="true">
    <div class="well">
        <div class="row">
            <div class="box-header with-border accounts" style="margin-top: -25px;padding: 0px 30px;">
                <h3 class="box-title">Accounts Details</h3>
                <span class="pull-right-container">
                    <i class="glyphicon glyphicon-chevron-right pull-right"  style="margin-top: 18px;"></i>
                </span>
            </div>
            <div class="box-body accounts-data">

                <div class="well">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>From Account</label>
                                <?php
                                if ($module_id == $this->config->item('sales_module'))
                                {
                                    ?>
                                    <select class="form-control" id="from_account" name="from_account">
                                        <option value="customer" <?php
                                        if (isset($data[0]->from_account) && $data[0]->from_account == "customer")
                                        {
                                            echo "selected";
                                        }
                                        ?>>Customer</option>


                                    </select>

                                    <?php
                                }
                                else if ($module_id == $this->config->item('purchase_module'))
                                {
                                    ?>
                                    <select class="form-control" id="from_account" name="from_account">
                                        <option value="purchase" <?php
                                                if (isset($data[0]->from_account) && $data[0]->from_account == "purchase")
                                                {
                                                    echo "selected";
                                                }
                                                ?>>Purchase</option>


                                    </select>


                                    <?php
                                }
                                else if ($module_id == $this->config->item('credit_note_module'))
                                {
                                    ?>
                                    <select class="form-control" id="from_account" name="from_account">
                                        <option value="customer" <?php
                                    if (isset($data[0]->from_account) && $data[0]->from_account == "customer")
                                    {
                                        echo "selected";
                                    }
                                    ?>>Customer</option>

                                    </select>

                                        <?php
                                    }
                                    else if ($module_id == $this->config->item('debit_note_module'))
                                    {
                                        ?>
                                    <select class="form-control" id="from_account" name="from_account">
                                        <option value="sales" <?php
                                        if (isset($data[0]->from_account) && $data[0]->from_account == "sales")
                                        {
                                            echo "selected";
                                        }
                                        ?>>Sales</option>


                                    </select>

                                        <?php
                                            }
                                            else if ($module_id == $this->config->item('purchase_credit_note_module'))
                                            {
                                                ?>
                                    <select class="form-control" id="from_account" name="from_account">
                                        <option value="purchase" <?php
                                    if (isset($data[0]->from_account) && $data[0]->from_account == "purchase")
                                    {
                                        echo "selected";
                                    }
                                    ?>>Purchase</option>


                                    </select>

    <?php
}
else if ($module_id == $this->config->item('purchase_debit_note_module'))
{
    ?>
                                    <select class="form-control" id="from_account" name="from_account">
                                        <option value="supplier" <?php
                                if (isset($data[0]->from_account) && $data[0]->from_account == "supplier")
                                {
                                    echo "selected";
                                }
                                ?>>Supplier</option>


                                    </select>

                                <?php } ?>

                                <span class="validation-color" id="err_from_account"></span>

                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>To Account</label>
                                    <?php
                                    if ($module_id == $this->config->item('sales_module'))
                                    {
                                        ?>
                                    <select class="form-control" id="to_account" name="to_account">
                                        <option value="sales" <?php
                                    if (isset($data[0]->to_account) && $data[0]->to_account == "sales")
                                    {
                                        echo "selected";
                                    }
                                    ?>>Sales</option>
                                        <!-- <option value="item" <?php
                                        if (isset($data[0]->to_account) && $data[0]->to_account == "item")
                                        {
                                            echo "selected";
                                        }
                                        ?>>Item slab</option> -->

                                    </select>

                                    <?php
                                }
                                else if ($module_id == $this->config->item('purchase_module'))
                                {
                                    ?>
                                    <select class="form-control" id="to_account" name="to_account">
                                        <option value="supplier" <?php
                                if (isset($data[0]->to_account) && $data[0]->to_account == "supplier")
                                {
                                    echo "selected";
                                }
                                    ?>>Supplier</option>
                                        <!-- <option value="item" <?php
                                        if (isset($data[0]->to_account) && $data[0]->to_account == "item")
                                        {
                                            echo "selected";
                                        }
                                        ?>>Item slab</option> -->

                                    </select>

                                    <?php
                                }
                                else if ($module_id == $this->config->item('credit_note_module'))
                                {
                                    ?>
                                    <select class="form-control" id="to_account" name="to_account">
                                        <option value="sales" <?php
                                    if (isset($data[0]->to_account) && $data[0]->to_account == "sales")
                                    {
                                        echo "selected";
                                    }
                                    ?>>Sales</option>
                                        <!-- <option value="item" <?php
                                    if (isset($data[0]->to_account) && $data[0]->to_account == "item")
                                    {
                                        echo "selected";
                                    }
                                    ?>>Item slab</option> -->

                                    </select>

                                        <?php
                                    }
                                    else if ($module_id == $this->config->item('debit_note_module'))
                                    {
                                        ?>
                                    <select class="form-control" id="to_account" name="to_account">
                                        <option value="customer" <?php
                                    if (isset($data[0]->to_account) && $data[0]->to_account == "customer")
                                    {
                                        echo "selected";
                                    }
                                        ?>>Customer</option>
                                        <!-- <option value="item" <?php
                                    if (isset($data[0]->to_account) && $data[0]->to_account == "item")
                                    {
                                        echo "selected";
                                    }
                                    ?>>Item slab</option> -->

                                    </select>

                                        <?php
                                    }
                                    else if ($module_id == $this->config->item('purchase_credit_note_module'))
                                    {
                                        ?>
                                    <select class="form-control" id="to_account" name="to_account">
                                        <option value="supplier" <?php
                                        if (isset($data[0]->to_account) && $data[0]->to_account == "supplier")
                                        {
                                            echo "selected";
                                        }
                                        ?>>Supplier</option>
                                        <!-- <option value="item" <?php
                                        if (isset($data[0]->to_account) && $data[0]->to_account == "item")
                                        {
                                            echo "selected";
                                        }
                                        ?>>Item slab</option> -->

                                    </select>

    <?php
}
else if ($module_id == $this->config->item('purchase_debit_note_module'))
{
    ?>
                                    <select class="form-control" id="to_account" name="to_account">
                                        <option value="purchase" <?php
    if (isset($data[0]->to_account) && $data[0]->to_account == "purchase")
    {
        echo "selected";
    }
    ?>>Purchase</option>
                                        <!-- <option value="item" <?php
    if (isset($data[0]->to_account) && $data[0]->to_account == "item")
    {
        echo "selected";
    }
    ?>>Item slab</option> -->

                                    </select>

<?php } ?>

                                <span class="validation-color" id="err_to_account"></span>

                            </div>
                        </div>



                    </div>


                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $('.accounts').click(function () {
        $('.accounts-data').toggle('slow');
    }).trigger('click');
</script>
