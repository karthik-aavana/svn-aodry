var name_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
var gst_regex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
var new_gst_regex = /^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/;
var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
var sname_regex = /^[-a-zA-Z\s0-9]+$/;
var alphnum_regex = /^[a-zA-Z0-9]+$/;
var num_regex = /^[0-9]+$/;
var hsn_regex = /^[0-9]+[0-9 ]+$/;
var digit_regex = /^\d+$/;	
var price_regex = /^\$?[0-9]+(\.[0-9][0-9])?$/;
var float_num_regex = /^[+-]?([0-9]*[.])?[0-9]+$/;
// var mobile_regex = /^[6-9][0-9]{9}$/;
var mobile_regex = /^\d{10}$/;
var land_line = /^[0-9-+\s()]*$/;
var email_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
var date_regex = /^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/;
var account_no_regex = /^[0-9]{8,20}$/;

var pan_regex = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/;
//first 5 characters // 4 digit // 1 characters

var tan_regex = /^([a-zA-Z]){4}([0-9]){5}([a-zA-Z]){1}?$/;
//first 5 characters // 4 digit // 1 characters

var cin_regex = /^([ULul]){1}([0-9]){5}([a-zA-Z]){2}([0-9]){4}([a-zA-Z]){3}([0-9]){6}?$/;
//first 5 characters // 4 digit // 1 characters

var amount_regex=/^\d+(\.\d{1,2})?$/;
//taking amount value with single ,double and without decimal value

var name_firstAlpha=/^[-a-zA-Z][-a-zA-Z\s0-9]+$/;
//first letter must be charecter and digits are optinol.
//Modules=>Services(service.js,category.js,subcategory.js)

var after2_decimal=/^[0-9]*\.[0-9]{3,}$/;
//true only when it is having more than two decimal
//Service.js=>keypress

var hsn_regex=/^\d[0-9.,\s]+$/;
//validate digits with '.' & ',' and spaces 
//Modules=>service.js(for hsn format)
//add_hsn_modal.php,edit_hsn_modul

var atlst_alpha=/[-a-zA-Z]+/;
//Atleast one charecter is required
//modules=>service.js 

var name_regex = /^[-a-zA-Z\s]+$/;
//take only alpha values
//modules=>currency(add_currency_modal.php,edit_currency_modal.php)
//modules=>uqc(add.php,edit.php)
$('.default_hide').hide();
$(document).ready(function() {
	/*Return Callback history(harish)*/
	 $('.back_button').on('click', function () {
           window.history.back(); 
        });

	/* added by chetna */
	$('#customer').on('change',function(){
		if($(this).val() != ''){
			$('.default_hide').show();
		}else{
			$('.default_hide').hide();
		}
	})

	$("#country").change(function(event) {
		var country = $('#country').val();

		if (country == null || country == "") {
			$("#err_country").text("Please Select Country");
			return !1;
		} else {
			$("#err_country").text("");
		}
	});
	$("#state").change(function(event) {
		var state = $('#state').val();
		if (state == null || state == "") {
			$("#err_state").text("Please Select State ");
			return !1;
		} else {
			$("#err_state").text("");
		}
	});
	$("#city").change(function(event) {
		var city = $('#city').val();
		if (city == null || city == "") {
			$("#err_city").text("Please Select City ");
			return !1;
		} else {
			$("#err_city").text("");
		}
	});

	//for get state according to country
	$('#country').change(function() {

		var id = $(this).val();

		$('#state').html('<option value="">Select</option>');
		$('#city').html('<option value="">Select</option>');
		$.ajax({
			url : base_url + 'general/get_state/' + id,
			method : "GET",
			dataType : "JSON",
			success : function(data) {
				for ( i = 0; i < data.length; i++) {
					$('#state').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');
				}

			}
		});
	});

	$('#contact_person_country').change(function() {

		var id = $(this).val();

		$('#contact_person_state').html('<option value="">Select</option>');
		$('#contact_person_city').html('<option value="">Select</option>');
		$.ajax({
			url : base_url + 'general/get_state/' + id,
			method : "GET",
			dataType : "JSON",
			success : function(data) {
				for ( i = 0; i < data.length; i++) {
					$('#contact_person_state').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');
				}

			}
		});
	});

	$('#sa_country').change(function() {

		var id = $(this).val();

		$('#sa_state').html('<option value="">Select</option>');
		$('#sa_city').html('<option value="">Select</option>');
		$.ajax({
			url : base_url + 'superadmin/general/get_state/' + id,
			type : "GET",
			dataType : "JSON",
			success : function(data) {
				for ( i = 0; i < data.length; i++) {
					$('#sa_state').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');
				}

			}
		});

	});

	//for get city according to state
	$('#state').change(function() {

		var id = $(this).val();

		$('#city').html('<option value="">Select</option>');
		$.ajax({
			url : base_url + 'general/get_city/' + id,
			type : "GET",
			dataType : "JSON",
			success : function(data) {
				for ( i = 0; i < data.length; i++) {
					$('#city').append('<option value="' + data[i].city_id + '">' + data[i].city_name + '</option>');
				}

				$.ajax({
					url : base_url + 'general/get_state_data/' + id,
					type : "GET",
					dataType : "TEXT",
					success : function(data) {
						var response = $.parseJSON(data);
						$('#state_code').val(response[0].state_code);
					}
				});

			}
		});
	});

	$('#contact_person_state').change(function() {

		var id = $(this).val();

		$('#contact_person_city').html('<option value="">Select</option>');
		$.ajax({
			url : base_url + 'general/get_city/' + id,
			type : "GET",
			dataType : "JSON",
			success : function(data) {
				for ( i = 0; i < data.length; i++) {
					$('#contact_person_city').append('<option value="' + data[i].city_id + '">' + data[i].city_name + '</option>');
				}

				$.ajax({
					url : base_url + 'general/get_state_data/' + id,
					type : "GET",
					dataType : "TEXT",
					success : function(data) {
						var response = $.parseJSON(data);
						$('#state_code').val(response[0].state_code);
					}
				});

			}
		});
	});

	$('#sa_state').change(function() {

		var id = $(this).val();

		$('#sa_city').html('<option value="">Select</option>');
		$.ajax({
			url : base_url + 'superadmin/general/get_city/' + id,
			type : "GET",
			dataType : "JSON",
			success : function(data) {
				for ( i = 0; i < data.length; i++) {
					$('#sa_city').append('<option value="' + data[i].city_id + '">' + data[i].city_name + '</option>');
				}
			}
		});
	});

	//set financial year

	$('#financial_year_head').change(function() {

		var id = $('#financial_year_head').val();

		$.ajax({
			url : base_url + 'general/set_financial_year/' + id,
			type : "GET",
			dataType : "JSON",
			success : function(data) {

				location.reload(true);

			}
		});

	});

	// hide or show gst

	if ($("#gstregtype").val() == "") {
		$("#gstn_selected").hide();
		$("#err_gstid_type").text("");
	}
	$("#gstregtype").change(function() {
		var gstn_type = $(this).val();

		if (gstn_type == 'Unregistered') {
			$("#gstn_selected").hide();
		} else if (gstn_type == 'Registered') {
			$("#gstn_selected").show();
			$("#err_gstid_type").text("");
		} else {

			$("#gstn_selected").hide();
			$("#err_gstid_type").text("");
		}
	});

	call_css();

});

