<div id="edit_category_modal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					&times;
				</button>
				<h4>Edit Category </h4>
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
					<div class="col-md-6">
						<div class="form-group">
							<input type="hidden" name="category_id" id="category_id">
							<label for ="category_name_a">Category Name <span class="validation-color">*</span></label>
							<input type="text" class="form-control" id="category_name_e" name="category_name_e" maxlength="50" value="<?php echo set_value('category_name_e'); ?>">
							<span class="validation-color" id="err_category_name_e"></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="category_type_e">Category Type <span class="validation-color">*</span></label>
							<select class="form-control custom_select2 select2" id="category_type_e" name="category_type_e">
								<option value="">Select Category Type</option>
								<option value='service'>Service</option>
								<option value='product'>Goods/Products</option>
							</select>
							<span class="validation-color" id="err_category_type_e"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="category_edit_submit" class="btn btn-info" class="close">
					Update
				</button>
				<button type="submit" class="btn btn-info" class="close" data-dismiss="modal">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script type="text/javascript">
	$(document).on("click", ".edit_category", function() {
		var id = $(this).data('id');
		$.ajax({
			url : base_url + 'category/get_category_modal/' + id,
			dataType : 'JSON',
			method : 'POST',
			success : function(result) {
				$('#category_id').val(result[0].category_id);
				$('#category_name_e').val(result[0].category_name);
				$('#category_type_e').val(result[0].category_type).change();
			}
		});
	});
	$(document).ready(function() {
		var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
		var atlst_alpha=/[-a-zA-Z]+/;
		$("#category_edit_submit").click(function(event) {
			var category_id = $('#category_id').val();
			var category_name_e = $('#category_name_e').val();
			//var first5 = category_name_e.substr(0, 5);
			var category_type_e = $('#category_type_e').val();
			var category_name_empty = "Please Enter Category Name";
			var category_name_length = "Please Enter Category Name Minimun 3 Character";
			
			if (category_name_e == null || category_name_e == "") {
				$("#err_category_name_e").text(category_name_empty);
				return !1
			} else {
				$("#err_category_name_e").text("")
			}

			if (!category_name_e.match(general_regex)) {
				$('#err_category_name_e').text("Category Name Invalid");
				return !1
			} else {
				$("#err_category_name_e").text("")
			}


			if (!category_name_e.match(atlst_alpha)) {
				$('#err_category_name_e').text('Category name must contain one Charecter Atleast');
				return !1
			} else {
				$("#err_category_name_e").text("")
			}


			/*if (!category_name_e.match(atlst_alpha)) {
				$('#err_category_name_e').text("Category name must contain one Charecter Atleast.");
				return !1
			} else {
				$("#err_category_name_e").text("")
			}*/

			if (category_name_e.length < 3) {
				$('#err_category_name_e').text(category_name_length);
				return !1
			} else {
				$("#err_category_name_e").text("")
			}

			if (category_type_e == null || category_type_e == "") {
				$("#err_category_type_e").text("Please Select Category Type");
				return !1
			} else {
				$("#err_category_type_e").text("")
			}

			if(!(category_name_e == null || category_name_e == "")) {
                var i,temp="";
                var addr_trim=$.trim(category_name_e);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_category_name_e").text("Please Enter Valid Category Name.");
                            return false; 
                        }
                    }  
                }  
            }
			$.ajax({
				url : base_url + 'category/edit_category_modal',
				dataType : 'JSON',
				method : 'POST',
				data : {
					'id' : category_id,
					'category_name' : category_name_e,
					'category_type' : category_type_e
				},
				beforeSend: function(){
                     // Show image container
                    $("#loader_coco").show();
                }, 
				success : function(result) {
					setTimeout(function () {// wait for 5 secs(2)
                            $(document).find('[name=check_item]').trigger('change');
                            //location.reload(); // then reload the page.(3)
                        },500);
					if(result.flag){
						$('#loader_coco').hide();
						$('#edit_category_modal').modal('hide');
						dTable.destroy();
                        dTable = getAllCategory();
						alert_d.text = result.msg;
						PNotify.success(alert_d);
					}else{
						if(result.duplicate == 'duplicate') {
							$('#err_category_name_e').text("Category Name already exist");
							return false;
						} else{
							$('#edit_category_modal').modal('hide');
							alert_d.text = result.msg;
							PNotify.error(alert_d);
						}
					}
				}
			});
		});
		$("#category_name_e").on("blur", function(event) {
			var category_name_e = $('#category_name_e').val();
			if (category_name_e == "") {
				$('#err_category_name_e').text("Please Enter Category Name");
				return !1
			} else {
				$('#err_category_name_e').text("");
			}
		});
		$("#category_type_e").change(function() {
			var category_type_e = $('#category_type_e').val();
			if (category_type_e == "") {
				$('#err_category_type_e').text("Please Select Category Type.");
				return !1
			} else {
				$('#err_category_type_e').text("");
			}
		});
		$('.select2').select2({
			minimumResultsForSearch : -1
		});
	}); 
</script>
