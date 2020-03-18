if (typeof category_ajax === 'undefined' || category_ajax === null)
{
    var category_ajax = "no"
}
$(document).ready(function ()
{
    var category_name_empty = "Please Enter the Category Name.";
    var category_name_invalid = "Please Enter Valid Category Name";
    var category_name_length = "Please Enter Category Name Minimun 3 Character";
    // var alphnum_regex = /^[a-zA-Z0-9]+$/;
    var alphnum_regex = /^[a-zA-Z0-9 ]+$/;
    var atlst_alpha=/[-a-zA-Z]+/;
    $("#add_category").click(function (event)
    {
        var category_name = $('#category_name').val().trim();
        $('#category_name').val(category_name);
        if (category_name == null || category_name == "")
        {
            $("#err_category_name").text(category_name_empty);
            return !1
        } else {
            $("#err_category_name").text("");
        }
        if (!category_name.match(alphnum_regex))
        {
            $('#err_category_name').text(category_name_invalid);
            return !1
        } else
        {
            $("#err_category_name").text("")
        }
        if (!category_name.match(atlst_alpha))
        {
            $('#err_category_name').text("Category must contain one Charecter Atleast.");
            return !1
        } else
        {
            $("#err_category_name").text("")
        }
        if (category_name.length < 3)
        {
            $('#err_category_name').text(category_name_length);
            return !1
        } else
        {
            $("#err_category_name").text("")
        }
        if(!(category_name == null || category_name == "")) {
                var i,temp=0;
                var addr_trim=$.trim(category_name);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_category_name").text("Please Enter Valid Category name.");
                            return !1; 
                        }
                        else {
                            $("#err_category_name").text("");
                        }
                    }  
                }  
            }
        var category_type = $('#category_type').val();
        /*if (category_type == null || category_type == "")
        {
            $("#err_category_type").text("Please Select Category Type");
            return !1
        } else
        {
            $("#err_category_type").text("")
        }*/
        if (category_ajax == "yes")
        {
            $.ajax(
                    {
                        url: base_url + 'category/add_category_ajax',
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'category_name': $('#category_name').val(),
                                    'category_type': $('#category_type').val()
                                },
                                beforeSend: function(){
                                    // Show image container
                                    $("#category_modal #loader").show();
                                },
                        success: function (result)
                        {
                              $("#category_modal #loader").hide();
                            if(result == 'duplicate'){
                                 $("#err_category_name").text("Category Name already exist");
                                 return !1;
                            }  else {
                            var data = result.data;
                            var category_item_type = $('#category_item_type').val();
                            if (category_item_type == "product")
                            {
                                $('#product_category').html('');
                                $('#product_subcategory').html('');
                                $('#category_id_model').html('');
                                $('#product_category').append('<option value="">Select</option>');
                                $('#product_subcategory').append('<option value="">Select</option>');
                                $('#category_id_model').append('<option value="">Select</option>');
                                $('#category_name').val('');
                                for (i = 0; i < data.length; i++)
                                {
                                    $('#product_category').append('<option value="' + data[i].category_id + '">' + data[i].category_name + '</option>')
                                }
                                for (i = 0; i < data.length; i++)
                                {
                                    $('#category_id_model').append('<option value="' + data[i].category_id + '">' + data[i].category_name + '</option>')
                                }
                                $('#category_id_model').val(result.id).attr("selected", "selected");
                                $('#product_category').val(result.id).attr("selected", "selected");
                                $('#category_modal').modal('toggle');
                                var product_code = $('#product_code').val();
                                var category_id = $('#product_category').val();
                                  $.ajax({
                                    url: base_url + 'product/get_product_sku',
                                    dataType: 'JSON',
                                    method: 'POST',
                                    data: {
                                        'product_code': product_code,
                                        'category_id': category_id
                                    },
                                    success: function (result) {
                                        $("#product_sku").val(result);
                                    }
                                });
                                // $("#categoryForm")[0].reset();
                            } else
                            {
                                $('#service_category').html('');
                                $('#service_subcategory').html('');
                                $('#category_id_model').html('');
                                $('#service_category').append('<option value="">Select</option>');
                                $('#service_subcategory').append('<option value="">Select</option>');
                                $('#category_id_model').append('<option value="">Select</option>');
                                $('#category_name').val('');
                                for (i = 0; i < data.length; i++)
                                {
                                    $('#service_category').append('<option value="' + data[i].category_id + '">' + data[i].category_name + '</option>')
                                }
                                for (i = 0; i < data.length; i++)
                                {
                                    $('#category_id_model').append('<option value="' + data[i].category_id + '">' + data[i].category_name + '</option>')
                                }
                                $('#service_category').val(result.id).attr("selected", "selected");
                                $('#category_id_model').val(result.id).attr("selected", "selected");
                                // $("#categoryForm")[0].reset();
                               // $('#category_modal').hide();
                                $('#category_modal').modal('toggle');
                            }
                            $('#subcategory_disable').removeClass('disable_div');
                        }
                        }
                    })
        }
    });
    $("#category_name").on("blur", function (event)
    {
        var category_name = $('#category_name').val();
        if (category_name.length > 3)
        {
            if (category_name == null || category_name == "")
            {
                $("#err_category_name").text(category_name_empty);
                return !1
            } else
            {
                $("#err_category_name").text("")
            }
            if (!category_name.match(alphnum_regex))
            {
                $('#err_category_name').text(category_name_invalid);
                return !1
            } else
            {
                $("#err_category_name").text("")
            }
            if (category_name.length < 3)
            {
                $('#err_category_name').text(category_name_length);
                return !1
            } else
            {
                $("#err_category_name").text("")
            }
        }
    });
});
