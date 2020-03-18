$(document).ready(function () {

    $("#module_settings_submit").click(function (event)
    {
        var module_name = $('#module_name').val();
        var invoice_first_prefix = $('#invoice_first_prefix').val();
        var invoice_last_prefix = $('#invoice_last_prefix').val();
        var invoice_seperation = $('#invoice_seperation').val();
        var invoice_type = $('#invoice_type').val();
        var invoice_creation = $('#invoice_creation').val();
        var invoice_readonly = $('#invoice_readonly').val();
        var item_access = $('#item_access').val();
        var note_split = $('#note_split').val();

        if (module_name == null || module_name == "")
        {
            $("#err_module_name").text("Please Select Module.");
            return false;
        } else
        {

            $("#err_module_name").text("");
        }

        if (invoice_first_prefix == null || invoice_first_prefix == "")
        {
            $("#err_invoice_first_prefix").text("Please Enter Invoice First Prefix.");
            return false;
        } else
        {
            $("#err_invoice_first_prefix").text("");
        }

        if (invoice_last_prefix == null || invoice_last_prefix == "")
        {
            $("#err_invoice_last_prefix").text("Please Select Invoice Last Prefix.");
            return false;
        } else
        {
            $("#err_invoice_last_prefix").text("");
        }

        if (invoice_seperation == null || invoice_seperation == "")
        {
            $("#err_invoice_seperation").text("Please Select Invoice Seperation.");
            return false;
        } else
        {
            $("#err_invoice_seperation").text("");
        }

        if (invoice_type == null || invoice_type == "")
        {
            $("#err_invoice_type").text("Please Select Invoice Type.");
            return false;
        } else
        {
            $("#err_invoice_type").text("");
        }

        if (invoice_creation == null || invoice_creation == "")
        {
            $("#err_invoice_creation").text("Please Select Invoice Creation.");
            return false;
        } else
        {
            $("#err_invoice_creation").text("");
        }

        if (invoice_readonly == null || invoice_readonly == "")
        {
            $("#err_invoice_readonly").text("Please Select Invoice Readonly.");
            return false;
        } else
        {
            $("#err_invoice_readonly").text("");
        }

        if (item_access == null || item_access == "")
        {
            $("#err_item_access").text("Please Select Item Access.");
            return false;
        } else
        {
            $("#err_item_access").text("");
        }

        if (note_split == null || note_split == "")
        {
            $("#err_note_split").text("Please Select Note Split.");
            return false;
        } else
        {
            $("#err_note_split").text("");
        }

        if (module_name == "10")
        {
            var reference_first_prefix = $('#reference_first_prefix').val();
            if (reference_first_prefix == null || reference_first_prefix == "")
            {
                $("#err_reference_first_prefix").text("Please Enter Reference First Prefix.");
                return false;
            } else
            {
                $("#err_reference_first_prefix").text("");
            }
        }
    });

    $("#module_name").change(function ()
    {
        var module_name = $('#module_name').val();
        if (module_name == "")
        {
            $('#err_module_name').text("Select Module Name");
        } else
        {
            $('#err_module_name').text("");
        }
        if (module_name == "10")
        {
            $('#reference_first_prefix_div').show();
        } else
        {
            $('#reference_first_prefix_div').hide();
        }
    });

    $("#invoice_creation").change(function ()
    {
        var invoice_creation = $('#invoice_creation').val();
        var option = '<option value="">Select</option>';
        if (invoice_creation == "automatic")
        {
            option += '<option value="yes">Yes</option>';
        } else
        {
            option += '<option value="yes">Yes</option><option value="no">No</option>';
        }
        $('#invoice_readonly').html(option);
    });

});
