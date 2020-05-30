<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<!-- <script type="text/javascript">
  function delete_id(id)
  {
     if(confirm('<?php echo $this->lang->line('service_delete_conform'); ?>'))
     {
        window.location.href='<?php  echo base_url('service/delete/'); ?>'+id;
     }
  }
</script> -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h5>
         <ol class="breadcrumb">
          <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
          <li class="active">Services</li>
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
              <h3 class="box-title">List Users</h3>
              <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('superadmin/admin/add');?>">Add Users</a>
<!--               <a class="btn btn-sm btn-default btn-flat pull-right" href="<?php echo base_url('service/service_barcode');?>" onclick="window.open(this.href,'popUpWindow','height=400,width=600,left=10,top=10,,scrollbars=yes,menubar=no'); return false;"></i>Service Barcode</a>-->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="index" class="table table-bordered table-striped table-hover table-responsive">
                <thead>
                  <tr>
                    <!-- <th><?php// echo $this->lang->line('service_image'); ?></th> -->
                    <th>Firm Name/Company Name</th>
                    <th></th>
                    <th>Branch Name</th>
                   <!-- <th width=10%><?php// echo $this->lang->line('service_cost').'('.$this->session->userdata('symbol').')'; ?></th>-->
                    <!-- <th><?php// echo $this->lang->line('service_quantity'); ?></th> -->
                    <!-- <th><?php //echo $this->lang->line('service_unit'); ?></th> -->
                    <!-- <th><?php //echo $this->lang->line('service_alert_quantity'); ?></th> -->
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                      foreach ($data as $row) {
                         $id= $row->id;
                    ?>
                    <tr>
                      <!-- <td width="5%"><img src="<?php echo $row->image; ?>" width="100%" height="10%"></td> -->
                      <td><?php echo $row->username; ?></td>
                      <td><?php echo $row->company_name; ?></td>
                      <td><?php echo $row->branch_name; ?></td>
                        <!-- <td align="right"><?php //echo $row->cost;?></td>-->
                        <!-- <td><?php// echo $row->quantity; ?></td> -->
                        <!-- <td><?php ///echo $row->unit; ?></td> -->
                        <!-- <td><?php //echo $row->alert_quantity; ?></td> -->
                      <td>
                          <!-- <a href="" title="View Details" class="btn btn-xs btn-warning"><span class="fa fa-eye"></span></a> -->
                          <a href="<?php echo base_url('superadmin/admin/edit_user/'); ?><?php echo $id; ?>" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a>
                          <!-- <a href="javascript:delete_id(<?php echo $id;?>)" title="Delete" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a> -->
                          <a data-toggle="modal" data-target="#delete_modal" data-id="<?= $id ?>" title="Delete" class="delete_record btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>
                      </td>
                    </tr>
                    <?php
                      }
                    ?>
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
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
    $this->load->view('general/modal/delete_modal');
?>
<script type="text/javascript">
    $(document).on("click", ".delete_record", function (){
        var id=$(this).data('id');
        $('#delete_id').val(id);
        $('#delete_modal_button').attr('href',base_url+'service/delete/'+id);
    });
</script>