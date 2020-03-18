if (typeof supplier_ajax === 'undefined' || supplier_ajax === null) {
    var supplier_ajax = "no";
}
if (typeof supplier_add === 'undefined' || supplier_add === null) {
    var supplier_add = "no";
}
/*$(document).on("click", "#add_contact_person", function ()
{
    if ($('#add_contact_person').is(":checked"))
    {
        $('#contact_person_div').show();
    } else
    {
        $('#contact_person_div').hide();
    }
});*/
/*$("#supplier_type").change(function (event)
{
    if (supplier_add == 'yes')
    {
        if ($('#supplier_type').val() == 'company')
        {
            $('#contact_person').show();
        } else
        {
            $('#contact_person').hide();
            $('#add_contact_person').prop('checked', false);
            $('#contact_person_div').hide();
        }
    }
});*/
$(document).on("click", ".open_supplier_modal", function ()
{
    var selected = 'current';
    var module_id = $("#supplier_module_id").val();
    var privilege = $("#privilege").val();
    $.ajax({
                url: base_url + 'general/generate_date_reference',
                type: 'POST',
                data:
                        {
                            date: selected,
                            privilege: privilege,
                            module_id: module_id
                        },
                success: function (data)
                {
                    var parsedJson = $.parseJSON(data);
                    var supplier_code = parsedJson.reference_no;
                    $(".modal-body #supplier_code").val(supplier_code);
                    if (parsedJson.access_settings[0].invoice_readonly == "yes")
                    {
                        $('.modal-body #supplier_code').attr('readonly', 'true');
                    }
                    $("#err_supplier_code").text("");
                }
            });
});
$(document).ready(function () {
    $('#cmb_country').change(function () {
        var id = $(this).val();
        $('#cmb_state').empty();
        $('#cmb_city').empty();
        $.ajax({
            url: base_url + 'general/get_state/',
            method: "POST",
            data: { id : id},
            dataType: "JSON",
            success: function (data) {
                 var state = $('#cmb_state');
                 var opt = document.createElement('option');
                    opt.text = 'Select State';
                    opt.value = '';
                    state.append(opt);
                var city = $('#cmb_city');
                var opt1 = document.createElement('option');
                    opt1.text = 'Select City';
                    opt1.value = '';
                    city.append(opt1);                     
                    for (i = 0; i < data.length; i++) {
                        $('#cmb_state').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');
                    }
                }
        });
    });
    $('#cmb_state').change(function () {
        var id = $(this).val();
        $('#cmb_city').empty();
        $.ajax({
            url: base_url + 'general/get_city/'+id,
            dataType: "JSON",
            success: function (data) {
                 var city = $('#cmb_city');
                 var opt = document.createElement('option');
                    opt.text = 'Select City';
                    opt.value = '';
                    city.append(opt);                    
                for (i = 0; i < data.length; i++) {
                     $('#cmb_city').append('<option value="' + data[i].city_id + '">' + data[i].city_name + '</option>');
                }
                var opt1 = document.createElement('option');
                    opt1.text = 'Others';
                    opt1.value = '0';
                    city.append(opt1);
            }
        });
    });
    var supplier_name_exist = 0;
    var supplier_code_exist = 0;

    $("#supplier_submit").click(function (event){
        var name_regex1 = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
        var supplier_code = $('#supplier_code').val()?$('#supplier_code').val():"";
        var supplier_name = $('#supplier_name').val()?$('#supplier_name').val():"";
        var supplier_type = $('#supplier_type').val()?$('#supplier_type').val():"";
        var city = $('#cmb_city').val()?$('#cmb_city').val():"";
        var state = $('#cmb_state').val()?$('#cmb_state').val():"";
        var country = $('#cmb_country').val()?$('#cmb_country').val():"";
        var gstin = $('#gst_number').val()?$('#gst_number').val():"";
        var address = $('#address').val()?$('#address').val():"";
        var pin_number = $('#txt_pin_code').val() ? $('#txt_pin_code').val() : "";
        var pan_number = $('#txt_pan_number').val() ? $('#txt_pan_number').val() : "";
        var tan_number = $('#txt_tan_number').val() ? $('#txt_tan_number').val() : "";
        var email = $('#email_address').val() ? $('#email_address').val() : "";
        var contact_person = $('#txt_contact_person').val()?$('#txt_contact_person').val():"";
        var payment_days = $('#payment_days').val() ? $('#payment_days').val():"";
        var name_regex = /^[-a-zA-Z\s0-9 ]+$/;
        var alpa_regex = /^[a-zA-Z ]+$/;
        var num_regex = /^[0-9]+$/;
        var email_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        var gst_regex = "^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$";
        var dl_no = $('#dl_no').val();
        var hsi_no = $('#hsi_no').val();
        // var address = $('#address').val();
        //var mobile = $('#mobile').val()?$('#mobile').val():"";
        //var email = $('#email').val()?$('#email').val():"";
        //var gsttype = $('#gstregtype').val()?$('#gstregtype').val():"";
        //var postal_code = $('#postal_code').val()?$('#postal_code').val():"";
       // var panno = $('#panno').val()?$('#panno').val():"";
        //var tanno = $('#tanno').val()?$('#tanno').val():"";
        //var state_code = $('#state_code').val()?$('#state_code').val():"";
       /* if ($('#add_contact_person').is(":checked"))
        {
            var add_contact_person = "yes";
        } else
        {
            var add_contact_person = "no";
        }*/
        if (supplier_code == null || supplier_code == "")
        {
            $("#err_supplier_code").text("Please Enter Supplier Code.");
            return false;
        } else
        {
            $("#err_supplier_code").text("");
        }
        if (supplier_code_exist == 1)
        {
            $("#err_supplier_code").text(supplier_code + " code already exists!");
            return false;
        }
        if (supplier_type == null || supplier_type == "") {
            $("#err_supplier_type").text("Please Select Supplier Type.");
            return false;
        } else {
            $("#err_supplier_type").text("");
        }
        if (supplier_name == null || supplier_name == "") {
            $("#err_supplier_name").text("Please Enter Supplier Name.");
            return false;
        } else {
            $("#err_supplier_name").text("");
            if($('[name=vendor_name_used]').val() > 0){
                $("#err_supplier_name").text("Name already used!");
                return false;
            }
        }
        if (!supplier_name.match(name_regex1)) {
            $('#err_supplier_name').text("Please Enter Valid Supplier Name ");
            return false;
        } else {
            $("#err_supplier_name").text("");
        }
        if (supplier_name_exist == 1)
        {
            $("#err_supplier_name").text(supplier_name + " name already exists!");
            return false;
        }
        
        if(gstin.length > 0){
            if (gstin.length < 15 || gstin.length > 15) {
                $("#err_gstin").text("GSTIN Number should have length 15");
                return false;
            } else {
                $("#err_gstin").text("");
            }
            if (!gstin.match(gst_regex)) {
            $("#err_gstin").text("Please Enter Valid GSTIN");
            return false;
            } else {
            $("#err_gstin").text("");
            }
        }
        if (country == null || country == "") {
            $("#err_country").text("Please Select Country ");
            return false;
        } else {
            $("#err_country").text("");
        }
        if (state == null || state == "") {
            $("#err_state").text("Please Select State ");
            return false;
        } else {
            $("#err_state").text("");
        }
        if (city == null || city == "") {
            $("#err_city").text("Please Select City ");
            return false;
        } else {
            $("#err_city").text("");
        }
        if (pin_number != "" ) {
           if (!pin_number.match(num_regex)) {
                $('#err_pin_code').text("Please Enter Valid Pin Number");
                return false; 
            }  else {
            $("#err_pin_code").text("");
        }
        } else {
            $("#err_pin_code").text("");
        }
        if (pan_number != "" ) {
           if (!pan_number.match(name_regex)) {
                $('#err_pan_number').text("Please Enter Valid Pan Number");
                return false; 
            } else {
            $("#err_pan_number").text("");
        } 
        } else {
            $("#err_pan_number").text("");
        }
        if (tan_number != "" ) {
           if (!tan_number.match(name_regex)) {
                $('#err_tan_number').text("Please Enter Valid Tan Number");
                return false; 
            } 
            else {
            $("#err_tan_number").text("");
        } 
        } else {
            $("#err_tan_number").text("");
        }
        if (contact_person != "" ) {
           if (!contact_person.match(alpa_regex)) {
                $('#err_contact_person').text("Please Enter Valid Contact Person Name");
                return false; 
            } else {
            $("#err_contact_person").text("");
        }  
        } else {
            $("#err_contact_person").text("");
        }
        /*if (email == null || email == "") {
            $("#err_email_address").text("Please Enter Email.");
            return false;
        } else {
            $("#err_email_address").text("");
        }*/
        if (email != "") {
            if (!email.match(email_regex)) {
                $('#err_email_address').text("Please Enter Valid Email");
                return false;
            } else {
                $("#err_email_address").text("");
            }
        }
        if ( payment_days != "") {
            if (payment_days < 0 || payment_days > 365 ) {
                $('#err_payment_days').text("Value must be less than or equal to 365");
                return false;
            } else {
                $("#err_payment_days").text("");
            }
        }
        if (address == null || address == "") {
            $("#err_address").text("Please Enter Address");
            return false;
        } else {
            $("#err_address").text("");
        }
        if (supplier_ajax != "yes")
            {
            if (country == null || country == "") {
                $("#err_country").text("Please Select Country ");
                return false;
            } else {
                $("#err_country").text("");
            }
            if (state == null || state == "") {
                $("#err_state").text("Please Select State ");
                return false;
            } else {
                $("#err_state").text("");
            }
            if (city == null || city == "") {
                $("#err_city").text("Please Select City ");
                return false;
            } else {
                $("#err_city").text("");
            }
        }
        /*if (add_contact_person == 'yes')
        {
            var contact_person_code = $('#contact_person_code').val()?$('#contact_person_code').val():"";
            var contact_person_name = $('#contact_person_name').val()?$('#contact_person_name').val():"";
            // var contact_person_address = $('#contact_person_address').val();
            var contact_person_city = $('#contact_person_city').val()?$('#contact_person_city').val():"";
            var contact_person_state = $('#contact_person_state').val()?$('#contact_person_state').val():"";
            var contact_person_country = $('#contact_person_country').val()?$('#contact_person_country').val():"";
            var contact_person_mobile = $('#contact_person_mobile').val()?$('#contact_person_mobile').val():"";
            var contact_person_telephone = $('#contact_person_telephone').val()?$('#contact_person_telephone').val():"";
            var contact_person_email = $('#contact_person_email').val()?$('#contact_person_email').val():"";
            var contact_person_postal_code = $('#contact_person_postal_code').val()?$('#contact_person_postal_code').val():"";
        if (supplier_ajax != "yes")
        {
            if (contact_person_code == null || contact_person_code == "")
            {
                $("#err_contact_person_code").text("Please Enter Contact Person Code.");
                return false;
            } else
            {
                $("#err_contact_person_code").text("");
            }
        }
            if (contact_person_name == null || contact_person_name == "")
            {
                $("#err_contact_person_name").text("Please Enter Contact Person Name.");
                return false;
            } else
            {
                $("#err_contact_person_name").text("");
            }
        if (supplier_ajax != "yes")
        {
            if (contact_person_country == null || contact_person_country == "") {
                $("#err_contact_person_country").text("Please Select Country ");
                return false;
            } else {
                $("#err_contact_person_country").text("");
            }
            if (contact_person_state == null || contact_person_state == "") {
                $("#err_contact_person_state").text("Please Select State ");
                return false;
            } else {
                $("#err_contact_person_state").text("");
            }
            if (contact_person_city == null || contact_person_city == "") {
                $("#err_contact_person_city").text("Please Select City ");
                return false;
            } else {
                $("#err_contact_person_city").text("");
            }
        }
            if (contact_person_mobile.length > 0)
            {
                if (contact_person_mobile == null || contact_person_mobile == "") {
                    $("#err_contact_person_mobile").text("Please Enter Mobile.");
                    return false;
                } else {
                    $("#err_contact_person_mobile").text("");
                }
                if (!contact_person_mobile.match(mobile_regex)) {
                    $('#err_contact_person_mobile').text("Please Enter Valid Mobile ");
                    return false;
                } else {
                    $("#err_contact_person_mobile").text("");
                }
            }
            if (contact_person_telephone.length > 0)
            {
                if (contact_person_telephone == null || contact_person_telephone == "") {
                    $("#err_contact_person_telephone").text("Please Enter Telephone.");
                    return false;
                } else {
                    $("#err_contact_person_telephone").text("");
                }
                if (!contact_person_telephone.match(mobile_regex)) {
                    $('#err_contact_person_telephone').text("Please Enter Valid Telephone.");
                    return false;
                } else {
                    $("#err_contact_person_telephone").text("");
                }
            }
            if (contact_person_postal_code.length > 0)
            {
                if (!contact_person_postal_code.match(digit_regex))
                {
                    $('#err_contact_person_postal_code').text("Please Enter Valid Postal Code");
                    return false;
                } else
                {
                    $("#err_contact_person_postal_code").text("");
                }
                if (contact_person_postal_code.length != 6)
                {
                    $('#err_contact_person_postal_code').text("Please Enter 6 Digit Postal Code");
                    return false;
                } else
                {
                    $("#err_contact_person_postal_code").text("");
                }
            }
            if (contact_person_email.length > 0)
            {
                if (contact_person_email == null || contact_person_email == "") {
                    $("#err_contact_person_email").text("Please Enter Email.");
                    return false;
                } else {
                    $("#err_contact_person_email").text("");
                }
                if (!contact_person_email.match(email_regex)) {
                    $('#err_contact_person_email').text("Please Enter Valid Email Address ");
                    return false;
                } else {
                    $("#err_contact_person_email").text("");
                }
            }
        }
        */
        if (supplier_ajax == "yes")
        {
            $("#loader_coco").show();
            $.ajax({
                url: base_url + 'supplier/add_supplier_ajax',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'supplier_name': $('#supplier_name').val(),
                    'supplier_code': $('#supplier_code').val(),
                    'supplier_type': $('#supplier_type').val(),
                    'reference_number': $('#reference_number').val(),
                    //'gstregtype': $('#gstregtype').val(),
                    //'supplier_area': $('#supplier_area').val(),
                    //'state_id': $('#state_id').val(),
                    'email': $('#email_address').val(),
                    'supplier_gstin_number': $('#gst_number').val(),
                    'postal_code': $('#txt_pin_code').val(),
                    'department' : $('#department').val(),
                    //'state_code': $('#state_code').val(),
                    'panno': $('#txt_pan_number').val(),
                    'tanno': $('#txt_tan_number').val(),
                    'supplier_address': $("#address").val(),
                    'country': $("#cmb_country").val(),
                    'state': $("#cmb_state").val(),
                    'city': $("#cmb_city").val(),
                    //'website': $("#website").val(),
                    'mobile': $('#txt_contact_number').val(),
                    //'telephone': $('#telephone').val(),
                    'supplier_contact_person': $("#txt_contact_person").val(),
                    'contact_person_name': $('#txt_contact_person').val(),
                    'payment_days': $('#payment_days').val(),
                    'dl_no' : $('#dl_no').val(),
                    'hsi_no' : $('#hsi_no').val(),
                    //'contact_person_code': $('#contact_person_code').val(),
                    //'contact_person_country': $("#contact_person_country").val(),
                    //'contact_person_state': $("#contact_person_state").val(),
                    //'contact_person_city': $("#contact_person_city").val(),
                    //'contact_person_email': $('#contact_person_email').val(),
                    //'contact_person_mobile': $('#contact_person_mobile').val(),
                },
                // beforeSend: function(){
                //     // Show image container
                //     $("#loader").show();
                // },
                success: function (result) {
                      $("#loader_coco").hide();
                    var data = result['data'];
                    $('#supplier').html('');
                    $('#supplier').append('<option value="">Select</option>');
                    for (i = 0; i < data.length; i++) {
                        if ($('#supplier_area').val() == "expense")
                        {
                            if(data[i].supplier_mobile != ''){
                                $('#supplier').append('<option value="' + data[i].supplier_id + '">' + data[i].supplier_name+'('+data[i].supplier_mobile+')' + '</option>');
                            }else{
                                $('#supplier').append('<option value="' + data[i].supplier_id + '">' + data[i].supplier_name + '</option>');
                            }      
                            $('#supplier').val(result['id']).attr("selected", "selected");
                        } else
                        {
                            if(data[i].supplier_mobile != ''){
                                $('#supplier').append('<option value="' + data[i].supplier_id + '">' + data[i].supplier_name+'('+data[i].supplier_mobile+')' + '</option>');
                            }else{
                                $('#supplier').append('<option value="' + data[i].supplier_id + '">' + data[i].supplier_name + '</option>');
                            }
                            $('#supplier').val(result['id']).attr("selected", "selected");
                        }
                    }
                    $('#supplier').change();
                    $("#supplierForm")[0].reset();
                }
            });
        }
    });
    $("#supplier_code").on("blur", function (event)
    {
        var supplier_code = $('#supplier_code').val();
        var supplier_id = $('#supplier_id').val();
        if (supplier_code == null || supplier_code == "") {
            $("#err_supplier_code").text("Please Enter supplier Code.");
            return false;
        } else {
            $("#err_supplier_code").text("");
        }
        $.ajax({
            url: base_url + 'supplier/get_check_supplier',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'supplier_code': supplier_code,
                'supplier_id': supplier_id
            },
            success: function (result) {
                if (result[0].num > 0)
                {
                    $('#supplier_code').val("");
                    $("#err_supplier_code").text(supplier_code + " code already exists!");
                    supplier_code_exist = 1;
                } else
                {
                    $("#err_supplier_code").text("");
                    supplier_code_exist = 0;
                }
            }
        });
    });
    // supplier Name check
    /*$("#supplier_name").on("blur", function (event) {
        var supplier_name = $('#supplier_name').val();
        if (supplier_name.length > 0)
        {
            var ledger_id = $('#supplier_ledger_id').val();
            if (supplier_name == null || supplier_name == "") {
                $("#err_supplier_name").text("Please Enter Supplier Name.");
                return false;
            } else {
                $("#err_supplier_name").text("");
            }
           
            $.ajax({
                url: base_url + 'ledger/get_check_ledger',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'ledger_name': supplier_name,
                    'ledger_id': ledger_id
                },
                success: function (result) {
                    if (result[0].num > 0)
                    {
                        $('#supplier_name').val("");
                        $("#err_supplier_name").text(supplier_name + " name already exists!");
                    } else
                    {
                        $("#err_supplier_name").text("");
                    }
                }
            });
        }
    });*/

    $('[name=supplier_name]').on('blur',function(){
        $('[name=supplier_name]').trigger('keyup');
    })

    $('[data-target="#supplier_modal"]').click(function(){
        $('#supplier_modal input').val('');
        $('#supplier_modal span[id^=err_]').text('');
        /*$('#supplier_modal span.validation-color').text('');*/
    })

    var xhr;
    $('[name=supplier_name]').on('keyup',function(){
        var cust_name= $(this).val();
        if(cust_name != ''){
            if (typeof xhr != 'undefined') {
                if(xhr.readyState != 4) xhr.abort();
            }
            $('[name=vendor_name_used]').val('0');
            $('#err_supplier_name').text('');
            xhr = $.ajax({
                url: base_url + 'supplier/SupplierValidation',
                type: 'post',
                data: {cust_name:cust_name,id:0},
                dataType: 'json',
                success: function (json) {
                    if(json.rows > 0){
                        $('#err_supplier_name').text('Name already used!');
                        $('[name=vendor_name_used]').val('1');
                    }
                }, complete: function () {
                   
                }
            })
        }
    })

    $("#contact_person_name").on("blur", function (event) {
        var contact_person_name = $('#contact_person_name').val();
        if (contact_person_name.length > 0)
        {
            // if (contact_person_name == null || contact_person_name == "") {
            //     $("#err_contact_person_name").text("Please Enter Contact Person Name.");
            //     return false;
            // } else {
            //     $("#err_contact_person_name").text("");
            // }
            if (!contact_person_name.match(general_regex)) {
                $('#err_contact_person_name').text("Please Enter Valid Contact Person Name ");
                return false;
            } else {
                $("#err_contact_person_name").text("");
            }
        }
    });
    //   $("#gstin").on("blur keyup", function (event) 
    //   {
    // if ($('#gstin').val() != "")
    //   {
    //        var gstin = $('#gstin').val();
    //       $("#gstin").val(gstin.toUpperCase());
    //           if (gstin == null || gstin == "") {
    //               $("#err_gstin").text("Please Enter GSTIN Number.");
    //               return false;
    //           } else {
    //               $("#err_gstin").text("");
    //           }
    //           if (!gstin.match(sname_regex)) {
    //               $('#err_gstin').text("Please Enter Valid GSTIN Number.");
    //               return false;
    //           } else {
    //               $("#err_gstin").text("");
    //           }
    //           if (gstin.length <15 || gstin.length>15) {
    //               $("#err_gstin").text("GSTIN Number should have length 15");
    //               return false;
    //           } else {
    //               $("#err_gstin").text("");
    //           }
    //       }
    //   });
    $("#gst_number").on("blur keyup", function (event) {
        var gst_regex = /^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/;
        var gstin = $('#gst_number').val();
        $("#gst_number").val(gstin.toUpperCase());
        var gstin_length = gstin.length;
        if (gstin_length > 0)
        {
            if (gstin == null || gstin == "") {
                $("#err_gstin").text("Please Enter GSTIN Number.");
                return false;
            } else {
                $("#err_gstin").text("");
            }
            if (!gstin.match(gst_regex)) {
                $('#err_gstin').text("Please Enter Valid GSTIN Number.");
                return false;
            } else {
                $("#err_gstin").text("");
            }
            if (gstin_length < 15 || gstin_length > 15)
            {
                $("#err_gstin").text("GSTIN Number should have length 15");
                return false;
            } else {
                $("#err_gstin").text("");
            }
            var res = gstin.slice(2, 12);
            $('#panno').val(res);
        }
    });
    $("#panno").on("blur keyup", function (event)
    {
        if ($('#panno').val() != "")
        {
            var panno = $('#panno').val();
            $("#panno").val(panno.toUpperCase());
            if (!panno.match(pan_regex))
            {
                $('#err_panno').text("Please Enter Valid Pan Number");
                return false;
            } else
            {
                $("#err_panno").text("");
            }
            if (panno.length != 10)
            {
                $('#err_panno').text("Please Enter 10 Digit Pan Number");
                return false;
            } else
            {
                $("#err_panno").text("");
            }
        }
    });
    $("#tanno").on("blur keyup", function (event)
    {
        if ($('#tanno').val() != "")
        {
            var tanno = $('#tanno').val();
            $("#tanno").val(tanno.toUpperCase());
            if (!tanno.match(tan_regex))
            {
                $('#err_tan').text("Please Enter Valid Tan Number");
                return false;
            } else
            {
                $("#err_tan").text("");
            }
            if (tanno.length != 10)
            {
                $('#err_tan').text("Please Enter 10 Digit Tan Number");
                return false;
            } else
            {
                $("#err_tan").text("");
            }
        }
    });
    $("#address").on("blur", function (event) {
        var address = $('#address').val();
        if (address.length > 0)
        {
            if (address == null || address == "") {
                $("#err_address").text("Please Enter Address");
                return false;
            } else {
                $("#err_address").text("");
            }
        }
    });
    $("#country").change(function (event) {
        var country = $('#country').val();
        if (country == null || country == "") {
            $("#err_country").text("Please Select Country");
            return false;
        } else {
            $("#err_country").text("");
        }
    });
    $("#gstregtype").change(function (event) {
        var gstregtype = $('#gstregtype').val();
        if (gstregtype == null || gstregtype == "") {
            $("#err_gstin_type").text("Please Select GST Registration");
            return false;
        } else {
            $("#err_gstin_type").text("");
        }
    });
    $("#state").change(function (event) {
        var state = $('#state').val();
        if (state == null || state == "") {
            $("#err_state").text("Please Select State ");
            return false;
        } else {
            $("#err_state").text("");
        }
    });
    $("#city").change(function (event) {
        var city = $('#city').val();
        if (city == null || city == "") {
            $("#err_city").text("Please Select City ");
            return false;
        } else {
            $("#err_city").text("");
        }
    });
    $("#postal_code").on("blur", function (event)
    {
        if ($('#postal_code').val() != "")
        {
            var postal_code = $('#postal_code').val();
            if (!postal_code.match(digit_regex))
            {
                $('#err_postal_code').text("Please Enter Valid Postal Code");
                return false;
            } else
            {
                $("#err_postal_code").text("");
            }
            if (postal_code.length != 6)
            {
                $('#err_postal_code').text("Please Enter 6 Digit Postal Code");
                return false;
            } else
            {
                $("#err_postal_code").text("");
            }
        }
    });
    $("#mobile").on("blur", function (event) {
        if ($('#mobile').val() != "")
        {
            var mobile = $('#mobile').val();
            $('#mobile').val(mobile);
            if (mobile == null || mobile == "") {
                $("#err_mobile").text("Please Enter Mobile.");
                return false;
            } else {
                $("#err_mobile").text("");
            }
            if (!mobile.match(mobile_regex)) {
                $('#err_mobile').text("Please Enter Valid Mobile ");
                return false;
            } else {
                $("#err_mobile").text("");
            }
        }
    });
    $("#email").on("blur", function (event)
    {
        if ($('#email').val() != "")
        {
            var email = $('#email').val();
            $('#email').val(email);
            if (email == null || email == "") {
                $("#err_email").text("Please Enter Email.");
                return false;
            } else {
                $("#err_email").text("");
            }
            if (!email.match(email_regex)) {
                $('#err_email').text("Please Enter Valid Email Address ");
                return false;
            } else {
                $("#err_email").text("");
            }
        }
    });

    
    
});
