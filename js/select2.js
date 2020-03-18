(function($) {
	'use strict';
	if ($(".js-example-basic-single").length) {
		$(".js-example-basic-single").select2();
	}
	if ($(".js-example-basic-multiple").length) {
		$(".js-example-basic-multiple").select2();
	}
	$(document).on('focus', '.select2-selection.select2-selection--single', function(e) {
		$(this).closest(".select2-container").siblings('select:enabled').select2('open');
	});
	$(".js-example-basic-single").select2({
		selectOnClose : true
	});

	$('.custom_select2').select2({
		minimumResultsForSearch : -1
	});

	$('.modal-body .js-example-basic-single').select2({
		dropdownParent : $('.modal')
	});

})(jQuery);
