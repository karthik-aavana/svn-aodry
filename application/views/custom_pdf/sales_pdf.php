<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// $p = array('admin', 'manager');
// if (!(in_array($this->session->userdata('type'), $p))) {
//     redirect('auth');
// }
// if(isset($data[0]->financial_year_password)){
//     echo $this->encryption->decrypt($data[0]->financial_year_password);
// }


$this->load->view('layout/header');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        
    </section>
    <div class="fixed-breadcrumb">
      <ol class="breadcrumb abs-ol">
        <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">Company Setting</li>
</ol>
          </div>

    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Sales PDF Setting</h3>
                    </div>
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('Custom_pdf/add_pdf_settings'); ?>" encType="multipart/form-data">
                            <div class="well">
                                <!-- <?= $pdf_results['theme']?> -->
                                <div class="row">
                                     <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Default Theme</label>
                                            <input type="radio" <?php echo ($pdf_results['theme'] =='default') ? 'checked':''   ?> value='default'  id="dtheme" name="theme">

                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Custom Theme</label>
                                            <input type="radio" <?php echo ($pdf_results['theme'] =='custom') ? 'checked':''   ?> value='custom'  id="ctheme" name="theme">

                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Background Color</label>
                                           <select  id="background" name="background">
                                                <?php
                                               $slected = $pdf_results['background'];
                                                ?>
                                                <option <?php echo ($slected=='white') ? 'selected':''?> >white</option>
                                                <option <?php echo ($slected=='black') ? 'selected':''?>>black</option>
                                                <!-- <option><?= $slected?></option> -->
                                            </select>

                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>


                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Company Name</label>
                                            <input type="checkbox" <?php echo ($pdf_results['company_name'] =='yes') ? 'checked':''   ?>  id="name" name="name">

                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Company Logo</label>
                                            <input type="checkbox" <?php echo ($pdf_results['logo'] =='yes') ? 'checked':''   ?>  id="logo" name="logo">

                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">

                                            <label for="name">Address</label>

                                            <input type="checkbox" <?php echo ($pdf_results['address'] =='yes') ? 'checked':''   ?>  id="address" name="address">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Country</label>

                                            <input type="checkbox" <?php echo ($pdf_results['country'] =='yes') ? 'checked':''   ?>  id="country" name="country">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div> 
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">State</label>
                                            <input type="checkbox"  <?php echo ($pdf_results['state'] =='yes') ? 'checked':'' ?> id="state" name="state">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">

                                          
                                            <label for="name">Mobile Number</label>
                                            <input type="checkbox" <?php echo ($pdf_results['mobile'] =='yes') ? 'checked':'' ?>   id="mobile" name="mobile">

                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">

                                            <label for="name">Landline Number</label>

                                            <input type="checkbox" <?php echo ($pdf_results['landline'] =='yes') ? 'checked':'' ?>   id="landline" name="landline">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Email</label>
                                            
                                            <input type="checkbox" <?php echo ($pdf_results['email'] =='yes') ? 'checked':'' ?>  id="email" name="email">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div> 
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">GSTIN</label>
                                            <input type="checkbox" <?php echo ($pdf_results['gst'] =='yes') ? 'checked':'' ?>   id="gst" name="gst">

                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">PAN</label>
                                            <input type="checkbox" <?php echo ($pdf_results['pan'] =='yes') ? 'checked':'' ?>  id="pan" name="pan">

                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">

                                            <label for="name">IEC</label>

                                            <input type="checkbox" <?php echo ($pdf_results['iec'] =='yes') ? 'checked':'' ?>  id="iec" name="iec">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">LUT</label>
                                             
                                            <input type="checkbox" <?php echo ($pdf_results['lut'] =='yes') ? 'checked':'' ?>  id="lut" name="lut">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">CIN</label>
                                            <input type="checkbox" <?php echo ($pdf_results['lut'] =='yes') ? 'checked':'' ?>  id="cin" name="cin">

                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">To Company Name</label>
                                            <input type="checkbox"  id="to_company" <?php echo ($pdf_results['lut'] =='yes') ? 'checked':'' ?> name="to_company">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">To Address</label>
                                            <input type="checkbox"  id="to_address" <?php echo ($pdf_results['lut'] =='yes') ? 'checked':'' ?> name="to_address">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">To Country</label>
                                            <input type="checkbox"  id="to_country" <?php echo ($pdf_results['lut'] =='yes') ? 'checked':'' ?> name="to_country">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">To State</label>
                                            <input type="checkbox"  id="to_state" <?php echo ($pdf_results['to_state'] =='yes') ? 'checked':'' ?> name="to_state">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">To Mobile number</label>
                                            <input type="checkbox"  id="to_mobile" <?php echo ($pdf_results['to_mobile'] =='yes') ? 'checked':'' ?> name="to_mobile">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">To Email</label>
                                            <input type="checkbox"  id="to_email" <?php echo ($pdf_results['to_email'] =='yes') ? 'checked':'' ?> name="to_email">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">To State Code</label>
                                            <input type="checkbox"  id="to_state_code" <?php echo ($pdf_results['to_state_code'] =='yes') ? 'checked':'' ?> name="to_state_code">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Place Of Supply</label>
                                            <input type="checkbox"  id="place_of_supply" <?php echo ($pdf_results['place_of_supply'] =='yes') ? 'checked':'' ?> name="place_of_supply">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">Billing Country</label>
                                            <input type="checkbox"  id="billing_country" <?php echo ($pdf_results['billing_country'] =='yes') ? 'checked':'' ?> name="billing_country">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">Nature Of Supply</label>
                                            <input type="checkbox"  id="nature_of_supply" <?php echo ($pdf_results['nature_of_supply'] =='yes') ? 'checked':'' ?> name="nature_of_supply">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">Gst Payable on reverse charge</label>
                                            <input type="checkbox"  id="gst_payable" <?php echo ($pdf_results['gst_payable'] =='yes') ? 'checked':'' ?> name="gst_payable">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">Quantity</label>
                                            <input type="checkbox"  id="quantity" <?php echo ($pdf_results['quantity'] =='yes') ? 'checked':'' ?> name="quantity">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">Price</label>
                                            <input type="checkbox"  id="price" <?php echo ($pdf_results['price'] =='yes') ? 'checked':'' ?> name="price">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">Sub Total</label>
                                            <input type="checkbox"  id="sub_total" <?php echo ($pdf_results['sub_total'] =='yes') ? 'checked':'' ?> name="sub_total">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">Taxable Value</label>
                                            <input type="checkbox"  id="taxable_value" <?php echo ($pdf_results['taxable_value'] =='yes') ? 'checked':'' ?> name="taxable_value">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">CGST</label>
                                            <input type="checkbox"  id="cgst" <?php echo ($pdf_results['cgst'] =='yes') ? 'checked':'' ?> name="cgst">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">SGST</label>
                                            <input type="checkbox"  id="sgst" <?php echo ($pdf_results['sgst'] =='yes') ? 'checked':'' ?> name="sgst">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                     <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">IGST</label>
                                            <input type="checkbox"  id="igst" <?php echo ($pdf_results['igst'] =='yes') ? 'checked':'' ?> name="igst">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">TDS</label>
                                            <input type="checkbox"  id="tds" <?php echo ($pdf_results['tds'] =='yes') ? 'checked':'' ?> name="tds">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                     <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">Show From</label>
                                            <input type="checkbox"  id="show_from" <?php echo ($pdf_results['show_from'] =='yes') ? 'checked':'' ?> name="show_from">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>

                                    
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">Display Affliiate image</label>
                                            <input type="checkbox"  id="display_affliate" <?php echo ($pdf_results['display_affliate'] =='yes') ? 'checked':'' ?> name="display_affliate">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">Align Left and Right</label>
                                            <input type="checkbox"  id="l_r" <?php echo ($pdf_results['l_r'] =='yes') ? 'checked':'' ?> name="l_r">
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">Logo Alignment</label>
                                            <select  id="logo_align" name="logo_align">
                                                <?php
                                               $slected = $pdf_results['logo_align'];
                                                ?>
                                                <option <?php echo ($slected=='Center') ? 'selected':''?> >Center</option>
                                                <option <?php echo ($slected=='Left') ? 'selected':''?>>Left</option>
                                                <option <?php echo ($slected=='Right') ? 'selected':''?>>Right</option>
                                                <!-- <option><?= $slected?></option> -->
                                            </select>
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="name">Heading Position</label>
                                            <select  id="heading_position" name="heading_position">
                                                <?php
                                               $position = $pdf_results['heading_position'];
                                                ?>
                                                <option <?php echo ($position=='top') ? 'selected':''?> >top</option>
                                                <option <?php echo ($position=='center') ? 'selected':''?>>center</option>
                                                <!-- <option><?= $slected?></option> -->
                                            </select>
                                            <span class="validation-color" id="err_name"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                </div>


                                </div> 
                            <div class="col-sm-12">
                                <div class="box-footer">
                                    <button type="submit" id="company_submit" name="company_submit" class="btn btn-info btn-flat">Submit</button>
                                    <span class="btn btn-default btn-flat" id="cancel" onclick="cancel('auth/dashboard')">Cancel</span>
                                </div>
                            </div>
                            </div>                            

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div id="remove_logo" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
   <div class="modal-dialog modal-md">
      <form id="logoForm" action="<?= base_url('custom_pdf/add_pdf_settings')?>">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title">Remove Logo</h4>
            </div>
            <div class="modal-body">
                <label for="category_name">Are your sure! You want to remove logo ?</label>
            </div>
            <div class="modal-footer">
               <a href="<?php echo base_url('company_setting/remove_logo');?>" class="btn btn-sm btn-info pull-right" style="margin-left: 10px;">Yes</a>
               <button class="btn btn-sm btn-submit pull-right" data-dismiss="modal">No</button>
            </div>
         </div>
      </form>
   </div>
