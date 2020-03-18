$(document).ready(function ()
{
$("#discount_percentage_modal").on("blur keyup", function (event)
    {
        var discount_percentage = $('#discount_percentage_modal').val();
        
            if (discount_percentage > 100){
                $("#err_discount_percentage_modal").text("Please Enter Discount Value Between 1 to 100");
                return !1;
            }
            if(discount_percentage < 0){
            	$("#err_discount_percentage_modal").text("Please Enter Only Positive Value");
            	return !1;
            }
            else{
                $("#err_discount_percentage_modal").text("")
            }
               
    });
});