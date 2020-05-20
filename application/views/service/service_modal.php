<div id="service_modal" class="modal fade" role="dialog" data-backdrop="static">    <div class="modal-dialog">               <div class="modal-content">            <div class="modal-header">                <button type="button" class="close" data-dismiss="modal">&times;</button>                <h4 class="modal-title">Add Service</h4>            </div>            <form id="serviceForm">                <div class="modal-body">                <div id="loader">                   <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>               </div>                         <div class="row">                        <div class="col-md-6">                            <div class="form-group">                                <label for="service_code">Service Code <span class="validation-color">*</span></label>                                <input type="text" class="form-control" id="service_code" name="service_code" value="<?php echo set_value('service_code'); ?>" tabindex="1">                                <span class="validation-color" id="err_service_code"><?php echo form_error('service_code'); ?></span>                            </div>                        </div>                        <div class="col-md-6">                            <div class="form-group">                                <label for="service_name">Service Name <span class="validation-color">*</span></label>                                <input type="hidden" name="service_id" id="service_id" value="0">                                <input type="text" class="form-control" id="service_name" name="service_name" value="<?php echo set_value('service_name'); ?>" tabindex="2">                                <span class="validation-color" id="err_service_name"><?php echo form_error('service_name'); ?></span>                            </div>                        </div>                    </div>                    <div class="row">                         <div class="col-md-6">                            <div class="form-group">                                <input type="hidden" class="form-control" id="c_type" name="c_type" value="service">                                <label for="service_category">Category <span class="validation-color">*</span></label>                                <div class="input-group">                                        <div class="input-group-addon">                                        <?php                                        if (in_array($category_module_id, $active_add)) {?>                                            <a href="" data-toggle="modal" data-target="#category_modal" data-name="service" class="pull-right new_category">+</a>                                        <?php } ?>                                        </div>                                    <select class="form-control select2" id="service_category" name="service_category" style="width: 100%;" tabindex="3">                                        <option value="">Select Category</option>                                        <?php                                        foreach ($service_category as $row) {                                            echo "<option value='$row->category_id'" . set_select('service_category', $row->category_id) . ">$row->category_name</option>";                                        }                                        ?>                                    </select>                                </div>                                <span class="validation-color" id="err_service_category"><?php echo form_error('service_category'); ?></span>                            </div>                        </div>                        <div class="col-md-6">                            <div class="form-group">                                <div id='subcategory_disable' class="disable_div form-group">                                    <label for="service_subcategory">Subcategory</label>                                    <div class="input-group">                                            <div class="input-group-addon">                                            <?php                                            if (in_array($subcategory_module_id, $active_add)) { ?>                                                <a href="" data-toggle="modal" data-target="#subcategory_modal" data-name="service" class="pull-right open_subcategory_modal new_subcategory">+</a>                                            <?php } ?>                                            </div>                                        <select class="form-control select2" id="service_subcategory" name="service_subcategory" style="width: 100%;" tabindex="4">                                            <option value="">Select Subcategory</option>                                        </select>                                    </div>                                    <span class="validation-color" id="err_service_subcategory"><?php echo form_error('service_subcategory'); ?></span>                                </div>                            </div>                        </div>                    </div>                    <div class="row">                        <div class="col-md-6">                            <div class="form-group">                                <label for="service_price">Unit Price <span class="validation-color">*</span></label>                                <input type="text" class="form-control" id="service_price" name="service_price"  maxlength="12" value="<?php echo set_value('service_price'); ?>" tabindex="5">                                <span class="validation-color" id="err_service_price"><?php echo form_error('service_price'); ?></span>                            </div>                        </div>                        <div class="col-md-6">                            <?php                                if ($access_settings[0]->tax_type == "gst") {                            ?>                            <div class="form-group">                                <label for="service_hsn_sac_code">HSN/SAC Code <span class="validation-color">*</span></label>                                <div class="input-group">                                    <div class="input-group-addon">                                        <a href="" data-toggle="modal" data-target="#hsn_modal" class="pull-right service_hsn_click"><span class="fa fa-eye"></span></a>                                    </div>                                    <input type="text" class="form-control" id="service_hsn_sac_code" name="service_hsn_sac_code" value="<?php echo set_value('service_hsn_sac_code'); ?>" tabindex="7">                                </div>                                <span class="validation-color" id="err_service_hsn_sac_code"><?php echo form_error('service_hsn_sac_code'); ?></span>                             </div>                                        <?php } ?>                         </div>                    </div>                    <div class="row">                        <div class="col-md-6">                            <div class="form-group">                                <label for="gst_tax">Tax(GST)</label>                                <div class="input-group">                                        <div class="input-group-addon">                                        <?php                                        if (in_array($tax_module_id, $active_add)) {?>                                            <a href="" data-toggle="modal" data-target="#tax_gst_modal" data-name="service" class="pull-right new_tax">+</a>                                        <?php } ?>                                        </div>                                    <select class="form-control select2" id="gst_tax" name="gst_tax" style="width: 100%;" tabindex="6">                                        <option value="">Select Tax</option>                                        <?php                                        foreach ($tax_gst as $row) {                                            echo "<option value='$row->tax_id'" . set_select('gst_tax', $row->tax_id . "-" . (float)$row->tax_value) . ">$row->tax_name @ " . (float)($row->tax_value) . "%</option>";                                        }                                        ?>                                    </select>                                </div>                                <span class="validation-color" id="err_service_tax"><?php echo form_error('gst_tax'); ?></span>                             </div>                                               </div>                        <div class="col-md-6">                            <div class="form-group">                                <label for="service_gst_code">GST Tax Percentage </label>                                                                                <input type="text" class="form-control" id="service_gst_code" name="service_gst_code" value="" readonly>                                <span class="validation-color" id="service_gst_code"></span>                            </div>                        </div>                    </div>                    <div class="row">                        <div class="col-md-6">                            <div class="form-group">                                <label for="tds_tax">Tax(TDS)</label>                                <div class="input-group">                                        <div class="input-group-addon">                                        <?php                                                                       if (in_array($tax_module_id, $active_add)) {?>                                            <a href="" data-toggle="modal" data-target="#tax_tds_modal" data-name="service" class="pull-right new_tax">+</a>                                        <?php } ?>                                        </div>                                    <select class="form-control select2" id="tds_tax" name="tds_tax" style="width: 100%;" tabindex="6">                                        <option value="">Select Tax</option>                                        <?php                                         foreach ($tax_tds as $row) {                                            echo "<option value='$row->tax_id'" . set_select('gst_tax', $row->tax_id . "-" . $row->tax_value) . ">$row->tax_name(Sec " . $row->section_name . ") @ " . round($row->tax_value, 2) . "%</option>";                                        }                                        ?>                                    </select>                                </div>                                <span class="validation-color" id="err_service_tax"><?php echo form_error('tds_tax'); ?></span>                                                                       </div>                                                                   </div>                        <div class="col-md-6">                            <div class="form-group">                                <label for="service_tds_code">TDS Tax Percentage </label>                                <input type="text" class="form-control" id="service_tds_code" name="service_tds_code" value="" readonly>                                <span class="validation-color" id="service_tds_code"></span>                            </div>                          </div>                    </div>                    <div class="row">                        <div class="col-md-6">                            <div class="form-group ">                                <label for="service_unit">Unit of Measurement<span class="validation-color">*</span></label>                                <div class="input-group">                                    <div class="input-group-addon">                                        <?php                                                                       if (in_array($uqc_module_id, $active_add)) {?>                                        <a href="#" data-toggle="modal" data-target="#uom_modal" data-name="service" class="pull-right new_tax" title="Add UOM">+</a>                                    <?php } ?>                                    </div>                                                                                  <select class="form-control select2" id="service_unit" name="service_unit" style="width: 100%;" tabindex="4">                                        <option value="">Select UOM</option>                                        <?php                                        foreach ($uqc_service as $value) {                                            echo "<option value='$value->id'" . set_select('brand', $value->id) . ">$value->uom - $value->description</option>";                                        }                                        ?>                                    </select>                            </div>                            <span class="validation-color" id="err_product_unit"></span>                        </div>                     </div>                                </div>                <div class="modal-footer">                    <button type="submit" id="service_modal_submit" class="btn btn-info" data-dismiss="modal">Add</button>                    <button class="btn btn-default" id="service_cancel" data-dismiss="modal">Cancel</button>                </div>            </form>        </div>    </div></div><script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script><script type="text/javascript">    var service_ajax = "yes";$(document).on('click', '.service_hsn_click', function(){    $('#hsn_modal .modal-title').text('HSN/SAC Lookup');    $('#sac_table thead th:first-child').text("HSN/SAC Code");});</script><script src="<?php echo base_url('assets/js/service/') ?>service.js"></script>