</div>

<div id="remove_iec" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
   <div class="modal-dialog modal-md">
      <form id="iecForm">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title">Remove Import & Export Code</h4>
            </div>
            <div class="modal-body">
                <label for="category_name">Are your sure! You want to remove this file ?</label>
            </div>
            <div class="modal-footer">
               <a href="<?php echo base_url('company_setting/remove_iec');?>" class="btn btn-sm btn-info pull-right" style="margin-left: 10px;">Yes</a>
               <button class="btn btn-sm btn-submit pull-right" data-dismiss="modal">No</button>
            </div>
         </div>
      </form>
   </div>
</div>

<div id="remove_shop" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
   <div class="modal-dialog modal-md">
      <form id="shopForm">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title">Remove Shop Establishment</h4>
            </div>
            <div class="modal-body">
                <label for="category_name">Are your sure! You want to remove this file ?</label>
            </div>
            <div class="modal-footer">
               <a href="<?php echo base_url('company_setting/remove_shop');?>" class="btn btn-sm btn-info pull-right" style="margin-left: 10px;">Yes</a>
               <button class="btn btn-sm btn-submit pull-right" data-dismiss="modal">No</button>
            </div>
         </div>
      </form>
   </div>
</div>

<?php $this->load->view('layout/footer'); ?>


