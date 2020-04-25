if (typeof subcategory_ajax === 'undefined' || subcategory_ajax === null)

{

    var subcategory_ajax = "no"

}

$(document).ready(function ()

{
    var subcategory_name_empty = "Please Enter Subcategory Type.";
    var subcategory_name_invalid = "Please Enter Valid Subategory Type";
    var subcategory_name_length = "Please Enter Subcategory Name Minimun 3 Character";
    var category_select = "Please Select Category.";

    // var alphnum_regex = /^[a-zA-Z0-9]+$/;

    //var alphnum_regex=/^[-a-zA-Z][-a-zA-Z\s0-9]+$/;

    var alphnum_regex = /^[a-zA-Z0-9 ]+$/;
        var atlst_alpha=/[-a-zA-Z]+/;

    $("#add_subcategory").click(function (event)

    {

        var subcategory_name = $('#subcategory_name').val().trim();

        var category = $('#category_id_model').val();

        if (category == "" || category == null)

        {

            $('#err_category_id_model').text(category_select);

            return !1

        } else

        {

            $('#err_category_id_model').text("")

        }

        $('#subcategory_name').val(subcategory_name);

        if (subcategory_name == null || subcategory_name == "")

        {

            $("#err_subcategory_name").text(subcategory_name_empty);

            return !1

        } else

        {

            $("#err_subcategory_name").text("")

        }

        if (!subcategory_name.match(alphnum_regex))

        {

            $('#err_subcategory_name').text(subcategory_name_invalid);

            return !1

        } else

        {

            $("#err_subcategory_name").text("");

        }



        if (!subcategory_name.match(atlst_alpha))
        {

            $('#err_subcategory_name').text("Subcategory must contain one Charecter Atleast .");

            return !1

        } else

        {

            $("#err_subcategory_name").text("");

        }





        if (subcategory_name.length < 3)

        {

            $('#err_subcategory_name').text(subcategory_name_length);

            return !1

        } else

        {
            $("#err_subcategory_name").text("")

        }
        if(!(subcategory_name == null || subcategory_name == "")) {
                var i,temp=0;
                var addr_trim=$.trim(subcategory_name);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_subcategory_name").text("Please Enter Valid Subcategory Type .");

                            return !1; 

                        }

                        else {

                            $("#err_subcategory_name").text("");

                        }

                    }  

                }  

            }

        if (subcategory_ajax == "yes")

        {

            $.ajax(

                    {

                        url: base_url + 'subcategory/add_subcategory_ajax',

                        dataType: 'JSON',

                        method: 'POST',

                        data:

                                {

                                    'subcategory_name': $('#subcategory_name').val(),

                                    'category_id_model': $('#category_id_model').val(),

                                    'category_id': $('#category').val()

                                },
                                beforeSend: function(){
                                    // Show image container
                                    $("#subcategory_modal #loader").show();
                                },

                        success: function (result){

                         $("#subcategory_modal #loader").hide();

                            if(result == 'duplicate'){
                                $("#err_subcategory_name").text("Sub Category is already exit");
                            }else{
                            var data = result.data;
                            var subcategory_type = $('#subcategory_item_type').val();
                            var category = result.data;
                            if (subcategory_type == "product")
                            {
                                $('#product_subcategory').html('');
                                $('#subcategory_name').val('');
                                $('#product_subcategory').append('<option value="">Select</option>');
                                for (i = 0; i < data.length; i++)
                                {
                                    $('#product_subcategory').append('<option value="' + data[i].sub_category_id + '">' + data[i].sub_category_name + '</option>')

                                }
                                $('#product_category').val($('#category_id_model').val()).attr("selected", "selected");
                                $('#product_subcategory').val(result.subcategory_id).attr("selected", "selected")
                                $('#subcategory_modal').modal('toggle');
                            } else
                            {
                                $('#service_subcategory').html('');
                                $('#subcategory_name').val('');
                                $('#service_subcategory').append('<option value="">Select</option>');
                                for (i = 0; i < data.length; i++)
                                {
                                    $('#service_subcategory').append('<option value="' + data[i].sub_category_id + '">' + data[i].sub_category_name + '</option>')
                                }
                                $('#service_category').val($('#category_id_model').val()).attr("selected", "selected");

                                $('#service_subcategory').val(result.subcategory_id).attr("selected", "selected");

                                $('#subcategory_modal').modal('toggle');

                            }

                        }

                        }

                    })

        }

    });

    $("#subcategory_name").on("blur", function (event)

    {

        var subcategory_name = $('#subcategory_name').val();

        if (subcategory_name.length > 3)

        {

            if (subcategory_name == null || subcategory_name == "")

            {

                $("#err_subcategory_name").text(subcategory_name_empty);

                return !1

            } else

            {

                $("#err_subcategory_name").text("")

            }

            if (!subcategory_name.match(alphnum_regex))

            {

                $('#err_subcategory_name').text(subcategory_name_invalid);

                return !1

            } else

            {

                $("#err_subcategory_name").text("")

            }

            if (subcategory_name.length < 3)

            {

                $('#err_subcategory_name').text(subcategory_name_length);

                return !1

            } else

            {

                $("#err_subcategory_name").text("")

            }

        }

    })



})

