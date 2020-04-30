<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    </section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Assign Modules To User Group</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Assign Modules To User Group</h3>
                    </div>
                    <div class="row filter-margin">
                        <div class="col-sm-3">
                            <select class="form-control select2" id="cmb_group" class="group" name="cmb_group" style="width: 100%;">
                                <option value="">Select User Group*</option>
                                <?php
                                foreach ($group_name as $key) {
                                    ?>
                                    <option value='<?php echo $key->id ?>' ><?php echo $key->name; ?> </option>
                                <?php } ?>
                            </select>
                            <span class="validation-color" id="err_user_module"></span>
                        </div>
                        <div class="col-sm-4 pl-2">
                            <button type="button" class="btn btn-primary tbl-btn" id="search_module">
                                Search
                            </button>
                            <button type="button" class="btn btn-primary tbl-btn" id="check_all">
                                Check All
                            </button>
                            <button type="button" class="btn btn-primary tbl-btn" id="uncheck_all">
                                Uncheck All
                            </button>
                        </div>
                   </div>
                    <div class="box-body">
                        <table id="module_group_list" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                               <tr>
                                    <th width="14%">Select</th>
                                    <th width="20%">Module Name</th> 
                                    <th width="14%">View</th>                                  
                                    <th width="14%">Add</th>
                                    <th width="14%">Edit</th>
                                    <th width="14%">Delete</th>
                                </tr>
                            </thead>
                        <tbody></tbody>
                    </table>
                    <div class="bottom pull-right">
                        <button type="submit" id="module_submit" class="btn btn-info">Assign</button>
                        <span class="btn btn-default" id="cancel" onclick="cancel('auth/dashboard')">
                                    Cancel</span>
                    </div>
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
<script>
$(document).ready(function () {
    var table = $('#module_group_list').dataTable({
        'paging':false,
        'search':false,
        'info':false,
        'filter':false
});
    $(document).on('click','#search_module',function(){
        var module_name = $('#cmb_group').val();
        /*$('#module_group_list').dataTable().fnClearTable();*/
        if (module_name == null || module_name == "") {
            $("#err_user_module").text("Please Select User Group.");
            return false;
        } else {
            $("#err_user_module").text("");
        }
        GetAllModules();
    })
    $(document).on('change','input[name="module"]',function(){
        var module_id = $(this).attr('data-id');
        if($(this).prop("checked") == true){
            $(this).parents('tr:first').find("[name=add]").prop('checked',true);
            $(this).parents('tr:first').find("[name=edit]").prop('checked',true);
            $(this).parents('tr:first').find("[name=view]").prop('checked',true);
            $(this).parents('tr:first').find("[name=delete]").prop('checked',true);
        }else{
            $(this).parents('tr:first').find("[name=add]").prop('checked',false);
            $(this).parents('tr:first').find("[name=edit]").prop('checked',false);
            $(this).parents('tr:first').find("[name=view]").prop('checked',false);
            $(this).parents('tr:first').find("[name=delete]").prop('checked',false);
        }
    });
    $(document).on('change','input[name="add"],input[name="edit"],input[name="delete"],input[name="view"]',function(){
        if($(this).parents('tr:first').find("[name='module']").is(':checked') == false){
               /*alert('First select module first');*/
                alert_d.text = 'First select module first';
                PNotify.error(alert_d);
               $(this).prop('checked', false);
        }
    });
    $('#check_all').click(function(){
        var module_name = $('#cmb_group').val();
        if (module_name == null || module_name == "") {
            $("#err_user_module").text("Please Select User Group.");
            return false;
        } else {
            $("#err_user_module").text("");
        }
        $(".reset-check").prop("checked", true);
    });
    $('#uncheck_all').click(function(){
        var module_name = $('#cmb_group').val();
        if (module_name == null || module_name == "") {
            $("#err_user_module").text("Please Select User Group.");
            return false;
        } else {
            $("#err_user_module").text("");
        }
        $(".reset-check").prop("checked", false);
    });
    $(document).on('click','#module_submit',function(){
        var module_data = {};
        var group_id = $('#cmb_group').val();
        $.each($("input[name='module']:checked"), function(){
            /*var module_id = $(this).attr('data-id');*/
            var module_id = $(this).val();
            var data_module_name =$(this).attr('data-modulename');
            data_modulename = data_module_name.trim();
            data_modulename = data_modulename.replace(/ /g,"_");
            data_modulename = data_modulename.toLowerCase();
            module_data[data_modulename] = {};
            module_data[data_modulename]['module_id'] = module_id;
            module_data[data_modulename]['module_name'] = data_module_name;
            module_data[data_modulename]['module'] = 1;
            module_data[data_modulename]['add'] = 0;
            module_data[data_modulename]['add'] = 0;
            module_data[data_modulename]['edit'] = 0;
            module_data[data_modulename]['view'] = 0;
            module_data[data_modulename]['delete'] = 0;

            if($(this).parents('tr:first').find("[name=add]").is(':checked')){
                module_data[data_modulename]['add'] = 1;
            }
            if($(this).parents('tr:first').find("[name=edit]").is(':checked')){
                module_data[data_modulename]['edit'] = 1;
            }
            if($(this).parents('tr:first').find("[name=view]").is(':checked')){
                module_data[data_modulename]['view'] = 1;
            }
            if($(this).parents('tr:first').find("[name=delete]").is(':checked')){
                module_data[data_modulename]['delete'] = 1;
            }
        });
        //module_data_json = JSON.stringify(module_data);
        if($.isEmptyObject(module_data)){
            alert_d.text = 'Select Atleast One Module';
            PNotify.error(alert_d);
        }else{
            $.ajax({
                url:'<?=base_url();?>Group_assign/add_active_modules_group',
                type:'post',
                dataType:'json',
                data:{module_data:module_data, group_id:group_id},
                success:function(result){
                    if(result.flag){
                        alert_d.text = result.msg;
                        PNotify.success(alert_d);
                        $('#module_group_list').dataTable().fnClearTable();
                        $('#cmb_group').prop('selectedIndex',0);
                        $('#cmb_group').select2();
                    }else{
                        alert_d.text = result.msg;
                        PNotify.error(alert_d);
                    }
                },
                error:function(msg){
                    alert_d.text = 'Something Went Wrong';
                    PNotify.error(alert_d);
                }
            });
        }
    });
});

function GetAllModules(page = 1){
    var group_id = $('#cmb_group').val();
    $('#search_voucher-popup').modal('hide');
    $.ajax({
        url:'<?=base_url();?>Group_assign/find_active_modules_group',
        type:'post',
        dataType:'json',
        data:{group_id:group_id},
        success:function(j){
           $('#module_group_list tbody').html(j);
        },
        error: function(msg){
            alert_d.text = 'Something Went Wrong';
            PNotify.error(alert_d);
        }
    })
}
</script>