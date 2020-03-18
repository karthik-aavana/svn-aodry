if (typeof customer_ajax === 'undefined' || customer_ajax === null)

{

   var customer_ajax = "no"

}

$(document).ready(function ()

{

   $("#customer_modal_submit").click(function (event)

   {

      var customer_name = $('#customer_name').val();

      var address = $('#address').val();

      var city = $('#city').val();

      var state = $('#state').val();

      var country = $('#country').val();

      var mobile = $('#mobile').val();

      var email = $('#email').val();

      var gsttype = $('#gstregtype').val();

      var gstid = $('#gstid').val();

      var postal_code = $('#postal_code').val();

      var panno = $('#panno').val();

      var tan = $('#tan').val();

      var state_code = $('#state_code').val();

      if (customer_name == null || customer_name == "")

      {

         $("#err_customer_name").text("Please Enter Customer Name.");

         return !1

      }

      else

      {

         $("#err_customer_name").text("")

      }

      if (!customer_name.match(name_regex))

      {

         $('#err_customer_name').text("Please Enter Valid Customer Name ");

         return !1

      }

      else

      {

         $("#err_customer_name").text("")

      }

      if (gsttype == null || gsttype == "")

      {

         $("#err_gstid_type").text("Please Select Gst Registration Type.");

         return !1

      }

      else

      {

         $("#err_gstid_type").text("")

      }

      if (gsttype != "Unregistered")

      {

         $("#gstid").val(gstid.toUpperCase());

         if (gstid == null || gstid == "")

         {

            $("#err_gstid").text("Please Enter GSTIN Number");

            return !1

         }

         else

         {

            $("#err_gstid").text("")

         }

         if (gstid.length < 15 || gstid.length > 15)

         {

            $("#err_gstid").text("GSTIN Number should have length 15");

            return !1

         }

         else

         {

            $("#err_gstid").text("")

         }

         var res = gstid.slice(0, 2);

         var res1 = gstid.slice(13, 14);

         if (res != state_code || res1 != 'Z' && res1 != 'z')

         {

            $("#err_gstid").text("Please enter valid GSTIN number(Ex:29AAAAA9999AXZX)");

            return !1

         }

         else

         {

            $("#err_gstid").text("")

         }

         var pan_filter = gstid.substr(2, 10);

         if (!pan_filter.match(pan_regex))

         {

            $('#err_gstid').text("Please Enter Valid GSTIN Number(Ex:29AAAAA9999AXZX).");

            return !1

         }

         else

         {

            $("#err_gstid").text("");

            $("#panno").val(pan_filter)

         }

      }

      if (panno.length > 2)

      {

         $("#panno").val(panno.toUpperCase());

         if (!panno.match(pan_regex))

         {

            $('#err_panno').text("Please Enter Valid Pan Number (Ex:AAAAA9999A)");

            return !1

         }

         else

         {

            $("#err_panno").text("")

         }

         if (panno.length != 10)

         {

            $('#err_panno').text("Please Enter 10 Digit Pan Number");

            return !1

         }

         else

         {

            $("#err_panno").text("")

         }

      }

      if (tan.length > 2)

      {

         $("#tan").val(tan.toUpperCase());

         if (!tan.match(tan_regex))

         {

            $('#err_tan').text("Please Enter Valid Tan Number(EX:XXXX00000X)");

            return !1

         }

         else

         {

            $("#err_tan").text("")

         }

         if (tan.length != 10)

         {

            $('#err_tan').text("Please Enter 10 Digit Tan Number");

            return !1

         }

         else

         {

            $("#err_tan").text("")

         }

      }

      if (address == null || address == "")

      {

         $("#err_address").text("Please Enter Address");

         return !1

      }

      else

      {

         $("#err_address").text("")

      }

      if (!address.match(general_regex))

      {

         $('#err_address').text("Please Enter Valid Address");

         return !1

      }

      else

      {

         $("#err_address").text("")

      }

      if (country == null || country == "")

      {

         $("#err_country").text("Please Select Country ");

         return !1

      }

      else

      {

         $("#err_country").text("")

      }

      if (state == null || state == "")

      {

         $("#err_state").text("Please Select State ");

         return !1

      }

      else

      {

         $("#err_state").text("")

      }

      if (city == null || city == "")

      {

         $("#err_city").text("Please Select City ");

         return !1

      }

      else

      {

         $("#err_city").text("")

      }

      if (mobile == null || mobile == "")

      {

         $("#err_mobile").text("Please Enter Mobile.");

         return !1

      }

      else

      {

         $("#err_mobile").text("")

      }

      if (!mobile.match(mobile_regex))

      {

         $('#err_mobile').text("Please Enter Valid Mobile ");

         return !1

      }

      else

      {

         $("#err_mobile1").text("")

      }

      if (postal_code.length > 0)

      {

         if (!postal_code.match(digit_regex))

         {

            $('#err_postal_code').text("Please Enter Valid Postal Code");

            return !1

         }

         else

         {

            $("#err_postal_code").text("")

         }

         if (postal_code.length != 6)

         {

            $('#err_postal_code').text("Please Enter 6 Digit Postal Code");

            return !1

         }

         else

         {

            $("#err_postal_code").text("")

         }

      }

      if (email !=  ""){

       if (!email.match(email_regex)){

         $('#err_email').text("Please Enter Valid Email Address ");

         return !1

      }else{
         $("#err_email").text("")

      }

      }

      

      if (customer_ajax == 'yes')

      {

         $.ajax(

         {

            url: base_url + 'customer/add_customer_ajax',

            dataType: 'JSON',

            method: 'POST',

            data:

            {

               'customer_name': $('#customer_name').val(),

               'customer_code': $('#customer_code').val(),

               'gstregtype': $('#gstregtype').val(),

               'state_id': $('#state_id').val(),

               'email': $('#email').val(),

               'gstid': $('#gstid').val(),

               'postal_code': $('#postal_code').val(),

               'state_code': $('#state_code').val(),

               'panno': $('#panno').val(),

               'tanno': $('#tan').val(),

               'address': $("#address").val(),

               'country': $("#country").val(),

               'state': $("#state").val(),

               'city': $("#city").val(),

               'mobile': $('#mobile').val()

            },

            success: function (result)

            {

               var data = result.data;

               $('#customer').html('');

               $('#customer').append('<option value="">Select</option>');

               for (i = 0; i < data.length; i++)

               {

                  $('#customer').append('<option value="' + data[i].customer_id + '">' + data[i].customer_name + '</option>')

               }

               $('#customer').val(result.id).attr("selected", "selected");

               $('#customer').change();

               $("#customerForm")[0].reset();

            }

         })

      }

   });

   $("#customer_name").on("blur keyup", function (event)

   {

      var customer_name = $('#customer_name').val();

      if (customer_name == null || customer_name == "")

      {

         $("#err_customer_name").text("Please Enter Customer Name.");

         return !1

      }

      else

      {

         $("#err_customer_name").text("")

      }

      if (!customer_name.match(name_regex))

      {

         $('#err_customer_name').text("Please Enter Valid Customer Name ");

         return !1

      }

      else

      {

         $("#err_customer_name").text("")

      }

   });

   $("#gstid").on("blur keyup", function (event)

   {

      var gstid = $('#gstid').val();

      $("#gstid").val(gstid.toUpperCase());

      if (gstid == null || gstid == "")

      {

         $("#err_gstid").text("Please Enter GSTIN Number.");

         return !1

      }

      else

      {

         $("#err_gstid").text("")

      }

      if (!gstid.match(sname_regex))

      {

         $('#err_gstid').text("Please Enter Valid GSTIN Number(Ex:29AAAAA9999AXZX).");

         return !1

      }

      else

      {

         $("#err_gstid").text("")

      }

      if (gstid.length < 15 || gstid.length > 15)

      {

         $("#err_gstid").text("GSTIN Number should have length 15");

         return !1

      }

      else

      {

         $("#err_gstid").text("")

      }

      var pan_filter = gstid.substr(2, 10);

      if (!pan_filter.match(pan_regex))

      {

         $('#err_gstid').text("Please Enter Valid GSTIN Number(Ex:29AAAAA9999AXZX).");

         return !1

      }

      else

      {

         $("#err_gstid").text("");

         $("#panno").val(pan_filter)

      }

   });

   $("#panno").on("blur keyup", function (event)

   {

      var panno = $('#panno').val();

      if (panno != "" && panno.length > 2)

      {

         $("#panno").val(panno.toUpperCase());

         if (!panno.match(pan_regex))

         {

            $('#err_panno').text("Please Enter Valid Pan Number (Ex:AAAAA9999A)");

            return !1

         }

         else

         {

            $("#err_panno").text("")

         }

         if (panno.length != 10)

         {

            $('#err_panno').text("Please Enter 10 Digit Pan Number");

            return !1

         }

         else

         {

            $("#err_panno").text("")

         }

      }

   });

   $("#tan").on("blur keyup", function (event)

   {

      var tan = $('#tan').val();

      if (tan != "" && tan.length > 2)

      {

         $("#tan").val(tan.toUpperCase());

         if (!tan.match(tan_regex))

         {

            $('#err_tan').text("Please Enter Valid Tan Number(EX:XXXX00000X)");

            return !1

         }

         else

         {

            $("#err_tan").text("")

         }

         if (tan.length != 10)

         {

            $('#err_tan').text("Please Enter 10 Digit Tan Number");

            return !1

         }

         else

         {

            $("#err_tan").text("")

         }

      }

   });

   $("#address").on("blur keyup", function (event)

   {

      var address = $('#address').val();

      if (address == null || address == "")

      {

         $("#err_address").text("Please Enter Address");

         return !1

      }

      else

      {

         $("#err_address").text("")

      }

      if (!address.match(general_regex))

      {

         $('#err_address').text("Please Enter Valid Address");

         return !1

      }

      else

      {

         $("#err_address").text("")

      }

   });



   $("#postal_code").on("blur keyup", function (event)

   {

      var postal_code = $('#postal_code').val();

      if (postal_code != "" && postal_code.length > 2)

      {

         if (!postal_code.match(digit_regex))

         {

            $('#err_postal_code').text("Please Enter Valid Postal Code");

            return !1

         }

         else

         {

            $("#err_postal_code").text("")

         }

         if (postal_code.length != 6)

         {

            $('#err_postal_code').text("Please Enter 6 Digit Postal Code");

            return !1

         }

         else

         {

            $("#err_postal_code").text("")

         }

      }

   });

   $("#mobile").on("blur keyup", function (event)

   {

      var mobile = $('#mobile').val();

      $('#mobile').val(mobile);

      if (mobile == null || mobile == "")

      {

         $("#err_mobile").text("Please Enter Mobile.");

         return !1

      }

      else

      {

         $("#err_mobile").text("")

      }

      if (!mobile.match(mobile_regex))

      {

         $('#err_mobile').text("Please Enter Valid Mobile ");

         return !1

      }

      else

      {

         $("#err_mobile").text("")

      }

   });

   $("#email").on("blur keyup", function (event)

   {

      var email = $('#email').val();

      $('#email').val(email);

      if (email == null || email == "")

      {

         $("#err_email").text("Please Enter Email.");

         return !1

      }

      else

      {

         $("#err_email").text("")

      }

      if (!email.match(email_regex))

      {

         $('#err_email').text("Please Enter Valid Email Address ");

         return !1

      }

      else

      {

         $("#err_email").text("")

      }

   })

})