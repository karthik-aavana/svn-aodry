<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
$this->load->view('super_admin/layouts/header');
?>
<div class="content-wrapper add_margin">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('superadmin/auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
                <li class="active">Add Branch</li>
            </ol>
        </h5>    
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Branch</h3>
                    </div>
                    <form role="form" id="form" enctype="multipart/form-data" method="post" action="<?php echo base_url('superadmin/branch/add_branch'); ?>">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="well">                                       
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                       <label for="type_of_supply">Firm Name <span class="validation-color">*</span></label> 
                                       <select class="form-control select2" id="firm_id" name="firm_id">
                                       <option value="">Select</option>
                                       <?php
                                          foreach ($firm as $key) {
                                              ?>
                                       <option value='<?php echo $key->firm_id ?>' <?php
                                          ?>>
                                          <?php echo $key->firm_name ; ?>
                                       </option>
                                       <?php
                                          }
                                          ?>
                                    </select>
                                       <span class="validation-color" id="err_billing_country"><?php echo form_error('billing_country'); ?></span>
                                    </div>
                                            </div>
                                           
                                             <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Email ID<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="text" class="form-control" id="email_id" name="email_id" value="" >
                                                    <span class="validation-color" id="err_reference_no"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                             <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Mobile Number<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="number" class="form-control" id="number" name="number" value="" >
                                                    <span class="validation-color" id="err_number"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Landline Number<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="text" class="form-control" id="land_line_no" name="land_line_no" value="" >
                                                    <span class="validation-color" id="err_land_line_no"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                              </div>
                                               <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Branch Pan Number<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="text" class="form-control" id="pan_no" name="pan_no" value="" >
                                                    <span class="validation-color" id="err_pan_no"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                             <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Branch CIN Number<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="text" class="form-control" id="cin_no" name="cin_no" value="" >
                                                    <span class="validation-color" id="err_cin_no"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                             <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Branch ROC Number<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="text" class="form-control" id="roc_no" name="roc_no" value="" >
                                                    <span class="validation-color" id="err_roc_no"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                              </div>
                                              <div class="row">
                                                <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Branch ESI<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="text" class="form-control" id="esi_no" name="esi_no" value="" >
                                                    <span class="validation-color" id="err_esi_no"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Branch PF<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="text" class="form-control" id="branch_pf" name="branch_pf" value="" >
                                                    <span class="validation-color" id="err_branch_pf"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Branch TAN Number<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="text" class="form-control" id="tan_no" name="tan_no" value="" >
                                                    <span class="validation-color" id="err_tan_no"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                            
                                              </div>
                                              <div class="row">
                                                 
                                             <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Branch Gstin Registration Type<span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="gstn_type" name="gstn_type" style="width: 100%;">
                                                    <option value="">
                                                       Select    
                                                    </option>
                                                    <option value="Registered">Registered</option>
                                                    <option value="Unregistered">Unregistered</option>
                                                </select>
                                                    <span class="validation-color" id="err_gstn_type"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                             <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Branch Gstin Number<span class="validation-color">*</span></label>
                                                    <input type="text" class="form-control" id="gstn_no" name="gstn_no" value="" >
                                                    <span class="validation-color" id="err_gstn_no"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                            
                                            
                                                
                                              </div>
                                              <div class="row">
                                                <div class="col-sm-3">
                                    <div class="form-group">
                                       <label for="type_of_supply">Country <span class="validation-color">*</span></label> 
                                       <select class="form-control select2" id="sa_country" name="country">
                                       <option value="">Select</option>
                                       <?php
                                          foreach ($country as $key) {
                                              ?>
                                       <option value='<?php echo $key->country_id ?>' <?php
                                          ?>>
                                          <?php echo $key->country_name; ?>
                                       </option>
                                       <?php
                                          }
                                          ?>
                                    </select>
                                       <span class="validation-color" id="err_country"></span>
                                    </div>
                                 </div>
                                  <div class="col-sm-3">
                                    <div class="form-group">
                                       <label for="type_of_supply">State <span class="validation-color">*</span></label> 
                                       <select class="form-control select2" id="sa_state" name="state">
                                       <option value="">Select</option>
                                     
                                    </select>
                                       <span class="validation-color" id="err_state"><?php echo form_error('billing_state'); ?></span>
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label for="type_of_supply">City <span class="validation-color">*</span></label> 
                                       <select class="form-control select2" id="sa_city" name="city">
                                       <option value="">Select</option>
                                     
                                    </select>
                                       <span class="validation-color" id="err_city"></span>
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Address<span class="validation-color">*</span></label><br>
                                                    <textarea name="firm-address" id="firm-address" style="width: 100%"></textarea>
                                                    <span class="validation-color" id="err_reference_no"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                   <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Branch<span class="validation-color">*</span></label><br>
                                                     <input type="text" class="form-control" id="branch_name" name="branch_name" value="" >
                                                    <span class="validation-color" id="err_branch_name"><?php echo form_error('reference_no'); ?></span>
                                                    <span class="validation-color" id="err_reference_no"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                    <div class="col-sm-3">
                                    <div class="form-group">
                                       <label for="type_of_supply">Financial Year <span class="validation-color">*</span></label> 
                                       <select class="form-control select2" id="financial_year_id" name="financial_year_id">
                                       <option value="">Select</option>
                                       <?php
                                          foreach ($financial_year as $key) {
                                              ?>
                                       <option value='<?php echo $key->financial_year_id ?>' <?php
                                          ?>>
                                          <?php echo $key->financial_year_title; ?>
                                       </option>
                                       <?php
                                          }
                                          ?>
                                    </select>
                                       <span class="validation-color" id="err_financial_year_id"></span>
                                    </div>
                                 </div>
                                     <div class="col-sm-3">
                                    <div class="form-group">
                                       <label for="type_of_supply">Currency <span class="validation-color">*</span></label> 
                                       <?php $this->load->view('currency_dropdown'); ?>
                                       <span class="validation-color" id="err_financial_year_id"></span>
                                    </div>
                                 </div>
                                              </div>
                                               <div class="row">
                          <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Branch Import and Export code</label>
                                                    <input type="text" class="form-control" id="branch_import_export_code" name="branch_import_export_code" value="" >
                                                    <span class="validation-color" id="err_firm_code"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                             <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Branch Establishment</label>
                                                    <input type="text" class="form-control" id="branch_shop_establishment" name="branch_shop_establishment" value="" >
                                                    <span class="validation-color" id="err_firm_code"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Postal code Establishment</label>
                                                    <input type="text" class="form-control" id="postal_code" name="postal_code" value="" >
                                                    <span class="validation-color" id="err_firm_code"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                        </div>
                                              <div class="row">
                                                  <div class="col-sm-12">
                           <div class="box-footer"> <!--id="service_modal_submit" -->
                                     <button type="submit" id="company_submit" name="company_submit" class="btn btn-info btn-flat">Add</button>
                                     <span class="btn btn-default btn-flat" id="cancel" onclick="cancel('superadmin/branch')">Cancel</span>
                                </div>
                        </div>  
                       
                                                
                                              </div>
                       
                    </form>
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
<script type="text/javascript">var base_url = "<?php echo base_url(); ?>";</script>
<?php
$this->load->view('layout/footer');
?>
<script type="text/javascript">
  $(document).ready(function(){
// country generation dynamically
$('#country').change(function ()
   {
      var id = $(this).val();
      $('#billing_state').html('<option value="">Select</option>');
      
      if(id!="101")
      {
         $('select#type_of_supply').val('Export').change();
      }
      else
      {
         $('select#type_of_supply').val('Regular').change();
      }
     
      
      $.ajax(
      {
         url: base_url + 'common/getState/' + id,
         type: "GET",
         dataType: "JSON",
         success: function (data)
         {
            for (i = 0; i < data.length; i++)
            {
               $('#billing_state').append('<option value="' + data[i].id + '">' + data[i].name + '</option>')
            }
         }
      })
   });
  })
</script>

<script>
    var mobile_regex= /^[\+\d\-\s]+$/;
    $(document).ready(function () {
        $("#gstn_selected").hide();
        $("#branch_gst_registration_type").change(function () {
            $("#gstn_selected").hide();
            var gstn_type = $(this).val();
            if (gstn_type == 'Registered') {
                $("#gstn_selected").show();
            } else {
                $("#gstn_selected").hide();
            }
        }).change();
        var name_empty = "Please Enter Name.";
        var name_invalid = "Please Enter Valid Name";
        var company_registered_type = "Please Select Company Registration Type";
        var name_length = "Please Enter Name Minimun 3 Character";
        var short_name_empty = "Please Enter Site Short Name.";
        var short_name_invalid = "Please Enter Valid Site Short Name";
        var short_name_length = "Please Enter Site Short Name Minimun 2 Character";
        var email_empty = "Please Enter Email.";
        var email_invalid = "Please Enter Valid Email";
        var mobile_empty = "Please Enter Mobile No.";
        var mobile_invalid = "Please Enter Valid Mobile No.";
        var mobile_length = "Please Enter 10 digit Mobile No.";
        var street_empty = "Please Enter Street.";
        var street_invalid = "Please Enter Valid Street";
        var street_length = "Please Enter Street Minimun 3 Character";
        var city_select = "Please Select City.";
        var state_select = "Please Select State.";
        var country_select = "Please Select Country.";
        var gstin_empty = "Please Enter GSTIN";
        var gstin_not_match = "Please Enter Valid GSTIN.Your GSTIN and State does not match.";
        var branch_name_empty="Please Enter Branch Name";
        
        $("#company_submit").click(function () {
            var name = $('#firm_id').val();
            var email = $('#email_id').val();

            var short_name = $('#short_name').val();
            var branch_gst_registration_type = $('#branch_gst_registration_type').val();          
            var address = $('#branch_address').val();
             var registered_type = $('#registered_type').val();
            
            var mobile = $('#mobile').val();
            var city = $('#city').val();
            var state = $('#state').val();
            var country = $('#country').val();           
            var gstin = $('#branch_gstin_number').val();
            var state_code = $('#state_code').val();
           // var gstin_state_code = gstin.slice(0, 2);
            var company_code = $('#company_code').val();
            var branch_code = $('#branch_code').val();
            var branch_name = $('#branch_name').val();
            var financial_year_password = $('#financial_year_password').val();
            if (name == null || name == "") {
                $("#err_name").text(name_empty);
                return false;
            } else {
                $("#err_name").text("");
            }
            if (!name.match(name_regex)) {
                $('#err_name').text(name_invalid);
                return false;
            } else {
                $("#err_name").text("");
            }
            if (name.length < 3) {
                $('#err_name').text(name_length);
                return false;
            } else {
                $("#err_name").text("");
            }
          //validation for company code
            if (registered_type == null || registered_type == "") {
                $("#err_registration_type").text('Please Select Company Registration Type');
                return false;
            } else {
                $("#err_registration_type").text("");
            }
            if (company_code == null || company_code == "") {
                $("#err_company_code").text('Please enter company code');
                return false;
            } else {
                $("#err_company_code").text("");
            }
            // if (branch_name == null || branch_name == "") {
            //     $("#err_branch_name").text('Please enter branch name');
            //     return false;
            // } else {
            //     $("#err_branch_name").text("");
            // }
            //name validation complite.
            // if (short_name == null || short_name == "") {
            //     $("#err_short_name").text(short_name_empty);
            //     return false;
            // } else {
            //     $("#err_short_name").text("");
            // }
            // if (!short_name.match(name_regex)) {
            //     $('#err_short_name').text(short_name_invalid);
            //     return false;
            // } else {
            //     $("#err_short_name").text("");
            // }
            // if (short_name.length < 2) {
            //     $('#err_short_name').text(short_name_length);
            //     return false;
            // } else {
            //     $("#err_short_name").text("");
            // }
            if (branch_gst_registration_type == null || branch_gst_registration_type == "") {
                $("#err_branch_gst_registration_type").text("Please Choose Registered Type");
                return false;
            } else {
                $("#err_branch_gst_registration_type").text("");
            }
            if (branch_gst_registration_type != "Unregistered") {
                if (gstin == null || gstin == "") 
                {
                    $("#err_branch_gstin_number").text("Please Enter GSTIN Number");
                    return false;
                } else 
                {
                    $("#err_branch_gstin_number").text("");
                }
                 if (gstin.length <15 || gstin.length>15) 
                 {
                    $("#err_branch_gstin_number").text("GSTIN Number should have length 15");
                    return false;
                } else 
                {
                    $("#err_branch_gstin_number").text("");
                }
                var res=gstin.slice(0, 2);
                var res1 = gstin.slice(13, 14);
                if (res!=state_code || res1!='Z') 
                {
                    $("#err_branch_gstin_number").text("Please enter valid GST number(Ex:29AAAAA9999AXZX).");
                    return false;
                } 
                else 
                {
                    $("#err_branch_gstin_number").text("");
                }
            }
          
            var companylogo = $("#logo").val();
            var hidden_logo_name = $("#hidden_logo_name").val();
            // if (companylogo == "" && hidden_logo_name=="" ) {
            //     $('#err_logo').text("Please Select Company Logo");
            //     return false;
            // } else {
            //     $('#err_logo').text("");
            // }
            if(companylogo)
            {
                var extension = companylogo.split('.').pop().toUpperCase();
                if (companylogo.length < 1) {
                    companylogook = 0;
                } else if (extension != "PNG" && extension != "JPG" && extension != "JPEG") {
                    companylogook = 0;
                    $('#err_logo').text("Invalid extension " + extension);
                    return false;
                } else {
                    companylogook = 1;
                }
            }
            if (country == "" || country == null) {
                $('#err_country').text(country_select);
                return false;
            } else {
                $('#err_country').text("");
            }
            if (state == "" || state == null) {
                $('#err_state').text(state_select);
                return false;
            } else {
                $('#err_state').text("");
            }
            //state validation copmlite.
            if (city == "" || city == null) {
                $('#err_city').text(city_select);
                return false;
            } else {
                $('#err_city').text("");
            }
          
            if (address == null || address == "") {
                $("#err_branch_address").text(" Please Enter Address");
                return false;
            } else {
                $("#err_branch_address").text("");
            }
            
          
            //stret validation complite.
            if (email == null || email == "") {
                $("#err_email").text(email_empty);
                return false;
            } else {
                $("#err_email").text("");
            }
            if (!email.match(email_regex)) {
                $('#err_email').text(email_invalid);
                return false;
            } else {
                $("#err_email").text("");
            }
            if (email.length < 2) {
                $('#err_email').text(email_length);
                return false;
            } else {
                $("#err_email").text("");
            }
            //email validation complite.
            // if (mobile == null || mobile == "") {
            //     $("#err_mobile").text(mobile_empty);
            //     return false;
            // } else {
            //     $("#err_mobile").text("");
            // }
            if(mobile.length>1)
            {
            if (!mobile.match(mobile_regex)) {
                $('#err_mobile').text(mobile_invalid);
                return false;
            } else {
                $("#err_mobile").text("");
            }
        }
            var import_export_code = $("#import_export_code").val();
            if(import_export_code)
            {
                var extension1 = import_export_code.split('.').pop().toUpperCase();
                if (import_export_code.length < 1) {
                    import_export_code_ok = 0;
                } else if (extension1 != "PNG" && extension1 != "JPG" && extension1 != "JPEG" && extension1 != "PDF" && extension1 != "doc" && extension1 != "docx" && extension1 != "xlx" && extension1 != "xlxs") {
                    import_export_code_ok = 0;
                    $('#err_import_export_code').text("Invalid extension " + extension1);
                    return false;
                } else {
                    import_export_code_ok = 1;
                }
            }
            
            var shop_establishment = $("#shop_establishment").val();
            if(shop_establishment)
            {
                var extension2 = shop_establishment.split('.').pop().toUpperCase();            
                if (shop_establishment.length < 1) {
                    shop_establishment_ok = 0;
                } else if (extension2 != "PNG" && extension2 != "JPG" && extension2 != "JPEG" && extension2 != "PDF" && extension2 != "doc" && extension2 != "docx" && extension2 != "xlx" && extension2 != "xlxs") {
                    shop_establishment_ok = 0;
                    $('#err_shop_establishment').text("Invalid extension " + extension2);
                    return false;
                } else {
                    shop_establishment_ok = 1;
                }
            }
            if (branch_code == null || branch_code == "") {
                $("#err_branch_code").text('Please enter login code');
                return false;
            } else {
                $("#err_branch_code").text("");
            }
            if (financial_year_password == null || financial_year_password == "") {
                $("#err_financial_year_password").text("Financial Year Password Required");
                return false;
            } else {
                $("#err_financial_year_password").text("");
            }
            if (financial_year_password.length < 3) {
                $('#err_financial_year_password').text("password should be greater then 4 charecter");
                return false;
            } else {
                $("#err_financial_year_password").text("");
            }
            //mobile validation complite.
        });
        $("#name").on("blur keyup", function (event) {
            var name = $('#name').val();
            if (name == null || name == "") {
                $("#err_name").text(name_empty);
                return false;
            } else {
                $("#err_name").text("");
            }
            if (!name.match(name_regex)) {
                $('#err_name').text(name_invalid);
                return false;
            } else {
                $("#err_name").text("");
            }
            if (name.length < 3) {
                $('#err_name').text(name_length);
                return false;
            } else {
                $("#err_name").text("");
            }
        });
         $("#registered_type").on("change", function (event) {
            var registered_type = $('#registered_type').val();
            if (registered_type == null || registered_type == "") {
                $("#err_registration_type").text(company_registered_type);
                return false;
            } else {
                $("#err_registration_type").text("");
            }
            if (registered_type == "Private Limited Company" || registered_type == "One Person Company" || registered_type == "Limited Liability Partnership") 
            {
                $("#hide_cin").show();
            }
            else
            {
                $("#hide_cin").hide();
                $('#cin_number').val('');
                $('#branch_roc').val('');
            }
           
        
        });
        $("#branch_name").on("blur keyup", function (event) 
        {
        
            var branch_name = $('#branch_name').val();
            if (branch_name == null || branch_name == "") {
                $("#err_branch_name").text(branch_name_empty);
                return false;
            } else {
                $("#err_branch_name").text("");
            }
        
        });
        // $("#short_name").on("blur keyup", function (event) {
       
        //     var short_name = $('#short_name').val();
        //     if (short_name == null || short_name == "") {
        //         $("#err_short_name").text(short_name_empty);
        //         return false;
        //     } else {
        //         $("#err_short_name").text("");
        //     }
        //     if (!short_name.match(name_regex)) {
        //         $('#err_short_name').text(short_name_invalid);
        //         return false;
        //     } else {
        //         $("#err_short_name").text("");
        //     }
        //     if (short_name.length < 2) {
        //         $('#err_short_name').text(short_name_length);
        //         return false;
        //     } else {
        //         $("#err_short_name").text("");
        //     }
        // });
        
        $("#address").on("blur keyup", function (event) {
            var address = $('#branch_address').val();
            if (address == null || address == "") {
                $("#err_branch_address").text(" Please Enter Address");
                return false;
            } else {
                $("#err_branch_address").text("");
            }
        });
        
        $("#email").on("blur keyup", function (event) {
  
            var email = $('#email').val();
            if (email == null || email == "") {
                $("#err_email").text(email_empty);
                return false;
            } else {
                $("#err_email").text("");
            }
            if (!email.match(email_regex)) {
                $('#err_email').text(email_invalid);
                return false;
            } else {
                $("#err_email").text("");
            }
        });
        $("#mobile").on("blur keyup", function (event) {
          
            var mobile = $('#mobile').val();
            // if (mobile == null || mobile == "") {
            //     $("#err_mobile").text(mobile_empty);
            //     return false;
            // } else {
            //     $("#err_mobile").text("");
            // }
            if(mobile.length>1)
            {
            if (!mobile.match(mobile_regex)) {
                $('#err_mobile').text(mobile_invalid);
                return false;
            } else {
                $("#err_mobile").text("");
            }
        }
          
        });
        $("#street").on("blur keyup", function (event) {
            var name_regex = /^[-a-zA-Z\s]+$/;
            var street = $('#street').val();
            if (street == null || street == "") {
                $("#err_street").text(street_empty);
                return false;
            } else {
                $("#err_street").text("");
            }
            if (!street.match(name_regex)) {
                $('#err_street').text(street_invalid);
                return false;
            } else {
                $("#err_street").text("");
            }
            if (street.length < 3) {
                $('#err_street').text(street_length);
                return false;
            } else {
                $("#err_street").text("");
            }
        });
        $("#city").change(function () {
            var city = $('#city').val();
            if (city == "" || city == null) {
                $('#err_city').text(city_select);
                return false;
            } else {
                $('#err_city').text("");
            }
        });
        $("#state").change(function () {
            var state = $('#state').val();
            if (state == "" || state == null) {
                $('#err_state').text(state_select);
                return false;
            } else {
                $('#err_state').text("");
            }
        });
        $('#country').change(function () {
            var country = $('#country').val();
            if (country == "" || country == null) {
                $('#err_country').text(country_select);
                return false;
            } else {
                $('#err_country').text("");
            }
        });
        //for company code form validation
         $("#company_code").on("blur keyup", function (event) {
          
            var company_code = $('#company_code').val();
            if (company_code == null || company_code == "") {
                $("#err_company_code").text('Please enter company code');
                return false;
            } else {
                $("#err_company_code").text("");
            }
            if (company_code.length < 3) {
                $('#err_company_code').text(name_length);
                return false;
            } else {
                $("#err_company_code").text("");
            }
        });
        //end company code form validation
        // $('#branch_gstin_number').on('keyup blur', function () 
        // {
        //     var gstin = $('#branch_gstin_number').val();
        //     var state_code = $('#state_code').val();
        //     $("#branch_gstin_number").val(gstin.toUpperCase());
        //     var gstin_state_code = gstin.slice(0, 2);
        //     if (gstin == "" || gstin == null) {
        //         $('#err_branch_gstin_number').text(gstin_empty);
        //         return false;
        //     } else 
        //     {
        //         $('#err_branch_gstin_number').text("");
        //     }
        // });
        $("#branch_gstin_number").on("blur keyup", function (event) {
            var gstin = $('#branch_gstin_number').val();
            $("#branch_gstin_number").val(gstin.toUpperCase());
            var gstin_length=gstin.length;
            if(gstin_length > 0)
            {
                if (gstin == null || gstin == "") {
                    $("#err_branch_gstin_number").text("Please Enter GSTIN Number.");
                    return false;
                } else {
                    $("#err_branch_gstin_number").text("");
                }
                if (!gstin.match(sname_regex)) {
                    $('#err_branch_gstin_number').text("Please Enter Valid GSTIN Number.");
                    return false;
                } else {
                    $("#err_branch_gstin_number").text("");
                }
                if (gstin_length < 15 || gstin_length > 15) 
                {
                    $("#err_branch_gstin_number").text("GSTIN Number should have length 15");
                    return false;
                } else {
                    $("#err_branch_gstin_number").text("");
                }
                var res = gstin.slice(2, 13);
                $('#pan_number').val(res);
            }
            else
            {
                $('#pan_number').val('');
            }
        });
    $('#logo').change(function () {
        var filePath=$('#logo').val();
        if(filePath!="" || filePath!=null)
        {
            document.getElementById("logo_pic").innerHTML = "";
        }
    });
    $('#import_export_code').change(function () {
        var filePath1=$('#import_export_code').val();
        if(filePath1!="" || filePath1!=null)
        {
            document.getElementById("iec_file").innerHTML = "";
        }
    });
    $('#shop_establishment').change(function () {
        var filePath2=$('#shop_establishment').val();
        if(filePath2!="" || filePath2!=null)
        {
            document.getElementById("shop_file").innerHTML = "";
        }
    });
 
});
</script>
