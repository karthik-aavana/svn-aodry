<div id="billing_addr" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_billing" >
                    &times;
                </button>
                <h4 class="modal-title">Billing Address</h4>
            </div>
            <div class="modal-body">
                <table id="billing_address_table" class="table table-bordered table-striped sac_table ">
                    <thead>
                    <th>Address Code</th>
                    <th>Contact Person</th>
                    <th>Address</th>
                    <th>GST Number</th>
                    <th>State</th>						
                    <th>Action</th>
                    </thead>
                    <tbody>						
                    </tbody>
                </table>                 
            </div>
            <div class="modal-footer">
                <div id="ship_to_checkbox" class="text pull-left">
                    <input type="checkbox" id="same_as_billing"> Is billing address same as shipping address..? 
                </div>                               
                <button type="button" class="btn btn-default close_billing" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-default apply_bill_address">
                    Apply
                </button>
                <span class="validation-color" id="err_bill_address"><?php echo form_error('customer'); ?></span>
            </div>
        </div>
    </div>
</div>
<!-- <script src="<?php echo base_url('assets/js/service/') ?>sac_code.js"></script> -->
<script src="<?php echo base_url('assets/custom/branch-'.$branch_id.'/js/sales/'); ?>ship_to.js"></script>