<div id="add_report_fixed" class="modal fade" role="dialog" data-backdrop="static">
	
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" >
					&times;
				</button>
                            <h4 class="modal-title">Report Found/Damged Stock </h4>
			</div>
			<form id="frm_add_damage" name="frm_add_damage" enctype="multipart/form-data" method="post" action="<?php echo base_url('missing_stock/add_fixed_product');?>">
			<div class="modal-body">
				<div id="loader">
                        <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                </div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="date">Date<span class="validation-color">*</span></label>
							<div class="input-group date">
								<input type="text" class="form-control datepicker" id="reference_date" name="reference_date" value="" autocomplete="off">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
							</div>
							<span class="validation-color" id="err_reference_date"></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Reference Number<span class="validation-color">*</span></label>
							<input type="text" class="form-control" id="reference_number" name="reference_number" value="<?php echo $invoice_number;?>" readonly>
							<span class="validation-color" id="err_reference_number"></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Product<span class="validation-color">*</span></label>
							<select class="from-control select2" name="cmb_product" id="cmb_product">
								<option value="">Select Product</option>
								<?php
									foreach ($products as $value) {
										echo '<option value="'.$value->product_id.'">'.$value->product_name.'</option>';
									}
								?>
							</select>
							<span class="validation-color" id="err_product"></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="txt_quantity">Quantity<span class="validation-color">*</span></label>
							<input type="number" class="form-control" id="txt_quantity" name="txt_quantity" value=""  min="1">
							<span class="validation-color" id="err_quantity"></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="category_type_a">Movement Type<span class="validation-color">*</span></label>
							<select class="form-control select2" id="cmb_move_product" name="cmb_move_product">
								<?php
                                    if (in_array($damaged_stock_module_id, $active_add)) {
                                ?>
								<option value='damaged'>Damaged</option>
								<?php } 
                                    if (in_array($product_module_id, $active_add)) {?>
								<option value='found'>Found</option>	
								<?php } ?>							
							</select>
							<span class="validation-color" id="err_product_movement"></span>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label for="">Attachment <span class="validation-color"></span></label>
							<input type="file" class="form-control" id="attachment" name="attachment">
							<span class="validation-color" id="err_attachment"></span>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label for="">Comments<span class="validation-color">*</span></label>
							<textarea name="txt_comments"  id="txt_comments" class="form-control" rows="2" cols="40"></textarea>
							<span class="validation-color" id="err_comments"></span>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group"><span class="validation-color" id="err_valid_qty"></span></div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="product_missing_submit" class="btn btn-info" >Add</button>
				<button type="button" class="btn btn-info" class="close" data-dismiss="modal">Cancel</button>
			</div>
			</form>
		</div>
	</div>
	
</div>


<script type="text/javascript">
	$("#cmb_move_product").change(function (event) {

      
        var type = $('#cmb_move_product').val();
         $.ajax({
            url: base_url + 'product_stock/generate_stock_ref_number',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'type': type
            },
            success: function (result) {
            	$('#reference_number').val(result.invoice_number);
               // console.log();
            }
        });
    });
	//$(document).on("submit", "form", function(event){
		$("#product_missing_submit").click(function () {
       
	       var myform = document.getElementById("frm_add_damage");
	        data = new FormData(myform);
	        data.append('file', $('#attachment')[0].files[0]);

            var reference_date = $('#reference_date').val()?$('#reference_date').val():"";
            var reference_number = $('#reference_number').val()?$('#reference_number').val():"";
            var product = $('#cmb_product').val()?$('#cmb_product').val():"";
            var quantity = $('#txt_quantity').val()?$('#txt_quantity').val():"";
            var move_product = $('#cmb_move_product').val()?$('#cmb_move_product').val():"";
            var comments = $('#txt_comments').val()?$('#txt_comments').val():"";
            
            if (reference_date == null || reference_date == ""){
                $("#err_reference_date").text("Please Select Reference Date.");
                return false;
            } else {
                $("#err_reference_date").text("");
            }

           /* if (reference_number == null || reference_number == ""){
                $("#err_reference_number").text("Please Enter Reference Number.");
                return false;
            } else {
                $("#err_reference_number").text("");
            }

            if (!reference_number.match(name_regex)) {
                $('#err_reference_number').text("Please Enter Valid Reference Number ");
                return false;
            } else {
                $("#err_reference_number").text("");
            } */

            if (product == null || product == "") {
                $("#err_product").text("Please select product ");
                return false;
            } else {
                $("#err_product").text("");
            }

            if (quantity == null || quantity == "") {
                $("#err_quantity").text("Please Enter quantity ");
                return false;
            } else {
                $("#err_quantity").text("");
            }

            if (move_product == null || move_product == "") {
                $("#err_product_movement").text("Please select products movement stage");
                return false;
            } else {
                $("#err_product_movement").text("");
            }


            if (comments == null || comments == "") {
                $("#err_comments").text("Please enter Comments ");
                return false;
            } else {
                $("#err_comments").text("");
            }
            var remaining = $('#remaining_'+product).val()?$('#remaining_'+product).val():"";
            if(parseFloat(remaining) < parseFloat(quantity)){
            	 $("#err_valid_qty").text("Please enter quantity less than remaining quantity ");
                return false;
            }else{
            	 $("#err_valid_qty").text("");
            }

            if(parseFloat(quantity) < 0){
            	 $("#err_valid_qty").text("Please enter quantity greater than zero ");
                return false;
            }else{
            	 $("#err_valid_qty").text("");
            }
          
		/*var url=$(this).attr("action");
		url: url,
                        dataType: 'JSON',
                        type: $(this).attr("method"),
                        data: new FormData(this),*/

           $.ajax({
                   	url:  base_url +'missing_stock/add_fixed_product',
            		data: data,
		            cache: false,
		            processData: false,
		            contentType: false,
		            dataType: 'JSON',
		            type: 'POST',
		            beforeSend: function(){
                		// Show image container
                		$("#loader").show();
            		},
                    success: function (result){
                        if(result.flag){
                        	$("#loader").hide();                    
		                    dTable.destroy();
		                    dTable = getAllMissingStock();
		                    alert_d.text = result.msg;
		                    PNotify.success(alert_d);
		                    $('#add_report_fixed').modal('hide');
		                    $("#frm_add_damage")[0].reset();
		                    var type = $("#cmb_move_product").val();
		                    $("#cmb_move_product").val(type).trigger('change');
		                    $("#cmb_product").select2();
                    
                		}else{
		                    alert_d.text = result.msg;
		                    PNotify.error(alert_d);                    
                		}	
                    },
		            error: function (result){
		            	$("#loader").hide();
		                alert_d.text ='Something Went Wrong!';
		                PNotify.error(alert_d);
		            }
                           
             });
        
    });
</script>

