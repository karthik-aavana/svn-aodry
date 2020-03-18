<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('super_admin/layouts/header');
?>
<style type="text/css">
  .filter-margin{margin-bottom: 20px;} 
</style>
<div class="content-wrapper">
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
              <h3 class="box-title">Ledgers Settings</h3>
              <!-- <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('superadmin/firm/add');?>">Add Firm</a> -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row filter-margin">
                <div class="col-sm-2 pl-2 pr-2">
                    <select class="form-control select2" name="branch_id">
                        <option value="">Select Branch*</option>
                        <?php
                        if (!empty($branch_list)) {
                          foreach ($branch_list as $row) {
                            $id = $row->branch_id;
                            $id = $this->encryption_url->encode($id);
                            echo "<option value='{$id}'>{$row->branch_name}</option>";
                          }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-2 pl-2 pr-2">
                    <select class="form-control select2" name="module">
                        <option value="">Select Modules*</option>
                        <option value="sales">Sales</option>
                        <option value="sales">Sales Credit Note</option>
                        <option value="sales">Sales Debit Note</option>
                        <option value="purchase">Purchase</option>
                        <option value="purchase">Purchase Credit Note</option>
                        <option value="purchase">Purchase Debit Note</option>
                        <option value="expense">Expense Bill</option>
                        <option value="receipt">Receipt Voucher</option>
                        <option value="payment">Payment Voucher</option>
                        <option value="advance">Advance Voucher</option>
                        <option value="refund">Refund Voucher</option>
                        <option value="bank">Bank Voucher</option>
                        <option value="boe">BOE</option>
                    </select>
                </div>
                <div class="col-sm-2 pl-2 pr-2">
                    <select class="form-control select2" name="gst_payable">
                        <option value="">Select GST Mode</option>
                        <option value="0">Both</option>
                        <option value="1">No</option> 
                        <option value="2">Yes</option> 
                    </select>
                </div>
                <div class="col-sm-2 pl-2 pr-2">
                    <select class="form-control select2" name="place_of_supply">
                        <option value="">Select Place Of Supply</option>
                        <option value="1">Within Country</option>
                        <option value="2">Out Of Country</option>
                    </select>
                </div>
                <div class="col-sm-2 pl-2">
                    <button type="button" class="btn btn-primary tbl-btn" id="search_list">
                        Search
                    </button>
                    <button type="reset" class="btn btn-primary tbl-btn" id="reset_filter">
                        Reset
                    </button>
                </div>
              </div>
              <table id="ledger_table" class="table table-bordered table-striped table-hover table-responsive">
                <thead>
                  <tr>
                      <th style="display: none;width: 0%;">Id</th>
                      <th>Main Group</th>
                      <th>Sub Group-1</th>
                      <th>Sub Group-2</th>
                      <th width="25%">Ledger</th>
                      <th>Action</th>
                  </tr>
                </thead>
                <tbody></tbody>
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
  $this->load->view('super_admin/ledgers/Edit_group');
?>
<script type="text/javascript">
    var tbl = GetLedgersDefault();
    $(document).ready(function(){
      $('#search_list').click(function(){
        var branch_id = $('[name=branch_id]').val();
        var module_name = $('[name=module]').val();
        var gst_payable = $('[name=gst_payable]').val();
        var place_of_supply = $('[name=place_of_supply]').val();
        var data = {branch_id:branch_id,module_name:module_name,gst_payable:gst_payable,place_of_supply:place_of_supply};
        if(tbl) tbl.destroy();
        tbl = GetLedgersDefault(data);
      });

      $(document).on('click', '.edit_grp', function () {
        var ledger_id = $(this).attr('data-id');
        var default_led_id = $(this).attr('dlId');
        var branch_id = $(this).parent().find('[name=branch_id]').val();
        var ledger_name = $(this).parent().find('[name=ledger_name]').val();
        var module_name = $(this).parent().find('[name=module]').val();
        var gst_payable = $(this).parent().find('[name=gst_payable]').val();
        var place_of_supply = $(this).parent().find('[name=place_of_supply]').val();
        $('#edit_ledgers #main_grp').find('option[value='+default_led_id+']').prop('selected',true).change();
        $('#edit_ledgers #primary_sub_group').find('option[value='+default_led_id+']').prop('selected',true).change();
        $('#edit_ledgers #sec_sub_group').find('option[value='+default_led_id+']').prop('selected',true).change();
        $('#edit_ledgers #default_ledger_name').find('option[value='+default_led_id+']').prop('selected',true).change();
        $('#edit_ledgers #ledger').val(ledger_name);
        $('#edit_ledgers [name=ledger_id]').val(ledger_id);
        $('#edit_ledgers [name=branch_id]').val(branch_id);
        $('#edit_ledgers [name=default_ledger_id]').val(default_led_id);
        $('#edit_ledgers .module_name').text(module_name);
        gst_pay = 'Both';
        if(gst_payable == '1') gst_pay = 'No';
        if(gst_payable == '2') gst_pay = 'Yes';
        $('#edit_ledgers .gst_payable').text(gst_pay);
        place_of = 'All';
        if(place_of_supply == '1') place_of = 'India';
        if(place_of_supply == '2') place_of = 'Out Of Country';
        $('#edit_ledgers .place_of_supply').text(place_of);
        $('#edit_ledgers').modal('show'); 
      });

      $('#editGroupLedger').click(function(){
        var ledger_name = $('#edit_ledgers #ledger').val();
        var ledger_id = $('#edit_ledgers [name=ledger_id]').val();
        var branch_id = $('#edit_ledgers [name=branch_id]').val();
        var default_ledger_id = $('#edit_ledgers [name=default_ledger_id]').val();
        if(ledger_name != ''){
          $.ajax({
            url:'<?=base_url();?>/superadmin/LedgersUpdate/UpdateDefaultLedger',
            type:'post',
            dataType : 'json',
            data : {branch_id:branch_id,ledger_name:ledger_name,ledger_id:ledger_id,default_ledger_id:default_ledger_id},
            success:function(j){
              if(!j.status){
                alert(j.error);
              }else{
                $('#edit_ledgers').modal('hide'); 
                $('#search_list').trigger('click');
              }
            },error:function(){

            }
          })
        }else{
          alert('Add valid ledger name!');
        }
      })

      $('#setDefaultLedger').click(function(){
        var default_ledger_name = $('#edit_ledgers #default_ledger_name > option:selected').text();
        if(default_ledger_name) $('#edit_ledgers #ledger').val(default_ledger_name);
        
      })

    });

    function GetLedgersDefault(data = {}) {
      
        /*$('#search_list').attr('disabled',true);*/
        var comp_table = $('#ledger_table').DataTable({
            'ajax': {
                url: '<?= base_url(); ?>superadmin/LedgersUpdate/GetLedgersDefault',
                type: 'post',
                data: data,
            },
            'paging': true,
            'searching': true,
            "bStateSave": true,
            'ordering': true,
            'columns': [
                {'data': 'ledger_id'},
                {'data': 'main_group'},
                {'data': 'sub_group_1', "sType": "mystring"},
                {'data': 'sub_group_2', "sType": "mystring"},
                {'data': 'ledger_name', "sType": "mystring"},
                {'data': 'action'}],
            'order': [[0, 'desc']],
            "columnDefs": [
                {"visible": false, "targets": [0]},
                {"orderable": false, "targets": [5]}
            ],
            "fnDrawCallback": function (oSettings) {
                var rowCount = this.fnSettings().fnRecordsDisplay();
                if (rowCount <= 10) {
                    $('.dataTables_length, .dataTables_paginate').hide();
                } else {
                    $('.dataTables_length, .dataTables_paginate').show();
                }
            },"success":function(){
              
              $('#search_list').attr('disabled',false);
            }
        });
        
        return comp_table;
    }
</script>