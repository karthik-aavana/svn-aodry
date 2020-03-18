<div class="modal fade" id="edit_ledgers" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Edit Group/Subgroup/Ledger</h4>				
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Main Group<span class="validation-color">*</span></label>
                            <select class="form-control select2" name="main_grp_id" id="main_grp" disabled="disabled">
                                <option value="">Select Main Group</option>
                                <?php
                                if (!empty($main_list)) {
                                    foreach ($main_list as $key => $value) {
                                        echo "<option value='{$value['ledger_id']}'>{$value['main_group']}</option>";
                                    }
                                }
                                ?>
                            </select>
                            <span class="validation-color" id="err_m_grp"></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Sub Group-1</label>
                            <select class="form-control select2" name="primary_sub_group" id="primary_sub_group" disabled="disabled">
                                <option value="">Select Primary Sub Group</option>
                                <?php
                                if (!empty($main_list)) {
                                    foreach ($main_list as $key => $value) {
                                        echo "<option value='{$value['ledger_id']}'>{$value['sub_group_1']}</option>";
                                    }
                                }
                                ?>
                            </select>
                            <!-- <input type="text" id="default" name="primary_sub_group" list="primary_sub_group_drop" class="form-control"> -->
                            <!-- <datalist id="primary_sub_group_drop" class=""></datalist>	 -->
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Sub Group-2</label>
                            <select class="form-control select2" name="sec_sub_group" id="sec_sub_group" disabled="disabled">
                                <option value="">Select Primary Sub Group</option>
                                <?php
                                if (!empty($main_list)) {
                                    foreach ($main_list as $key => $value) {
                                        echo "<option value='{$value['ledger_id']}'>{$value['sub_group_2']}</option>";
                                    }
                                }
                                ?>
                            </select>
                            <!-- <input type="text" id="default" name="sec_sub_group" list="sec_sub_group_drop" class="form-control"> -->
                            <!-- <datalist id="sec_sub_group_drop" class="js-example-basic-single"></datalist> -->
                        </div>
                    </div>
                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <div style="display: none;">
                            <select class="form-control select2" name="default_ledger_name" id="default_ledger_name" disabled="disabled" style="display: none;">
                                <option value="">Select Primary Sub Group</option>
                                <?php
                                if (!empty($main_list)) {
                                    foreach ($main_list as $key => $value) {
                                        echo "<option value='{$value['ledger_id']}'>{$value['ledger_name']}</option>";
                                    }
                                }
                                ?>
                            </select>
                            </div>
                            <label>Ledger<span class="validation-color">*</span></label>
                            <input class="form-control" name="ledger_name" id="ledger" />
                            <span class="validation-color" id="err_ledger"></span>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <input type="hidden" name="ledger_id">
                            <input type="hidden" name="branch_id">
                            <input type="hidden" name="default_ledger_id">
                            <label>Module : <span class="validation-color module_name"></span></label>
                            <br>
                            <label>GST Payable : <span class="validation-color gst_payable"></span></label>
                            <br>
                            <label>Place Of Supply : <span class="validation-color place_of_supply"></span></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default tbl-btn validation_btn" id="setDefaultLedger">
                    Set Default Ledger Name
                </button>
                <button type="button" class="btn btn-primary tbl-btn" data-dismiss="modal">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary tbl-btn validation_btn" id="editGroupLedger">
                    Update
                </button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    /*$(document).ready(function() {
        $(".select2").select2({
            dropdownParent: $("#add_groups")
        });
    });    */
</script>