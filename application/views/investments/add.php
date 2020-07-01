<div id="add_investments_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Add Investment</h4>
            </div>
            <form name="form">
                <div class="modal-body">
                    <div id="loader">
                        <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="investment_code">Investment Code <span class="validation-color">*</span>
                                </label>                                        
                                    <input type="text" class="form-control" id="investment_code" name="investment_code">
                            </div>
                        </div>                               
                        <div class="form-group col-md-6">
                            <label for="investment_name">Investments<span class="validation-color">*</span></label>
                                <input class="form-control" type="text" name="investment_name" id="investment_name" maxlength="30" />
                                    <span class="validation-color" id="err_investment_name"></span>
                                    <input type="hidden" name="investment_id" id="investment_id" value="0">
                                    <input type="hidden" class="form-control" id="investment_name_used" name="investment_name_used" maxlength="90">
                        </div>
                    </div>      
                </div>
                <div class="modal-footer">
                    <button type="button" id="investment_submit" class="btn btn-info">
                        Add
                    </button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
