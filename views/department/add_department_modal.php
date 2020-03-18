<div id="add_department_modal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					&times;
				</button>
				<h4>Add Department </h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="department_name_a">Department Code <span class="validation-color">*</span></label>
							<input type="text" class="form-control" id="department_code_a" name="department_code_a" maxlength="50" value="<?php echo $invoice_number; ?>" <?php if ($access_settings[0]->invoice_readonly == 'yes') { echo "readonly"; }?>>
							<span class="validation-color" id="err_department_code_a"></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="department_name_a">Department Name <span class="validation-color">*</span></label>
							<input type="text" class="form-control" id="department_name_a" name="department_name_a" maxlength="50" value="<?php echo set_value('department_name_a'); ?>">
							<span class="validation-color" id="err_department_name_a"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="department_add_submit" class="btn btn-info">
					Add
				</button>
				<button type="button" class="btn btn-info" class="close" data-dismiss="modal">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
	$(document).ready(function() {
		$("#department_add_submit").click(function(event) {
			var department_name_a = $('#department_name_a').val();	
			var department_code_a = $('#department_code_a').val();	
			var first5 = department_name_a.substr(0, 5);
			var department_name_empty = "Please Enter the Department Name.";
			var department_name_invalid = "Please Enter Valid Department Name";
			var department_name_length = "Please Enter Department Name Minimun 3 Character";
			if (department_name_a == null || department_name_a == "") {
				$("#err_department_name_a").text(department_name_empty);
				return !1
			} else {
				$("#err_department_name_a").text("")
			}
			if (!department_name_a.match(general_regex)) {
				$('#err_department_name_a').text(department_name_invalid);
				return !1
			} else {
				$("#err_department_name_a").text("")
			}
			
			if (department_name_a.length < 3) {
				$('#err_department_name_a').text(department_name_length);
				return !1
			} else {
				$("#err_department_name_a").text("")
			}
			
			if(!(department_name_a == null || department_name_a == "")) {
                var i,temp="";
                var addr_trim=$.trim(department_name_a);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_department_name_a").text("Please Enter Valid department Name.");
                            return false; 
                        }
                    }  
                }  
            }
			$.ajax({
				url : base_url + 'department/add_department_modal',
				dataType : 'JSON',
				method : 'POST',
				data : {'department_name' : department_name_a,'department_code' :department_code_a},
				success : function(result) {
					if(result.flag){
						$('#add_department_modal').modal('hide');
						dTable.destroy();
                        dTable = getAllDepartment();
						alert_d.text = result.msg;
						PNotify.success(alert_d);
						$('#department_name_a').val('');
					}else{
						if(result.duplicate == 'duplicate') {
							$('#err_department_name_a').text("Department Name already exist");
							return false;
						} else{
							$('#add_department_modal').modal('hide');
							alert_d.text = result.msg;
							PNotify.error(alert_d);
						}
					}
				},
				error: function(msg){
                    alert_d.text = 'Something Went Wrong';
                    PNotify.error(alert_d);
                }
			});
		});
		$("#department_name_a").on("blur", function(event) {
			var department_name_a = $('#department_name_a').val();
			if (department_name_a == "") {
				$('#err_department_name_a').text("Please Enter department Name");
				return !1
			} else {
				$('#err_department_name_a').text("");
			}
		});
		
		
	}); 
</script>
