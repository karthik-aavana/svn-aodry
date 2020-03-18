<!-- Category Modal -->
<div id="add_discount" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4>Add Discount</h4>
                </div>
                <div class="modal-body">
                    <div id="loader">
                        <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="discount_name">
                                    Discount Name  <span class="validation-color">*</span>
                                </label>    
                                <input type="text" class="form-control" id="discount_name" name="discount_name" value="Discount" readonly="readonly">
                                <span class="validation-color" id="err_discount_name"><?php echo form_error('discount_name'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="discount_percentage">
                                    Discount Value <span class="validation-color">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-right number" id="discount_percentage_modal" name="discount_percentage_modal">
                                    <span class="input-group-addon percentage_icon">%</span>
                                </div>
                                <span class="validation-color" id="err_discount_percentage_modal"><?php echo form_error('discount_percentage_modal'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-12">
							<div class="form-group">
								<label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" tabindex="3"></textarea>
                                 <span class="validation-color" id="err_discount_description"><?php echo form_error('err_discount_description'); ?></span>
							</div>
						</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="modal_discount_submit" class="btn btn-info" >Add</button>
                    <button type="button"  class="btn btn-info" data-dismiss="modal">Cancel</button>
                </div>
            </div>
    </div>
</div>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var float_num_regex = /^[+-]?([0-9]{1,2}[.])?[0-9]{1,2}$/;
        $("#modal_discount_submit").click(function (event) {
            var discount_name = $('#discount_name').val();
            var discount_percentage = $('#discount_percentage_modal').val();        
            var description = $('#description').val();

            if (discount_percentage == null || discount_percentage == "")
            {
                $("#err_discount_percentage_modal").text("Please Enter Discount Value.");
                $('#discount_percentage_modal').focus();
                return !1
            } else
            {
                $("#err_discount_percentage_modal").text("")
            }
             if (discount_percentage > 100)
            {
                $("#err_discount_percentage_modal").text("Please Enter Discount Value Between 1 to 100");
                return !1
            } else {
                if (!discount_percentage.match(float_num_regex)) {
                    var after_dot = (discount_percentage.toString().split(".")[1]).length;
                    var before_dot= (discount_percentage.toString().split(".")[0]);
                    if(after_dot > 2) {
                       $('#err_discount_percentage_modal').text("Decimal part shouldn't exceed more than 2. ");
                        $('#discount_percentage_modal').focus();
                        return !1 
                    } else {
                    $("#err_discount_percentage_modal").text("")
                    }
                }
            }
            
            $.ajax(
                {
                    url: base_url + 'discount/add_discount_modal',
                    dataType: 'JSON',
                    method: 'POST',
                    data:{'discount_name': discount_name, 'discount_percentage': discount_percentage, 'description': description  },
                    beforeSend: function(){
                     // Show image container
                    $("#loader").show();
                    },   
                    success: function (result) {
                        if(result.flag){
                            $("#loader").hide();
                            $('#add_discount').modal('hide');
                            dTable.destroy();
                            dTable = getAllDiscount();
                            alert_d.text = result.msg;
                            PNotify.success(alert_d);
                            $('#discount_percentage_modal, #description').val('');
                        }else{
                            $("#loader").hide();
                            if(result.msg == 'duplicate'){
                                $('#err_discount_percentage_modal').text("Discount already exist");
                                return !1
                            }else{

                                alert_d.text = result.msg;
                                PNotify.error(alert_d);
                            }
                        }               
                    },
                    error: function(){
                        alert_d.text = 'Something Went Wrong!';
                        PNotify.error(alert_d);
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
