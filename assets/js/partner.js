
$(document).ready(function (){
    $("input[type=radio][name=eligible_claim].minimal").on("ifChanged",
        function () {
            var eligible_claim = $("input[type=radio][name='eligible_claim']:checked").val();
            if(eligible_claim == 'yes'){
                $('#remuneration_div').show();
                $('#txt_remuneration').val('');
            }else{
                $('#remuneration_div').hide();
                $('#txt_remuneration').val('');
            }
        });


         $('.number').keypress(function (event) {
        var $this = $(this);
        if ((event.which != 46 || $this.val().indexOf('.') != -1) &&
                ((event.which < 48 || event.which > 57) &&
                        (event.which != 0 && event.which != 8))) {
            event.preventDefault();
        }
        var text = $(this).val();
        if ((event.which == 46) && (text.indexOf('.') == -1)) {
            setTimeout(function () {
                if ($this.val().substring($this.val().indexOf('.')).length > 3) {

                    $this.val($this.val().substring(0, $this.val().indexOf('.') + 3));

                }

            }, 1);

        }

        if ((text.indexOf('.') != -1) &&
                (text.substring(text.indexOf('.')).length > 2) &&
                (event.which != 0 && event.which != 8) &&
                ($(this)[0].selectionStart >= text.length - 2)) {
            event.preventDefault();
        }
    });


     $('#partner_type').on('change',function(){    

        var type = $(this).val();
        if(type == 'partner'){
            $('#div_partner_initial_capt_amt').show();
            $('#eligible_claim').show();
            $('#remuneration_div').show();
            $('#div_partner_capt_roi').show();
            $('#div_partner_profit').show();
            $('#div_dir_date_app').hide();
            $('#div_dir_type').hide();
            $('#div_share_nos').hide();
            $('#div_share_value').hide();
            $('#div_share_premium').hide();
            $('#div_share_paid_capital').hide();
            $('#lbl_partner_name').html('Partner Name<span class="validation-color">*</span>');
            $('#div_share_type').hide();
            $('#txt_roi_capital_intrest').val('');
            $('#txt_share_profit').val('');
            $('#txt_date_apptmnt').val('');
            $('#type_director').val('');
            $('#txt_no_of_shares').val('');
            $('#txt_face_value').val('');
            $('#txt_security_premimum').val('');
            $('#txt_amount_paid_capital').val('');
            $('#txt_capital_amount').val('');
            $('#cmb_share_type').val('');
        }else if(type == 'shareholder'){
            $('#div_partner_initial_capt_amt').hide();            
            $('#eligible_claim').hide();
            $('#remuneration_div').hide();
            $('#div_partner_capt_roi').hide();            
            $('#div_partner_profit').hide();            
            $('#div_dir_date_app').hide();            
            $('#div_dir_type').hide();            
            $('#div_share_nos').show();            
            $('#div_share_value').show();            
            $('#div_share_premium').show();            
            $('#div_share_paid_capital').show();            
            $('#txt_remuneration').val('');
            $('#lbl_partner_name').html('Shareholder Name<span class="validation-color">*</span>');
            $('#div_share_type').show();
            $('#txt_roi_capital_intrest').val('');
            $('#txt_share_profit').val('');
            $('#txt_date_apptmnt').val('');
            $('#type_director').val('');
            $('#txt_no_of_shares').val('');
            $('#txt_face_value').val('');
            $('#txt_security_premimum').val('');
            $('#txt_amount_paid_capital').val('');
            $('#txt_capital_amount').val('');
            $('#cmb_share_type').val('');
        }else if(type == 'director'){
            $('#div_partner_initial_capt_amt').hide();
            $('#eligible_claim').hide();
            $('#remuneration_div').hide();
            $('#div_partner_capt_roi').hide();
            $('#div_partner_profit').hide();
            $('#div_dir_date_app').show();
            $('#div_dir_type').show();
            $('#div_share_nos').hide();
            $('#div_share_value').hide();
            $('#div_share_premium').hide();
            $('#div_share_paid_capital').hide();
            $('#txt_remuneration').val('');
            $('#lbl_partner_name').html('Director Name<span class="validation-color">*</span>');
            $('#div_share_type').hide();
            $('#txt_roi_capital_intrest').val('');
            $('#txt_share_profit').val('');
            $('#txt_date_apptmnt').val('');
            $('#type_director').val('');
            $('#txt_no_of_shares').val('');
            $('#txt_face_value').val('');
            $('#txt_security_premimum').val('');
            $('#txt_amount_paid_capital').val('');
            $('#txt_capital_amount').val('');
            $('#cmb_share_type').val('');
        }

     });

});