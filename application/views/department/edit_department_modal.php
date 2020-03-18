<div id="edit_department_modal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					&times;
				</button>
				<h4>Edit Department </h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="department_code_e">Department Code <span class="validation-color">*</span></label>
							<input type="text" class="form-control" id="department_code_e" name="department_code_e" maxlength="50" value="" <?php if ($access_settings[0]->invoice_readonly == 'yes') { echo "readonly"; }?>>
							<span class="validation-color" id="err_department_code_e"></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="department_name_e">Department Name <span class="validation-color">*</span></label>
							<input type="hidden" name="department_id" id="department_id" value="">
							<input type="text" class="form-control" id="department_name_e" name="department_name_e" maxlength="50" value="<?php echo set_value('department_name_e'); ?>">
							<span class="validation-color" id="err_department_name_e"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="department_edit_submit" class="btn btn-info" class="close">
					Update
				</button>
				<button type="button" class="btn btn-info" class="close" data-dismiss="modal">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).on("click", ".edit_category", function() {
		var id = $(this).data('id');
		$.ajax({
			url : base_url + 'department/get_department_modal/' + id,
			dataType : 'JSON',
			method : 'POST',
			success : function(result) {
				$('#department_id').val(result[0].department_id);
				$('#department_name_e').val(result[0].department_name);
				$('#department_code_e').val(result[0].department_code);
			}
		});
	});
	$(document).ready(function() {
		var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
		var atlst_alpha=/[-a-zA-Z]+/;
		$("#department_edit_submit").click(function(event) {
			var department_name_e = $('#department_name_e').val();	
			var department_code_e = $('#department_code_e').val();	
			var department_id = $('#department_id').val();
			var first5 = department_name_e.substr(0, 5);
			var department_name_empty = "Please Enter the Department Name.";
			var department_name_invalid = "Please Enter Valid Department Name";
			var department_name_length = "Please Enter Department Name Minimun 3 Character";
			if (department_name_e == null || department_name_e == "") {
				$("#err_department_name_e").text(department_name_empty);
				return !1
			} else {
				$("#err_department_name_e").text("")
			}
			if (!department_name_e.match(general_regex)) {
				$('#err_department_name_e').text(department_name_invalid);
				return !1
			} else {
				$("#err_department_name_e").text("")
			}
			
			if (department_name_e.length < 3) {
				$('#err_department_name_e').text(department_name_length);
				return !1
			} else {
				$("#err_department_name_e").text("")
			}
			
			if(!(department_name_e == null || department_name_e == "")) {
                var i,temp="";
                var addr_trim=$.trim(department_name_e);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_department_name_e").text("Please Enter Valid department Name.");
                            return false; 
                        }
                    }  
                }  
            }
			$.ajax({
				url : base_url + 'department/edit_department_modal',
				dataType : 'JSON',
				method : 'POST',
				data : {'department_name' : department_name_e,'department_code' :department_code_e,'id':department_id},
				success : function(result) {
					setTimeout(function () {// wait for 5 secs(2)
                            $(document).find('[name=check_item]').trigger('change');
                            //location.reload(); // then reload the page.(3)
                        },500);
					if(result.flag){
						$('#edit_department_modal').modal('hide');
						dTable.destroy();
                        dTable = getAllDepartment();
						alert_d.text = result.msg;
						PNotify.success(alert_d);
					}else{
						if(result.duplicate == 'duplicate') {
							$('#err_department_name_e').text("Department Name already exist");
							return false;
						} else{
							$('#edit_department_modal').modal('hide');
							alert_d.text = result.msg;
							PNotify.error(alert_d);
						}
					}
				}
			});
		});
		$("#department_name_e").on("blur", function(event) {
			var department_name_e = $('#department_name_e').val();
			if (department_name_e == "") {
				$('#err_department_name_e').text("Please Enter Department Name");
				return !1
			} else {
				$('#err_department_name_e').text("");
			}
		});
	}); 
</script>
