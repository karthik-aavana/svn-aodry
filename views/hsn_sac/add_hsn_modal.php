<div id="add_hsn_modal" class="modal fade z_index_modal" role="dialog" data-backdrop="static">

	<div class="modal-dialog modal-md">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal">

					&times;

				</button>

				<h4>Add HSN</h4>

			</div>			

				<form  id="form">

					<!--<form role="form" id="form" method="post" action="<?php echo base_url('hsn_sac/addHsn'); ?>">-->

				<div class="modal-body">

					<div class="row">

						<div class="col-sm-6">

							<div class="form-group">

								<label for="">Type<span class="validation-color">*</span></label>

								<select class="form-control select2" id="hsnType" name="hsnType" style="width: 100%">

									<option value="">Select HSN Type</option>

									<option value="goods">Goods</option>

									<option value="services">Services</option>

								</select>

								<span class="validation-color" id="err_type"></span>

							</div>

						</div>

						<div class="col-sm-6">

							<div class="form-group">

								<label for="">Code<span class="validation-color">*</span></label>

								<input type="text" class="form-control" id="hsnCode" name="hsnCode">

								<input type="hidden" class="form-control" id="hsnId" name="hsnId" value="0" >

								<span class="validation-color" id="err_code"></span>

							</div>

						</div>

						<div class="col-sm-12">

							<div class="form-group">

								<label for="description">Description</label>

								<textarea class="form-control" id="description" name="description" ></textarea>								

							</div>

						</div>

					</div>

				</div>

				<div class="modal-footer">

					<button type="submit" id="submitHsn" class="btn btn-info">

						Add

					</button>

					<button type="button" class="btn btn-info" data-dismiss="modal">

						Cancel

					</button>

				</div>

			</form>

		</div>

	</div>

</div>





<script type="text/javascript">

    $(document).ready(function ()

    {

    	var hsn_code_exist = 0;

        $("#submitHsn").click(function() { 



            var hsnType = $('#hsnType').val();

			var hsnCode = $('#hsnCode').val();

			var description = $('#description').val();





            if (hsnType == null || hsnType == "") {

				$("#err_type").text("Please Select Type ");

				return false;

			} else {

				$("#err_type").text("");

			}



			if (hsnCode == null || hsnCode == "") {

				$("#err_code").text("Please Enter Code ");

				return false;

			} else {

				$("#err_code").text("");

			}



          if (hsn_code_exist > 0) {

                $("#err_code").text("The Hsn code is already exist.");

                 return false;

            } else  {

                $("#err_code").text("");

            }





             $.ajax(

                    {

                        url: base_url + 'hsn_sac/addHsn',

                        dataType: 'JSON',

                        method: 'POST',

                        data: { 'hsnType': hsnType, 'hsnCode': hsnCode,'description':description },

                        success: function (result)

                        {

                        	//alert('succ');

                            setTimeout(function () {

                                location.reload();

                            });

                        },

                         error: function (result) 

                        {
                        alert_d.text ='error';
                        PNotify.error(alert_d); 

      

                        }

                    });





            

        });  





$("#hsnCode").on("blur", function (event)

    {

        var hsnId = $('#hsnId').val();

        var hsnCode = $('#hsnCode').val();

        if (hsnCode.length > 1)

        {

            if (hsnCode == null || hsnCode == "")

            {

                $("#err_code").text("Please Enter Code ");

                return !1

            } else

            {

                $("#err_code").text("")

            }

           

        }



       $.ajax(

                {

                    url: base_url + 'hsn_sac/gethsnCode',

                    type: "POST",

                    dataType: "json",

                    data:

                            {

                                hsnCode: hsnCode,

                                 hsnId: hsnId

                                

                            },

                    success: function (data)

                    {

                        if (data[0].num_hsn_code > 0)

                        {

                            $('#err_code').text("The Hsn code is already exist.");

                           

                            hsn_code_exist = 1;

                        } else

                        {

                            $('#err_code').text("");

                            hsn_code_exist = 0;

                        }

                    }

                });

    });





        

    });

</script>