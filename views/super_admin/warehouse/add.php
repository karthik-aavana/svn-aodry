<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
$this->load->view('super_admin/layouts/header');
?>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('superadmin/auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
                <li class="active">Add Warehouse</li>
            </ol>
        </h5>    
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Warehouse</h3>
                    </div>
                    <form role="form" id="form" enctype="multipart/form-data" method="post" action="<?php echo base_url('superadmin/warehouse/add_warehouse'); ?>">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="well">                                       
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                       <label for="branch_name">Branch Name <span class="validation-color">*</span></label> 
                                       <select class="form-control select2" id="branch_id" name="branch_id">
                                       <option value="">Select</option>
                                       <?php
                                          foreach ($branch as $key) {
                                              ?>
                                       <option value='<?php echo $key->branch_id ?>' <?php
                                          ?>>
                                          <?php echo $key->branch_name ; ?>
                                       </option>
                                       <?php
                                          }
                                          ?>
                                    </select>
                                       <span class="validation-color" id="err_branch_id"><?php echo form_error('branch_id'); ?></span>
                                    </div>
                                            </div>
                                           
                                             <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="reference_no">Warehouse Name <span class="validation-color">*</span></label>
                                                    <?php
                                                    ?>
                                                    <input type="text" class="form-control" id="warehouse_name" name="warehouse_name" value="" >
                                                    <span class="validation-color" id="err_warehouse_name"><?php echo form_error('warehouse_name'); ?></span>
                                                </div>
                                            </div>                                             
                                          </div>
                                      <div class="row">
                                        <div class="col-sm-3">
                                          <div class="form-group">
                                             <label for="country">Country <span class="validation-color">*</span></label> 
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
                                            <span class="validation-color" id="err_country"><?php echo form_error('country'); ?></span>
                                        </div>
                                     </div>
                                  <div class="col-sm-3">
                                    <div class="form-group">
                                       <label for="state">State <span class="validation-color">*</span></label> 
                                       <select class="form-control select2" id="sa_state" name="state">
                                       <option value="">Select</option>
                                     
                                    </select>
                                       <span class="validation-color" id="err_state"><?php echo form_error('state'); ?></span>
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label for="city">City <span class="validation-color">*</span></label> 
                                       <select class="form-control select2" id="sa_city" name="city">
                                       <option value="">Select</option>
                                     
                                    </select>
                                       <span class="validation-color" id="err_city"><?php echo form_error('city'); ?></span>
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="address">Address <!-- <span class="validation-color">*</span> --></label><br>
                                        <textarea name="address" id="address" style="width: 100%"></textarea>
                                        <span class="validation-color" id="err_address"><?php echo form_error('address'); ?></span>
                                    </div>
                                </div>
                            </div>
                                          
                            <div class="row">
                                <div class="col-sm-12">
                                  <div class="box-footer">
                                    <button type="submit" id="warehouse_submit" class="btn btn-info"> Add </button>
                                    <span class="btn btn-default" id="cancel" onclick="cancel('superadmin/warehouse')">Cancel</span>
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

        $.ajax(
        {
            url: base_url + 'common/getState/' + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                for (i = 0; i < data.length; i++)
                {
                    $('#billing_state').append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
                }
            }
        });
    });

    $("#warehouse_submit").click(function (event)
    {
        var branch_id = $('#branch_id').val();
        var warehouse_name = $('#warehouse_name').val();
        var country = $('#sa_country').val();
        var state = $('#sa_state').val();
        var city = $('#sa_city').val();

        if (branch_id == null || branch_id == "")
        {
            $("#err_branch_id").text("Please Select Branch");
            return false;
        } else
        {
            $("#err_branch_id").text("");
        }

        if (warehouse_name == null || warehouse_name == "")
        {
            $("#err_warehouse_name").text("Please Enter Warehouse Name");
            return false;
        } else
        {
            $("#err_warehouse_name").text("");
        }

        if (country == null || country == "") {
            $("#err_country").text("Please Select Country");
            return false;
        } else {
            $("#err_country").text("");
        }

        if (state == null || state == "") {
            $("#err_state").text("Please Select State");
            return false;
        } else {
            $("#err_state").text("");
        }

        if (city == null || city == "") {
            $("#err_city").text("Please Select City");
            return false;
        } else {
            $("#err_city").text("");
        }

    });
})
</script>

