$(document).ready(function () {
        
        $(document).on("click", "#shipping_pop", function () {
            var comp_table = $('#customer_address_table').DataTable();
            var billing_state = $('#billing_state').val();
            var party_id = $("#ship_to").val();
            var ship_add = $('[name=shipping_address]').val();
            comp_table.destroy();
            comp_table = $('#customer_address_table').DataTable({
                'ajax': {
                    url: base_url + 'general/get_shipping_popup',
                    type: 'post',
                    data: {'billing_state': billing_state, 'party_id': party_id, shipping_id: ship_add},
                },
                "processing": true,
                "serverSide": true,
                'columns': [
                    {'data': 'shipping_code'},
                    {'data': 'contact_person'},
                    {'data': 'shipping_address'},
                    {'data': 'gst'},
                    {'data': 'state'},
                    {'data': 'action'}
                ]
            });

            /*if(ship_add){
             console.log($('#customer_addr').find('[name=apply][value="'+ship_add+'"]'),ship_add);
             $('#customer_addr').find('[name=apply][value="'+ship_add+'"]').prop('checked',true);
             }*/
            $("#customer_addr").modal();
        });

        var biling_table = $('#billing_address_table').DataTable();
        $(document).on("click", "#customer_pop", function () {
            $("#billing_addr").modal();
        });

        var comp_table = $('#customer_address_table').DataTable();
        $(document).on("click", "#shipping_pop_edit", function () {
            var billing_state = $('#billing_state').val();
            var party_id = $("#ship_to").val();
            comp_table.destroy();
            var shipping_id = $('[name=shipping_address]').val();
            comp_table = $('#customer_address_table').DataTable({
                'ajax': {
                    url: base_url + 'general/get_shipping_popup_edit',
                    type: 'post',
                    data: {'billing_state': billing_state, 'party_id': party_id, 'shipping_id': shipping_id},
                },
                "processing": true,
                "serverSide": true,
                'columns': [
                    {'data': 'shipping_code'},
                    {'data': 'contact_person'},
                    {'data': 'shipping_address'},
                    {'data': 'gst'},
                    {'data': 'state'},
                    {'data': 'action'}
                ]
            });
            $("#customer_addr").modal();
        });
        
        $(document).on("change", "#customer", function () {
             var billing_state = $('#billing_state').val();
                var party_id = $("#customer").val();
                var ship_add = '';
            $.ajax({
                url: base_url + "general/get_billing_popup",
                type: "post",
                dataType: "JSON",
                data: {'party_id': party_id},
                success: function (data) {
                    var table_data = data.data;
                       var biling_table = $('#billing_address_table').DataTable();
                       biling_table.destroy();
                       biling_table = $('#billing_address_table').DataTable({
                           data: table_data,
                           'columns': [
                               {'data': 'shipping_code'},
                               {'data': 'contact_person'},
                               {'data': 'shipping_address'},
                               {'data': 'gst'},
                               {'data': 'state'},
                               {'data': 'action'}
                           ],
                           "initComplete": function(settings, json) {
                               $("#same_as_billing").prop('checked', true);
                               $(":input#same_as_billing").trigger('change');
                           }
                       });
                    if(data['recordsTotal'] == 1){
                       var val = data['shipping_address_id'];
                       $("#shipping_address").val(val);
                       $("#billing_address").val(val);
                       var customer_name = $("#customer :selected").val();

                       $('#ship_to>option[value='+customer_name+']').prop('selected',true);
                       $('#ship_to').select2();
                    }else{
                       $("#billing_addr").modal();    
                    }
                }
            });
        });

        $(document).on("change", "#ship_to", function(){
                var billing_state = $('#billing_state').val();
                var party_id = $("#ship_to").val();
                var ship_add = '';
                var comp_table = $('#customer_address_table').DataTable();
                comp_table.destroy();
                comp_table = $('#customer_address_table').DataTable({
                    'ajax': {
                        url: base_url + 'general/get_shipping_popup',
                        type: 'post',
                        data: {'billing_state': billing_state, 'party_id': party_id, shipping_id: ship_add},
                    },
                    "processing": true,
                    "serverSide": true,
                    'columns': [
                        {'data': 'shipping_code'},
                        {'data': 'contact_person'},
                        {'data': 'shipping_address'},
                        {'data': 'gst'},
                        {'data': 'state'},
                        {'data': 'action'}
                    ]
                });

                /*if(ship_add){
                 console.log($('#customer_addr').find('[name=apply][value="'+ship_add+'"]'),ship_add);
                 $('#customer_addr').find('[name=apply][value="'+ship_add+'"]').prop('checked',true);
                 }*/
                $("#customer_addr").modal();
        });
    });

    $(document).on('click', '.close_billing', function () {
        var val = $("input[name='apply_billing']:checked").val();
        console.log(val);
        if(val == undefined){
           // $('#billing_addr').modal({backdrop: 'static', keyboard: false}) 
            alert("Please Select One Address.")
           // $('#billing_addr .close').css('display','none');
            return false;
        }
         $('#billing_addr').modal('hide');
        //// $('#billing_addr').modal({backdrop: 'static'}) 
        $("#billing_address").val(val);
    });
    $(document).on('click', '.apply_bill_address', function () {
        var val = $("input[name='apply_billing']:checked").val();
        if(val == undefined){
           // $('#billing_addr').modal({backdrop: 'static', keyboard: false}) 
            alert("Please Select One Address.")
           // $('#billing_addr .close').css('display','none');
            return false;
        }
        $('#billing_addr').modal('hide');
        $("#billing_address").val(val);
        
    });

    $(document).on('click', '.close_customer', function () {
        var val = $("input[name='apply']:checked").val();
        var ship_to = $("#ship_to").val();
        if(val == undefined && ship_to != ''){
           // $('#billing_addr').modal({backdrop: 'static', keyboard: false}) 
            alert("Please Select One Address.")
           // $('#billing_addr .close').css('display','none');
            return false;
        }
        $('#customer_addr').modal('hide');
        $("#shipping_address").val(val);
    });
    $(document).on('click', '.apply_ship_address', function () {
        var val = $("input[name='apply']:checked").val();
        var ship_to = $("#ship_to").val();
        if(val == undefined && ship_to != ''){
           // $('#billing_addr').modal({backdrop: 'static', keyboard: false}) 
            alert("Please Select One Address.")
           // $('#billing_addr .close').css('display','none');
            return false;
        }
        $('#customer_addr').modal('hide');
        $("#shipping_address").val(val);
    });
    $(document).on('click', 'input[name=apply]:radio', function () {
        var id = $(this).val();
        $("#shipping_address").val(id);
    });
    /*$(document).on('click', 'input[name=apply_billing]:radio', function () {
        var id = $(this).val();
        $("#billing_address").val(id);
    });
    */
    $(document).on('click', 'input[name=apply_billing]:radio', function () {
        var id = $(this).val();
        var state_id = $("#state_id_suppuly_" + id).val();
        var country_type = $("#country_type_" + id).val();
        var country_id = $("#apply_country_id_" + id).val();
        $("#same_as_billing").prop('checked', false);
         
        $("#billing_address").val(id);
       /* if (country_type == 'same') {
            $("#billing_state").val(state_id).change();
        } else {
            $('#billing_state').val('0').change();
        }
        $('#billing_country').val(country_id).change();*/

        $.ajax({
            url: base_url + "general/get_shipping_address_place_change",
            dataType: "JSON",
            method: "POST",
            data: {
                party_id: $("#customer").val(),
                party_type: "customer",
                state_id: state_id
            },
            success: function (result) {
                var data = result["shipping_address_data"];
                // $("#shipping_address").empty();
                // $("#shipping_address").html("");
                // $("#shipping_address").append('<option value="">Select</option>');
                // for (i = 0; i < data.length; i++) {
                //     var shipping_address = data[i].shipping_address;

                //     var shipping_address_new = shipping_address.replace(
                //             /\n\r|\\r|\\n|\r|\n|\r\n|\\n\\r|\\r\\n/g,
                //             "<br/>"
                //             );

                //     $("#shipping_address").append(
                //             '<option value="' +
                //             data[i].shipping_address_id +
                //             '">' +
                //             shipping_address_new +
                //             "</option>"
                //             );
                // }

                // $("#shipping_address").select2({
                //     containerCssClass: "wrap"
                // });
                // $("#shipping_address").change();

            }
        });
    });
    $(document).on("change", "#same_as_billing", function () {
        var val = $("input[name='apply_billing']:checked").val();
        var customer_name = $("#customer :selected").val();
        $("#shipping_address").val(val);
        $('#ship_to>option[value='+customer_name+']').prop('selected',true);
        $('#ship_to').select2()     
        if(!$(this).is(':checked')){ 
            $("#shipping_address").val('');
            $('#ship_to').val(''); 
            $('#ship_to').select2('')    
        }   
    });
    /*$(document).on("change", "#billing_state", function () {
         var state_id = $(this).val();
         
         $.ajax({
         url: base_url + "general/get_shipping_address_place_change",
         dataType: "JSON",
         method: "POST",
         data: {
         party_id: $("#customer").val(),
         party_type: "customer",
         state_id: state_id
         },
         success: function (result) {
         var data = result["shipping_address_data"];
         $("#shipping_address").html("");
         $("#shipping_address").append('<option value="">Select</option>');
         for (i = 0; i < data.length; i++) {
         var shipping_address = data[i].shipping_address;
         
         var shipping_address_new = shipping_address.replace(
         /\n\r|\\r|\\n|\r|\n|\r\n|\\n\\r|\\r\\n/g,
         "<br/>"
         );
         
         $("#shipping_address").append(
         '<option value="' +
         data[i].shipping_address_id +
         '">' +
         shipping_address_new +
         "</option>"
         );
         }
         
         $("#shipping_address").select2({
         containerCssClass: "wrap"
         });
         $("#shipping_address").change();
         
         }
         });
         
    });*/

     /*  $(document).on('click', 'input[name=apply]:radio', function () {
        var id = $(this).val();
        var state_id = $("#state_id_suppuly_" + id).val();
        var country_type = $("#country_type_" + id).val();
        var country_id = $("#apply_country_id_" + id).val();
        
        $("#shipping_address").val(id);
        if (country_type == 'same') {
            $("#billing_state").val(state_id).change();
        } else {
            $('#billing_state').val('0').change();
        }
        $('#billing_country').val(country_id).change();

        $.ajax({
            url: base_url + "general/get_shipping_address_place_change",
            dataType: "JSON",
            method: "POST",
            data: {
                party_id: $("#customer").val(),
                party_type: "customer",
                state_id: state_id
            },
            success: function (result) {
                var data = result["shipping_address_data"];
                // $("#shipping_address").empty();
                // $("#shipping_address").html("");
                // $("#shipping_address").append('<option value="">Select</option>');
                // for (i = 0; i < data.length; i++) {
                //     var shipping_address = data[i].shipping_address;

                //     var shipping_address_new = shipping_address.replace(
                //             /\n\r|\\r|\\n|\r|\n|\r\n|\\n\\r|\\r\\n/g,
                //             "<br/>"
                //             );

                //     $("#shipping_address").append(
                //             '<option value="' +
                //             data[i].shipping_address_id +
                //             '">' +
                //             shipping_address_new +
                //             "</option>"
                //             );
                // }

                // $("#shipping_address").select2({
                //     containerCssClass: "wrap"
                // });
                // $("#shipping_address").change();

            }
        });
    });
   */