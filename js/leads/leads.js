if (typeof lead_edit === 'undefined' || lead_edit === null) {
    var lead_edit = "no";
}

$(document).ready(function ()
{
    $("#group").change(function (event)
    {
        var group = $('#group').val();
        if (group != "" && group != null)
        {
            $('.stages_modal').show();
        } else
        {
            $('.stages_modal').hide();
        }
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: base_url + 'leads/get_stages',
            data:
                    {
                        group: group
                    },
            success: function (data)
            {
                $('#stages').html('<option value="">Select</option>');
                var option = '';
                for (i = 0; i < data.length; i++)
                {
                    option += '<option value="' + data[i].lead_stages_id + '">' + data[i].lead_stages_name + '</option>';
                }
                $('#stages').append(option);
            }
        });
        if (group != "" && group != null)
        {
            var group_name = $('#group option:selected').text();
            var option = '<option value="' + group + '">' + group_name + '</option>';

            $('#group_name1').html('');
            $('#group_name1').html(option);
        } else
        {
            $('#group_name1').html('');
        }
        // var option = '<option value="">Select</option>';
        // $('#stages').html('');
        // if(group == 'Subscriber')
        // {
        //     option += '<option value="Subscriber">Subscriber</option>';
        // }
        // else if(group == 'Lead')
        // {
        //     option += '<option value="Open / Not Attempted">Open / Not Attempted</option>';
        //     option += '<option value="Attempting to Contact">Attempting to Contact</option>';
        //     option += '<option value="Interested">Interested</option>';
        //     option += '<option value="Nurture">Nurture</option>';
        //     option += '<option value="Unresponsive">Unresponsive</option>';
        //     option += '<option value="No Further Action">No Further Action</option>';
        // }
        // else if(group == 'Opportunities')
        // {
        //     option += '<option value="Qualified">Qualified</option>';
        //     option += '<option value="Presentation and Demo">Presentation and Demo</option>';
        //     option += '<option value="Proposal">Proposal</option>';
        //     option += '<option value="Negotiation">Negotiation</option>';
        //     option += '<option value="Close / Customer">Close / Customer</option>';
        //     option += '<option value="Closed Lost">Closed Lost</option>';
        // }
        // else if(group == 'Not Interested')
        // {
        //     option += '<option value="Not Interested">Not Interested</option>';
        // }
        // $('#stages').html(option);
    });

    $("#leads_submit").click(function (event)
    {
        var lead_date = $('#lead_date').val();
        var asr_no = $('#asr_no').val();
        var customer = $('#customer').val();
        var group = $('#group').val();
        var stages = $('#stages').val();
        var item_access = $('#item_access').val();
        var products = $('#products').val();
        var services = $('#services').val();
        var source = $('#source').val();
        var business_type = $('#business_type').val();
        var next_action_date = $('#next_action_date').val();
        var comments = $('#comments').val();
        var expected_closing = $('#expected_closing').val();
        var priority = $('#priority').val();
        // var evange_list       =  $('#evange_list').val();
        var assign_to = $('#assign_to').val();

        if (lead_date == null || lead_date == "")
        {
            $("#err_lead_date").text("Please Select Lead Date.");
            return false;
        } else
        {
            $("#err_lead_date").text("");
        }
        if (asr_no == null || asr_no == "")
        {
            $("#err_asr_no").text("Please Enter ASR No.");
            return false;
        } else
        {
            $("#err_asr_no").text("");
        }
        if (customer == null || customer == "")
        {
            $("#err_customer").text("Please Select Customer.");
            return false;
        } else
        {
            $("#err_customer").text("");
        }
        if (lead_edit == "no")
        {
            if (group == null || group == "")
            {
                $("#err_group").text("Please Select Group.");
                return false;
            } else
            {
                $("#err_group").text("");
            }
            if (stages == null || stages == "")
            {
                $("#err_stages").text("Please Select Stages.");
                return false;
            } else
            {
                $("#err_stages").text("");
            }
        }
        if (item_access == 'product' || item_access == 'both')
        {
            if (products == null || products == "")
            {
                $("#err_products").text("Please Select Product.");
                return false;
            } else
            {
                $("#err_products").text("");
            }
        }
        if (item_access == 'service' || item_access == 'both')
        {
            if (services == null || services == "")
            {
                $("#err_services").text("Please Select Service.");
                return false;
            } else
            {
                $("#err_services").text("");
            }
        }
        if (source == null || source == "")
        {
            $("#err_source").text("Please Select Source.");
            return false;
        } else
        {
            $("#err_source").text("");
        }
        if (business_type == null || business_type == "")
        {
            $("#err_business_type").text("Please Enter Business Type.");
            return false;
        } else
        {
            $("#err_business_type").text("");
        }
        if (lead_edit == "no")
        {
            if (next_action_date == null || next_action_date == "")
            {
                $("#err_next_action_date").text("Please Select Next Action Date.");
                return false;
            } else
            {
                $("#err_next_action_date").text("");
            }
            if (comments == null || comments == "")
            {
                $("#err_comments").text("Please Enter Comments.");
                return false;
            } else
            {
                $("#err_comments").text("");
            }
        }
        if (expected_closing == null || expected_closing == "")
        {
            $("#err_expected_closing").text("Please Select Expected Closing Date.");
            return false;
        } else
        {
            $("#err_expected_closing").text("");
        }
        if (priority == null || priority == "")
        {
            $("#err_priority").text("Please Select Priority.");
            return false;
        } else
        {
            $("#err_priority").text("");
        }
        // if (evange_list == null || evange_list == "") 
        // {
        //     $("#err_evange_list").text("Please Select Evange List.");
        //     return false;
        // }
        // else
        // {
        //     $("#err_evange_list").text("");
        // }

        var attachment = $("#attachment").val();
        if (attachment)
        {
            var extension = attachment.split('.').pop().toUpperCase();
            if (extension != "PNG" && extension != "JPG" && extension != "JPEG" && extension != "PDF" && extension != "DOC" && extension != "DOCX" && extension != "XLS" && extension != "XLSX")
            {
                $('#err_attachment').text("Invalid extension " + extension);
                return false;
            } else
            {
                $('#err_attachment').text("");
            }
        }

        if (lead_edit == "no")
        {
            if (assign_to == null || assign_to == "")
            {
                $("#err_assign_to").text("Please Enter Assign To.");
                return false;
            } else
            {
                $("#err_assign_to").text("");
            }
        }
    });
});
