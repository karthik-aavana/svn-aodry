<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">    
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>
                    Dashboard</a>
            </li>
            <li><a href="<?php echo base_url('deposit'); ?>">
                    Loan
                </a></li>
            <li class="active"><!-- Add customer -->
                Edit Loan
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Edit Loan
                        </h3>                                            
                    </div>
                    <form name="form" id="form" method="post" action="<?php echo base_url('loan/edit_loan'); ?>"  encType="multipart/form-data">
                        <div class="box-body">                      
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="loan_code">
                                            Code<span class="validation-color">*</span>
                                        </label>                                        
                                        <input type="text" class="form-control" id="loan_code" name="loan_code" value="<?php echo $data[0]->loan_code; ?>" <?php
                                        if ($access_settings[0]->invoice_readonly == 'yes') {
                                            echo "readonly";
                                        }
                                        ?>>
                                        <span class="validation-color" id="err_loan_code"><?php echo form_error('loan_code'); ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="loan_type">Type <span class="validation-color">*</span>
                                    </label>
                                    <select class="form-control select2" id="cmb_loan_type" name="cmb_loan_type" style="width: 100%;">
                                        <?php
                                        $bank_selected = '';
                                        $others_selected = '';
                                        if ($data[0]->loan_type == 'bank') {
                                            $bank_selected = 'selected';
                                        }

                                        if ($data[0]->loan_type == 'others') {
                                            $others_selected = 'selected';
                                        }
                                        ?>
                                        <option value="bank" <?php echo $bank_selected; ?>>Bank</option>
                                        <option value="others" <?php echo $others_selected; ?>>Others</option>
                                    </select>
                                    <input type="hidden" name="loan_id" id="loan_id" value="<?php echo $data[0]->loan_id; ?>">
                                    <input type="hidden" name="ledger_id" id="ledger_id" value="<?php echo $data[0]->ledger_id; ?>">
                                    <input type="hidden" class="form-control" id="loan_name_used" name="loan_name_used" maxlength="90">
                                    <span class="validation-color" id="err_loan_type"><?php echo form_error('loan_type'); ?></span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="date_of_loan">Date of Loan<span class="validation-color">*</span></label>
                                    <div class="input-group date">
                                        <input class="form-control datepicker" type="text" name="txt_date_of_loan" id="txt_date_of_loan" maxlength="30" value="<?php echo date('d-m-Y', strtotime($data[0]->loan_date)); ?>" />
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div> 
                                    <span class="validation-color" id="err_date_of_loan"><?php echo form_error('date_of_loan'); ?></span>
                                </div>                            
                                <div class="form-group col-md-3" style="display: none" id="name_div">
                                    <label for="loan_name">Loan Name<span class="validation-color">*</span></label>
                                    <input class="form-control" type="text" name="txt_loan_name" id="txt_loan_name" maxlength="30" value="<?php echo $data[0]->others_name; ?>" />
                                    <span class="validation-color" id="err_loan_name"><?php echo form_error('loan_name'); ?></span>
                                </div>
                                <div class="form-group col-md-3" id="bank_div">
                                    <label for="bank">Bank<span class="validation-color">*</span></label>
                                    <select id="cmb_bank" name="cmb_bank" class="form-control select2">
                                        <option value="">Select</option>
                                        <?php
                                        foreach ($bank_account as $key => $value) {
                                            $bank_name = $value->bank_account_id . "/" . $value->ledger_title;
                                            if ($data[0]->loan_bank == $bank_name) {
                                                ?>
                                                <option value='<?php echo $value->bank_account_id . "/" . $value->ledger_title; ?>' selected><?php echo $value->ledger_title; ?> </option>
                                                <?php
                                            } else {
                                                ?>
                                                <option value='<?php echo $value->bank_account_id . "/" . $value->ledger_title; ?>'><?php echo $value->ledger_title; ?> </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <span class="validation-color" id="err_bank"><?php echo form_error('bank'); ?></span>
                                </div>
                            </div>
                            <div class="row"> 
                                <div class="form-group col-md-3" id="status_div" style="display: none">
                                    <label for="status">Status<span class="validation-color">*</span></label>
                                    <select id="cmb_status" name="cmb_status" class="form-control select2">
                                        <option value="">Select</option>
                                        <?php
                                        foreach ($status as $key => $value) {
                                            if ($data[0]->other_staus == $key) {
                                                echo '<option value="' . $key . '" selected>' . $value . '</option>';
                                            } else {
                                                echo '<option value="' . $key . '">' . $value . '</option>';
                                            }
                                        }
                                        ?>                                        
                                    </select>
                                    <span class="validation-color" id="err_status"><?php echo form_error('status'); ?></span>
                                </div>
                                <div class="form-group col-md-3" style="display: none" id="pan_div">
                                    <label for="pan">PAN<span class="validation-color">*</span></label>
                                    <input class="form-control" type="text" name="txt_pan" id="txt_pan" maxlength="30" value="<?php echo $data[0]->pan; ?>" />
                                    <span class="validation-color" id="err_pan"><?php echo form_error('pan'); ?></span>
                                </div>
                                <div class="form-group col-md-3" style="display: none" id="roi_div">
                                    <label for="roi">Rate of Interest<span class="validation-color">*</span></label>
                                    <input class="form-control text-right number" type="text" name="txt_roi" id="txt_roi" maxlength="2" value="<?php echo $data[0]->rate_of_interest; ?>" />
                                    <span class="validation-color" id="err_roi"><?php echo form_error('roi'); ?></span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="comments">Comments<span class="validation-color"></span>
                                    </label>
                                    <textarea class="form-control" id="comments" rows="2" name="comments" maxlength="150"><?php echo $data[0]->comments; ?></textarea>
                                    <span class="validation-color" id="err_comments"><?php echo form_error('comments'); ?></span>
                                </div>
                            </div>            
                            <div class="box-footer">
                                <button type="submit" id="loan_submit" class="btn btn-info">Update</button>
                                <span class="btn btn-default" id="cancel" onclick="cancel('loan')">Cancel</span>
                            </div>
                        </div>                      
                    </form>
                </div>   
            </div>
        </div>
    </section>
</div>
<?php $this->load->view('layout/footer'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('[name=txt_loan_name]').on('blur', function () {
            var loan_name = $(this).val();
            $('[name=loan_name_used]').val('0');
            $('#err_loan_name').text('');
             if(loan_name != ''){
            xhr = $.ajax({
                url: '<?= base_url(); ?>loan/LoanValidation',
                type: 'post',
                data: {loan_name: loan_name, id: 0},
                dataType: 'json',
                success: function (json) {
                    if (json.rows > 0) {
                        $('#err_loan_name').text('Name already used!');
                        $('[name=loan_name_used]').val('1');
                    }
                }, complete: function () {

                }
            });
        }
        });
        $('#cmb_bank').on('change', function () {
            var loan_bank = $(this).val();
            $('#err_bank').text('');
            $('[name=loan_name_used]').val('0');
            if(loan_bank != ''){
            xhr = $.ajax({
                url: '<?= base_url(); ?>loan/BankValidation',
                type: 'post',
                data: {loan_bank: loan_bank, id: 0},
                dataType: 'json',
                success: function (json) {
                    if (json.rows > 0) {
                        $('#err_bank').text('Bank Name already used!');
                        $('[name=loan_name_used]').val('1');
                    }
                }, complete: function () {

                }
            });
        }
        });
        $("#cmb_loan_type").on("change", function (event) {
            var partner_type = $(this).val();
            if (partner_type == 'others') {
                $('#bank_div').hide();
                $('#name_div').show();
                $('#roi_div').show();
                $('#pan_div').show();
                $('#status_div').show();
            } else {
                $('#bank_div').show();
                $('#name_div').hide();
                $('#roi_div').hide();
                $('#pan_div').hide();
                $('#status_div').hide();
            }
        });
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
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.cust_type .select2').select2({
            minimumResultsForSearch: -1
        });
        var partner_type = '<?php echo $data[0]->loan_type; ?>';
        if (partner_type == 'others') {
            $('#bank_div').hide();
            $('#name_div').show();
            $('#roi_div').show();
            $('#pan_div').show();
            $('#status_div').show();
        } else {
            $('#bank_div').show();
            $('#name_div').hide();
            $('#roi_div').hide();
            $('#pan_div').hide();
            $('#status_div').hide();
        }
    });
    $("#loan_submit").click(function (event) {
        var loan_code = $('#loan_code').val() ? $('#loan_code').val() : "";
        var loan_name = $('#txt_loan_name').val() ? $('#txt_loan_name').val() : "";
        var loan_type = $('#cmb_loan_type').val() ? $('#cmb_loan_type').val() : "";
        var bank_name = $('#cmb_bank').val() ? $('#cmb_bank').val() : "";
        var date_of_loan = $('#txt_date_of_loan').val() ? $('#txt_date_of_loan').val() : "";
        var name_regex1 = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
        var name_regex = /^[-a-zA-Z\s0-9 ]+$/;
        var roi = $('#txt_roi').val() ? $('#txt_roi').val() : "";
        var status = $('#cmb_status').val() ? $('#cmb_status').val() : "";
        var pan = $('#txt_pan').val() ? $('#txt_pan').val() : "";
        if (loan_type == null || loan_type == "") {
            $("#err_loan_type").text("Please Select Loan Type.");
            return false;
        } else {
            $("#err_loan_type").text("");
        }
        if (date_of_loan == null || date_of_loan == "") {
            $("#err_date_of_loan").text("Please Select Loan Date.");
            return false;
        } else {
            $("#err_date_of_loan").text("");
        }
        if (loan_type == 'others') {
            if (loan_name == null || loan_name == "") {
                $("#err_loan_name").text("Please Enter Loan Name.");
                return false;
            } else {
                $("#err_loan_name").text("");
                if ($('[name=loan_name_used]').val() > 0) {
                    $("#err_loan_name").text("Name already used!");
                    return false;
                }
            }
            if (!loan_name.match(name_regex1)) {
                $('#err_loan_name').text("Please Enter Valid Loan Name");
                return false;
            } else {
                $("#err_loan_name").text("");
            }
            if (status == null || status == "") {
                $("#err_status").text("Please Select Status.");
                return false;
            } else {
                $("#err_status").text("");
            }
            if (pan == null || pan == "") {
                $("#err_pan").text("Please Enter PAN.");
                return false;
            } else {
                $("#err_pan").text("");
            }


            if (pan != "") {
                if (!pan.match(name_regex)) {
                    $('#err_pan').text("Please Enter Valid Pan Number");
                    return false;
                } else {
                    $("#err_pan").text("");
                }
            } else {
                $("#err_pan").text("");
            }

            if (roi == null || roi == "") {
                $("#err_roi").text("Please Enter Rate of Interest.");
                return false;
            } else {
                $("#err_roi").text("");
            }
        } else {
            if (bank_name == null || bank_name == "") {
                $("#err_bank").text("Please Select Bank.");
                return false;
            } else {
                $("#err_bank").text("");
            }

            $("#err_bank").text("");
            if ($('[name=loan_name_used]').val() > 0) {
                $("#err_bank").text("Bank already used!");
                return false;
            }
        }
    });
</script>