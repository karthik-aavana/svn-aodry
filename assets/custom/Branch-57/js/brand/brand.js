$(document).ready(function () {    
    $("#err_brand_name").text("");
    var xhr;
     $('#brand_name').on('blur',function(){
            $('#brand_name').trigger('keyup');
        })
        $('#brand_name').on('keyup',function(){
            var nm = $(this).val();
            var id = $('#brand_id').val();
            
            if(nm != ''){
                if(xhr && xhr.readyState != 4){
                    xhr.abort();
                }
                xhr = $.ajax({
                    url: base_url + "brand/validateBrand",
                    dataType: "JSON",
                    method: "POST",
                    data: {brand_name: nm,brand_id: id},
                    success: function (result) {
                        if(result > 0){
                            $('[name=exist]').val(1);
                            $("#err_brand_name").text("Brand name already exist!");
                        }else{
                            $('[name=exist]').val(0);
                            $("#err_brand_name").text("");
                        }
                    }
                })
            }else{
                $('[name=exist]').val(0);
                $("#err_brand_name").text("");
            }
        })
    $("#brand_submit").click(function (event){
        var brand_name = $('#brand_name').val();
        var brand_id = $('#brand_id').val();/*
        var invoice_first_prefix = $('#invoice_first_prefix').val();
        var invoice_last_prefix = $('#invoice_last_prefix').val();
        var invoice_seperation = $('#invoice_seperation').val();
        var invoice_type = $('#invoice_type').val();
        var invoice_creation = $('#invoice_creation').val();
        var brand_invoice_readonly = $('#brand_invoice_readonly').val();*/
       

        if (brand_name == null || brand_name == ""){
            $("#err_brand_name").text("Please add brand name.");
            return false;
        } else if($('[name=exist]').val() == '1'){
            $("#err_brand_name").text("Brand name already exist!");
            return false;
        } else {
            $("#err_brand_name").text("");
        }

        /*if (invoice_first_prefix == null || invoice_first_prefix == ""){
            $("#err_invoice_first_prefix").text("Please Enter Invoice First Prefix.");
            return false;
        } else {
            $("#err_invoice_first_prefix").text("");
        }
        if (invoice_last_prefix == null || invoice_last_prefix == "") {
            $("#err_invoice_last_prefix").text("Please Select Invoice Last Prefix.");
            return false;
        } else{
            $("#err_invoice_last_prefix").text("");
        }

        if (invoice_seperation == null || invoice_seperation == ""){
            $("#err_invoice_seperation").text("Please Select Invoice Seperation.");
            return false;
        } else {
            $("#err_invoice_seperation").text("");
        }

        if (invoice_type == null || invoice_type == "") {
            $("#err_invoice_type").text("Please Select Invoice Type.");
            return false;
        } else {
            $("#err_invoice_type").text("");
        }

        if (invoice_creation == null || invoice_creation == ""){
            $("#err_invoice_creation").text("Please Select Invoice Creation.");
            return false;
        } else {
            $("#err_invoice_creation").text("");
        }

        if (brand_invoice_readonly == null || brand_invoice_readonly == ""){
            $("#err_brand_invoice_readonly").text("Please Select Invoice Readonly.");
            return false;
        } else {
            $("#err_brand_invoice_readonly").text("");
        }*/
        var action = 'add_brand';
        if(brand_id != '' && brand_id != 0 && typeof brand_id != 'undefined'){
            action = 'edit_brand';
        }
        
        $.ajax({
            url: base_url + 'brand/'+action,
            type: 'POST',
            dataType: 'JSON',
            data:{ brand_name: brand_name,brand_id:brand_id,
                /*invoice_first_prefix: invoice_first_prefix,
                invoice_last_prefix: invoice_last_prefix,
                invoice_seperation: invoice_seperation,
                invoice_type: invoice_type,
                invoice_creation: invoice_creation,
                invoice_readonly: brand_invoice_readonly,*/
            },
            success: function (data) {
                alert_d.text = data.msg;
                if(data.flag){
                    PNotify.success(alert_d);
                    $('#brand_popup').modal('hide');
                    $('#brand_popup').find('input').val('');
                    $('#brand_popup').find('select').val('').prop('selected',true).change();
                    BrandTable.destroy();
                    BrandTable = getBrandList();
                    $('#plus_btn').show();
                    $('#filter').hide();
                    $('.filter_body').html('');
                }else{
                    PNotify.error(alert_d);
                }
            }
        });
    });

    $(document).on('click','.editBrand',function(){
        var brand_name = $(this).parent().find('[name=brand_name]').val();
        var brand_id = $(this).parent().find('[name=brand_id]').val();
        /*var invoice_first_prefix = $(this).parent().find('[name=invoice_first_prefix]').val();
        var invoice_last_prefix = $(this).parent().find('[name=invoice_last_prefix]').val();
        var invoice_seperation = $(this).parent().find('[name=invoice_seperation]').val();
        var invoice_type = $(this).parent().find('[name=invoice_type]').val();
        var invoice_creation = $(this).parent().find('[name=invoice_creation]').val();
        var brand_invoice_readonly = $(this).parent().find('[name=invoice_readonly]').val();*/
        $('#brand_popup').find('[name=brand_name]').val(brand_name);
        $('#brand_popup').find('[name=brand_id]').val(brand_id);
       /* $('#brand_popup').find('[name=invoice_first_prefix]').val(invoice_first_prefix);
        $('#brand_popup').find('[name=invoice_last_prefix] option[value='+invoice_last_prefix+']').prop('selected',true).change();
        $('#brand_popup').find('[name=invoice_seperation] option[value="'+invoice_seperation+'"]').prop('selected',true).change();
        $('#brand_popup').find('[name=invoice_creation] option[value='+invoice_creation+']').prop('selected',true).change();
        $('#brand_popup').find('[name=invoice_type] option[value='+invoice_type+']').prop('selected',true).change();
        $('#brand_popup').find('[name=brand_invoice_readonly] option[value='+brand_invoice_readonly+']').prop('selected',true).change();*/
        $('#brand_popup .modal-title').text('Edit Brand');
        $('#brand_popup #brand_submit').text('Update');
        $('#brand_popup').modal('show');
    })

    $('.addBrand').click(function(){
        $('#brand_popup .modal-title').text('Add Brand');
        $('#brand_popup #brand_submit').text('Add');
        $('#brand_popup').find('input').val('');
        $('#brand_popup').find('select').val('').prop('selected',true).change();
        $('#brand_popup').modal('show');
    })
    
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
        $('#brand_invoice_readonly').html(option);
    });
});