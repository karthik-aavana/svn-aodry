if (typeof department_ajax === 'undefined' || department_ajax === null){
    var department_ajax = "no"
}

$(document).ready(function (){
   var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
    $("#department_add_submit").click(function (event){
        var department_name_a = $('#department_name_a').val();  
            var department_code_a = $('#department_code_a').val();  
            var first5 = department_name_a.substr(0, 5);
            var department_name_empty = "Please Enter the Department Name.";
            var department_name_invalid = "Please Enter Valid Department Name";
            var department_name_length = "Please Enter Department Name Minimun 3 Character";
            if (department_name_a == null || department_name_a == "") {
                $("#err_department_name_a").text(department_name_empty);
                return !1
            } else {
                $("#err_department_name_a").text("")
            }
            if (!department_name_a.match(general_regex)) {
                $('#err_department_name_a').text(department_name_invalid);
                return !1
            } else {
                $("#err_department_name_a").text("")
            }
            
            if (department_name_a.length < 3) {
                $('#err_department_name_a').text(department_name_length);
                return !1
            } else {
                $("#err_department_name_a").text("")
            }
            
            if(!(department_name_a == null || department_name_a == "")) {
                var i,temp="";
                var addr_trim=$.trim(department_name_a);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_department_name_a").text("Please Enter Valid department Name.");
                            return false; 
                        }
                    }  
                }  
            }
        if (department_ajax == "yes"){
            $.ajax({
                url : base_url + 'department/add_department_ajax',
                dataType : 'JSON',
                method : 'POST',
                data : {'department_name' : department_name_a,'department_code' :department_code_a},
                    success: function (result){
                        if(result == 'duplicate'){
                             $("#err_department_name_a").text("Department Name already exist");
                             return !1;
                        }else {
                            var data = result.data;                          
                           
                            $('#cmb_department').html('');
                            $('#cmb_subdepartment').html('');
                            $('#cmb_department').append('<option value="">Select</option>');
                            $('#cmb_subdepartment').append('<option value="">Select</option>');                                
                            for (i = 0; i < data.length; i++){
                                $('#cmb_department').append('<option value="' + data[i].department_id + '">' + data[i].department_name + '</option>')
                            }                                
                            $('#cmb_department').val(result.id).attr("selected", "selected");
                            $('#department_modal').modal('toggle');
                           $('#department_name_a').val('');
                        }
                }
            })
        }
    });
    
});
