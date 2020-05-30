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
              <table id="billingTable" class="table table-bordered table-striped table-hover table-responsive">
                <thead>
                  <tr><th>Company Code</th>
                      <th>Company/Individual</th>
                      <th>Package</th>
                      <th>Activation Date</th>
                      <th>End Date</th>
                      <th>Amount</th>
                      <th>Payment</th>
                      <th>Status</th>
                      <th>Action</th>
                  </tr>
                </thead>
                <tbody>
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
  $this->load->view('super_admin/billing/edit_bill_info');
?>
<script type="text/javascript">
  var tbl = GetBillingInfo();
  

  function GetBillingInfo(data = {}) {

    var comp_table = $('#billingTable').DataTable({
      "processing": true,
      "serverSide": true,
      "ajax": {
          "url": '<?= base_url(); ?>superadmin/Payment_methods/getBillingInfo',
          "dataType": "json",
          "type": "POST",
          "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
      },
      "columns": [
          {"data": "firm_company_code"},
          {"data": "firm_name"},
          {"data": "payment_method"},
          {"data": "activation_date", },
          {"data": "end_date"},
          {"data": "amount"},
          {"data": "payment_status"},
          {"data": "package_status"},
          {"data": "action"}
      ],
      "columnDefs": [ {"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false}],
      'language': {
          'loadingRecords': '&nbsp;',
          'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
          },
      });

      anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});

    return comp_table;
  }

  function updateBillStatus(ths){
    
    var msg = "Are you sure want to Inactive Status..?";
    var sts = 0;
      
    if (ths.is(":checked")) {
      msg = "Are you sure want to Active Status..?";
      sts = 1;
    } 
    if(confirm(msg)){
      var bill_id = ths.attr('data-id');
      var package = ths.closest('tr').find('input[name=package_name]').val();
      $.ajax({
        url:'<?=base_url()?>superadmin/Payment_methods/updateBillStatus',
        type:'post',
        dataType:'json',
        data:{bill_id:bill_id,status:sts,package:package},
        success:function(j){
          alert_d.text = j.msg;
          PNotify.success(alert_d);
          if(!j.flag){
            if (ths.is(":checked")) ths.prop('checked',false);
            return false;
          }
        }
      });
    }else{
      return false;
    }
  }
</script>