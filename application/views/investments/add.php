<div id="add_investments_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Add Investment</h4>
            </div>
            <form name="form">
                <div class="modal-body">
                    <div id="loader">
                        <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="investment_code">Investment Code <span class="validation-color">*</span>
                                </label>
                                    <input type="text" class="form-control" id="investment_code" name="investment_code" value="" readonly>
                            </div>
                        </div>                               
                        <div class="form-group col-md-6">
                            <label for="investment_name">Investments<span class="validation-color">*</span></label>
                                <input class="form-control" type="text" name="investment_name" id="investment_name" maxlength="30" />
                                    <span class="validation-color" id="err_investment_name"></span>
                                    <input type="hidden" name="investment_id" id="investment_id" value="0">
                                    <input type="hidden" class="form-control" id="investment_name_used" name="investment_name_used" maxlength="90">
                        </div>
                    </div>      
                </div>
                <div class="modal-footer">
                    <button type="button" id="investment_submit" class="btn btn-info">
                        Add
                    </button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script type="text/javascript">
    $(document).on("click", ".add_investments", function() {
        $.ajax({
            url : base_url + 'investments/get_investmentcode',
            dataType : 'JSON',
            method : 'POST',
            success : function(result) {
                var inv = result.invoice_number;
                $('#investment_code').val(inv);
            }
        });
    });
    var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
    var atlst_alpha=/[-a-zA-Z]+/;
    $(document).ready(function() {
        $("#investment_submit").click(function(event) {
             var investment_code = $('#investment_code').val() ? $('#investment_code').val() : "";
        var investment_name = $('#investment_name').val() ? $('#investment_name').val() : "";  
        var name_regex1 = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
        
        if (investment_name == null || investment_name == "") {
            $("#err_investment_name").text("Please Enter Investments Name.");
            return false;
        } else {
            $("#err_investment_name").text("");
            if($('[name=investment_name_used]').val() > 0){
                $("#err_investment_name").text("Name already used!");
                return false;
            }
        }

        if (!investment_name.match(name_regex1)) {
            $('#err_investment_name').text("Please Enter Valid Investments Name ");
            return false;
        } else {
            $("#err_investment_name").text("");
        }
            $.ajax({
                url : base_url + 'investments/add_investments',
                dataType : 'JSON',
                method : 'POST',
                data : {
                    'investment_code' : investment_code,
                    'investment_name' : investment_name
                },
                 beforeSend: function(){
                    // Show image container
                    $("#loader").show();
                 },
                success : function(result) {
                    if(result.flag){
                        $("#loader").hide();
                        $('#add_investments_modal').modal('hide');
                        dTable.destroy();
                        dTable = getAllInvestment();
                        alert_d.text = result.msg;
                        PNotify.success(alert_d);
                        $('#investment_name').val('');
                    }else{                        
                            $("#loader").hide();
                            $('#add_investments_modal').modal('hide');
                            alert_d.text = result.msg;
                            PNotify.error(alert_d);
                        
                    }
                },
                error: function(msg){
                    alert_d.text = 'Something Went Wrong';
                    PNotify.error(alert_d);
                }
            });
        });
        $('[name=investment_name]').on('blur',function(){            
                var investment_name= $(this).val();            
                $('[name=investment_name_used]').val('0');
                $('#err_investment_name').text('');
                if(investment_name != ''){
                    xhr = $.ajax({
                        url: '<?= base_url(); ?>investments/investmentValidation',
                        type: 'post',
                        data: {investment_name:investment_name,id:0},
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
</script>
