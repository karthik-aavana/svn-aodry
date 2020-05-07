<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Product</h3>
                        <a class="btn btn-default pull-right back_button" onclick="cancel('product/product_batchList')">Back</a>
                    </div>
                    <form role="form" id="form" method="post" action="<?php echo base_url('product/edit_product_batch'); ?>">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product">Product <span class="validation-color">*</span> </label>
                                    <select class="form-control select2" id="product_option">
                                        <option value="<?=$products_detail->batch_parent_product_id;?>" serial='<?=$products_detail->batch_serial;?>' batch='<?=$products_detail->batch_serial;?>'><?=$products_detail->product_name;?></option>
                                    </select>
                                    <input type="hidden" name="product_id" value="<?=$products_detail->product_id;?>">
                                    <span class="validation-color" id="err_product_option"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_price">Purchase Price</label>
                                    <input type="text" class="form-control float_number" id="product_price" name="product_price" value="<?php echo precise_amount($products_detail->product_price); ?>" >
                                    <span class="validation-color" id="product_price"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="profit_margin">Profit Margin(%)</label>
                                    <input type="text" class="form-control float_number" id="profit_margin" name="profit_margin" value="<?php echo (float)($products_detail->product_profit_margin); ?>" maxlength="5">
                                    <span class="validation-color" id="profit_margin"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_selling_price">Selling Price</label>
                                    <input type="text" class="form-control float_number" id="product_selling_price" name="product_selling_price" value="<?=precise_amount($products_detail->product_selling_price,2);?>" >
                                    <span class="validation-color" id="product_selling"></span>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_mrp">MRP <span class="validation-color">*</span></label>
                                    <input type="text" class="form-control float_number" id="product_mrp" name="product_mrp" value="<?=precise_amount($products_detail->product_mrp_price,2);?>" >
                                    <span class="validation-color" id="err_product_mrp"></span>
                                </div>
                            </div>
                            <div class="col-md-3 product_discount">
                                <div class="form-group">
                                    <label for="product_discount">Discount<!-- <span class="validation-color">*</span> --></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-name="product" data-target="#add_discount_product" class="pull-right new_tax" title="Add discount">+</a>
                                        </div>
                                        <select class="form-control select2" id="product_discount" name="product_discount"  tabindex="4">
                                            <option value="">Select Discount <?=$products_detail->product_discount_id;?></option>
                                            <?php
                                            foreach ($discount as $value) {
                                                if ($value->discount_id == $products_detail->product_discount_id) {
                                                    echo "<option value='$value->discount_id' selected>".(float)$value->discount_value."%</option>";
                                                } else {
                                                    echo "<option value='$value->discount_id'>".(float)$value->discount_value."%</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <span class="validation-color" id="err_product_discount"></span>
                                </div>
                            </div>
                            <div class="col-md-3 product_gst">
                                <div class="form-group">
                                    <label for="gst_tax_product">Tax (GST Output)</label>
                                    <div class="input-group"><?php
                                        if (in_array($tax_module_id, $active_add)) {
                                            ?>
                                            <div class="input-group-addon">
                                                <a href="" data-toggle="modal" data-target="#tax_gst_modal" data-name="product" class="pull-right new_tax">+</a>
                                            </div>
                                        <?php } ?>
                                        <select class="form-control select2" id="gst_tax_product" name="gst_tax_product" style="width: 100%;" tabindex="6">
                                            <option value="">Select Tax</option>
                                            <?php
                                            foreach ($tax_gst as $row) {
                                                echo "<option value='$row->tax_id'" . set_select('gst_tax', $row->tax_id . "-" . $row->tax_value) . " per='".(float)($row->tax_value)."' ".($row->tax_id == $products_detail->product_gst_id ? 'selected' : '').">$row->tax_name@" . (float)($row->tax_value) . "%</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <span class="validation-color" id="err_product_code"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_gst_code">GST Tax Percentage </label>
                                    <input type="text" class="form-control" id="product_gst_code" name="product_gst_code" value="<?=(float)($products_detail->product_gst_value);?>" readonly>
                                    <span class="validation-color" id="product_gst_code"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_batch">Product Batch</label>
                                    <input type="text" class="form-control" id="product_batch" name="product_batch" value="<?=($products_detail->product_batch);?>" readonly>
                                    <input type="hidden" name="batch_serial" value="<?=(float)($products_detail->batch_serial);?>">
                                    <input type="hidden" name="batch_parent_product_id" value="<?=(float)($products_detail->batch_parent_product_id);?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_scheme_no">Product Scheme Number</label>
                                    <input type="number" class="form-control" id="product_scheme_no" name="product_scheme_no" value="<?=(float)($products_detail->batch_serial);?>" readonly>
                                </div>
                            </div>
                        </div>      
                        <div class="box-footer">
                            <button type="submit" id="submit_product_batch" class="btn btn-info">
                                Update
                            </button>
                            <span class="btn btn-default" onclick="cancel('product/product_batchList')">
                                Cancel
                            </span>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('discount/discount_modal_product');
if (in_array($tax_module_id, $active_add)) {
    $this->load->view('tax/tax_modal_tds');
    $this->load->view('tax/tax_modal_gst');
}
$this->load->view('layout/footer');
?>
<script src="<?php echo base_url('assets/custom/branch-'.$this->session->userdata('SESS_BRANCH_ID').'/js/product/') ?>product.js"></script>
<script src="<?php echo base_url('assets/js/product/') ?>product_mrp.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
        $(document).on('change','[name=profit_margin],[name=product_price]',function(){
            var profit_margin = $('[name=profit_margin]').val();
            var pro_pr = $('[name=product_price]').val();
            var basic_pr = pro_pr;
            if(pro_pr > 0){
                if(profit_margin > 99){
                    $(this).val(0);
                }else{
                    var profit_amnt = (pro_pr * profit_margin)/100;
                    basic_pr = +pro_pr + +profit_amnt;
                }
            }
            $('[name=product_selling_price]').val(precise_amount(basic_pr));
        })
		$('#submit_product_batch').click(function(){
			var id= $('#product_option').val();

			if (id == null || id == "") {
	            $("#err_product_option").text("Please Select Product");
	            return false;
	        } else {
	            $("#err_product_option").text("");
	        }
	        
	        var product_mrp = $('#product_mrp').val();

	        if (product_mrp == null || product_mrp == "") {
	            $("#err_product_mrp").text("Please Enter valid MRP");
	            return false;
	        } else {
	            $("#err_product_mrp").text("");
	        }
		})
	})
</script>