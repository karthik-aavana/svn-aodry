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
                <li class="active"> Add UQC</li>
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
                        <h3 class="box-title">Add UQC</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('uqc/add_uqc'); ?>">
                            <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="uom">UOM (Unit of Measurement)123<span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="uom" name="uom" value="">
                                    <span class="validation-color" id="err_uom"><?php echo form_error('uom'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="description">Description<span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="description" name="description" value="">
                                    <span class="validation-color" id="err_description"><?php echo form_error('description'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-4 mt-15">
                                <div class="box-footer">
                                    <button type="submit" id="submit" class="btn btn-info">Add</button>
                                    <span class="btn btn-info" id="cancel" onclick="cancel('uqc')"><!-- Cancel -->Cancel</span>
                                </div>
                            </div>
                            </div>
                        </form>
                    </div>
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
            console.log(uom);
			var description = $('#description').val().trim();
			if (uom == null || uom == "") {
				$("#err_uom").text("Please Enter UOM");
				return false;
			} else {
				$("#err_uom").text("");
			}
        });
        if(!(uom == null || uom == "")) {
                var i,temp="";
                var addr_trim=$.trim(uom);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_uom").text("Please Enter Valid Address.");
                            return false; 
                        }
                    }  
                }  
            }
			//UOM name validation complite.
			if (description == null || description == "") {
				$("#err_description").text("Please Enter Description");
				return false;
			} else {
				$("#err_description").text("");
			}
			//description name validation complite.
		
		$("#uom").on("blur", function(event) {
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
