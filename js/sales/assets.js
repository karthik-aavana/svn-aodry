

$(document).ready(function () {

    $("#invoice_to").val("Asset");

    $("#invoice_from").val('');

    $('#acc_bank').hide();
    $('#mod_pay').change(function ()
    {
        var mod_pay = $('#mod_pay').val();
        if (mod_pay == "Bank")
        {
            $('#acc_bank').show();
            $("#invoice_from").val('');

        } else if (mod_pay == "Cash")
        {
            $('#acc_bank').hide();
            $("#invoice_from").val('Cash');
        }
    }).change();

    $('#paying_by').change(function ()
    {
        var mod_pay = $('#mod_pay').val();
        // var acc_name = $('#acc').val();
        var op = $("option:selected", '#paying_by');
        var valSe = $('#paying_by').value;
        var acc = $("option:selected", '#paying_by').text();

        if (mod_pay == "Bank")
        {
            $("#invoice_from").val(acc);

        } else if (mod_pay == "Cash")
        {
            $("#invoice_from").val('Cash');
        }


    }).change();

    $(function () {

        $("#date").change(function (event) {
            var selected = $(this).val();
            $.ajax({
                url: base_url + 'sales_asset/getDateAjaxAsset',
                type: 'POST',
                data: {
                    date: selected
                },
                success: function (data) {
                    var parsedJson = $.parseJSON(data);
                    var type = parsedJson.reference_no;
                    $('#reference_no').val(type);
                }
            });
        }).change();
    });

});



