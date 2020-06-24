<div id="edit_discount" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4>Edit Discount</h4>
                </div>
                <div class="modal-body">
                    <div id="loader_coco">
                        <h1 class="ml8">
                            <span class="letters-container">
                                <span class="letters letters-left">
                                    <img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px">
                                </span>
                            </span>
                            <span class="circle circle-white"></span>
                            <span class="circle circle-dark"></span>
                            <span class="circle circle-container">
                            <span class="circle circle-dark-dashed"></span>
                            </span></h1>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="hidden" name="discount_id" id="discount_id">
                                <label for="discount_name_edit">
                                    Discount Name  <span class="validation-color">*</span>
                                </label>
                                 <input type="text" class="form-control" id="discount_name_edit" name="discount_name_edit" value="Discount" readonly="readonly">
                                <span class="validation-color" id="err_discount_name_edit"><?php echo form_error('err_discount_name_edit'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="discount_percentage_modal_edit">
                                    Discount Value <span class="validation-color">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-right number" id="discount_percentage_modal_edit" name="discount_percentage_modal_edit" tabindex="2">
                                    <span class="input-group-addon percentage_icon">%</span>
                                </div>
                                <span class="validation-color" id="err_discount_percentage_edit"><?php echo form_error('err_discount_percentage_edit'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="description_edit">Description</label>
                                <textarea class="form-control" id="description_edit" name="description_edit" tabindex="3"></textarea>
                                 <span class="validation-color" id="err_discount_description_edit"><?php echo form_error('err_discount_description'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="discount_update_modal" class="btn btn-info">Update</button>
                   <button type="button"  class="btn btn-info" data-dismiss="modal">Cancel</button>
                </div>
            </div>      
    </div>
</div>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script type="text/javascript">
    $(document).on("click", ".edit_discount", function () {
        var id = $(this).data('id');
        $.ajax(
                {
                    url: base_url + 'discount/get_discount_modal/' + id,
                    dataType: 'JSON',
                    method: 'POST',
                    success: function (result)
                    {
                        $('#discount_id').val(result[0].discount_id);
                        $('#discount_percentage_modal_edit').val(parseFloat(result[0].discount_value));
                        $('#description_edit').val(result[0].description).change();
                    }
                });
    });
    $(document).ready(function () {
       $("#discount_update_modal").click(function (event) {
        var discount_name = $('#discount_name_edit').val();
        var discount_percentage = $('#discount_percentage_modal_edit').val();        
        var description = $('#description_edit').val();
        var discount_id = $('#discount_id').val();
        var float_num_regex = /^[+-]?([0-9]{1,2}[.])?[0-9]{1,2}$/;
        
        if (discount_percentage == null || discount_percentage == "")
        {
            $("#err_discount_percentage_edit").text("Please Enter Discount Value.");
            return !1
        } else
        {
            $("#err_discount_percentage_edit").text("")
        }

        if (!discount_percentage.match(float_num_regex))
        {
            $('#err_discount_percentage_edit').text(" Please Enter Valid Discount Value. ");
            return !1
        } else
        {
            $("#err_discount_percentage_edit").text("")
        }
        if (discount_percentage > 100)
        {
            $("#err_discount_percentage_edit").text("Please Enter Discount Value Between 1 to 100");
            return !1
        } else
        {
            $("#err_discount_percentage_edit").text("")
        }
        $.ajax(
               {
                    url: base_url + 'discount/update_discount_modal',
                    dataType: 'JSON',
                    method: 'POST',
                    data:{'discount_name': discount_name, 'discount_percentage': discount_percentage, 'description': description,  'id': discount_id },
                     beforeSend: function(){
                     // Show image container
                    $("#loader_coco").show();
                    }, 
                    success: function (result) {
                        setTimeout(function () {// wait for 5 secs(2)
                            $(document).find('[name=check_item]').trigger('change');
                            //location.reload(); // then reload the page.(3)
                        },500);
                        if(result.flag){
                            $("#loader_coco").hide();
                            $('#edit_discount').modal('hide');
                            dTable.destroy();
                            dTable = getAllDiscount();
                            alert_d.text = result.msg;
                            PNotify.success(alert_d);
                        }else{
                            $("#loader_coco").hide();
                            if(result.msg == 'duplicate'){
                                $('#err_discount_percentage_edit').text("Discount already exist");
                                return !1
                            }else{
                                alert_d.text = result.msg;
                                PNotify.error(alert_d);
                            }
                        }
                    },
                    error: function(){
                        $('#loader_coco').hide();
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

