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

          <li class="active">Branch list</li>

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

              <h3 class="box-title">List Branch</h3>

              <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('superadmin/branch/add');?>">Add Branch</a>



            </div>

            <!-- /.box-header -->

            <div class="box-body">

              <table id="index" class="table table-bordered table-striped table-hover table-responsive">

                <thead>

                  <tr>

                   <th>Firm Name</th>

                    <th>Branch Name</th>



                    <th>Action</th>

                  </tr>

                </thead>

                <tbody>

                  <?php 

                      foreach ($branch_list as $row) {

                         $id = $row->branch_id;

                         $id = $this->encryption_url->encode($id);

                    ?>

                    <tr>

                      <td><?php echo $row->firm_name; ?></td>

                      <td><?php echo $row->branch_name; ?></td>

                    <td>

                      <a href="<?php echo base_url('superadmin/branch/edit/'); ?><?php echo $id; ?>" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a>

                        

                          <!-- <a data-toggle="modal" data-target="#delete_modal" data-id="<?= $id ?>" title="Delete" class="delete_record btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a> -->

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