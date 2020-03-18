<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
$this->load->view('super_admin/layouts/header');
?>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?php echo base_url('expense_bill'); ?>">Super Admin</a></li>
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
                    <form role="form" id="form" enctype="multipart/form-data" method="post" action="<?php echo base_url('superadmin/admin/add_firm'); ?>">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="well">                                       
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Firm Name<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="text" class="form-control" id="firm_name" name="firm_name" value="" >
                                                    <span class="validation-color" id="err_firm_name"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Firm Short Name<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="text" class="form-control" id="firm_short_name" name="firm_short_name" value="" >
                                                    <span class="validation-color" id="err_firm_short_name"><?php echo form_error('reference_no'); ?></span>
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
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Firm Logo<span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="file" class="form-control" id="logo" name="logo" value="" >
                                                    <span class="validation-color" id="err_logo"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                              </div>
                                              <div class="row">
                                                 <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Firm Company code<span class="validation-color">*</span></label>
                                                    <input type="text" class="form-control" id="firm_code" name="firm_code" value="" >
                                                    <span class="validation-color" id="err_firm_code"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                             <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Branch Gstin Number<span class="validation-color">*</span></label>
                                                    <input type="text" class="form-control" id="gstn_no" name="gstn_no" value="" >
                                                    <span class="validation-color" id="err_gstn_no"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                             <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Branch Gstin Registration Type<span class="validation-color">*</span></label>
                                                    <input type="text" class="form-control" id="gstn_type" name="gstn_type" value="" >
                                                    <span class="validation-color" id="err_gstn_type"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">firm Registration type<span class="validation-color">*</span></label>
                                                    <input type="text" class="form-control" id="registration_type" name="registration_type" value="" >
                                                    <span class="validation-color" id="err_registration_type"><?php echo form_error('reference_no'); ?></span>
                                                </div>
                                            </div>
                                                
                                              </div>

                                              <div class="row">
                                                <div class="col-sm-3">
                                    <div class="form-group">
                                       <label for="type_of_supply">Country <span class="validation-color">*</span></label> 
                                       <select class="form-control select2" id="country" name="country">
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
                                       <span class="validation-color" id="err_billing_country"><?php echo form_error('billing_country'); ?></span>
                                    </div>
                                 </div>
                                  <div class="col-sm-3">
                                    <div class="form-group">
                                       <label for="type_of_supply">State <span class="validation-color">*</span></label> 
                                       <select class="form-control select2" id="state" name="state">
                                       <option value="">Select</option>
                                      <?php
                                          foreach ($state as $key) {
                                              ?>
                                       <option value='<?php echo $key->state_id ?>' <?php
                                          if ($key->state_id == $branch[0]->branch_state_id) {
                                              echo "selected";
                                          }
                                          ?>>
                                          <?php echo $key->state_name; ?>
                                       </option>
                                       <?php
                                          }
                                          ?>
                                    </select>
                                       <span class="validation-color" id="err_billing_state"><?php echo form_error('billing_state'); ?></span>
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
                           <div class="box-footer">
                                    <button type="submit" id="service_modal_submit" class="btn btn-info">Add</button>
                                    <span class="btn btn-default" id="cancel" onclick="cancel('service')">Cancel</span>
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

    $("#firm_name").on("blur keyup", function(event){
      if($("#firm_name").val()==null || $("#firm_name").val() ==""){
        // alert("df")
        $("#err_firm_name").text("please enter firm name");
        return !1
      }
       else
         {
            $("#err_firm_name").text("")
         }
    })
  })
</script>
