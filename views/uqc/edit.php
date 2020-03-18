<?php defined('BASEPATH') OR exit('No direct script access allowed');
$this -> load -> view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> <!-- Dashboard --> Dashboard</a></li>
                <li><a href="<?php echo base_url('uqc'); ?>">UQC</a></li>
                <li class="active"> Edit UQC</li>
            </ol>
        </h5>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit UQC</h3>
                    </div>
                    <!-- /.box-header -->
                    <?php
                    foreach ($data as $key)
                    {
                        # code...
                        ?>
                        <div class="box-body">
                            <form role="form" id="form" method="post" action="<?php echo base_url('uqc/edit_uqc'); ?>">
                                <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="uom">UOM (Unit of Measurement)<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="uom" name="uom" value="<?php echo $key -> uom; ?>">
                                        <input type="hidden" name="id" value="<?php echo $key->id ?> ">
                                        <span class="validation-color" id="err_uom"><?php echo form_error('uom'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="description">Description<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo $key -> description; ?>">
                                        <span class="validation-color" id="err_description"><?php echo form_error('description'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="box-footer mt-15">
                                        <button type="submit" id="submit" class="btn btn-info">Update</button>
                                        <span class="btn btn-info" id="cancel" onclick="cancel('uqc')"><!-- Cancel -->Cancel</span>
                                    </div>
                                </div>
                                </div>
                            </form>
                        </div>
                        <?php } ?>
                </div>
                
            </div>
            
        </div>
        
</div>

</section>

<?php $this -> load -> view('layout/footer'); ?>
<script>
	$(document).ready(function() {
        var name_regex = /^[-a-zA-Z\s]+$/;
		$("#submit").click(function(event) {
			var uom = $('#uom').val().trim();
			var description = $('#description').val().trim();
			if (uom == null || uom == "") {
				$("#err_uom").text("Please Enter UOM");
				return false;
			} else {
				$("#err_uom").text("");
			}
			//UOM name validation complite.
			if (description == null || description == "") {
				$("#err_description").text("Please Enter Description");
				return false;
			} else {
				$("#err_description").text("");
			}
			//description name validation complite.
		});
		$("#uom").on("blur keyup", function(event) {
			//var name_regex = /^[-a-zA-Z\s]+$/;
			var uom = $('#uom').val();
			if (uom == null || uom == "") {
				$("#err_uom").text("Please Enter UOM");
				return false;
			} else {
				$("#err_uom").text("");
			}
		});
		$("#description").on("blur keyup", function(event) {
			//var name_regex = /^[-a-zA-Z\s]+$/;
			var description = $('#description').val();
			if (description == null || description == "") {
				$("#err_description").text("Please Enter Description");
				return false;
			} else {
				$("#err_description").text("");
			}
		});
	});
</script>
