<div class="modal fade" id="default_ledger_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true">
    <div class="modal-dialog model-md modal-dialog-centered">
        <div class="modal-content">           
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> 
                <h4 class="modal-title">Edit Ledger</h4>
            </div>
            <div class="modal-body">
                <form class="forms-sample filter">
                    <input type="hidden" name="branch_id" value="0" id="branch_id">
                    <div class="row">                        
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Main Group</label>
                                <input type="text" name="main_group" id="main_group" class="form-control" autocomplete="off">
                                <span class="validation-color" id="err_main_group"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Primary Group</label>
                                <input type="text" name="sub_group_1" id="sub_group_1" class="form-control" autocomplete="off">
                                <span class="validation-color" id="err_sub_group_1"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Secondary Group</label>
                                <input type="text" name="sub_group_2" id="sub_group_2" class="form-control" autocomplete="off">
                                <span class="validation-color" id="err_sub_group_2"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Ledger Name</label>
                                <input type="text" name="ledger_name" id="ledger_name" class="form-control" autocomplete="off">
                                <span class="validation-color" id="err_ledger_name"></span>
                            </div>
                        </div> 
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary tbl-btn" id="AddNewVoucher">
                    Add
                </button>
                <button type="button" class="btn btn-primary tbl-btn" data-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>