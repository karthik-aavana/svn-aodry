
<div id="add_director_org_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>
                    Add <lable id="modal_type"></lable>
                </h4>
            </div>
            <div class="modal-body">
                <div class="control-group">
                    <div class="controls">
                        <div class="tabbable">
                            <div class="box-body">
                                <div class="row">
                                    <form id="director_org">
                                        <div class="col-md-12">

                                            <div class="form-group col-md-12">
                                                <label >
                                                    Name<span class="validation-color">*</span>
                                                </label>
                                                <input type="hidden" name="name_ledger_id" id="name_ledger_id" value="0">
                                                <input type="text"  class="form-control" id="name" name="name" >
                                                <span class="validation-color" id="err_name"></span>
                                            </div>

                                            <div class="form-group col-md-12">
                                                <label >
                                                    Type<span class="validation-color">*</span>
                                                </label>
                                                <input type="hidden" name="type_text" id="type_text">
                                                <select name="acc_sub_group" id="acc_sub_group" class="select2 form-control" style="width: 100%;">  </select>

                                                <span class="validation-color" id="err_acc_sub_group"></span>
                                            </div>

                                            <div class="form-group col-md-12">
                                                <label>
                                                    Mobile
                                                </label>

                                                <input type="text" name="dir_mobile" id="dir_mobile" class="form-control">
                                                <span class="validation-color" id="err_dir_mobile"></span>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label>
                                                    Address
                                                </label>

                                                <textarea class="form-control" id="dir_address" rows="2" name="dir_address"></textarea>
                                                <span class="validation-color" id="err_dir_address"></span>
                                            </div>
                                            <div class="form-group col-md-12" hidden="true" id="bank_acc">
                                                <label>
                                                    Bank Account Number
                                                </label>

                                                <input type="text" name="banck_acc_no" id="banck_acc_no" class="form-control">
                                                <span class="validation-color" id="err_banck_acc_no"></span>
                                            </div>
                                            <div class="form-group col-md-12" hidden="true" id="pan_no">
                                                <label>
                                                    Pan Number
                                                </label>

                                                <input type="text" name="pan_no" id="pan_no" class="form-control">
                                                <span class="validation-color" id="err_pan_no"></span>
                                            </div>



                                            <div class="col-sm-12">
                                                <div class="box-footer">
                                                    <button type="submit" class="btn btn-primary" id="modal_submit" data-dismiss="modal">Add</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                    </form>
                                </div>
                                <!-- /.box-body -->
                            </div>

                        </div>
                    </div>
                </div> <!-- /controls -->
            </div> <!-- /control-group -->
        </div>
    </div>
</div>

<script>
    $(document).ready(function ()
    {
        $("#purpose_of_transaction").change(function (event)
        {
            var purpose_of_transaction = $('#purpose_of_transaction').val();
            if (purpose_of_transaction == "Capital")
            {
                $("#modal_type").text("Director");
                // $("#bank_acc").hide();
                //  $("#pan_no").hide();
            } else
            {
                $("#modal_type").text("Organization/Person");
            }

            $.ajax(
                    {

                        url: base_url + 'account_sub_group/get_sub_group_data_ajax',
                        dataType: 'JSON',
                        method: 'POST',
                        data: {
                            "purpose": purpose_of_transaction

                        },
                        success: function (result)
                        {

                            var option = "<option value=''>Select</option>";
                            for (var i = 0; i < result.length; i++)
                            {
                                option += "<option value='" + result[i].account_subgroup_id + "'>" + result[i].subgroup_title + "</option>";
                            }
                            $('#acc_sub_group').html(option);
                        }
                    });
        });

        $("#acc_sub_group").change(function (event)
        {
            var type_text = $("option:selected", '#acc_sub_group').text();
            $("#type_text").val(type_text);
            if (type_text == "Organization")
            {
                $("#bank_acc").show();
                $("#pan_no").hide();
            } else if (type_text == "Person")
            {
                $("#pan_no").show();
                $("#bank_acc").hide();
            }
        });
    });

</script>
<script>

    $(document).ready(function ()
    {
        $("#modal_submit").click(function (event)
        {

            var name = $('#name').val();
            var address = $('#dir_address').val();
            var purpose_of_transaction = $('#purpose_of_transaction').val();
            var account_sub_group = $('#acc_sub_group').val();
            var bank_account = $('#banck_acc_no').val();
            var pan_no = $('#pan_no').val();
            var mobile = $('#dir_mobile').val();
            var type_text = $('#type_text').val();
            $.ajax(
                    {
                        url: base_url + 'ledger/add_ledger_general_bill',
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'ledger_title': name,
                                    'address': address,
                                    'purpose_of_transaction': purpose_of_transaction,
                                    'account_sub_group': account_sub_group,
                                    'bank_account': bank_account,
                                    'mobile': mobile,
                                    'pan_no': pan_no,
                                    'type_text': type_text
                                },
                        success: function (result)
                        {

                            var data = result['data'];
                            var ledger_data = result['ledger_data'];

                            $('#from_to').html('');
                            $('#from_to').append('<option value="">Select</option>');
                            for (i = 0; i < ledger_data.length; i++) {
                                $('#from_to').append('<option value="' + ledger_data[i].ledger_id + '">' + ledger_data[i].ledger_title + '</option>');
                            }
                            $('#from_to').val(result['id']).attr("selected", "selected");
                            $('#from_to').change();
                            //$("#director_org")[0].reset();
                        }

                    });

        });

        $("#name").on("blur", function (event)
        {
            var ledger_id = $('#name_ledger_id').val();
            var name = $('#name').val();

            $.ajax({
                url: base_url + 'ledger/get_check_ledger',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'ledger_name': name,
                    'ledger_id': ledger_id
                },
                success: function (result) {
                    console.log(result);
                    if (result[0].num > 0)
                    {
                        $('#name').val("");
                        $("#err_name").text(name + " name already exists!");
                    } else
                    {
                        $("#err_name").text("");
                    }
                }
            });
        });

    });
</script>
<script type="text/javascript">
    $('#add_director_org_modal').on('show.bs.modal', function (e) {
        $(this).css("overflow-y", "auto");
    });
</script>


