<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">    
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a>
            </li>
            <li><a href="<?php echo base_url('deposit'); ?>"> Fixed Assets </a></li>
            <li class="active"><!-- Add customer -->
                Edit Fixed Assets
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Edit Fixed Assets
                        </h3>                                            
                    </div>
                    <form name="form" id="form" method="post" action="<?php echo base_url('fixed_assets/edit_fixed_assets'); ?>"  encType="multipart/form-data">
                        <div class="box-body">                      
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fixed_assets_code">
                                            Code<span class="validation-color">*</span>
                                        </label>                                        
                                        <input type="text" class="form-control" id="fixed_assets_code" name="fixed_assets_code" value="<?php echo $data[0]->fixed_assets_code; ?>" <?php
                                        if ($access_settings[0]->invoice_readonly == 'yes') {
                                            echo "readonly";
                                        }
                                        ?>>
                                        <span class="validation-color" id="err_fixed_assets_code"><?php echo form_error('fixed_assets_code'); ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-md-3 disable_div">
                                    <label for="assets_type">Particulars <span class="validation-color">*</span>
                                    </label>
                                    <select class="form-control select2" id="cmb_assets_type" name="cmb_assets_type">
                                        <?php
                                        foreach ($type as $key => $value) {
                                            if ($data[0]->particulars == $key) {
                                                echo '<option value="' . $key . '" selected>' . $value . '</option>';
                                            } else {
                                                echo '<option value="' . $key . '">' . $value . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" name="fixed_assets_id" id="fixed_assets_id" value="<?php echo $data[0]->fixed_assets_id; ?>">
                                    <input type="hidden" name="ledger_id" id="ledger_id" value="<?php echo $data[0]->ledger_id; ?>">
                                    <input type="hidden" class="form-control" id="fixed_assets_name_used" name="fixed_assets_name_used" maxlength="90">
                                    <span class="validation-color" id="err_assets_type"><?php echo form_error('assets_type'); ?></span>
                                </div>
                                <div class="form-group col-md-3" >
                                    <label for="asset_name">Name of the asset purchased<span class="validation-color">*</span></label>
                                    <input class="form-control" type="text" name="txt_asset_name" id="txt_asset_name" maxlength="30" value="<?php echo $data[0]->name_of_assets_purchase; ?>" />
                                    <span class="validation-color" id="err_asset_name"><?php echo form_error('asset_name'); ?></span>
                                </div>                            
                                <div class="form-group col-md-3">
                                    <label for="date_of_purchase">Date of purchase<span class="validation-color">*</span></label>
                                    <div class="input-group date">
                                        <input class="form-control datepicker" type="text" name="txt_date_of_purchase" id="txt_date_of_purchase" maxlength="30" value="<?php echo date('d-m-Y', strtotime($data[0]->date_purchase)); ?>" />
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                    <span class="validation-color" id="err_date_of_purchase"><?php echo form_error('date_of_purchase'); ?></span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="date_of_asset_put">Date of asset put to use<span class="validation-color">*</span></label>
                                    <div class="input-group date">
                                        <input class="form-control datepicker" type="text" name="txt_date_of_asset_put" id="txt_date_of_asset_put" maxlength="30" value="<?php echo date('d-m-Y', strtotime($data[0]->date_of_use)); ?>" />
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>                                    
                                    <span class="validation-color" id="err_date_of_asset_put"><?php echo form_error('date_of_asset_put'); ?></span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="rate_of_depr_it">Rate of depreciation (Income tax)<span class="validation-color">*</span></label>
                                    <input class="form-control text-right number" type="text" name="txt_rate_of_depr_it" id="txt_rate_of_depr_it" maxlength="15" value="<?php echo round($data[0]->rate_depreciation_income_tax, 2); ?>" />
                                    <span class="validation-color" id="err_rate_of_depr_it"><?php echo form_error('rate_of_depr_it'); ?></span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="rate_of_depr_comp_act">Rate of depreciation (Companies Act)<span class="validation-color">*</span></label>
                                    <input class="form-control text-right number" type="text" name="txt_rate_of_depr_comp_act" id="txt_rate_of_depr_comp_act" maxlength="15" value="<?php echo round($data[0]->rate_depreciation_company_act, 2); ?>" />
                                    <span class="validation-color" id="err_rate_of_depr_comp_act"><?php echo form_error('rate_of_depr_comp_act'); ?></span>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" id="fixed_asset_submit" class="btn btn-info">Update</button>
                                <span class="btn btn-default" id="cancel" onclick="cancel('fixed_assets')">Cancel</span>
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
        $('[name=txt_asset_name]').on('blur', function () {
            var fixed_asset_name = $(this).val();
            var fixed_asset_id = $('#fixed_assets_id').val();
            $('[name=fixed_assets_name_used]').val('0');
            $('#err_asset_name').text('');
            xhr = $.ajax({
                url: '<?= base_url(); ?>fixed_assets/FixedAssetsValidation',
                type: 'post',
                data: {fixed_asset_name: fixed_asset_name, id: fixed_asset_id},
                dataType: 'json',
                success: function (json) {
                    if (json.rows > 0) {
                        $('#err_asset_name').text('Fixed Asset Name already used!');
                        $('[name=fixed_assets_name_used]').val('1');
                    }
                }, complete: function () {
                }
            });
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
    });
    $("#fixed_asset_submit").click(function (event) {
        var fixed_code = $('#fixed_assets_code').val() ? $('#fixed_assets_code').val() : "";
        var assets_type = $('#cmb_assets_type').val() ? $('#cmb_assets_type').val() : "";
        var asset_name = $('#txt_asset_name').val() ? $('#txt_asset_name').val() : "";
        var date_of_purchase = $('#txt_date_of_purchase').val() ? $('#txt_date_of_purchase').val() : "";
        var date_of_asset_put = $('#txt_date_of_asset_put').val() ? $('#txt_date_of_asset_put').val() : "";
        var rate_of_depr_it = $('#txt_rate_of_depr_it').val() ? $('#txt_rate_of_depr_it').val() : "";
        var rate_of_depr_comp_act = $('#txt_rate_of_depr_comp_act').val() ? $('#txt_rate_of_depr_comp_act').val() : "";
        var name_regex1 = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
        if (fixed_code == null || fixed_code == "") {
            $("#err_deposit_code").text("Please Enter Fixed Assets Code.");
            return false;
        } else {
            $("#err_deposit_code").text("");
        }
        if (assets_type == null || assets_type == "") {
            $("#err_assets_type").text("Please Select Particulars.");
            return false;
        } else {
            $("#err_assets_type").text("");
        }
        if (asset_name == null || asset_name == "") {
            $("#err_asset_name").text("Please Enter Fixed Assets Name.");
            return false;
        } else {
            if ($('[name=fixed_assets_name_used]').val() > 0) {
                $("#err_asset_name").text("Fixed Asset Name already used!");
                return false;
            }
        }
        if (!asset_name.match(name_regex1)) {
            $('#err_asset_name').text("Please Enter Valid Fixed Assets Name.");
            return false;
        } else {
            $("#err_asset_name").text("");
        }
        if (date_of_purchase == null || date_of_purchase == "") {
            $("#err_date_of_purchase").text("Please Select Date of purchase.");
            return false;
        } else {
            $("#err_date_of_purchase").text("");
        }
        if (date_of_asset_put == null || date_of_asset_put == "") {
            $("#err_date_of_asset_put").text("Please Select Date of asset put to use.");
            return false;
        } else {
            $("#err_date_of_asset_put").text("");
        }
        if (rate_of_depr_it == null || rate_of_depr_it == "") {
            $("#err_rate_of_depr_it").text("Please Enter Rate of depreciation (Income tax).");
            return false;
        } else {
            $("#err_rate_of_depr_it").text("");
        }
        if (rate_of_depr_comp_act == null || rate_of_depr_comp_act == "") {
            $("#err_rate_of_depr_comp_act").text("Please Enter Rate of depreciation (Company Act).");
            return false;
        } else {
            $("#err_rate_of_depr_comp_act").text("");
        }
    });
</script>