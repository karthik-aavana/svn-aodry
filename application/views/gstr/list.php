<?php
   defined('BASEPATH') OR exit('No direct script access allowed');
   $this->load->view('layout/header');
   ?>
<div class="content-wrapper">
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">GSTR</h3>
            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url()?>">Back</a>
          </div>
          <div class="box-body">
            <div class="row">
              <form method="post" action="<?php echo base_url() ?>gst_report/download_report/" id="gst_report_download">
                <div class="form-group col-sm-2">
                  <select class="form-control select2" name="gstr_filter_type" id="gstr_filter_type">
                    <option value="monthly">Monthly</option>
                    <option value="custom">Custom</option>
                  </select>
                </div>
                <span id="gstr_monthly">
                  <div class="form-group col-sm-2">
                    <div class="input-group date">
                      <input type="text" class="form-control datepicker" name="from_month" id="from_month" placeholder="Month" autocomplete="off">
                      <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                    <span class="validation-color" id="err_from_month"></span>
                  </div> </span>
                <span id="gstr_custom" style="display: none;">
                  <div class="form-group col-sm-2">
                    <div class="input-group date">
                      <input type="text" class="form-control datepicker" name="from_date" id="from_date" placeholder="From Date" autocomplete="off">
                      <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                    <span class="validation-color" id="err_from_date"></span>
                  </div>
                  <div class="form-group col-sm-2">
                    <div class="input-group date">
                      <input type="text" class="form-control datepicker" name="to_date" id="to_date" placeholder="To Date" autocomplete="off">
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                    <span class="validation-color" id="err_to_date"></span>
                  </div> </span>
                <div class="form-group col-sm-2">
                  <select class="form-control select2" name="gst_report_type" id="gst_report_type">
                    <option value="">Report Type</option>
                    <option value="gstr1">GSTR1</option>
                    <!-- <option value="gstr1_advance">GSTR1 Advances</option>
                    <option value="gstr1_b2cl">GSTR1 B2CL</option>
                    <option value="gstr1_b2cs">GSTR1 B2CS</option>
                    <option value="credit_debit_note_b2b">Credit Debit Note - B2B</option>
                    <option value="credit_debit_note_b2c">Credit Debit Note - B2C</option>
                    <option value="documents">GSTR1 Documents</option>
                    <option value="exempt_supply">Exempt Supply</option>
                    <option value="gstr1_exports">GSTR1 Exports</option>
                    <option value="hsn_summary">HSN Summary</option> -->
                  </select>
                  <span class="validation-color" id="err_gst_report_type"></span>
                </div>
                <div class="form-group col-sm-2">
                  <select class="form-control select2" name="download_format_type" id="download_format_type">
                    <option value="">Select Export Type</option>
                    <option value="excel">Export to Excel</option>
                    <option value="csv">Export to CSV</option>
                    <option value="pdf">Export to PDF</option>
                  </select>
                  <span class="validation-color" id="err_download_format_type"></span>
                </div>
                <div class="modal fade" id="myModallist" role="dialog">
                  <div class="modal-dialog" style="width: 30%">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                          &times;
                        </button>
                        <h4 class="modal-title">GSTR1 Report Types</h4>
                      </div>
                      <div class="modal-body">
                        <table class="table table-bordered">
                          <thead style="color: blue">
                            <tr>
                              <th scope="col">Select</th>
                              <th scope="col">Report Type</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>
                              <input type="checkbox" name="report_type[]" class='gstr_types' value ="gstr1_advance" checked>
                              </td>
                              <td>GSTR1 Advances</td>
                            </tr>
                            <tr>
                              <td>
                              <input type="checkbox" name="report_type[]" class='gstr_types' value ="gstr1_b2cl" checked>
                              </td>
                              <td>GSTR1 B2CL</td>
                            </tr>
                            <tr>
                              <td>
                              <input type="checkbox" name="report_type[]" class='gstr_types' value ="gstr1_b2cs" checked>
                              </td>
                              <td>GSTR1 B2CS</td>
                            </tr>
                            <tr>
                              <td>
                              <input type="checkbox" name="report_type[]" class='gstr_types' value ="credit_debit_note_b2b" checked>
                              </td>
                              <td>Credit Debit Note - B2B</td>
                            </tr>
                            <tr>
                              <td>
                              <input type="checkbox" name="report_type[]" class='gstr_types' value ="credit_debit_note_b2c" checked>
                              </td>
                              <td>Credit Debit Note - B2C</td>
                            </tr>
                            <tr>
                              <td>
                              <input type="checkbox" name="report_type[]" class='gstr_types' value ="credit_debit_note_b2cs" checked>
                              </td>
                              <td>Credit Debit Note - B2Cs</td>
                            </tr>
                            <tr>
                              <td>
                              <input type="checkbox" name="report_type[]" class='gstr_types' value ="documents" checked>
                              </td>
                              <td>GSTR1 Documents</td>
                            </tr>
                            <tr>
                              <td>
                              <input type="checkbox" name="report_type[]" class='gstr_types' value ="exempt_supply" checked>
                              </td>
                              <td>Exempt Supply</td>
                            </tr>
                            <tr>
                              <td>
                              <input type="checkbox" name="report_type[]" class='gstr_types' value ="gstr1_exports" checked>
                              </td>
                              <td>GSTR1 Exports</td>
                            </tr>
                            <tr>
                              <td>
                              <input type="checkbox" name="report_type[]" class='gstr_types' value ="hsn_summary" checked>
                              </td>
                              <td>HSN Summary</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-primary tbl-btn download_search" id="download_sheet">Export</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal fade" id="myModalpdflist" role="dialog">
                  <div class="modal-dialog" style="width: 30%">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                          &times;
                        </button>
                        <h4 class="modal-title">Select file to download</h4>
                      </div>
                      <div class="modal-body">
                        <table id='paths' class="table table-bordered">
                          <!-- <thead style="color: blue">
                          <tr>
                          <th scope="col">Select file to be download</th>
                          </tr>
                          </thead> -->
                          <tbody>
                            <!-- <?php foreach ($path_data as $key => $value) {?>
                            <tr>
                              <td><a href="<?php echo base_url($value[0]); ?>" target="_blank"><?= $value[1] ?></a></td>
                            </tr>
                            <?php } ?> -->
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group col-sm-2">
                  <!-- <button type="button" class="btn btn-primary tbl-btn download_search" id="download_sheet">
                    Export
                  </button> -->
                  <button type="button" class="btn btn-primary tbl-btn reset_filter" id="reset_filter">
                    Reset
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php $this->load->view('layout/footer'); ?>
<script type="text/javascript">
  /*$(document).ready(function(){
    <?php if(!empty($path_data)){?>
      $('#myModalpdflist').modal('show');
    <?php }?>
  });*/
  
   $(document).on('change', "#gstr_filter_type", function () {
          var filter_type = $(this).val();
          if (filter_type == "") {
              $("#gstr_custom").hide();
              $("#gstr_monthly").hide();
          }
          if (filter_type == "custom") {
              $("#gstr_custom").show();
              $("#gstr_monthly").hide();
          }
          if (filter_type == "monthly") {
              $("#gstr_monthly").show();
              $("#gstr_custom").hide();
          }
      });
   $(document).on('change', "#download_format_type", function () {
          var download_type = $(this).val();
          var gstr_filter_type = $('#gstr_filter_type').val();
          var from_date = $('#from_date').val();
          var to_date = $('#to_date').val();
          var from_month = $('#from_month').val();
          var gst_report_type = $('#gst_report_type').val();
          var download_format_type = $('#download_format_type').val();
          var gstr_types = [];
          $.each($(".gstr_types:checked"), function(){
              gstr_types.push($(this).val());
          });
          if(gstr_filter_type == 'monthly'){
          if (from_month == null || from_month == "") {
              $("#err_from_month").text("Please Select Month And Year.");
              $('#download_format_type').prop('selectedIndex',0);
              $('#download_format_type').select2();
              return false;
            } else {
                $("#err_from_month").text("");
            }  
          }
          if(gstr_filter_type == 'custom'){
            if (from_date == null || from_date == "") {
              $("#err_from_date").text("Please Select From Date.");
              $('#download_format_type').prop('selectedIndex',0);
              $('#download_format_type').select2();
              return false;
            } else {
                $("#err_from_date").text("");
            }  
            if (to_date == null || to_date == "") {
              $("#err_to_date").text("Please Select To Date.");
              $('#download_format_type').prop('selectedIndex',0);
              $('#download_format_type').select2();
              return false;
            } else {
                $("#err_to_date").text("");
            }
          }
          if(gst_report_type == null || gst_report_type == ""){
            $("#err_gst_report_type").text("Please Select Report Type.");
            $('#download_format_type').prop('selectedIndex',0);
            $('#download_format_type').select2();
            return false;
          }else{
            $("#err_gst_report_type").text("");
          }
          if(download_format_type == null || download_format_type == ""){
            $("#err_download_format_type").text("Please Select Export Type.");
            return false;
          }else{
            $("#err_download_format_type").text("");
          }
          if(download_type == 'excel'){
            $('#myModallist').modal('show');
          }
          if(download_type == 'csv' || download_type == 'pdf'){
            $.ajax({
                url: '<?= base_url(); ?>Gst_report/download_report',
                type: 'post',
                dataType: 'json',
                data: {gstr_filter_type: gstr_filter_type, from_date: from_date, to_date:to_date, from_month:from_month,gst_report_type: gst_report_type, download_format_type:download_format_type},
                success: function (data) {
                    $('#download_format_type').prop('selectedIndex',0);
                    if(!$.trim(data)){
                      alert_d.text = 'Something Went Wrong';
                      PNotify.error(alert_d);
                    }else{
                      var path_new = '';
                      if(data['report_format'] == 'pdf'){
                        $.each(data['path_data'],  function (index, path) { 
                          path_new += '<tr><td><a href="' + base_url + path[0] + '" target="_blank">'+ path[1]+'</a></td><tr>';  
                        });
                      }
                      if(data['report_format'] == 'csv'){
                        $.each(data['path_data'],  function (index, path) { 
                          path_new += '<tr><td><a href="' + base_url + path[0] + '">'+ path[1]+'</a></td><tr>';  
                        });
                      }
                      $('#paths').find('tbody').empty();
                      $('#paths tbody').append(path_new);
                      $('#myModalpdflist').modal('show');
                    }
                }
              });
          }       
      });
   $('#reset_filter').click(function(){
    $('#from_month , #from_date, #to_date').val('');
    $('#gst_report_type').prop('selectedIndex',0);
    $('#gst_report_type').select2();
    $('#download_format_type').prop('selectedIndex',0);
    $('#download_format_type').select2();
   });
   $('.close').click(function(){
      $('#download_format_type').prop('selectedIndex',0);
   });
      $("#gstr_monthly .datepicker").datepicker( {
          format: "mm-yyyy",
          viewMode: "months", 
          minViewMode: "months"
      });
      $(document).on('click', '#download_sheet', function () {
        var gstr_filter_type = $('#gstr_filter_type').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var from_month = $('#from_month').val();
        var gst_report_type = $('#gst_report_type').val();
        var download_format_type = $('#download_format_type').val();
        var gstr_types = [];
        $.each($(".gstr_types:checked"), function(){
            gstr_types.push($(this).val());
        });
        if(gstr_filter_type == 'monthly'){
          if (from_month == null || from_month == "") {
            $("#err_from_month").text("Please Select Month And Year.");
            return false;
          } else {
              $("#err_from_month").text("");
          }  
        }
        if(gstr_filter_type == 'custom'){
          if (from_date == null || from_date == "") {
            $("#err_from_date").text("Please Select From Date.");
            return false;
          } else {
              $("#err_from_date").text("");
          }  
          if (to_date == null || to_date == "") {
            $("#err_to_date").text("Please Select To Date.");
            return false;
          } else {
              $("#err_to_date").text("");
          }
        }
        if(gst_report_type == null || gst_report_type == ""){
          $("#err_gst_report_type").text("Please Select Report Type.");
          return false;
        }else{
          $("#err_gst_report_type").text("");
        }
        if(download_format_type == null || download_format_type == ""){
          $("#err_download_format_type").text("Please Select Export Type.");
          return false;
        }else{
          $("#err_download_format_type").text("");
        }
        if(gstr_types.length == 0){
          alert_d.text = 'Please Check At Least One Checkbox';
          PNotify.error(alert_d);
          return false; 
        }
        $('#myModallist').modal('hide');
        $('#gst_report_download').submit();
        $('#download_format_type').prop('selectedIndex',0);
        /*$.ajax({
                url: '<?= base_url(); ?>Gst_report/ajax_count_reports',
                type: 'post',
                dataType: 'json',
                data: {gstr_filter_type: gstr_filter_type, from_date: from_date, to_date:to_date, from_month:from_month,gst_report_type: gst_report_type, download_format_type:download_format_type},
                success: function (data_count) {
                    if(data_count == 0){
                      alert_d.text ='No Report Data Is Available In-Between This Period';
                      PNotify.error(alert_d);
                    }else{
                      if(download_format_type == 'pdf'){
                        $('#gst_report_download').attr('target', '_blank');
                      }
                        $('#gst_report_download').submit();
                    }
                }
              });*/
      });
</script>