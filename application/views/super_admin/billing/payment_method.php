<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('super_admin/layouts/header');
?>
<style type="text/css">
  .filter-margin{margin-bottom: 20px;} 
</style>
<div class="content-wrapper add_margin">
  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h5>
         <ol class="breadcrumb">
          <li><a href="<?php echo base_url('superadmin/auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
          <li class="active">Firm</li>
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
              <h3 class="box-title">Payment Methods</h3>
              <!-- <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('superadmin/firm/add');?>">Add Firm</a> -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="ledger_table" class="table table-bordered table-striped table-hover table-responsive">
                <thead>
                  <tr><th>No.</th>
                      <th>Payment Method</th>
                      <th>Valid Days</th>
                      <th>Amount</th>
                      <th width="25%">Status</th>
                      <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($payments)){
                    $i = 0;
                    foreach ($payments as $key => $value) { 
                      $i++;
                      $checked = '';
                      if($value->delete_status == 0) $checked = 'checked';
                    ?>
                    <tr>
                      <td><?=$i;?></td>
                      <td><?=$value->payment_method;?></td>
                      <td><?=$value->valid_days;?></td>
                      <td><?=number_format($value->amounts,2);?></td>
                      <td><label class="switch"><input type="checkbox" <?=$checked;?> class="checkbox disable_in" name="acc_status" data-id="<?=$value->Id;?>" onclick="return updateBillStatus($(this))"><span class="slider round"></span></label></td>
                      <td><a href='<?=base_url()?>superadmin/payment_methods/edit/<?=$value->Id;?>' class='edit_bill' data-id='<?=$value->Id;?>'><i class='fa fa-pencil'></i></a></td>
                    </tr>
                  <?php }
                } ?>
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
  $this->load->view('general/delete_modal');
?>