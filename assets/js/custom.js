$('.form-check-input').change(function () {
    var i = 0;
    $.each($("input[name='check_item']:checked"), function () {
        i++;
    });
    if (i == 1) {
        $('#add-box').hide();
        $('#alert-mst').show();
    } else {
        $('#alert-mst').hide();
        $('#add-box').show();
    }
});

$("#ckbCheckAll").click(function () {
    $(".checkBoxClass").prop('checked', $(this).prop('checked'));
});

$(".checkBoxClass").change(function () {
    if (!$(this).prop("checked")) {
        $("#ckbCheckAll").prop('checked', false);
    }
});

$('.custome_select2').select2({
    minimumResultsForSearch: -1
});

$(document).on('click', '.open_tds_modal', function () {
    var body = $(this).closest('td').find('.tds_modal_body').html();
    var tds_id = $(this).closest('td').find('[name=item_tds_id]').val();
    $('#tds_tcs .modal-body').html(body);
   
    $('#tds_tcs').find('#tds_table [name=tds_tax][tds_id=' + tds_id + ']').prop('checked', true);
    $("#tds_tcs").find("#tds_table").dataTable();
    $('#tds_tcs').modal({backdrop: 'static', keyboard: false}, 'show');
})

$(document).on('change', '[name=tds_tax]', function () {
    var ind = $(this).closest('#tds_table').attr('index');
    if ($(this).is(':checked')) {
        var per = $(this).val();
        var typ = $(this).attr('typ');
        var id = $(this).attr('tds_id');
        var row = $(document).find('tr#' + ind);
        row.find('[name=item_tds_percentage]').val(parseFloat(per) + '%');
        row.find('[name=item_tds_type]').val(typ);
        row.find('[name=item_tds_id]').val(id);
        calculateTable(row);
    }
})

$(document).on('click', '.remove_tds', function () {
    var ind = $('#tds_tcs').find('#tds_table').attr('index');
    var row = $(document).find('tr#' + ind);
    row.find('[name=item_tds_percentage]').val(0);
    row.find('[name=item_tds_type]').val('');
    row.find('[name=item_tds_id]').val('0');
    $('#tds_tcs').find('#tds_table [name=tds_tax]').prop('checked', false);
    calculateTable(row);
})

if($('#invoice_date').length > 0){
	ValidateInvoiceDate();
}

var dateXhr = null;

$(document).on('change','#invoice_date', function () {
    ValidateInvoiceDate();
}).blur();

function ValidateInvoiceDate(){
	var invoice_date = $("#invoice_date").val();
    $('#err_date').text('');
    var frm = $('#form');
    var is_send = false;
    
    if(invoice_date != ''){
        $(this).attr('valid', '1');
        dateXhr = $.ajax({
            url: base_url + "general/ValidateInvoiceDate",
            type: "POST",
            data: {invoice_date: invoice_date},
            beforeSend : function()    {           
                if(dateXhr != null) {
                    dateXhr.abort();
                }
            },
            success: function (resp) {
                if (parseInt(resp) <= 0) {
                    $('#err_date').text('Please check Financial year for this date!');
                    $('#invoice_date').attr('valid', '0');
                    
                }
            }
        });
    }
}
// Restricts input for each element in the set of matched elements to the given inputFilter.
(function($) {
  $.fn.inputFilter = function(inputFilter) {
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
      if (inputFilter(this.value)) {
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      } else {
        this.value = "";
      }
    });
  };
}(jQuery));
// Install input filters.
$(".int").inputFilter(function(value) {
  return /^-?\d*$/.test(value); });

$(document).on('keydown','.number_only',function(event){
    if (event.shiftKey == true) {
        event.preventDefault();
    }

    if ((event.keyCode >= 48 && event.keyCode <= 57) || 
        (event.keyCode >= 96 && event.keyCode <= 105) || 
        event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
        event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

    } else {
        event.preventDefault();
    }

    if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
        event.preventDefault(); 
});

$(".number_only1").inputFilter(function(value) {
  /*return /^-?\d*[,]*\d*[.]*\d*$/.test(value);*//*/^\d*$/.test(value);*/ 
  return /^-?\d*[.]?\d*$/.test(value);/*/^\d*$/.test(value);*/ 
});
$(".intLimit").inputFilter(function(value) {
  return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 500); });
$(".float").inputFilter(function(value) {
  return /^-?\d*[.,]?\d*$/.test(value); });
$(".currency").inputFilter(function(value) {
  return /^-?\d*[.,]?\d{0,2}$/.test(value); });
$(".latin").inputFilter(function(value) {
  return /^[a-z]*$/i.test(value); });
$(".hex").inputFilter(function(value) {
  return /^[0-9a-f]*$/i.test(value); });