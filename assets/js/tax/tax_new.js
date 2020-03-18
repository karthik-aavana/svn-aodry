$(document).ready(function(){
	$("#add_tax_val").click(function (event)
    {
        var tax_name = $('#tax_name').val();
        var tax_value = $('#tax_value').val();
        var tax_section= $('#cmb_section').val();
        var description = $('#description').val();
        console.log(tax_value);
        // return false;

        if (tax_name == null || tax_name == "") {
            $("#err_tax_name_add").text("Please Enter Tax Type.");
            return !1
        } else {
            $("#err_tax_name_add").text("")
        } if(tax_value == null || tax_value == ""){
        	$("#err_tax_value").text("Please Enter Tax Value.");
            return !1
        } else {
        	$("#err_tax_value").text("");
        }
        if(!(tax_value > 0 && tax_value < 100)) {
            $('#err_tax_value').text("Enter Valid Tax value.");
            return !1
        } else {
            $('#err_tax_value').text("");
        }
    });
});