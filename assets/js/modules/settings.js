// Function for round off

function global_round_off(val)

{

    var val=parseFloat(val);

    if (common_settings_round_off == 'yes')

    {

        // var amt = Math.round(val);

        var dat = val.toFixed(common_settings_amount_precision);

        return dat;

    } else

    {

        return val;

    }

}



function round_off_next(val)

{

    var val=parseFloat(val);

    if (common_settings_round_off == 'yes')

    {

         var amt = Math.ceil(val);

        var dat = amt.toFixed(common_settings_amount_precision);

        return dat;

    } else

    {

        return val;

    }

}

function round_off_prev(val)

{

    var val=parseFloat(val);

    if (common_settings_round_off == 'yes')

    {

         var amt = Math.floor(val);

        var dat = amt.toFixed(common_settings_amount_precision);

        return dat;

    } else

    {

        return val;

    }

}



function round_off(val)

{

    var val=parseFloat(val);

    // var amt = Math.round(val);

    var dat = val.toFixed(common_settings_amount_precision);

    return dat;

}



function precise_amount(val)

{

    if (val == "" || isNaN(val))

    {

        val = 0;

    }

    var val=parseFloat(val);
    if(typeof common_settings_amount_precision == 'undefined' || common_settings_amount_precision == ''){
        common_settings_amount_precision = 2;
    }
    var amt = val.toFixed(common_settings_amount_precision);

    var dat = amt;

    return dat;

}



