if (typeof discount_ajax === 'undefined' || discount_ajax === null)

{

    var discount_ajax = "no"

}

$(document).ready(function()

    {

        var discount_exist = 0;
        var float_num_regex = /^[+-]?([0-9]{1,2}[.])?[0-9]{1,2}$/;

        $("#discount_submit").click(function(event)

            {

                var discount_name = $('#discount_name').val();

                var discount_percentage = $('#discount_percentage').val();

                if (discount_name == null || discount_name == "")

                {

                    $("#err_discount_name").text("Please Enter Discount Name.");

                    $('#discount_name').focus();

                    return !1

                } else

                {

                    $("#err_discount_name").text("")

                }

                if (!discount_name.match(float_num_regex))

                {

                    $('#err_discount_name').text("Please Enter Valid Discount Name");

                    $('#discount_name').focus();

                    return !1

                } else

                {

                    $("#err_discount_name").text("")

                }

                if (discount_percentage == null || discount_percentage == "")

                {

                    $("#err_discount_percentage").text("Please Enter Discount Value.");

                    $('#discount_percentage').focus();

                    return !1

                } else

                {

                    $("#err_discount_percentage").text("")

                }

                if (discount_exist > 0)

                {

                    $('#err_discount_percentage').text("This discount value is already exist.");

                    return !1

                } else

                {

                    $('#err_discount_percentage').text("");

                }

                /* if (!discount_percentage.match(price_regex))

                 {

                     $('#err_discount_percentage').text(" Please Enter Valid Discount Value. ");

                     $('#discount_percentage').focus();

                     return !1

                 } else

                 {

                     $("#err_discount_percentage").text("")

                 }*/

                if (discount_percentage > 100)

                {

                    $("#err_discount_percentage").text("Please Enter Discount Value Between 1 to 100");

                    return !1

                } else

                {

                    $("#err_discount_percentage").text("")

                }

                if (discount_ajax == 'yes')

                {

                    $.ajax(

                        {

                            url: base_url + 'discount/add_discount_ajax',

                            dataType: 'JSON',

                            method: 'POST',

                            data:

                            {

                                'discount_name': $('#discount_name').val(),

                                'discount_percentage': $('#discount_percentage').val()

                            },

                            success: function(result)

                            {

                                // var data = result.data;

                                // $('.open_discount').html('');

                                // $('.open_discount').append('<option value="">Select</option>');

                                // for (var i = 0; i < data.length; i++)

                                // {

                                $('.open_discount').append('<option value="' + result.id + '-' + result.value + '">' + result.value + '% </option>');

                                // }

                                // $("#discountForm")[0].reset()



                                // $('#item_discount').html('');

                                // $('#item_discount').append('<option value="">Select</option>');

                                // for (i = 0; i < data.length; i++) 

                                // {

                                //    $('#item_discount').append('<option value="' + data[i].discount_id + '-' + data[i].discount_value + '">' + data[i].discount_value + '% </option>');

                                // }

                                // $('#item_discount').val(result.id+"-"+result.value).attr("selected", "selected");

                                $("#discountForm")[0].reset();

                                $('#item_discount').change();

                            }

                        })
                }
            });


        $("#discount_percentage").on("blur", function(event)

            {

                var discount_id = $('#discount_id').val();

                var discount_percentage = $('#discount_percentage').val();

                if (discount_percentage.length > 1 && discount_percentage != "")

                {

                    if (discount_percentage == null || discount_percentage == "")

                    {

                        $("#err_discount_percentage").text("Please Enter Discount Value.");

                        $('#discount_percentage').focus();

                        return !1

                    } else

                    {

                        $("#err_discount_percentage").text("")

                    }

                    if (!discount_percentage.match(float_num_regex))

                    {

                        $('#err_discount_percentage').text(" Please Enter Valid Discount Value. ");

                        $('#discount_percentage').focus();

                        return !1

                    } else

                    {

                        $("#err_discount_percentage").text("")

                    }

                    if (discount_percentage > 100)

                    {

                        $("#err_discount_percentage").text("Please Enter Discount Value Between 1 to 100");

                        return !1

                    } else

                    {

                        $("#err_discount_percentage").text("")

                    }

                }

                $.ajax(

                    {

                        url: base_url + 'discount/get_discount',

                        type: "POST",

                        dataType: "json",

                        data:

                        {

                            discount_id: discount_id,

                            discount_percentage: discount_percentage

                        },

                        success: function(data)

                        {

                            if (data[0].num_discount_percentage > 0)

                            {

                                $('#err_discount_percentage').text("The discount value is already exist.");

                                // $('#tax_value').val('');

                                discount_exist = 1;

                            } else

                            {

                                $('#err_discount_percentage').text("");

                                discount_exist = 0;

                            }

                        }

                    });

            })

    })