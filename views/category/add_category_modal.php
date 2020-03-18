<div id="add_category_modal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					&times;
				</button>
				<h4>Add Category </h4>
			</div>
			<div class="modal-body">
				<div id="loader">
                    <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                </div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="category_name_a">Category Name <span class="validation-color">*</span></label>
							<input type="text" class="form-control" id="category_name_a" name="category_name_a" maxlength="50" value="<?php echo set_value('category_name_a'); ?>">
							<span class="validation-color" id="err_category_name_a"></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="category_type_a">Category Type <span class="validation-color">*</span></label>
							<select class="form-control select2" id="category_type_a" name="category_type_a">
								<option value="">Select Category Type</option>
								<option value='service'>Service</option>
								<option value='product'>Goods/Products</option>
							</select>
							<span class="validation-color" id="err_category_type_a"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="category_add_submit" class="btn btn-info">
					Add
				</button>
				<button type="button" class="btn btn-info" class="close" data-dismiss="modal">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script type="text/javascript">
	var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
	//var alphnum_regex = /^[a-zA-Z0-9]+$/;
	//var alphnum_space_regex = /^[a-zA-Z0-9 ]*$/;
	var atlst_alpha=/[-a-zA-Z]+/;
	$(document).ready(function() {
		$("#category_add_submit").click(function(event) {
			var category_name_a = $('#category_name_a').val();
			var category_type_a = $('#category_type_a').val();
			var first5 = category_name_a.substr(0, 5);
			var category_name_empty = "Please Enter the Category Name.";
			var category_name_invalid = "Please Enter Valid Category Name";
			var category_name_length = "Please Enter Category Name Minimun 3 Character";
			if (category_name_a == null || category_name_a == "") {
				$("#err_category_name_a").text(category_name_empty);
				return !1
			} else {
				$("#err_category_name_a").text("")
			}
			if (!category_name_a.match(general_regex)) {
				$('#err_category_name_a').text(category_name_invalid);
				return !1
			} else {
				$("#err_category_name_a").text("")
			}
			if (!category_name_a.match(atlst_alpha)) {
				$('#err_category_name_a').text('Category name must contain one Charecter Atleast');
				return !1
			} else {
				$("#err_category_name_a").text("")
			}
			if (category_name_a.length < 3) {
				$('#err_category_name_a').text(category_name_length);
				return !1
			} else {
				$("#err_category_name_a").text("")
			}
			if (category_type_a == null || category_type_a == "") {
				$("#err_category_type_a").text("Please Select Category Type");
				return !1
			} else {
				$("#err_category_type_a").text("");
			}
			if(!(category_name_a == null || category_name_a == "")) {
                var i,temp="";
                var addr_trim=$.trim(category_name_a);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_category_name_a").text("Please Enter Valid Category Name.");
                            return false; 
                        }
                    }  
                }  
            }
			$.ajax({
				url : base_url + 'category/add_category_modal',
				dataType : 'JSON',
				method : 'POST',
				data : {
					'category_name' : category_name_a,
					'category_type' : category_type_a
				},
				 beforeSend: function(){
                    // Show image container
                    $("#loader").show();
                 },
				success : function(result) {
					if(result.flag){
						$("#loader").hide();
						$('#add_category_modal').modal('hide');
						dTable.destroy();
                        dTable = getAllCategory();
						alert_d.text = result.msg;
						PNotify.success(alert_d);
						$('#category_name_a').val('');
						$('#category_type_a').prop('selectedIndex',0);
                        $('#category_type_a').select2();
					}else{
						if(result.duplicate == 'duplicate') {
							$('#err_category_name_a').text("Category Name already exist");
							return false;
						} else{
							$("#loader").hide();
							$('#add_category_modal').modal('hide');
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
		$("#category_name_a").on("blur", function(event) {
			var category_name_a = $('#category_name_a').val();
			if (category_name_a == "") {
				$('#err_category_name_a').text("Please Enter Category Name");
				return !1
			} else {
				$('#err_category_name_a').text("");
			}
		});
		$("#category_type_a").change(function() {
			var category_type_a = $('#category_type_a').val();
			if (category_type_a == "") {
				$('#err_category_type_a').text("Please Select Category Type.");
				return !1
			} else {
				$('#err_category_type_a').text("");
			}
		});
		$('.select2').select2({
			minimumResultsForSearch : -1
		});
	}); 
</script>
