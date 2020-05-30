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

          <li class="active">Warehouse list</li>

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

              <h3 class="box-title">List Warehouse</h3>

              <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('superadmin/warehouse/add');?>">Add Warehouse</a>



            </div>

            <!-- /.box-header -->

            <div class="box-body">

              <table id="index" class="table table-bordered table-striped table-hover table-responsive">

                <thead>

                  <tr>

                    <th>Warehouse Name</th>

                   <th>Address</th>

                    <th>Action</th>

                  </tr>

                </thead>

                <tbody>

                  <?php 

                      foreach ($warehouse_list as $row) {

                         $id = $row->warehouse_id;

                         $id = $this->encryption_url->encode($id);

                    ?>

                    <tr>

                      <td><?php echo $row->warehouse_name; ?></td>

                      <td><?php echo $row->warehouse_address; ?></td>

                    <td>

                      <a href="<?php echo base_url('superadmin/warehouse/edit/'); ?><?php echo $id; ?>" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a>



                      <a data-backdrop="static" data-keyboard="false" class="delete_button btn btn-xs btn-danger" data-toggle="modal" data-target="#delete_modal" data-id="<?= $id ?>" data-path="superadmin/warehouse/delete" href="#" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>                        

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