function call_css() {
	$('.float_number').keyup(function() {
		var val = $(this).val();
		if (isNaN(val)) {
			val = val.replace(/[^0-9\.]/g, '');
			if (val.split('.').length > 2)
				val = val.replace(/\.+$/, "");
		}
		$(this).val(val);
	});
}

function openNav() {
	document.getElementById("myCustomNav").style.width = "100%";
	$("body").css("overflow", "hidden");
	//disable the scroll
	$(".closebtn").css("position", "fixed");
	//disable the scroll
}

function closeNav() {
	document.getElementById("myCustomNav").style.width = "0%";
	$("body").css("overflow", "auto");
	//enables the scroll back
	$(".closebtn").css("position", "absolute");
	//disable the scroll
}
$('.custom_select2').select2({
	minimumResultsForSearch : -1,
	selectOnClose : true
});

$.fn.dataTableExt.oSort['mystring-asc'] = function(x,y) {
	var retVal;
	x = $.trim(x);
	y = $.trim(y);

	if (x==y) retVal= 0;
	else if (x == "" || x == " ") retVal= 1;
	else if (y == "" || y == " ") retVal= -1;
	else if (x > y) retVal= 1;
	else retVal = -1; // <- this was missing in version 1

	return retVal;
};

$.fn.dataTableExt.oSort['mystring-desc'] = function(y,x) {
	var retVal;
	x = $.trim(x);
	y = $.trim(y);

	if (x==y) retVal= 0; 
	else if (x == "" || x == " ") retVal= -1;
	else if (y == "" || y == " ") retVal= 1;
	else if (x > y) retVal= 1;
	else retVal = -1; // <- this was missing in version 1

	return retVal;
};

$.extend( jQuery.fn.dataTableExt.oSort, {
    "formatted-num-pre": function ( a ) {
        a = (a === "-" || a === "") ? 0 : a.replace( /[^\d\-\.]/g, "" );
        return parseFloat( a );
    },
 
    "formatted-num-asc": function ( a, b ) {
        return a - b;
    },
 
    "formatted-num-desc": function ( a, b ) {
        return b - a;
    }
});
$.fn.dataTableExt.afnSortData['dom-text'] = function  ( oSettings, iColumn ){
	var aData = [];
	$( 'td:eq('+iColumn+') input', oSettings.oApi._fnGetTrNodes(oSettings)).each( function () {
    	aData.push( this.value );
	});
	return aData;
};

//$(document).on("click", "#select_all", function() {
//	if (this.checked) {
//		$('input[type="checkbox"][name="check_item"]').each(function() {
//			this.checked = true;			
//			check_all();
//		});
//	} else {
//		$('input[type="checkbox"][name="check_item"]').each(function() {
//			this.checked = false;
//			check_all();
//		});
//	}
//});
//
//$(document).on('click', 'input[type="checkbox"][name="check_item"]', function() {
//	check_all();
//});

$(document).on('change', 'input[name="check_item"]', function () {
    $("input[name='check_item']").not(this).prop('checked', false);
    check_all();
});
function check_all() {
	var i = 0;
	$.each($("input[name='check_item']:checked"), function() {
		i++;
	});
	
	if (i == 1) {
		var row = $("input[name='check_item']:checked").closest("tr");
		var action_button = row.find('.action_button').html();
		$('#plus_btn').hide();
		$('.filter_body').html(action_button);
		$('#filter').show();
	} else if (i > 1) {
		$('#filter span:first-child').hide();
	} else {
		$('#plus_btn').show();
		$('#filter').hide();
		$('.filter_body').html('');
	}
}
$.fn.dataTable.ext.errMode = function ( settings, helpPage, message ) { 
    alert('Something went wrong!');
    /*console.log(message);*/
};