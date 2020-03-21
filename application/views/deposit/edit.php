<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Edit Deposit
                        </h3>                                            
                    </div>
                    <form name="form" id="form" method="post" action="<?php echo base_url('deposit/edit_deposit'); ?>"  encType="multipart/form-data">
                        <div class="box-body">                      
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="deposit_code">
                                            Code<span class="validation-color">*</span>
                                        </label> 
                                        <input type="text" class="form-control" id="deposit_code" name="deposit_code" value="<?php echo $data[0]->deposit_code; ?>" <?php
                                        if ($access_settings[0]->invoice_readonly == 'yes') {
                                            echo "readonly";
                                        }
                                        ?>> 
                                        <span class="validation-color" id="err_deposit_code"><?php echo form_error('deposit_code'); ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="deposit_type">Deposit Type <span class="validation-color">*</span>
                                    </label>
                                    <select class="form-control select2" id="cmb_deposit_type" name="cmb_deposit_type" style="width: 100%;">
                                        <?php
                                        if($data[0]->deposit_type == 'others'){
                                            $sel_others = 'selected';
                                        }else{
                                            $sel_others = '';
                                        }

                                        if($data[0]->deposit_type == 'recurring deposit'){
                                            $sel_rd = 'selected';
                                        }else{
                                            $sel_rd = '';
                                        }

                                        if($data[0]->deposit_type == 'fixed deposit'){
                                            $sel_fd = 'selected';
                                        }else{
                                            $sel_fd = '';
                                        }
                                        ?>
                                        <option value="fixed deposit" <?php echo $sel_fd;?>>Fixed Deposit</option>
                                        <option value="recurring deposit"  <?php echo $sel_rd;?>>Recurring Deposit</option>
                                        <option value="others" <?php echo $sel_others;?>>Others</option>
                                    </select>
                                    <input type="hidden" name="deposit_id" id="deposit_id" value="<?php echo $data[0]->deposit_id; ?>">
                                    <input type="hidden" class="form-control" id="deposit_name_used" name="deposit_name_used" maxlength="90">
                                    <span class="validation-color" id="err_deposit_type"><?php echo form_error('deposit_type'); ?></span>
                                    <input type="hidden" name="ledger_id" id="ledger_id" value="<?php echo $data[0]->ledger_id; ?>">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="date_of_deposit">Date of deposit<span class="validation-color">*</span></label>
                                    <div class="input-group date">
                                        <input class="form-control datepicker" type="text" name="txt_date_of_deposit" id="txt_date_of_deposit" maxlength="30"  value="<?php echo date('d-m-Y', strtotime($data[0]->deposit_date)); ?>" />
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                    <span class="validation-color" id="err_date_of_deposit"><?php echo form_error('date_of_deposit'); ?></span>
                                </div>                           
                                <div class="form-group col-md-3" style="display: none" id="name_div">
                                    <label for="deposit_name">Deposit Name<span class="validation-color">*</span></label>
                                    <input class="form-control" type="text" name="txt_deposit_name" id="txt_deposit_name" maxlength="30" value="<?php echo $data[0]->others_name; ?>" />
                                    <span class="validation-color" id="err_deposit_name"><?php echo form_error('deposit_name'); ?></span>
                                </div>

                                <div class="form-group col-md-3" id="bank_div">
                                    <label for="bank">Bank<span class="validation-color">*</span></label>
                                    <select id="cmb_bank" name="cmb_bank" class="form-control select2">
                                        <option value="">Select</option>
                                        <?php
                                        foreach ($bank_account as $key => $value) {
                                            $title = $value->bank_account_id . "/" . $value->ledger_title;
                                            if ($data[0]->deposit_bank == $title) {
                                                ?>
                                                <option value='<?php echo $value->bank_account_id . "/" . $value->ledger_title ?>' selected><?php echo $value->ledger_title; ?> </option>
                                                <?php
                                            } else {
                                                ?>
                                                <option value='<?php echo $value->bank_account_id . "/" . $value->ledger_title ?>'><?php echo $value->ledger_title; ?> </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <span class="validation-color" id="err_bank"><?php echo form_error('bank'); ?></span>
                                </div>
                            </div>
                            <div class="row"> 
                                <div class="form-group col-md-12">
                                    <label for="comments">Comments</label>
                                    <textarea class="form-control" id="comments" rows="2" name="comments" maxlength="150"><?php echo $data[0]->comments; ?></textarea>
                                    <span class="validation-color" id="err_comments"><?php echo form_error('comments'); ?></span>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" id="deposit_submit" class="btn btn-info add_deposit">Update</button>
                                <span class="btn btn-default" id="cancel" onclick="cancel('deposit')">Cancel</span>
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
    var type = '<?php echo $data[0]->deposit_type; ?>';
    $(document).ready(function () {
        $('.add_deposit').attr('disabled', true);
        $('[name=txt_deposit_name]').on('blur', function () {

            var deposit_name = $(this).val();
            var deposit_id = $('#deposit_id').val();
            $('[name=deposit_name_used]').val('0');
            $('#err_deposit_name').text('');
            if(deposit_name != ''){
            xhr = $.ajax({
                url: '<?= base_url(); ?>deposit/DepositValidation',
                type: 'post',
                data: {deposit_name: deposit_name, id: deposit_id},
                dataType: 'json',
                success: function (json) {
                    if (json.rows > 0) {
                        $('#err_deposit_name').text('Name already used!');
                        $('[name=deposit_name_used]').val('1');
                    }else{
                        $('.add_deposit').attr('disabled', false);
                    }
                }, complete: function () {

                }
            })
            }
        });

        $('#cmb_bank, #cmb_deposit_type').on('change', function () {

            var deposit_bank = $(this).val();
            var deposit_id = $('#deposit_id').val();
            var deposit_tye = $('#cmb_deposit_type').val();
            $('[name=deposit_name_used]').val('0');
            $('#err_bank').text('');
            if(deposit_bank != ''){
            xhr = $.ajax({
                url: '<?= base_url(); ?>deposit/BankValidation',
                type: 'post',
                data: {deposit_bank: deposit_bank, id: deposit_id},
                dataType: 'json',
                success: function (json) {
                    if (json.rows > 0) {
                        $('#err_bank').text('Bank Name already used!');
                        $('[name=deposit_name_used]').val('1');
                    }
                }, complete: function () {

                }
            })
            }
        });
        $("#cmb_deposit_type").on("change", function (event) {
            var partner_type = $(this).val();
            if (partner_type == 'others') {
                $('#bank_div').hide();
                $('#name_div').show();
                $('#cmb_bank').val('');
            } else {
                $('#bank_div').show();
                $('#name_div').hide();
                $('#txt_deposit_name').val('');
            }
        });

        if (type == 'others') {
            $('#bank_div').hide();
            $('#name_div').show();
            $('#cmb_bank').val('');
        } else {
            $('#bank_div').show();
            $('#name_div').hide();
            $('#txt_deposit_name').val('');
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
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.cust_type .select2').select2({
            minimumResultsForSearch: -1
        });
    });
    $("#deposit_submit").click(function (event) {
        var deposit_code = $('#deposit_code').val() ? $('#deposit_code').val() : "";
        var deposit_name = $('#txt_deposit_name').val() ? $('#txt_deposit_name').val() : "";
        var deposit_type = $('#cmb_deposit_type').val() ? $('#cmb_deposit_type').val() : "";
        var bank_name = $('#cmb_bank').val() ? $('#cmb_bank').val() : "";
        var date_of_deposit = $('#txt_date_of_deposit').val() ? $('#txt_date_of_deposit').val() : "";
        var name_regex1 = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
        var eligible_claim = $("input[type=radio][name='eligible_claim']:checked").val();
        if (deposit_type == null || deposit_type == "") {
            $("#err_deposit_type").text("Please Select Deposit Type.");
            return false;
        } else {
            $("#err_deposit_type").text("");
        }
        if (deposit_type == 'others') {
            if (deposit_name == null || deposit_name == "") {
                $("#err_deposit_name").text("Please Enter Deposit Name.");
                return false;
            } else {
                $("#deposit_name").text("");
                if ($('[name=deposit_name_used]').val() > 0) {
                    $("#err_deposit_name").text("Name already used!");
                    return false;
                }
            }
            if (!deposit_name.match(name_regex1)) {
                $('#err_deposit_name').text("Please Select Valid Deposit Name");
                return false;
            } else {
                $("#err_deposit_name").text("");
            }
        } else {
            if (bank_name == null || bank_name == "") {
                $("#err_bank").text("Please Select Bank.");
                return false;
            } else {
                $("#err_bank").text("");
            }

            $("#err_bank").text("");
            if ($('[name=deposit_name_used]').val() > 0) {
                $("#err_bank").text("Bank Name already used!");
                return false;
            }
        }
        if (date_of_deposit == null || date_of_deposit == "") {
            $("#err_date_of_deposit").text("Please Select Deposit Date.");
            return false;
        } else {
            $("#err_date_of_deposit").text("");
        }
    });
</script>