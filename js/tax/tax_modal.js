$(document).ready(function ()
{
$("#tax_value").on("blur keyup", function (event)
    {
        var tax_percentage = $('#tax_value').val();
        
            if (tax_percentage > 100){
                $("#err_tax_value").text("Please Enter Tax Value Between 1 to 100");
                return !1
            }else if(tax_percentage == '-'){
            	$("#err_tax_value").text("Please Enter Only positive Value");
            } 
            else{
                $("#err_tax_value").text("")
            }
               
    });
});