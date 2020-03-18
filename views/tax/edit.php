<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard </a></li>
                <li><a href="<?php echo base_url('tax'); ?>">Tax <?php echo $this->lang->line('tax_label'); ?></a></li>
                <li class="active"><!-- Edit Tax --> Edit Tax</li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Tax</h3>
                    </div>
                    <form role="form" id="form" method="post" action="<?php echo base_url('tax/edit_tax'); ?>">
                        <div class="box-body">                      
                            <div class="row">                               
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="tax_name"><!-- Tax Name --> Tax Name <span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="tax_name" name="tax_name" value="<?php echo $data[0]->tax_name; ?>" tabindex="1">
                                        <input type="hidden" class="form-control" id="tax_id" name="id" value="<?php echo $data[0]->tax_id; ?>" >
                                        <span class="validation-color" id="err_tax_name"><?php echo form_error('tax_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group sales">
                                        <label for="tax_value">Tax Rate <span class="validation-color">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-right" id="tax_value" name="tax_value" value="<?php echo $data[0]->tax_value; ?>" tabindex="2">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                        <span class="validation-color" id="err_tax_value"><?php echo form_error('tax_value'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="description"><!-- Description --> Description</label>
                                        <textarea class="form-control" id="description" name="description" tabindex="3"><?php echo set_value('description'); ?><?php
                                            echo str_replace(array(
                                                "\r\n",
                                                "\\r\\n"), "\n", $data[0]->tax_description);
                                            ?></textarea>
                                        <span class="validation-color" id="err_description"></span>
                                    </div>
                                </div>                                  
                            </div>                        
                        </div>                          
                        <div class="modal-footer">
                            <button type="submit" id="add_tax" class="btn btn-info"><!-- Add --> Update </button>
                            <span class="btn btn-default" id="cancel" onclick="cancel('tax')"><!-- Cancel --> Cancel</span>
                        </div>
                    </form>
                </div>                   
            </div>                
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
?>
<script src="<?php echo base_url('assets/js/tax/') ?>tax.js"></script>

