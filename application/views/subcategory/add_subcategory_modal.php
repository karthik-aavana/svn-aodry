<!--Subcategory  Modal -->
<div id="add_subcategory_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Subcategory</h4>
            </div>
            <div class="modal-body">
            	<div id="loader">
                    <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="category_a">Category Name<span class="validation-color">*</span></label>                            
                            <select name="category_a" id="category_a" class="select2 form-control">
                            </select>                         
                            <span class="validation-color" id="err_category_a"><?php echo form_error('category_a'); ?></span>
                        </div>
                        </div>
                        <div class="col-sm-6">
                        <div class="form-group">
                            <label for="subcategory_name_a">Subcategory Name <span class="validation-color">*</span></label>
                            <input type="input" class="form-control" name="subcategory_name_a" id="subcategory_name_a" maxlength="50">
                            <span class="validation-color" id="err_subcategory_name_a"><?php echo form_error('subcategory_name_a'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="subcategory_add_submit" class="btn btn-info">Add</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script>
	$(document).on("click", ".add_subcategory", function() {
		$.ajax({
			url : base_url + 'subcategory/get_category',
			dataType : 'JSON',
			method : 'POST',
			success : function(result) {			
		var option = "<option value=''>Select Category";
		for (var i = 0; i < result.length; i++) {
					option += "<option value='" + result[i].category_id + "'>" + result[i].category_name + "</option>";
				}
				$('#category_a').html(option);
			}
		});
	});
	$(document).ready(function() {
		$("#subcategory_add_submit").click(function(event) {
			var subcategory_name_empty = "Please Enter Subcategory Name.";
			var subcategory_name_invalid = "Please Enter Valid Subategory Name";
			var subcategory_name_length = "Please Enter Subcategory Name Minimun 3 Character";
			var category_select = "Please Select Category.";
			var subcategory_name = $('#subcategory_name_a').val().trim();
			//var first5 =subcategory_name.substr(0, 5);
			var category = $("option:selected",'#category_a').text();
			var category_id = $('#category_a').val();
			var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
			var atlst_alpha=/[-a-zA-Z]+/;
			if (category_id == "" || category_id == null) {
				$('#err_category_a').text(category_select);
				return !1
			} else {
				$('#err_category_a').text("")
			}
			$('#subcategory_name').val(subcategory_name);
			if (subcategory_name == null || subcategory_name == "") {
				$("#err_subcategory_name_a").text(subcategory_name_empty);
				return !1
			} else {
				$("#err_subcategory_name_a").text("")
			}
			if (!subcategory_name.match(general_regex)) {
				$('#err_subcategory_name_a').text(subcategory_name_invalid);
				return !1
			} else {
				$("#err_subcategory_name_a").text("")
			}
			if (!subcategory_name.match(atlst_alpha)) {
				$('#err_subcategory_name_a').text("Subcategory name must contain one Charecter Atleast.");
				return !1
			} else {
				$("#err_subcategory_name_a").text("")
			}
			if (subcategory_name.length < 3) {
				$('#err_subcategory_name_a').text(subcategory_name_length);
				return !1
			} else {
				$("#err_subcategory_name_a").text("")
			}
			if(subcategory_name == category) {
				$('#err_subcategory_name_a').text("Subcategory name should not be identical");
				return !1
			}else {
				$("#err_subcategory_name_a").text("");
			}
			if(!(subcategory_name == null || subcategory_name == "")) {
                var i,temp="";
                var addr_trim=$.trim(subcategory_name);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_subcategory_name_a").text("Please Enter Valid Subcategory Name.");
                            return false; 
                        }
                    }  
                }  
            }
			$.ajax({
				url : base_url + 'subcategory/add_subcategory_modal',
				dataType : 'JSON',
				method : 'POST',
				data : {'subcategory_name' : subcategory_name, 'category_id_model' : category_id },
				beforeSend: function(){
                    // Show image container
                    $("#loader").show();
                },
				success : function(result) {
					if(result.flag){
						$("#loader").hide();
						$('#add_subcategory_modal').modal('hide');
                        dTable.destroy();
                        dTable = getAllSubcategory();
                        alert_d.text = result.msg;
                        PNotify.success(alert_d);
                        $('#subcategory_name_a').val('');
					}else{
						if(result.msg == 'duplicate'){
							 $("#loader").hide();
							$('#err_subcategory_name_a').text("Subcategory Name already exist");
                                return !1
                            }else{
                            	$("#loader").hide();
                            	$('#add_subcategory_modal').modal('hide');
                            	alert_d.text = result.msg;
                        		PNotify.error(alert_d);
                            }
					}
				}
			});
		});
		$("#category_a").change(function() {
			var category_a = $('#category_a').val();
			if (category_a == "") {
				$('#err_category_a').text("Please Select Category Name");
				return !1
			} else {
				$('#err_category_a').text("");
			}
		});
		$("#subcategory_name_a").on("blur", function(event) {
			var subcategory_name_a = $('#subcategory_name_a').val();
			if (subcategory_name_a == "") {
				$('#err_subcategory_name_a').text("Please Enter Subcategory Name.");
				return !1
			} else {
				$('#err_subcategory_name_a').text("");
			}
		});
	}); 
</script>
