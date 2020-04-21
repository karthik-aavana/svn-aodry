if (typeof tax_ajax === 'undefined' || tax_ajax === null)
{
    var tax_ajax = "no";
}
$(document).ready(function() {
    $("#submit").click(function(event) {
        var name_regex = /^[-a-zA-Z\s]+$/;
        var uom = $('#uom').val().trim();
        var uom_type = $('#uom_type_a').val().trim();
        var description = $('#description_uom').val().trim();
        if (uom == null || uom == "") {
            $("#err_uom").text("Please Enter UOM");
            return false;
        } else {
            $("#err_uom").text("");
        }
        if (uom_type == null || uom_type == "") {
            $("#err_uom_type").text("Please Select UOM Type");
            return false;
        } else {
            $("#err_uom_type").text("");
        }
        if(uom === description){
           $("#err_description").text("Both UOM and Description shouldn't Identical");
            return false; 
        } else {
            $("#err_description").text("");
        }
        if (!uom.match(name_regex)){
            $('#err_uom').text("Please Enter Valid UOM.");
            return !1
        } else {
            $("#err_uom").text("")
        }
        /*if (!description.match(name_regex) && description !=''){
            $('#err_description').text("Please Enter Valid Description.");
            return !1
        } else {
        $("#err_description").text("")
        }*/
        if(!(description == null || description == "")) {
            var i,temp="";
            var addr_trim=$.trim(description);
            // console.log(addr_trim.length);
            for(i=0;i<addr_trim.length;i++) {
                if(addr_trim[i] == addr_trim[i+1]) {
                    temp +=addr_trim[i];
                    if(temp.length >= 4) {
                       $("#err_description").text("Please Enter Valid Address.");
                        return false; 
                    }
                }  
            }  
        }
        
        $.ajax({ 
                url: base_url + 'uqc/duplicate_check_uqc_modal',
                dataType: 'JSON',
                method: 'POST',
                data:{'uom': uom, 'description': description,'uom_type': uom_type },
                beforeSend: function(){
                                    // Show image container
                                    $("#uom_modal #loader").show();
                                },
               
                success: function (result) {
                     $("#uom_modal #loader").hide();
                    if((result['uom_count'] > 0 && result['uom_type'] =='both') || (result['uom_count'] > 0 && result['uom_type'] ==uom_type)){
                        $('#err_uom').text("UQC already exist in "+result['uom_type']);
                        return !1
                    } else{
                        UpdateUQC();
                         $("#uom_modal").modal('hide');
                    }
                }        
            });
    });
    
    $("#uom").on("blur keyup", function(event) {
        var name_regex = /^[-a-zA-Z\s]+$/;
        var uom = $('#uom').val();
        return !1;
        if (uom == null || uom == "") {
            $("#err_uom").text("Please Enter UOM");
            return false;
        } else {
            $("#err_uom").text("");
        }
    });
    function UpdateUQC(){
        if (tax_ajax == "yes")
        {
            $.ajax(
                    {
                        url: base_url + "uqc/add_uom_ajax",
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'uom': $('#uom').val(),
                                    'description': $('#description_uom').val(),
                                    'uom_type': $('#uom_type_a').val()
                                },
                                //  beforeSend: function(){
                                //     // Show image container
                                //     $("#uom_modal #loader").show();
                                // },
                        success: function (result)

                        {
                             // $("#uom_modal #loader").hide();
                            var tax_item_type = $('#tax_item_type').val();
                            var data = result.data;
                            if (tax_item_type == "product")
                            {
                                $('#product_unit').html('');
                                $('#product_unit').append('<option value="">Select UOM</option>');
                               /* $('#service_unit').append('<option value="0">No Tax</option>');*/
                                for (i = 0; i < data.length; i++)
                                {
                                    var tax_value1 = parseFloat(data[i].tax_value).toFixed(2);
                                    //$('#service_unit').append('<option value="' + data[i].id + '">' + data[i].uom + '-' + data[i].description+'</option>');
                                    $('#product_unit').append('<option value="' + data[i].id + '">' + data[i].uom + '-' + data[i].description+'</option>');
                                }
                                var tax_value = parseFloat(result.tax_value).toFixed(2);
                                
                                /*$('#product_gst_code').val(tax_value);*/
                               // var tax_value = parseFloat(result.tax_value).toFixed(2);
                                //$("#gst_tax").select2().val(result.id + '-' + tax_value).trigger('change.select2');
                                $('#product_unit').val(result.id).attr("selected", "selected");
                                $("#uomForm")[0].reset();
                               
                               // $("#gst_tax").change();
                            } else if (tax_item_type == "service")
                            {
                                $('#service_unit').html('');
                                $('#service_unit').append('<option value="">Select UOM</option>');
                                /*$('#service_unit').append('<option value="0">No Tax</option>');*/
                                for (i = 0; i < data.length; i++)
                                {
                                    var tax_value1 = parseFloat(data[i].tax_value).toFixed(2);
                                    $('#service_unit').append('<option value="' + data[i].id + '">' + data[i].uom + '-' + data[i].description+'</option>');
                                    //$('#product_unit').append('<option value="' + data[i].id + '">' + data[i].uom + '-' + data[i].description+'</option>');
                                }
                                    /*var tax_value = parseFloat(result.tax_value).toFixed(2);*/
                                    $('#service_unit').val(result.id).attr("selected", "selected");
                                    $("#uomForm")[0].reset();
                                    $("#uom_modal").modal('hide');
                                //$("#tds_tax").select2().val(result.id + '-' + tax_value).trigger('change.select2');
                               
                              //  $("#tds_tax").change()
                            }
                        }
                    })
        }
        }
/*
    $("#description").on("blur keyup", function(event) {
            var name_regex = /^[-a-zA-Z\s]+$/;
            var description = $('#description').val();
            if (description == null || description == "") {
                $("#err_description").text("Please Enter Description");
                return false;
            } else {
                $("#err_description").text("");
            }
        });*/
    
    });