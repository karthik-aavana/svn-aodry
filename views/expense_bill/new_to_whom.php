<div id="toWhom_model" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form role="form" id="towhomform">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4>Add </h4>
                </div>
                <div class="modal-body">
                    <div class="box-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="to_whom_name"> Name<span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="to_whom_name" name="to_whom_name" value="<?php echo set_value('to_whom_name'); ?>">
                                    <span class="validation-color" id="err_to_whom"><?php echo form_error('to_whom_name'); ?></span>
                                </div>
                                <div class="form-group hide">
                                    <input type="text" class="form-control" id="whom_hidden_id" name="whom_hidden_id" value="0">

                                </div>


                                <!-- /.form-group -->

                            </div>




                        </div>
                        <div class="col-sm-12">
                            <div class="box-footer">
                                <button type="submit" id="whom_submit" name="whom_submit" class="btn btn-info" data-dismiss="modal"><!-- Add --><?php echo $this->lang->line('subcategory_add'); ?></button>
                                <span class="btn btn-default" id="cancel" type="button" data-dismiss="modal"><!-- Cancel --><?php echo $this->lang->line('subcategory_cancel'); ?></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
    </div>
</form>
</div>
</div>





<script type="text/javascript">
    $(function ()
    {

        $("#to_whom_name").on("blur", function (event) {


            var to_whom = $('#to_whom_name').val();


            $.post("<?php echo site_url() ?>/ledger/to_whom_title_check",
                    {
                        name: to_whom
                    },
                    function (data) {

                        var response = data;
                        //console.log(response);
                        if (to_whom == null || to_whom == "")
                        {
                            $("#err_to_whom").text("Please Enter Name.");
                            return false;

                        }
                        if (response > 0)
                        {

                            $('#err_to_whom').text("Ledger Name Already Exists ");
                            $('#to_whom_hidden_id').val(response);
                        } else
                        {
                            $("#err_to_whom").text("");
                            enterLedger();

                        }
                    });

        });

    });

</script>
<script type="text/javascript">
    $("#whom_submit").click(function (event)
    {
        var whomName = $('#to_whom_name').val();

        if (whomName == null || whomName == "")
        {
            $("#err_to_whom").text("Please Enter Name.");

            return false;
        }

    });
</script>
<script type="text/javascript">

    function enterLedger()
    {
        var whom_name = $('#to_whom_name').val();


        $.ajax({

            url: '<?php echo base_url('expense_bill/add_to_whom') ?>',
            dataType: 'JSON',
            method: 'POST',
            data: {

                'to_whom_name': whom_name


            },
            success: function (result)
            {
                var data = result['data'];
                $('#to_ac').val(whom_name);
                $('#to_whom').html('');
                $('#to_whom').append('<option value="">Select</option>');
                for (i = 0; i < data.length; i++) {
                    if (data[i].type == "expense")
                    {
                        $('#to_whom').append('<option value="' + data[i].ledger_id + '">' + data[i].title + '</option>');
                    }
                }
                $('#to_whom').val(result['id']).attr("selected", "selected");
                $('#to_whom').change();
                $('#towhomform')[0].reset();
            }

        });
    }
</script>
