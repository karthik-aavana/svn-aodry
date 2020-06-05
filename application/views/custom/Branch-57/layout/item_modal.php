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
                        <div class="col-md-6">
                            <div class="form-group">
                                <a href="<?=base_url('service/add');?>" target="_blank">+ Add Service</a>
                            </div>
                        </div>
                        <?php
                        $service_modal = 1;
                        $product_modal = 0;
                        $product_inventory_modal = 0;
                        if (isset($inventory_access) && $inventory_access == "yes") {
                            $product_inventory_modal = 1;
                            ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <a href="<?=base_url('product/add');?>" target="_blank">+ Add Product</a>
                                </div>
                            </div>
                            <?php
                        } else {
                            $product_modal = 1;
                            ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a href="<?=base_url('product/add');?>">+ Add Product</a>
                                </div>
                            </div>
                        <?php } ?>
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