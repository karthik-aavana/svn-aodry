<?php defined('BASEPATH') OR exit('No direct script access allowed');

defined('APPLICATION_PATH')||define('APPLICATION_PATH',realpath(dirname(__FILE__).'/../application'));

$this->load->view('layout/header');

?>

<style type="text/css">

	.tabactive a {

		background-color: #3f51b5 !important;

		color: #fff !important

	}

	.mt-30 {

		margin: 30px 0px;

	}

	.table-scroll {

		width: 100%;

		overflow: scroll;

		display: none;

	}

	.add-more > .fa, .removeDiv > .fa {

		font-size: 20px;

	}

	.add-more, .removeDiv {

		color: #0177a9 !important;

		cursor: pointer;

	}

	.add-more {

		margin-top: 25px;

	}

	.mt-25 {

		margin-top: 25px

	}

	#getRowsValue {

		color: #0177a9;

		display: none;

	}

	.alert-custom {

		color: red;

		display: none;

	}

	input[type=radio] {

		height: 20px;

		width: 20px;

	}

	.tabactive .active > a {

		background-color: #eeeef5 !important;

		color: #191818 !important;

	}

	.nav-tabs li {

		width: 24.5%;

		border: 1px solid #eee;

		margin-right: 0px;

	}

	.require_field {

		display: none;

		color: red

	}

</style>

