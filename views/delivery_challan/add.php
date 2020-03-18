<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$type = user_limit();
$scount = supplier_count();
$limit = $type['coun'] + $scount['scount'];
$this->load->view('layout/header');
?>
<div class="content-wrapper">   
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('delivery_challan'); ?>">Delivery Challan</a></li>
            <li class="active">Add</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Delivery Challan</h3>
                        <a class="btn btn-sm btn-default pull-right back_button" href1="<?php echo base_url('delivery_challan'); ?>">Back </a>
                    </div>
                    <form role="form" id="form" method="post" action="<?php echo base_url('delivery_challan/add_delivery_challan'); ?>">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="date">Invoice Date<span class="validation-color">*</span></label>
                                        <?php
                                            $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));
                                            if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1])) {
                                                $date = '01-04-'.$financial_year[0];
                                            } else {
                                                $date = date('d-m-Y');
                                            }
                                            ?>
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" id="invoice_date" name="invoice_date" value="<?php echo $date; ?>">
                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                        </div>
                                        <span class="validation-color" id="err_date"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="invoice_number">Invoice Number<span class="validation-color">*</span></label>
                                        <?php ?>
                                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?= $invoice_number ?>" <?php
                                        if ($access_settings[0]->invoice_readonly == 'yes') {
                                            echo "readonly";
                                        }
                                        ?>>
                                        <span class="validation-color" id="err_invoice_number"></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="customer">Customer/Supplier <span class="validation-color">*</span></label>
                                        <div class="input-group1">
                                            <!-- <div class="input-group-addon">
                                                <?php
                                                if (isset($other_modules_present['customer_module_id']) && $other_modules_present['customer_module_id'] != "") {
                                                    ?>
                                                    <a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" data-target="#customer_modal" class="open_customer_modal pull-right">+</a>
                                                <?php } ?>
                                            </div> -->
                                            <select class="form-control select2" autofocus="on" id="customer" name="customer">
                                                <option value="">Select</option>
                                                <?php
                                                foreach ($customer as $row) {
                                                    if($row->customer_mobile != ''){
                                                        echo "<option value='$row->customer_id-$row->customer_country_id-$row->customer_state_id' type='customer'>$row->customer_name($row->customer_mobile)</option>";
                                                    }else{
                                                        echo "<option value='$row->customer_id-$row->customer_country_id-$row->customer_state_id' type='customer'>$row->customer_name</option>";
                                                    }
                                                }
                                                foreach ($supplier as $row) {
                                                    if($row->supplier_mobile != ''){
                                                        echo "<option value='$row->supplier_id-$row->supplier_country_id-$row->supplier_state_id' type='supplier'>$row->supplier_name($row->supplier_mobile)</option>";
                                                    }else{
                                                        echo "<option value='$row->supplier_id-$row->supplier_country_id-$row->supplier_state_id' type='supplier'>$row->supplier_name</option>";
                                                    }  
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <span class="validation-color" id="err_customer"><?php echo form_error('customer'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="delivery_reference">Sales/Purchase Reference Number<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="delivery_reference" name="delivery_reference">
                                            <option value=''>Select</option>
                                        </select>
                                        <span class="validation-color" id="delivery_referencember"><?php echo form_error('delivery_reference'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <input type="hidden" name="reference_type">
                                    <input type="hidden" name="reference_id">
                                    <!-- <button type="submit" value="PDF" name="pdf" class="btn btn-success">PDF</button> -->
                                </div>
                            </div>
                            <div class="box-header with-border" id="view_html"></div>
                            <div class="box-footer">
                                <button type="submit" id="delivery_challan_submit" name="submit" value="add" class="btn btn-info">Add</button>
                                <span class="btn btn-default" id="delivery_challan_cancel" onclick="cancel('delivery_challan')">Cancel</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.content-wrapper -->
<?php
$this->load->view('layout/footer');
/* $this->load->view('customer/customer_modal');
  $this->load->view('product/product_modal');
  $this->load->view('service/service_modal');
  $this->load->view('layout/item_modal');
  $this->load->view('category/category_modal');
  $this->load->view('subcategory/subcategory_modal');
  $this->load->view('tax/tax_modal');
  $this->load->view('discount/discount_modal'); */
/* $this->load->view('service/sac_modal'); */
/* $this->load->view('product/hsn_modal'); */
/* $this->load->view('product/product_inventory_modal'); */
?>
<script type="text/javascript">
    var sales_data = new Array();
    var branch_state_list = <?php echo json_encode($state); ?>;
    var item_gst = new Array();
    var common_settings_round_off = "<?= $access_common_settings[0]->round_off_access ?>";
    var common_settings_tax_split = "<?= $access_common_settings[0]->tax_split_equaly ?>";
</script>
<script src="<?php echo base_url('assets/js/sales/') ?>delivery_challan.js"></script>
<!--  -->
