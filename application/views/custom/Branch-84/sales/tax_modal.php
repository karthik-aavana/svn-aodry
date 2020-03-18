<div id="tax_gst_modal" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Add Tax</h4>
            </div>
            <form name="form" id="taxForm_gst" >
                <div class="modal-body">
                     <div id="loader">
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
                            </span>
                        </h1>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="tax_name_gst">
                                    <!-- Tax Name --> Tax Type <span class="validation-color">*</span>
                                </label>
                                <select class="form-control" id="tax_name_gst" name="tax_name_gst" readonly>
                                    <option value="GST">GST</option>
                                </select>
                                <input type="hidden" name="tax_item_type_gst" id="tax_item_type_gst">
                                <input type="hidden" class="form-control" id="tax_id_gst" name="id" value="0" >
                                <span class="validation-color" id="err_tax_name_gst"><?php echo form_error('tax_name_gst'); ?></span>
                            </div>
                            <input type="hidden" value="" id="cmb_section_gst" name="cmb_section_gst">
                        </div>
                        <div class="col-sm-6" id="section" style="display: none">
                                <div class="form-group">
                                    <label for="tax_type">Section <span class="validation-color">*</span></label>
                                <select class="form-control select2" id="cmb_section_gst" name="cmb_section_gst">
                                    <option value="">Select Section</option>
                                    <?php foreach ($tax_section as  $value) {
                                        ?>
                                        <option value="<?php echo $value->section_id; ?>"><?php echo $value->section_name;?></option>
                                        <?php
                                    }
                                    ?>
                                </select>                                 
                                    <span class="validation-color" id="err_tax_name_gst"><?php echo form_error('tax_name'); ?></span>
                                </div>
                            </div>
                        <div class="col-sm-6">
                                <div class="form-group sales">
                                    <label for="tax_value">Tax Value in % <span class="validation-color">*</span></label>
                                    <div class="input-group">
                                        <input type="number" min="0" max="100" maxlength="5"  onKeyUp="if(this.value>100){this.value='100';}else if(this.value<0){this.value='0';}" class="form-control text-right" id="tax_value_gst" name="tax_value_gst" value="<?php echo set_value('tax_value'); ?>" tabindex="2">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                    <span class="validation-color" id="err_tax_value_gst"><?php echo form_error('tax_value'); ?></span>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="description">
                                    <!-- Description --> Description
                                </label>
                                <textarea class="form-control" id="tax_description" name="tax_description"><?php echo set_value('description_gst'); ?></textarea>
                                <span class="validation-color" id="err_description_gst"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="add_tax_gst_sales" type="submit" class="btn btn-info" data-dismiss="modal">Add</button>
                     <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
var tax_ajax = "yes";
</script>
<script type="text/javascript">
$(document).on("click", '.new_tax', function (e) {
var type = $(this).data('name');
$("#tax_item_type_gst").val(type);
});
</script>
<script src="<?php echo base_url('assets/js/tax/') ?>tax_gst.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#add_tax_gst_sales").click(function (event){
       
            var description = $('#tax_description').val();
            var tax_name = $('#tax_name_gst').val();
            var tax_value = $('#tax_value_gst').val();
            var section_id = $('#cmb_section_gst').val();
            var price_regex = /^\$?[0-9]+(\.[0-9][0-9])?$/;
            var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
           
            if (tax_value == null || tax_value == ""){
                $("#err_tax_value_gst").text("Please Enter Tax Value.");
                return !1
            } else{
                $("#err_tax_value_gst").text("")
            }
            if (!tax_value.match(price_regex)){
                $('#err_tax_value_gst').text(" Please Enter Valid Tax Percentage ");
                return !1
            } else {
                $("#err_tax_value_gst").text("")
            }
            if (tax_exist > 0) {
                $('#err_tax_value_gst').text("The tax rate is already exist.");
                return !1
            } else {
                $('#err_tax_value_gst').text("");
            }
            if (description != ""){
                if (!description.match(general_regex)){
                    $('#err_description_gst').text("Please Enter Valid Description");
                    return !1
                } else{
                    $("#err_description_gst").text("")
                }
            }
            
            if (tax_ajax == "yes"){
                $.ajax({
                    url: base_url + "tax/add_tax_ajax",
                    dataType: 'JSON',
                    method: 'POST',
                    data:{
                        'tax_name': $('#tax_name_gst').val(),
                        'tax_value': $('#tax_value_gst').val(),
                        'description': $('#tax_description').val(),
                        'section_id': $('#cmb_section_gst').val()
                    },
                    beforeSend: function(){
                        // Show image container
                        $("#tax_gst_modal #loader").show();
                    },
                    success: function (result){
                        var tax_item_type = $('#tax_item_type_gst').val();
                        var data = result.data;
                        console.log(data,'data');
                        $("#tax_gst_modal #loader").hide();
                       
                        var txHtml = '<option value="">Select</option>';
                        for (i = 0; i < data.length; i++){
                            txHtml += '<option value="' + data[i].tax_id + "-" + parseFloat(data[i].tax_value) +'" >'+parseFloat(data[i].tax_value) +"%</option>";
                        }

                        $('#sales_table_body').find('tr').each(function(){
                            var item_tax = $(this).find('[name=item_tax]').val();
                            $(this).find('[name=item_tax]').html(txHtml);
                            $(this).find('[name=item_tax]').val(item_tax).attr("selected", "selected");
                            
                        })
                        $("#taxForm")[0].reset();
                        $("#tax_gst_modal").modal('hide');
                    }
                })
            }
        });
    })
</script>