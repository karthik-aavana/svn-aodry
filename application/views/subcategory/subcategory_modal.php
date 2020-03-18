<div id="subcategory_modal" class="modal fade z_index_modal" role="dialog" data-backdrop="static">

    <div class="modal-dialog modal-md">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Add Subcategory</h4>
            </div>
            <div class="modal-body">               
                <div class="row">
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
                        </span>
                    </h1>
                </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="category_id_model">Category Type <span class="validation-color">*</span></label>

                            <input type="hidden" name="category_id_model" value="" id="category_id_model" class="form-control"/>

                            <input type="text" name="category_name" value="" id="category_name" class="form-control" readonly=""/>

                            <!-- <select name="category_id_model" id="category_id_model" class="form-control" readonly>

                                <option value="">Select Category Type</option>

                            <?php

                            foreach ($product_category as $row) {

                                echo "<option value='$row->category_id'>$row->category_name</option>";

                            }

                            ?>

                            </select>  -->

                            <span class="validation-color" id="err_category_id_model"><?php echo form_error('category_id_model'); ?></span>

                        </div>

                       </div>

                        <div class="col-sm-6">

                        <div class="form-group">

                            <label for="subcategory_name">Subcategory Type<span class="validation-color">*</span></label>

                            <input type="input" class="form-control" name="subcategory_name" id="subcategory_name" maxlength="50">

                            <input type="hidden" name="subcategory_item_type" id="subcategory_item_type">

                            <input type="hidden" name="category" id="category">

                            <span class="validation-color" id="err_subcategory_name"><?php echo form_error('subcategory_name'); ?></span>

                        </div>

                        </div>

                </div>

            </div>

            <div class="modal-footer">

                <button id="add_subcategory" class="btn btn-info" >Add</button>

                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>

            </div>

        </div>

    </div>

</div>

<script type="text/javascript">

    $(document).on("click", '.new_subcategory', function (e) {

        var type = $(this).data('name');

        $("#subcategory_item_type").val(type);

        if (type == 'product')

        {

            $('#category').val($('#product_category').val());

        } else

        {

            $('#category').val($('#service_category').val());

        }

    });

    var subcategory_ajax = "yes";

    $(document).on("click", ".new_subcategory", function ()

    {

        var new_category = $('#category').val();

        var subcategory_type = $('#subcategory_item_type').val();

        $.ajax({

            url: base_url + 'category/get_category_ajax',

            dataType: 'JSON',

            method: 'POST',

            data: {

                'new_category': $('#category').val(),

                'new_type': subcategory_type

            },

            success: function (result)

            {

                var data = result['data'];

                $('#category_id_model').html('Select Category Type');

                //$('#category_id_model').append('<option value="">Select</option>');

                for (i = 0; i < data.length; i++)

                {

                    if (result.id == data[i].category_id)

                    {

                        //$('#category_id_model').append('<option value="' + data[i].category_id + '">' + data[i].category_name + '</option>');

                       //console.log(data[i].category_id, data[i].category_name, $('#category_name'));



                        $('#subcategory_modal #category_id_model').val(data[i].category_id);

                        $('#subcategory_modal #category_name').val(data[i].category_name);

                    }

                }

            }

        });

    });

</script>

<script src="<?php echo base_url('assets/js/subcategory/') ?>subcategory.js"></script>