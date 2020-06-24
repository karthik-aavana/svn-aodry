<div id="varient_modal" class="modal fade" role="dialog" data-backdrop="static">    <div class="modal-dialog modal-md">        <div class="modal-content">            <div class="modal-header">                <button type="button" class="close" data-dismiss="modal">&times;</button>                <h4>Add Variant</h4>            </div>            <form id="varient_form">                <div class="modal-body">                    <div class="modal-body">                    <div id="loader">                        <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>                    </div>                    <div class="row">                                            <div class="col-md-12">                            <div class="form-group">                                <label for="varient_name"><!-- Category Name -->Variant Name <span class="validation-color">*</span></label>                                <input type="text" class="form-control" id="varient_name" name="varient_name">                                <span class="validation-color" id="err_varient_name"></span>                            </div>                        </div>                                           </div>                </div>                <div class="modal-footer">                    <button type="submit" id="varient_add_submit" class="btn btn-info pull-right" class="close" data-dismiss="modal"> Add                    </button>                    <button type="button" class="btn btn-info" data-dismiss="modal">                        Cancel                    </button>                </div>            </form>        </div>    </div></div><script type="text/javascript">    $(document).ready(function () {        $("#varient_add_submit").click(function (event) {            var varient_name = $('#varient_name').val();            var varient_name_empty = "Please Enter Varient Name.";            var varient_name_invalid = "Please Enter Valid Varient Name";            if (varient_name == null || varient_name == "")            {                $("#err_varient_name").text(varient_name_empty);                return !1;            } else            {                $("#err_varient_name").text("");            }            if (!varient_name.match(name_regex))            {                $('#err_varient_name').text(varient_name_invalid);                return !1;            } else            {                $("#err_varient_name").text("");            }            if (varient_name.length < 3)            {                $('#err_varient_name').text(varient_name_length);                return !1;            } else            {                $("#err_varient_name").text("");            }            $.ajax({                url: base_url + 'varients/add_varient_key_modal',                dataType: 'JSON',                method: 'POST',                data: {'varient_name': varient_name},                beforeSend: function(){                    // Show image container                    $("#loader").show();                },                 success: function (result)                {                    $("#loader").hide();                    var data = result.data;                    $('#varient_key').html('');                    $('#varient_key').append('<option value="">Select</option>');                    for (i = 0; i < data.length; i++)                    {                        $('#varient_key').append('<option value="' + data[i].varients_id + '">' + data[i].varient_key + '</option>')                    }                    $('#varient_key').val(result.id).attr("selected", "selected");                    $("#varient_form")[0].reset()                }            });        });        $("#varient_name").on("blur", function (event)        {            var varient_name = $('#varient_name').val();            if (varient_name == "")            {                $('#err_varient_name').text("Please Enter Varient Name");                return !1;            } else            {                $('#err_varient_name').text("");            }        });    });</script>