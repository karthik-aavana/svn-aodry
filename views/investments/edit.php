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
            <li><a href="<?php echo base_url('investments'); ?>">
                    Investments
                </a></li>
            <li class="active"><!-- Add customer -->
                Edit Investments
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Edit Investments
                        </h3>                                            
                    </div>
                    <form name="form" id="form" method="post" action="<?php echo base_url('investments/edit_investments'); ?>"  encType="multipart/form-data">
                        <div class="box-body">                      
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="investment_code">Investment Code <span class="validation-color">* </span>
                                        </label>                                        
                                        <input type="text" class="form-control" id="investment_code" name="investment_code" value="<?php echo $data[0]->investments_code; ?>" <?php if ($access_settings[0]->invoice_readonly == 'yes') { echo "readonly"; }?>>
                                        <span class="validation-color" id="err_investment_code"><?php echo form_error('investment_code'); ?></span>
                                    </div>
                                </div>                               
                                <div class="form-group col-md-4">
                                    <label for="investment_name">Investment<span class="validation-color">*</span></label>
                                    <input class="form-control" type="text" name="investment_name" id="investment_name" maxlength="100" value="<?php echo $data[0]->investments_type; ?>" />
                                    <span class="validation-color" id="err_investment_name"><?php echo form_error('investment_name'); ?></span>
                                    <input type="hidden" name="investment_id" id="investment_id" value="<?php echo $data[0]->investments_id; ?>">
                                    <input type="hidden" name="ledger_id" id="ledger_id" value="<?php echo $data[0]->ledger_id; ?>">
                                    <input type="hidden" class="form-control" id="investment_name_used" name="investment_name_used" maxlength="90">
                                </div>
                            </div>                           
                                              
                            <div class="box-footer">
                                <button type="submit" id="investment_submit" class="btn btn-info">Update</button>
                                <span class="btn btn-default" id="cancel" onclick="cancel('investments')">Cancel</span>
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

    $("#investment_submit").click(function (event) {
        var investment_code = $('#investment_code').val() ? $('#investment_code').val() : "";
        var investment_name = $('#investment_name').val() ? $('#investment_name').val() : "";  
        var name_regex1 = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
        
        if (investment_name == null || investment_name == "") {
            $("#err_investment_name").text("Please Enter Investments Name.");
            return false;
        } else {
            $("#investment_name").text("");
            if($('[name=investment_name_used]').val() > 0){
                $("#err_investment_name").text("Name already used!");
                return false;
            }
        }

        if (!investment_name.match(name_regex1)) {
            $('#err_investment_name').text("Please Enter Valid Deposit Name ");
            return false;
        } else {
            $("#err_investment_name").text("");
        }
        
    });
        $('[name=investment_name]').on('blur',function(){            
                var investment_name= $(this).val();  
                var investment_id= $('#investment_id').val();          
                $('[name=investment_name_used]').val('0');
                $('#err_investment_name').text('');
                if(investment_name != ''){
                    xhr = $.ajax({
                        url: '<?= base_url(); ?>investments/investmentValidation',
                        type: 'post',
                        data: {investment_name:investment_name,id:investment_id},
                        dataType: 'json',
                        success: function (json) {
                            if(json.rows > 0){
                                $('#err_investment_name').text('Name already used!');
                                $('[name=investment_name_used]').val('1');
                            }
                        }, complete: function () {
                           
                        }
                    })
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
    });
</script>

