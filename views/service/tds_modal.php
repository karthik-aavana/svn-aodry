    <div id="tds_modal" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>TDS Lookup</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <div class="form-group">
                        TDS Section
                        <select class="form-control select2" id="tds_section" name="tds_section" style="width: 30%;">
                            <?php
                            foreach ($tds_section as $row)
                            {
                                echo "<option value='$row->section_id'> Section $row->section_name</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table id="tds_table" width="100%" class="table table-bordered table-striped tds_table ">
                                <thead>
                                  <!-- <th>Sl No</th> -->
                                <th>TDS Value</th>
                            <!--     <th>TDS Id</th> -->
                                <th>Description</th>
                                <th>Module Type</th>
                                <th>Action</th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var tcs_tbl = '';
    $(document).ready(function () {
        tcs_tbl = tds_data_table();
 
        $("#tds_table").on("click", "span.apply", function (event)
        {
            
             var row = $(this).closest("tr");
             var tds_value = +row.find('#accounting_tds').text();
             var tds_id = +row.find('#accounting_tds_id').text();
       
            var section = $(this).data('id');   
           var section=$('#section').val();
           
             if(section=="edit_expense")
             {
                
                 $('#service_tds_code_e').val(tds_value);
                 $('#tds_id_e').val(tds_id);
             }
            
             else
             {
  
                 $('#service_tds_code').val(tds_value);
                 $('#service_tds_code1').val(tds_value);
                 $('#tds_id').val(tds_id);
             }           
          
        }); 
        $('#tds_section').change(function ()
        {
            $("#tds_table").dataTable().fnDestroy();
            tds_data_table();
        });
    });
    function tds_data_table()
    {
        if(tcs_tbl){
            $("#tds_table").dataTable().fnDestroy();
        }
        var tbl = $('#tds_table').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "service/tds_list",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>', 'tds_section': $('#tds_section').val()}
            },
            "columns": [
                {"data": "tds_value"},
                 {"data": "description"},
                {"data": "module_type"},
               
                {"data": "action"}
            ]
        });
        return tbl;
    }
</script>