<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

	<!-- Content Header (Page header) -->

	<section class="content-header"></section>

	<div class="fixed-breadcrumb">

		<ol class="breadcrumb abs-ol">

			<li>

				<a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a>

			</li>

			<li>

				<a href="<?php echo base_url('product/varient_list'); ?>">Manufacturing</a>

			</li>

			<li class="active">

				Add Manufacturing

			</li>

		</ol>

	</div>

	<!-- Main content -->

	<section class="content mt-50">

		<div class="row">

			<div class="col-md-12">

				<div class="box">

					<div class="box-header with-border">

						<h3 class="box-title">Add Manufacturing Inventory</h3>

						<a class="btn btn-default pull-right" id="cancel" onclick="cancel('product')">Back</a>

					</div>

					<div class="tabactive">

						<ul class="nav nav-tabs">

							<li class="active">

								<a data-toggle="tab" href="#home">Product Inventory</a>

							</li>

							<li>

								<a data-toggle="tab" href="#menu1">Raw Materials</a>

							</li>

							<li>

								<a data-toggle="tab" href="#menu2">Labour</a>

							</li>

							<li>

								<a data-toggle="tab" href="#menu3">Over Head</a>

							</li>

						</ul>

					</div>

					<div class="tab-content">

						<div id="home" class="tab-pane fade in active">

							<div class="box-body">

								<form role="form" id="form" method="post" action="<?php echo base_url('manufacturing/manufacturing_post'); ?>" encType="multipart/form-data">

									<div class="col-md-4">

										<div class="form-group">

											<label for="product_code">Manufacturing Code <span class="validation-color">*</span></label>

											<input type="text" class="form-control" tabindex="1"  id="ajax_product_code" name="ajax_product_code" value="<?php echo $invoice_number; ?>" <?php

											if($access_settings[0]->invoice_readonly=='yes') {

											echo "readonly";

											}

											?>

											> <span class="validation-color" id="err_manufacturing_code"></span>

										</div>

									</div>

									<div class="col-md-4">

										<div class="form-group">

											<label for="product_name">Product Name<span class="validation-color">*</span></label>

											<select class="form-control select2" autofocus="on" id="product_name_select" name="product_name_select">

												<!--  <?php

												$val = count($product_final_val);

												for ($i = 0; $i < $val; $i++)

												{

												echo "<option>" . $product_final_val[$i] . "</option>";

												}

												?> -->

												<option>Select</option>

												<?php

												foreach($products_list as $key) {

												echo "<option value ='".$key->product_id."'>".$key->product_name."</option>";

												}

												?>

											</select>

											<span class="validation-color" id="err_product_name_select"></span>

										</div>

									</div>

									<div class="col-md-4">

										<div class="form-group">

											<label for="product_code">Quantity<span class="validation-color">*</span></label>

											<input type="text" class="form-control" tabindex="1"  id="quantity" name="quantity" value="">

											<span class="validation-color" id="err_manufacturing_code"></span>

										</div>

									</div>

									<?php $array_varients=htmlspecialchars(json_encode($varients_key)); ?>

									<div class="col-sm-12">

										<div class="box-footer">

											<a data-toggle="tab" href="#menu1">

											<button type="button" id="ajaxPost" class="btn btn-info">

												Add Raw Materials

											</button></a>

										</div>

									</div>

							</div>

						</div>

						<div id="menu1" class="tab-pane fade">

							<div class="box-body">

								<table class="table raw_materials">

									<thead>

										<tr>

											<th>Raw Material</th>

											<th>product</th>

											<th>Quantity</th>

											<th>Cost</th>

											<th>Total</th>

											<th>Action</th>

										</tr>

									</thead>

									<tbody>

										<tr  class="clone_more_raw_materials">

											<td>

											<select class="raw_materials_select cost_p">

												<option>Select</option>

												<?php

												foreach($raw_materials as $rm) {

												echo "<option value='".$rm->product_id."'>".$rm->product_name."</option>";

												}

												?>

											</select></td>

											<td>

											<input class="product_invent" type="text" name="">

											</td>

											<td>

											<input class="p_quantity cost_p" type="text" name="product_invent">

											</td>

											<td>

											<input class="p_cost cost_p" type="text" name="">

											</td>

											<td>

											<input class="p_total_cost cost_p" type="text" name="p_total_cost">

											</td>

											<td>

											<p class="clone">

												+ Clone

											</p>

											<p class="remove-p-r">

												- Remove

											</p></td>

										</tr>

									</tbody>

								</table>

								<div class="col-sm-12">

									<div class="box-footer">

										<a data-toggle="tab" href="#menu2" id="add_labour">

										<button type="button"  class="btn btn-info">

											Add Labour

										</button></a>

									</div>

								</div>

							</div>

						</div>

						<div id="menu2" class="tab-pane fade">

							<div class="box-body" style="overflow: scroll;">

								<table class="labour_values">

									<thead>

										<tr>

											<th>Labour Activity <a data-toggle="modal" data-target="#add_labour_modal"  class="pull-middle"> + Add</a></th>

											<th>product</th>

											<th>No Of Labours</th>

											<th>Cost Per Hour</th>

											<th>Total Hours</th>

											<th>Total Cost</th>

											<th>Action</th>

										</tr>

									</thead>

									<tbody>

										<tr  class="clone_more_labour">

											<td>

											<select class="labour_change l_cost">

												<option>Select</option>

												<?php

												foreach($labour as $lab) {

												echo "<option value ='".$lab->labour_id."'>".$lab->activity_name."</option>";

												}

												?>

											</select></td>

											<td>

											<input class="product_invent" type="text" name="">

											</td>

											<td>

											<input class="labour_quantity l_cost" type="text" name="labour_quantity">

											</td>

											<td>

											<input class="labour_price l_cost" type="text" name="">

											</td>

											<td>

											<input class="total_hours l_cost" type="text" name="">

											</td>

											<td>

											<input class="total_labour_cost l_cost" type="text" name="">

											</td>

											<td>

											<p class="clone-labour">

												+ Clone

											</p>

											<p class="remove-p-r">

												- Remove

											</p></td>

										</tr>

									</tbody>

								</table>

								<div class="col-sm-12">

									<div class="box-footer">

										<a data-toggle="tab" href="#menu3">

										<button type="button" id="add_over_head" class="btn btn-info">

											Add Over Head

										</button></a>

									</div>

								</div>

							</div>

						</div>

						<div id="menu3" class="tab-pane fade">

							<div class="box-body">

								<table class="table overHeadTable">

									<thead>

										<tr>

											<th>Over Head name<a data-toggle="modal" data-target="#add_over_head_modal"  class="pull-middle"> + Add</a></th>

											<th>product</th>

											<th>Quantity</th>

											<th>Cost per unit</th>

											<th>Total</th>

											<th>Action</th>

										</tr>

									</thead>

									<tbody>

										<tr  class="clone_more_o_h">

											<td>

											<select class="over-head-change oh_cost">

												<option>Select</option>

												<?php

												foreach($over_head as $oh) {

												echo "<option value='".$oh->over_head_id."'>".$oh->over_head_name."</option>";

												}

												?>

											</select></td>

											<td>

											<input class="product_invent" type="text" name="">

											</td>

											<td>

											<input class="overHeadquantity oh_cost" type="text" name="">

											</td>

											<td>

											<input class="overHeadCost oh_cost" type="text" name="">

											</td>

											<td>

											<input class="overHeadtotal oh_cost" type="text" name="">

											</td>

											<td>

											<p class="clone-o-h">

												+ Clone

											</p>

											<p class="remove-p-r">

												- Remove

											</p></td>

										</tr>

									</tbody>

								</table>

								<button type="button" id="create_json">

									Over Head

								</button>

								<input style="float: right;" type="submit" name="submit" value="submit">

							</div>

						</div>

						<input type="hidden" name="o_h_quantity" id="o_h_quantity">

						<input type="hidden" name="o_h_ids" id="o_h_ids">

						<input type="hidden" name="q_values" id="q_values">

						<input type="hidden" name="labourIds" id="labourIds">

						<input type="hidden" name="r_values" id="r_values">

						<input type="hidden" name="raw_materials_Ids" id="raw_materials_Ids">



						</form>

					</div>

					<!-- <form method="post" action="<?php echo base_url('manufacturing/manufacturing_post'); ?>">

					<input type="text" name="arr[]">

					<input type="text" name="arr[]">

					<input type="text" name="arr[]">

					<input type="submit" name="submit" value="test">

					</form> -->

				</div>

				<p id="result"></p>

				<!-- <button id="finalresults">finalresults</button>    -->

			</div>

			<!-- /.box-body -->

		</div>

		<!--/.col (right) -->

