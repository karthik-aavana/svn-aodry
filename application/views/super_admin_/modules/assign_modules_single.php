<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('super_admin/layouts/header');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?php echo base_url('service'); ?>">Modules</a></li>
                <li class="active">Assign Modules </li>
            </ol>
        </h5>    
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">User Name : <span><?= $this->session->userdata("user_name");?></span></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('superadmin/modules/add_modules'); ?>">
                        
                        <div class="row">
                        <input type="hidden" id="branch_id" name="branch_id" value="<?= $this->session->userdata("branch_id");?>">
                        <input type="hidden" id="user_id" name="user_id" value="<?= $this->session->userdata("user_id");?>"> 
                            <div class="row">
                                <div class="col-md-4">
                                     <select class="form-control select2" id="module_id" name="module_id">
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
                                </div>
                                <div class="col-md-2">
                                    <input type="checkbox" checked="" id="add"  name="add"> add<br> 
                                </div>
                                <div class="col-md-2">
                                    <input type="checkbox" id="view"  name="view"> view<br> 
                                </div>
                                <div class="col-md-2">
                                    <input type="checkbox" id="edit"  name="edit"> edit<br> 
                                </div>
                                <div class="col-md-2">
                                    <input type="checkbox" id="delete" name="delete"> delete<br> 
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-sm-12">
                           <div class="box-footer">
                                    <button type="submit" id="model_submit" class="btn btn-info">Add</button>
                                    <span class="btn btn-default" id="cancel" onclick="cancel('service')">Cancel</span>
                                </div>
                        </div>
                    </form>
                    <div>
                        <table id="index" class="table table-bordered table-striped table-hover table-responsive">
                <thead>
                  <tr>
                    <th>Module</th>
                    <th>Add</th>
                    <th>Edit</th>
                    <th>view</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
                    </div>
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
<?php
$this->load->view('layout/footer');
$this->load->view('tax/tax_modal');
// $this->load->view('general/modal/hsn_modal');
$this->load->view('category/category_modal');
$this->load->view('subcategory/subcategory_modal');
?>
<script type="text/javascript">
$(document).ready(function () {  
  
  $("#model_submit").click(function(e){
    e.preventDefault();
    var batchId = $("#branch_id").val();
    var user_id = $("#user_id").val();
    var module_id = $("#module_id").val()
    var add = document.getElementById('add').checked ? 'yes' : 'no';
    var view = document.getElementById('view').checked ? 'yes' : 'no';
    var edit = document.getElementById('edit').checked ? 'yes' : 'no';
    var del = document.getElementById('delete').checked ? 'yes' : 'no';
    var module_name = $("#module_id option:selected").text();
    $.ajax({
         url: base_url + 'superadmin/modules/post_user_access',
         type: "POST",
         data:
            {
               'user_id': user_id,
               'batch_id' : batchId,
               'add': add,
               'view': view,
               'edit': edit,
               'del' :del,
               'module_id' :module_id,
               'module_name' :module_name
            },
         success: function (result){
            $('#index tbody').append(result);
            $(".dataTables_empty").hide();
         }
    })
  })
  })
</script>
