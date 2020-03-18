    <div id="expense_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form role="form" id="expenseCategory_a">
            <div class="modal-content">               
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Expense</h4>
                </div>
                <div class="modal-body">
                    <div id="loader">
                        <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="group_title"><!-- Supplier Name -->
                                    Expense Title
                                    <span class="validation-color">*</span>
                                </label>
                                <input type="hidden" name="ledger_id" id="ledger_id_a" value="0">
                                <input type="text" class="form-control" id="expense_name" name="expense_name">
                                <span class="validation-color" id="err_expense_name"></span>
                            </div>
                        </div>  
                        <div class="col-md-12 produc_hsn">
                            <div class="form-group">
                                <label for="product_hsn_sac_code">Expense HSN/SAC Code</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <a href="" data-toggle="modal" data-target="#hsn_modal" class="pull-right"><span class="fa fa-eye"></span></a>
                                    </div>
                                    <input type="text" class="form-control" id="product_hsn_sac_code" name="product_hsn_sac_code" value="" tabindex="11">
                                </div>
                                <span class="validation-color" id="err_product_hsn_sac_code"><?php echo form_error('product_hsn_sac_code'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="group_title"><!-- Supplier Name -->
                                    Expense Description
                                    <span class="validation-color"></span>
                                </label>
                                <textarea class="form-control" rows="3" id="expense_description" name="expense_description"></textarea>
                                <span class="validation-color" id="err_expense_description"></span>
                            </div>
                        </div>      
                    </div>
                    <!-- <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gst_tax">Tax Type(TDS)</label>
                                <select class="form-control select2" id="tds_tax" name="tds_tax" style="width: 100%;" tabindex="6">
                                    <option value="">Select Tax</option>
                    <?php
                    foreach ($tax_tds as $row) {
                        echo "<option value='$row->tax_id'" . set_select('gst_tax', $row->tax_id . "-" . $row->tax_value) . " per='" . $row->tax_value . "'>$row->tax_name@(" . $row->section_name . ")" . round($row->tax_value, 2) . "%</option>";
                    }
                    ?>
                                </select>
                                <span class="validation-color" id="err_product_code"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gst_tax">Tax Type(GST)</label>
                                
                                <select class="form-control select2" id="gst_tax" name="gst_tax" style="width: 100%;" tabindex="6">
                                    <option value="">Select Tax</option>
                    <?php
                    foreach ($tax_gst as $row) {
                        echo "<option value='$row->tax_id'" . set_select('gst_tax', $row->tax_id . "-" . $row->tax_value) . " per='" . $row->tax_value . "'>$row->tax_name@" . round($row->tax_value, 2) . "%</option>";
                    }
                    ?>
                                </select>
                                
                                <span class="validation-color" id="err_product_code"></span>
                            </div>
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="submit" id="expense_submit" class="btn btn-info btn-flat" class="close" data-dismiss="modal">Add</button>
                    <button type="submit" class="btn btn-info btn-flat" class="close" data-dismiss="modal">Cancel</button>
                </div>            
            </div>
        </form>
    </div>
</div>
<?php
/*$this->load->view('product/tds_modal');*/
?>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script>
$(document).on('click', '.expense_btn', function(){   
    $("#expense_name").val('');
    $("#expense_description").val('');
});
    $(document).ready(function () {
        var expense_name_exist = 0;
        $("#expense_submit").click(function (event){
            var expense_name = $('#expense_name').val();
            if (expense_name_exist == 1)
            {
                $("#err_expense_name").text(expense_name + " name already exists!");
                return !1;
            }
            if (expense_name == null || expense_name == "")
            {
                $("#err_expense_name").text("Please Enter Expense Title.");
                return !1;
            } else
            {
                $("#err_expense_name").text("");
            }
            if (!expense_name.match(general_regex))
            {
                $('#err_expense_name').text("Please Enter Valid Expense Name ");
                return !1;
            } else
            {
                $("#err_expense_name").text("");
            }
            var expense_description = $('#expense_description').val();
            /*'expense_name': $('#expense_name').val(),
             'expense_tds': $('#expense_tds').val(),
             'expense_tds_id': $('#tds_tax').val(),
             'expense_gst_id': $('#gst_tax').val(),
             'expense_gst_per': $('#gst_tax option:selected').attr('per'),
             'expense_tds_value': $('#tds_tax option:selected').attr('per')*/
            $.ajax({
                url: base_url + 'expense/add_expense_ajax',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'expense_name': $('#expense_name').val(),
                    'expense_hsn_code' : $('#product_hsn_sac_code').val(),
                    'expense_description': expense_description,
                    'expense_tds': 0,
                    'expense_tds_id': 0,
                    'expense_gst_id': 0,
                    'expense_gst_per': 0,
                    'expense_tds_value': 0
                },
                  beforeSend: function(){
                     // Show image container
                    $("#loader").show();
                    },
                success: function (result){
                    $("#loader").hide();
                    if(result.flag){
                        $('#expense_modal').modal('hide');
                        dTable.destroy();
                        alert_d.text = result.msg;
                        PNotify.success(alert_d);
                        dTable = GetAllExpences();
                    }else{
                        alert_d.text = result.msg;
                        PNotify.error(alert_d);
                    }
                    
                    /*setTimeout(function () {// wait for 5 secs(2)
                     location.reload(); // then reload the page.(3)
                     });*/
                },
                error: function(msg){
                     $("#loader").hide();
                    alert_d.text = 'Something Went Wrong';
                    PNotify.error(alert_d);
                }
            });
        });

        $("#expense_name").on("blur", function (event) {
            var expense_name = $('#expense_name').val();
            var ledger_id = $('#ledger_id_a').val();
            if (expense_name.length > 2)
            {
                if (expense_name == null || expense_name == "")
                {
                    $("#err_expense_name").text("Please Enter Expense Name.");
                    return !1;
                } else
                {
                    $("#err_expense_name").text("");
                }
                if (!expense_name.match(general_regex))
                {
                    $('#err_expense_name').text("Please Enter Valid Expense Name ");
                    return !1;
                } else
                {
                    $("#err_expense_name").text("");
                }
            } else
            {
                $("#err_expense_name").text("");
            }
            $.ajax({
                url: base_url + 'ledger/get_check_ledger',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'ledger_name': expense_name,
                    'ledger_id': ledger_id
                },
                success: function (result) {
                    if (result[0].num > 0)
                    {
                        $('#expense_name').val("");
                        $("#err_expense_name").text(expense_name + " name already exists!");
                    } else
                    {
                        $("#err_expense_name").text("");
                    }
                }
            });
        });

        $("#expense_name").on("keyup", function (event) {
            var expense_name = $('#expense_name').val();
            var ledger_id = $('#ledger_id_a').val();
            if (expense_name != '') {
                $.ajax({
                    url: base_url + 'ledger/get_check_ledger',
                    dataType: 'JSON',
                    method: 'POST',
                    data: {
                        'ledger_name': expense_name,
                        'ledger_id': ledger_id
                    },
                    success: function (result) {
                        if (result[0].num > 0)
                        {
                            $("#err_expense_name").text(expense_name + " name already exists!");
                            expense_name_exist = 1;
                        } else
                        {
                            $("#err_expense_name").text("");
                            expense_name_exist = 0;
                        }
                    }
                });
            }
        });

        $("#expense_tds").on("keyup", function (event) {
            var expense_tds = $('#expense_tds').val();
            if (expense_tds != "" || expense_tds != null)
            {
                if (!expense_tds.match(price_regex))
                {
                    $("#err_expense_tds").text("Please Enter Valid Tds.");
                    return !1;
                } else
                {
                    $("#err_expense_tds").text("");
                }
            }
        });
    });
</script>