</div>

<!-- /.row -->

</section>

<!-- /.content -->

</div>

<!-- /.content-wrapper -->

<!-- Modal -->

<?php

$this->load->view('layout/footer');

$this->load->view('tax/tax_modal');

$this->load->view('product/hsn_modal');

$this->load->view('category/category_modal');

$this->load->view('subcategory/subcategory_modal');

$this->load->view('labour/add_labour_modal');

$this->load->view('over_head/over_head_modal');

?>

<script type="text/javascript">

	$(document).ready(function() {

		$('#category_type').html("<option value='product'>Product</option>");

		$.ajax({

			url : base_url + 'purchase/get_product_code',

			type : 'POST',

			success : function(data) {

				var parsedJson = $.parseJSON(data);

				var product_code = parsedJson.product_code;

				$("#code").val(product_code);

				$(".modal-body #category_type").val('product');

				// $(".modal-body .type_div").hide();

			}

		});

	}); 

</script>

<script type="text/javascript">

	$(document).ready(function () {

	varients_array = JSON.parse($('#varients_array').val());

	var x = 1;

	var in_array_data = new Array();

	$('body').on('click', '#add_more', function ()

	{

	if ($(".value_varient ").val() == '') {
		alert_d.text ='please Select Values';
        PNotify.error(alert_d);

	} else {

	if ($('.key_varient').val() != "")

	{

	x++;

	var addVarients = "<div class='row mt-25'><div class='col-sm-3'> <select class='form-control select2 key_varient' id='varient_key_" + x + "' name='varient_key[]' style='width: 100%;'><option value=''>Select</option></select></div><div class='col-sm-3'><select multiple='' class='form-control select2 value_varient' id='varient_value_" + x + "' name='varient_value[]' style='width: 100%;'></select></div><div class='col-sm-3'><p class='removeDiv'><i class='fa fa-trash' aria-hidden='true'></i> Remove</p></div><br></div>";

	if (x > varients_array.length) {

	$("#err_add_more").text('cannot add more');

	} else {

	$("#err_add_more").text('');

	$('#newRows').append(addVarients);

	}

	for (var i = 0; i < varients_array.length; i++)

	{

	var prev_val = $('#varient_key_' + (i + 1)).val()

	if (prev_val != varients_array[i].varients_id) {

	$('#varient_key_' + x).append('<option value="' + varients_array[i].varients_id + '">' + varients_array[i].varient_key + '</option>');

	}

	}

	$('.key_varient').select2();

	$('.value_varient').select2();

	call_css();

	}

	}

	})

	$('body').on('click', '.removeDiv', function () {

	$(this).closest('.row').remove();

	x--;

	});

	$(document).on('change', '.key_varient', function (event)

	{

	// alert(($('.get_num select').length)/2);

	var id = $(this).attr('id');

	var id_index = id.split("_");//get the x value

	var value = $(this).val();

	$.ajax({

	url: base_url + 'varients/get_varient_value',

	dataType: 'JSON',

	method: 'POST',

	data: {

	'varient_id': value

	},

	success: function (result)

	{

	var data = result;

	$('#varient_value_' + id_index[2]).html('');

	for (i = 0; i < data.length; i++)

	{

	$('#varient_value_' + id_index[2]).append('<option value="' + data[i].varients_value_id + '">' + data[i].varients_value + '</option>');

	}

	}

	});

	});

	});

	function get_dynamic_table() {

	if ($(".value_varient").val() == "") {

	$('.alert-custom').show();

	} else {

	$('.alert-custom').hide();

	$("#getRowsValue").show();

	$('#genarate_table').empty();

	$(".generate_table_data").empty();

	$('.table-scroll').show();

	$('.box-footer').show();

	var numberOfColoumns = ($('.get_num select').length) / 2;

	for (t = 1; t <= numberOfColoumns; t++) {

	var rows = "<th>" + $('#varient_key_' + t + ' option:selected').text() + "</th>";

	var rowsText = $("select#varient_value_" + t + " option:selected").map(function () {

	return '<option value=' + $(this).val() + ' >' + $(this).text() + '</option>';

	}).get();

	// var rowsVal = $("select#varient_value_"+t+" option:selected").map(function() {return '<option>'+$(this).val()+'</option>';}).get();

	var cols = "<td><select name='var_val[]' class='select2'>" + rowsText + "</select></td>";

	var remove = "<td>remove</td>";

	$('#genarate_table').append(rows);

	$(".generate_table_data").append(cols);

	}

	$('#genarate_table:last').append("<td class='removeTd'><b>Varient Code</b></td>");

	$('#genarate_table:last').append("<td class='removeTd'><b>Varient Name</b></td>");

	$('#genarate_table:last').append("<td class='removeTd'><b>Purchase price</b></td>");

	$('#genarate_table:last').append("<td class='removeTd'><b>Selling Price</b></td>");

	$('#genarate_table:last').append("<td class='removeTd'><b>Unit</b></td>");

	$('#genarate_table:last').append("<td class='removeTd'><b>Quantity</b></td>");

	$('#genarate_table:last').append("<td class='removeTd'><b>Action</b></td>");

	for (var k = 0; k < $('.generate_table_data').length; k++) {

	$('.generate_table_data').eq(k).append("<td class='input_purchase'><input class='diff_varient' type='text'><p class='require_field'>required</p></td>");

	$('.generate_table_data').eq(k).append("<td class='input_purchase'><input  class='diff_varient' type='text'><p class='require_field'>required</p></td>");

	$('.generate_table_data').eq(k).append("<td class='input_purchase'><input class='diff_varient' type='text'><p class='require_field'>required</p></td>");

	$('.generate_table_data').eq(k).append("<td class='input_purchase'><input class='diff_varient' type='text'><p class='require_field'>required</p></td>");

	$('.generate_table_data').eq(k).append("<td class='input_purchase'><p class='require_field'>required</p><select class='diff_varient'> <?php

	foreach($uqc as $value) {

	echo "<option value='$value->uom - $value->description'".set_select('brand',$value->id).">$value->uom - $value->description</option>";

	}

	?>

		</select></td>");

		$('.generate_table_data').eq(k).append("<td class='input_purchase'><input class='diff_varient' type='text'><p class='require_field'>required</p></td>");

		$('.generate_table_data').eq(k).append("<td class='removeTd'><i class='fa fa-trash'></i>remove</td>");

		}

		}

		}

		// row cloan

		$(document).on('click', '#getRowsValue', function () {

		var numberOfColoumns = ($('.get_num select').length) / 2;

		$('.generate_table_data:last').clone().insertAfter(".generate_table_data:last");

		})

		$(document).on('click', '.removeTd', function () {

		$(this).parent('tr').empty();

		})

		//row clone end

		function automatic_generate() {

		var numberOfColoumns = ($('.get_num select').length) / 2;

		for (t = 1; t <= numberOfColoumns; t++) {

		var rows = "<th>" + $('#varient_key_' + t + ' option:selected').text() + "</th>";

		}

		}

		function json_string() {

		var values = $("input[name='var_val[]']")

		.map(function () {

		return $(this).val();

		}).get();

		}

		$("#product_modal_submit").click(function () {

		var tbl2 = $('.gen_table tbody tr').each(function (e) {

		$(this).find(".diff_varient").each(function () {

		if ($(this).val() == '') {

		$(this).closest('tr .diff_varient').next('.require_field').show();

		} else {

		$(this).closest('tr .diff_varient').next('.require_field').hide();

		}

		});

		});

		if ($('.diff_varient').val() == "") {

		return false;

		} else {

		var json = html2json();

		var json1 = get_key_value();

		$("#table_data").val(json);

		$("#key_value").val(json1);

		$("#product_modal_submit").submit();

		}

		});

		function html2json() {

		var json = '{';

		var otArr = [];

		var rowCount = $('#gen_table tbody tr').length;

		// var i = 1;

		var tbl2 = $('.gen_table tbody tr').each(function (e) {

		x = $(this).children();

		var itArr = [];

		var t = 0;

		var l = 0;

		var count = 0;

		var column = $('.gen_table tbody tr').length;

		$(this).find(".diff_varient").each(function () {

		varient_array = ['varient_code', 'varient_name', 'purchase_price', 'selling_price', 'varient_unit', 'quantity'];

		itArr.push('"' + varient_array[l] + '"' + ':' + '"' + this.value + '"');

		l++;

		});

		otArr.push('"' + e + '": {' + itArr.join(',') + '}');

		})

		json += otArr.join(",") + '}'

		return json;

		}

		function get_key_value() {

		var json = '{';

		var otArr = [];

		var rowCount = $('#gen_table tbody tr').length;

		// var i = 1;

		var tbl2 = $('.gen_table tbody tr').each(function (e) {

		x = $(this).children();

		var itArr = [];

		var t = 0;

		var l = 0;

		var count = 0;

		x.each(function () {

		count++;

		if ($(this).find(".select2 option:selected").text() != "") {

		t++;

		var r = $('#varient_key_' + t + ' option:selected').val();

		itArr.push('"' + r + '"' + ':' + '"' + $(this).find('option:selected').val() + '"');

		}

		});

		otArr.push('"' + e + '": {' + itArr.join(',') + '}');

		})

		json += otArr.join(",") + '}'

		return json;

		}

		function getVarients() {

		var initial_array = new Array();

		$("#product_invent").empty();

		$("#labour").empty();

		$("#gen_dynamic_table").find('tr').each(function () {

		var inner_array = new Array();

		var First = ($(this).find('select.select2').find(':selected').text());

		var second = $('#product_name').val() + '/';

		$("#product_invent").append("<option>" + second + First + "</option>")

		$("#labour").append("<option>" + second + First + "</option>")

		$("#overHead").append("<option>" + second + First + "</option>")

		// inner_array.push(First+second)

		//  console.log(inner_array)

		})

		}

		// $('.clone').click(function(){

		//    $('.clone_more_raw_materials:last').clone().insertAfter(".clone_more_raw_materials:last");

		// })

		// row cloan and remove for raw materials

		$(document).on('click', '.clone', function () {

		$('.clone_more_raw_materials:last').clone().insertAfter(".clone_more_raw_materials:last");

		})

		$(document).on('click', '.remove-p-r', function () {

		$(this).closest($('tr')).remove();

		})

		// row cloan and remove for raw materials

		$(document).on('click', '.clone-labour', function () {

		$('.clone_more_labour:last').clone().insertAfter(".clone_more_labour:last");

		})

		$(document).on('click', '.remove-labour', function () {

		$(this).closest($('tr')).remove();

		})

		// row cloan and remove for raw materials

		$(document).on('click', '.clone-o-h', function () {

		$('.clone_more_o_h:last').clone().insertAfter(".clone_more_o_h:last");

		})

		$(document).on('click', '.remove-p-r', function () {

		$(this).closest($('tr')).remove();

		})

		$("#showIfVarients").hide()

		$("#hasVarients").click(function () {

		if ($("#hasVarients").is(":checked")) {

		$("#showIfVarients").show()

		} else {

		$("#showIfVarients").hide()

		}

		})

		$("#ajaxPost").click(function () {

		if ($("#hasVarients").is(":checked")) {

		var val = "checked";

		} else {

		var val = "notChecked";

		}

		$.ajax({

		type: "POST",

		url: base_url + "manufacturing/add_manufacturing_inventory",

		dataType: 'JSON',

		data: {

		'ajax_product_code': $("#ajax_product_code").val(),

		'product_name': $("#product_name").val(),

		'product_hsn_sac_code': $("#product_hsn_sac_code").val(),

		'product_category': $("#product_category").val(),

		'product_subcategory': $("#product_subcategory").val(),

		'product_quantity': $("#product_quantity").val(),

		'product_price': $("#product_price").val(),

		'product_tax': $("#product_tax").val(),

		'product_igst': $("#product_igst").val(),

		'product_cgst': $("#product_cgst").val(),

		'product_sgst': $("#product_sgst").val(),

		'varients_array': $('#varients_array').val(),

		'table_data': $('#table_data').val(),

		'key_value': $('#key_value').val(),

		'hasVarients': val

		},

		success: function (res) {

		// alert(res);

		}

		})

		})

		$(document).on('change', '.raw_materials_select', function () {

		var id = $(this).val();

		var targetQuantity = $(this).closest($('tr')).find($('.p_quantity'));

		var targetPrice = $(this).closest($('tr')).find($('.p_cost'));

		var totalCost = $(this).closest($('tr')).find($('.p_total_cost'))

		$.ajax({

		url: base_url + "manufacturing/get_raw_materials_price",

		type: 'POST',

		data: {'id': id},

		success: function (result) {

		res = JSON.parse(result)

		var labourQuantity = res[0].purchase_price;

		var labourtotal = res[0].quantity;

		var total = parseInt(labourQuantity) * parseInt(labourtotal);

		targetQuantity.val(labourQuantity);

		targetPrice.val(labourtotal);

		totalCost.val(total)

		}

		})

		})

		$(document).on('keyup', '.p_quantity', function () {

		var targetQuantity = $(this).closest($('tr')).find($('.p_quantity'));

		var targetPrice = $(this).closest($('tr')).find($('.p_cost'));

		var totalCost = $(this).closest($('tr')).find($('.p_total_cost'));

		var total = targetPrice.val() * $(this).val();

		totalCost.val(total)

		})

		$(document).on('keyup', '.labour_quantity,.labour_price, .total_hours', function () {

		var labour_quantity = $(this).closest($('tr')).find($('.labour_quantity'));

		var labour_price = $(this).closest($('tr')).find($('.labour_price'));

		var total_hours = $(this).closest($('tr')).find($('.total_hours'));

		var total_labour_cost = $(this).closest($('tr')).find($('.total_labour_cost'));

		total_labour_cost.val(labour_price.val() * total_hours.val() * labour_quantity.val())

		})

		$(document).on('keyup', '.p_cost', function () {

		var targetQuantity = $(this).closest($('tr')).find($('.p_quantity'));

		var targetPrice = $(this).closest($('tr')).find($('.p_cost'));

		var totalCost = $(this).closest($('tr')).find($('.p_total_cost'));

		var total = targetQuantity.val() * $(this).val();

		totalCost.val(total)

		})

		$(document).on('keyup', '.overHeadquantity', function () {

		var overHeadCost = $(this).closest($('tr')).find($('.overHeadCost'));

		var overHeadtotal = $(this).closest($('tr')).find($('.overHeadtotal'));

		var total = overHeadCost.val() * $(this).val();

		overHeadtotal.val(total)

		})

		$(document).on('keyup', '.overHeadCost', function () {

		var overHeadquantity = $(this).closest($('tr')).find($('.overHeadquantity'));

		var overHeadtotal = $(this).closest($('tr')).find($('.overHeadtotal'));

		var total = overHeadquantity.val() * $(this).val();

		overHeadtotal.val(total)

		})

		$(document).on('change', '.labour_change', function () {

		var id = $(this).val();

		var targetQuantity = $(this).closest($('tr')).find($('.labour_quantity'));

		var targetPrice = $(this).closest($('tr')).find($('.labour_price'));

		var totalHours = $(this).closest($('tr')).find($('.total_hours'));

		var total_labour_cost = $(this).closest($('tr')).find($('.total_labour_cost'));

		$.ajax({

		url: base_url + "labour/get_labour_price",

		type: 'POST',

		data: {'id': id},

		success: function (result) {

		res = JSON.parse(result)

		var labourQuantity = res[0].no_of_labour;

		var labourtotal = res[0].cost_per_hour;

		var totalHr = res[0].total_no_hours;

		targetQuantity.val(labourQuantity);

		targetPrice.val(labourtotal);

		totalHours.val(totalHr);

		total_labour_cost.val(labourQuantity * labourtotal * totalHr);

		}

		})

		})

		$(document).on('change', '.over-head-change', function () {

		var id = $(this).val();

		var targetQuantity = $(this).closest($('tr')).find($('.overHeadquantity'));

		var targetPrice = $(this).closest($('tr')).find($('.overHeadCost'));

		var overHeadtotal = $(this).closest($('tr')).find($('.overHeadtotal'));

		$.ajax({

		url: base_url + "over_head/get_price",

		type: 'POST',

		data: {'id': id},

		success: function (result) {

		res = JSON.parse(result)

		// alert(res[0].quantity)

		var labourQuantity = res[0].quantity;

		var labourtotal = res[0].over_head_cost_per_unit;

		targetQuantity.val(labourQuantity);

		targetPrice.val(labourtotal);

		overHeadtotal.val(labourQuantity * labourtotal)

		}

		})

		})

		$("#product_name_select").change(function () {

		var productVal = $("#product_name_select option:selected").text();

		$(".product_invent").val(productVal)

		})

		function test() {

		var json = '{';

		var otArr = [];

		var rowCount = $('#gen_table tbody tr').length;

		// var i = 1;

		var tbl2 = $('.labour_values tbody tr').each(function (e) {

		x = $(this).children();

		var itArr = [];

		var t = 0;

		var l = 0;

		var count = 0;

		var column = $('.gen_table tbody tr').length;

		$(this).find(".l_cost").each(function () {

		varient_array = ['labour_id', 'no_of_labour', 'cost_per_hour', 'total_no_hours', 'total_cost'];

		itArr.push('"' + varient_array[l] + '"' + ':' + '"' + this.value + '"');

		l++;

		});

		otArr.push('"' + e + '": {' + itArr.join(',') + '}');

		})

		json += otArr.join(",") + '}'

		return json;

		}

		$("#add_over_head").click(function () {

		var json = test();

		$("#q_values").val(json);

		// alert(json)

		var labourJson = get_selected_labour_id();

		// alert(labourJson)

		$("#labourIds").val(labourJson)

		})

		//function to get selected values of labour

		function get_selected_labour_id() {

		var json = '{';

		var otArr = [];

		var rowCount = $('#gen_table tbody tr').length;

		// var i = 1;

		var tbl2 = $('.labour_values tbody tr').each(function (e) {

		x = $(this).children();

		var itArr = [];

		var t = 0;

		var l = 0;

		var count = 0;

		x.each(function () {

		count++;

		if ($(this).find(".labour_change option:selected").text() != "") {

		t++;

		var r = 'labour_id';

		itArr.push('"' + r + '"' + ':' + '"' + $(this).find('option:selected').val() + '"');

		}

		});

		otArr.push('"' + e + '": {' + itArr.join(',') + '}');

		})

		json += otArr.join(",") + '}'

		return json;

		}

		$("#create_json").click(function (e) {

		var oh_values = overhead_values();

		// alert(oh_values)

		$("#o_h_quantity").val(oh_values)

		var oh_id = get_selected_over_head_id();

		// alert(oh_id);

		$("#o_h_ids").val(oh_id)

		})

		function overhead_values() {

		var json = '{';

		var otArr = [];

		var tbl2 = $('.overHeadTable tbody tr').each(function (e) {

		x = $(this).children();

		var itArr = [];

		var t = 0;

		var l = 0;

		var count = 0;

		var column = $('.gen_table tbody tr').length;

		$(this).find(".oh_cost").each(function () {

		varient_array = ['over_head_id', 'quantity', 'price', 'cost'];

		itArr.push('"' + varient_array[l] + '"' + ':' + '"' + this.value + '"');

		l++;

		});

		otArr.push('"' + e + '": {' + itArr.join(',') + '}');

		})

		json += otArr.join(",") + '}'

		return json;

		}

		function get_selected_over_head_id() {

		var json = '{';

		var otArr = [];

		var rowCount = $('#gen_table tbody tr').length;

		// var i = 1;

		var tbl2 = $('.overHeadTable tbody tr').each(function (e) {

		x = $(this).children();

		var itArr = [];

		var t = 0;

		var l = 0;

		var count = 0;

		x.each(function () {

		count++;

		if ($(this).find(".over-head-change option:selected").text() != "") {

		t++;

		var r = 'over_head';

		itArr.push('"' + r + '"' + ':' + '"' + $(this).find('option:selected').val() + '"');

		}

		});

		otArr.push('"' + e + '": {' + itArr.join(',') + '}');

		})

		json += otArr.join(",") + '}'

		return json;

		}

		// function for raw materials

		function raw_materials() {

		var json = '{';

		var otArr = [];

		var rowCount = $('#gen_table tbody tr').length;

		// var i = 1;

		var tbl2 = $('.raw_materials tbody tr').each(function (e) {

		x = $(this).children();

		var itArr = [];

		var t = 0;

		var l = 0;

		var count = 0;

		var column = $('.gen_table tbody tr').length;

		$(this).find(".cost_p").each(function () {

		varient_array = ['raw_material_varient_id', 'quantity', 'price', 'cost'];

		itArr.push('"' + varient_array[l] + '"' + ':' + '"' + this.value + '"');

		l++;

		});

		otArr.push('"' + e + '": {' + itArr.join(',') + '}');

		})

		json += otArr.join(",") + '}'

		return json;

		}

		// function to get selected rawmaterials varient id

		// function get_selected_raw_material_id(){

		//  var json = '{';

		//    var otArr = [];

		//    var rowCount = $('#gen_table tbody tr').length;

		//   // var i = 1;

		//    var tbl2 = $('.raw_materials tbody tr').each(function(e) {

		//       x = $(this).children();

		//       var itArr = [];

		//       var t =0;

		//       var l = 0;

		//       var count =0;

		//       x.each(function() {

		//         count++;

		//         if($(this).find(".raw_materials_select option:selected").text() != ""){

		//         t++;

		//         var r = 'rm';

		//         itArr.push('"' + r +'"'+':'+'"'+ $(this).find('option:selected').val() + '"' );

		//      }

		//       });

		//       otArr.push('"' + e + '": {' + itArr.join(',') + '}');

		//    })

		//    json += otArr.join(",") + '}'

		//    return json;

		// }

		$("#add_labour").click(function () {

		var val = raw_materials();

		$("#r_values").val(val)



		})

</script>

<script src="<?php echo base_url('assets/js/product/') ?>product.js"></script>

