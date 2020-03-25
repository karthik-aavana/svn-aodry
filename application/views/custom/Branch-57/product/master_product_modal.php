<!-- Category Modal -->
<div id="master_product_modal" class="modal fade z_index_modal" role="dialog" data-backdrop="static" >
    <div class="modal-dialog modal-md" style="width: 35%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Product</h4>
                </div>
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
                            </span></h1>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="master_product_name">Product Name<span class="validation-color">*</span></label>
                                <input type="input" class="form-control" name="master_product_name" id="master_product_name" maxlength="100" style="width: 400px">
                                <span class="validation-color" id="err_master_product_name"><?php echo form_error('master_product_name'); ?></span>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="add_master_product" class="btn btn-info" >Add</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
    </div>
</div>
<script type="text/javascript">

$(document).ready(function ()
{
var name_regex1 = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
     $("#add_master_product").click(function (event){
         var master_product_name = $('#master_product_name').val().trim();
        if (master_product_name == null || master_product_name == "")
        {
            $("#err_master_product_name").text("Please Enter Product Name.");
            return !1
        } else {
            $("#err_product_name").text("");
        }
        if (!master_product_name.match(name_regex1))
        {
            $('#err_master_product_name').text("Plese Enter Valid  Product Name.");
            return !1
        } else
        {
            $("#err_master_product_name").text("")
        }

        $.ajax({url: base_url + 'product/add_product_master',
                dataType: 'JSON',
                method: 'POST',
                data: {'master_product_name': $('#master_product_name').val()},                                
                success: function (result){
                    if(result == 'duplicate'){
                         $("#err_master_product_name").text("Product Name already exist");
                         return !1;
                    }else {
                        var data = result.data;
                        $('#product_name').html('');
                        $('#product_name').append('<option value="">Select</option>');
                        $('#master_product_name').val('');
                        for (i = 0; i < data.length; i++){
                            if(result.id == data[i].id){
                                $('#product_name').append('<option value="' + data[i].master_product_name + '" selected>' + data[i].master_product_name + '</option>')
                            }else{
                             $('#product_name').append('<option value="' + data[i].master_product_name + '">' + data[i].master_product_name + '</option>')   
                            }
                            
                        }   
                        
                    }
                    $('#master_product_modal').modal('toggle');
                }
            })
     });
 });
</script>