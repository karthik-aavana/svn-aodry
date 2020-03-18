<!--Subcategory Modal -->
<div id="edit_subcategory_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Subcategory</h4>
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
                    <div class="col-sm-6">
                        <div class="form-group">
                            <input type="hidden" name="subcategory_id" id="subcategory_id">
                            <label for="category_e">Category <span class="validation-color">*</span></label>
                            <select name="category_e" id="category_e" class="select2 form-control">
                            </select>
                            <span class="validation-color" id="err_category_e"><?php echo form_error('category_e'); ?></span>
                        </div>
                        </div>
                        <div class="col-sm-6">
                        <div class="form-group">
                            <label for="subcategory_name_e">Subcategory Name <span class="validation-color">*</span></label>
                            <input type="input" class="form-control" name="subcategory_name_e" id="subcategory_name_e" maxlength="50">
                            <span class="validation-color" id="err_subcategory_name_e"><?php echo form_error('subcategory_name_e'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="subcategory_edit_submit" class="btn btn-info" >Update</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script>
	$(document).on("click", ".edit_subcategory", function() {
		var id = $(this).data('id');
		// console.log(id);
		$.ajax({
			url : base_url + 'subcategory/get_category',
			dataType : 'JSON',
			method : 'POST',
			success : function(data) {
				var option = "<option value=''>Select Category</option>";
				for (var i = 0; i < data.length; i++) {
					option += "<option value='" + data[i].category_id + "'>" + data[i].category_name + "</option>";
				}
				$('#category_e').html(option);
				$.ajax({
					url : base_url + 'subcategory/get_subcategory_modal/' + id,
					dataType : 'JSON',
					method : 'POST',
					success : function(result) {
						$('#subcategory_id').val(result[0].sub_category_id);
						$('#subcategory_name_e').val(result[0].sub_category_name);
						$('#category_e').val(result[0].category_id);
						$('#category_e').change();
					}
				});
			}
		});
	});
	$(document).ready(function() {
		$("#subcategory_edit_submit").click(function(event) {
			var subcategory_id = $('#subcategory_id').val();
			var subcategory_name_empty = "Please Enter Subcategory.";
			var subcategory_name_invalid = "Please Enter Valid Subategory Name";
			var subcategory_name_length = "Please Enter Subcategory Name Minimun 3 Character";
			var category_select = "Please Select Category.";
			var subcategory_name = $('#subcategory_name_e').val().trim();
			//var first5 =subcategory_name.substr(0, 5);
			var category = $("option:selected",'#category_e').text().trim();
			var category_id = $('#category_e').val();
			var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
			var atlst_alpha=/[-a-zA-Z]+/;
			if (category_id == "" || category_id == null) {
				$('#err_category_e').text(category_select);
				return !1
			} else {
				$('#err_category_e').text("")
			}
			$('#subcategory_name').val(subcategory_name);
			if (subcategory_name == null || subcategory_name == "") {
				$("#err_subcategory_name_e").text(subcategory_name_empty);
				return !1
			} else {
				$("#err_subcategory_name_e").text("")
			}
			if (!subcategory_name.match(general_regex)) {
				$('#err_subcategory_name_e').text(subcategory_name_invalid);
				return !1
			} else {
				$("#err_subcategory_name_e").text("")
			}
			if (!subcategory_name.match(atlst_alpha)) {
				$('#err_subcategory_name_e').text("Subcategory name must contain one Charecter Atleast.");
				return !1
			} else {
				$("#err_subcategory_name_e").text("")
			}
			if (subcategory_name.length < 3) {
				$('#err_subcategory_name_e').text(subcategory_name_length);
				return !1
			} else {
				$("#err_subcategory_name_e").text("")
			}
			if(subcategory_name == category) {
				$('#err_subcategory_name_e').text("Subcategory name should not be identical");
				return !1
			}else {
				$("#err_subcategory_name_e").text("");
			}
			if(!(subcategory_name == null || subcategory_name == "")) {
                var i,temp="";
                var addr_trim=$.trim(subcategory_name);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_subcategory_name_e").text("Please Enter Valid Subcategory Name.");
                            return false; 
                        }
                    }  
                }  
            }
			$.ajax({
				url : base_url + 'subcategory/edit_subcategory_modal',
				dataType : 'JSON',
				method : 'POST',
				data : {
					'id' : subcategory_id,
					'subcategory_name' : subcategory_name,
					'category_id_model' : category_id
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
						$('#edit_subcategory_modal').modal('hide');
                        dTable.destroy();
                        dTable = getAllSubcategory();
                        alert_d.text = result.msg;
                        PNotify.success(alert_d);
					}else{
						if(result.msg == 'duplicate'){
							$('#err_subcategory_name_e').text("Subcategory Name already exist");
                                return !1
                        }else{
                        	$('#edit_subcategory_modal').modal('hide');
                        	alert_d.text = result.msg;
                    		PNotify.error(alert_d);
                        }
					}
				}
			});
		});
		$("#category_e").change(function() {
			var category_e = $('#category_e').val();
			if (category_e == "") {
				$('#err_category_e').text("Please Select Category Name");
				return !1
			} else {
				$('#err_category_e').text("");
			}
		});
		$("#subcategory_name_e").on("blur", function(event) {
			var subcategory_name_e = $('#subcategory_name_e').val();
			if (subcategory_name_e == "") {
				$('#err_subcategory_name_e').text("Please Enter Subcategory.");
				return !1
			} else {
				$('#err_subcategory_name_e').text("");
			}
		});
	}); 
</script>
