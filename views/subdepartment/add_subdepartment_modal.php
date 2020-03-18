<!--subdepartment  Modal -->
<div id="add_subdepartment_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Sub Department</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                	<div class="col-sm-6">
                        <div class="form-group">
                            <label for="subdepartment_code_a">Sub Department Code <span class="validation-color">*</span></label>
                            <input type="input" class="form-control" name="subdepartment_code_a" id="subdepartment_code_a" maxlength="50" value="<?php echo $invoice_number; ?>" <?php if ($access_settings[0]->invoice_readonly == 'yes') { echo "readonly"; }?>>
                            <span class="validation-color" id="err_subdepartment_code_a"><?php echo form_error('subdepartment_code_a'); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="department_a">Department Name<span class="validation-color">*</span></label>                            
                            <select name="department_a" id="department_a" class="select2 form-control">
                            </select>                         
                            <span class="validation-color" id="err_department_a"><?php echo form_error('department_a'); ?></span>
                        </div>
                        </div>
                       </div>
                       <div class="row">
                        <div class="col-sm-6">
                        <div class="form-group">
                            <label for="subdepartment_name_a">Sub Department Name <span class="validation-color">*</span></label>
                            <input type="input" class="form-control" name="subdepartment_name_a" id="subdepartment_name_a" maxlength="50">
                            <span class="validation-color" id="err_subdepartment_name_a"><?php echo form_error('subdepartment_name_a'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="subdepartment_add_submit" class="btn btn-info">Add</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script>
	$(document).on("click", ".add_subdepartment", function() {
		$.ajax({
			url : base_url + 'subdepartment/get_department',
			dataType : 'JSON',
			method : 'POST',
			success : function(result) {			
		var option = "<option value=''>Select Department";
		for (var i = 0; i < result.length; i++) {
					option += "<option value='" + result[i].department_id + "'>" + result[i].department_name + "</option>";
				}
				$('#department_a').html(option);
			}
		});
	});
	$(document).ready(function() {
		$("#subdepartment_add_submit").click(function(event) {			
			var subdepartment_name_empty = "Please Enter Sub Department Name.";
			var subdepartment_name_invalid = "Please Enter Valid Sub Department Name";
			var subdepartment_name_length = "Please Enter Sub Department Name Minimun 3 Character";
			var department_select = "Please Select Department.";
			var subdepartment_name = $('#subdepartment_name_a').val().trim();
			var subdepartment_code = $('#subdepartment_code_a').val().trim();
			var department = $("option:selected",'#department_a').text();
			var department_id = $('#department_a').val();
			var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
			if (department_id == "" || department_id == null) {
				$('#err_department_a').text(department_select);
				return !1
			} else {
				$('#err_department_a').text("")
			}
			$('#subdepartment_name').val(subdepartment_name);
			if (subdepartment_name == null || subdepartment_name == "") {
				$("#err_subdepartment_name_a").text(subdepartment_name_empty);
				return !1
			} else {
				$("#err_subdepartment_name_a").text("")
			}
			if (!subdepartment_name.match(general_regex)) {
				$('#err_subdepartment_name_a').text(subdepartment_name_invalid);
				return !1
			} else {
				$("#err_subdepartment_name_a").text("")
			}
			
			if (subdepartment_name.length < 3) {
				$('#err_subdepartment_name_a').text(subdepartment_name_length);
				return !1
			} else {
				$("#err_subdepartment_name_a").text("")
			}
			if(subdepartment_name == department) {
				$('#err_subdepartment_name_a').text("subdepartment name should not be identical");
				return !1
			}else {
				$("#err_subdepartment_name_a").text("");
			}
			if(!(subdepartment_name == null || subdepartment_name == "")) {
                var i,temp="";
                var addr_trim=$.trim(subdepartment_name);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_subdepartment_name_a").text("Please Enter Valid subdepartment Name.");
                            return false; 
                        }
                    }  
                }  
            }
			$.ajax({
				url : base_url + 'subdepartment/add_subdepartment_modal',
				dataType : 'JSON',
				method : 'POST',
				data : {'subdepartment_name' : subdepartment_name, 'department_id' : department_id, 'subdepartment_code': subdepartment_code },
				success : function(result) {
					if(result.flag){
						$('#add_subdepartment_modal').modal('hide');
                        dTable.destroy();
                        dTable = getAllSubdepartment();
                        alert_d.text = result.msg;
                        PNotify.success(alert_d);
                        $('#subdepartment_name_a').val('');
					}else{
						if(result.msg == 'duplicate'){
							$('#err_subdepartment_name_a').text("Sub Department Name already exist");
                                return !1
                            }else{
                            	$('#add_subdepartment_modal').modal('hide');
                            	alert_d.text = result.msg;
                        		PNotify.error(alert_d);
                            }
					}
				}
			});
		});
		$("#department_a").change(function() {
			var department_a = $('#department_a').val();
			if (department_a == "") {
				$('#err_department_a').text("Please Select Department Name");
				return !1
			} else {
				$('#err_department_a').text("");
			}
		});
		$("#subdepartment_name_a").on("blur", function(event) {
			var subdepartment_name_a = $('#subdepartment_name_a').val();
			if (subdepartment_name_a == "") {
				$('#err_subdepartment_name_a').text("Please Enter Sub Department Name.");
				return !1
			} else {
				$('#err_subdepartment_name_a').text("");
			}
		});
	}); 
</script>
