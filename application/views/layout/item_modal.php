<style>
    #item_modal .form-group{
        margin: 0;
    }
</style>
<div id="item_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Add Item</h4>
            </div>
            <div class="modal-body" style="min-height: 60px">               
                <form id="itemForm">                       
                    <div class="row">
                        <?php if (in_array($service_module_id, $active_add)) {?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a href="" data-toggle="modal" data-target="#service_modal" class="open_service_modal">+ Add Service</a>
                                </div>
                            </div>
                        <?php }
                        $service_modal = 1;
                        $product_modal = 0;
                        $product_inventory_modal = 0;
                        if (in_array($product_module_id, $active_add)) {
                            if (isset($inventory_access) && $inventory_access == "yes") {
                                $product_inventory_modal = 1;
                                ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <a href="" data-toggle="modal" data-target="#product_inventory_modal" class="open_product_modal">+ Add Product</a>
                                    </div>
                                </div>
                                <?php
                            } else {
                                $product_modal = 1;
                                ?>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <a href="" data-toggle="modal" data-target="#product_inventory_modal" class="open_product_modal">+ Add Product</a>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                    </div>
                </form>
            </div>
           <!--  <div class="modal-footer">
                <button type="close" id="item_modal_close" class="btn btn-info" data-dismiss="modal">Close</button>
            </div> -->
        </div>
    </div>
</div>
<script type="text/javascript">
     $('[data-toggle="modal"]').attr({
        'data-backdrop' : 'static',
        'data-keyboard' : false                       
    });
</script>
<?php
$this->load->view('product/product_inventory_modal');
if ($product_inventory_modal == 1 || $product_modal == 1) {
}
/*if ($product_modal == 1) {
    $this->load->view('product/product_modal');
}*/
if ($product_modal == 1 || $product_inventory_modal == 1) {
    //$this->load->view('product/hsn_modal');
}
if ($service_modal == 1) {
    $this->load->view('service/service_modal');
    //  $this->load->view('service/sac_modal');
}
?>