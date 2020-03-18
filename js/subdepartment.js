if (typeof subdepartment_ajax === 'undefined' || subdepartment_ajax === null){
    var subdepartment_ajax = "no"
}

$(document).ready(function (){

   $("#subdepartment_add_submit").click(function(event) {           
            var subdepartment_name_empty = "Please Enter Subdepartment Name.";
            var subdepartment_name_invalid = "Please Enter Valid Subategory Name";
            var subdepartment_name_length = "Please Enter Subdepartment Name Minimun 3 Character";
            var department_select = "Please Select Department.";
            var subdepartment_name = $('#subdepartment_name_a').val().trim();
            var department = $('#department_name').val();
            var department_id = $('#department_id_model').val();
            var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
            if (department_id == "" || department_id == null) {
                $('#err_department_id_model').text(department_select);
                return !1
            } else {
                $('#err_department_id_model').text("")
            }
           
            if (subdepartment_name == null || subdepartment_name == "") {
                $("#err_subdepartment_name_a").text(subdepartment_name_empty);
                return !1
            } else {
                $("#err_subdepartment_name_a").text("")
            }
            if (!subdepartment_name.match(general_regex)) {
                $('#err_subdepartment_name_a').text(subdepartment_name_invalid);
                return !1
            } else {
                $("#err_subdepartment_name_a").text("")
            }
            
            if (subdepartment_name.length < 3) {
                $('#err_subdepartment_name_a').text(subdepartment_name_length);
                return !1
            } else {
                $("#err_subdepartment_name_a").text("")
            }
            if(subdepartment_name == department) {
                $('#err_subdepartment_name_a').text("subdepartment name should not be identical");
                return !1
            }else {
                $("#err_subdepartment_name_a").text("");
            }
            if(!(subdepartment_name == null || subdepartment_name == "")) {
                var i,temp="";
                var addr_trim=$.trim(subdepartment_name);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_subdepartment_name_a").text("Please Enter Valid subdepartment Name.");
                            return false; 
                        }
                    }  
                }  
            }

        if (subcategory_ajax == "yes"){
                $.ajax({
                    url : base_url + 'subdepartment/add_subdepartment_ajax',
                    dataType : 'JSON',
                    method : 'POST',
                    data : {'subdepartment_name' : subdepartment_name, 'department_id' : department_id},
                        success: function (result){
                            if(result == 'duplicate'){
                                $("#err_subdepartment_name_a").text("Subdepartment is already exit");
                            }else{
                                var data = result.data;
                                $('#cmb_subdepartment').html('');
                                $('#subdepartment_name_a').val('');
                                $('#cmb_subdepartment').append('<option value="">Select</option>');

                                for (i = 0; i < data.length; i++){
                                    $('#cmb_subdepartment').append('<option value="' + data[i].sub_department_id + '">' + data[i].sub_department_name + '</option>')
                                }                                


                                $('#cmb_subdepartment').val(result.subdepartment_id).attr("selected", "selected")
                                $('#subdepartment_modal').modal('toggle');
                        }

                        }

                    })
        }

    });




})

