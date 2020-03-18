<!--Subcategory Modal -->
<div id="edit_tax" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Tax</h4>
            </div>
            <form name="form_tax_e" id="form_tax_e" method="post">
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
                                    <label for="tax_name"><!-- Tax Name --> Tax Type <span class="validation-color">*</span></label>
                                     <select class="form-control select2" id="tax_name_e" name="tax_name_e">
                                        <option value="">Select Tax Type</option>
                                        <option value="GST">GST</option>
                                        <option value="TDS">TDS</option>
                                        <option value="TCS">TCS</option>
                                        <option value="CESS">CESS</option>
									</select>                                   
                                    <span class="validation-color" id="err_tax_name"><?php echo form_error('tax_name'); ?></span>
                                    <input type="hidden" class="form-control" id="tax_id_e" name="tax_id_e" value="" >
                                </div>
                            </div>
                            <div class="col-sm-6" id="section_e">
                                <div class="form-group">
                                    <label for="tax_type">Section <span class="validation-color">*</span></label>
                                <select class="form-control select2" id="cmb_section_e" name="cmb_section_e">
                                    <option value="">Select Section</option>
                                    <?php foreach ($tax_section as  $value) {
                                        ?>
                                        <option value="<?php echo $value->section_id; ?>"><?php echo $value->section_name;?></option>
                                        <?php
                                    }
                                    ?>
                                </select>                                 
                                    <span class="validation-color" id="err_tax_section_e"><?php echo form_error('tax_name'); ?></span>
                                </div>
                            </div>                            
                           </div>
                           <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group sales">
                                    <label for="tax_value">Tax Value in % <span class="validation-color">*</span></label>
                                    <div class="input-group">
                                        <input type="text" min="0" max="100" class="form-control text-right number" id="tax_value_e" name="tax_value_e" value="">
                                        <span class="input-group-addon">%</span>
                                    </div>
                            <span class="validation-color" id="err_tax_value_e"><?php echo form_error('tax_value'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="description"><!-- Description --> Description</label>
                                    <textarea rows="2" class="form-control" id="description_e" name="description_e" tabindex="3"></textarea>
                                    <span class="validation-color" id="err_description_e"></span>
                                </div>
                            </div> 
                        </div> 
                  </div>                
            <div class="modal-footer">
					<button type="button" id="edit_tax_submit" class="btn btn-info">
						Update
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
<script>
    $(document).on("click", ".edit_tax", function() {
        var id = $(this).data('id');
        $.ajax({
            url : base_url + 'tax/get_tax_modal/' + id,
            dataType : 'JSON',
            method : 'POST',
            success : function(result) {
                var val = parseFloat(result[0].tax_value);
                $('#tax_id_e').val(result[0].tax_id);
                $('#tax_value_e').val(Number(val).toFixed(2));
                 var tax_na =  result[0].tax_name;
                 var section_id =  result[0].section_id;
                 //$("#tax_name_e").html("<option value='TDS'>TDS</option><option value='GST'>GST</option><option value='TCS'>TCS</option><option value='CESS'>CESS</option>");
                 $('#tax_name_e').val(tax_na).change(); 
                 
                 if(tax_na == 'CESS' || tax_na == 'GST' ){
                    $("#section").hide();
                }else{
                    $('#cmb_section_e').val(section_id).change(); 
                    $("#section").show();
                }
                //$('#tax_name_e [value='+tax_na+']').prop('selected', true);
                $('#description_e').val(result[0].tax_description);
            }
        });
    });
    </script>
    <script src="<?php echo base_url('assets/js/tax/') ?>tax_edit.js"></script>
    <script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
    <script>
    $(document).on("change", "#tax_name_e", function() {        
        var val = $(this).val();
       
        if (val == "CESS" || val == "GST") {
            $('#cmb_section_e').val('').change();  
            $("#section_e").hide();
        } else {
            $("#section_e").show();
        };
    });
    $('.number').keypress(function(event) {
    var $this = $(this);
    if ((event.which != 46 || $this.val().indexOf('.') != -1) &&
       ((event.which < 48 || event.which > 57) &&
       (event.which != 0 && event.which != 8))) {
           event.preventDefault();
    }
    var text = $(this).val();
    if ((event.which == 46) && (text.indexOf('.') == -1)) {
        setTimeout(function() {
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
$('.number').bind("paste", function(e) {
var text = e.originalEvent.clipboardData.getData('Text');
if ($.isNumeric(text)) {
    if ((text.substring(text.indexOf('.')).length > 3) && (text.indexOf('.') > -1)) {
        e.preventDefault();
        $(this).val(text.substring(0, text.indexOf('.') + 3));
   }
}
else {
        e.preventDefault();
     }
});
</script>
