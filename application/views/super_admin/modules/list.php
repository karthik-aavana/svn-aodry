<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('super_admin/layouts/header');

?>



<div class="content-wrapper add_margin">

  <!-- Content Header (Page header) -->

    <section class="content-header">

      <h5>

         <ol class="breadcrumb">

          <li><a href="<?php echo base_url('superadmin/auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>

          <li class="active">Modules to Users</li>

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

              <h3 class="box-title">Modules to Users</h3>

              <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('superadmin/users/add');?>">Add Users</a>

            </div>

            <!-- /.box-header -->

            <div class="box-body">

              <table id="index" class="table table-bordered table-striped table-hover table-responsive">

                <thead>

                  <tr>

                    <!-- <th><?php// echo $this->lang->line('service_image'); ?></th> -->

                    <th>Username</th>

                    <th>Company Name</th>

                    <th>Branch Name</th> 

                    <th>Action</th>

                  </tr>

                </thead>

                <tbody>

                  <?php 

                      foreach ($data as $row) {

                         $id = $row->id;

                         $id = $this->encryption_url->encode($id);

                         $branch_id = $row->branch_id;

                         $branch_id = $this->encryption_url->encode($branch_id);

                    ?>

                    <tr>

                      <!-- <td width="5%"><img src="<?php echo $row->image; ?>" width="100%" height="10%"></td> -->

                      <td><?php echo $row->username; ?></td>

                      <td><?php echo $row->firm_name; ?></td>

                      <td><?php echo $row->branch_name; ?></td>

                        <!-- <td align="right"><?php //echo $row->cost;?></td>-->

                        <!-- <td><?php// echo $row->quantity; ?></td> -->

                        <!-- <td><?php ///echo $row->unit; ?></td> -->

                        <!-- <td><?php //echo $row->alert_quantity; ?></td> -->

                      <td>

                          <!-- <a href="" title="View Details" class="btn btn-xs btn-warning"><span class="fa fa-eye"></span></a> -->

                          <a href="<?php echo base_url('superadmin/modules/add_privilege/'); ?><?php echo $id; ?>/<?= $branch_id?>" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a>

                          <!-- <a href="javascript:delete_id(<?php echo $id;?>)" title="Delete" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a> -->

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