<!--Subcategory Modal -->
<div id="edit_subdepartment_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Sub Department</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                	<div class="col-sm-6">
                        <div class="form-group">
                            <label for="subdepartment_code_e">Sub Department Code <span class="validation-color">*</span></label>
                            <input type="input" class="form-control" name="subdepartment_code_e" id="subdepartment_code_e" maxlength="50" value="<?php echo $invoice_number; ?>" <?php if ($access_settings[0]->invoice_readonly == 'yes') { echo "readonly"; }?>>
                            <span class="validation-color" id="err_subdepartment_code_e"><?php echo form_error('subdepartment_code_e'); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="department_e">Department Name<span class="validation-color">*</span></label>                            
                            <select name="department_e" id="department_e" class="select2 form-control">
                            </select>                         
                            <span class="validation-color" id="err_department_e"><?php echo form_error('department_e'); ?></span>
                        </div>
                        </div>
                       </div>
                       <div class="row">
                        <div class="col-sm-6">
                        <div class="form-group">
                            <label for="subdepartment_name_e">Sub Department Name <span class="validation-color">*</span></label>
                            <input type="input" class="form-control" name="subdepartment_name_e" id="subdepartment_name_e" maxlength="50">
                            <input type="hidden" name="sub_department_id" id="sub_department_id" value="">
                            <span class="validation-color" id="err_subdepartment_name_e"><?php echo form_error('subdepartment_name_e'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="subdepartment_edit_submit" class="btn btn-info" >Update</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script>
	$(document).on("click", ".edit_subdepartment", function() {
		var id = $(this).data('id');
		// console.log(id);
		$.ajax({
			url : base_url + 'subdepartment/get_department',
			dataType : 'JSON',
			method : 'POST',
			success : function(result) {
				var option = "<option value=''>Select Department";
				for (var i = 0; i < result.length; i++) {
					option += "<option value='" + result[i].department_id + "'>" + result[i].department_name + "</option>";
				}
				$('#department_e').html(option);
				$.ajax({
					url : base_url + 'subdepartment/get_subdepartment_modal/' + id,
					dataType : 'JSON',
					method : 'POST',
					success : function(data) {
						$('#sub_department_id').val(data[0].sub_department_id);
						$('#subdepartment_code_e').val(data[0].sub_department_code);
						$('#subdepartment_name_e').val(data[0].sub_department_name);
						$('#department_e').val(data[0].department_id);
						$('#department_e').change();
					}
				});
			}
		});
	});
	$(document).ready(function() {
		$("#subdepartment_edit_submit").click(function(event) {
			var subdepartment_id = $('#sub_department_id').val();
			var subdepartment_name_empty = "Please Enter Sub Department Name.";
			var subdepartment_name_invalid = "Please Enter Valid Sub Department Name";
			var subdepartment_name_length = "Please Enter Subdepartment Name Minimun 3 Character";
			var department_select = "Please Select Department.";
			var subdepartment_name = $('#subdepartment_name_e').val().trim();
			var subdepartment_code = $('#subdepartment_code_e').val().trim();
			var department = $("option:selected",'#department_e').text();
			var department_id = $('#department_e').val();
			var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
			if (department_id == "" || department_id == null) {
				$('#err_department_e').text(department_select);
				return !1
			} else {
				$('#err_department_e').text("")
			}
			$('#subdepartment_name').val(subdepartment_name);
			if (subdepartment_name == null || subdepartment_name == "") {
				$("#err_subdepartment_name_e").text(subdepartment_name_empty);
				return !1
			} else {
				$("#err_subdepartment_name_e").text("")
			}
			if (!subdepartment_name.match(general_regex)) {
				$('#err_subdepartment_name_e').text(subdepartment_name_invalid);
				return !1
			} else {
				$("#err_subdepartment_name_e").text("")
			}
			
			if (subdepartment_name.length < 3) {
				$('#err_subdepartment_name_e').text(subdepartment_name_length);
				return !1
			} else {
				$("#err_subdepartment_name_e").text("")
			}
			if(subdepartment_name == department) {
				$('#err_subdepartment_name_e').text("subdepartment name should not be identical");
				return !1
			}else {
				$("#err_subdepartment_name_e").text("");
			}
			if(!(subdepartment_name == null || subdepartment_name == "")) {
                var i,temp="";
                var addr_trim=$.trim(subdepartment_name);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_subdepartment_name_e").text("Please Enter Valid subdepartment Name.");
                            return false; 
                        }
                    }  
                }  
            }
			$.ajax({
				url : base_url + 'subdepartment/edit_subdepartment_modal',
				dataType : 'JSON',
				method : 'POST',
				data : {'subdepartment_name' : subdepartment_name, 'department_id' : department_id, 'subdepartment_code': subdepartment_code,'id' : subdepartment_id },
				success : function(result) {
					setTimeout(function () {// wait for 5 secs(2)
                            $(document).find('[name=check_item]').trigger('change');
                            //location.reload(); // then reload the page.(3)
                        },500);
					if(result.flag){
						$('#edit_subdepartment_modal').modal('hide');
                        dTable.destroy();
                        dTable = getAllSubdepartment();
                        alert_d.text = result.msg;
                        PNotify.success(alert_d);
					}else{
						if(result.msg == 'duplicate'){
							$('#err_subdepartment_name_e').text("Subdepartment Name already exist");
                                return !1
                        }else{
                        	$('#edit_subdepartment_modal').modal('hide');
                        	alert_d.text = result.msg;
                    		PNotify.error(alert_d);
                        }
					}
				}
			});
		});
		$("#department_e").change(function() {
			var department_e = $('#department_e').val();
			if (department_e == "") {
				$('#err_department_e').text("Please Select Department Name");
				return !1
			} else {
				$('#err_department_e').text("");
			}
		});
		$("#subdepartment_name_e").on("blur", function(event) {
			var subdepartment_name_e = $('#subdepartment_name_e').val();
			if (subdepartment_name_e == "") {
				$('#err_subdepartment_name_e').text("Please Enter Sub Department.");
				return !1
			} else {
				$('#err_subdepartment_name_e').text("");
			}
		});
	}); 
</script>
