<div id="add_warehouse" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Warehouse</h4>
            </div>
            <form name="frm_warehouse_add" id="frm_warehouse_add" method="post" >
                <div class="modal-body">
                    <div id="loader_coco">
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
                                <label for="warehouse_name">
                                    Warehouse Name<span class="validation-color">*</span>
                                </label>
                                <input type="text"  class="form-control" id="warehouse_name" name="warehouse_name" maxlength="30">  
                                <span class="validation-color" id="err_warehouse_name"><?php echo form_error('warehouse_name'); ?></span>                              
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Country<span class="validation-color">*</span></label>
                                <select class="form-control country select2" id="cmb_country" name="cmb_country" style="width: 100%;">
                                    <option value="">Select Country</option>
                                    <?php
                                    foreach ($country as $row) {
                                        echo "<option value='" . $row->country_id . "'>" . $row->country_name . "</option>";
                                    }
                                    ?>
                                </select>
                                <span class="validation-color" id="err_country"><?php echo form_error('cmb_country'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">State<span class="validation-color">*</span></label>
                                <select class="form-control state select2" id="cmb_state" name="cmb_state" style="width: 100%;">
                                    <option value="">Select State</option>

                                </select>
                                <span class="validation-color" id="err_state"><?php echo form_error('cmb_state'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">City<span class="validation-color">*</span></label>
                                <select class="form-control country select2" id="cmb_city" name="cmb_city" style="width: 100%;">
                                    <option value="">Select City</option>

                                </select>
                                <span class="validation-color" id="err_city"><?php echo form_error('cmb_city'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="warehouse_address">Warehouse Address<span class="validation-color">*</span></label>
                                <textarea class="form-control" id="warehouse_address" rows="2" name="warehouse_address" maxlength="1000"></textarea>
                                <span class="validation-color" id="err_warehouse_address"><?php echo form_error('warehouse_address'); ?></span>
                                <!-- <label for="warehouse_address">Warehouse Address<span class="validation-color">*</span> </label>
                                <input type="text" class="form-control" id="warehouse_address" name="warehouse_address" value="" maxlength="120"> 
                                <span class="validation-color" id="err_warehouse_address"><?php echo form_error('warehouse_address'); ?></span>-->
                            </div>  
                        </div>  
                    </div>
                </div> 
                <div class="modal-footer">
                    <button type="button" id="warehouse_submit" class="btn btn-info"> Add </button>
                    <button type="button" class="btn btn-info" class="close" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
        </div>               
    </div>
</div>
</div>
<script>
    var warehouse_name_count = 0;
    $('#cmb_country').change(function () {
        var id = $(this).val();
        $('#cmb_state').empty();
        $('#cmb_city').empty();
        $.ajax({
            url: base_url + 'general/get_state/',
            method: "POST",
            data: {id: id},
            dataType: "JSON",
            success: function (data) {
                var state = $('#cmb_state');
                var opt = document.createElement('option');
                opt.text = 'Select State';
                opt.value = '';
                state.append(opt);
                var city = $('#cmb_city');
                var opt1 = document.createElement('option');
                opt1.text = 'Select City';
                opt1.value = '';
                city.append(opt1);
                for (i = 0; i < data.length; i++) {
                    $('#cmb_state').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');
                }
            }
        });
    });
    $('#cmb_state').change(function () {
        var id = $(this).val();
        $('#cmb_city').empty();
        $.ajax({
            url: base_url + 'general/get_city/' + id,
            dataType: "JSON",
            success: function (data) {
                var city = $('#cmb_city');
                var opt = document.createElement('option');
                opt.text = 'Select City';
                opt.value = '';
                city.append(opt);

                for (i = 0; i < data.length; i++) {
                    $('#cmb_city').append('<option value="' + data[i].city_id + '">' + data[i].city_name + '</option>');
                }
                var opt1 = document.createElement('option');
                opt1.text = 'Others';
                opt1.value = '0';
                city.append(opt1);
            }
        });
    });
    $('[name=warehouse_name]').on('keyup', function() {
        var warehouse_name = $(this).val();
        if (warehouse_name != '') {
            if (typeof xhr != 'undefined') {
                if (xhr.readyState != 4) xhr.abort();
            }
            $('#err_warehouse_name').text('');
            xhr = $.ajax({
                url: base_url + 'warehouse/WarehouseNameValidation',
                type: 'post',
                data: {
                    warehouse_name: warehouse_name
                },
                dataType: 'json',
                success: function(json) {
                    if (json[0].num > 0) {
                        $('#err_warehouse_name').text('Name already used!');
                        warehouse_name_count = 1;
                        return false;
                    }else{
                        warehouse_name_count = 0;
                    }
                },
                complete: function() {

                }
            })
        }
    });
    $("#warehouse_submit").click(function (event) {
        var warehouse_name = $('#warehouse_name').val();
        var warehouse_address = $('#warehouse_address').val();
        var country = $('#cmb_country').val();
        var state = $('#cmb_state').val();
        var city = $('#cmb_city').val();
        if (warehouse_name == null || warehouse_name == "") {
            $("#err_warehouse_name").text("Please Enter Warehouse Name.");
            return false;
        } else {
            $("#err_warehouse_name").text("");
        }
         
        if (country == null || country == "") {
            $("#err_country").text("Please Select Country.");
            return false;
        } else {
            $("#err_country").text("");
        }
        if (state == null || state == "") {
            $("#err_state").text("Please Select State.");
            return false;
        } else {
            $("#err_state").text("");
        }
        if (city == null || city == "") {
            $("#err_city").text("Please Select City.");
            return false;
        } else {
            $("#err_city").text("");
        }
        if (warehouse_address == null || warehouse_address == "") {
            $("#err_warehouse_address").text("Please Enter Warehouse Address.");
            return false;
        } else {
            $("#err_warehouse_address").text("");
        }
        var form_data = $('#frm_warehouse_add').serializeArray();
        if(warehouse_name_count != 1){
            $.ajax({
            url: base_url + 'warehouse/add_warehouse',
            dataType: 'JSON',
            method: 'POST',
            data: form_data,
            beforeSend: function(){
                     // Show image container
                    $("#loader_coco").show();
            },
            success: function (result) {
                setTimeout(function () {
                location.reload();
                 $("#loader_coco").hide();

                });                
            }
            });
             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
        }else{
            $('#err_warehouse_name').text('Name already used!');
        }
    });
</script>