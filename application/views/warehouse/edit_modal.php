<div id="edit_warehouse" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Edit Warehouse</h4>
            </div>

            <form name="frm_warehouse_edit" id="frm_warehouse_edit" method="post" >
                <div class="modal-body">
                    <div id="loader">
                        <h1 class="ml8">
                            <span class="letters-container">
                                <span class="letters letters-left">
                                    <img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px">
                                </span>
                            </span>
                            <span class="circle circle-white"></span>
                            <span class="circle circle-dark"></span>
                            <span class="circle circle-container">
                                <span class="circle circle-dark-dashed"></span>
                            </span></h1>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="warehouse_name_edit">
                                    Warehouse Name<span class="validation-color">*</span>
                                </label>
                                <input type="text"  class="form-control" id="warehouse_name_edit" name="warehouse_name_edit"  maxlength="30">
                                <span class="validation-color" id="err_warehouse_name_edit"><?php echo form_error('warehouse_name_edit'); ?></span>
                                <input type="hidden" name="warehouse_id_edit" id="warehouse_id_edit" value="">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Country<span class="validation-color">*</span></label>
                                <select class="form-control country select2" id="cmb_country_edit1" name="cmb_country_edit1" style="width: 100%;">
                                    <?php
                                    foreach ($country as $row) {
                                        echo "<option value='" . $row->country_id . "'>" . $row->country_name . "</option>";
                                    }
                                    ?>
                                </select>
                                <span class="validation-color" id="err_cmb_country_edit"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">State<span class="validation-color">*</span></label>
                                <select class="form-control state select2" id="cmb_state_edit" name="cmb_state_edit" style="width: 100%;">
                                    <option value="">Select State</option>
                                </select>
                                <span class="validation-color" id="err_state_edit"><?php echo form_error('cmb_state_edit'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">City<span class="validation-color">*</span></label>
                                <select class="form-control country select2" id="cmb_city_edit" name="cmb_city_edit" style="width: 100%;">                                   
                                </select>
                                <span class="validation-color" id="err_city_edit"><?php echo form_error('cmb_city_edit'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">  
                            <div class="form-group">
                                <label for="warehouse_address_edit">Warehouse Address<span class="validation-color">*</span></label>
                                <textarea class="form-control" id="warehouse_address_edit" rows="2" name="warehouse_address_edit" maxlength="1000"></textarea>
                                <span class="validation-color" id="err_warehouse_address_edit"><?php echo form_error('warehouse_address_edit'); ?></span>
                                <!-- <label for="warehouse_address_edit">Warehouse Address<span class="validation-color">*</span> </label>
                                <input type="text" class="form-control" id="warehouse_address_edit" name="warehouse_address_edit" value=""  maxlength="120">
                                <span class="validation-color" id="err_warehouse_address_edit"><?php echo form_error('warehouse_address_edit'); ?></span> -->
                            </div>  
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="warehouse_update" class="btn btn-info"> Update </button>
                    <button type="button" class="btn btn-info" class="close" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>                
        </div>
    </div>
</div>
<script>
    var warehouse_name_count_edit = 0;
    $('#cmb_country_edit1').change(function () {
      var id = $(this).val();
        $('#cmb_state_edit').empty();
        $('#cmb_city_edit').empty();
        $.ajax({
            url: base_url + 'general/get_state/',
            method: "POST",
            data: {id: id},
            dataType: "JSON",
            success: function (data) {
            
                var state = $('#cmb_state_edit');
                var opt = document.createElement('option');
                opt.text = 'Select State';
                opt.value = '';
                state.append(opt);
                var city = $('#cmb_city_edit');
                var opt1 = document.createElement('option');
                opt1.text = 'Select City';
                opt1.value = '';
                city.append(opt1);
                for (i = 0; i < data.length; i++) {
                    $('#cmb_state_edit').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');
                }
            }
        });
    });
    $('#cmb_state_edit').change(function () {
        var id = $(this).val();
        $('#cmb_city_edit').empty();
        $.ajax({
            url: base_url + 'general/get_city/' + id,
            dataType: "JSON",
            success: function (data) {
                
                var city = $('#cmb_city_edit');
                var opt = document.createElement('option');
                opt.text = 'Select City';
                opt.value = '';
                city.append(opt);
                for (i = 0; i < data.length; i++) {
                    $('#cmb_city_edit').append('<option value="' + data[i].city_id + '">' + data[i].city_name + '</option>');
                }
                var opt1 = document.createElement('option');
                opt1.text = 'Others';
                opt1.value = '0';
                city.append(opt1);
            }
        });
    });
    $(document).on("click", ".edit_warehouse", function () {
        var id = $(this).data('id');
        $.ajax({
            url: base_url + 'warehouse/edit_modal/' + id,
            dataType: 'JSON',
            method: 'POST',
            success: function (result) {
                var data = result['data'];
                $('#cmb_country_edit1').val(data[0].warehouse_country_id).trigger('change');
                var state_list = result['state_list'];
                var city_list = result['city_list'];
                $('#cmb_state_edit').append(state_list);
                $('#cmb_city_edit').append(city_list);
                $('#warehouse_id_edit').val(data[0].warehouse_id);
                $('#warehouse_name_edit').val(data[0].warehouse_name);
                $('#warehouse_address_edit').val(data[0].warehouse_address);
                $('#cmb_state_edit').val(data[0].warehouse_state_id);
                $('#cmb_city_edit').val(data[0].warehouse_city_id);
            }
        });
    });
    $('[name=warehouse_name_edit]').on('keyup', function() {
        var warehouse_name_edit = $(this).val();
        var warehouse_id_edit = $('#warehouse_id_edit').val();
        if (warehouse_name_edit != '') {
            if (typeof xhr != 'undefined') {
                if (xhr.readyState != 4) xhr.abort();
            }
            $('#err_warehouse_name_edit').text('');
            xhr = $.ajax({
                url: base_url + 'warehouse/WarehouseNameValidationEdit',
                type: 'post',
                data: {
                    warehouse_name_edit : warehouse_name_edit,
                    warehouse_id_edit : warehouse_id_edit
                },
                dataType: 'json',
                success: function(json) {
                    if (json[0].num > 0) {
                        $('#err_warehouse_name_edit').text('Name already used!');
                        warehouse_name_count_edit = 1;
                        return false;
                    }else{
                        warehouse_name_count_edit = 0;
                    }
                },
                complete: function() {

                }
            })
        }
    });
    $(document).ready(function () {
        $("#warehouse_update").click(function (event) {
            var warehouse_name = $('#warehouse_name_edit').val();
            var warehouse_address = $('#warehouse_address_edit').val();
            var country = $('#cmb_country_edit1').val();
            var state = $('#cmb_state_edit').val();
            var city = $('#cmb_city_edit').val();
            if (warehouse_name == null || warehouse_name == "") {
                $("#err_warehouse_name_edit").text("Please Enter Warehouse Name.");
                return false;
            } else {
                $("#err_warehouse_name_edit").text("");
            }
            if (country == null || country == "") {
                $("#err_country_edit").text("Please Select Country.");
                return false;
            } else {
                $("#err_country_edit").text("");
            }
            if (state == null || state == "") {
                $("#err_state_edit").text("Please Select State.");
                return false;
            } else {
                $("#err_state_edit").text("");
            }
            if (city == null || city == "") {
                $("#err_city_edit").text("Please Select City.");
                return false;
            } else {
                $("#err_city_edit").text("");
            }
            if (warehouse_address == null || warehouse_address == "") {
                $("#err_warehouse_address_edit").text("Please Enter Warehouse Address.");
                return false;
            } else {
                $("#err_warehouse_address_edit").text("");
            }
            var form_data = $('#frm_warehouse_edit').serializeArray();
            if(warehouse_name_count_edit != 1){
                $("#loader").show();
                $.ajax({
                    url: base_url + 'warehouse/edit_warehouse',
                    dataType: 'JSON',
                    method: 'POST',
                    data: form_data,
                     beforeSend: function(){
                         // Show image container
                        $("#loader").show();
                    },
                    success: function (result) {
                        setTimeout(function () {
                            location.reload();
                            $("#loader").hide();
                        });
                    }
                });
                anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
            }else{
                $('#err_warehouse_name_edit').text('Name already used!');
            }
    });

        });

</script>