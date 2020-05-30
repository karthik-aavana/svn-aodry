<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
$this->load->view('super_admin/layouts/header');
?>
<div class="content-wrapper add_margin">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('superadmin/auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?php echo base_url('superadmin/modules/branch'); ?>">Modules to Branch</a></li>
              <li class="active">Assign modules</li>
            </ol>
        </h5>    
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Assign Modules</h3>
                    </div>
                    <form role="form" id="form" enctype="multipart/form-data" method="post" action="<?php echo base_url('superadmin/modules/post_branch_access'); ?>">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="well">                                       
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                       <label for="type_of_supply">Module Name <span class="validation-color">*</span></label> 
                                    <select multiple="" class="form-control select2" id="module_id" name="module_id[]">
                                      <option value="">Select</option>
                                      <?php
                                          foreach ($modules as $module) {
                                      ?>
                                      <option value='<?php echo $module->module_id ?>' <?php
                                          ?>>
                                          <?php echo $module->module_name ; ?>
                                      </option>
                                      <?php
                                          }
                                      ?>
                                    </select>
                                       <span class="validation-color" id="err_billing_country"><?php echo form_error('billing_country'); ?></span>
                                    </div>
                                            </div>
                                            
                                            <div class="col-md-2 mt-25">
                                             
                                    <button type="submit" id="model_submit" class="btn btn-info pull-left">Add</button>
                                                                               
                                            </div>
                                            
                                            
                                          </div>
                                         
                                              <div class="row">
                          <div class="col-sm-12">
                            <?php
                  $branch_id =  $this->uri->segment(4);
                  $branch_id = $this->encryption_url->decode($branch_id);
                  // $user_id =  $this->uri->segment(4);
                  ?>
                  <p style="display: none;" id="branch_id"><?= $branch_id?></p>
                <div id="test">
                <table id="index" class="table table-bordered table-striped table-hover table-responsive">
                <thead>
                  
                  <!-- <p style="display: none;" id="user_id"><?= $user_id?></p> -->
                  <tr>
                    <th>Module</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                      foreach ($active_modules as $row) {
                    ?>
                    <tr id="row<?= $row->active_id ?>">
                      <td><?= $row->module_name ?></td>
                      <td> <a data-toggle="modal" data-target="#delete_modal" data-id="<?= $row->active_id ?>" title="Delete" class="delete_record btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a></td>
                    </tr>
                    <?php
                      }
                    ?>
                </tbody>
              </table> 
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
// $this->load->view('general/modal/delete_modal');
?>
  <script type="text/javascript">
  $(document).ready(function () {  
      // var tes= $("#index").parents("tr").children("td:first").text();
      // alert(tes);
    $("#model_submit").click(function(e){
        // alert($("#module_id").val())
      e.preventDefault();
      // $('#test').empty();
      var batchId = $("#branch_id").text();
      var module_id = $("#module_id").val()
      var module_name = $("#module_id option:selected").text();
      $.ajax({
           url: base_url + 'superadmin/modules/post_branch_access',
           type: "POST",
           data:
              {
                 'batch_id' : batchId,
                 'module_id' :module_id,
                 'module_name' :module_name
              },
           success: function (result){
            // alert(result)
             // $('#test').empty();
              //$("#test").html(result);
              location.reload();
           }
      })
    })
    })
  </script>
<script type="text/javascript">
    $(document).on("click", ".delete_record", function (){
        var id=$(this).data('id');
        var r = confirm("Are u sure?");
     if(r==true){
      $.ajax({
        url: base_url + 'superadmin/modules/active_module_delete/'+id,
         type: "GET",
         success: function (result){
            location.reload();
          //   if(result == "sucess"){
          // $("#row"+id).hide();
          //   }
         }
         
       });
     }
     else{
     }
    });
</script>
